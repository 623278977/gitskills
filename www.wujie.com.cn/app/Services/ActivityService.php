<?php

namespace App\Services;

use App\Models\Agent\Agent;
use App\Models\Live\Entity as LiveModel;
use App\Models\User\Ticket;
use App\Models\Orders\Entity as Orders;
use App\Http\Libs\Weixin\Lib\WxPayApi;
use App\Http\Libs\Weixin\Lib\WxPayOrderQuery;
use DB;
use App\Models\Identify;
use App\Models\User\Entity as User;
use App\Models\Activity\Ticket as ActivityTicket;
use App\Models\Order\Entity as Order;
use App\Models\Activity\Entity as Activity;
use App\Models\Activity\Sign as ActivitySign;
use App\Models\Message;
use App\Models\Live\Subscribe;
use App\Models\Live\Entity as Live;

use App\Models\ScoreLog;
use App\Models\Activity\Banner;
use App\Models\Agent\Activity\Sign as AgentActivitySign;
class ActivityService
{
    /**
     * 作用:获取一场活动的banner
     * 参数:$id 活动id
     *
     * 返回值:int
     */
    public function banners($activity_id)
    {
        $banners = Banner::where('activity_id', $activity_id)->select('src')->get();
        foreach ($banners as $k => $v) {
            $v->src = getImage($v->src, 'activity', '', 0);

        }

        return $banners;
    }


    /**
     * 作用:判断一场活动id获取主办场地maker_id
     * 参数:$id 活动id
     *
     * 返回值:int
     */
    public function getOidByAid($id)
    {
        $live = \DB::table('activity')
            ->leftJoin('activity_maker', 'activity.id', '=', 'activity_maker.activity_id')
            ->where('activity_maker.type', 'organizer')
            ->where('activity_maker.status', 1)
            ->where('activity_maker.activity_id', $id)
            ->select('activity_maker.maker_id')
            ->first();


        return $live->maker_id;
    }


    /**
     * 去第三方库查询是否完成支付
     *
     * @param $order_no
     * @param int $is_orders 是order表还是orders表
     * @return bool|int
     * @author tangjb
     * todo  银联支付的情况没有考虑
     */
    public function postThirdResult($order_no, $is_orders = 0)
    {
        if ($is_orders == 0) {
            $order = DB::table('order')->where('order_no', $order_no)->first();
        } else {
            $check = Orders::check($order_no);
            return $check;
        }
        if (!is_object($order) || !in_array($order->pay_way, ['ali', 'weixin', 'unionpay'])) {
            return -1;
        }

        if ($order->pay_way == 'weixin') {
            $pay = new WxPayApi();
            $query = new WxPayOrderQuery();
            $query->SetOut_trade_no($order->order_no);
            $result = $pay->orderQuery($query);
            if (isset($result['trade_state']) && $result['trade_state'] == 'SUCCESS') {
                DB::table('order')->where('order_no', $order_no)->update(['status' => 1, 'third_no' => 'weixin-' . $result['transaction_id'], 'updated_at' => time()]);
                DB::table('user_ticket')->where('order_id', $order->id)->where('uid', $order->uid)->update(['status' => 1, 'updated_at' => time()]);

                return true;
            }
            return false;
        } else {
            //构造请求参数
            $parameter = array(
                'service' => 'single_trade_query',
                'partner' => config('alipay.alipay.partner'),
                '_input_charset' => strtolower('utf-8'),
                'out_trade_no' => $order_no,
            );
            ksort($parameter);
            reset($parameter);
            $param = '';
            $sign = '';
            foreach ($parameter as $key => $val) {
                $param .= "$key=" . urlencode($val) . "&";
                $sign .= "$key=$val&";
            }
            $param = substr($param, 0, -1);
            $sign = substr($sign, 0, -1) . config('alipay.alipay.key');
            $url = 'https://mapi.alipay.com/gateway.do?' . $param . '&sign=' . md5($sign) . '&sign_type=MD5';
            $result = file_get_contents($url);
            $result = $this->FromXml($result);
            if (isset($result['response']['trade']['trade_status']) && in_array(
                    $result['response']['trade']['trade_status'],
                    [
                        'WAIT_SELLER_SEND_GOODS',
                        'WAIT_BUYER_CONFIRM_GOODS',
                        'TRADE_FINISHED',
                        'WAIT_SYS_PAY_SELLER',
                        'TRADE_PENDING',
                        'TRADE_SUCCESS',
                        'BUYER_PRE_AUTH'
                    ]
                )
            ) {
                DB::table('order')->where('order_no', $order_no)->update(['status' => 1, 'third_no' => 'ali-' . $result['response']['trade']['trade_no'], 'updated_at' => time()]);
                DB::table('user_ticket')->where('order_id', $order->id)->where('uid', $order->uid)->update(['status' => 1, 'updated_at' => time()]);

                return true;
            } else {
                return false;
            }
        }
    }


