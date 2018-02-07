<?php namespace App\Services\Version\Agent\AgentIndex;

use App\Http\Controllers\Agent\BrandController;
use App\Http\Controllers\Agent\MessageController;
use App\Http\Controllers\Agent\NewsController;
use App\Http\Controllers\Agent\VideoController;
use App\Http\Controllers\Agent\ActivityController;
use App\Models\Agent\AgentCustomer;
use App\Models\Agent\AgentCustomerLog;
use App\Models\Agent\Brand;
use App\Models\Agent\TraitBaseInfo\RongCloud;
use App\Services\News;
use App\Models\Agent\Agent;
use App\Services\Version\VersionSelect;
use App\Models\SendOrderQueue\V020800 as SendOrderQueue;
use App\Models\SendInvestor\V020800 as SendInvestor;
use App\Models\User\Entity as User;
use App\Models\Zone\Entity as Zone;
use Illuminate\Support\Str;

class _v010000 extends VersionSelect
{
    use RongCloud;

    const DOWN = 2;     //登录经纪人的下线标记数

    /**
     * 经纪人首页全局搜索
     *
     * @param $param
     * @return array
     */
    public function postSearch($param)
    {
        $data = [

            //品牌
            'brand' => call_user_func_array(
                array(new BrandController(), 'postLists'),
                array($param['request'], $param['version'])),

            //活动
            'activity' => call_user_func_array(
                array(new ActivityController(), 'postLists'),
                array($param['request'], $param['version'])),

            //资讯
            'news' => call_user_func_array(
                array(new NewsController(), 'postLists'),
                array($param['request'], new News(), $param['version'])),

            //录播
            'video' => call_user_func_array(
                array(new VideoController(), 'postLists'),
                array($param['request'], $param['version'])),
        ];

        if (!empty($param['type']) && array_key_exists(trim($param['type']), $data)) {
            /*$data = [ $param['type'] => $data[$param['type']] ];*/
            $data = $data[$param['type']];
        }

        return ['message' => $data, 'status' => true];
    }

    /**
     * 经纪人首页显示
     * @param $param  参数集合
     * @param version 版本号
     * @return array|string
     * @internal param Request $request
     * @internal param null $version
     */
    public function postIndex($param, $version = null)
    {
        $result = $param['request']->input('agent_id', '');
        $page = $param['request']->input('page', 1);
        $page_size = $param['request']->input('page_size', 10);

        //判断当前的经纪人ID是否存在
        if (!$result) {
            return ['message' => '缺少经纪人ID：agent_id', 'status' => false];
        }

        //经纪人基本信息和品牌信息
        $agent_info_an_brand_info_and_new_list = Agent::instance()->agentIndex($result, $page, $page_size, $version);

        //返回组合后的综合信息
        return ['message' => $agent_info_an_brand_info_and_new_list, 'status' => true];
    }

    /**
     * 经纪人个人详情   --数据中心版
     *
     * @param $param
     * @return array|string
     * @internal param Request $request
     * @internal param null $version
     */
    public function postAccount($param)
    {
        $by_examine  = $param['request']->input('agent_id', '');          //被查看的经纪人ID
        $login_agent = $param['request']->input('login_agent_id', '');    //登录的经济人ID

        if (!$by_examine) {
            return ['message' => '缺少被查看经纪人ID：agent_id', 'status' => false];
        }

        //返回数据
        $agent_data = Agent::instance()->getAgentLikeIndustry($by_examine);
        if (is_string($agent_data) && $agent_data === "by_agent_null") {
            return ['message' => '被查看的经纪人不存在', 'status' => false];
        }
        $relation_data = Agent::instance()->getRelationAgent($login_agent, $agent_data, $by_examine);

        $agent_data['relation'] = $relation_data['relation'];
        if ($relation_data['not_agent'] && $relation_data['not_agent'] === "not_agent" ||
            is_array($relation_data) && $relation_data['relation'] !== self::DOWN ||
            is_null($relation_data)
        ) {

            return ['message' => ['agent_data' => $agent_data], 'status' => true];
        }

        //结合数据
        $combine['agent_data']     = $agent_data;
        $combine['relation_data']  = $relation_data;
        $agentInfo                 = Agent::find($login_agent);
        $combine['is_verified']    = trim($agentInfo['is_verified']);

        return ['message' => $combine, 'status' => true];

    }

