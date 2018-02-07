<?php

namespace App\Services\Version\User;

use App\Models\Activity\Entity as Activity;
use App\Models\Brand\Entity as Brand;
use App\Models\CurrencyLog;
use App\Models\Live\Entity as Live;
use App\Models\Activity\Sign;
use App\Models\Activity\Ticket;
use App\Models\Orders\Entity;
use App\Models\Score\Goods\V020700 as ScoreGoodsV020700;
use App\Models\ScoreLog;
use App\Models\User\Entity as User;
use App\Models\User\Free;
use App\Models\User\Friend;
use App\Models\User\Withdraw;
use App\Services\Version\VersionSelect;
use App\Http\Controllers\Api\UserController;
use App\Models\Activity\Ticket as Activity_Ticket;
use App\Models\Video;
use \DB;

class _v020500 extends _v020400
{
    /*
     * 我的订单列表
     */
    public function postMyorders($param)
    {
        $request = $param['request'];
        $is_complete = (int)$param['request']->input('is_complete', 1);

        $perPage = (int)$param['request']->input('page_size', 10);
        $pageStart = (int)$param['request']->input('page', 1);

        $type = $param['request']->input('type', 'all');

        $offSet = ($pageStart * $perPage) - $perPage;

        if (!User::find($param['uid'])) {
            return ['message' => '非法的uid', 'status' => false];
        }

        $userObj = new UserController($request);

        if ($is_complete) {

            $data = \App\Models\Orders\Entity::myOrders($param, function ($builder) {
                return $builder->where('orders.status','pay')->get();
            });

            //本月时间
            $now = time();
            $list = $thisMonth = $previousMonth = [];

            foreach ($data as $item) {

                $item->status = $userObj->payStatus($item->status);//支付状态
                $item->amount = formatMoney(number_format($item->amount,2));
                if($item->amount === '0'){
                    $item->amount = '免费';
                }else{
                    $item->amount = '¥' . $item->amount;
                }
                $item->type_name = $this->getOrderType($item);
                $item->table = 'orders';
                $item->goodsName = $this->getGoodsName($item, $userObj);
                $info = $userObj->productInfo($item);
                $item->refer_id = $info ? $info->id : 0;
                $item->treaty = $info ? $info->treaty : '';
                $item->pay_way = $item->pay_way == 'none' ? '/' : ($item->pay_way == 'ali' ? '支付宝' : '微信支付');

                $item->oi_id=$item->product_id;
                $item->brand_goods_id = $item->product_id;
                unset($item->oi_status, $item->product_id);

                if (($now - $item->pay_at) < 30 * 24 * 3600) {
                    $thisMonth[] = objToArray($item);
                } else {
                    $previousMonth[] = objToArray($item);
                }
            }


            //直播,活动,点播订单
            $oldData = \App\Models\Order\Entity::baseQuery(10, function ($builder) use ($param) {
                $data = $builder->where('order.uid', $param['uid'])
                    ->whereIn('order.status', [1, 2])
                    ->join('activity_ticket as at', 'order.ticket_id', '=', 'at.id')
                    ->join('user_ticket as ut', 'ut.order_id', '=', 'order.id')
                    ->select('order.status', 'ut.type', 'ut.activity_id', 'order.online_money as amount', 'at.id as oi_id', 'order.pay_way', 'order.updated_at as pay_at','order.id','at.id as at_id','order.order_no','ut.id as user_ticket_id')
                    ->orderBy('pay_at', 'desc')
                    ->get();
                return $data;
            }, $this->oldDataFormat($userObj));

            foreach ($oldData as $item) {
                if (($now - $item->pay_at) < date('t', $now) * 24 * 3600) {
                    $thisMonth[] = objToArray($item);
                } else {
                    $previousMonth[] = objToArray($item);
                }
            }


            switch ($type) {
                case 'this_month':
                    $list['thisMonth'] = array_slice(array_map($this->newDataFormat(), $thisMonth), $offSet, $perPage, TRUE);
                    break;
                case 'previous_month':
                    $list['previous'] = array_slice(array_map($this->newDataFormat(), $previousMonth), $offSet, $perPage, TRUE);
                    break;
                case 'all':
                    $all = array_merge($thisMonth, $previousMonth);

                    //按支付时间倒序排序
                    $all = muliterArraySortByfield($all,'pay_at','desc')?:[];

                    $list = array_slice(array_map($this->newDataFormat(), $all), $offSet, $perPage, TRUE);
                default:
                    break;
            }

            $return = ['message' => $list, 'status' => true];

        } else {

            $data = \App\Models\Orders\Entity::myOrdersIncomplte($param);

            $list = $noPay = $expire = [];

            foreach ($data as $item) {

                $item->status = $userObj->payStatus($item->status);//支付状态
                $item->amount = formatMoney(number_format($item->amount,2));
                $item->type_name = $this->getOrderType($item);
                $item->table = 'orders';
                $item->goodsName = $this->getGoodsName($item, $userObj);
                $item->refer_id = ($res = $userObj->productInfo($item)) ? $res->id : 0;
                $item->pay_way = $item->pay_way == 'none' ? '/' : ($item->pay_way == 'ali' ? '支付宝' : '微信支付');
                if($item->amount === '0'){
                    $item->amount = '免费';
                }else{
                    $item->amount = '¥' . $item->amount;
                }

                $item->oi_id=$item->product_id;
                $item->brand_goods_id = $item->product_id;
                $item->activity_is_over = 0 ;
                unset( $item->oi_status, $item->product_id);

                if ($item->status == '未完成支付环节') {
                    $noPay[] = objToArray($item);
                } else {
                    $expire[] = objToArray($item);
                }
            }

            //直播,活动,点播订单
            $oldData = \App\Models\Order\Entity::baseQuery(10, function ($builder) use ($param) {
                $data = $builder->where('order.uid', $param['uid'])
                    ->whereIn('order.status', [-1, 0])
                    ->join('activity_ticket as at', 'order.ticket_id', '=', 'at.id')
                    ->join('user_ticket as ut', 'ut.order_id', '=', 'order.id')
                    ->select('order.status', 'ut.type', 'ut.activity_id', 'order.online_money as amount','order.order_no','order.cost as price','order.ticket_id', 'order.created_at as created_at','at.id as oi_id', 'order.pay_way', 'order.deadline as deadline_raw','order.id','order.order_no')
                    ->orderBy('created_at', 'desc')
                    ->get();
                return $data;
            }, $this->oldDataFormat($userObj));

            foreach ($oldData as $item) {
                if ($item->oi_status == '未完成支付环节') {
                    $noPay[] = objToArray($item);
                } else {
                    $expire[] = objToArray($item);
                }
            }

            //按生成时间倒序排序
            $noPay = muliterArraySortByfield($noPay,'created_at','desc')?:[];

            $expire = muliterArraySortByfield($expire,'created_at','desc')?:[];

            switch ($type) {
                case 'no_pay':

                    $list['nopay'] = array_slice(array_map($this->newDataFormat(), $noPay), $offSet, $perPage, TRUE);
                    break;
                case 'expired':

                    $list['expired'] = array_slice(array_map($this->newDataFormat(), $expire), $offSet, $perPage, TRUE);
                    break;
                case 'all':
                    $all = array_merge($noPay, $expire);
                    $list = array_slice(array_map($this->newDataFormat(), $all), $offSet, $perPage, TRUE);
                default:
                    break;
            }

            $return = ['message' => $list, 'status' => true];

        }

        return $return;

    }

