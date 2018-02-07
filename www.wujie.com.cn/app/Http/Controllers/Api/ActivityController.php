<?php
namespace App\Http\Controllers\Api;

use App\Http\Requests\Activity\ApplyAndPayRequest;
use App\Http\Requests\Activity\ApplyRequest;
use App\Http\Requests\Activity\CheckRequest;
use App\Http\Requests\Activity\IncreRequest;
use App\Http\Requests\Activity\PadSignRequest;
use App\Http\Requests\Activity\ScrollsRequest;
use App\Http\Requests\Activity\SignAndPayRequest;
use App\Http\Requests\Activity\SignRequest;
use App\Http\Requests\Activity\TelAppliedRequest;
use App\Http\Requests\Activity\TempSignRequest;
use App\Http\Requests\ApplyActiviyRequest;
use App\Http\utils\randomViewUtil;
use App\Models\Activity\Maker;
use App\Models\Activity\Sign;
use App\Models\Maker\Entity;
use App\Models\Order\Entity as Order;
use App\Models\User\Favorite;
use App\Models\User\Entity as User;
use App\Models\User\Share;
use App\Models\User\Ticket;
use App\Models\Activity\Ticket as ActivityTicket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Activity\Entity as Activity;
use App\Models\Live\Entity as Live;
use App\Models\Live\Subscribe;
use App\Models\Activity\Sign as ActivitySign;
use App\Models\Message;
use Illuminate\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator as lPaginator;
use App\Models\Activity\ApplyActiviy;
use DB, \Exception;
use App\Http\Libs\Weixin\JsApiPay;
use App\Http\Libs\Weixin\Lib\WxPayNotifyReply;
use App\Http\Libs\Weixin\Lib\WxPayApi;
use App\Http\Libs\Weixin\Lib\WxPayUnifiedOrder;
use App\Http\Libs\Weixin\Lib\WxPayDataBase;
use App\Http\Libs\Weixin\Lib\WxPayOrderQuery;
use App\Models\ScoreLog;
use App\Models\Identify;
use App\Models\Orders\Entity as Orders;
use App\Services\ActivityService;
class ActivityController extends CommonController
{
    /**
     * 活动列表
     */
    public function postList(Request $request ,$version = null)
    {

        $pageSize = $request->get('pageSize', 15);
        $page = $request->get('page', 1);
        $maker_id = $request->get('maker_id', 0);
        $keywords = $request->get('keywords', '');
        $keyword = $request->get('keyword', '');
        $keywords = empty($keywords) ? $keyword :$keywords;
        $vip_id = $request->get('vip_id', 0);
        $type = $request->get('type', 0);
        $hotwords = $request->get('hotwords', '');
//        if($maker_id == ''){
//            return AjaxCallbackMessage('OVO中心ID必传',false);
//        }

        $data = Activity::getActivityListOfMaker($maker_id, $type, $pageSize,$vip_id,$keywords, $page ,$version ,$hotwords);
        $is_return = $request->input('is_return') ?: 0;

        //v020700版本对接口做相关处理
//        $versionService = $this->init(__METHOD__, $version);
//        if($versionService){
//            $response = $versionService->bootstrap($data);
//            return ($is_return==1) ? $data: AjaxCallbackMessage($data, $response['status']);
//        }

        return ($is_return == 1) ? $data : AjaxCallbackMessage($data, true);
    }

    /**
     * 活动列表
     */
    public function postScrolls(ScrollsRequest $request)
    {
        $size = $request->get('size', 4);
        $lists = Activity::where('status', 1)->where('end_time', '>', time())
            ->orderBy('begin_time', 'desc')->limit($size)->select('id', 'banner', 'subject')->get();
        foreach($lists as $k=>$v){
            //banner，没有banner就取list_img。
            if($v->banner){
                $v->banner = getImage($v->banner,'activity', '', 0);
            }else{
                $v->banner = getImage($v->list_img,'activity', '', 0);
            }
            $maker = DB::table('activity_maker')->where('type', 'organizer')->where('activity_id', $v->id)->where('status', 1)->first();
            $v->maker_id = $maker->maker_id;
        }


        return AjaxCallbackMessage($lists, true);
    }



