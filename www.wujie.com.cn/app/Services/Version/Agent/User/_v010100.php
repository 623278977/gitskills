<?php namespace App\Services\Version\Agent\User;

use App\Models\Agent\Agent;
use App\Models\Agent\AgentCustomer;
use App\Services\Version\VersionSelect;
use DB;
use Validator;
//use App\Models\Agent\Agent;
use App\Models\User\Entity as User;
use App\Models\RedPacket\RedPacket;
use App\Models\RedPacket\RedPacketPerson;
use App\Models\Agent\AgentScreenCapture;


class _v010100 extends _v010005
{
    //邀请注册投资人返回接口
    public function postRegisterCustomerResult($input)
    {
        $uid = intval($input['uid']);
        $userInfo = User::find($uid);
        if (!in_array($userInfo['status'], [1, 2])) {
            return ['message' => "用户无效", 'status' => false];
        }
        $data = [];
        $nowTime = time();
        $data['username'] = idCardEncrypt($userInfo['username'], 3, 4, 4);

        $userRedPacketInfo = RedPacketPerson::with(['red_packet' => function ($query) {
            $query->where('type', 3);
        }])->where('receiver_id', $uid)->first();
        if (is_object($userRedPacketInfo)) {
            $data['redpacket_id'] = trim($userRedPacketInfo['red_packet']['id']);
            $data['amount'] = trim($userRedPacketInfo['red_packet']['amount']);
            $data['amount_str'] = RedPacket::redPacketLevel($userRedPacketInfo['red_packet']['id']);
            $data['status'] = $nowTime >= $userRedPacketInfo['expire_at'] ? -1 : 1;
        } else {
            $inviteRedPacketInfo = RedPacket::showWhere()->where('type', 3)->first();
            if (!is_object($inviteRedPacketInfo)) {
                return ['message' => '红包已下架', 'status' => false];
            }
            $data['amount'] = $inviteRedPacketInfo['amount'];
            $data['amount_str'] = RedPacket::redPacketLevel($inviteRedPacketInfo['id']);
            $data['status'] = '0';
            $data['redpacket_id'] = $inviteRedPacketInfo['id'];
        }
        return ['message' => $data, 'status' => true];
    }

    /**
     * shiqy
     * 投资人领取红包接口
     */
    public function postCustomReceiveRedpacket($input)
    {
        $redPacketIds = trim($input['redpacket_ids']);
        $uid = trim($input['uid']);
        $soure = empty($input['soure']) ? 0 : intval($input['soure']);
        $userInfo = User::where('uid', $uid)->whereIn('status', [1, 2])->first();
        if (!is_object($userInfo)) {
            return ['message' => '投资人ID无效', 'status' => false];
        }
        if (empty($redPacketIds)) {
            return ['message' => '参数不能为空', 'status' => false];
        }
        $redPacketIdArr = explode(',', $redPacketIds);
        $redPacketIdArr = array_filter($redPacketIdArr);
        if (empty($redPacketIdArr)) {
            return ['message' => '参数错误', 'status' => false];
        }
        foreach ($redPacketIdArr as $redPacketId) {
            $redPacketInfo = RedPacket::showWhere()->where('id', $redPacketId)->first();
            if (!is_object($redPacketInfo)) {
                return ['message' => '红包无效', 'status' => false];
            }
            $isHave = RedPacketPerson::where('receiver_id' , $uid)
                ->where('red_packet_id',$redPacketInfo['id'])->first();
            if(is_object($isHave)){
                if(count($redPacketIdArr) == 1){
                    //如果领取过了
                    return ['message' => 'has_draw', 'status' => false];
                }
                continue;
            }
            DB::transaction(function () use ($redPacketInfo, $uid,$soure) {
                $nowTime = time();
                $data = [];
                $data['receiver_id'] = $uid;
                $data['red_packet_id'] = $redPacketInfo['id'];
                $data['status'] = 0;
                $data['expire_at'] = RedPacket::getExpireTime($redPacketInfo['id']);
                $data['created_at'] = $nowTime;
                $data['updated_at'] = $nowTime;
                $data['used_at'] = 0;
                $data['gain_source'] = $soure;
                $data['type'] = intval($redPacketInfo['type']);
                $data['amount'] = trim($redPacketInfo['amount']);
                RedPacketPerson::insert($data);
                RedPacket::where('id', $redPacketInfo['id'])->increment('gives', 1);
            });
        }
        return ['message' => '领取成功', 'status' => true];
    }


