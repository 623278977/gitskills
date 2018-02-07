<?php

namespace App\Services\Version\Order;

use App\Exceptions\ExecuteException;
use App\Http\Requests\Order\OrderSignRequest;
use App\Models\Agent\AgentCustomer;
use App\Services\Version\VersionSelect;
use App\Models\Activity\Entity as Activity;
use App\Models\Activity\Sign;
use App\Models\ScoreLog;
use App\Models\User\Ticket;
use Validator;
use App\Models\Orders\Entity\V020700 as OrdersV020700;
use App\Models\Order\Entity;
use App\Models\User\Entity as UserEntity;
use App\Models\Orders\Items as Items;

class _v020700 extends _v020500
{

    //允许购买的商品类型
    protected $good_types = [
        'brand',
        'score',
        'brand_goods',
        'video_reward',
        'live_reward',
        'inspect_invite',
        'contract'
    ];


    //static $enable = FALSE;  //版本是否启用
    public function postOrderAndSign($data)
    {

//        $data['items'] = [
//            ['type' => 'contract', 'product_id' => 68, 'price' => 2990.00, 'num' => 1, 'fund_id' => 151],
//        ];
//
//        $data['items'] = [
//            ['type' => 'inspect_invite', 'product_id' => 20, 'price' => '3,000', 'num' => 1],
//        ];

        $types = array_unique(array_pluck($data['items'], 'type'));

        if (array_diff($types, $this->good_types)) {
            return ['message' => '商品类型不在允许的范围内', 'status' => false];
        }



        //去掉千分位
        $data['amount'] = abondonComma($data['amount']);
        if($data['amount']<=0){
            $data['amount'] = 0.01;
        }


        //下单
        $orders = OrdersV020700::place(
            $data['uid'],
            $data['amount'],
            $data['items'],
            $data['pay_way'],
            0,
            0,
            $data['amount'],
            'npay',
            $data['mobile'],
            $data['realname'],
            $data['zone_id'],
            $data['address']
        );

        if(!$orders){
            return ['message' => '异常，无法支付', 'status' => false];
        }


        //签名
        $str = OrdersV020700::sign($orders->order_no, $data['pay_way']);


        ScoreLog::add($data['uid'], $data['score_num'], $data['order_type'], $data['order_type_des'], -1, false, 'orders', $orders->id);

        if ($str == -1) {
            return ['message' => ['str' => '订单号和支付方式是必传参数', 'order_no' => $orders->order_no], 'status' => false];
        } elseif ($str == -2) {
            return ['message' => ['str' => '支付方式只能为ali,weixin或unionpay', 'order_no' => $orders->order_no], 'status' => false];
        } elseif ($str == -3) {
            return ['message' => ['str' => '不存在该订单', 'order_no' => $orders->order_no], 'status' => false];
        } elseif ($str == -4) {
            return ['message' => ['str' => '获取prepay_id失败', 'order_no' => $orders->order_no], 'status' => false];
        }

        return ['message' => ['str' => $str, 'order_no' => $orders->order_no], 'status' => true];
    }

    public function postOrderAndPay($data)
    {
        $types = array_unique(array_pluck($data['items'], 'type'));
        if (array_diff($types, ['news', 'video'])) {
            return ['message' => '商品类型只允许为news或者video', 'status' => false];
        }

        $user = UserEntity::where('uid', $data['uid'])->first();
        if ($user->score < $data['score_num']) {
            return ['message' => '当前用户积分不足', 'status' => false];
        }

        if (!$data['score_num']) {
            return ['message' => 'score_num为必传参数', 'status' => false];
        }


        empty($data['mobile']) && $data['mobile'] = '';
        empty($data['realname']) && $data['realname'] = '';
        empty($data['address']) && $data['address'] = '';
        empty($data['zone_id']) && $data['zone_id'] = 0;

        //开始事务
        \DB::beginTransaction();
        try {
            $rate = config('system.score_rate');

//            $data['items'] = [
//                ['type' => 'video', 'product_id' => 1, 'score_price' => 50, 'num' => 1],
//            ];

            //下单 购买项填数据
            $orders = OrdersV020700::place(
                $data['uid'],
                $data['score_num'] / $rate,
                $data['items'],
                'score',
                $data['score_num'],
                $data['score_num'] / $rate,
                0,
                'npay',
                $data['mobile'],
                $data['realname'],
                $data['zone_id'],
                $data['address']
            );


            //改变订单状态
            OrdersV020700::where('id', $orders->id)->update(['status' => 'pay', 'pay_at' => time()]);
            //改变购买项状态
            Items::where('order_id', $orders->id)->update(['status' => 'pay']);

            \DB::commit();
        } catch (\Exception $e) {
            \DB::rollBack();
            throw(new ExecuteException('支付失败，出现异常' . $e->getMessage()));
        }

        return ['message' => '支付成功', 'status' => true];
    }

}