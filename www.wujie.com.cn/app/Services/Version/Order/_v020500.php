<?php

namespace App\Services\Version\Order;

use App\Http\Requests\Order\OrderSignRequest;
use App\Services\OrderService;
use App\Services\Version\VersionSelect;
use App\Models\Activity\Entity as Activity;
use App\Models\Activity\Sign;
use App\Models\ScoreLog;
use App\Models\User\Ticket;
use Validator;
use App\Models\Orders\Entity as Orders;
use App\Models\Order\Entity;
use \DB;

class _v020500 extends _v020400
{
    /**
     * 继续支付
     */
    public function postContinuePay($data)
    {
        $info = $this->identifyTable($data['order_no']);
        $orderservice = new OrderService();
        //识别使用的是order还是orders表
        if (!$info) {
            return ['message' => '找不到该订单', 'status' => false];
        }
        if ($info['type'] == 'order' && ($info['order']->created_at + 1800) < time()) {
            return ['message' => '该订单已经过期了，不能调用该接口', 'status' => false];
        }

        if ($info['order']->status != 'npay') {
            return ['message' => '该订单已经不能支付了', 'status' => false];
        }

        if ($info['type'] == 'orders') {
            //签名 生成str
            $str = Orders::sign($info['order']->order_no, $data['pay_way'], 1);
        } else {
            DB::table('order')->where('order_no', $data['order_no'])->update(['pay_way' => $data['pay_way']]);

            if ($data['pay_way'] == 'ali') {
                $str = $orderservice->aliSign($data['order_no'], $info['order']->product, $info['order']->online_money, $info['order']->body);
            } elseif ($data['pay_way'] == 'weixin') {
                $str = $orderservice->weixinsign($info['order']->product, $info['order']->order_no . '_' . rand(10000, 99999), ($info['order']->online_money) * 100, config('weixin.weixin.NOTIFYURL'));
            } else {
                $res = Orders::unionPaySign($info['order']->order_no . rand(10000, 99999), ($info['order']->online_money) * 100);
                $str = $res['res'];
            }
        }
        return ['message' => $str, 'status' => true];
    }

    /**
     * 识别是orders表还是order表, 并获取order
     */
    public function identifyTable($order_no)
    {
        //如果是以3个字母开头的则证明是orders表，否则order表
        if (preg_match('/^[A-Za-z]{3}/', $order_no)) {
            $order = \DB::table('orders')->where('order_no', $order_no)->first();
            $type = 'orders';
        } else {
            $order = \DB::table('order')->where('order_no', $order_no)->first();
            $type = 'order';
        }

        if (is_object($order)) {
            return ['order' => $order, 'type' => $type];
        } else {
            return false;
        }
    }


}