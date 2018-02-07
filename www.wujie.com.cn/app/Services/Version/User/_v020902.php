<?php
/**
 * Created by PhpStorm.
 * Title：
 * User: yaokai
 * Date: 2017/11/9 0009
 * Time: 10:12
 */

namespace App\Services\Version\User;


use App\Models\Agent\Invitation;
use App\Models\AgentScore;
use App\Models\Brand\Payinfo;
use App\Models\Contract\Contract;
use App\Models\Agent\ContractPayLog;
use App\Models\Agent\Agent;
use App\Models\Brand\Goods;
use App\Models\MoneyLog;
use App\Models\Orders\Entity as Orders;
use App\Models\RedPacket\RedPacketPerson;
use App\Models\User\Entity as User;
use App\Models\Activity\Entity as Activity;
use App\Models\Activity\Entity\V020700 as ActivityV020700;
use App\Models\Live\Entity as Live;
use App\Models\News\Entity as News;
use App\Models\User\Withdraw;
use App\Models\Brand\Entity as Brand;
use App\Models\Activity\Ticket;
use App\Models\Orders\Entity;
use App\Models\Orders\Entity\V020700 as OrdersV020700;
use App\Http\Controllers\Api\UserController;
use App\Models\Video;
use \DB;
use App\Models\User\Praise;
use App\Models\Comment\Entity as Comment;
use App\Models\Agent\AgentCustomer;
use App\Models\Zone\Entity as Zone;


