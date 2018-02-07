<?php
/**活动模型
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/5/5
 * Time: 11:10
 */

namespace App\Models\Order;

use App\Models\Agent\AgentCurrencyLog;
use App\Models\Agent\AgentCustomer;
use App\Models\Agent\AgentCustomerLog;
use App\Models\Agent\CommonEvents\Events;
use App\Models\Agent\Score\AgentScoreLog;
use App\Models\Agent\TraitBaseInfo\RongCloud;
use App\Models\Brand\Intent;
use App\Models\RedPacket\RedPacketPerson;
use App\Models\ScoreLog;
use App\Models\User\Fund;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use \DB, Closure;
use App\Models\Orders\Items;
use App\Models\Orders\Entity as Orders;
use App\Models\User\Entity as User;
use App\Models\Comment\Entity as Comment;
use App\Models\Vip\Term as VipTerm;
use App\Models\Vip\Entity as VipEntity;
use App\Models\Vip\User as UserVip;
use App\Models\Message;
use App\Models\Brand\Consult;
use App\Models\Brand\Entity as Brand;
use App\Models\Brand\Goods;
use App\Models\Brand\BrandGoods;
use App\Models\Score\Goods\V020700 as GoodsV020700;
use App\Models\Activity\Ticket;
use App\Models\Agent\Invitation;
use App\Models\Zone\Entity as Zone;
use App\Models\Agent\Agent;
use \App\Models\Contract\Contract;
use App\Events\ChristmasWinPrize;
use App\Models\Agent\Entity\_v010200 as Agentv010200;
class Entity extends Model
{
    use RongCloud;

    const SUCCESS_TYPE = 1;      //成功的数字标记
    const URLS         = 'js/agent/generic/web/viewer.html?file=';
    protected $dateFormat = 'U';

    protected $table = 'order';
    public static $instance = null;
    public function instance()
    {
        if (is_null(self::$instance)) {
            self::$instance = new self;
        }
        return self::$instance;
    }

    //黑名单
    protected $guarded = [];

    static function getRow($where)
    {
        return self::where($where)->first();
    }

    static function getRows($where)
    {
        return self::where($where)->get();
    }

    public function ticket()
    {
        return $this->hasOne('App\Models\Activity\Ticket', 'id', 'ticket_id');
    }

    //关联活动门票
    public function belongsToActivityTicket()
    {
        return $this->belongsTo(Ticket::class, 'ticket_id', 'id');
    }

    /**
     * 下订单
     */
    static function place($uid, $ticket_id, $cost = 0, $product, $body, $pay_way = '', $score_num, $score_money, $online_money, $status = 0, $type = 'activity')
    {
        $deadline = time() + 60 * 30;
        $order_no = chr(rand(97, 122)) . chr(rand(97, 122)) . time() . chr(rand(97, 122)) . chr(rand(97, 122));
        $place = self::create(
            [
                'uid' => $uid,
                'ticket_id' => $ticket_id,
                'deadline' => $deadline,
                'order_no' => $order_no,
                'product' => $product,
                'body' => $body,
                'pay_way' => $pay_way,
                'status' => $status,
                'cost' => $cost,
                'score_num' => $score_num,
                'score_money' => $score_money,
                'online_money' => $online_money,
                'type' => $type,
            ]
        );

        return $place;
    }

    /**
     * 检验积分是否合法
     */
    static function checkScore($score_num, $uid, $rate)
    {
        //先判断是不是正整数
        if (!isInt($score_num)) {
            return -1;
        }

        $user = DB::table('user')->where('uid', $uid)->first();
        if ($score_num > $user->score) {
            return -2;
        }

        //是不是汇率的百分之一的倍数

        $mod = fmod($score_num, ($rate / 100));
        if ($mod != 0) {
            return -3;
        }

        return 1;
    }

    static function updateOrderByField(Array $array, Array $field)
    {
        $result = DB::table('order')->where(array_keys($field)[0], array_values($field)[0])
            ->update($array);

        return $result;
    }

