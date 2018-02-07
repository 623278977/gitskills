<?php
/**用户关注行业模型
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/5/5
 * Time: 11:10
 */
namespace App\Models\User;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class Ticket extends Model
{
    protected $table = 'user_ticket';

//    public $timestamps = false;
    protected $dateFormat = 'U';

    protected $guarded = [];
// 模型这里不要写构造函数，会影响Model的create方法
//    function __construct()
//    {
//        $this->uploadDir = config('upload.uploadDir');
//    }

    public function user()
    {
        return $this->hasOne('App\Models\User\Entity', 'uid', 'uid');
    }

    public function activity()
    {
        return $this->hasOne(\App\Models\Activity\Entity::class, 'id', 'activity_id');
    }

    public function ticket_obj()
    {
        return $this->hasOne('App\Models\Activity\Ticket', 'id', 'ticket_id');
    }

    static function getRow($where)
    {
        return self::where($where)->first();
    }

    static function getRows($where, $page = 1, $pageSize = 10)
    {
        return self::where($where)->skip(($page - 1) * $pageSize)->take($pageSize)->get();
    }

    static function getCount($where)
    {
        return self::where($where)->count();
    }

    /**
     * @param $activity_id
     * @return mixed
     * 一场活动的报名总数
     */
    static function applyCount($activity_id)
    {
        return self::getCount(array(
            'activity_id' => $activity_id,
            'status' => 1
        ));
    }

    /**
     * @param $maker_id
     * @param $activity_id
     * @return mixed
     * 我的ovo下面报名人总数
     */
    static function myMakerApplyCount($maker_id, $activity_id)
    {
        return self::getCount(array(
            'maker_id' => $maker_id,
            'activity_id' => $activity_id,
            'status' => 1
        ));
    }

    /**
     * @param $where
     * @return mixed
     * 根据条件获取门票
     */
    static function  getTicketsList($where, $type, $page, $pageSize ,$param = [])
    {
        if(isset($param['has_starting']) && $param['has_starting'] == 1){
            $has_starting = 1;
        }else{
            $has_starting = 0;
        }

        $query = self::findTicket($where);

        $data = array();
        $query = $query->leftJoin('order as o', 'o.id', '=', 'ut.order_id')->select(
            'ut.qrcode',
            'ut.ticket_no',
            'ut.status',
            'ut.uid',
            'ut.order_id',
            'ut.form',
            'o.deadline',
            'o.order_no',
            'o.status as order_status',
            'o.online_money',
            'ut.id',
            'a.list_img',
            'a.subject',
            'a.id as activity_id',
            'a.begin_time',
            'a.end_time',
            'm.subject as maker_subject',
            'm.address',
            'm.tel',
            'ut.type',
            'at.price',
            'ut.score_price',
            'ut.is_check',
            'ut.maker_id',
            'z.name as city',
            'z.upid',
            'ut.form',
            'at.id as aid',
            'o.pay_way',
            'o.id as order_id'
        );

        if ($type == "notover") {
            /***未完成***/
            $query = $query->where('o.status', '!=', 1);
        } else {
            /***我的***/
            $query = $query->where(function($query){
                $query->orWhere('o.status', '=', 1)
                ->orWhere('ut.order_id',0);
            });
        }
        $n_query = clone $query;

        if($has_starting){
            $starting_query = clone $query;
            $end_query = clone $query;

            //正在发生
            $starting_data = $starting_query
                ->where('a.begin_time', '<', time())
                ->where('a.end_time' ,'>',time())
                ->orderBy('a.begin_time', 'desc')->orderBy('at.type', 'asc')->orderBy('ut.created_at')
                ->get();
            if(count($starting_data)>0){
                foreach($starting_data as $item){
                    $item->group = 'starting';
                }
            }

            //还未开始
            $no_start_data = $n_query
                ->where('a.begin_time', '>=', time())
                ->orderBy('a.begin_time', 'asc')->orderBy('at.type', 'asc')->orderBy('ut.created_at')
                ->get();
            if(count($no_start_data)>0){
                foreach($no_start_data as $item){
                    $item->group = 'no_start';

                }
            }

            //已经使用或过期
            $end_data = $end_query
                ->where('a.begin_time', '<', time())
                ->where('a.end_time' ,'<',time())
                ->orderBy('a.begin_time', 'desc')->orderBy('at.type', 'asc')->orderBy('ut.created_at')
                ->get();
            if(count($end_data)>0){
                foreach($end_data as $item){
                    $item->group = 'end';
                }
            }

            $tickets = array_merge($starting_data, $no_start_data,$end_data);

            if($type == 'notover'){
                $need_pay = $expire = [];
                foreach($tickets as $item){
                    if($item->order_status < 0){
                        $item->group = 'expire';
                        $expire[] = $item;
                    }
                    if($item->order_status == 0){
                        $item->group = 'need_pay';
                        $need_pay[] = $item;
                    }
                }
                $tickets = array_merge($need_pay, $expire);
            }

        }else{
            $started = $query
                ->where('a.begin_time', '<', time())
                ->orderBy('a.begin_time', 'desc')->orderBy('at.type', 'asc')->orderBy('ut.created_at')
                ->get();

            $no_start = $n_query
                ->where('a.begin_time', '>=', time())
                ->orderBy('a.begin_time', 'asc')->orderBy('at.type', 'asc')->orderBy('ut.created_at')
                ->get();
            $tickets = array_merge($no_start, $started);
        }

        $tickets = array_slice($tickets, ($page-1) * $pageSize, $pageSize);

        if (count($tickets)) {
            foreach ($tickets as $k => $v) {
                $data[$k] = self::getData($v);
                $data[$k]['order_status'] = $v->order_status;
                $data[$k]['tel'] = $v->tel;
                $data[$k]['city'] = $v->city;
                $data[$k]['upid'] = $v->upid;
                $data[$k]['order_lefttime'] = $v->deadline - time();
            }
        }

        return $data;
    }

