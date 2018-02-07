<?php

namespace App\Services\Version\Activity;


use App\Exceptions\ExecuteException;
use App\Models\Activity\Ticket;
use App\Models\Agent\AgentCurrencyLog;
use App\Models\Agent\AgentCustomer;
use App\Models\Distribution\Action;
use App\Models\Live\Entity as Live;
use Illuminate\Support\Facades\DB;
use App\Models\User\Entity as User;
use App\Models\Brand\Entity as Brand;
use App\Models\Activity\Ticket as ActivityTicket;
use App\Models\Activity\Entity as Activity;
use App\Models\Order\Entity as Order;
use App\Models\Activity\Sign as ActivitySign;
use App\Models\User\Ticket as UserTicket;
use App\Models\Message;
use App\Models\Live\Subscribe;
use App\Models\ScoreLog;
use App\Models\Distribution\Entity as Distribution;
use App\Models\Activity\Sign;
use App\Models\Agent\Agent;
use App\Events\ChristmasWinPrize;


class _v020700 extends _v020600
{

    /**
     * 活动详情
     */
    public function postDetail($param = [], $tag = false)
    {
        $tags = !$tag ?  true : $tag;

        $data = parent::postDetail($param, $tags)['message'];
        $distribution_id = $data['distribution_id'];
        $activity_id = $data['id'];
        $uid = $param['uid'];//用户id
        //品牌是否已收藏
        foreach ($data['brand'] as $k => $v) {
            $brand_id = $v['id'];
            $is_collect = Brand\V020700::getCollect($uid, $brand_id);
            $data['brand'][$k]['is_collect'] = $is_collect;
//            dd($data['brand'][$k]['brand_summary']);
            //品牌描述
            if (empty($data['brand'][$k]['brand_summary'])) {
                $data['brand'][$k]['brand_summary'] = mb_substr(trim(preg_replace('#<\w+(\s+.*?)?>|</\w+>|&nbsp;#', '', $data['brand'][$k]['details'])), 0, 50);
            }

        }
        //返回分享规则
        $data['distribution'] = Action\V020700::getDescribe($distribution_id,'activity',$activity_id);
        $data['is_distribution'] = Distribution::IsDeadline($distribution_id,$data['distribution_deadline']);//分销是否失效

        //门票
        $ticket = ActivityTicket::select('surplus', 'num')
            ->where('activity_id', $activity_id)
            ->where('type', 1)
            ->get();

        //活动门票的种类数
        $data['site_ticket_count'] = count($ticket);

        //默认席位
        $data['surplus'] = 0;//剩余门票席位
        $data['num'] = 0;//总门票数

        foreach ($ticket as $v) {
            $data['surplus'] += $v->surplus;//剩余门票席位
            $data['num'] += $v->num;//总门票数
        }

        //直播
        $live = Live::select('id', 'begin_time', 'end_time')
            ->where('activity_id', $activity_id)
            ->where('status', 0)
            ->first();
        if ($live->id != 0) {
            $data['islive'] = Live\V020700::liveStatus($live->begin_time, $live->end_time);
            $data['live_id'] = $live->id;//关联直播id
        } else {
            $data['live_id'] = 0;//未关联直播id
            $data['islive'] = -1;//无直播
        }

        $qrcode = Activity::where('id', $activity_id)->value('qrcode');
        //如果没找到则创建
        if (!$qrcode) {
            //活动二维码
            $value = url('webapp/activity/detail/_v020700?id=' . $activity_id . '&is_share=1');
            $file_name = unique_id() . '.png';
            $qrcode = img_create($value, $file_name);
            //将活动二维码保存
            Activity::where('id', $activity_id)->update(['qrcode' => $qrcode]);
        }
        //价格最低的现场票的积分价格
        $data['min_ticket_score'] = Ticket::getMinScore($activity_id);

        $data['qrcode'] = getImage($qrcode,'','');
        return ['message' => $data, 'status' => true];
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

        if(isset($data['share_mark']) && $data['share_mark']){
            $share_remark = \Crypt::decrypt($data['share_mark']);
            $md5 = substr($share_remark, 0,32);
            if($md5!=md5($_SERVER['HTTP_HOST'])){
                return ['message'=>'分享码有误', 'status'=>false];
            }
            $share_remark = explode('&', substr($share_remark, 44));
            $data['source_uid'] = $share_remark[2];
        }else{
            $data['source_uid'] = 0;
        }

        //判断其是否已经报名
        $sign = ActivitySign::whereIn('status', [0, 1])->where(['non_reversible' => $non_reversible, 'activity_id' => $data['activity_id']])->first();
        if (is_object($sign)) {
            return ['data' => '该手机号已报名', 'status' => false];
        }
        $user = User::getRow(['uid' => $data['uid']]);
        $is_register=1;
        if($data['path'] =='html5'){
            $htmlApply= $this->htmlApply($data);

            //数据中心处理错误了直接返回
            if ($htmlApply['status'] == false){
                return ['data' => $htmlApply['message'], 'status' => false];
            }

            $is_register = $htmlApply['is_register'];
            $user = $htmlApply['user'];
            if(!$htmlApply['user']){
                return ['data' => '分享页面报名出现错误', 'status' => false];
            }
        }

        $ticket = ActivityTicket::getRow(['id' => $data['ticket_id']]);


        if($ticket->type ==1 && !$data['maker_id']) { //现场票一定要传空间
            return ['data' => '现场票一定要传maker_id', 'status' => false];
        }
        else if($ticket->type == 2){
            $data['maker_id'] = 0;
        }

        $activity = Activity::getRow(['id' => $data['activity_id']]);

        empty($data['product']) && $data['product'] =$activity->subject;
        empty($data['body']) && $data['body'] =$activity->subject;
        empty($data['name']) && $data['name'] =$user->nickname;
        empty($data['company']) && $data['company'] ='';
        empty($data['job']) && $data['job'] ='';
        empty($data['is_invite']) && $data['is_invite'] = 0;

        if (empty($data['tel'])){
            $non_reversible =$user->non_reversible;
            $username =$user->username;
        }


        $num = DB::table('user_ticket')->where('ticket_id', $data['ticket_id'])->whereIn('status', [0, 1])->count();
        if ($num >= $ticket->num) {
            return ['data' => '票已售完', 'status' => false];
        }

        if($ticket->type==2 && $data['maker_id']==0){  //获取该场活动主办场地id
            $data['maker_id'] =$this->getOidByAid($ticket->activity_id);
        }

        $exist = UserTicket::getRow(['uid' => $data['uid'], 'ticket_id' => $data['ticket_id'], 'maker_id' => $data['maker_id'], 'status' => 0]);

        if (is_object($exist)) {
            return ['data' => '关于该活动已经存在了一个未支付的门票，请先支付，再重新下单', 'status' => false];
        }
        //判断活动是否已结束
        if($activity->end_time<time()){
            return ['data' => '该场活动已经结束', 'status' => false];
        }
        $rate = config('system.score_rate');
        //开始事务
        \DB::beginTransaction();
        try{
            //下单
            $order = Order::place(
                $user->uid,
                $data['ticket_id'],
                $data['score_num']/$rate,
                $data['product'],
                $data['body'],
                'score',
                $data['score_num'],
                $data['score_num'] / $rate,
                0,
                1,
                $ticket->type == 1?'activity':'live'
            );
            //出票
            $u_ticket = UserTicket::produce($user->uid, $order->id, $data['ticket_id'], $data['maker_id'], $ticket->type, 0, 1, $data['score_num'],$data['is_invite']);
            ActivityTicket::incre(['id' => $data['ticket_id']], ['surplus' => -1]);
            $result = ActivitySign::apply($user->uid, $data['maker_id'], $data['activity_id'], $data['company'], $data['job'], $u_ticket->ticket_no, 0, $data['name'], $username, $non_reversible, $data['source_uid'],$data['is_invite']);

            //发短信 和站内信
            $a_ticket = ActivityTicket::getRow(['id' => $data['ticket_id']]);
            Activity::sendMessage($user->uid, $data['activity_id'], $u_ticket->id);
            if ($a_ticket->type == 1) {  //如果是现场票
                $buy_type = 'site_ticket_buy';
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
            }

            //如果是直播还要订阅
            if ($a_ticket->type == 2) {
                $buy_type = 'live_ticket_buy';
                //订阅该直播
                $live = Live::where('activity_id', $data['activity_id'])->first();
                Subscribe::subscribe(['uid'=>$user->uid,'live_id'=>$live->id,'type'=>1]);
            }

            if ($a_ticket->type == 1) {  //如果是现场票
                //报名加积分
                Activity::addScore($user->uid, $result->id);
            }

            //如果是通过活动邀请函渠道报名，则免费,不使用积分
            if($data['is_invite'] == 0){
                //使用积分
                ScoreLog::add($data['uid'], $data['score_num'], $buy_type, '活动票或直播票购买使用积分', -1, false);
            }
            \DB::commit();
        }catch (\Exception $e){
            \DB::rollBack();
            throw(new ExecuteException('报名失败，出现异常'.$e->getMessage()));
        }

        //发短信
        if ($a_ticket->type == 1) {  //如果是现场票
            //因短信平台原因可能取消动态url短信
            $url = config('app.app_url') . 'activity/detail/'.config('app.version').'?pagetag=02-2&id='.$activity->id.'&is_share=1';
            @SendTemplateSMS('activitySiteSign',$user->non_reversible,'activitySiteSign',[
                'name' => $activity->subject,
                'time' => date('m', $activity->begin_time) . '月' . date('d', $activity->begin_time) . '日 ' . date('H', $activity->begin_time) . '点' . date('i', $activity->begin_time) . '分',
                'url'=>shortUrl($url)
            ],$user->nation_code);
        }


        if ($a_ticket->type == 2) {
            $live_url = config('app.app_url') . 'live/detail/'.config('app.version').'?pagetag='.config('app.live_detail').'&id='.$live->id.'&is_share=1';
            @SendTemplateSMS('activityLiveSign',$user->non_reversible,'activityLiveSign',[
                'name' => $activity->subject,
                'time' => date('m', $activity->begin_time) . '月' . date('d', $activity->begin_time) . '日 ' . date('H', $activity->begin_time) . '点' . date('i', $activity->begin_time) . '分',
                'url'=>shortUrl($live_url)
            ],$user->nation_code);
        }    //因平台原因可能取消动态url短信


        if (is_object($order) && $data['path'] =='html5') {
            return ['data' => ['order_no'=>$order['order_no'], 'is_register'=>$is_register, 'activity_sign_id'=>$result->id], 'status' => true];
        } elseif(is_object($order)) {
            return ['data' => $order['order_no'], 'status' => true];
        }else{
            return ['data' => '报名失败', 'status' => false];
        }

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
     * 分享页面报名的处理  数据中心版
     * @User yaokai
     * @param $data
     * @return array
     */
    protected function htmlApply($data)
    {

        //伪号码
        $username = pseudoTel($data['tel']);

        //用户加密后的手机号
        $non_reversible = encryptTel($data['tel']);

        $user = User::getRow(['non_reversible' => $non_reversible]);
        if(is_object($user)){
            return ['user'=>$user,'is_register'=>1,'status' => true];
        }else{
            //数据中心处理
            $url = config('system.data_center.hosts') . config('system.data_center.encrypt');
            $datas = [
                'nation_code' => $data['nation_code']?:'86',
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
            return ['user'=>$user,'is_register'=>0,'status' => true];
        }
    }




    /**
     * 获取活动的报名信息
     */
    public function postEnrollInfos($param)
    {
        //活动信息
        $activity = \DB::table('activity')->where('id', $param['id'])->select('subject', 'begin_time', 'keywords', 'list_img', 'detail_img')->first();

        //场地信息
        $makers = \DB::table('activity_maker')
            ->leftJoin('maker', 'activity_maker.maker_id', '=', 'maker.id')
            ->leftJoin('zone', 'maker.zone_id', '=', 'zone.id')
            ->where('activity_maker.activity_id', $param['id'])
            ->where('activity_maker.status', 1)
            ->select('zone.name', 'maker.address', 'maker.tel', 'maker.subject','maker.id')
            ->get();

        $citys = [];
        foreach($makers as $k=>$v){
            $v->name = str_replace('市','',$v->name);
            $citys[] = $v->name;
        }
        $activity->host_cities = $citys;
        $activity->begin_time_format = date('Y年m月d日 H:i', $activity->begin_time);
        $activity->keywords ?$activity->keywords = explode(' ', $activity->keywords):$activity->keywords = [];
        $activity->list_img = getImage($activity->list_img,'activity', '', 0);
        $activity->detail_img = getImage($activity->detail_img,'activity', '', 0);


        //门票信息
        $ticket = \DB::table('activity_ticket')
            ->where('activity_id', $param['id'])
            ->where('status',1)
            ->select('is_recommend', 'name', 'original','score_price',
                'surplus as left', 'intro', 'type', 'id', 'remark')
            ->addSelect(\DB::raw("'$activity->subject' subject"))
            ->orderBy('type', 'desc')
            ->get()
        ;

        foreach($ticket as $k=>$v){
            if($v->left==0){
                $v->is_recommend = 0;
            }
            $v->name ?:$v->name='直播票';
        }

        return ['status' => true, 'message' => ['activity'=>$activity, 'makers'=>$makers, 'ticket'=>$ticket]];
    }

    /**
     * 活动签到   --数据中心版
     * @User yaokai
     * @param array $param
     * @return array
     */
    public function postSign($param = [])
    {
        $uid = $param['uid'];

        if (!\App\Models\User\Entity::checkAuth($uid)) {
            return ['status' => false, 'message' => '账号异常'];
        }

        $activity_id = trim($param['activity_id']);
        $maker_id = trim($param['maker_id']);

        //活动类型
        $activity = Activity::where('id', $activity_id)->first();
        //找出这个活动相同标签的活动
        $tag_id = $activity->tag_id;
        $activitys = Activity::where('tag_id', $tag_id)->get()->toArray();
        $activity_ids = array_pluck($activitys, 'id');//这个标签所有的关联活动id

        $exist = Sign::where(['uid' => $uid, 'maker_id' => $maker_id])
            ->whereIn('activity_id', $activity_ids)
            ->whereIn('status', [0, 1])
            ->get();
        $status = '';
        if ($exist) {
            foreach ($exist as $v) {
                $status = $v->status;
            }
        }
        switch ($activity->sign) {
            //全开放,只统计人数
            case 2 :
                $user = User::where('uid', $uid)->first();
                if ($user) {
                    $name = $user->nickname ?: $user->username;
                    $tel = $user->username;
                    $non_reversible = $user->non_reversible;
                } else {
                    return ['status' => false, 'message' => '用户未找到'];
                }
                //报名状态更新
                $sign = $this->updateStatus($exist, $uid, $activity_id, $maker_id, $name, $tel, $non_reversible);
                if ($sign) {
                    $return = ['status' => false, 'message' => '该会员已经签到'];
                } else {
                    $return = ['status' => true, 'message' => $activity->subject . '@' . date('H:i', $activity->begin_time)];
                }

                break;

            //半开放,需要手机号码验证
            case 1:
                //是否已经报名
                $sign = Sign::where('uid', $uid)
                    ->whereIn('activity_id', $activity_ids)
                    ->where('maker_id', $maker_id)
                    ->where('status', 0)
                    ->get()->toArray();
                if (!empty($sign)) {
                    return ['status' => true, 'message' => $this->getSignInfo($uid, $maker_id, $activity_id, $activity, $activity_ids)];
                }

                if ($status == 1) {
                    $return = ['status' => false, 'message' => '该会员已经签到'];
                } else {
                    $return = ['status' => true, 'message' => 'half_open'];
                }
                break;

            //标准
            case 0:
                if (count($exist) == 0) {
                    if($activity->end_time > time()){
                        return ['status' => false, 'message' => ['message'=>'没有在该会场报名活动','type'=>'0']];//未报名
                    };
                    return ['status' => false, 'message' => ['message'=>'无法完成活动签到，请联系现场工作人员。','type'=>'2']];//已结束
                }

                if ($status == 1) {//已签到
                    return ['status' => false, 'message' => ['message'=>$this->getSignInfo($uid, $maker_id, $activity_id, $activity, $activity_ids),'type'=>'1']];
                }

                $return = ['status' => true, 'message' => $this->getSignInfo($uid, $maker_id, $activity_id, $activity, $activity_ids)];

                //在圣诞英雄榜中添加数据
                $signInfo = $exist->first();
                if(is_object($signInfo) && $signInfo['is_invite'] == 1){
                    $user = User::where('uid', $uid)->first();
                    $agentInfo = Agent::where('status',1)->where('id',$signInfo['agent_id'])->first();
                    event(new ChristmasWinPrize(['type'=>2 , 'agent'=>$agentInfo ,'activty_name'=>$activity['subject'] , 'username'=>$user['username']]));
                }

                break;

            default:
                $return = ['status' => false, 'message' => '活动签到限制异常'];
                break;
        }


        return $return;
    }


}