    /**
     * 支付成功后的操作
     */
    public static function afterPay($order_no, $status, $third_no = null, $buyer_id = null)
    {
        $old_order = Orders::where('order_no', $order_no)->first();

        if ($third_no && is_object($old_order)) {
            $old_order->third_no = $third_no;
            $old_order->save();
        }

        if ($buyer_id && is_object($old_order)) {
            $old_order->buyer_id = $buyer_id;
            $old_order->save();
        }

        //更新购买项状态  订单表的状态也一起更新了
        $update = Items::updateByNo($order_no, $status);
        if (!$update) {
            return false;
        }
        //获得购买项
        $items = Items::getByNo($order_no, $status);

        foreach ($items as $k => $v) {
            //如果是专版
            if ($v->type == 'vip') {
                self::vipAfterPay($v->uid, $v->product_id, $v->orders_id, $v->num, $v->id, $old_order->status);
            }

            //如果是视频打赏
            if ($v->type == 'video_reward') {
                self::reward($v->uid, $v->product_id, 'Video', "对视频打赏了<span class='f63'>{$v->price}</span>元", 'reward', $old_order->status);
            }

            //如果是直播打赏
            if ($v->type == 'live_reward') {
                self::reward($v->uid, $v->product_id, 'Live', "对直播打赏了<span class='f63'>{$v->price}</span>元", 'reward', $old_order->status);
            }

            //如果是品牌商品支付
            if ($v->type == 'brand') {
                self::brandAfterPay($v->uid, $v->product_id, 'Live', $v->id, 'brand', $v->zone_id, $old_order->status);
            }

            //如果是品牌商品加盟
            if ($v->type == 'brand_goods') {
                self::brandGoodsAfterPay($v->uid, $v->product_id, $v->id, $v->zone_id, $old_order->status, $v->mobile, $v->realname);
            }


            //如果是购买积分
            if ($v->type == 'score') {
                self::scoreAfterPay($v->uid, $v->product_id, $old_order->status, $old_order->id);
            }


            //如果是考察邀请函
            if ($v->type == 'inspect_invite') {
                self::inviteAfterPay($v->product_id, $order_no, $old_order->status);
            }


            if ($v->type == 'contract') {
                self::contractAfterPay($v->uid, $v->product_id, $order_no, $old_order->status);

            }


            //todo  其他情况
        }

        return true;
    }