class _v020902 extends _v020900
{
    /**
     * 订单详情
     */
    public function postMyorderinfo($param)
    {
        $nowTime = time();
        $uid = abs((int)$param['request']->input('uid', 0));
        if (!$uid) {
            return ['message' => 'uid必填项', 'status' => false];
        }

        if (!($user = User::find($uid))) {
            return ['message' => '非法的uid', 'status' => false];
        }
        $type = $param['request']->input('type');
        $refer_id = $param['request']->input('refer_id');

        $orerInfo = [];

        if ($type && $refer_id) {
            goto oldVersion;
        }

        $userObj = new UserController($param['request']);

        $order_no = $param['request']->input('order_no');

        if (!$order_no) {
            return ['message' => 'order_no必填项', 'status' => false];
        }

        if (preg_match('/^[A-Za-z]{3}/', $order_no)) {
            $table = 'orders';
            $order_id = Entity::where('order_no', $order_no)->value('id');
        } else {
            $table = 'order';
            $order_id = \App\Models\Order\Entity::where('order_no', $order_no)->where('uid', $uid)->value('id');
        }
        if (!$order_id) {
            return ['message' => '订单不存在', 'status' => false];
        }

        if ($table == 'order') {
            $oldData = \App\Models\Order\Entity::baseQuery(
                10,
                function ($builder) use ($uid, $order_id) {
                    $data = $builder->where('order.uid', $uid)
                        ->where('order.id', $order_id)
                        ->join('activity_ticket as at', 'order.ticket_id', '=', 'at.id')
                        ->join('user_ticket as ut', 'ut.order_id', '=', 'order.id')
                        ->select(
                            'order.status',
                            'ut.type',
                            'ut.activity_id',
                            'order.online_money as amount',
                            'at.id as oi_id',
                            'order.pay_way',
                            'order.updated_at as pay_at',
                            'order.id',
                            'ut.score_price'
                        )
                        ->orderBy('pay_at', 'desc')
                        ->get();

                    return $data;
                },
                $this->oldDataFormat($userObj)
            );

            if (!$oldData) {
                return ['message' => '订单不存在', 'status' => false];
            }

            $needFormatData = $oldData->toArray();
            $orerInfo = array_map($this->newDataFormat(), $needFormatData);
            $orerInfo = $orerInfo[0];

            $type = array_get($orerInfo, 'type');
            $refer_id = array_get($orerInfo, 'refer_id');
        }
        elseif ($table == 'orders') {
            /*
             * V020800,添加合同和考察订单，改版直接在207版上改动，接口调用链接还是以前的。
             * */
            $orderInfo = Orders::with('hasOneOrdersItems')->where('id',$order_id)->first()->toArray();
            $orderType = trim($orderInfo['has_one_orders_items']['type']);
            if($orderType == 'inspect_invite'){
                $orderInfos = Orders::with('hasOneOrdersItems.belongsToInvitation.hasOneStore.hasOneBrand.categorys1',
                    'hasOneOrdersItems.belongsToInvitation.hasOneStore.hasOneZone',
                    'hasOneOrdersItems.belongsToInvitation.belongsToAgent.hasOneZone')
                    ->with(['hasOneOrdersItems'=>function($query){
                        $query->where('type','inspect_invite');
                    }])
                    ->where('id',$order_id)->first()->toArray();
                $arr = [];
                $status = trim($orderInfos['status']);
                $statusStr = '';
                switch ($status){
                    case 'pay': $statusStr= '已支付';break;
                    case 'npay': $statusStr= '未支付';break;
                    case 'expire': $statusStr= '已过期';break;
                }
                $payWay = trim($orderInfos['pay_way']);
                $payWayStr = '';
                switch ($payWay){
                    case 'ali': $payWayStr= '支付宝';break;
                    case 'weixin': $payWayStr= '微信';break;
                    case 'unionpay': $payWayStr= '银联';break;
                    case 'red_packet': $payWayStr= '邀请红包抵扣';break;
                    default : $payWayStr= '积分';
                }
                //判断对该经纪人是否公开手机号
                $agentId = trim($orderInfos['has_one_orders_items']['belongs_to_invitation']['belongs_to_agent']['id']);
                $uid = trim($orderInfos['uid']);
                $agentCustomerInfo = AgentCustomer::where(function($query)use($agentId,$uid){
                    $query->where('agent_id',$agentId);
                    $query->where('uid',$uid);
                })->first();

                $arr = array(
                    'tel_public' => trim($agentCustomerInfo['has_tel']),
                    'type'=> 'inspect_invite',
                    'brand_logo' => getImage($orderInfos['has_one_orders_items']['belongs_to_invitation']['has_one_store']['has_one_brand']['logo'],'',''),
                    'brand_title' => trim($orderInfos['has_one_orders_items']['belongs_to_invitation']['has_one_store']['has_one_brand']['name']),
                    'goodsName' => trim($orderInfos['has_one_orders_items']['belongs_to_invitation']['has_one_store']['has_one_brand']['name']),
                    'brand_slogan' => trim($orderInfos['has_one_orders_items']['belongs_to_invitation']['has_one_store']['has_one_brand']['slogan']),
                    'category_name' => trim($orderInfos['has_one_orders_items']['belongs_to_invitation']['has_one_store']['has_one_brand']['categorys1']['name']),
                    'brand_id' => trim($orderInfos['has_one_orders_items']['belongs_to_invitation']['has_one_store']['has_one_brand']['id']),
                    'store' => trim($orderInfos['has_one_orders_items']['belongs_to_invitation']['has_one_store']['name']),
                    'zone' => trim($orderInfos['has_one_orders_items']['belongs_to_invitation']['has_one_store']['has_one_zone']['name']),
                    'address' => trim($orderInfos['has_one_orders_items']['belongs_to_invitation']['has_one_store']['address']),
                    'created_at' => trim($orderInfos['has_one_orders_items']['belongs_to_invitation']['inspect_time']),
//                    'agent_name' => trim($orderInfos['has_one_orders_items']['belongs_to_invitation']['belongs_to_agent']['nickname']),
                    'agent_name' => Agent::unifiHandleName($orderInfos['has_one_orders_items']['belongs_to_invitation']['belongs_to_agent']),
                    'agent_id' => trim($agentId),
                    'agent_avatar' => getImage($orderInfos['has_one_orders_items']['belongs_to_invitation']['belongs_to_agent']['avatar'],'avatar',''),
                    'agent_gender' => trim($orderInfos['has_one_orders_items']['belongs_to_invitation']['belongs_to_agent']['gender']),
                    'agent_username' => trim($orderInfos['has_one_orders_items']['belongs_to_invitation']['belongs_to_agent']['username']),
                    'agent_city' => trim($orderInfos['has_one_orders_items']['belongs_to_invitation']['belongs_to_agent']['has_one_zone']['name']),
                    'type_name' => '考察订金',
                    'status' => $statusStr,
                    'amount' => '¥ '.doFormatMoney(floatval($orderInfos['amount'])),
                    'pay_way' => $payWayStr,
                    'pay_at' => trim(date('Y-m-d H:i:s',$orderInfos['updated_at'])),
                    'account_info' => accountEncrypt($orderInfos['buyer_id'],$payWay),
                    'should_pay' => '¥ '.doFormatMoney(floatval($orderInfos['amount'])),
                );
                $content['orderInfo'] = $arr;
                return ['message' => $content, 'status' => true];
            }
            if($orderType == 'contract'){
                $contractId = intval($orderInfo['has_one_orders_items']['product_id']);
                //获取优惠清单数据
                $discountInfo = ContractPayLog::getPayDetailByType($contractId , ContractPayLog::$_REFUND_TYPES ,'success','type');
                //获取pos 支付数据
                $posPayInfo = ContractPayLog::getPayDetailByType($contractId , ContractPayLog::$_PAY_TYPES);
                //获取加盟信息数据
                $leagueInfo = Contract::with(['brand','brand_contract','agent', 'brand_contract.brandContractCost' => function($query) {
                    $query->where('is_delete', 0)
                        ->orderBy('sort', 'asc')
                        ->select('brand_contract_id', 'cost_type', 'cost_limit', 'is_commission');
                }])
                    ->where('id',$contractId)->first();
                //获取该经纪人是否对该投资人公开手机号码
                $agentId = trim($leagueInfo->agent_id);
                $uid = trim($leagueInfo->uid);
                $agentCustomerInfo = AgentCustomer::where(function($query)use($agentId,$uid){
                    $query->where('agent_id',$agentId);
                    $query->where('uid',$uid);
                })->select('has_tel')->first();
                $arr = [];
//                $arr['type_name'] = '付款协议';
                $arr['type'] = 'contract';
                $arr['tel_public'] = trim($agentCustomerInfo['has_tel']);
                $arr['contract_name'] = trim($leagueInfo['name']);
                $arr['contract_id'] = trim($leagueInfo['id']);
                $arr['contract_no'] = trim($leagueInfo['contract_no']);
                $arr['contract_addr'] = trim($leagueInfo['address']);
                $arr['brand_title'] = trim($leagueInfo->brand->name);
                $arr['league_type'] = trim($leagueInfo->brand_contract['league_type']);
                $arr['amount'] = '¥ '.doFormatMoney(floatval($leagueInfo->amount));
//                $arr['discount']['total'] = '¥ '.doFormatMoney(floatval($discountInfo['total']));
//                $arr['discount']['list'] = [];
//                foreach ($discountInfo['list'] as $one){
//                    $one['num'] = '¥ '.doFormatMoney($one['num']);
//                    $arr['discount']['list'][] = $one;
//                }
                $arr['should_pay'] = '¥ '.doFormatMoney(floatval($leagueInfo->amount - $discountInfo['total'] > 0 ?
                        $leagueInfo->amount - $discountInfo['total'] : 0 ));

//                '合同状态： -3主动撤回； -2超时拒绝； -1主动拒绝； 0待支付； 1已首付； 2已签订(全付)；
//                3支付中（款项已经支付过，不过没有全部支付完） 4申请返现中 5返现成功；',

//                订单状态0未付款 1支付中 2支付完成3返现申请中 4 返现成功
                if (in_array($leagueInfo['status'], [0,6])) {
                    $arr['contract_status'] = '0'; //未支付
                } else if (in_array($leagueInfo['status'], [3])) {
                    $arr['contract_status'] = '1';  //完成支付
                } else if (in_array($leagueInfo['status'], [2])) {
                    $arr['contract_status'] = '2';  //完成支付
                } elseif (in_array($leagueInfo['status'], [4])) {
                    $arr['contract_status'] = '3';  //已返现
                }elseif (in_array($leagueInfo['status'], [5])) {
                    $arr['contract_status'] = '4';  //已返现
                } else {
                    throw new \Exception('数据异常');
                }

                $arr['total_pay'] = '¥ '.doFormatMoney(floatval($posPayInfo['total']));
                $arr['residue'] = '¥ '.doFormatMoney(floatval($leagueInfo->amount - $posPayInfo['total']));
                //处理卡号，保留后四位
                foreach ($posPayInfo['list'] as &$one){
                    $one['num'] = '¥ '.doFormatMoney(floatval($one['num']));
                    $one['bank_card_no'] = empty($one['bank_card_no']) ? '' : substr($one['bank_card_no'] , -4) ;
                }

                $arr['pos_list'] = $posPayInfo['list'];
                $arr['agent_avatar'] = getImage($leagueInfo->agent->avatar , 'avatar','');
                $arr['agent_nickname'] = Agent::unifiHandleName($leagueInfo->agent);
                $arr['agent_id'] = trim($leagueInfo->agent->id);
                $arr['agent_gender'] = trim($leagueInfo->agent->gender);
                $arr['agent_city'] = Zone::getCityAndProvince($leagueInfo->agent->agent_level_id);
                $arr['agent_username'] = trim(getRealTel($leagueInfo->agent->non_reversible , 'agent'));
                //判断投资人是否对改合同评价过
                $isEvaluate = AgentScore::where('contract_id' , $contractId)->first();
                $arr['order_isComment'] = '0';
                is_object($isEvaluate) && $arr['order_isComment'] = '1';

                $arr['created_at'] =  date('Y-m-d H:i', $orderInfo['has_one_orders_items']['created_at']);  //订单生成时间

                $arr['brand_id'] = $leagueInfo->brand_id;
                $arr['is_commented'] = Contract::isCommented($contractId, $uid);
                //支付信息
                $account_info = Payinfo::where('brand_id', $leagueInfo->brand_id)->where('status', 0)->first();

                if(!$account_info){
                    throw new \Exception('该品牌没有账号信息，数据异常');
                }

                $arr['account_company_name'] = $account_info->company; //公司名称
                $arr['account_account'] = $account_info->account;//账户
                $arr['account_bank_name'] = $account_info->bank_name; //银行

                //优惠红包
                $red_packet = ContractPayLog::usedDiscount($contractId);
                $arr['initial_packet'] = $red_packet['initial']; //初创红包
                $arr['packet_sum'] = $red_packet['packet_sum'];  //红包优惠
                $arr['invite_packet'] = $red_packet['invite'];   //考察订金
                $arr['intent_packet'] = $red_packet['intent_brand']; //意向加盟金
                $arr['total_packet'] = $red_packet['total'];


                //返现信息
                if (in_array($leagueInfo['status'], [4, 5])) {
                    $withdraw = Withdraw::where('relation_type', 1)->where('relation_id', $contractId)->first();
                    $arr['withdraw_account_type'] = $withdraw->account_type;    //账户类型
                    $arr['withdraw_account'] = $withdraw->account;    //账户
                    $arr['withdraw_realname'] = $withdraw->name;    //真实姓名
                    $arr['withdraw_created_at'] = date('Y-m-d H:i',$withdraw->created_at->timestamp);    //提交日期
                    $arr['withdraw_status'] = $withdraw->status;    //状态
                    if($withdraw->status==2){
                        $arr['withdraw_reply_at'] = date('Y-m-d H:i',$withdraw->reply_at);    // 打款日期
                    }
                }


                //获取合同模板费用
                if (!is_null($leagueInfo->brand_contract->brandContractCost)) {
                    foreach ($leagueInfo->brand_contract->brandContractCost as $key => $vls) {
                        $arr['cost'][$key] = [
                            'cost_type' => $vls->cost_type,
                            'cost_limit' => number_format($vls->cost_limit),
                        ];
                    }
                }

                $content['orderInfo'] = $arr;
                return ['message' => $content, 'status' => true];
            }


            $data = OrdersV020700::myOrders(
                ['uid' => $uid],
                function ($builder) use ($order_id) {
                    return $builder
                        ->where('orders.id', $order_id)
                        ->first();
                }
            );
            if (!$data) {
                return ['message' => '订单不存在', 'status' => false];
            }

            $data->status = $userObj->payStatus($data->oi_status);//支付状态
            $data->amount = formatMoney(number_format($data->amount, 2));
            if ($data->amount == '0') {
                $data->amount = '免费';
            } else {
                $data->amount = '¥' . $data->amount;
            }
            $data->type_name = $this->getOrderType($data);
            $data->table = 'orders';
            $data->goodsName = $this->getGoodsName($data, $userObj);
            $info = $userObj->productInfo($data);
            $data->refer_id = $info ? $info->id : 0;
            $data->treaty = $info ? $info->treaty : '';
            $pay_way = $data->pay_way;

            //支付方式
            if ($pay_way == 'weixin') {
                $data->pay_way = '微信';
            } elseif ($pay_way == 'ali') {
                $data->pay_way = '支付宝';
            } else {
                $data->pay_way = '积分';
            }

            $data->oi_id = $data->product_id;
            $title = Orders::getProduct($data->type, $data->product_id);
            $data->type_name = $title['type'];//商品类型
            $data->order_type = $title['order_type'];//订单类型
            unset($data->order_no, $data->created_at, $data->oi_status, $data->product_id);

            array_map($this->newDataFormat(), [$data]);
            $orerInfo = $data;

            $type = $orerInfo->type;
            $refer_id = $orerInfo->refer_id;
        }

        oldVersion:
        $content = [];

        switch ($type) {
            case 'brand_goods':
                goto brand;

                break;
            case 'brand':

                brand:
                if ($rawData = Brand::where('id', $refer_id)->first()) {
                    $live_goods = Goods::select('title', 'league')->where('id', $data->oi_id)->first();
                    $content = [
                        'id'                 => $refer_id,
                        'name'               => $rawData->name,
                        'goods_name'         => $live_goods->title,
                        'goods_league'       => trim(preg_replace('#<\w+(\s+.*?)?>|</\w+>|&nbsp;|\r|\n|\t#', '', $live_goods->league)),
                        'keywords'           => $rawData->keywords ? strpos($rawData->keywords, ' ') !== false ? explode(' ', $rawData->keywords) : [$rawData->keywords] : [],
                        /**                        'detail' => trim(preg_replace('#<\w+(\s+.*?)?>|</\w+>|&nbsp;|\r|\n|\t#','',$rawData->details)),*/
//                        'brand_summary' => $rawData->brand_summary? :(mb_substr(preg_replace('/\s+/i','',str_replace('&nbsp;','',strip_tags($rawData->details))),0,50)),
                        'treaty'             => strip_tags($rawData->treaty),
                        'logo'               => getImage($rawData->logo, '', ''),
                        'investment_arrange' => formatMoney($rawData->investment_min) . '-' . formatMoney($rawData->investment_max) . '万',
                        'category_name'      => \DB::table('categorys')->where('id', $rawData->categorys1_id)->first()->name,
                    ];
                }

                break;
            case 'activity':

                if ($rawData = Activity::find($refer_id)) {
                    $status = ActivityV020700::ActivityStatus($rawData->begin_time, $rawData->end_time);
                    $content = [
                        'id'                => $refer_id,
                        'name'              => $rawData->subject,
                        'list_img'          => getImage($rawData->list_img, 'activity', ''),
                        'keywords'          => $rawData->keywords ? strpos($rawData->keywords, ' ') !== false ? explode(' ', $rawData->keywords) : [$rawData->keywords] : [],
                        'begin_time_format' => date("m月d日 H:i", $rawData->begin_time),
                        'status'            => $status,
                        'host_cities'       => Activity::getAllCitiesOfActivity($refer_id),
                    ];
                    $content['begin_time'] = $content['begin_time_format'];
                    $content['cities'] = $content['host_cities'];
                }

                break;
            case 'live':

                if ($rawData = Live::where('activity_id', $refer_id)->first()) {
                    $status = ActivityV020700::ActivityStatus($rawData->begin_time, $rawData->end_time);
                    $ticket = Ticket::where('activity_id', $rawData->activity_id)->where('type', 2)->where('status', 1)->min('score_price');
                    $content = [
                        'id'                => $refer_id,
                        'name'              => $rawData->subject,
                        'list_img'          => getImage($rawData->list_img, 'live', ''),
                        'summary'           => $rawData->summary ?: (mb_substr(preg_replace('/\s+/i', '', str_replace('&nbsp;', '', strip_tags($rawData->description))), 0, 50)),
                        'keywords'          => $rawData->keywords ? strpos($rawData->keywords, ' ') !== false ? explode(' ', $rawData->keywords) : [$rawData->keywords] : [],
                        'begin_time'        => date("m月d日 H:i", $rawData->begin_time),
                        'begin_time_format' => date('Y-m-d H:i:s', $rawData->begin_time),
                        'status'            => $status,
                        'score_price'       => $ticket ? ($ticket == '0.01' ? '0.01' : ($ticket == '0.00' ? '0' : $ticket)) : 0,
                    ];
                }

                break;
            case 'video':

                if ($rawData = Video::find($refer_id)) {
                    $content = [
                        'id'                => $refer_id,
                        'name'              => $rawData->subject,
                        'summary'           => $rawData->description ?: (mb_substr(preg_replace('/\s+/i', '', str_replace('&nbsp;', '', strip_tags($rawData->content))), 0, 50)),
                        'keywords'          => $rawData->keywords ? strpos($rawData->keywords, ' ') !== false ? explode(' ', $rawData->keywords) : [$rawData->keywords] : [],
                        'record_at'         => date("Y-m-d", $rawData->created_at->getTimestamp()),
                        'length'            => $rawData->duration ? changeTimeType($rawData->duration) : 0,
                        'image'             => getImage($rawData->image, 'video', ''),
                        'begin_time_format' => date('Y-m-d H:i:s', $rawData->created_at->getTimestamp()),
                    ];
                }

                break;
            case 'news':
                if ($rawData = News::find($refer_id)) {
                    $content = [
                        'id'            => $refer_id,
                        'name'          => $rawData->title,
                        'summary'       => $rawData->summary ?: (mb_substr(preg_replace('/\s+/i', '', str_replace('&nbsp;', '', strip_tags($rawData->detail))), 0, 50)),
                        'logo'          => getImage($rawData->logo, '', ''),
                        'author'        => $rawData->author,//作者
                        'view'          => $rawData->sham_view,//阅读
                        'count_zan'     => Praise::ZanCount($refer_id, 'news'),//点赞
                        'count_comment' => Comment::ConmmentCount($refer_id, 'News'),//评论
                    ];
                }

                break;
            default:
                $content = [];
                break;
        }

        if ($orerInfo) {
            $content['orderInfo'] = $orerInfo;
        }

        return ['message' => $content, 'status' => true];
    }
    /*
     * 红包详情
     */
    public function postPackageDetail($input)
    {
        $package=RedPacketPerson::with('red_packet','hasOneContractPayLogs')->where('id',$input['id'])->first();
        if(!$package){
            return ['message'=>'该红包不存在','status'=>false];
        }
        $data=[];
        $data['id'] = $package->id;
        $data['type'] = $package->red_packet->type;
        //数额
        $data['amount'] = abandonZero($package->red_packet->amount);
        //使用场景
        $data['use_scenes']=$package->red_packet->use_scence;
        //红包名称
        $data['name']=$package->red_packet->name;
        //红包描述
        $data['description']=$package->red_packet->description;
        //状态
        $data['status']=$package->status;
        //最低消费
        if($package->red_packet->min_consume){
            $data['min_consume']=$package->red_packet->min_consume;
        }
        switch ($package->status){
            case -1:
                $data['expire_at'] = date('Y.m.d', $package->expire_at);
                break;
            case 0:
                //使用期限
                $data['expire_at'] = $package->expire_at==-1?'不限期限':date('Y.m.d', $package->created_at->timestamp).'-'.date('Y.m.d', $package->expire_at);
                break;
            case 1:
                $data['expire_at'] = date('Y.m.d H:i:s', $package->used_at);
                //使用信息
                $data['used_info'] = $package->getUsedInfo();
                break;
            default:
                break;
        }

        //品牌红包
        if (2 == $package->red_packet->type) {
            //品牌标题
            $data['brand_name'] = $package->red_packet->brand->name;
            $data['brand_id'] = $package->red_packet->brand->id;
            $data['brand_logo'] = getImage($package->red_packet->brand->logo,'','');
            //判断是否有该品牌的跟单经纪人,有则加上经纪人id
            if($agent=AgentCustomer::with('belongsToAgent')->where('brand_id',$package->red_packet->brand->id)->where('status','<>',-1)->where('uid',$package->receiver_id)->first()){
                $data['agent_id']=$agent['agent_id'];
                $data['agent_name']=$agent['belongsToAgent']['realname']?$agent['belongsToAgent']['realname']:$agent['belongsToAgent']['nickname'];
            }

        }
        //奖励红包
        if (4 == $package->red_packet->type){
            //品牌标题
            $data['brand_name'] = $package->red_packet->brand->name;
            $data['brand_id'] = $package->red_packet->brand->id;
            $data['brand_logo'] = getImage($package->red_packet->brand->logo,'','');
            //邀请考察记录
            $agent=Invitation::with('belongsToAgent')->where('uid',$package->receiver_id)->where('status',3)->where('post_id',$package->red_packet->brand->id)->first();
            if($agent){
                $data['agent_id'] = $agent['agent_id'];
                $data['agent_name'] = $agent['belongsToAgent']['realname']?$agent['belongsToAgent']['realname']:$agent['belongsToAgent']['nickname'];
            }

        }

         return ['message'=>$data, 'status'=>true];

    }