    /**
     * @param $where
     * 获取票券详细
     */
    static function getDetail($where,$type=1)
    {
        if($type == 1){
            //购买票详情
            $ticket = self::findTicket($where)
                ->join('order as o', 'o.id', '=', 'ut.order_id')
                ->select(
                    'ut.status',
                    'ut.qrcode',
                    'ut.ticket_no',
                    'ut.activity_id',
                    'ut.order_id',
                    'o.deadline',
                    'o.order_no',
                    'o.online_money',
                    'o.status as order_status',
                    'ut.id',
                    'a.list_img',
                    'a.subject',
                    'a.id as activity_id',
                    'a.begin_time',
                    'a.end_time',
                    'm.subject as maker_subject',
                    'm.address',
                    'm.id as maker_id',
                    'at.type',
                    'ut.price',
                    'ut.is_check',
                    'ut.maker_id',
                    'ut.form'
                )
                ->first();
        }else{
            //免费票详情
            $ticket = self::findTicket($where)
                ->select(
                    'ut.status',
                    'ut.qrcode',
                    'ut.ticket_no',
                    'ut.activity_id',
                    'ut.order_id',
                    'ut.id',
                    'a.list_img',
                    'a.subject',
                    'a.id as activity_id',
                    'a.begin_time',
                    'a.end_time',
                    'm.subject as maker_subject',
                    'm.address',
                    'm.id as maker_id',
                    'at.type',
                    'at.price',
                    'ut.is_check',
                    'ut.maker_id',
                    'ut.form'
                )
                ->first();
        }
        $data = self::getData($ticket);
        $data['order_status'] = $ticket->order_status;
        $data['order_lefttime'] = $ticket->deadline - time();

        return $data;
    }

    private static function findTicket($where)
    {
        return DB::table('user_ticket as ut')->where($where)
            ->where('ut.status', '!=', -4)
            ->leftjoin('activity_ticket as at', 'at.id', '=', 'ut.ticket_id')
            ->leftjoin('activity as a', 'a.id', '=', 'at.activity_id')
            ->leftjoin('maker as m', 'm.id', '=', 'ut.maker_id')
            ->leftjoin('zone as z', 'm.zone_id', '=', 'z.id');
    }

    private static function getData($ticket)
    {
        $data = array();
        if(isset($ticket->group)){
            $data['group'] = $ticket->group ?: '';

        }
        $data['begin_time_raw'] = $ticket->begin_time;
        $data['list_img'] = getImage($ticket->list_img, 'activity');
        $data['id'] = $ticket->id;
        $data['activity_id'] = $ticket->activity_id;
        $suffix = isset($ticket->city) ? '-'.\App\Models\Activity\Entity::getCityWithSuffix($ticket->city):'';

        if($ticket->type===1){
            $data['subject'] = $ticket->subject .$suffix;
        }else{
            $data['subject'] = $ticket->subject;
        }
        $data['price'] = $ticket->price;
        $data['score_price'] = $ticket->score_price;
        $data['online_money'] = $ticket->online_money;
        $data['begin_time'] = date('Y-m-d H:i', $ticket->begin_time);
        $data['maker_subject'] = $ticket->maker_subject;
        $data['maker_id'] = $ticket->maker_id;
        $data['address'] = $ticket->address;
        $data['form'] = $ticket->form;
        $data['is_over'] = $ticket->end_time > time() ? 0 : 1;
        $data['type'] = \App\Models\Activity\Ticket::getType($ticket->type);
        $data['is_check'] = $ticket->is_check;
        $data['ticket_no'] = $ticket->ticket_no;
        $data['order_no'] = $ticket->order_no;
//        $data['ticket_no'] = $ticket->ticket_no;
//        $data['qrcode'] = getImage($ticket->qrcode, 'qrcode', null);
//        $data['activity_id'] = $ticket->activity_id;
        $data['is_send'] = $ticket->form=='order' ? 0 : 1;
//        $data['maker_id'] = $ticket->maker_id;
//        $data['activity_url'] = createUrl(
//            'activity/detail',
//            array(
//                'id' => $ticket->activity_id,
//                'pagetag' => config('app.activity_detail')
//            )
//        );

        $data['ticket_url'] = self::getTicketUrl($ticket);
        $data['aid'] = $ticket->aid;
        $data['pay_way'] = $ticket->pay_way;
        $data['order_id'] = $ticket->order_id;
        return $data;
    }