    /**
     * 获取某个活动详情
     */
    public function postDetail(Request $request, $version = null)
    {
        $data = $request->input();

        //初始化
        $versionService = $this->init(__METHOD__, $version);

        if($versionService){
            $response = $versionService->bootstrap($data, ['uid' => $this->uid]);
            return AjaxCallbackMessage($response['message'], $response['status']);
        }


        if (!isset($data['id'])) {
            return AjaxCallbackMessage('活动id必须要有', false);
        }
        if (floor($data['id']) != $data['id'] || $data['id'] <= 0) {
            return AjaxCallbackMessage('活动id必须要为正整数', false);
        }
        $id = (int)$data['id'];
        $maker_id = $request->get('maker_id', 0);
        if($maker_id == 0){
            //收藏没有maker_id随机取个
            $maker_id = \DB::table('activity_maker')
                ->where('activity_id',$id)
                ->groupBy('activity_id')
                ->first()->maker_id;
        }
        $list = Activity::detail($id, $this->uid, $maker_id);

        if ($this->uid > 0) {
            $follow = Activity::follow($list->id, $this->uid);
            $list->follow = $follow;
            $favorite = Activity::isFavorite($list->id, $this->uid);
            $list->favorite = $favorite;

        } else {
            $list->favorite = 0;
            $list->follow = -1;
        }

        $rec_lists = Activity::recommend($id);
        $uid = $request->get('uid',0);
        $position_id = $request->get('position_id',0);
        if($uid == 0){
            $maker_id =0;
            $position_id=0;
        }else{
            $user = DB::table('user')
                ->where('uid',$uid)
                ->first();
            $maker_id = $user->maker_id;
            $position_id = $user->zone_id;
        }
        $list->url = createUrl('activity/detail', array('id' => $data['id'], 'pagetag' => config('app.activity_detail')));
        $result['self'] = $list;
//        $result['rec'] = $rec_lists;
        $recs = Activity::getActivityOfInterested($data['id'],$maker_id,$position_id);
        foreach($recs as $key=>$rec){
            if($data['id']==$rec->id){
                unset($recs[$key]);
            }
        }
        $result['rec'] = $recs;
        $result['page_url'] = createUrl('activity/detail', array('id' => $data['id'], 'uid' => $this->uid, 'pagetag' => config('app.activity_detail')));
        $result['self']->is_shareable = Activity::canShare($id);
        $result['self']->vip = Activity::getVipInfo($id);

        return AjaxCallbackMessage($result, true);
    }