    public function newDataFormat(){

        $func = function(&$arr) {
            if (isset($arr['pay_at'])) {
                $arr['pay_at'] = date('Y-m-d H:i', $arr['pay_at']);
            }

            if (isset($arr['deadline'])) {
                $timediff = $arr['deadline'] - time();
                $arr['deadline'] = $timediff < 0 ? -1 : $timediff;
            }

            if (isset($arr['created_at']) && !isset($arr['deadline'])) {
                $timediff = 30*60 - (time() - $arr['created_at']);
                $arr['deadline'] = $timediff < 0 ? -1 : $timediff;
            }

            if (isset($arr['created_at'])) {
                $arr['created_at'] = date('Y-m-d H:i', $arr['created_at']);
            }

            if (isset($arr['activity_id'])) {
                unset($arr['activity_id']);
            }

            return $arr;
        };

        return $func;
    }


    /*
     * 格式化以前的数据
     */
    public function oldDataFormat($userObj)
    {
        $func = function ($data) use ($userObj) {

            foreach ($data as $k => $v) {

                $v->status = $userObj->payStatus($v->status);//支付状态
                $v->amount = formatMoney(number_format($v->amount,2));
                if($v->amount === '0'){
                    $v->amount = '免费';
                }else{
                    $v->amount = '¥' . $v->amount;
                }
                $v->type_name = $this->getOrderType($v);
                $v->goodsName = $this->getGoodsName($v, $userObj);
                $v->refer_id = $this->getReferId($v);
                if($v->type==2){
                    $v->live_id = Live::where('activity_id',$v->activity_id)->value('id');
                }
                $v->ticket_type = $v->type;
                $v->type = $v->type == 1 ? 'activity' : ($v->type == 2 ? 'live' : 'video');
                $v->pay_way = $v->pay_way == 'none' ? '/' : ($v->pay_way == 'ali' ? '支付宝' :($v->pay_way == 'weixin' ? '微信支付':'积分'));

                $activity = Activity::find($v->activity_id);
                $v->activity_is_over = $activity ? ( $activity->end_time < time() ? 1 : 0 ) : 0;

                if (isset($v->deadline_raw)) {
                    $v->deadline = $v->deadline_raw;
                    unset($v->deadline_raw);
                }


            }

            return $data;
        };

        return $func;
    }