    /**
     * 合同被支付后的操作  --数据中心版
     *
     * @param $uid
     * @param $post_id
     * @param $status
     * @param $orders_id
     * @return bool
     * @author tangjb
     */
    public static function contractAfterPay($uid, $post_id, $order_no, $status)
    {
        //防止多次通知
        if ($status != 'npay') {
            return false;
        }

        $user = User::where('uid', $uid)->first();

        $contract = Contract::with(
            ['user' => function ($query) {
                $query->select('uid', 'avatar', 'realname', 'nickname');
            }, 'brand' => function ($query) {
                $query->select('id', 'name');
            }, 'agent' => function ($query) {
                $query->select('id', 'realname');
            }]
        )->where('id', $post_id)->first();


        $agent = Agent::find($contract->agent_id);
        //改变合同状态
        $contract->status = 1;
        //添加合同号
//        $contract->contract_no = Contract::getInstance()->produceNo($contract->id);
        $contract->confirm_time = time();
        $contract->save();

        //红包
        RedPacketPerson::where('id', $contract->fund_id)->update(['status' => 1]);

        //把该合同对应的邀请函状态改变
        Invitation::where('id', $contract->invitation_id)->update(['status' => 2]);


        //新增日志
        $agent_customer_id = AgentCustomer::where('uid', $contract->uid)->where('agent_id', $contract->agent_id)->value('id');

        $datas = [
            'agent_customer_id' => $agent_customer_id ? $agent_customer_id : 0,
            'agent_id' => $contract->agent_id,
            'action' => 11,
            'brand_id' => $contract->brand_id,
            'created_at' => time(),
            'uid' => $contract->uid,
            'post_id' => $contract->id,
        ];


        AgentCustomerLog::create($datas);

        //给积分
        Agentv010200::add($contract->agent_id, AgentScoreLog::$TYPES_SCORE[10], 10, '完成加盟首付', $post_id, 1);


        $order = Orders::with('hasOneOrdersItems')->where('order_no', $order_no)->first();
        $pay_at = date('Y年m月d日 H:i', $order->pay_at);

        //融云推送消息
        $agentContent = [
            'agent_name'  => $agent->is_public_realname ?  $agent->realname : $agent->nickname,
            'contract_no' => $contract->contract_no,
            'brand_name'  => $contract->brand->name,
            'money'       => number_format($contract->amount),
            'first_money' => number_format($contract->pre_pay),
            'time'        => $pay_at,
            'urls'        => shortUrl('https://'.env('APP_HOST') .'/'.self::URLS . $contract['address'])
        ];
        $investorContent = [
            'agent_name'    => $agent->is_public_realname ?  $agent->realname : $agent->nickname,
            'contract_no'   => $contract->contract_no,
            'brand_name'    => $contract->brand->name,
            'money'         => number_format($contract->amount),
            'first_money'   => number_format($contract->pre_pay),
            'time'          => $pay_at,
            'urls'          => shortUrl('https://'.env('APP_HOST'). '/' .'/'.self::URLS . $contract['address'])
        ];

        $gather_data = [
            'content' => [
                'investorContent' => trans('tui.contract_rong_content_notice', $investorContent),
                'agentContent'    => trans('tui.contract_rong_content_notice', $agentContent)
            ],
            'investorContent' => trans('tui.contract_rong_content_notice', $investorContent),
            'agentContent'    => trans('tui.contract_rong_content_notice', $agentContent)
        ];

        $user_result_status = SendCloudMessage($user->uid, 'agent' . $agent->id, $gather_data, 'TY:DiffMsg', $gather_data, 'custom');  //发送融云消息


        //agent端推送消息
        $content = [
            'type' => 'contract',
            'style' => 'json',
            'value' => ['content' => '投资人' . $contract['user']['nickname'] . '接受了您的付款协议'],
        ];


        //发送透传消息
        $tui_result = send_transmission(json_encode($content), $agent, null, 1);

        $user->realname ? $name = $user->realname: $name = $user->nickname;
        $content = [
            'type' => 'new_remind',
            'style' => 'json',
            'value' => [
                'title' => "{$name} 接受了 [{$contract->brand->name}] 付款协议",
                'sendTime' => time(),
            ],
        ];

        $tui_result = send_transmission(json_encode($content), $agent, null, 1);

        $name = $user->realname?$user->realname:$user->nickname;
        $brand = Brand::find($contract->brand_id);


        $url = 'https://'.env(APP_HOST).'/webapp/agent/contract/pactdetails?contract_id='.$contract->id.'&is_out=1';


        //给经纪人发送短信
        $res = SendTemplateSMS('contract_pre_pay',
            $agent->non_reversible, 'contract_pre_pay',
            [
                'name' => $name,   //客户名称
                'username' => $user->username,   //客户手机号
                'brand_title' => $brand->name,   //品牌标题
                'amount' => ($contract->amount/10000).'万',   //费用，以万为单位
                'pay_time' => date('Y-m-d'),   //支付时间    YYYY年MM月DD日
                'shorturl' => substr(shortUrl($url), 7),   //短链接
            ],
            $agent->nation_code);

        $url = 'https://'.env(APP_HOST).'/webapp/client/pactdetails/_v020800?contract_id='.$contract->id.'&uid='.$user->uid.'&is_out=1&';

        //给客户发送短信
        $res = SendTemplateSMS('contract_pre_pay_customer',
            $user->non_reversible, 'contract_pre_pay_customer',
            [
                'name' => $agent->realname,   //经纪人名称
                'zone' => Zone::getCityAndProvince($agent->zone_id),   //地区  浙江 杭州
                'username' => $agent->username,   //经纪人手机号
                'brand_title' =>$brand->name,   //品牌名称
                'slogan' => $brand->slogan,   //品牌口号
                'amount' => ($contract->amount/10000),   //费用，以万为单位
                'pay_time' => date('Y-m-d'),   //支付时间    YYYY年MM月DD日
                'shorturl' => substr(shortUrl($url), 7),   //短链接
            ],
            $user->nation_code);

        //增加：经纪人邀请的投资人加盟后，进行短信提示 zhaoyf 2017-12-13 14:45
        //self::_sendInfos(['id' => $contract->id], Contract::class);

        return $user_result_status  && $tui_result;
    }