    /**
     * 报名不支付   --数据中心版
     * @User yaokai
     * @param ApplyRequest $request
     * @param ActivityService|null $activityService
     * @param null $version
     * @return string
     */
    public function postApplyNoPay(ApplyRequest $request, ActivityService $activityService = null, $version = null)
    {
        $data = $request->input();
        if (isset($data['version']) && $data['version'] > 3 && (!isset($data['name']) || !isset($data['tel']))) {
            return AjaxCallbackMessage('name和tel是必传参数', false, '');
        }
        $score_num = $request->get('score_num', 0);
        $name = $request->get('name', '');
        $tel = $request->get('tel', '');

        //fixme 手机号简单验证处理  2017.12.26  yaokai
        $nation_code = $request->get('nation_code', '86');
        if (!checkMobile($tel, $nation_code)) {
            return ['status' => FALSE, 'message' => '手机号格式不对'];
        }

        $data['pay_way'] = $request->input('pay_way', 'none');
        $data['company'] = $request->input('company', '');
        $data['job'] = $request->input('job', '');
        $data['path'] = $request->input('path', 'app');
        $rate = config('system.score_rate');

        //检查积分是否合法
        $check = Order::checkScore($score_num, $data['uid'], $rate);
        $arr = ['-1' => '不是正整数', '-2' => '积分大于会员拥有的积分', '-3' => '积分应该为汇率的百分之一的倍数'];
        if ($check != 1) {
            return AjaxCallbackMessage($arr[$check], false);
        }

        if($version){
            $versionService = $this->init(__METHOD__, $version);
            $result = $versionService->bootstrap($data, ['score_num'=>$score_num, 'name'=>$name, 'tel'=>$tel,
                                                         'pay_way'=>$data['pay_way'], 'company'=>$data['company'],
                                                         'job'=>$data['job'], 'path'=>$data['path'],
                                                         'rate'=>$rate
            ]);
            return AjaxCallbackMessage($result['data'],$result['status']);
        }

        $user = User::getRow(['uid' => $data['uid']]);
        $is_register=1;
        if($data['path'] =='html5'){
            $htmlApply= $this->htmlApply($data);
            $is_register = $htmlApply['is_register'];
            $user = $htmlApply['user'];
            if(!$htmlApply['user']){
                return AjaxCallbackMessage('分享页面报名出现错误',false);
            }
        }

        $ticket = ActivityTicket::getRow(['id' => $data['ticket_id']]);
        $num = DB::table('user_ticket')->where('ticket_id', $data['ticket_id'])->whereIn('status', [0, 1])->count();
        if ($num >= $ticket->num) {
            return AjaxCallbackMessage('票已售完', false);
        }

        if($ticket->type==2 && $data['maker_id']==0){
            $data['maker_id'] =$activityService->getOidByAid($ticket->activity_id);
        }

        $exist = Ticket::getRow(['uid' => $data['uid'], 'ticket_id' => $data['ticket_id'], 'maker_id' => $data['maker_id'], 'status' => 0]);

        if (is_object($exist)) {
            return AjaxCallbackMessage('关于该活动已经存在了一个未支付的门票，请先支付，再重新下单', false);
        }


        if ($data['pay_way'] == 'none') {
            if (!$data['cost'] == 0) {
                return AjaxCallbackMessage('免费票的价格必须为0', false);
            }
            if (!$score_num == 0) {
                return AjaxCallbackMessage('免费票的使用的积分必须为0', false);
            }
            //先下单
            $order = Order::place(
                $user->uid,
                $data['ticket_id'],
                $data['cost'],
                $data['product'],
                $data['body'],
                $data['pay_way'],
                $score_num,
                $score_num / $rate,
                ($data['cost'] - ($score_num / $rate)),
                1,
                $ticket->type == 1?'activity':'live'
            );

            //出票
            $u_ticket = Ticket::produce($user->uid, $order->id, $data['ticket_id'], $data['maker_id'], $ticket->type, $data['cost'], 1);
            ActivityTicket::incre(['id' => $data['ticket_id']], ['surplus' => -1]);
            if ($ticket->type == 1) {
                //活动报名
                $result = ActivitySign::apply($user->uid, $data['maker_id'], $data['activity_id'], $data['company'], $data['job'], $u_ticket->ticket_no, 0, $name, $tel);
            }
            //发消息
            $activity = Activity::getRow(['id' => $data['activity_id']]);
            $a_ticket = ActivityTicket::getRow(['id' => $data['ticket_id']]);
            Activity::sendMessage($user->uid, $data['activity_id'], $u_ticket->id);

            if ($a_ticket->type == 1) {
                Message::create(
                    [
                        'title'     => $activity->subject . '报名成功',
                        'uid'       => $user->uid,
                        'content'   => '您已成功报名' . $activity->subject,
                        'type'      => 2,
                        'post_id'   => $activity->id,
                        'url'       => 'user_ticket_id=' . $u_ticket->id,
                        'send_time' => time(),
                    ]
                );
                //因平台原因暂时取消动态url短信
                $url = config('app.app_url') . 'activity/detail/'.config('app.version').'?pagetag=02-2&id='.$activity->id.'&is_share=1';
                @SendTemplateSMS('activitySiteSign',$user->non_reversible,'activitySiteSign',[
                    'name' => $activity->subject,
                    'time' => date('m月d日 H点i分',$activity->begin_time),
                    'url'=>shortUrl($url)
                ],$user->nation_code);
            }
            if ($a_ticket->type == 2) {
                //订阅该直播
                $live = Live::where('activity_id', $data['activity_id'])->first();
                //因短信平台原因暂时取消动态url短信
                $live_url = config('app.app_url') . 'live/detail/'.config('app.version').'?pagetag='.config('app.live_detail').'&id='.$live->id.'&is_share=1';
                @SendTemplateSMS('activitySiteSign',$user->non_reversible,'activityLiveSign',[
                    'name' => $activity->subject,
                    'time' => date('m月d日 H点i分',$activity->begin_time),
                    'url'=>shortUrl($live_url)
                ],$user->nation_code);
                Subscribe::subscribe(['uid'=>$user->uid,'live_id'=>$live->id,'type'=>1]);
            }

            //加积分
            if ($ticket->type == 1) {
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
                $score_num,
                $score_num / $rate,
                ($data['cost'] - ($score_num / $rate)),
                0,
                $ticket->type == 1?'activity':'live'
            );

            //出票
            $u_ticket = Ticket::produce($data['uid'], $order->id, $data['ticket_id'], $data['maker_id'], $ticket->type, $data['cost']);
            //减去积分
            ScoreLog::add($data['uid'], $score_num, 'ticket_buy', '活动报名使用积分', -1, false, 'user_ticket', $u_ticket->id);
            if ($ticket->type == 1 ) {
                //活动报名
                $result = ActivitySign::apply($data['uid'], $data['maker_id'], $data['activity_id'], $data['company'], $data['job'], $u_ticket->ticket_no, -1, $name, $tel);
            }
        }
        if (is_object($order) && $data['path'] =='html5') {
            return AjaxCallbackMessage(['order_no'=>$order['order_no'], 'is_register'=>$is_register], true);
        } elseif(is_object($order)) {
            return AjaxCallbackMessage($order['order_no'], true);
        }else{
            return AjaxCallbackMessage('报名失败', false);
        }
    }