    /*
     * 获取关联id
     */
    private function getReferId($obj)
    {
        switch($obj->type){
            case 1:
                return $obj->activity_id;
                break;
            case 2:
                //$live = Live::where('activity_id',$obj->activity_id)->first();
                //return $live?$live->id:0;
                return $obj->activity_id;
                break;
            case -1:
                $live = Live::where('activity_id',$obj->activity_id)->first();
                $video = Video::where('activity_id',$obj->activity_id)->where('live_id',$live->id)->first();
                return $video?$video->id:0;
                break;
        }
    }

    /*
     * 获取订单类型
     */
    public function getOrderType($obj)
    {
        switch ($obj->type) {
            case 'brand':
                return '品牌加盟定金支付';
                break;
            case 'brand_goods':
                return '品牌加盟定金支付';
                break;
            case 'video':
            case -1:
                return '无界学院视频观看购买';
                break;
            case 1:
                return '无界商圈活动门票购置';
                break;
            case 2:
                return $obj->activity_id ? '无界商圈活动门票购置' : '无界商圈直播门票购置';
                break;
            default:
                return $obj->type;
        }
    }

    /*
     * 商品名称
     */
    public function getGoodsName($obj, $userObj)
    {
        if ($obj->table == 'orders') {
            $product = $userObj->productInfo($obj);
            if($obj->type == 'video'){
                return '录播视频观看票';
            }elseif ($obj->type == 'news'){
                return $product->title;
            }elseif ($obj->type == 'score'){
                $name = ScoreGoodsV020700::where('id',$obj->product_id)->value('subject');
                return $name.'充值';
            }
            return $product ? ($product->product_name ?: '') : '';

        } else {

            $obj->table = 'order';

            switch ($obj->type) {
                case -1:
                    return '录播视频观看票';
                    break;
                case 1:
                    if ($ticket = Activity_Ticket::find($obj->oi_id)) {

                        $ticket_type = $ticket->type == 1 ? '现场票' : ($ticket->type == 2 ? '直播票' : '点播票');

                        $ticket_name = $ticket->name ? '·' . $ticket->name : '';//门票名称缺失隐藏

                        if ($ticket->type == 2) {//直播票隐藏
                            $ticket_name = '';
                        }

                        $activity_name = '';
                        $activity = Activity::find($ticket->activity_id);

                        if ($activity) {
                            $activity_name = substrwithdot($activity->subject,12);
                        }

                        return $activity_name . "($ticket_type" . "$ticket_name)";
                    }
                    break;
                case 2:
                    if($obj->activity_id){
                        $activity_name = '';
                        $activity = Activity::find($obj->activity_id);

                        if ($activity) {
                            $activity_name = substrwithdot($activity->subject,12);
                        }
                        return $activity_name . '(直播票)';
                    }
                    return '直播票';
                    break;
                default:
                    return $obj->type;
            }

            return '活动名称（门票类型·门票名称）';

        }
    }


