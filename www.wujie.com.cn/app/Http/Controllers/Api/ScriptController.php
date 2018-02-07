<?php
/****脚本控制器********/
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\CommonController;
use App\Models\Brand\Goods;
use App\Models\Order\Entity;
use App\Models\Orders\Entity as Orders;
use App\Models\Orders\Items;
use App\Models\ScoreLog;
use Illuminate\Http\Request;
use DB;
use App\Models\Message;

class ScriptController extends CommonController
{
    /*
     * 每秒执行
     * 检验订单状态 （半个小时后 状态改为超时）
     */
    public function getOrderstatus()
    {
        $orders = Entity::getRows(array('status' => 0));
        if (count($orders)) {
            foreach ($orders as $k => $v) {
                if ($v->deadline <= time()) {
                    $v->status = -1;
                    $v->save();
                    DB::table('user_ticket')->where('order_id', $v->id)->update(['status' => -3]);

                    //返回积分
                    ScoreLog::add($v->uid, $v->score_num, 'nopay_order_return', '未支付订单积分返回', 1, false, 'order', $v->id);

                }
            }
        }

        //新表orders
        $orders_new = Orders::getRows(['status'=>'npay']);
        foreach($orders_new as $k=>$v){
            if(($v->created_at->timestamp+1800) < time()){
                if ($v->hasOneOrdersItems->type != 'contract') {
                    Orders::updateOrderByField(['status' => 'expire'], ['id' => $v->id]);
                }

                //返回积分
                ScoreLog::add($v->uid, $v->score_num, 'nopay_order_return', '未支付订单积分返回', 1, false, 'orders', $v->id);
                //返还商品数量
                $items = Items::getByNo($v->order_no);

                foreach($items as $key=>$val){
                    $val->status = 'expire';
                    $val->save();
                    Goods::where('id', $val->product_id)->increment('num', 1);
                }
            }
        }
    }

    /**每天八点执行
     *
     *  默认2  0不提醒 1当天提醒 2提前1天提醒 3提前2天提醒
     */
    public function getActivityremind()
    {
        /**两天提醒**/
        $now = time();
        $two_day_activitys = \App\Models\Activity\Entity::where('status', 1)
            ->where('begin_time', '>=', $now + 2 * 24 * 60 * 60)
            ->where('begin_time', '<', $now + 3 * 24 * 60 * 60)
            ->get();
        $one_day_activitys = \App\Models\Activity\Entity::where('status', 1)
            ->where('begin_time', '>=', $now + 1 * 24 * 60 * 60)
            ->where('begin_time', '<', $now + 2 * 24 * 60 * 60)
            ->get();
        $current_day_activitys = \App\Models\Activity\Entity::where('status', 1)
            ->where('begin_time', '>=', $now)
            ->where('begin_time', '<', $now + 24 * 60 * 60)
            ->get();
        \App\Models\Activity\Entity::remindUser($two_day_activitys, 3);
        \App\Models\Activity\Entity::remindUser($one_day_activitys, 2);
        \App\Models\Activity\Entity::remindUser($current_day_activitys, 1);
    }

    /**
     * 早上11点发 十天未登陆   --数据中心版
     * @User yaokai
     */
    public function getTendaynologin()
    {
        $day = 24 * 60 * 60;
        $users = \App\Models\User\Entity::where('last_login', '<', time() - 10 * $day)->where('status', 1)->get();
//        $tendays_content = trans('sms.tendaysnologin');
        if (count(($users))) {
            foreach ($users as $k => $v) {
//                @SendSMS($v->username, $tendays_content, 'tendaysnologin', 3);
                @SendTemplateSMS('tendaysnologin',$v->non_reversible,'tendaysnologin',[],$v->nation_code);
            }
        }
    }




}