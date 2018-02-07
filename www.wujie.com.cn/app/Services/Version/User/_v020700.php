<?php

/*
 * 用户编辑
 */

namespace App\Services\Version\User;

use App\Models\Agent\Agent;
use App\Models\Brand\Goods;
use App\Models\Orders\Entity as Orders;
use App\Models\Order\Entity as Order;
use App\Models\User\Entity as User;
use App\Models\ScoreLog;
use App\Models\Message;
use App\Models\Activity\Sign as ActivitySign;
use App\Models\Activity\Entity as Activity;
use App\Models\Activity\Entity\V020700 as ActivityV020700;
use App\Models\Live\Entity as Live;
use App\Models\News\Entity as News;
use App\Models\Video\Entity\V020700 as VideoV020700;
use App\Models\Brand\Entity\V020700 as BrandV020700;
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

class _v020700 extends _v020600
{

    /**
     * 我的订单列表
     */
    public function postMyorders($param)
    {
//        $list = parent::postMyorders($param);
        $perPage = (int)$param['request']->input('page_size', 10);
        $pageStart = (int)$param['request']->input('page', 1);

        $offSet = ($pageStart * $perPage) - $perPage;

        //获取订单类型  默认获取成功订单
        $is_complete = (int)$param['request']->input('is_complete', 1);
        //本月第一天的时间戳
        $this_month = strtotime(date('Y-m-01', strtotime(date("Y-m-d"))));
        //上个月第一天的时间戳
        $last_month = strtotime(date('Y-m-01', (strtotime(date('Y-m')) - 1)));
        //验证用户uid
        if (!User::find($param['uid'])) {
            return ['message' => '非法的uid', 'status' => false];
        };

        //成功订单
        if ($is_complete) {
            //orders表的订单
            $orders = Orders::with('orders_items')
                ->select('id', 'pay_way', 'pay_at', 'order_no', 'amount as price', 'created_at')
                ->where('uid', $param['uid'])
                ->where('status', 'pay')
                ->orderBy('created_at', 'desc')
                ->get()
                ->toArray();
            //orders表的订单
            $order = Order::with('ticket')
                ->select(
                    'id',
                    'ticket_id',
                    'order_no',
                    'pay_way',
                    'deadline',
                    'online_money',
                    'score_num as score_price',
                    'created_at',
                    'updated_at'
                )
                ->where('uid', $param['uid'])
                ->whereIn('status', ['1', '2'])
                ->orderBy('created_at', 'desc')
                ->get()
                ->toArray();
            $thisMonth = [];//本月订单
            $lastMonth = [];//上月订单
            $beforeOrder = [];//之前订单
//            dd($order);
            //取出orders表订单数据
            foreach ($orders as $k => $v) {
                if (empty($v['orders_items']['type'])) {
                    unset($v);
                } else {
                    $type = $v['orders_items']['type'];//商品类型
                    $product_id = $v['orders_items']['product_id'];
                    $title = Orders::getProduct($type, $product_id);
                    $v['name'] = $title['title'];//商品名称
                    $v['type'] = $title['type'];//商品类型
                    $v['order_type'] = $title['order_type'];//订单类型
                    $v['deadline'] = 0;//过期时间
                    $v['score_price'] = $v['orders_items']['score_price'];//积分形式价格
                    //支付方式
                    if ($v['pay_way'] == 'weixin') {
                        $v['pay_way'] = '微信';
                    } elseif ($v['pay_way'] == 'ali') {
                        $v['pay_way'] = '支付宝';
                    } else {
                        $v['pay_way'] = '积分';
                    }
                    //删除多余字段
                    unset($v['orders_items']);

                    //归纳数据
                    $order_time = $v['created_at'];//下单时间
                    if ($order_time >= $this_month) {
                        $thisMonth[] = $v;//本月
                    } elseif ($order_time < $this_month && $order_time >= $last_month) {
                        $lastMonth[] = $v;//上月
                    } else {
                        $beforeOrder[] = $v;//之前
                    }
                }
            }
            //取出order表订单数据
            foreach ($order as $k => $v) {
                $activity_id = $v['ticket']['activity_id'];//活动id
                $type = $v['ticket']['type'];//商品:门票类型
                if ($type == 1) {
                    $v['type'] = '活动';
                    $v['order_type'] = '活动门票';
                } else {
                    $v['type'] = '直播';
                    $v['order_type'] = '直播票';
                }
                //支付方式
                if ($v['pay_way'] == 'weixin') {
                    $v['pay_way'] = '微信';
                } elseif ($v['pay_way'] == 'ali') {
                    $v['pay_way'] = '支付宝';
                } else {
                    $v['pay_way'] = '积分';
                }
                //整理数据
                $v['name'] = Activity::where('id', $activity_id)->value('subject');
                $v['price'] = $v['online_money'];
                $v['pay_at'] = $v['ticket']['updated_at'];

                //删除多余字段
                unset($v['ticket'], $v['ticket_id']);

                $order_time = $v['created_at'];//下单时间
                if ($order_time >= $this_month) {
                    $thisMonth[] = $v;//本月
                } elseif ($order_time < $this_month && $order_time >= $last_month) {
                    $lastMonth[] = $v;//上月
                } else {
                    $beforeOrder[] = $v;//之前
                }
            }

            $thisMonth = muliterArraySortByfield($thisMonth, 'created_at', 'desc');//排序
            $lastMonth = muliterArraySortByfield($lastMonth, 'created_at', 'desc');//排序
            $beforeOrder = muliterArraySortByfield($beforeOrder, 'created_at', 'desc');//排序

            //分页返回数据
            $all['thisMonth'] = array_slice($thisMonth, $offSet, $perPage, true);
            $all['lastMonth'] = array_slice($lastMonth, $offSet, $perPage, true);
            $all['beforeOrder'] = array_slice($beforeOrder, $offSet, $perPage, true);
            //统计订单
            $all['count']['thisMonth'] = count($thisMonth);
            $all['count']['lastMonth'] = count($lastMonth);
            $all['count']['beforeOrder'] = count($beforeOrder);

            return ['message' => $all, 'status' => true];
        } else {

            //未成功的订单
            //orders表未成功的订单
            //有效时间内的订单
            $deadline = time() - 1800;
            $orders = Orders::with('orders_items')
                ->select('id', 'pay_way', 'order_no', 'pay_at', 'amount as price', 'created_at')
                ->where('uid', $param['uid'])
                ->where('status', 'npay')
                ->where('created_at', '>=', $deadline)
                ->orderBy('created_at', 'desc')
                ->get()
                ->toArray();
            //orders表未成功的订单
            $order = Order::with('ticket')
                ->select(
                    'id',
                    'ticket_id',
                    'order_no',
                    'pay_way',
                    'deadline',
                    'online_money',
                    'score_num as score_price',
                    'created_at',
                    'updated_at'
                )
                ->where('uid', $param['uid'])
                ->where('deadline', '>=', time())
                ->where('status', 0)
                ->orderBy('created_at', 'desc')
                ->get()
                ->toArray();
            $fail_order = [];
            //取出orders表订单数据
            foreach ($orders as $k => $v) {
                if (empty($v['orders_items']['type'])) {
                    unset($v);
                } else {
                    $type = $v['orders_items']['type'];//商品类型
                    $product_id = $v['orders_items']['product_id'];
                    $title = Orders::getProduct($type, $product_id);
                    $v['name'] = $title['title'];//商品名称
                    $v['type'] = $title['type'];//商品类型
                    $v['order_type'] = $title['order_type'];//订单类型
                    $v['deadline'] = 0;//过期时间
                    $v['score_price'] = $v['orders_items']['score_price'];//积分形式价格
                    //支付方式
                    if ($v['pay_way'] == 'weixin') {
                        $v['pay_way'] = '微信';
                    } elseif ($v['pay_way'] == 'ali') {
                        $v['pay_way'] = '支付宝';
                    } else {
                        $v['pay_way'] = '积分';
                    }

                    //删除多余字段
                    unset($v['orders_items']);
                    //归纳数据
                    $fail_order[] = $v;
                }
            }
            //取出order表订单数据
            foreach ($order as $k => $v) {
                $activity_id = $v['ticket']['activity_id'];//活动id
                $type = $v['ticket']['type'];//商品:门票类型
                if ($type == 1) {
                    $v['type'] = '活动';
                    $v['order_type'] = '活动门票';
                } else {
                    $v['type'] = '直播';
                    $v['order_type'] = '直播票';
                }
                //支付方式
                if ($v['pay_way'] == 'weixin') {
                    $v['pay_way'] = '微信';
                } elseif ($v['pay_way'] == 'ali') {
                    $v['pay_way'] = '支付宝';
                } else {
                    $v['pay_way'] = '积分';
                }
                //整理数据
                $v['name'] = Activity::where('id', $activity_id)->value('subject');
                $v['price'] = $v['online_money'];
                $v['pay_at'] = $v['ticket']['updated_at'];

                //删除多余字段
                unset($v['ticket'], $v['ticket_id']);

                $fail_order[] = $v;
            }

            //统计订单
            $data['count']['fail'] = count($fail_order);

            $fail = muliterArraySortByfield($fail_order, 'created_at', 'desc');//排序

            //分页返回数据
            $data['fail_order'] = array_slice($fail, $offSet, $perPage, true);

            return ['message' => $data, 'status' => true];
        }
    }