    /*
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

        if($type && $refer_id){
            goto oldVersion;
        }

        $userObj = new UserController($param['request']);

        $order_no = $param['request']->input('order_no');

        if(!$order_no){
            return ['message' => 'order_no必填项', 'status' => false];
        }

        if(preg_match('/^[A-Za-z]{3}/', $order_no)){
            $table = 'orders';
            $order_id = Entity::where('order_no',$order_no)->value('id');
        }else{
            $table = 'order';
            $order_id = \App\Models\Order\Entity::where('order_no',$order_no)->where('uid',$uid)->value('id');
        }
        if (!$order_id) {
            return ['message' => '订单不存在', 'status' => false];
        }

        if($table == 'order'){

            $oldData = \App\Models\Order\Entity::baseQuery(10, function ($builder) use ($uid , $order_id) {
                $data = $builder->where('order.uid', $uid)
                    ->where('order.id',$order_id)
                    ->join('activity_ticket as at', 'order.ticket_id', '=', 'at.id')
                    ->join('user_ticket as ut', 'ut.order_id', '=', 'order.id')
                    ->select('order.status', 'ut.type', 'ut.activity_id', 'order.online_money as amount', 'at.id as oi_id', 'order.pay_way', 'order.updated_at as pay_at','order.id')
                    ->orderBy('pay_at', 'desc')
                    ->get();
                return $data;
            }, $this->oldDataFormat($userObj));

            if (!$oldData) {
                return ['message' => '订单不存在', 'status' => false];
            }

            $needFormatData = $oldData->toArray();
            $orerInfo = array_map($this->newDataFormat(),$needFormatData);
            $orerInfo = $orerInfo[0];

            $type = array_get($orerInfo,'type');
            $refer_id = array_get($orerInfo,'refer_id');


        }elseif($table == 'orders'){

            $data = \App\Models\Orders\Entity::myOrders(['uid'=>$uid], function ($builder) use ($order_id) {
                return $builder
                    ->where('orders.id',$order_id)
                    ->first();
            });

            if (!$data) {
                return ['message' => '订单不存在', 'status' => false];
            }

            $data->status = $userObj->payStatus($data->oi_status);//支付状态
            $data->amount = formatMoney(number_format($data->amount,2));
            if($data->amount == '0'){
                $data->amount = '免费';
            }else{
                $data->amount = '¥' . $data->amount;
            }
            $data->type_name = $this->getOrderType($data);
            $data->table = 'orders';
            $data->goodsName = $this->getGoodsName($data, $userObj);
            $info = $userObj->productInfo($data);
            $data->refer_id = $info ? $info->id : 0;
            $data->treaty = $info ? $info->treaty : '';
            $data->pay_way = $data->pay_way == 'none' ? '/' : ($data->pay_way == 'ali' ? '支付宝' : '微信支付');

            $data->oi_id=$data->product_id;
            unset($data->order_no, $data->created_at, $data->oi_status, $data->product_id);

            array_map($this->newDataFormat(),[$data]);
            $orerInfo = $data;

            $type = $orerInfo->type;
            $refer_id = $orerInfo->refer_id;
        }

        oldVersion:
        $content = [];

        switch($type){
            case 'brand_goods':
                goto brand;

                break;
            case 'brand':

                brand:
                if($rawData = Brand::where('id',$refer_id)->first()){
                    $content = [
                        'id' => $refer_id,
                        'name' => $rawData->name,
                        'keywords' => $rawData->keywords ? strpos($rawData->keywords,' ')!==FALSE ? explode(' ',$rawData->keywords) : [$rawData->keywords] : [],
                        'detail' => trim(preg_replace('#<\w+(\s+.*?)?>|</\w+>|&nbsp;|\r|\n|\t#','',$rawData->details)),
                        'treaty' => strip_tags($rawData->treaty),
                        'logo' => getImage($rawData->logo, '', ''),
                        'investment_arrange' => formatMoney($rawData->investment_min) . '-' .formatMoney($rawData->investment_max) .'万',
                        'category_name' => \DB::table('categorys')->where('id',$rawData->categorys1_id)->first()->name,
                    ];
                }

                break;
            case 'activity':

                if($rawData = Activity::find($refer_id)){
                    $content = [
                        'id' => $refer_id,
                        'name' => $rawData->subject,
                        'keywords' => $rawData->keywords ? strpos($rawData->keywords,' ')!==FALSE ? explode(' ',$rawData->keywords) : [$rawData->keywords] : [],
                        'begin_time_format' => date("m月d日 H:i",$rawData->begin_time),
                        'host_cities' => Activity::getAllCitiesOfActivity($refer_id),
                    ];
                    $content['begin_time']=$content['begin_time_format'];
                    $content['cities']=$content['host_cities'];
                }

                break;
            case 'live':

                if($rawData = Live::where('activity_id',$refer_id)->first()){
                    $ticket = Ticket::where('activity_id',$rawData->activity_id)->where('type',2)->where('status',1)->min('price');
                    $content = [
                        'id' => $refer_id,
                        'name' => $rawData->subject,
                        'keywords' => $rawData->keywords ? strpos($rawData->keywords,' ')!==FALSE ? explode(' ',$rawData->keywords) : [$rawData->keywords] : [],
                        'begin_time' => date("m月d日 H:i",$rawData->begin_time),
                        'begin_time_format' => date('Y-m-d H:i:s',$rawData->begin_time),
                        'min_price' => $ticket?($ticket == '0.01' ? '0.01' : ($ticket == '0.00' ? '0' :$ticket)):0,
                    ];
                }

                break;
            case 'video':

                if($rawData = Video::find($refer_id)){
                    $content = [
                        'id' => $refer_id,
                        'name' => $rawData->subject,
                        'keywords' => $rawData->keywords ? strpos($rawData->keywords,' ')!==FALSE ? explode(' ',$rawData->keywords) : [$rawData->keywords] : [],
                        'record_at' => date("Y-m-d",$rawData->created_at->getTimestamp()),
                        'length' => $rawData->duration?changeTimeType($rawData->duration):0,
                        'image' => getImage($rawData->image, 'video', ''),
                        'begin_time_format' => date('Y-m-d H:i:s',$rawData->created_at->getTimestamp()),
                    ];
                }

                break;
            default:
                $content = [];
                break;
        }

        if($orerInfo){
            $content['orderInfo'] = $orerInfo;
        }

        return ['message' => $content, 'status' => true];


    }


    /**
     * todo 疑似弃用  暂不处理
     * @User yaokai
     * @param $param
     * @return array
     */
    public function postGetuserdetail($param)
    {
        $data = User::getUserByuidOrUsername(array($param['user_outh']), 'detail');
        if (\Auth::check()) {
            foreach ($data as $k => $v) {
                if ($v['is_wjsq']) {
                    $data[$k]['friend'] = Friend::getRemark(\Auth::id(), $v['uid']);
                }
            }
        }
        return ['message' => $data, 'status' => true];
    }


