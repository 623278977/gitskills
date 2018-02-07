<?php

namespace App\Services\Version\Activity;

use App\Jobs\SendRemindSMS;
use App\Models\Agent\Agent;
use App\Exceptions\ExecuteException;

use App\Models\Agent\AgentCustomer;
use App\Models\Agent\Entity\_v010200 as Agentv010200;
use App\Models\Agent\Score\AgentScoreLog;
use App\Models\Agent\TraitBaseInfo\RongCloud;
use App\Models\Live\Entity as Live;
use Illuminate\Support\Facades\DB;
use App\Models\User\Entity as User;
use App\Models\Activity\Ticket as ActivityTicket;
use App\Models\Activity\Entity as Activity;
use App\Models\Order\Entity as Order;
use App\Models\Activity\Sign as ActivitySign;
use App\Models\User\Ticket as UserTicket;
use App\Models\Message;
use App\Models\Live\Subscribe;
use App\Models\ScoreLog;
use App\Services\ActivityService;
use App\Models\Orders\Entity as Orders;
use App\Models\Agent\Invitation;
use App\Models\Agent\Activity\Sign as AgentActivitySign;
use Illuminate\Foundation\Bus\DispatchesJobs;
class _v020800 extends _v020700
{
    use RongCloud , DispatchesJobs;
    /**
     * 获取活动的报名信息
     */
    public function postEnrollInfos($param)
    {
        $parentData = parent::postEnrollInfos($param);
        $data = $parentData['message'];
        $agentId = intval($param['agent_id']);
        $uid = intval($param['uid']);
        if (!empty($agentId) && !empty($uid)) {
            $agentInfo = Agent::where('id', $agentId)->where('status', 1)->first();
            if (!is_object($agentInfo)) {
                return ['status' => false, 'message' => "请输入有效的经纪人id"];
            }
            $arr = Agent::getAgentEnroll($agentId, $uid);
            $data['agent'] = $arr;
        }
        return ['status' => true, 'message' => $data];
    }