    /**
     * 邀请函被支付后的动作  --数据中心版
     *
     * @param $post_id
     * @param $order_no
     * @param $status
     * @return bool
     * @author tangjb
     */
    public static function inviteAfterPay($post_id, $order_no, $status)
    {
        //防止多次通知
        if ($status != 'npay') {
            return false;
        }

        //经纪人姓名
        $invitation = Invitation::with('hasOneStore.hasOneBrand', 'belongsToAgent', 'hasOneStore.hasOneZone')
            ->where('id', $post_id)->first();

        $order = Orders::with('hasOneOrdersItems')->where('order_no', $order_no)->first();


        $invitation->status = 1;
        $invitation->pay_time = time();
        $invitation->save();

        $agent_customer_id = AgentCustomer::where('uid', $invitation->uid)->where('agent_id', $invitation->agent_id)->value('id');

        //添加跟进日志
        $logs = [
            'agent_customer_id' => $agent_customer_id,
            'action' => 8,
            'post_id' => $post_id,
            'remark' => '',
            'brand_id' => $invitation->hasOneStore->hasOneBrand->id,
            'agent_id' => $invitation->agent_id,
            'uid' => $invitation->uid,
            'created_at' => time(),
        ];

        AgentCustomerLog::create($logs);
        $data = [
            'brand_title' => $invitation->hasOneStore->hasOneBrand->name,
            'realname' => $invitation->belongsToAgent->realname,
            'store_name' => $invitation->hasOneStore->name,
            'address' => $invitation->hasOneStore->address,
            'zone' => Zone::getCityAndProvince($invitation->hasOneStore->zone_id),
            'amount' => numFormatWithComma(abandonZero($order->amount)),
            'pay_at' => date('Y年m月d日 H:i', $order->pay_at),
            'order_no' => $order->order_no,
            'pay_way' => Orders::$_PAYWAY[$order->pay_way],
            'inspect_time' => date('Y年m月d日 H:i', $invitation->inspect_time),
            'url' => env('APP_HOST') . '/webapp/agent/newsinvestask/detail?inspect_id=' . $post_id,
        ];
        $inspect_time = date('Y年m月d日', $invitation->inspect_time);
        $urls  = shortUrl('https://' . $data['url']);
        $urlss = shortUrl('https://' . env('APP_HOST') . '/webapp/investinvitation/detail/_v020800?inspect_id=' . $post_id);
        //$user = User::where('uid', $invitation->uid)->first();
        $agentContent = [
            'agent_name'  => $invitation->belongsToAgent->is_public_realname ?  $invitation->belongsToAgent->realname : $invitation->belongsToAgent->nickname,
            'brand_name'  => $data['brand_title'],
            'money'       => $data['amount'],
            'store_name'  => $data['store_name'],
            'place'       => $data['zone'],
            'time'        => $inspect_time,
            'url'         => $urls
        ];
        $investorContent = [
            'agent_name' => $invitation->belongsToAgent->is_public_realname ?  $invitation->belongsToAgent->realname : $invitation->belongsToAgent->nickname,
            'brand_name' => $data['brand_title'],
            'money'      => $data['amount'],
            'store_name' => $data['store_name'],
            'place'      => $data['zone'],
            'time'       => $inspect_time,
            'url'        => $urlss
        ];
        $gather_data = [
            'content' => [
                'investorContent' => trans('tui.inspect_rong_content_notice', $investorContent),
                'agentContent'    => trans('tui.inspect_rong_content_notice', $agentContent)
            ],
            'investorContent' => trans('tui.inspect_rong_content_notice', $investorContent),
            'agentContent'    => trans('tui.inspect_rong_content_notice', $agentContent)
        ];

        $result_status = SendCloudMessage($invitation->uid, 'agent' . $invitation->agent_id, $gather_data, 'TY:DiffMsg', $gather_data, 'custom');

        //发送成功更新邀请表status状态为 1
        if ($result_status['status']) {
            Invitation::where('agent_id', $invitation->agent_id)
                ->where('uid', $invitation->uid)
                ->where('id', $invitation->id)
                ->update(['status' => self::SUCCESS_TYPE]);
        }

        //获取当前投资人是否存在邀请经纪人，如果给对方发送消息
        $gain_result = AgentCustomer::instance()->gainCustomerAgentRelationDatas($invitation->uid);

        //发送融云消息
        if ($gain_result) {
//            Entity::gatherInfoSends([
//                $invitation->uid,
//                'agent'.$gain_result->agent_id, [
//                    'brand_name'   => $invitation->hasOneStore->hasOneBrand->name,
//                    'inspect_time' => date("Y年m月d日", $invitation->inspect_time),
//                ]
//            ], 'confirm_inspect_brand', 'text', 'true', 'user');
            $_datas = trans('tui.confirm_inspect_brand', [
                'brand_name'   => $invitation->hasOneStore->hasOneBrand->name,
                'inspect_time' => date("Y年m月d日", $invitation->inspect_time),
            ]);
            $send_notice_result = SendCloudMessage($invitation->uid,'agent'.$gain_result->agent_id,  $_datas, 'RC:TxtMsg', '', true,'one_user');
        }


        $agent = Agent::find($invitation->agent_id);
        $user = User::where('uid', $invitation->uid)->first();
        $name = $user->realname? $user->realname: $user->nickname;


        //给积分
        Agentv010200::add($invitation->agent_id, AgentScoreLog::$TYPES_SCORE[9], 9, '成功邀请投资人考察品牌', $post_id, 1);

        //Xavier 接受了你的 喜茶考察邀请
        //发透传用于透传
        $content = [
            'type' => 'new_remind',
            'style' => 'json',
            'value' => [
                'title' => "{$name} 接受了你的 {$data['brand_title']}考察邀请",
                'sendTime' => time(),
            ],
        ];
        $tui_result = send_transmission(json_encode($content), $agent, null, 1);


        $url = 'https://'.env(APP_HOST).'/webapp/agent/newsinvestask/detail?inspect_id='.$invitation->id.'is_out=1';
        //给经纪人发送短信
        $res = SendTemplateSMS('inspect_invitation_pay',
            $agent->non_reversible, 'inspect_invitation_pay',
            [
                'name' => $name,   //客户名称
                'username' => $user->username,   //客户手机号
                'brand_title' => $invitation->hasOneStore->hasOneBrand->name,   //门店名称
                'store_title' => $invitation->hasOneStore->name,   //门店名称
                'inspect_time' => $inspect_time,   //考察时间
                'amount' => $invitation->default_money,   //费用，以元为单位
                'shorturl' => substr(shortUrl($url), 7),   //短链接
            ],
            $agent->nation_code);


        $url = 'https://'.env(APP_HOST).'/webapp/investinvitation/detail/'.config('app.version').'?inspect_id='.$invitation->id.'&uid='.$user->uid.'&is_out=1';



        //找出该投资人的邀请经纪人
//        $agentCustomer = AgentCustomer::whereIn('source', [1,6,7])->where('uid', $invitation->uid)->first();


//        if($agentCustomer){
//            $ids = Invitation::whereIn('status', [-4,1,2])->where('uid', $invitation->uid)->get();
//            $ids = array_pluck($ids, 'id');
//            $exists = AgentCurrencyLog::where('agent_id', $agentCustomer->agent_id)->where('type', 15)->whereIn('post_id', $ids)->first();
//
//            if(!$exists){
//                //给该经纪人添加奖励
//                AgentCurrencyLog::addCurrency($agentCustomer->agent_id, 200, 15, $post_id, 1);
//            }
//
//        }


        //给客户发送短信
        $res = SendTemplateSMS('inspect_invitation_pay_customer',
            $user->non_reversible, 'inspect_invitation_pay_customer',
            [
                'name' => $agent->is_public_realname ?$agent->realname: $agent->nickname,   //经纪人名称
                'zone' => Zone::getCityAndProvince($agent->zone_id),   //地区  浙江 杭州
                'brand_title' => $invitation->hasOneStore->hasOneBrand->name,   //品牌名称
                'slogan' => $invitation->hasOneStore->hasOneBrand->slogan,   //品牌口号
                'store_title' => $invitation->hasOneStore->name,   //门店名称
                'inspect_time' => $inspect_time,   //考察时间
                'amount' => $invitation->default_money,   //费用，以元为单位
                'shorturl' => substr(shortUrl($url), 7),   //短链接
            ],
            $user->nation_code);

            //如果投资人使用了红包抵扣定金，再发送一条短信提示 zhaoyf
            self::_sendInfos(['id' => $post_id], Invitation::class);

        $agentCustomer = AgentCustomer::with('belongsToAgent')->where('uid', $invitation->uid)->whereIn('source', [1,7])->first();
        //如果该投资人有邀请的经纪人就也发条通知
        if ($agentCustomer) {

            //有地区就取---【原来写法： $text['zone'] = '('.Zone::getCityAndProvince($user->zone_id).')';】
            //导致地区没有出来，写法有问题； 更改于 2017-12-15 17:10 zhaoyf
            if ($user->zone_id) {
                $zone = '('.Zone::getCityAndProvince($user->zone_id).')';
            } else { $zone = ""; }

            $text = [
                'name'         => $name,                                       //客户名称
                'time'         => $inspect_time,                               //考察时间
                'brand'        => $invitation->hasOneStore->hasOneBrand->name, //品牌名称
                'inspect_zone' => Zone::getCityAndProvince($invitation->hasOneStore->zone_id),   //地区  浙江 杭州
                'zone'         => $zone,
            ];

            $text = trans('notification.invite_accept', $text);

            //因为之前移动端已经有了一个跳转到投资人详情页的，所以这里用invite_customer，方便移动端
            $content = json_encode(['type'=>'invite_customer', 'style'=>'id', 'value' => $user->uid]);
            @send_notification('邀请投资人接受考察邀请函', $text, $content ,$agentCustomer->belongsToAgent, null, 1);

            //在圣诞英雄榜中添加数据    -- 数据中心暂不处理 这个
            event(new ChristmasWinPrize(['type'=>3 , 'agent'=>$agent ,'brandName'=> $invitation->hasOneStore->hasOneBrand->name , 'username'=>$user['username']]));

        }
    }