    /*
     * 红包2.92新增排序及新红包类型
     */
    public function postPackagelist($param)
    {
        $validator = \Validator::make($param, [
            'uid' => 'required|exists:user,uid',
            'contact_id' => 'sometimes|exists:contract,id',
        ], [], [
            'uid' => '用户id',
            'contact_id' => '合同id',
        ]);

        if ($validator->fails()) {
            $warnings = $validator->messages()->all();
            return ['message' => $warnings[0], 'status' => false];
        }

        $page = array_get($param, 'page', 1);
        $pageSize = array_get($param, 'pageSize', 1000);
        $status = array_get($param, 'status', 0);

        //把到期的红包的状态改成-1
        RedPacketPerson::where('expire_at', '<', time())->where('expire_at', '<>', -1)->where('status', 0)->update(['status' => -1]);

        //在有合同id的时候需要按照本品牌来排序
        if (!empty($param['contact_id'])) {
            $contract = Contract::where('id', $param['contact_id'])->first();

            $ids = RedPacket::where(function ($query) {
                $query->where('post_id', 0)->where('type', 1);
            })->orWhere(function ($query) use ($contract) {
                $query->where('post_id', $contract->brand_id)->where('type', 2);
            })->orWhere('type', 3)
                ->lists('id')->toArray();

            $lists = RedPacketPerson::with('red_packet')->where('status', 0)
                ->where('receiver_id', $param['uid'])->where(function($query){
                    $query->where('expire_at', '>', time())->orWhere('expire_at', -1);
                })
                ->whereIn('red_packet_id', $ids)->orderBy('type', 'desc')//专属品牌红包放前面，全场放后面
                ->forPage($page, $pageSize)->get();

        } else {
            $lists = RedPacketPerson::with('red_packet')->where('status', $status)
                ->where('receiver_id', $param['uid'])->orderBy('created_at', 'desc')->forPage($page, $pageSize)->get();
        }



        $data = [];
        foreach ($lists as $k => $v) {
            $data[$k]['id'] = $v->id;
            //类型
            $data[$k]['type'] = $v->red_packet->type;
            //数额
            $data[$k]['num'] = abandonZero($v->red_packet->amount);
            //使用场景
            $data[$k]['use_scenes']=$v->red_packet->use_scence;
            //红包名称
            $data[$k]['name']=$v->red_packet->name;
            //红包描述
            $data[$k]['description']=$v->red_packet->description;

            if (in_array($v->red_packet->type, [1, 2, 3, 4])) {
                //开始时间
                $data[$k]['begin_time'] = date('Y/m/d', $v->created_at->timestamp);

                //结束时间或过期时间
                $data[$k]['expire_at'] = date('Y/m/d', $v->expire_at);
            }


            //已使用
            if ($status == 1) {
                $data[$k]['expire_at'] = date('Y/m/d', $v->used_at);
            }


            if (2 == $v->red_packet->type) {
                //品牌标题
                $data[$k]['brand_name'] = $v->red_packet->brand->name;
                $data[$k]['brand_id'] = $v->red_packet->brand->id;
                //判断是否有该品牌的跟单经纪人,有则加上经纪人id
                if($agent=AgentCustomer::with('belongsToAgent')->where('brand_id',$v->red_packet->brand->id)->where('status','<>',-1)->where('uid',$param['uid'])->first()){
                    $data[$k]['agent_id']=$agent['agent_id'];
                    $data[$k]['agent_name']=$agent['belongsToAgent']['nickname'];
                }

            }
            //奖励红包
            if (4 == $v->red_packet->type){
                //品牌标题
                $data[$k]['brand_name'] = $v->red_packet->brand->name;
                $data[$k]['brand_id'] = $v->red_packet->brand->id;
                $agent=Invitation::with('belongsToAgent')->where('uid',$param['uid'])->where('status',3)->where('brand_id',$v->red_packet->brand->id)->select('agent_id')->first();
                if($agent){
                    $data[$k]['agent_id'] = $agent['agent_id'];
                    $data[$k]['agent_name'] = $agent['belongsToAgent']['nickname'];
                }

            }

            //最低消费限制
            if(!empty($v->red_packet->min_consume)){
                $data[$k]['min_consume']=number_format($v->red_packet->min_consume);
            }

            if(!empty($data[$k]['expire_at']) && $data[$k]['expire_at']=='1970/01/01'){
                $data[$k]['expire_at'] = '不限期限';
            }

            if(!empty($data[$k]['begin_time']) && $data[$k]['begin_time']=='1970/01/01'){
                $data[$k]['begin_time'] = '不限期限';
            }

        }
        $result=[
            'brand'=>[],
            'common'=>[],
            'reward'=>[]
        ];
        foreach ($data as $v){
            switch ($v['type']){
                case 1:
                    $result['common'][]=$v;
                    break;
                case 2:
                    $result['brand'][]=$v;
                    break;
                case 3:
                    $result['common'][]=$v;
                    break;
                case 4:
                    $result['reward'][]=$v;
                    break;
            }
        }

        return ['message' => $result, 'status' => true];
    }