    /**
     * 使用积分 购买现场票或直播票  --数据中心版
     * @User tangjb
     * @param $data
     * @return array
     */
    public function postApplyAndPay($data)
    {
        //伪号码
        $username = pseudoTel($data['tel']);

        //用户加密后的手机号
        $non_reversible = encryptTel($data['tel']);

        $user = User::getRow(['uid' => $data['uid']]);

        //如果该号码没有注册过，就去沉淀

        if ($user->non_reversible != $non_reversible) {
            depositTel($data['tel'], encryptTel($data['tel']), 'wjsq');
        }

        if(!empty($data['is_invite'])){
            if(empty($data['agent_id'])){
                return ['message' => '经纪人id不能为空', 'status' => false];
            }
            if(empty($data['invitation_id'])){
                return ['message' => '邀请函id不能为空', 'status' => false];
            }
        }
        if (isset($data['share_mark']) && $data['share_mark']) {
            $share_remark = \Crypt::decrypt($data['share_mark']);
            $md5 = substr($share_remark, 0, 32);
            if ($md5 != md5($_SERVER['HTTP_HOST'])) {
                return ['message' => '分享码有误', 'status' => false];
            }
            $share_remark = explode('&', substr($share_remark, 44));
            $data['source_uid'] = $share_remark[2];
        } else {
            $data['source_uid'] = 0;
        }

        //判断其是否已经报名
        $sign = ActivitySign::whereIn('status', [0, 1])->where(['non_reversible' => $non_reversible, 'activity_id' => $data['activity_id']])->first();
        $agent_sign = AgentActivitySign::whereIn('status', [0, 1])->where(['non_reversible' => $non_reversible, 'activity_id' => $data['activity_id']])->first();

        if (is_object($sign) || is_object($agent_sign)) {
            return ['data' => '该号码已报名活动', 'status' => false];
        }

        $is_register = 1;
        if ($data['path'] == 'html5') {
            $htmlApply = $this->htmlApply($data);

            //数据中心处理错误了直接返回
            if ($htmlApply['status'] == false){
                return ['data' => $htmlApply['message'], 'status' => false];
            }

            $is_register = $htmlApply['is_register'];
            $user = $htmlApply['user'];
            if (!$htmlApply['user']) {
                return ['data' => '分享页面报名出现错误', 'status' => false];
            }
        }

        $ticket = ActivityTicket::getRow(['id' => $data['ticket_id']]);

        if ($ticket->type == 1 && !$data['maker_id']) { //现场票一定要传空间
            return ['data' => '现场票一定要传maker_id', 'status' => false];
        }
        else if($ticket->type == 2) {
            $data['maker_id'] = 0;
        }

        $activity = Activity::getRow(['id' => $data['activity_id']]);

        empty($data['product']) && $data['product'] = $activity->subject;
        empty($data['body']) && $data['body'] = $activity->subject;
        empty($data['name']) && $data['name'] = $user->nickname;
        empty($data['company']) && $data['company'] = '';
        empty($data['job']) && $data['job'] = '';

        if(isset($data['agent_id'])){
            $data['score_num'] = 0;
        }else{
            $data['agent_id'] = 0;
        }

        if (empty($data['tel'])){
            $non_reversible =$user->non_reversible;
            $username =$user->username;
        }


        $num = DB::table('user_ticket')->where('ticket_id', $data['ticket_id'])->whereIn('status', [0, 1])->count();
        if ($num >= $ticket->num) {
            return ['data' => '票已售完', 'status' => false];
        }

        if ($ticket->type == 2 && $data['maker_id'] == 0) {  //获取该场活动主办场地id
            $data['maker_id'] = $this->getOidByAid($ticket->activity_id);
        }

        $exist = UserTicket::getRow(['uid' => $data['uid'], 'ticket_id' => $data['ticket_id'], 'maker_id' => $data['maker_id'], 'status' => 0]);

        if (is_object($exist)) {
            return ['data' => '关于该活动已经存在了一个未支付的门票，请先支付，再重新下单', 'status' => false];
        }
        //判断活动是否已结束
        if ($activity->end_time < time()) {
            return ['data' => '该场活动已经结束', 'status' => false];
        }
        $rate = config('system.score_rate');
        if (isset($data['is_invite'])) {
            $is_invite = 1;
            $data['score_num'] = 0;
        } else {
            $is_invite = 0;
        }

        //开始事务
        \DB::beginTransaction();
        try {
            //下单
            $order = Order::place(
                $user->uid,
                $data['ticket_id'],
                $data['score_num'] / $rate,
                $data['product'],
                $data['body'],
                'score',
                $data['score_num'],
                $data['score_num'] / $rate,
                0,
                1,
                $ticket->type == 1 ? 'activity' : 'live'
            );
            //出票
            $u_ticket = UserTicket::produce($user->uid, $order->id, $data['ticket_id'], $data['maker_id'],
                $ticket->type, 0, 1, $data['score_num'], $is_invite);
            ActivityTicket::incre(['id' => $data['ticket_id']], ['surplus' => -1]);

            $result = ActivitySign::apply($user->uid, $data['maker_id'], $data['activity_id'], $data['company'], $data['job'],
                $u_ticket->ticket_no, 0, $data['name'], $username, $non_reversible, $data['source_uid'], $is_invite, $data['agent_id']);

            if(!empty($data['agent_id']) && $data['agent_id']){
                //给积分
                Agentv010200::add($data['agent_id'], AgentScoreLog::$TYPES_SCORE[8], 8, '成功邀请投资人参加活动', $result->id, 1);
            }

            //发短信 和站内信
            $a_ticket = ActivityTicket::getRow(['id' => $data['ticket_id']]);
            Activity::sendMessage($user->uid, $data['activity_id'], $u_ticket->id);

            if ($a_ticket->type == 1) {  //如果是现场票
                $buy_type = 'site_ticket_buy';
                Message::create(
                    [
                        'title' => $activity->subject . '报名成功',
                        'uid' => $user->uid,
                        'content' => '您已成功报名' . $activity->subject,
                        'type' => 2,
                        'post_id' => $activity->id,
                        'url' => 'user_ticket_id=' . $u_ticket->id,
                        'send_time' => time(),
                    ]
                );
            }

            //如果是直播还要订阅
            if ($a_ticket->type == 2) {
                $buy_type = 'live_ticket_buy';
                //订阅该直播
                $live = Live::where('activity_id', $data['activity_id'])->first();
                Subscribe::subscribe(['uid' => $user->uid, 'live_id' => $live->id, 'type' => 1]);
            }

            //使用积分
            ScoreLog::add($data['uid'], $data['score_num'], $buy_type, '活动票或直播票购买使用积分', -1, false);
            //如果是通过邀请渠道，报名成功，修改邀请函状态
            if($is_invite && $data['invitation_id']) {
                Invitation::where('id', $data['invitation_id'])->update(['status' => 1]);
            }

            //发送融云消息
            $return_result = AgentCustomer::instance()->gainCustomerAgentRelationDatas($user->uid);
            if ($return_result && $data['agent_id'] != $return_result->agent_id) {
                $activity_result = Activity::where('id', $data['activity_id'])->first();
                $this->_activitySignSuccessSendInfos([
                    'customer_id'   => $user->uid,
                    'agent_id'      => $return_result->agent_id,
                    'activity_name' => $activity_result->subject,
                    'activity_time' => date("Y年m月d日", $activity_result->begin_time),
                    'activity_make' => DB::table('maker')->where('id', $data['maker_id'])->value('subject'),
                ]);
            }

            \DB::commit();
        } catch (\Exception $e) {
            \DB::rollBack();
            throw(new ExecuteException('报名失败，出现异常' . $e->getMessage()));
        }

        //发短信
        if ($a_ticket->type == 1) {  //如果是现场票
            //因短信平台原因可能取消动态url短信
            $url = config('app.app_url') . 'activity/detail/' . config('app.version') . '?pagetag=02-2&id=' . $activity->id . '&is_share=1';
            $param = [
                'content'=>'activityLiveSign',
                'strMobile'=>$user->non_reversible,
                'type'=>'activityLiveSign',
                'tag'=> [
                    'name' => $activity->subject,
                    'time' => date('m月d日H点i分', $activity->begin_time),
                    'url' => shortUrl($url)
                ],
                'is_md5'=>true,
            ];

            $this->dispatch(new SendRemindSMS($param));

//            @SendTemplateSMS('activitySiteSign', $user->non_reversible, 'activitySiteSign', [
//                'name' => $activity->subject,
//                'time' => date('m月d日H点i分', $activity->begin_time),
//                'url' => shortUrl($url)
//            ], $user->nation_code);

        }


        if ($a_ticket->type == 2) {
            //因平台原因可能取消动态url短信
            $live_url = config('app.app_url') . 'live/detail/' . config('app.version') . '?pagetag=' . config('app.live_detail') . '&id=' . $live->id . '&is_share=1';

            $param = [
                'content'=>'activityLiveSign',
                'strMobile'=>$user->non_reversible,
                'type'=>'activityLiveSign',
                'tag'=> [
                    'name' => $activity->subject,
                    'time' => date('m月d日H点i分', $activity->begin_time),
                    'url' => shortUrl($live_url)
                ],
                'is_md5'=>true,
            ];

            $this->dispatch(new SendRemindSMS($param));

//            @SendTemplateSMS('activityLiveSign', $user->non_reversible, 'activityLiveSign', [
//                'name' => $activity->subject,
//                'time' => date('m月d日H点i分'),
//                'url' => shortUrl($live_url)
//            ], $user->nation_code);

        }

        if (is_object($order) && $data['path'] == 'html5') {
            return ['data' => ['order_no' => $order['order_no'], 'is_register' => $is_register, 'activity_sign_id' => $result->id], 'status' => true];
        } elseif (is_object($order)) {
            return ['data' => $order['order_no'], 'status' => true];
        } else {
            return ['data' => '报名失败', 'status' => false];
        }

    }