    /*
    * 作用：积分充值付款成功后的操作
    * 参数：$id 评论id
    * 返回值：
    */
    public static function scoreAfterPay($uid, $post_id, $status, $orders_id)
    {
        //防止多次通知
        if ($status != 'npay') {
            return false;
        }

        $score_good = GoodsV020700::where('id', $post_id)->first();
        $result = ScoreLog::add($uid, $score_good->num, 'score_buy', '积分充值', 1, false, 'orders', $orders_id);

        return $result;
    }


    /*
    * 作用：品牌商品加盟付款成功后的操作
    * 参数：$id 评论id
    * 返回值：
    */
    public static function brandGoodsAfterPay($uid, $post_id, $content, $zone_id, $status, $mobile, $realname)
    {
        if ($status != 'npay') {
            return false;
        }
        $goods = BrandGoods::where('id', $post_id)->select('brand_id')->first();
        //写入洽询表
        Intent::create(
            [
                'uid' => $uid,
                'brand_id' => $goods->brand_id,
                'zone_id' => $zone_id,
                'mobile' => $mobile,
                'realname' => $realname
            ]
        );
        $result = Consult::create(
            [
                'type' => 'brand_goods',
                'relation_id' => $content,
                'brand_id' => $goods->brand_id,
                'uid' => $uid,
                'zone_id' => $zone_id
            ]
        );
        return $result;
    }