    /**
     * 经纪人红包列表
     *
     * @param $input
     * @return array
     * @author tangjb
     */
    public function postPackageList($input)
    {
        $agent_id = array_get($input, 'agent_id');
        $type = array_get($input, 'type', 0);
        $page = array_get($input, 'page', 1);
        $pageSize = array_get($input, 'pageSize', 10);

        $validator = \Validator::make($input, [
            'agent_id' => 'required|exists:agent,id',
        ], [], ['agent_id'=>'经纪人id']);

        if ($validator->fails()) {
            $warnings = $validator->messages()->all();
            return ['message' => $warnings[0], 'status' => false];
        }


        $lists = AgentCustomer::with('user', 'contract')->where('agent_id', $agent_id)
            ->where(function ($query) use ($type) {
                if ($type) {
                    $query->where('contract_id', '>', 0);
                } else {
                    $query->where('contract_id', 0);
                }
            })
            ->orderBy('created_at', 'desc')
            ->whereIn('source', ['1', '6', '7'])
            ->forPage($page, $pageSize)
            ->get();


        $data = [];
        foreach ($lists as $k => $v) {
            //被邀请的投资人
            $data[$k]['uid'] = $v->uid;
            $data[$k]['customer_name'] = $v->user->realname ? $v->user->realname : $v->user->nickname;
            //入账时间
            $data[$k]['in_time'] = date('Y.m.d H:i:s', $v->created_at->timestamp);

            if ($type) {
                //激活时间
                $data[$k]['active_time'] = date('Y.m.d H:i:s', $v->contract->created_at->timestamp);
                //加盟品牌
                $data[$k]['brand_name'] = $v->contract->brand->name;
                //加盟金额
                $data[$k]['amount'] = $v->contract->amount;
                //促单经纪人
                $data[$k]['follow_agent'] = $v->contract->agent->realname;
                //邀请经纪人
                $data[$k]['invite_agent'] = Agent::find($agent_id)->realname;
            }
        }

        return ['message' => $data, 'status' => true];
    }


    /**
     * 经纪人红包详情
     *
     * @param $input
     * @return array
     * @author tangjb
     */
    public function postPackageDetail($input)
    {
        $id = array_get($input, 'id');
        $agent_id = array_get($input, 'agent_id');
        $validator = \Validator::make($input, [
            'id' => 'required|exists:agent_customer,id',
            'agent_id' => 'required|exists:agent,id',
        ], [], [
            'id'=>'红包id',
            'agent_id'=>'经纪人id'
        ]);

        if ($validator->fails()) {
            $warnings = $validator->messages()->all();
            return ['message' => $warnings[0], 'status' => false];
        }

        $list = AgentCustomer::with('user', 'contract')->where('id', $id)->first();

        $data = [];
        //投资人
        $data['customer_name'] = $list->user->realname ? $list->user->realname : $list->user->nickname;
        //加盟品牌
        $data['brand_name'] = $list->contract->brand->name;
        //加盟金额
        $data['amount'] = $list->contract->amount;
        //促单经纪人
        $data['follow_agent'] = $list->contract->agent->realname;
        //邀请经纪人
        $data['invite_agent'] = Agent::find($agent_id)->realname;
        //成交时间
        $data['active_time'] = date('Y.m.d H:i:s', $list->contract->created_at->timestamp);
        //激活状态
        $data['status'] = $list->contract_id?1:0;
        //入账时间
        $data['in_time'] = date('Y.m.d H:i:s', $list->created_at->timestamp);

        return ['message' => $data, 'status' => true];
    }



    /**
     * 经纪人集赞截屏上传
     *
     * @param $input
     * @return array
     * @author tangjb
     */
    public function postScreenCapture($input)
    {
        $url = array_get($input, 'url');
        $agent_id = array_get($input, 'agent_id');
        $validator = \Validator::make($input, [
            'url' => 'required',
            'agent_id' => 'required|exists:agent,id',
        ], [], [
            'agent_id'=>'经纪人id',
            'url'=>'图片'
        ]);

        if ($validator->fails()) {
            $warnings = $validator->messages()->all();
            return ['message' => $warnings[0], 'status' => false];
        }

        $exist = AgentScreenCapture::where('agent_id', $agent_id)->whereIn('status', [0,1])->first();

        if($exist){
            return ['message' => '你已经上传过一个截屏', 'status' => false];
        }

        $res = AgentScreenCapture::create([
            'agent_id'=>$agent_id,
            'url'=>$url
        ]);

        if($res){
            return ['message' => '上传成功，请等待审核', 'status' => true];
        }else{
            return ['message' => '上传失败', 'status' => false];
        }
    }
}