    /*
     * 提现记录
     */
    public function postWithdrawlist($param)
    {
        $uid = abs((int)$param['request']->input('uid', 0));
        if (!$uid) {
            return ['message' => 'uid必填项', 'status' => false];
        }

        if (!($user = User::find($uid))) {
            return ['message' => '非法的uid', 'status' => false];
        }

        $page = $param['request']->input('page', 1);
        $pageSize = $param['request']->input('pageSize', 10);

        $data = Withdraw::lists(['uid' => $uid, 'pageSize' => $pageSize]);
        $return = $list = [];
        $rawData = $data->toArray()['data'];

        if (count($rawData)) {
            $return = array_map($this->formateData($return), $rawData);
            $list = array_group_by_key($return, 'status_en');
        }

        return ['message' => $list?:['pending'=>[]], 'status' => true];

    }

    /*
     * 格式化
     */
    private function formateData($return)
    {
        $func = function ($value) use ($return) {

            if(!$value){
                return '';
            }

            $return['id'] = $value['id'];
            $return['source_num'] = number_format($value['source_num']);
            $return['money'] = $return['source_num'];
            $return['created_at'] = date('Y-m-d H:i:s',$value['created_at']);
            $return['updated_at'] = $value['status'] == 'pending' ? '/' : date('Y-m-d H:i:s',$value['updated_at']);
            $return['status_en'] = $value['status'];
            $return['status_cn'] = $value['status'] == 'pending' ? '处理中' : '已完成';

            return $return;
        };

        return $func;
    }

    /*
     * 提现详情
     */
    public function postWithdrawdetail($param)
    {
        $request = $param['request'];
        $uid = abs((int)$request->input('uid', 0));

        if (!$uid) {
            return ['message' => 'uid必填项', 'status' => false];
        }

        if (!($user = User::find($uid))) {
            return ['message' => '非法的uid', 'status' => false];
        }

        $id = abs((int)$request->input('id', 0));
        if (!$id) {
            return ['message' => 'id必填项', 'status' => false];
        }

        $data = Withdraw::detail(['id' => $id, 'uid' => $uid]);
        $return = $list = [];
        $rawData = $data ? $data->toArray() : '';

        if (count($rawData)) {
            $return = array_map($this->formateData($return), [$rawData]);
            $list = count($return) == 1 ? $return[0] : [];
        }

        return ['message' => $list, 'status' => true];

    }