    /*
    * 作用：品牌付款成功后的操作
    * 参数：$id 评论id
    * 返回值：
    */
    public static function brandAfterPay($uid, $post_id, $type, $content, $form, $zone_id, $status)
    {
        if ($status != 'npay') {
            return false;
        }
        $goods = Goods::where('id', $post_id)->select('brand_id', 'live_id')->first();
        //添加评论
        $comment = Comment::create(
            [
                'uid' => $uid,
                'post_id' => $goods->live_id,
                'type' => $type,
                'content' => $content,
                'form' => $form,
            ]
        );

        //再写入洽询表
        $result = Consult::create(
            [
                'type' => 'prepay',
                'relation_id' => $content,
                'brand_id' => $goods->brand_id,
                'uid' => $uid,
                'zone_id' => $zone_id
            ]
        );

        $brand = Brand::where('id', $goods->brand_id)->select('name')->first();
        $user = User::where('uid', $uid)->first();
        $items = Items::where('id', $content)->select('order_id')->first();

        //发推送
        $order = DB::table('orders')->where('id', $items->order_id)->first();
        //发送短信  如果是美国号码就不发短信
        if (strlen($order->mobile) == 11) {
            @SendTemplateSMS('brand_prepay', $order->mobile, 'brand_prepay', ['brand_name' => $brand->name, 'order_no' => $order->order_no]);
        }

        createMessage(
            $uid,
            $title = '成功购买“立即加盟”产品',
            $content = '你已经成功加盟' . $brand->name . '，1-2个工作日内客服会跟你联系，<a href="' . "wjsq://orderdetail?order_no=" . $order->order_no . "&oi_id=" . $content . '">' . '点击查看' . '</a>详情',
            $ext = '',
            $end = '',
            $type = 1
        );

        return $result;
    }