    /**
     * 分享页面报名的处理
     */
    private function htmlApply($data)
    {
        //验证
//        $identify=Identify::getRow(array(
//            'code'=>$data['code'],
//            'mobile'=>$data['tel'],
//            'type'=>'authorize'
//        ));
//
//        if($identify){
//            if((time()-strtotime($identify->created_at)) > 900){
//                return false;
//            }
//        }else{
//            return false;
//        }

        $user = User::getRow(['username' => $data['tel']]);
        if(is_object($user)){
            return ['user'=>$user,'is_register'=>1];
        }else{
            $user = User::create(['username'=>$data['tel'], 'password'=>md5($data['tel']), 'nickname'=>$data['tel'], 'realname'=>$data['name'], 'source'=>'5']);
            $user->nickname = 'wjsq'.$user->uid;
            $user->save();
            return ['user'=>$user,'is_register'=>0];
        }
    }



    /**
     * 检验支付结果并报名，供回调。
     */
    public function postCheckAndApply(CheckRequest $request, $version= null)
    {
        $data = $request->input();

        if($version){
            $versionService = $this->init(__METHOD__, $version);
            $result = $versionService->bootstrap($data);
            return AjaxCallbackMessage($result['data'],$result['status']);
        }

        $is_orders = 0;
        //检验
        if(strstr($data['order_no'], 'video_id')){
            $data['order_no'] = substr($data['order_no'],8);
            $is_orders = 1;
        }

        $pay_result = $this->postThirdResult($data['order_no'], $is_orders);

        if ($pay_result === -1) {
            return AjaxCallbackMessage('-1', false);
        }

        if ($pay_result === -2) {
            return AjaxCallbackMessage('支付失败', false);
        }

        if ($pay_result == true) {
            return AjaxCallbackMessage('1', true);
        } else {
            return AjaxCallbackMessage('0', true);
        }
    }