    /*
     * 积分提现申请
     */
    public function postWithdraw($param)
    {
        $request = $param['request'];
        $uid = (int)$request->input('uid', 0);
        if (!$uid) {
            return ['message' => 'uid必填项', 'status' => false];
        }
        if (! ($user = User::find($uid))) {
            return ['message' => '非法的uid', 'status' => false];
        }

        $source_num = $request->input('source_num');
        if (!$source_num) {
            return ['message' => '提现总数不能为0', 'status' => false];
        }
        if (strpos($source_num, '.')) {
            return ['message' => '提现总数不能为小数', 'status' => false];
        }
        $source_num = (int)$source_num;
        if ($source_num < 0) {
            return ['message' => '提现总数不能为负数', 'status' => false];
        }

        if ($source_num > $user->currency ) {
            return ['message' => '没有足够多的无界币', 'status' => false];
        }

        $account_type = $request->input('account_type', 'alipay');
        if (!in_array($account_type, ['alipay', 'bank'])) {
            return ['message' => '提现方式非法', 'status' => false];
        }

        $account = $request->input('account', '');
        $name = $request->input('name', '');

        if (!$name) {
            return ['message' => '姓名是必填项', 'status' => false];
        }
        if (preg_match('/[@.#\$%\^&\*]+/', $name)) {
            return ['message' => '姓名包含非法字符', 'status' => false];
        }

        $bank_name = $request->input('bank_name', '');

        if ($account_type == 'alipay') {
            if (!$account) {
                return ['message' => '支付宝账号是必填项', 'status' => false];
            }
            if (!preg_match('/\w+([-+.]\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*/', $account)) {
                if (!preg_match('/^(0|86|17951)?(13[0-9]|15[012356789]|17[678]|18[0-9]|14[57])[0-9]{8}$/', $account)) {
                    return ['message' => '非法的支付宝账号', 'status' => false];
                }
            }
        } elseif ($account_type == 'bank') {
            if (!$bank_name) {
                return ['message' => '银行名称是必填项', 'status' => false];
            }
            if (!$account) {
                return ['message' => '银行卡号是必填项', 'status' => false];
            }
            if (!preg_match('/\d{16,20}/', $account)) {
                return ['message' => '非法的银行卡号', 'status' => false];
            }
        } else {
            return ['message' => '提现方式非法', 'status' => false];
        }

        $return = Withdraw::create([
            'uid' => $uid,
            'source_num' => $source_num,
            'money' => $source_num,
            'account_type' => $account_type,
            'account' => $account,
            'name' => $name,
            'bank_name' => $bank_name,
        ]);

        //提交申请成功,减少相应的无界币
        User::where('uid',$uid)->decrement('currency', $source_num);
        //无界币记录
        CurrencyLog::create([
            'uid' => $uid,
            'operation' => '-1',
            'num' => $source_num,
            'relation_type' => 'withdraw',
            'relation_id' => $return->id,
            'action' => 'extract',
        ]);

        if ($return) {
            return ['message' => ['withdraw_id' => $return->id], 'status' => true];
        }

        return ['message' => '提现失败', 'status' => false];

    }