    /**
     * 现金提现申请
     *
     * @param Request $request
     * @param null $version
     * @return string
     * @author tangjb
     */
    public function postMoneyWithdraw($param)
    {
        $validator_result = \Validator::make($param, [
            'uid' => 'required|integer|exists:user,uid',
            'realname' => 'required',
            'account' => 'required',
            'account_type' => 'required|in:ali,bank',
            'contract_id' => 'required|integer|exists:contract_pay_log,contract_id',
        ],[
            'required' => ':attribute为必填项',
            'integer' => ':attribute必须为整数',
        ], [
            'uid' => '当前登录用户ID',
            'realname' => '真实姓名',
            'account' => '支付宝账号或银行卡号',
            'account_type' => '账户类型',
            'contract_id' => '合同id',
        ]);

        //对验证结果进行处理
        if ($validator_result->fails()) {
            return ['message' => $validator_result->messages()->first(), 'status' => false];
        }


        $bank_name = array_get($param, 'bank_name', '');
        //如果提现是银行卡类型
        if($param['account_type'] == 'bank'){
            if(!$bank_name){
                return ['message' => '开户行必填', 'status' => false];
            }
        }


        //获取该订单可提现的值
//        1：考察订金抵扣；2：pos机支付；3：通用红包 4：品牌红包  5:奖励红包(车马费) 6：初创红包（邀请红包) 7:新年活动经纪人答题红包  8: 线下到帐'
        $rebate = ContractPayLog::where('contract_id', $param['contract_id'])
            ->where('status', 1)->whereIn('type', ContractPayLog::$_REFUND_TYPES)->sum('num');


        //开始事务
        \DB::beginTransaction();
        try {
            $res = Contract::where('id',$param['contract_id'])->update(['status'=>4]);
            MoneyLog::extractMoney($param['uid'], $rebate, $param['account_type'], $param['account'], $param['realname'], $bank_name, $param['contract_id']);
            \DB::commit();
        } catch (\Exception $e) {
            \DB::rollBack();
            throw(new \Exception('操作失败' . $e->getMessage()));
        }

        return ['message' => '操作成功', 'status' => true];
    }


