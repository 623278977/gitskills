<?php

namespace App\Services;

use App\Models\Live\Entity as LiveModel;
use App\Models\User\Ticket;
use App\Models\Activity\Ticket as ActivityTicket;
use App\Models\Brand\Goods;
use App\Models\Orders\Entity as Orders;
use App\Models\Orders\Items;
use \DB;
use App\Models\Live\Log;
class Live
{
    /**
     * 作用:判断一场直播是不是招商会
     * 参数:$id 直播id
     *
     * 返回值:bool
     */
    public function isInvest($id)
    {
        $live = LiveModel::with('activity.brand')->where('id', $id)->where('status', 0)->first();


        if (count($live->activity->brand)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * 作用:判断一场直播是否需要某用户付费
     * 参数:$id 直播id
     *
     * 返回值:bool
     */
    public function needPay($id, $uid)
    {
        $live = LiveModel::where('id', $id)->where('status', 0)->first();
        $activity_ticket = ActivityTicket::where('activity_id', $live->activity_id)->where('type', 2)
            ->where('status', 1)->where('price', '>', 0)->first();

        $ticket = Ticket::where('uid', $uid)->where('activity_id', $live->activity_id)
            ->where('type', 2)->where('status', 1)->first();


        return (is_object($activity_ticket) && !is_object($ticket));
    }


    /**
     * 作用:获取一场直播的订单列表
     * 参数:$id 直播id
     *
     * 返回值:bool
     */
    public function orderList($id, $real_order_max_id, $sham_order_max_id, $type='mix')
    {
        //该场直播的所有品牌商品
        $goods_ids = Goods::where('live_id', $id)->lists('id')->toArray();
        $query = \DB::table('orders_items')
            ->leftJoin('orders', 'orders.id', '=', 'orders_items.order_id')
//            ->leftJoin('user', 'orders.uid', '=', 'user.uid')
            ->leftJoin('live_brand_goods', 'live_brand_goods.id', '=', 'orders_items.product_id')
            ->where('orders_items.status', 'pay')
            ->where('orders_items.type', 'brand')
            ->whereIn('orders_items.product_id', $goods_ids)
        ;
        $sham_query = \DB::table('orders_sham')
            ->leftJoin('zone', 'orders_sham.zone_id', '=', 'zone.id')
            ->leftJoin('live_brand_goods', 'live_brand_goods.id', '=', 'orders_sham.product_id')
            ->where('orders_sham.type', 'brand')
            ->whereIn('orders_sham.product_id', $goods_ids)
        ;
        $uninon_query = clone $query;
        $sham_query_count = clone $sham_query;
        $sham_query_union = clone $sham_query;
        $sham_query_count = $sham_query_count
            ->select(\DB::raw("count(lab_orders_sham.id) as sham_count,sum(lab_orders_sham.amount) as sham_amount"))->first();
        $orders_structure = $query->groupBy('live_brand_goods.brand_id')
            ->leftJoin('brand', 'brand.id', '=', 'live_brand_goods.brand_id')
            ->select(\DB::raw("
                sum(lab_orders.amount) as total_amount,
                count(lab_orders.id) as orders_count,
                lab_live_brand_goods.brand_id as product_id,
                lab_brand.name as title,
                lab_brand.logo as logo,
                lab_brand.id as brand_id,
                lab_orders.pay_at as pay_at
            "))->get();
        $sham_orders =$sham_query->select('live_brand_goods.brand_id as product_id', 'orders_sham.amount')
                ->leftJoin('brand','brand.id','=','live_brand_goods.brand_id')
                ->get();
        if($type=='mix'){
            $all_count = $sham_query_count->sham_count;
            $all_amount = $sham_query_count->sham_amount;
        }else{
            $all_count = $all_amount = 0;
        }

        $uninon_query = $uninon_query
            ->leftJoin('zone', 'orders.zone_id', '=', 'zone.id')
            ->leftJoin('brand', 'live_brand_goods.brand_id', '=', 'brand.id')
            ->orderBy('pay_at', 'desc')
            ->select(
                'orders.id',
                'brand.name as title',
                'brand.id as brand_id',
                'brand.logo as brand_logo',
                'zone.name as zone_name',
                'orders.mobile',
                'orders.pay_at as created_at',
                'orders.realname',
                'orders.status as type',
                'live_brand_goods.title as brand_goods_title'
            )
            ->where('orders.id', '>', $real_order_max_id)
        ;
        if($type=='mix'){
            //总订单，包括伪造的订单
            $orders_dynamic =$sham_query_union
                ->leftJoin('brand', 'live_brand_goods.brand_id', '=', 'brand.id')
                ->select(
                    'orders_sham.id',
                    'brand.name as title',
                    'brand.id as brand_id',
                    'brand.logo as brand_logo',
                    'zone.name as zone_name',
                    'orders_sham.mobile',
                    'orders_sham.created_at',
                    'orders_sham.realname',
                    'orders_sham.type',
                    'live_brand_goods.title as brand_goods_title'
                )
                ->where('orders_sham.id', '>', $sham_order_max_id)
                ->union($uninon_query)
                ->orderBy('created_at', 'desc')
                ->orderBy('id', 'desc')
                ->get();
        }else{
            $orders_dynamic = $uninon_query->orderBy('created_at', 'desc')->orderBy('id', 'desc')->get();
        }

        $goods_count = \DB::table('live_brand_goods')
            ->select(\DB::raw("count(brand_id) as goods_count,brand_id "))
            ->where('status','allow')
            ->groupBy('brand_id','live_id')
            ->get();

        $already_sham = $already_real =0;
        foreach($orders_dynamic as $k=>$v){
            $v->mobile = mb_substr($v->mobile,0,4).'****'.mb_substr($v->mobile,8,3);
            $v->realname = starReplace($v->realname);
            if($v->type!='pay'){
                $already_sham==0  &&  $sham_order_max_id = $v->id;
                $already_sham =1;
                $v->id='sham'.$v->id;
                $v->type = 'sham';
                $v->zone_name = starReplace(str_replace('市','',$v->zone_name), 4);
            }else{
                $v->type = 'real';
                $already_real==0 && $real_order_max_id = $v->id;
                $already_real=1;
                $v->zone_name = starReplace(str_replace('市','',$v->zone_name), 4);
            }
            foreach ($goods_count as &$gc){
                if($gc->brand_id == $v->brand_id){
                    $v->goods_count = $gc->goods_count;
                }
            }
            if (!$v->goods_count){
                $v->goods_count = '';
            }
        }

        foreach($orders_structure as $k=>$v){
            $all_amount+=$v->total_amount;
            $all_count+=$v->orders_count;

            if($type=='mix'){
                foreach($sham_orders as $key=>$val){
                    if($val->product_id==$v->product_id){
                        $v->total_amount+=$val->amount;
                        $v->orders_count+=1;
                    }
                }
            }
            foreach ($goods_count as &$gc){
                if($gc->brand_id == $v->brand_id){
                    $v->goods_count = $gc->goods_count;
                }
            }
            if (!$v->goods_count){
                $v->goods_count = '';
            }
        }

        $online_count = $this->getOnlineUsers($id);
        $online_count = $online_count['count'];
        return compact('all_amount', 'all_count', 'online_count','real_order_max_id',
            'sham_order_max_id','orders_structure', 'orders_dynamic');
    }

    /**
     * 作用:获取一场直播的在线人数
     * 参数:$live_id 直播id
     *
     * 返回值:int
     */
    public function getOnlineUsers($live_id, $log_id=0, $with_anonymous=1, $fetchSize=0)
    {
        //该直播在线人数
        $count = Log::where('vid', $live_id)->count();

        $users = \DB::table('log_live')
            ->leftJoin('user', 'log_live.uid', '=', 'user.uid')
            ->select('user.avatar', 'user.nickname', 'log_live.id as log_id')
            ->orderBy('log_live.id', 'asc')
            ->groupBy('log_live.uid')
            ->where('log_live.id', '>', $log_id)
            ->where(function ($builder) use ($log_id, $live_id){
                $old_uids = \DB::table('log_live')->where('vid', $live_id)->where('id', '<=', $log_id)->lists('uid');
                $builder->whereNotIn('log_live.uid', $old_uids);
            })
            ->where('log_live.vid', $live_id)
            ->where(function($builder)use($with_anonymous){
                if(!$with_anonymous){
                    $builder->where('log_live.uid', '>', 0);
                }
            });

        if ($fetchSize) {
            $users = $users->take($fetchSize)->get();
        } else {
            $users = $users->get();
        }

        foreach($users as $k=>$v){
            $v->avatar = getImage($v->avatar,'avatar', '', 0);
        }

        if(count($users)){
            $max_log_id = $users[count($users)-1]->log_id;
        }else{
            $max_log_id = $log_id;
        }

        return compact('count', 'users', 'max_log_id');
    }



    /**
     * 作用:判断一场直播是否需要某用户付费
     * 参数:$id 直播id
     *
     * 返回值:bool
     */
    public function getWallInfo($id)
    {
        $wall = \DB::table('data_wall')->where('live_id', $id)->select('image', 'subject')->first();
        if(!is_object($wall)){
            return false;
        }

        $wall->image = getImage($wall->image, 'avatar', '', 0);

        return $wall;
    }



}