    /**
     * 经纪人是否在线操作
     *
     * @param   $param          集合参数
     * @return array|string
     * @internal param Request $request
     * @internal param null $version
     *
     */
    public function postToggle($param)
    {
        $result = $param['request']->input();

        if (empty($result['agent_id']) || !isset($result['is_online'])) {
            return ['message' => '经纪人ID：agent_id和在线状态：is_online 为必填项', 'status' => false];
        }
        if (!intval($result['agent_id']) || !is_numeric($result['is_online'])) {
            return ['message' => '经纪人ID：agent_id只能为整形,在线状态：is_online 只能为 0 | 1', 'status' => false];
        }

        //对经纪人的在线状态进行处理
        $update_result = Agent::where('id', $result['agent_id'])->update(['is_online' => $result['is_online']]);
        if ($update_result) {
            return ['message' => '状态改变成功', 'status' => true];
        } else {
            return ['message' => '状态改变失败', 'status' => false];
        }

    }


    /**
     * 接受派单  --数据中心版
     *
     * @param $param
     * @return array
     * @author tangjb
     */
    public function postAcceptOrder($param)
    {
        $sendInvestorId = SendOrderQueue::where('id', $param['queue_id'])->value('send_investor_id');
        //判断是否已经被别人接单
        $res = SendInvestor::where('id', $sendInvestorId)->first();


        if (!$res ||$res->status==1 ) {
            return ['message' => '无效，此单已经被其他经纪人抢去', 'status' => false];
        }


        if ($res->status==-1) {
            return ['message' => '无效，此单已被取消', 'status' => false];
        }

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

            //删除队列
            SendOrderQueue::where(
                [
                    'brand_id' => $param['brand_id'],
                    'uid' => $param['uid'],
                ]
            )->delete();

            $res->status=1;
            $res->save();

            \DB::commit();
        } catch (\Exception $e) {
            \DB::rollBack();
            throw(new \RuntimeException($e->getMessage()));
        }

        $user = User::where('uid', $param['uid'])->first();
        $agent = Agent::where('id', $param['agent_id'])->first();
        $content = ['brand_id'=>$param['brand_id'], 'uid'=>$param['uid'], 'agent_id'=>$param['agent_id']];

        //发送透传到C端用户  为客户添加经纪人
        send_transmission(json_encode(['type'=>'accept_order', 'style'=>'json', 'value'=>$content]), $user);

        //为经纪人添加客户
        send_transmission(json_encode(['type'=>'accept_order', 'style'=>'json',
            'value'=>['username'=>getRealTel($agent->non_reversible, 'agent'), 'id'=>$user->uid, 'realname'=>$user->realname,
                'nickname'=>$user->nickname]]), $agent, null, 1);

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
            $datas = ['content' => 'Hi，我对这个品牌有咨询意向~',];
            $send_notice_result = SendCloudMessage($param['uid'], 'agent' . $param['agent_id'], $datas, 'RC:TxtMsg', '', 'custom','one_user');

            //经纪人发送融云消息
            $_datas = ['content' => trans('tui.agent_pai_notice_infos', ['brand_name' => $brand_result->name])];
            $send_notice_result = SendCloudMessage('agent' . $param['agent_id'], $param['uid'], $_datas, 'RC:TxtMsg', '', 'custom','one_agent');

            //获取当前投资人是否存在邀请经纪人，如果给对方发送消息
            $gain_result = AgentCustomer::instance()->gainCustomerAgentRelationDatas($param['uid']);

            //发送融云消息
            if ($gain_result) {
//                _v010000::gatherInfoSends([
//                    $param['uid'],
//                    'agent'.$gain_result->agent_id, [
//                        'brand_name' => $brand_result->name,
//                        'agent_name' => $agent->nickname,
//                        'zone_name'  => abandonProvince(Zone::pidNames([$agent->zone_id])),
//                    ]
//                ], 'confirm_pai_relation', 'text', 'true', 'user');
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