    /**
     * 打开邀请函
     *
     * @param $param
     * @return array
     * @author tangjb
     */
    public function  postOpenInvitation($param)
    {
        $validator_result = \Validator::make($param, [
            'id' => 'required|integer|exists:invitation,id',
        ],[
            'required' => ':attribute为必填项',
            'integer' => ':attribute必须为整数',
        ], [
            'id' => '邀请函ID',
        ]);

        //对验证结果进行处理
        if ($validator_result->fails()) {
            return ['message' => $validator_result->messages()->first(), 'status' => false];
        }

        $invitation = Invitation::with('hasOneUsers', 'hasOneStore.hasOneBrand')->where('type', 2)->where('open_time',  0)
            ->where('id', $param['id'])->first();

        $invitation->open_time = time();
        $invitation->save();

        $agent = Agent::where('id', $invitation->agent_id)->first();
        $realname = $invitation->hasOneUsers->realname ? $invitation->hasOneUsers->realname : $invitation->hasOneUsers->nickname;
        $zone = Zone::getCityAndProvince($invitation->hasOneUsers->zone_id);
        //'你的投资人 :name（:province :city）打开了你发送的【:brand_name】考察邀请函，戳>>',
        $text = trans('notification.open_invitation', ['name' => $realname, 'zone' => $zone,
             'brand_name' => $invitation->hasOneStore->hasOneBrand->name]);

        if(!$agent){
            return ['message' => '不存在对应的经纪人', 'status' => false];
        }

        $res = send_notification('邀请函被打开', $text,
            json_encode(['type'=>'open_invitation',
                'style'=>'uid',
                'nickname'=>$invitation->hasOneUsers->nickname ,
                'value'=>$invitation->hasOneUsers->uid]
            ),
            $agent, null, 1);

        return ['message' => '操作成功', 'status' => true];
    }