    /*
     * 我的门票列表
     */
    public function postUserticketlist($param)
    {
        if (empty($param['_uid'])) {
            return ['message' => 'uid必填', 'status' => false];
        }

        if (!User::find($param['_uid'])) {
            return ['message' => '非法的uid', 'status' => false];
        }

        $request = $param['request'];;
        $type = $request->input('type') ?: 'my'; //我的my  未完成notover
        $page = (int)$request->input('page') ?: 1;
        $pageSize = (int)$request->input('pageSize') ?: 15;

        $where = array();
        $where['ut.uid'] = $param['_uid'];

        $return = [];

        $tickets = \App\Models\User\Ticket::getTicketsList($where, $type, $page, $pageSize, ['has_starting' => 1]);

        if ($tickets) {
            $_NOW=time();
            foreach ($tickets as $k => $item) {
                $return[$k]['group'] = $item['group'];
                $return[$k]['order_no'] = $item['order_no'];
                $return[$k]['subject'] = $item['subject'];
                $return[$k]['begin_time'] = date('Y年m月d日 H:i', $item['begin_time_raw']);
                $return[$k]['surplus_time'] = $item['begin_time_raw'] > $_NOW ? $item['begin_time_raw'] - $_NOW : -1;
                $return[$k]['ticket_type'] = $item['type'];
                $return[$k]['price'] = $item['price'] == '0.00' ? '免费' : $item['price'];
                $return[$k]['online_money'] = $item['online_money'];
                $ticket = Ticket::find($item['aid']);
                $ticket_name = $ticket ? $ticket->name : '';
                $return[$k]['ticket_name'] = $ticket_name;
                $return[$k]['ticket_status'] = $this->getTicketStatus($item, $param['_uid'], $type);
                $return[$k]['activity_id'] = $item['activity_id'];
                $return[$k]['is_sign'] = $item['is_sign'];
                $return[$k]['ticket_url'] = $item['ticket_url'];
                $return[$k]['is_over'] = $item['is_over'];
                $return[$k]['order_lefttime'] = $item['order_lefttime'];
                $return[$k]['maker_id'] = $item['maker_id'];
                $live = Live::where('activity_id', $item['activity_id'])->first();
                $return[$k]['live_id'] = $live ? $live->id : 0;
                $video = Video::where('activity_id',$item['activity_id'])->where('live_id',$return[$k]['live_id'])->first();
                $return[$k]['video_id'] = $video ? $video->id : 0;
                $return[$k]['ticket_id'] = $item['id'];
                $return[$k]['is_check'] = $item['is_check'];
                $return[$k]['pay_way'] = $item['pay_way'] == 'ali' ? '支付宝支付' : '微信支付';
                $return[$k]['order_id'] = $item['order_id'];
                $return[$k]['activity_ticket_id'] = $item['aid'];
            }
        }

        return ['message' => $return, 'status' => true];


    }

    /*
     * 获取票券状态
     */
    protected function getTicketStatus(&$item, $uid, $type)
    {
        $group = $item['group'];
        $item['is_sign'] = 0;

        switch ($group) {
            case 'starting':
                if ($item['type'] == '现场票') {
                    $sign = Sign::where('uid', $uid)
                        ->where('activity_id', $item['activity_id'])
                        ->first();
                    if (@$sign->status == 0) {
                        return '活动即将开始，未签到，请及时赴会签到';
                    } elseif ($sign->status == 1) {
                        $item['is_sign'] = 1;
                        return '活动已开始';
                    }

                }

                if ($item['type'] == '直播票') {

                    return '直播已开启';
                }
                break;
            case 'no_start':
                if ($item['is_check']) {
//                    return '直播未开始';
                    return '已签到';
                }
                return '未使用';
                break;
            case 'end':
                if ($item['type'] == '现场票') {
                    $sign = Sign::where('uid', $uid)
                        ->where('activity_id', $item['activity_id'])
                        ->first();
                    if (@$sign->status == 0) {

                        return '已过期 (未签到)';
                    } elseif ($sign->status == 1) {
                        $item['is_sign'] = 1;
                        return '已签到';
                    }

                }

                if ($item['type'] == '直播票') {

                    return '直播已结束';
                }
                break;
            case 'need_pay':
                return '未完成支付环节';
                break;
            case 'expire':
                return '超出支付时间';
                break;
            default:
                return '';
                break;
        }
    }

    /**
     * 被邀请人填写邀请码  --数据中心版  弃用  不处理
     * @User
     * @param $param
     * @return array
     */
    public function postWritecode($param)
    {
        if (empty($param['_uid'])) {
            return ['message' => 'uid必填', 'status' => false];
        }

        if (!($user = User::where('uid', $param['_uid'])->first())) {
            return ['message' => '非法的uid', 'status' => false];
        }

        $request = $param['request'];
        $invite_code = trim($request->input('invite_code', ''));

        if (empty($invite_code)) {
            return ['message' => '邀请码不能为空', 'status' => false];
        }

        if (!preg_match('/[012356789]{8,9}/', $invite_code)) {
            return ['message' => '邀请码为8位或9位不包含4的数字', 'status' => false];
        }

        if (!User::where('my_invite', $invite_code)->first()) {
            return ['message' => '邀请码不存在', 'status' => false];
        }
        
        if (User::where('my_invite', $invite_code)->where('register_invite','=',$user->my_invite)->first()) {
            return ['message' => '邀请码无效，无法输入你拓展的用户邀请码', 'status' => false];
        }

        if ($user->register_invite) {
            return ['message' => '已经输入过邀请码', 'status' => false];
        }

        //写入数据
        $update = [
            'register_invite' => $invite_code
        ];

        $res = User::where('uid', $param['_uid'])->update($update);

        //邀请人获得100积分
        if($invitor = User::where('my_invite',$invite_code)->first()){

            $name = $user->realname?:($user->nickname?:'');

            //系统消息
            createMessage(
                $invitor->uid,
                $title = '获得了100积分',
                $content = "恭喜你,($name) 填写了你的邀请码并注册了账号,100积分已到账户中,打开app查看我的积分",
                $ext = '',
                $end = '<p>如有疑问，请致电服务热线<span>400-011-0061</span></p>',
                $type = 1,
                $delay = 300
            );

//            $content_sms = trans('sms.invite_score',['name'=>$name]);

            //给邀请人赠送积分
            ScoreLog::add($invitor->uid, 100, 'invite_register', '邀请用户注册');

            //赠送一次免费抽奖机会
            Free::create(
                [
                    'uid'=>$invitor->uid,
                    'num'=>1,
                    'use'=>0,
                    'source'=>'invite',
                    'source_id'=>$param['_uid'],
                ]
            );

            //短信
            @SendTemplateSMS('invite_score',$invitor->username,'invite',['name'=>$name],$invitor->nation_code);
        }


        if ($res !== false) {
            return ['message' => '操作成功', 'status' => true];
        }

        return ['message' => '操作失败', 'status' => false];

    }