    /**
     * 订单详情
     */
    public function postMyorderinfo($param)
    {
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
                    'agent_username' => getRealTel($orderInfos['has_one_orders_items']['belongs_to_invitation']['belongs_to_agent']['non_reversible'] , 'agent'),
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
                $orderInfos = Orders::with('hasOneOrdersItems.belongsToContract.brand',
                    'hasOneOrdersItems.belongsToContract.invitation',
                    'hasOneOrdersItems.belongsToContract.red_packet',
                    'hasOneOrdersItems.belongsToContract.agent.hasOneZone',
                    'hasOneOrdersItems.belongsToContract.admin'
                )
                ->with(['hasOneOrdersItems'=>function($query){
                    $query->where('type','contract');
                }])
                ->where('id',$order_id)->first()->toArray();
                //总金额
                $amount =  floatval($orderInfos['has_one_orders_items']['belongs_to_contract']['amount']);
//                实际支付
                $actual_pay = floatval($orderInfos['amount']);
                //首付相关
                //首付、订金、基金
                $perPay = floatval($orderInfos['has_one_orders_items']['belongs_to_contract']['pre_pay']);
                $invitationStatus = trim($orderInfos['has_one_orders_items']['belongs_to_contract']['invitation']['status']);
                $deduction = 0;
                $invitationStatus == 2 && $deduction = floatval($orderInfos['has_one_orders_items']['belongs_to_contract']['invitation']['default_money']);
                $fundStatus = trim($orderInfos['has_one_orders_items']['belongs_to_contract']['red_packet']['status']);
                $fund = 0;
                $fundStatus == '1' && $fund = floatval($orderInfos['has_one_orders_items']['belongs_to_contract']['red_packet']['amount']);
                $firstShouldPay = floatval($perPay - $deduction - $fund);
                $firstStatus = '';
                $status = trim($orderInfos['status']);
                switch ($status){
                    case 'pay': $firstStatus= '已支付';break;
                    case 'npay': $firstStatus= '未支付';break;
                    case 'expire': $firstStatus= '已过期';break;
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
                //尾款相关
                $finalPay = floatval($amount - $perPay);
                $contractStatus = trim($orderInfos['has_one_orders_items']['belongs_to_contract']['status']);
                $finalStatus = '未结清';
                $contractStatus == 2 && $finalStatus = '已结清';

                $finalAccountInfo = '';

                $finalCustomerCardNo = trim($orderInfos['has_one_orders_items']['belongs_to_contract']['bank_no']);
                $finalCustomeCardName = trim($orderInfos['has_one_orders_items']['belongs_to_contract']['customer_bank_name']);
                $finalCustomerCardNo && $finalAccountInfo = bankFormat($finalCustomerCardNo).'('.$finalCustomeCardName.')';

                //判断对该经纪人是否公开手机号
                $agentId = trim($orderInfos['has_one_orders_items']['belongs_to_contract']['agent']['id']);
                $uid = trim($orderInfos['uid']);
                $agentCustomerInfo = AgentCustomer::where(function($query)use($agentId,$uid){
                    $query->where('agent_id',$agentId);
                    $query->where('uid',$uid);
                })->first();
                //支付时间格式化
                $preTime = trim($orderInfos['pay_at']);
                $finalTime = trim($orderInfos['has_one_orders_items']['belongs_to_contract']['tail_pay_at']);
                $preTime && $preTime = trim(date('Y/m/d H:i:s',$orderInfos['pay_at']));
                $finalTime && $finalTime = trim(date('Y/m/d H:i:s',$orderInfos['has_one_orders_items']['belongs_to_contract']['tail_pay_at']));
                $arr = [];
                $arr = array(
                    'tel_public' => trim($agentCustomerInfo['has_tel']),
                    'type_name'=> '付款协议',
                    'type'=> 'contract',
                    'contract_name'=> trim($orderInfos['has_one_orders_items']['belongs_to_contract']['name']),
                    'contract_id'=> trim($orderInfos['has_one_orders_items']['belongs_to_contract']['id']),
                    'contract_no'=> trim($orderInfos['has_one_orders_items']['belongs_to_contract']['contract_no']),
                    'contract_addr'=> trim($orderInfos['has_one_orders_items']['belongs_to_contract']['address']),
                    'brand_title'=> trim($orderInfos['has_one_orders_items']['belongs_to_contract']['brand']['name']),
                    'goodsName'=> trim($orderInfos['has_one_orders_items']['belongs_to_contract']['brand']['name']),
                    'amount'=> '¥ '.doFormatMoney(floatval($amount)),
                    'actual_pay' => '¥ '.doFormatMoney(floatval($actual_pay)),
                    'agent_avatar'=> getImage($orderInfos['has_one_orders_items']['belongs_to_contract']['agent']['avatar'],'avatar',''),
                    'agent_nickname' => Agent::unifiHandleName($orderInfos['has_one_orders_items']['belongs_to_contract']['agent']),
                    'agent_id' => trim($orderInfos['has_one_orders_items']['belongs_to_contract']['agent']['id']),
                    'agent_gender' => trim($orderInfos['has_one_orders_items']['belongs_to_contract']['agent']['gender']),
                    'agent_city' => trim($orderInfos['has_one_orders_items']['belongs_to_contract']['agent']['has_one_zone']['name']),
                    'agent_username' => getRealTel($orderInfos['has_one_orders_items']['belongs_to_contract']['agent']['non_reversible'] , 'agent'),
                    'first_pay' => '¥ '.doFormatMoney($perPay),
                    'deduction'=> '-¥ '.doFormatMoney($deduction),
                    'funt'=> '-¥ '.doFormatMoney($fund),
                    'first_should_pay'=> '¥ '.doFormatMoney($firstShouldPay),
                    'first_status' => $firstStatus,
                    'first_pay_way' => $payWayStr,
                    'first_account_info' => trim($orderInfos['buyer_id']),
                    'first_pay_at' => $preTime,
                    'final_pay'=> '¥ '.doFormatMoney($finalPay),
                    'final_should_pay'=> '¥ '.doFormatMoney($finalPay),
                    'final_status'=> trim($finalStatus),
                    'final_account_info'=> trim($finalAccountInfo),
                    'die_time'=> trim(date('n月j日',strtotime('+30 days',$orderInfos['created_at']))),
//                    'final_pay_at'=> trim($orderInfos['has_one_orders_items']['belongs_to_contract']['tail_pay_at']),
                    'final_pay_at'=> $finalTime,
                    'companyBankNo'=> config('bank.company_bank_no'),
                    'companyBankName'=> config('bank.company_bank_name'),
                    'companyName'=> config('bank.company_name'),
                    'affirm'=> trim($orderInfos['has_one_orders_items']['belongs_to_contract']['admin']['nickname']),
                );
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
    * 用户额外信息
    */
    public function postUserinfoext($param)
    {
        $uid = $param['uid'];

        if (!($user = User::where('uid', $uid)->first())) {
            return ['message' => '非法的uid', 'status' => false];
        }

        //无界币
        $return['currency'] = $user->currency;
        //无界币
        $return['score'] = $user->score;

        //分享次数
        $share_currency_count = \DB::table('currency_log')
            ->where('uid', $uid)
            ->whereIn('action', ['share_distribution', 'relay_distribution'])
            ->count();

        $share_score_count = \DB::table('score_log')
            ->where('uid', $uid)
            ->whereIn('type', ['share_distribution', 'relay_distribution'])
            ->count();

        $return['share_count'] = $share_currency_count + $share_score_count;

        //阅读量
        $currency_reward_count = \DB::table('currency_log')
            ->where('uid', $uid)
            ->where('action', 'view_distribution')
            ->count();

        $score_reward_count = \DB::table('score_log')
            ->where('uid', $uid)
            ->where('type', 'view_distribution')
            ->count();

        $return['read_count'] = $currency_reward_count + $score_reward_count;

        //意向客户
        $intend_brand_ids = \DB::table('distribution_log as a')
            ->where('a.uid', $uid)
            ->where('a.relation_type', 'brand')
            ->where('a.genus_type', 'intent')
            ->select('a.id')
            ->get();

        $return['intend_count'] = count($intend_brand_ids);

        //累计佣金
        $currency = \DB::table('currency_log')
            ->where('uid', $uid)
            ->where('operation', 1)
            ->whereIn(
                'action',
                [
                    'share_distribution',
                    'relay_distribution',
                    'relay_distribution',
                    'watch_distribution',
                    'enroll_distribution',
                    'sign_distribution',
                    'view_distribution',
                    'intent_distribution'
                ]
            )
            ->sum('num');

        $return['currency_total'] = $currency;

        //是否已填写邀请码 1 :是 0 :否
        $return['is_done_invitecode'] = $user->register_invite ? 1 : 0;
        $unReadCount = Message::unReadcounts($uid);
        $return['unread_messages'] = $unReadCount;

        //有多少人填写了我的邀请码
        $return['invite_count'] = User::where('register_invite', $user->my_invite)->count();

        //今天是否签到
        $return['is_sign'] = ScoreLog::typeCount($user->uid, 'user_sign', date('Y-m-d')) ||
        ScoreLog::typeCount($user->uid, 'user_sign_first', date('Y-m-d')) ? 1 : 0;
        if ($user->serial_sign > 0 && !ScoreLog::typeCount($user->uid, 'user_sign', date('Y-m-d', time() - 86400))
            && !ScoreLog::typeCount($user->uid, 'user_sign_first', date('Y-m-d', time() - 86400))
        ) {//未连续
            $user->update(['serial_sign' => 0]);
        }

        //连续签到几次
        $return['serial_sign'] = $user->serial_sign;
        //本次签到赠送
        if ($user->serial_sign >= 30) {
            $return['sign_score'] = 100;
        } else {
            $return['sign_score'] = ScoreLog::typeCount($user->uid, 'user_sign') == 0 ? 10 : min($user->serial_sign * 5 + 5, 150);
        }
        //已经提取佣金
        $return['extracted'] = \App\Models\User\Withdraw::where('uid', $uid)->where('status', '!=', 'fail')
            ->sum(\DB::raw('if(status="pending",money,actual)'));

        //累计邀请好友报名
        $return['invite_sign_count'] = ActivitySign::inviteSign($uid);

        return ['message' => $return, 'status' => true];
    }

    /**
     * 积分明细
     */
    public function postScorelist($data)
    {
        if (empty($data['page'])) {
            return ['message' => 'page参数必须', 'status' => false];
        }

        if (empty($data['uid'])) {
            return ['message' => 'uid参数必须', 'status' => false];
        }
        //当前积分
        $user = User::where('uid', $data['uid'])->first();
        //总累计收入
        $total_income = ScoreLog::where('uid', $data['uid'])->where('operation', 1)->sum('num');
        //总累计支出
        $total_pay = ScoreLog::where('uid', $data['uid'])->where('operation', -1)->sum('num');

        //获取上个月1号凌晨的时间戳 5-1
        $m = date('m') - 2 * ($data['page'] - 1);
        $first = mktime(0, 0, 0, $m + 1, 1, date('Y'));
        $second = mktime(0, 0, 0, $m, 1, date('Y'));
        $third = mktime(0, 0, 0, $m - 1, 1, date('Y'));
//        $first_month = $m . '月';
        $m == date('m') && $first_month = '本月';
//        $second_month = ($m - 1) . '月';

        $user = User::where('uid', $data['uid'])->first();
        //到用户注册日就可以了
        if ($user->created_at->timestamp > $first) {
            return ['message' => '没有数据了', 'status' => false];
        }

        $score_list = [];
        $score_list[] = $this->getScoreByMonth($second, $first, $data['uid']);
        $score_list[] = $this->getScoreByMonth($third, $second, $data['uid']);

        $message = ['now_score' => $user->score, 'total_income' => $total_income, 'total_pay' => $total_pay, 'score_list' => $score_list];

        return ['message' => $message, 'status' => true];
    }

    public function getScoreByMonth($start, $end, $uid)
    {
        $month_name = date('m', $start) . '月';
        if (date('m', $start) == date('m') && date('Y', $start) == date('Y')) {
            $month_name = '本月';
        }
        if (date('m', $start) == 1) {
            $month_name = date('Y', $start) . '年' . $month_name;
        }

        //本月收入
        $income = ScoreLog::
        where('uid', $uid)->
        where('operation', 1)->whereBetween('created_at', [$start, $end])->sum('num');
        //本月支出
        $pay = ScoreLog::where('uid', $uid)->where('operation', -1)->whereBetween('created_at', [$start, $end])->sum('num');

        $first = ScoreLog::
        where('uid', $uid)->
        whereNotIn(
            'type',
            [
                'share_distribution',
                'relay_distribution',
                'enroll_distribution',
                'sign_distribution'
                ,
                'watch_distribution',
                'view_distribution',
                'intent_distribution'
            ]
        )
            ->select('operation', 'created_at', 'type', 'relation_id', 'id', 'num')
            ->whereBetween('created_at', [$start, $end])->orderBy('created_at', 'desc')->get();
        $second = ScoreLog::
        where('uid', $uid)
            ->where('created_at', '>=', $start)
            ->where('created_at', '<', $end)
            ->whereIn(
                'type',
                [
                    'share_distribution',
                    'relay_distribution',
                    'enroll_distribution',
                    'sign_distribution',
                    'watch_distribution',
                    'view_distribution',
                    'intent_distribution'
                ]
            )
            ->select(\DB::raw('"1" operation, count(*) as count_num, created_at, relation_type, relation_id,  id, type, sum(num) as sum_num'))
            ->addSelect(\DB::raw('from_unixtime(created_at,"%m-%d") as day'))
            ->groupBy('type')
            ->groupBy('day')
            ->groupBy('relation_type')
            ->groupBy('relation_id')
            ->get();

        $list = collect($first)->merge(collect($second));

        $list = $list
            ->sortBy(
                function ($item, $key) {
                    return 0 - $item->created_at->timestamp;
                }
            );

        $list->transform(
            function ($item, $key) {
                //类型 加减
                //时间
                $item->created_at_formart = date('m-d', $item->created_at->timestamp);
                $d = date('d', $item->created_at->timestamp);
                $m = date('m', $item->created_at->timestamp);
                $y = date('Y', $item->created_at->timestamp);
                if ((date('d') - $d) === 0 && (date('m') - $m) === 0 && (date('Y') - $y === 0)) {
                    $item->created_at_formart = '今天';
                }

                if ((date('d') - $d) === 1 && (date('m') - $m) === 0 && (date('Y') - $y === 0)) {
                    $item->created_at_formart = '昨天';
                }

                if ((date('d') - $d) === 2 && (date('m') - $m) === 0 && (date('Y') - $y === 0)) {
                    $item->created_at_formart = '前天';
                }

                //积分数

                //1、分享赚佣 - 分享奖励：针对首次分享的奖励。50积分
                if ($item->type == 'share_distribution') {
                    //小标题
                    $item->summary = '分享赚佣 - 分享奖励';
                    $arr = ['news' => '资讯', 'live' => '直播', 'video' => '视频', 'activity' => '活动', 'brand' => '品牌'];
                    isset($arr[$item->relation_type]) ? $type = $arr[$item->relation_type] : $type = '';
                    $title = $this->getTargetTitle($item->relation_type, $item->relation_id);
                    $item->title = '分享' . $type . $title . ',当天分享' . $item->count_num . '次';
                    $item->score_num = $item->sum_num;
                    unset($item->sum_num, $item->type, $item->relation_type, $item->relation_id, $item->day, $item->created_at, $item->count_num);
                } //2、分享赚佣 - 阅读奖励：针对每个分享条目阅读量的奖励。10积分/次
                elseif ($item->type == 'view_distribution') {
                    //小标题
                    $item->summary = '分享赚佣 - 阅读奖励';
                    //大标题 'live','video','brand','news','activity'
                    $arr = ['news' => '资讯', 'live' => '直播', 'video' => '视频', 'activity' => '活动', 'brand' => '品牌'];
                    isset($arr[$item->relation_type]) ? $type = $arr[$item->relation_type] : $type = '';
                    $title = $this->getTargetTitle($item->relation_type, $item->relation_id);
                    $item->title = '分享' . $type . $title . ',当天新增' . $item->count_num . '次阅读';
                    $item->score_num = $item->sum_num;
                    unset($item->sum_num, $item->type, $item->relation_type, $item->relation_id, $item->day, $item->created_at, $item->count_num);
                } //3、分享赚佣 - 观看奖励：针对直播/录播的观看奖励。50积分/次
                elseif ($item->type == 'watch_distribution') {
                    //小标题
                    $item->summary = '分享赚佣 - 观看奖励';
                    //大标题 'live','video','brand','news','activity'
                    $arr = ['news' => '资讯', 'live' => '直播', 'video' => '视频', 'activity' => '活动', 'brand' => '品牌'];
                    isset($arr[$item->relation_type]) ? $type = $arr[$item->relation_type] : $type = '';
                    $title = $this->getTargetTitle($item->relation_type, $item->relation_id);
                    $item->title = '分享' . $type . $title . ',当天新增' . $item->count_num . '次观看';
                    $item->score_num = $item->sum_num;
                    unset($item->sum_num, $item->type, $item->relation_type, $item->relation_id, $item->day, $item->created_at, $item->count_num);
                } //转发奖励
                elseif ($item->type == 'relay_distribution') {
                    //小标题
                    $item->summary = '分享赚佣 - 转发奖励';
                    //大标题 'live','video','brand','news','activity'
                    $arr = ['news' => '资讯', 'live' => '直播', 'video' => '视频', 'activity' => '活动', 'brand' => '品牌'];
                    isset($arr[$item->relation_type]) ? $type = $arr[$item->relation_type] : $type = '';
                    $title = $this->getTargetTitle($item->relation_type, $item->relation_id);
                    $item->title = '分享' . $type . $title . ',当天新增' . $item->count_num . '次转发';
                    $item->score_num = $item->sum_num;
                    unset($item->sum_num, $item->type, $item->relation_type, $item->relation_id, $item->day, $item->created_at, $item->count_num);
                } //报名奖励
                elseif ($item->type == 'apply_distribution') {
                    //小标题
                    $item->summary = '分享赚佣 - 报名奖励';
                    //大标题 'live','video','brand','news','activity'
                    $arr = ['news' => '资讯', 'live' => '直播', 'video' => '视频', 'activity' => '活动', 'brand' => '品牌'];
                    isset($arr[$item->relation_type]) ? $type = $arr[$item->relation_type] : $type = '';
                    $title = $this->getTargetTitle($item->relation_type, $item->relation_id);
                    $item->title = '分享' . $type . $title . ',当天新增' . $item->count_num . '次报名';
                    $item->score_num = $item->sum_num;
                    unset($item->sum_num, $item->type, $item->relation_type, $item->relation_id, $item->day, $item->created_at, $item->count_num);
                } //签到奖励
                elseif ($item->type == 'sign_distribution') {
                    //小标题
                    $item->summary = '分享赚佣 - 转发奖励';
                    //大标题 'live','video','brand','news','activity'
                    $arr = ['news' => '资讯', 'live' => '直播', 'video' => '视频', 'activity' => '活动', 'brand' => '品牌'];
                    isset($arr[$item->relation_type]) ? $type = $arr[$item->relation_type] : $type = '';
                    $title = $this->getTargetTitle($item->relation_type, $item->relation_id);
                    $item->title = '分享' . $type . $title . ',当天新增' . $item->count_num . '次签到';
                    $item->score_num = $item->sum_num;
                    unset($item->sum_num, $item->type, $item->relation_type, $item->relation_id, $item->day, $item->created_at, $item->count_num);
                } //品牌意向加盟
                elseif ($item->type == 'intent_distribution') {
                    //小标题
                    $item->summary = '分享赚佣 - 转发奖励';
                    //大标题 'live','video','brand','news','activity'
                    $arr = ['news' => '资讯', 'live' => '直播', 'video' => '视频', 'activity' => '活动', 'brand' => '品牌'];
                    isset($arr[$item->relation_type]) ? $type = $arr[$item->relation_type] : $type = '';
                    $title = $this->getTargetTitle($item->relation_type, $item->relation_id);
                    $item->title = '分享' . $type . $title . ',当天新增' . $item->count_num . '次品牌意向加盟';
                    $item->score_num = $item->sum_num;
                    unset($item->sum_num, $item->type, $item->relation_type, $item->relation_id, $item->day, $item->created_at, $item->count_num);
                } //4、应用签到领取积分 ：日常签到，或签到额外奖励都归到这类
                elseif ($item->type == 'user_sign') {
                    //小标题
                    $item->summary = '应用签到领取积分';
                    //大标题 'live','video','brand','news','activity'
                    $item->title = '应用签到领取积分';
                    $item->score_num = $item->num;
                    unset($item->created_at, $item->type, $item->relation_id, $item->num);
                } //5、线上活动积分消耗：参与类似抽奖等线上活动
                elseif ($item->type == 'lottery') {
                    //小标题
                    $item->summary = '线上活动积分消耗';
                    //大标题
                    $item->title = '参与线上抽奖';
                    $item->score_num = $item->num;
                    unset($item->created_at, $item->type, $item->relation_id, $item->num);
                } //6、直播场次支付：购买直播票
                elseif ($item->type == 'live_ticket_buy') {
                    //小标题
                    $item->summary = '直播场次支付';
                    //大标题
                    $title = $this->getTargetTitle('live', $item->relation_id);
                    $item->title = '购买直播' . $title . '直播门票';
                    $item->score_num = $item->num;
                    unset($item->created_at, $item->type, $item->relation_id, $item->num);
                } //7、录播观看支付：购买录播票
                elseif ($item->type == 'video_buy') {
                    //小标题
                    $item->summary = '录播支付';
                    //大标题
                    $title = $this->getTargetTitle('video', $item->relation_id);
                    $item->title = '购买录播' . $title;
                    $item->score_num = $item->num;
                    unset($item->created_at, $item->type, $item->relation_id, $item->num);
                } //8、资讯阅读支付：购买咨询阅读
                elseif ($item->type == 'news_buy') {
                    //小标题
                    $item->summary = '资讯购买支付';
                    //大标题
                    $title = $this->getTargetTitle('news', $item->relation_id);
                    $item->title = '购买资讯' . $title;
                    $item->score_num = $item->num;
                    unset($item->created_at, $item->type, $item->relation_id, $item->num);
                } //9、活动门票支付：购买活动门票
                elseif ($item->type == 'site_ticket_buy') {
                    //小标题
                    $item->summary = '活动门票支付';
                    //大标题
                    $title = $this->getTargetTitle('activity', $item->relation_id);
                    $item->title = '购买活动' . $title . '现场门票';
                    $item->score_num = $item->num;
                    unset($item->created_at, $item->type, $item->relation_id, $item->num);
                } //10、积分商城兑换：于积分商城进行相应的商品兑换
                elseif ($item->type == 'duiba_pay') {
                    //小标题
                    $item->summary = '积分商城兑换';
                    //大标题
                    $item->title = '积分商城兑换';
                    $item->score_num = $item->num;
                    unset($item->created_at, $item->type, $item->relation_id, $item->num);
                } //11、积分充值入账：对于通过支付宝、微信进行积分充值记录。
                elseif ($item->type == 'score_buy') {
                    //小标题
                    $item->summary = '积分充值';
                    //大标题
                    $item->title = '积分充值';
                    $item->score_num = $item->num;
                    unset($item->created_at, $item->type, $item->relation_id, $item->num);
                } //13 之前没有区分开的门票购买
                elseif ($item->type == 'ticket_buy') {
                    //小标题
                    $item->summary = '活动门票支付';
                    //大标题
                    $title = $this->getTargetTitle('activity', $item->relation_id);
                    $item->title = '购买活动' . $title . '门票';
                    $item->score_num = $item->num;
                    unset($item->created_at, $item->type, $item->relation_id, $item->num);
                } else {
                    //小标题
                    $item->summary = ScoreLog::getType($item->type);
                    //大标题
                    $item->title = ScoreLog::getType($item->type);
                    $item->score_num = $item->num;
                    unset($item->created_at, $item->type, $item->relation_id, $item->num);
                }

                //12、其他：无法纳入到以上11种情况的积分支出收入，则暂算为其他

                return $item;
            }
        );

        return ['month_name' => $month_name, 'income' => $income, 'pay' => $pay, 'list' => $list];
    }

    public function getTargetTitle($type, $id)
    {
        switch ($type) {
            case 'activity':
                $target = Activity::where('id', $id)->first();
                is_object($target) ? $title = $target->subject : $title = '';
                break;
            case 'live':
                $target = live::where('id', $id)->first();
                is_object($target) ? $title = $target->subject : $title = '';
                break;
            case 'news':
                $target = News::where('id', $id)->first();
                is_object($target) ? $title = $target->title : $title = '';

                break;
            case 'video':
                $target = VideoV020700::where('id', $id)->first();
                is_object($target) ? $title = $target->subject : $title = '';
                break;
            case 'brand':
                $target = BrandV020700::where('id', $id)->first();
                is_object($target) ? $title = $target->name : $title = '';
                break;
            default:
                $title = '';
        }

        if ($title != '') {
            return '"' . cut_str($title, 10) . '"';
        } else {
            return $title;
        }
    }

    //签到
    public function postSign($data)
    {
        $uid = $data['uid'];
        if (!($user = User::where('uid', $uid)->first())) {
            return ['message' => '非法的uid', 'status' => false];
        }

        $is_sign = (bool)ScoreLog::typeCount($user->uid, 'user_sign', date('Y-m-d'))
            || (bool)ScoreLog::typeCount($user->uid, 'user_sign_first', date('Y-m-d'));

        if (!$is_sign) {
            $serial_sign = $user->serial_sign + 1;
            //首次
            if (!ScoreLog::typeCount($user->uid, 'user_sign') && !ScoreLog::typeCount($user->uid, 'user_sign_first')) {
                $num = 10;
                $msg = '首次签到成功';
                $sign_type = 'user_sign_first';
            } elseif (ScoreLog::typeCount($user->uid, 'user_sign', date('Y-m-d', time() - 86400))) {//连续
                $num = min($serial_sign * 5, 150);
                if ($serial_sign > 30) {
                    $num = 100;
                }
                $msg = '第' . $serial_sign . '次签到成功';
                $sign_type = 'user_sign';
            } else {
                $num = 5;
                $msg = '签到成功';
                $serial_sign = 1;
                $sign_type = 'user_sign';
            }

            if (!User::where('uid', $uid)
                ->where('serial_sign', '=', $user->serial_sign)
                ->update(['serial_sign' => $serial_sign])
            ) {
                return ['message' => '签到失败', 'status' => false];
            }
            //赠送积分
            ScoreLog::add($user->uid, $num, $sign_type, $msg);

            $reward_num = 0;
            switch ($serial_sign) {//额外赠送
                case 7:
                    $reward_num = 20;
                    break;
                case 15:
                    $reward_num = 50;
                    send_notification('这么勤快，额外再赠送50积分！', '好腻害！已经连续签到15天了，去积分商城看看吧！', json_encode(['type' => 'duiba']), $user);
                    break;
                case 30:
                    $reward_num = 100;
                    break;
            }

            $reward_num > 0 && ScoreLog::add($user->uid, $reward_num, 'user_sign_reward', '连续签到' . $serial_sign . '天额外奖励');

            //已连续签到

            //今天已得
            $today_score = $reward_num + $num;

            //明天可得
            $tomorrow_score = $this->countScore($serial_sign + 1, $uid);

            //前面的天数
            $lists = [];
            $fore_begin = min(3, $serial_sign - 1);
            for ($i = $fore_begin; $i > 0; $i--) {
                //积分
                $item['score'] = $this->countScore($serial_sign - $i, $uid, time() - 3600 * 24 * $i);
                //日期
                $item['day'] = date('m.d', time() - 3600 * 24 * $i);
                //颜色
                $item['color'] = 'red';
                $lists[] = $item;
            }

            $lists[] = ['score' => $reward_num + $num, 'day' => '今天', 'color' => 'red'];

            //后面的天数  [1,2,3,4]
            $back_begin = max(3, 7 - $serial_sign);
            for ($i = 1; $i <= $back_begin; $i++) {
                //积分
                $item['score'] = $this->countScore($serial_sign + $i, $uid);
                //日期
                $item['day'] = date('m.d', time() + 3600 * 24 * $i);
                $i == 1 && $item['day'] = '明天';
                //颜色
                $item['color'] = 'gray';
                $lists[] = $item;
            }


        }else{
            $serial_sign = $user->serial_sign;

            $today_score =$this->countScore($serial_sign, $uid, time());
            //明天可得
            $tomorrow_score = $this->countScore($serial_sign + 1, $uid);

            //前面的天数
            $lists = [];
            $fore_begin = min(3, $serial_sign - 1);
            for ($i = $fore_begin; $i > 0; $i--) {
                //积分
                $item['score'] = $this->countScore($serial_sign - $i, $uid, time() - 3600 * 24 * $i);
                //日期
                $item['day'] = date('m.d', time() - 3600 * 24 * $i);
                //颜色
                $item['color'] = 'red';
                $lists[] = $item;
            }

            $lists[] = ['score' => $today_score, 'day' => '今天', 'color' => 'red'];

            //后面的天数  [1,2,3,4]
            $back_begin = max(3, 7 - $serial_sign);
            for ($i = 1; $i <= $back_begin; $i++) {
                //积分
                $item['score'] = $this->countScore($serial_sign + $i, $uid);
                //日期
                $item['day'] = date('m.d', time() + 3600 * 24 * $i);
                $i == 1 && $item['day'] = '明天';
                //颜色
                $item['color'] = 'gray';
                $lists[] = $item;
            }
        }


        $message = [
            'serial_sign'    => $serial_sign,
            'today_score'    => $today_score,
            'tomorrow_score' => $tomorrow_score,
            'lists'          => $lists
        ];

        return ['message' => $message, 'status' => true];
    }

    /**
     * 算出连续第几天签到应该获得的积分
     */
    public function countScore($serial_sign, $uid, $stamp = 0)
    {
        if ($serial_sign == 1) {
            $num = 5;
            //判断是不是首次   之前首次签到也存了user_sign
            if ($stamp) {
                $stamp = mktime(0, 0, 0, date('m', $stamp), (date('d', $stamp) + 1), date('Y'));
                $sign_count = ScoreLog::where('uid', $uid)
                    ->whereIn('type', ['user_sign', 'user_sign_first'])
                    ->where('created_at', '<', $stamp)
                    ->count();
                $sign_count == 1 && $num = 10;
            }
        } else {
            $num = min($serial_sign * 5, 150);
            if ($serial_sign > 30) {
                $num = 100;
            }
        }

        switch ($serial_sign) {//额外赠送
            case 7:
                $reward_num = 20;
                break;
            case 15:
                $reward_num = 50;
                break;
            case 30:
                $reward_num = 100;
                break;
            default:
                $reward_num = 0;
                break;
        }

        return $num + $reward_num;
    }

}