    /**
     * 打开邀请函
     *
     * @param $param
     * @return array
     * @author tangjb
     */
    public function  postOpenContract($param)
    {
        $validator_result = \Validator::make($param, [
            'id' => 'required|integer|exists:contract,id',
        ],[
            'required' => ':attribute为必填项',
            'integer' => ':attribute必须为整数',
        ], [
            'id' => '合同ID',
        ]);

        //对验证结果进行处理
        if ($validator_result->fails()) {
            return ['message' => $validator_result->messages()->first(), 'status' => false];
        }

        $contract = Contract::with('user', 'brand')->where('id', $param['id'])->where('open_time', 0)->first();
        $contract->open_time = time();
        $contract->save();

        $agent = Agent::where('id', $contract->agent_id)->first();
        $realname = $contract->user->realname ? $contract->user->realname : $contract->user->nickname;
        $zone = Zone::getCityAndProvince($contract->user->zone_id);
        //'你的投资人 :name（:province :city）打开了你发送的【:brand_name】考察邀请函，戳>>',
        $text = trans('notification.open_contract', ['name' => $realname, 'zone' => $zone,
            'brand_name' => $contract->brand->name]);

        if (!$agent) {
            return ['message' => '不存在对应的经纪人', 'status' => false];
        }

        $res = send_notification('合同被打开', $text,
            json_encode(['type' => 'open_contract',
                    'style' => 'uid',
                    'nickname' => $contract->user->nickname,
                    'value' => $contract->user->uid]
            ),
            $agent, null, 1);




        return ['message' => '操作成功', 'status' => true];
    }



}