    /*
     * 获取用户提现账号
     */
    public function postWithdrawrecord($param)
    {
        $request = $param['request'];
        $uid = (int)$request->input('uid', 0);
        if (!$uid) {
            return ['message' => 'uid必填项', 'status' => false];
        }
        if (!User::find($uid)) {
            return ['message' => '非法的uid', 'status' => false];
        }

        $account_type = $request->input('account_type');
        if (!$account_type) {
            return ['message' => '提现方式不能为空', 'status' => false];
        }

        $account = Withdraw::where('uid',$uid)
            ->where('account_type',$account_type)
            ->select('account','name','bank_name')
            ->orderBy('id','desc')
            ->first()
//            ->toArray()
        ;

        if(is_object($account)){
            $account = $account->toArray();
            return ['message' => $account ? $account : [], 'status' => true];

        }else{
            return ['message' => '没有找到该条提现记录', 'status' => false];
        }


//        return ['message' => $account ? $account : [], 'status' => true];
    }

    /**
     * 作用:我的品牌浏览记录(30天内)
     * 参数:$data
     *
     * 返回值:
     */
    public function postBrandsBrowse($data)
    {
        if ($data['type'] == 'brand') {
            //获取30天内浏览的品牌id 去重
            $brand_ids = \DB::table('user_browse')
                ->leftJoin('brand', 'brand.id', '=', 'user_browse.relation_id')
                ->where('user_browse.uid', $data['uid'])
                ->where('user_browse.relation', 'brand')
                ->where(
                    function ($query) use ($data) {
                        if (isset($data['keywords']) && $data['keywords'] != '') {
                            $query->where('brand.name', 'like', '%' . $data['keywords'] . '%');
                        }
                    }
                )
                ->where('user_browse.created_at', '>', (time() - 3600 * 24 * 30))
                ->orderBy('user_browse.created_at', 'desc')
                ->lists('user_browse.relation_id');
            $brand_ids = array_unique($brand_ids);
            $brand_ids = array_slice($brand_ids, ($data['page'] - 1) * $data['page_size'], $data['page_size']);
            $brand = new \App\Services\Brand;
            //获取品牌
            $brands = $brand->brandList($brand_ids);
            return array_map(function($item){
                $item['detail']=strip_tags($item['detail']);
                return $item;
            }, $brands);
        }
    }


    /**
     * 作用:我的意向品牌
     * 参数:$data
     *
     * 返回值:
     */
    public function postIntentBrands($data)
    {
        //获取和该用户相关的品牌id
        $brand_ids = $this->intentBrandIds($data['uid'], $data['page'], $data['page_size'], $data['keywords']);
        $brand = new \App\Services\Brand;
        //获取品牌
        $brands = $brand->brandList($brand_ids, 1);
        return array_map(function($item){
            $item['detail'] =  strip_tags($item['detail']);
            return $item;
        }, $brands);
    }


    public function postCurrency($param)
    {
        $user = User::getRow(array('uid' => $param['uid']));
        if(!is_object($user)){
            return ['message' => '参数错误', 'status' => false];
        }

        $rate = config('system.currency_rate');
        $data = ['currency' => $user->currency, 'rate' => $rate];

        return ['message' => $data, 'status' => true];
    }
}