    static function getTicketUrl($ticket)
    {
        if ($ticket->status == 1) {
            //已完成
            if ($ticket->type == 2) {
                //现场
                $url = createUrl(
                    'ticket/scene-ticket-detial',
                    array(
                        'id' => $ticket->id,
                        'pagetag' => config('app.activity_detail')
                    )
                );
            } else {
                //直播
                $url = createUrl(
                    'ticket/zb-ticket-detial',
                    array(
                        'id' => $ticket->id,
                        'pagetag' => config('app.activity_detail')
                    )
                );
            }
        } else {
            //未完成
            $url = createUrl(
                'ticket/not-ticket',
                array(
                    'id' => $ticket->id,
                    'pagetag' => config('app.activity_detail')
                )
            );
        }

        return $url;
    }

    static function  createQrcode($uid, $order_id, $ticket_id, $ticket_no, $maker_id)
    {
        $file_name = unique_id() . '.png';
        $value = 'ticket_no=' . $ticket_no;
        $object = new Ticket();
        return img_create($value, $file_name, config('upload.uploadDir'));
    }


    /**
     * 生成一张会员购票记录
     */
    static function produce($uid, $order_id, $ticket_id, $maker_id, $type, $price, $status = 0, $score_price = 0, $is_invite = 0)
    {
        $object = new Ticket();
        $ticket = DB::table('activity_ticket')->where('id', $ticket_id)->first();
        $ticket_no = $object->ticketNo($maker_id);

        $qrcode_path = self::createQrcode($uid, $order_id, $ticket_id, $ticket_no, $maker_id);

        $user_ticket = self::create(
            [
                'uid' => $uid,
                'order_id' => $order_id,
                'ticket_id' => $ticket_id,
                'ticket_no' => $ticket_no,
                'maker_id' => $maker_id,
                'qrcode' => $qrcode_path,
                'status' => $status,
                'activity_id' => $ticket->activity_id,
                'type' => $type,
                'price' => $price,
                'score_price' => $score_price,
                'is_invite' => $is_invite,
            ]
        );

        return $user_ticket;
    }

    /**
     * 生成不重复的票号
     */
    static function ticketNo($maker_id)
    {
        $nos = self::lists('ticket_no');
        $ticket_no = date('md') . str_pad($maker_id, 4, "0", STR_PAD_LEFT) . rand(0, 9) . rand(0, 9) . rand(0, 9) . rand(0, 9);
        if (in_array($ticket_no, $nos->toArray())) {
            self::ticketNo($maker_id);
        }

        return $ticket_no;

    }

    /*
    * 作用:获取活动分享票
    * 参数:活动ID
    *
    * 返回值:array
    */
    public static function getShareTickets($activity_id)
    {
        return self::where([
            'activity_id'=>$activity_id,
            'status'=>1,
        ])->get()->toArray();
    }

    /*
    * 作用:创建分享票
    * 参数:用户ID $uid , 活动ID $activity_id, ovo中心ID $maker_id
    *
    * 返回值:
    */
    public static function createShareTicket($uid,$activity_id, $maker_id,$type)
    {
        $ticketNo = self::ticketNo($maker_id);
        $ticket_id = DB::table('activity_ticket')->where([
            'activity_id'=>$activity_id,
            'type'=>$type,
        ])->first()->id;
        $qrcode = self::createQrcode(1,1,1,$ticketNo,$maker_id);
        //查看是否已经领取过门票了
        $ticket = DB::table('user_ticket')
            ->where([
                'uid'=>$uid,
                'activity_id'=>$activity_id,
                'maker_id'=>$maker_id,
                'status' => 1,
                'type'=>$type,
                'form'=>'share'
            ])->first();
        if(is_object($ticket)){
            //已经领取过门票了
            return $ticket;
        }
        return self::create([
            'uid'=>$uid,
            'activity_id'=>$activity_id,
            'ticket_id'=>$ticket_id,
            'maker_id'=>$maker_id,
            'ticket_no'=>$ticketNo,
            'qrcode'=>$qrcode,
            'type'=>$type,
            'status'=>1,
            'form'=>'share',
        ]);
    }


    /**
     * 更新某些字段
     */
    public static function updateBy(Array $where, Array $update)
    {
        $result = self::where($where)->update($update);

        return $result;
    }

    /**
     * 批量更新某些字段
     */
//    public static function updateBys(Array $where,array $whereIn, Array $update)
//    {
//        $result = self::where($where)->whereIn($whereIn)->update($update);
//
//        return $result;
//    }

}