    /**
     * 检测支付结果并改变活动报名及订单状态  --数据中心版
     * @param $param
     * @return array
     * @author tangjb
     */
    public function postCheckAndApply($param)
    {
        $activityService = new ActivityService();
        $order = Order::with('ticket')->where('order_no', $param['order_no'])->first();
        if ($order->status == 1 || $order->status == 2) {
            $data = $activityService->getOrderDetail($param['order_no']);
        } else {
            $is_orders = 0;
            //检验
            if (strstr($param['order_no'], 'video_id')) {
                $param['order_no'] = substr($param['order_no'], 8);
                $is_orders = 1;
            }
            $pay_result = $activityService->postThirdResult($param['order_no'], $is_orders);
            if ($pay_result == 1) {
                $data = $activityService->getOrderDetail($param['order_no']);
            } else {
                return ['data' => '支付失败', 'status' => false];
            }
        }

        return ['data' => $data, 'status' => true];
    }

    /**
     * 内部调用---报名成功后发送融云消息  author zhaoyf
     *
     * @param = [
     *      'customer_id'   => '用户ID',
     *      'agent_id'      => '经纪人ID'
     *      'activity_name' => '活动名称',
     *      'activity_time' => '活动时间' //某年某月某日,
     *      'activity_make' => '活动场地',
     * ]
     *
     * @return bool
     */
    private function _activitySignSuccessSendInfos(array $param)
    {
        $_datas = trans('tui.confirm_join_activity', [
            'activity_name' => $param['activity_name'],
            'activity_time' => $param['activity_time'],
            'activity_zone' => $param['activity_make']
        ]);

        return $send_notice_result = SendCloudMessage($param['customer_id'],'agent'.$param['agent_id'],  $_datas, 'RC:TxtMsg', '', true, 'one_user');
    }

}