    /*
    * 作用：根据评论id获取该评论的id
    * 参数：$id 评论id
    * 返回值：
    */
    public static function reward($uid, $post_id, $type, $content, $form, $status)
    {
        if ($status != 'npay') {
            return false;
        }
        $user = User::where('uid', $uid)->first();
        $result = Comment::create(
            [
                'uid' => $uid,
                'post_id' => $post_id,
                'type' => $type,
                'content' => $content,
                'nickname' => $user->nickname,
                'form' => $form,
            ]
        );

        return $result;
    }

    /**
     *专版支付成功后的处理   --数据中心版弃用  不处理
     * uid:用户id
     * vip_term_id:套餐id
     * order_id:订单id
     * num:数量
     * id：orders_items id
     */
    public static function vipAfterPay($uid, $vip_term_id, $order_id, $num, $id, $status)
    {
        if ($status != 'npay') {
            return false;
        }        //更新orders表格的pay_at字段
        Orders::updateOrderByField(['pay_at' => time()], ['id' => $order_id]);

        $user = User::getRow(['uid' => $uid]);

        //往user_vip表格中插入记录
        $term = VipTerm::getRow(['id' => $vip_term_id]);
//        print_r($vip_term_id);exit;

        $vip = VipEntity::getRow(['id' => $term->vip_id]);

        $arr = array('day' => '天', 'week' => '周', 'month' => '个月', 'year' => '年');

        for ($i = 1; $i <= $num; $i++) {
            $records = UserVip::getByUid($uid, $term->vip_id);
            if ($records['end_time'] < time()) {
                $start_time = mktime(0, 0, 0, date('m'), (date('d') + 1), date('Y'));
            } else {
                $start_time = $records['end_time'];
            }

            if ($term->unit == 'year') {
                $end_time = mktime(0, 0, 0, date('m', $start_time), (date('d', $start_time) + $term->number), (date('Y', $start_time) + $term->number));
            } elseif ($term->unit == 'month') {
                $end_time = mktime(0, 0, 0, (date('m', $start_time) + $term->number), date('d', $start_time), date('Y', $start_time));
            } elseif ($term->unit == 'week') {
                $end_time = mktime(0, 0, 0, date('m', $start_time), (date('d', $start_time) + 7 * $term->number), date('Y', $start_time));
            } else {
                $end_time = mktime(0, 0, 0, date('m', $start_time), (date('d', $start_time) + $term->number), date('Y', $start_time));
            }
            //往user_vip表格中插入数据
            $user_vip = UserVip::create(
                [
                    'uid' => $uid,
                    'vip_id' => $term->vip_id,
                    'vip_term_id' => $vip_term_id,
                    'start_time' => $start_time,
                    'end_time' => $end_time,
                    'order_id' => $order_id,
                    'orders_items_id' => $id,
                ]
            );

            //发送官方消息
            $param = [
                'title' => '你已成功购买 ' . $term->name . ' ' . $term->number . $arr[$term->unit] . '会员套餐',
                'uid' => $uid,
                'content' => json_encode(
                    [
                        'period' => $term->number . $arr[$term->unit],
                        'term_name' => $term->name,
                        'expire_time' => date('Y-m-d', $end_time),
                        'vip_id' => $term->vip_id,
                        'vip_name' => $vip->name,
                    ]
                ),
                'type' => 7,
                'post_id' => $id,
                'url' => 'vip_id=' . $term->vip_id,
                'send_time' => time(),
            ];

            Message::create($param);

            //发送短信
            @SendTemplateSMS('buyVip', $user->username, 'buyVip', ['vip_name' => $vip->name, 'vip_term_name' => $term->name, 'expire' => date('Y-m-d', $end_time)], $user->nation_code);
        }
    }

    /*
     * 我的订单
     */
    static function baseQuery($page_size = 10, Closure $callback = null, Closure $formatCallback = null)
    {
        $builder = self::query();

        if ($callback) {
            if ($formatCallback) {
                return $formatCallback($callback($builder));
            }
            return $callback($builder);
        }

        return $builder->paginate($page_size);
    }

    /**
     * author zhaoyf 2017-12-8
     *
     * 短信通知
     *
     * @param param   参数ID值  array
     *
     */
    private static function _sendInfos(array $param, $class)
    {
        //发送消息数据
        Events::instance($param)
           ->attach($class)
           ->sendInform();

        //消息发送后释放对象
        Events::instance()->detach($class);
    }
}