<?php
/**
 * Created by PhpStorm.
 * Title：
 * User: yaokai
 * Date: 2017/11/9 0009
 * Time: 15:52
 */
namespace App\Services\Version\Agent\AgentIndex;

use App\Models\Agent\Agent;
use App\Models\Agent\AgentCustomer;
use App\Models\Agent\AgentCustomerLog;
use App\Models\Agent\Score\AgentScoreLog;
use App\Models\SendOrderQueue\V020800 as SendOrderQueue;
use App\Models\SendInvestor\V020800 as SendInvestor;
use App\Models\User\Entity as User;
use App\Models\Zone\Entity as Zone;
use Illuminate\Support\Str;
use App\Models\Agent\Entity\_v010200 as Agentv010200;

class _v010003 extends _v010002
{
    /**
     * 接受派单   --数据中心版
     * @User yaokai
     * @param $param
     * @return array
     */
    public function postAcceptOrder($param)
    {

        //判断是否已经被别人接单
        // Todo 这里的queue_id其实就是派单send_investor_id  当时为了方便移动端和前端  后台统一修改 不同于010000版本
        $res = SendInvestor::where('id', $param['queue_id'])->first();
        $user = User::where('uid', $param['uid'])->first();
        $realname = $user->realname ?: $user->nickname;

        if (!$res || $res->status == 1) {
            //相关异常订单处理
            SendOrderQueue::where('send_investor_id', $param['queue_id'])->where('status', '1')->update(['status' => '-1']);

            return ['message' => '晚了一步，投资人' . $realname . '被其他经纪人抢走了！', 'status' => false];
        }


//        if ($res->status==-1) {  fixme 这里1.0.3之后已经没有 取消订单功能
//            return ['message' => '该笔咨询任务已取消！', 'status' => false];
//        }

        //开始事务
        \DB::beginTransaction();
        try {
            $agentCustomer = AgentCustomer::where('uid', $param['uid'])->where('agent_id', $param['agent_id'])->first();

            //如果没有就创建，防止创建多条记录
            if(!$agentCustomer){
                $agentCustomer = AgentCustomer::create([
                    'brand_id' => $param['brand_id'],
                    'uid' => $param['uid'],
                    'agent_id' => $param['agent_id'],
                    'source' => 5,
                ]);
            }

            //如果原来正好是邀请关系，就改成先邀请后派单获得
            if($agentCustomer->source==8 ||$agentCustomer->source==9){
                $agentCustomer->source=5;
                $agentCustomer->save();
            }

            AgentCustomerLog::create([
                'agent_customer_id'=>$agentCustomer->id,
                'action'=>0,
                'post_id'=>0,
                'brand_id'=>$param['brand_id'],
                'agent_id'=>$param['agent_id'],
                'uid'=>$param['uid'],
                'created_at'=>time()
            ]);

            AgentCustomerLog::create([
                'agent_customer_id'=>$agentCustomer->id,
                'action'=>1,
                'post_id'=>0,
                'brand_id'=>$param['brand_id'],
                'agent_id'=>$param['agent_id'],
                'uid'=>$param['uid'],
                'created_at'=>time()
            ]);

            //修改队列
            SendOrderQueue::where('send_investor_id' ,$param['queue_id'])
                    ->update(['status'=>'-1']);//已被他人接单

            //修改队列
            SendOrderQueue::where('agent_id', $param['agent_id'])
                ->where('send_investor_id' ,$param['queue_id'])
                ->update(['status'=>'2']);//已被自己接单

            $res->status=1;
            $res->agent_id=$param['agent_id'];
            $res->save();


            //给积分
            Agentv010200::add($param['agent_id'], AgentScoreLog::$TYPES_SCORE[11], 11, '接受派单咨询任务', $res->id, 1);

            \DB::commit();
        } catch (\Exception $e) {
            \DB::rollBack();
            throw(new \RuntimeException($e->getMessage()));
        }

        $agent = Agent::where('id', $param['agent_id'])->first();
        $content = ['brand_id'=>$param['brand_id'], 'uid'=>$param['uid'], 'agent_id'=>$param['agent_id']];

        //发送透传到C端用户  为客户添加经纪人
        send_trans_and_notice(json_encode(['type'=>'accept_order', 'style'=>'json', 'value'=>$content]), $user);

        //为经纪人添加客户
        send_transmission(json_encode(['type'=>'accept_order', 'style'=>'json', 'value'=>['username'=>getRealTel($agent->non_reversible, 'agent'), 'id'=>$user->uid, 'realname'=>$user->realname, 'nickname'=>$user->nickname]]), $agent, null, 1);

        //接单透传红点
        $res = send_transmission(json_encode(['type'=>'new_message', 'style'=>'json',
            'value'=> ['sendTime' => time()]]),
            $agent,null, 1);


        //获取用户名称和品牌名称--发送融云消息
        $brand_result  = \App\Models\Brand\Entity::with('categorys1')
            ->where('id', $param['brand_id'])
            ->first();

        if ($brand_result) {
            $data = [
                'title'    => $brand_result->name,
                'digest'   => !empty($brand_result->brand_summary) ?  $brand_result->brand_summary  : Str::limit(trim(preg_replace('#<\w+(\s+.*?)?>|</\w+>|&nbsp;#','', $brand_result->details)), 50),
                'imageURL' => getImage($brand_result->logo),
                'url'      => 'https://'. env('APP_HOST') . '/webapp/agent/brand/detail?agent_id='. $agent->id .'&id=' . $brand_result->id,
                'type'     => '0',
            ];

            //发送融云消息
            $send_result = SendCloudMessage($param['uid'], 'agent' . $param['agent_id'], $data, 'TY:RichMsg', '', 'custom','one_user');

            //再次发送融云消息
            $datas = 'Hi，我对这个品牌有咨询意向~';
            $send_notice_result = SendCloudMessage($param['uid'], 'agent' . $param['agent_id'], $datas, 'RC:TxtMsg', '', true,'one_user');

            //经纪人发送融云消息

            $_datas = trans('tui.agent_pai_notice_infos', ['brand_name' => $brand_result->name]);
            $send_notice_result = SendCloudMessage('agent' . $param['agent_id'], $param['uid'], $_datas, 'RC:TxtMsg', '', true,'one_agent');

            //获取当前投资人是否存在邀请经纪人，如果给对方发送消息
            $gain_result = AgentCustomer::instance()->gainCustomerAgentRelationDatas($param['uid']);

            //发送融云消息
            if ($gain_result) {
                $_datas = trans('tui.confirm_pai_relation', [
                    'brand_name' => $brand_result->name,
                    'agent_name' => $agent->nickname,
                    'zone_name'  => abandonProvince(Zone::pidNames([$agent->zone_id])),
                ]);
                $send_notice_result = SendCloudMessage($param['uid'],'agent'.$gain_result->agent_id,  $_datas, 'RC:TxtMsg', '', true,'one_user');
            }
        }

        return ['message' => '抢单成功', 'status' => true];
    }


}











