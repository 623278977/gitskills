<?php
/**活动模型
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/5/5
 * Time: 11:10
 */
namespace App\Models\Orders\Entity;

use App\Models\Zone;
use Illuminate\Database\Eloquent\Model;
use \DB;
use App\Models\Live\Entity as Live;
use App\Models\Brand\Entity as Brands;
use App\Models\Orders\Entity;
class V020700 extends Entity
{
    /**
     * 签名
     */
    static function sign($order_no, $pay_way, $is_continue = 0)
    {
        if (!isset($order_no) || !isset($pay_way)) {
            return -1;//订单号和支付方式是必传参数
        }

        if (!in_array($pay_way, array('ali', 'weixin', 'unionpay'))) {
            return -2;//支付方式只能为ali或微信
        }

        $order = self::where('order_no', $order_no)->first();
        $items = DB::table('orders_items')->where('order_id', $order->id)->lists('product_id', 'type');

        //如果不在线上就让他为0.01
        if (in_array(array_keys($items)[0], ['contract', 'inspect_invite']) && env('APP_ENV')!='production') {
            $order->online_money = 0.01;
        }


        $order->pay_way = $pay_way;
        $order->save();
        $items = self::titleAndDesc($items);
        if (!is_object($order)) {
            return -3;//不存在该订单
        }

        //如果全部为积分支付，就认为的让现金支付为0.01
        $order->online_money == 0 && $order->online_money = 0.01;



        if ($pay_way == 'ali') {
            DB::table('orders')->where('order_no', $order_no)->update(['pay_way' => 'ali']);
//            $str = self::aliSign($order->order_no, $items['product'], 0.01, $items['body']);
            $str = self::aliSign($order->order_no, $items['product'], $order->online_money, $order->body);
        } elseif($pay_way == 'weixin') {
            DB::table('orders')->where('order_no', $order_no)->update(['pay_way' => 'weixin']);
//            $str = self::weixinsign($items['product'], $order->order_no, 1, config('weixin.weixin.NOTIFYURL'));
            if ($is_continue) {
                $str = self::weixinsign($items['product'], $order->order_no . '_' . rand(10000, 99999), ($order->online_money) * 100, config('weixin.weixin.NOTIFYURL'));
            } else {
                $str = self::weixinsign($items['product'], $order->order_no, ($order->online_money) * 100, config('weixin.weixin.NOTIFYURL'));
            }

            if (-1 === $str) {
                return -4;//获取prepay_id失败
            }
        }else{
            DB::table('orders')->where('order_no', $order_no)->update(['pay_way' => 'unionpay']);
            $res = self::unionPaySign($order->order_no, ($order->online_money) * 100);
            $str = $res['res'];
        }


        return $str;
    }

    /**
     * 我的订单列表(_v020700)
     */
    static function myOrders(array $param, \Closure $callback = null)
    {
        $builder = self::join('orders_items as oi', 'oi.order_id', '=', 'orders.id')
            ->where('orders.uid', (int)$param['uid'])
            ->whereIn('oi.type', ['brand', 'brand_goods', 'video','news','score'])
//            ->where('orders.status','pay')
            ->select(
                'orders.id',
                'orders.order_no',
                'orders.status',
                'oi.type',
                'orders.online_money as amount',
                'orders.created_at',
                'orders.status',
                'oi.product_id',
                'oi.status as oi_status',
                'oi.score_price',
                'oi.id as oi_id',
                'orders.pay_way',
                'orders.pay_at',
                'oi.type as oi_type',
                DB::raw('(select price from lab_orders_items  as a WHERE a.order_id = lab_orders.id) as price')
            )
            ->orderBy('oi.created_at', 'desc');
        if ($callback) {
            return $callback($builder);
        }
        $data = $builder->paginate(isset($param['page_size']) ? ((int)$param['page_size']) : 10);

        return $data;
    }



}