    /**
     * 去第三方库查询该笔订单的支付结果
     */
    private function postThirdResult($order_no, $is_orders=0)
    {
        if($is_orders==0){
            $order = DB::table('order')->where('order_no', $order_no)->first();
        }else{
            $check = Orders::check($order_no);
            return $check;
        }
        if (!is_object($order) || !in_array($order->pay_way, ['ali', 'weixin'])) {
            return -1;
        }

        if ($order->pay_way == 'weixin') {
            $pay = new WxPayApi();
            $query = new WxPayOrderQuery();
            $query->SetOut_trade_no($order->order_no);
            $result = $pay->orderQuery($query);
            if (isset($result['trade_state']) && $result['trade_state'] == 'SUCCESS') {
                DB::table('order')->where('order_no', $order_no)->update(['status' => 1, 'third_no' => 'weixin-'.$result['transaction_id'], 'updated_at' => time()]);
                DB::table('user_ticket')->where('order_id', $order->id)->where('uid', $order->uid)->update(['status' => 1, 'updated_at' => time()]);

                return true;
            }
            return false;
        } else {
            //构造请求参数
            $parameter = array(
                'service'        => 'single_trade_query',
                'partner'        => config('alipay.alipay.partner'),
                '_input_charset' => strtolower('utf-8'),
                'out_trade_no'   => $order_no,
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
                DB::table('order')->where('order_no', $order_no)->update(['status' => 1, 'third_no' => 'ali-'.$result['response']['trade']['trade_no'], 'updated_at' => time()]);
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
     * 某场活动的门票
     */
    public function postTickets(Request $request , $version = null)
    {
        //初始化
        $versionService = $this->init(__METHOD__, $version);

        if($versionService){

            $response = $versionService->bootstrap($request->all(),['version' => $version]);
            return AjaxCallbackMessage($response['message'],$response['status']);
        }

        $data = $request->input();
        $list = Activity::tickets($data['id'], 0, 1, $this->uid);

        return AjaxCallbackMessage($list, true);
    }

    /**
     * 获取主办方活动
     *
     * @param Request $param
     * @return string
     */
    public function postOrganizerlist($param)
    {
        if (empty($param['organizer_id'])) {
            return '操作异常';
        }
        $data = Activity::organizerList($param);

        return $data;
    }

    /**
     * OVO中心列表
     *
     * @param Request $request
     * @return string
     */
    public function postMakerlist(Request $request)
    {
        $obj = new MakerController();
        $res = $obj->postList($request);

        return $res;
    }

    /**
     * 活动申请
     *
     * @param $request
     * @return string
     */
    public function postApplyactivity(Request $request)
    {
        $data = $request->all();
        if (!isset($data['uid'])) {
            return AjaxCallbackMessage('操作异常', false);
        }
        $maker_ids = explode(',', $data['maker_ids']);
        $param = [
            'uid'         => $data['uid'],
            'subject'     => isset($data['subject']) ? $data['subject'] : '',
            'description' => isset($data['description']) ? $data['description'] : '',
            'begin_time'  => strtotime($data['begin_time']),
            'end_time'    => strtotime($data['end_time']),
            'type'        => isset($data['type']) ? $data['type'] : 0,
            'apply_name'  => $data['name'],
            'apply_phone' => $data['phone'],
        ];
        try {
            DB::beginTransaction();
            $obj = ApplyActiviy::create($param);
            foreach ($maker_ids as $item) {
                $params = [
                    'apply_activity_id' => $obj->id,
                    'maker_id'          => (int)$item,
                ];
                $id = DB::table('apply_activity_maker')->insertGetId($params);
                if (!$id) {
                    throw new Exception('操作异常');
                }
            }
            DB::commit();
        } catch (Exception $e) {
            DB::rollback();

            return AjaxCallbackMessage('操作异常', false);
        }
        //活动申请成功,生成系统消息
        $makers = DB::table('maker')
            ->whereIn('id', $maker_ids)
            ->select('subject')
            ->get();
        $makers = implode(',', array_flatten(objToArray($makers)));
        createMessage(
            $param['uid'],
            $title = '你的活动申请已成功提交',
            $content = '你的活动申请已经提交至无界商圈后台，之后会有运营人员与你取得联系。届时请保持联系方式的畅通。',
            $m_ext = '<div>
	   	            <p>活动名称：' . $param['subject'] . '</p>
	   	            <p>期望举办场地：' . $makers . '</p>
	   	            <p>活动描述：' . $param['description'] . '</p>
	   	            <p>意向活动时间：' . date('Y-m-d H:i', $param['begin_time']) . '~' . date('Y-m-d H:i', $param['end_time']) . '</p>
	   	            <p>活动类型：' . ($param['type'] ? '跨域活动' : '本地活动') . '</p>
	   	            <p>手机号：' . $request->input('phone') . '</p>
	   	            <p>姓名：' . $request->input('name') . '</p>
                    </div>',
            $end = '',
            $type = 1
        );
        //首次申请活动增加40积分
        $this->firstAddScore($param['uid'], $obj->id);
        //活动申请提交,发送短信
//        $register_content = trans('sms.activity_apply_submit');
        $register_content['name'] = 'activity_apply_submit';
        $register_content['tag'] = '';
        @sendSMSbyJob($data['phone'], $register_content, 'activity_apply_submit', 3, 60);

        return AjaxCallbackMessage('操作成功', true);
    }

    /**
     * //首次申请活动成功,增加40积分
     *
     * @param $uid
     */
    private function firstAddScore($uid, $apply_activity_id)
    {
        $count = DB::table('apply_activity')
            ->where('uid', $uid)
            ->where('status', 0)
            ->count();
        if ($count == 1) {
//            ScoreLog::add($uid, 40, 'first_publish_opp_success', '首次发布商机成功', 1, 1, 'apply_activity', $apply_activity_id);
        }
    }

    /*
     * 活动签到
     *
     * @param Request $request
     */
    public function postSign(SignRequest $request , $version = NULL)
    {
        //初始化
        $versionService = $this->init(__METHOD__, $version);

        if($versionService){
            $response = $versionService->bootstrap($request->all());
            return AjaxCallbackMessage($response['message'], $response['status']);
        }

        $uid = $request->input('uid');
        if (!\App\Models\User\Entity::checkAuth($uid)) {
            return AjaxCallbackMessage('账号异常', false);
        }
        $activity_id = trim($request->input('activity_id'));
        $activity = Activity::where('id', $activity_id)->first();
        //找出这个活动相同标签的活动
        $tag_id = $activity->tag_id;
        $activitys = Activity::where('tag_id',$tag_id)->get()->toArray();
        $activity_ids = array_pluck($activitys,'id');//这个标签所有的关联活动id
        $maker_id = trim($request->input('maker_id'));

        $exist = Sign::getRow(['uid' => $uid, 'status' => 0, 'maker_id' => $maker_id, 'activity_id' => $activity_id]);

        if (count($exist) == 0) {
            return AjaxCallbackMessage('该会员没有报名或者已经签到了', false);
        }
        //积分赠送
        $count = Sign::getCount(array('uid' => $uid));

        Sign::updateBy(['uid' => $uid, 'status' => 0, 'maker_id' => $maker_id], ['status' => 1, 'updated_at' => time()]);
        Ticket::updateBy(['uid' => $uid,'activity_id' => $activity_ids, 'maker_id' => $maker_id] ,['is_check' => 1, 'updated_at' => time()]);


        return AjaxCallbackMessage('签到成功', true);
    }

    /**
     * 扫用户的票卷二维码
     */
    public function postPadsign(PadSignRequest $request)
    {
        $data = $request->input();

        $result = Sign::updateBy(['ticket_no' => $data['ticket_no'], 'status' => 0], ['status' => 1, 'updated_at' => time()]);
        Ticket::updateBy(['is_check' => 0, 'ticket_no' => $data['ticket_no']], ['is_check' => 1, 'updated_at' => time()]);

        $ticket = Ticket::with('activity')->where('ticket_no',$data['ticket_no'])->first();
        $tag_id = $ticket->activity->tag_id;
        $activitys = Activity::where('tag_id', $tag_id)->get()->toArray();
        $activity_ids = array_pluck($activitys, 'id');//这个标签所有的关联活动id


        //查询有没有对应的经纪人
        $agent_sign = Sign::whereIn('activity_id', $activity_ids)->where('uid', $ticket->uid)->where('agent_id', '>', 0)->first();
        if($agent_sign){
            //活动邀约 给经纪人奖励
            AgentCurrencyLog::addCurrency($agent_sign->agent_id, 50, 14, $agent_sign->id, 1);
        }


        if ($result) {
            return AjaxCallbackMessage('签到成功', true);
        } else {
            return AjaxCallbackMessage('未报名或已签到', false);
        }
    }

    /**
     * 自减或自增
     */
    public function postIncre(IncreRequest $request)
    {
        $data = $request->input();
        Activity::incre([$data['col'] => $data['type']], ['id' => $data['id']]);
        //伪浏览量
        $sham_view = Activity::where('id', $data['id'])->value('sham_view');
        $increment = randomViewUtil::getRandViewCount($sham_view);//增量
        Activity::where('id', $data['id'])->increment('sham_view',$increment);

        return AjaxCallbackMessage('操作成功', true);
    }

    public function postUserApply(Request $request)
    {
        $data = $request->input();
        if (!isset($data['uid'])) {
            return AjaxCallbackMessage('用户活动uid是必传参数', false);
        }

        $result = Activity::applyLists($data['uid'], 1);
        foreach ($result as $k => $v) {
            $v->url = 'api/activity/detail?id=' . $v->id;
            $v->list_img = getImage($v->list_img, 'activity');
            $v->description = strip_tags($v->description);
            $price = array_unique(explode('@', $v->price));
            if (count($price) == 1 && $price[0] == 0) {
                $v->price = -1;
            }
        }

        $page = isset($data['page']) ? $data['page'] : 0;
        $size = isset($data['page_size']) ? $data['page_size'] : 10;
        $paginator = array_slice($result, $page * $size, $size);

        return AjaxCallbackMessage($paginator, true);
    }

    /*
    * 作用:活动邀请信息
    * 参数:activity_id,uid
    *
    * 返回值:
    */
    public function postInvite(Request $request)
    {
        $activity_id = Activity::hasAvailableActivity($request->get('activity_id', ''));
        $user = User::hasAvailableUser($request->get('uid', ''));
        $maker_id = $request->get('maker_id', '');
        if ($activity_id == '') {
            return AjaxCallbackMessage('活动ID必须!', false);
        }

        if ($user == '') {
            return AjaxCallbackMessage('用户ID必须!', false);
        }

//        if ($maker_id == '') {
//            return AjaxCallbackMessage('maker_id必须!', false);
//        }
        $responseData = Activity::getInviteResponseData($activity_id, $user, $maker_id);

        return AjaxCallbackMessage($responseData, true);
    }

    /*
    * 作用:获取举办活动的OVO中心
    * 参数:activity_id 活动id
    * 
    * 返回值:
    */
    public function postMakers(Request $request)
    {
        $activity_id = $request->get('activity_id', '');
        $pageSize = $request->get('pageSize', 15);
        if ($activity_id == '') {
            return AjaxCallbackMessage('活动ID非法', false);
        }
        $responseData = Maker::getMakers($activity_id, $pageSize);
        return AjaxCallbackMessage($responseData, true);
    }

    /*
    * 作用:领取分享票    TODO：领取分享票弃用  --数据中心 2017.12.13
    * 参数:activity_id 活动ID，maker_id OVO中心ID
    *
    * 返回值:领取成功true 失败false
    */
    public function postReceive(Request $request)
    {
        $activity_id = $request->get('activity_id', '');
        $uid = $request->get('uid', '');
        $maker_id = $request->get('maker_id', '');
//        $name = $request->get('name');
//        $tel = $request->get('tel');
//        $company = $request->get('company');
//        $job = $request->get('job');
        if ($activity_id < 1 || $uid < 1 || $maker_id < 1/* || empty($name) || empty($tel) || empty($company) || empty($job) */) {
            return AjaxCallbackMessage('参数异常', false);
        }
        $user = User::where('uid', $uid)->first();
        if(!$user){
            return AjaxCallbackMessage('用户信息不存在', false);
        }
        $activity = Activity::where('id', $activity_id)->where('status', 1)->first();
        if(!$activity){
            return AjaxCallbackMessage('活动不存在', false);
        }
        $obj = Ticket::createShareTicket($uid, $activity_id, $maker_id, 1);
        if (!$obj instanceof Ticket) {
            return AjaxCallbackMessage('您已经领取过了，请勿重复领取', false);
        }
        //发送票券领取成功消息
        //活动现场门票领取成功。活动：活动名称 将于 活动开始时间 举办。届时请准时赴会，感谢你的信赖和合作。如有疑问，请致电服务热线 400-011-0061
//        $content['name'] = '活动现场门票领取成功。活动：' . $activity->subject . ' 将于 ' . date("Y-m-d H:i", $activity->begin_time) . '举办。届时请准时赴会，感谢你的信赖和合作。如有疑问，请致电服务热线 400-011-0061';
        $content['name'] = 'receiveTicket';
        $type = 'receiveTicket';
        $content['tag'] = ['name' => $activity->subject,'time'=>date("Y-m-d H:i", $activity->begin_time)];
        $sender = new \App\Http\Controllers\Api\SmsController;
        $sender->sendSMS($user->username, $content, $type);
        $company = $job = '';
        $name = $user->realname;
        $tel = $user->username;
        //生成签到数据
        ActivitySign::apply($uid, $maker_id, $activity_id, $company, $job, $obj->ticket_no, 0, $name, $tel);
        return AjaxCallbackMessage('领取成功', true);
    }

    /*
    * 作用:分享记录入库
    * 参数:activity_id 活动id uid 用户id 
    * 
    * 返回值:
    */
    public function postRecordshare(Request $request)
    {
        $activity_id = $request->get('activity_id', '');
        $uid = $request->get('uid', '');

        if ($activity_id == '') {
            return AjaxCallbackMessage('活动ID必填', false);
        }

        if ($uid == '') {
            return AjaxCallbackMessage('用户uid必填', false);
        }

        $content = 'activity';
        $code = md5($uid . $activity_id . $content);
        if (Share::where('code', $code)->count() == 1) {
            return AjaxCallbackMessage('该用户已经已经分享过该活动了', false);
        }
        $obj = Share::create(
            [
                'content_id' => $activity_id,
                'uid'        => $uid,
                'code'       => $code,
                'content'    => $content,
            ]
        );

//        //分享次数累加到活动记录中
//        $count = Share::where('content_id',$activity_id)->where('content','activity')->count();
//        if($count){
//            Activity::where('id',$activity_id)->updated(['share_num'=>$count]);
//        }

        if (is_object($obj)) {
            return AjaxCallbackMessage(['code' => $code], true);
        }

        return AjaxCallbackMessage('分享失败', false);
    }

    /*
    * 作用:获取分享信息
    * 参数:activity_id 活动ID
    * 
    * 返回值:
    */
    public function postSharecontent(Request $request)
    {
        $activity_id = $request->get('activity_id', '');
        $uid = $request->get('uid', '');
        if ($activity_id == '') {
            return AjaxCallbackMessage('活动ID必填', false);
        }
        if ($uid == '') {
            return AjaxCallbackMessage('用户ID必填', false);
        }
        $user = User::where('uid', $uid)->first();
        $data = Activity::where('id', $activity_id)
            ->select('share_image', 'wx_title', 'wx_friend_summary', 'wx_summary', 'wb_summary', 'invite_num')->first();
//        $data->share_image = getImage($data->share_image, 'activity');
        $data->share_image = getImage($data->share_image?:'images/share_image.png', '', '');
        $data->code = md5($uid . $activity_id . 'activity');
        $data->name = $user->nickname;
        $data->url = createUrl('invite/detail', array('code' => $data->code));

        return AjaxCallbackMessage($data, true);
    }

    /*
    * 作用:获取分享人名
    * 参数:code 分享唯一标识码
    *
    * 返回值:
    */
    public function postSharename(Request $request)
    {
        $code = $request->get('code', '');
        //
        $share = Share::where('code', $code)->with('user')->first();

        return AjaxCallbackMessage(['name' => $share->user->nickname], true);
    }

    /*
    * 作用:活动列表
    * 参数:一次返回abc三类活动
    *
    * 返回值:
    */
    public function postListthree(Request $request ,$version = null)
    {
        //step1 参数验证
        $params = $this->validateParamsOfListthree($request);
        $fetch_end = $request->get('fetch_end', 0);

        if (!empty($params['errors'])) {
            return AjaxCallbackMessage($params['errors'], false);
        }

        //step2 获取A类活动
        $activity_a = [];
        if ($params['maker_id'] != 0) {
            $activity_a = Activity::getActivityOfYourMaker($params['maker_id'], [], 1, $params['vip_id'] ,$version);
        }

        //step3获取B类活动
        $exclusion = [];
        //获取OVO中心的活动ID
        foreach($activity_a as $activity){
            array_push($exclusion,$activity->id);
        }

        $activity_b = [];
        if ($params['position_id'] != 0) {
            $activity_b = Activity::getActivityOfYourCity($params['position_id'], $exclusion, 1, $params['vip_id'] ,$version);
//            $maker_ids = DB::table('maker')->where('zone_id',$params['position_id'])->lists('id');
//            $activity_ids = DB::table('activity_maker')->whereIn('maker_id',$maker_ids)->lists('activity_id');
//            $exclusion = array_merge($activity_ids,$exclusion);
            foreach($activity_b as $activity){
                array_push($exclusion,$activity->id);
            }
        }

        $activity_c = Activity::getActivityOfAll($exclusion, $params['pageSize'], 1, $params['vip_id'] , $version);
        if($fetch_end){
            $activity_d = Activity::getActivityOfAll($exclusion, $params['pageSize'], 2, $params['vip_id'] , $version);
            $activity_c = array_merge($activity_c, $activity_d);
        }


        $activity_c =array_slice($activity_c, ($params['page']-1) * $params['pageSize'], $params['pageSize']);


        return AjaxCallbackMessage(compact('activity_a', 'activity_b', 'activity_c'), true);
}

    /*
    * 作用:参数校验
    * 参数:
    * 
    * 返回值:
    */
    private function validateParamsOfListthree(Request $request)
    {
        $errors = '';
        $position_id = $request->get('position_id', 0);
//        if ($position_id == '') {
//            $errors .= '城市id必传';
//        }
        $maker_id = $request->get('maker_id', 0);
        $vip_id = $request->get('vip_id', 0);
//        if ($maker_id == '') {
//            $errors .= 'ovo中心id必传';
//        }
        $pageSize = $request->get('pageSize', 15);
        $page = $request->get('page', 1);

        return [
            'position_id' => $position_id,
            'maker_id'    => $maker_id,
            'vip_id'      => $vip_id,
            'pageSize'    => $pageSize,
            'page'          => $page,
            'errors'      => $errors
        ];
    }

    /*
     * 活动临时签到
     */
    public function postTempsign(TempSignRequest $request , $version = NULL)
    {
        //初始化
        $versionService = $this->init(__METHOD__, $version);

        if($versionService){

            $response = $versionService->bootstrap($request->all());
            return AjaxCallbackMessage($response['message'],$response['status']);
        }

        return AjaxCallbackMessage('api接口不再维护',FALSE);
    }

    /*
     * 活动报名列表
     */
    public function postSignuserlist(Request $request , $version = NULL)
    {
        //初始化
        $versionService = $this->init(__METHOD__, $version);

        if($versionService){

            $response = $versionService->bootstrap($request->all());
            return AjaxCallbackMessage($response['message'],$response['status']);
        }

        return AjaxCallbackMessage('api接口不再维护',FALSE);
    }

    /*
     * 活动评论点赞
     */
    public function postZan(Request $request , $version = NULL)
    {
        //初始化
        $versionService = $this->init(__METHOD__, $version);

        if($versionService){

            $response = $versionService->bootstrap($request->all());
            return AjaxCallbackMessage($response['message'],$response['status']);
        }

        return AjaxCallbackMessage('api接口不再维护',FALSE);
    }



    /*
     * 判断某手机号是否已经报名过
    */
    public function postTelApplied(TelAppliedRequest $request , $version = NULL)
    {
        $versionService = $this->init(__METHOD__, $version);

        if($versionService){
            $response = $versionService->bootstrap($request->all());
            return AjaxCallbackMessage($response['data'],$response['status']);
        }

        return AjaxCallbackMessage('api接口不再维护',false);
    }



    /*
     * 获取活动的报名信息
    */
    public function postEnrollInfos(Request $request , $version = NULL)
    {
        $versionService = $this->init(__METHOD__, $version);

        if($versionService){
            $response = $versionService->bootstrap($request->all());
            return AjaxCallbackMessage($response['message'],$response['status']);
        }

        return AjaxCallbackMessage('api接口不再维护',false);
    }



    /*
    * 活动报名并支付
    */
    public function postApplyAndPay(ApplyAndPayRequest $request , $version = null)
    {
        $versionService = $this->init(__METHOD__, $version);

        if($versionService){
            $response = $versionService->bootstrap($request->all());
            return AjaxCallbackMessage($response['data'],$response['status']);
        }

        return AjaxCallbackMessage('api接口不再维护',false);
    }




}