    /**
     * 将xml转为array
     *
     * @param string $xml
     * @throws WxPayException
     */
    private function FromXml($xml)
    {
        //将XML转为array
        //禁止引用外部xml实体
        libxml_disable_entity_loader(true);
        $this->values = json_decode(json_encode(simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA)), true);

        return $this->values;
    }


    /**
     * 作用:根据order_no， 获取此订单的各方面的信息  --数据中心版
     * 参数:$id 活动id
     *
     * 返回值:int
     */
    public function getOrderDetail($order_no)
    {
        $order = \DB::table('order')
            ->leftJoin('activity_ticket', 'activity_ticket.id', '=', 'order.ticket_id')
            ->leftJoin('activity', 'activity_ticket.activity_id', '=', 'activity.id')
            ->leftJoin('user_ticket', 'user_ticket.order_id', '=', 'order.id')
            ->leftJoin('activity_sign', 'activity_sign.ticket_no', '=', 'user_ticket.ticket_no')
            ->where('order.order_no', $order_no)
            ->select('activity.subject', 'activity.begin_time', 'activity.id', 'activity_sign.name', 'activity_sign.agent_id',
                'activity_sign.tel', 'activity_sign.company', 'activity_sign.job', 'activity_sign.non_reversible','user_ticket.maker_id',
                'activity_ticket.type', 'activity_ticket.name as ticket_name', 'activity_ticket.price',
                'user_ticket.score_price')
            ->first();

        $order->tel = getRealTel($order->non_reversible, 'wjsq');
        $order->begin_time = date('Y年m月d日 H:i', $order->begin_time);
        $order->ticket_type = $order->type;
        if ($order->type == 1) {
            $zone_name = \DB::table('maker')
                ->leftJoin('zone', 'maker.zone_id', '=', 'zone.id')
                ->where('maker.id', $order->maker_id)->select('zone.name')->first();
            $order->type = '现场票-' . str_replace('市', '', $zone_name->name);
            $order->zone_name = abandonProvince($zone_name->name);
        } else {
            $order->type = '直播票';
        }
        $images = $this->getSignUsers($order->id);
        $order->images = $images;
        $order->images_count = count($images);
        $order->price = $order->price == 0 ? $order->price = '免费' : $order->price;
        $order->score_price = $order->score_price == 0 ? $order->score_price = '免费' : $order->score_price;

        if ($order->agent_id) {
            $agent = Agent::where('id', $order->agent_id)->first();
            
            if($agent->is_public_realname && $agent->realname){
                $order->agent_name =$agent->realname;
            }else{
                $order->agent_name = $agent->nickname;
            }
        }


        return $order;
    }

    /**
     * 作用:根据activity_id， 获取已经报名了的会员头像 --数据中心版
     * 参数:$id 活动id
     *
     * 返回值:int
     */
    public function getSignUsers($activity_id)
    {
        $anony_signs = ActivitySign::select('uid', 'image', 'name', 'tel', 'non_reversible', 'created_at')
            ->addSelect(\DB::raw("'user' type"))
            ->where('activity_id', $activity_id)
            ->where('status', -3);

        $agent_signs = AgentActivitySign::select('agent_id as uid', 'image', 'name', 'tel', 'non_reversible', 'created_at')
            ->addSelect(\DB::raw("'agent' type"))
            ->where('activity_id', $activity_id)
            ->whereIn('status', [0, 1]);

        $signs = ActivitySign::select('uid', 'image', 'name', 'tel', 'non_reversible', 'created_at')
            ->where('activity_id', $activity_id)
            ->where('uid', '<>', 0)
            ->whereIn('status', [0, 1])
            ->union($anony_signs)
            ->union($agent_signs)
            ->orderBy('created_at', 'desc')
            ->addSelect(\DB::raw("'user' type"))
            ->get();


//        dd($signs);

        $sign_users = [];
        foreach ($signs as $key => $val) {
            if ($val->uid > 0) {
                $user = \DB::table('user')->where('status', 1)
                    ->where('uid', $val->uid)->select('avatar', 'non_reversible', 'username')->first();

                if (is_object($user)) {

                    $sign_users[$key]['image'] = getImage($user->avatar, 'avatar', '', 0);
                    $user->avatar ? $sign_users[$key]['has_image'] = 1 : $sign_users[$key]['has_image'] = 0;

                    $sign_users[$key]['name'] = $val->name;
                    //如果 是代报名
                    if ($user->non_reversible != $val->non_reversible) {
                        $sign_users[$key]['image'] = \Illuminate\Support\Facades\URL::asset('/') . "images/default/avator-m.png";
                        $sign_users[$key]['has_image'] = 0;
                    }
                }
            } else {
                $val->image && $sign_users[$key]['image'] = getImage($val->image, 'user', '', 0);
                $val->image && $sign_users[$key]['has_image'] = 1;
                $val->image && $sign_users[$key]['name'] = $val->name;
            }
        }

        $sign_users = collect($sign_users)->sortByDesc('has_image');
        return $sign_users;
    }


    /**
     * 分享页面报名的处理
     * @User yaokai
     * @param $data
     * @return array
     */
    private function htmlApply($data)
    {

        //伪号码
        $username = pseudoTel($data['tel']);

        //用户加密后的手机号
        $non_reversible = encryptTel($data['tel']);

        $user = User::getRow(['non_reversible' => $non_reversible]);
        if(is_object($user)){
            return ['user'=>$user,'is_register'=>1,'status'=>true];
        }else{
            //数据中心处理
            $url = config('system.data_center.hosts') . config('system.data_center.encrypt');
            $datas = [
                'nation_code' => $data['nation_code'],
                'tel' => $data['tel'],
                'platform' => 'wjsq',//来源无界商圈注册
                'en_tel' => $non_reversible,//通过加盐后得到手机号码
            ];

            //请求数据中心接口
            $result = json_decode(getHttpDataCenter($url, '', $datas));


            //如果异常则停止
            if (!$result) {
                return ['status' => FALSE, 'message' => '服务器异常！'];
            } elseif ($result->status == false) {
                return ['status' => false, 'message' => $result->message];
            }

            $user = User::create(['username'=>$username,'non_reversible'=>$non_reversible, 'password'=>md5($data['tel']), 'nickname'=>  uniqid().mt_rand(10000, 99999), 'realname'=>$data['name'], 'source'=>'5']);
            $user->nickname = 'wjsq'.$user->uid;
            $user->save();
            return ['user'=>$user,'is_register'=>0,'status'=>true];
        }
    }


    /**
     * 报名不支付  --数据中心版
     * @User
     * @param $data
     * @return array
     */
    public function postApplyNoPay($data)
    {
        if (!isset($data['source_uid'])) {
            $data['source_uid'] = 0;
        }
        $agent_id = array_get($data, 'agent_id', 0);
        $is_invite = array_get($data, 'is_invite', 0);
        $user = User::getRow(['uid' => $data['uid']]);
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
        $num = DB::table('user_ticket')->where('ticket_id', $data['ticket_id'])->whereIn('status', [0, 1])->count();

        if ($num >= $ticket->num) {
            return ['data' => '票已售完', 'status' => false];
        }

        if ($ticket->type == 2 && $data['maker_id'] == 0) {  //获取该场活动主办场地id
            $data['maker_id'] = $this->getOidByAid($ticket->activity_id);
        }

        $exist = Ticket::getRow(['uid' => $data['uid'], 'ticket_id' => $data['ticket_id'], 'maker_id' => $data['maker_id'], 'status' => 0]);

        if (is_object($exist)) {
            return ['data' => '关于该活动已经存在了一个未支付的门票，请先支付，再重新下单', 'status' => false];
        }

        $activity = Activity::getRow(['id' => $data['activity_id']]);

        //判断活动是否已结束
        if ($activity->end_time < time()) {
            return ['data' => '该场活动已经结束', 'status' => false];
        }

        if ($data['pay_way'] == 'none') {
            if ($data['cost'] != 0) {
                return ['data' => '免费票的价格必须为0', 'status' => false];
            }
            if ($data['score_num'] != 0) {
                return ['data' => '免费票的使用的积分必须为0', 'status' => false];
            }
            //先下单
            $order = Order::place(
                $user->uid,
                $data['ticket_id'],
                $data['cost'],
                $data['product'],
                $data['body'],
                $data['pay_way'],
                $data['score_num'],
                $data['score_num'] / $data['rate'],
                ($data['cost'] - ($data['score_num'] / $data['rate'])),
                1,
                $ticket->type == 1 ? 'activity' : 'live'
            );

            //出票
            $u_ticket = Ticket::produce($user->uid, $order->id, $data['ticket_id'], $data['maker_id'], $ticket->type, $data['cost'], 1);
            ActivityTicket::incre(['id' => $data['ticket_id']], ['surplus' => -1]);
            if ($ticket->type == 1) {
                //活动报名
                $result = ActivitySign::apply($user->uid, $data['maker_id'], $data['activity_id'], $data['company'],
                    $data['job'], $u_ticket->ticket_no, 0, $data['name'], pseudoTel($data['tel']), encryptTel($data['tel']),  $data['source_uid'], $is_invite, $agent_id);
            }
            //发消息
            $a_ticket = ActivityTicket::getRow(['id' => $data['ticket_id']]);
            Activity::sendMessage($user->uid, $data['activity_id'], $u_ticket->id);
            if ($a_ticket->type == 1) {
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
                //因短信平台原因可能取消动态url短信
                $url = config('app.app_url') . 'activity/detail/' . config('app.version') . '?pagetag=02-2&id=' . $activity->id . '&is_share=1';
                @SendTemplateSMS('activitySiteSign', $user->non_reversible, 'activitySiteSign', [
                    'name' => $activity->subject,
                    'time'=>date('m月d日H点i分', $activity->begin_time),
                    'url' => shortUrl($url)
                ], $user->nation_code);
            }
            if ($a_ticket->type == 2) {
                //订阅该直播
                $live = Live::where('activity_id', $data['activity_id'])->first();
                //因平台原因可能取消动态url短信
                $live_url = config('app.app_url') . 'live/detail/' . config('app.version') . '?pagetag=' . config('app.live_detail') . '&id=' . $live->id . '&is_share=1';
                @SendTemplateSMS('activityLiveSign', $user->non_reversible, 'activityLiveSign', [
                    'name' => $activity->subject,
                    'time'=>date('m月d日H点i分', $activity->begin_time),
                    'url' => shortUrl($live_url)
                ], $user->nation_code);
                Subscribe::subscribe(['uid' => $user->uid, 'live_id' => $live->id, 'type' => 1]);
            }
            //加积分
            if ($a_ticket->type == 1) {
                Activity::addScore($user->uid, $result->id);
            }
        } else {
            if (!$data['cost'] > 0.01) {
                return AjaxCallbackMessage('费用不允许为0', false);
            }
            //先下单
            $order = Order::place(
                $data['uid'],
                $data['ticket_id'],
                $data['cost'],
                $data['product'],
                $data['body'],
                $data['pay_way'],
                $data['score_num'],
                $data['score_num'] / $data['rate'],
                ($data['cost'] - ($data['score_num'] / $data['rate'])),
                0,
                $ticket->type == 1 ? 'activity' : 'live'
            );

            //出票
            $u_ticket = Ticket::produce($data['uid'], $order->id, $data['ticket_id'], $data['maker_id'], $ticket->type, $data['cost']);
            if ($ticket->type == 1) {
                //活动报名 直播票也要报名
                $result = ActivitySign::apply($data['uid'], $data['maker_id'], $data['activity_id'], $data['company'],
                    $data['job'], $u_ticket->ticket_no, -1, $data['name'], $data['tel'], $data['source_uid'],$is_invite, $agent_id);
            }

            //减去积分
            is_object($u_ticket) && ScoreLog::add($data['uid'], $data['score_num'], 'ticket_buy', '活动报名使用积分', -1, false);
        }
        if (is_object($order) && $data['path'] == 'html5') {
            return ['data' => ['order_no' => $order['order_no'], 'is_register' => $is_register, 'activity_sign_id' => $result->id], 'status' => true];
        } elseif (is_object($order)) {
            return ['data' => $order['order_no'], 'status' => true];
        } else {
            return ['data' => '报名失败', 'status' => false];
        }
    }


}