<?php
/**活动模型
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/5/5
 * Time: 11:10
 */
namespace App\Models\Activity;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use \DB;
use App\Models\Activity\Entity as Activity;

class Ticket extends Model
{

    protected $dateFormat = 'U';

    protected $table = 'activity_ticket';

    //关联活动
    public function belongsToActivity()
    {
        return $this->belongsTo(Activity::class,'activity_id','id');
    }

    //黑名单
    protected $guarded = [];
    /**
     * @param $type
     * @return string
     * 门票类型
     */
    static function getType($type){
        switch($type){
            case 0:
                return '免费票';
            case 1:
                return '现场票';
            case 2:
                return '直播票';
            case 3:
                return 'vip票';
            default:
                return'免费票';
        }
    }

    static function getRow($where){
        return self::where($where)->first();
    }

    /*
   * 作用:获取活动分享票
   * 参数:活动ID
   *
   * 返回值:array
   */
    public static function getShareTickets($activity_id,$uid,$maker_id)
    {
        $tickets = self::where([
            'activity_id'=>$activity_id,
            'status'=>1,
            'is_share'=>'yes',
        ])->get();
        $data = [];
        foreach($tickets as $key=>$ticket){
            $activity                             = \DB::table('activity')->where('id',$ticket->activity_id)->first();
            $data[$key]['subject']                = $activity->subject;
            $data[$key]['is_receivable']          = self::canReceiveTicket($activity_id,$uid,$ticket->type);
            $data[$key]['type']                   = self::getType($ticket->type);
            $data[$key]['price']                  = $ticket->price;
            $data[$key]['end_time']               = date('Y-m-d',$activity->end_time);
            $data[$key]['begin_time']             = date('Y-m-d',$activity->begin_time);
            $data[$key]['remark']                 = '*最终解释权归无界商圈所有';
        }

        return $data;
    }

    /*
    * 作用:是否可以领取分享票
    * 参数:$ticket门票 acitvity_id活动id uid 用户ID
    *
    * 返回值:0不可领取 1可以领取 2已经领取
    */
    public static function canReceiveTicket($activity_id,$user_share_id,$type)
    {
        $needNum = \DB::table('activity')
            ->where('id',$activity_id)
            ->first()->invite_num;
        //获取分享记录ID
        $arrivedNum = \DB::table('user')
            ->where([
                'user_share_id'=>$user_share_id,
                'status'=>1
            ])->count();
        //0 > 0 1
        //当前用户ID
        $uid = DB::table('user_share')->where('id',$user_share_id)->first()->uid;
        $status = $needNum > $arrivedNum ? 0 : 1;
        if($status){
            //如果可以领取
            //判断是否领取过
            $ticket = \DB::table('user_ticket')
                ->where([
                    'activity_id'=>$activity_id,
                    'uid'=>$uid,
                    'status'=>1,
                    'type'=>1,//现场票是否领取过
                    'form'=>'share'
                ])->first();
            if(is_object($ticket) || $type==2){
                $status = 2;
            }
        }
        return $status;
    }
    /*
    * 作用:获取活动的最低票价
    * 参数:
    *
    * 返回值:
    */
    public static function getLowestTicketPriceOfActivity($activity_id)
    {
        $tickets = self::where('activity_id',$activity_id)->where('status',1)->get();
        $random = self::where('activity_id',$activity_id)->where('status',1)->first();
        $price = $random->price;

        foreach($tickets as $ticket){
            $price = $ticket->price < $price ? $ticket->price : $price;
        }
        return $price;
    }

    /**
     * 对某个字段进行自增操作
     *
     * @param $activity_id
     *
     * @return int
     */
    public static function incre(Array $where, Array $field)
    {
        $result = self::where($where)->increment(array_keys($field)[0], array_values($field)[0]);

        return $result;
    }



    public static function getInfo($ticket_id)
    {
        $result = DB::table('activity_ticket')
            ->leftJoin('activity', 'activity.id', '=', 'activity_ticket.activity_id')
            ->where('activity_ticket.id', $ticket_id)
            ->select('activity.subject', 'activity_ticket.price', 'activity_ticket.type', 'activity.id', 'activity.begin_time')->first();

        return $result;
    }

    /*
     * 根据id获取票券信息
     */
    static function getTicketInfo($id)
    {
        $return = self::where('activity_id',$id)
            ->where('status',1)
            ->where('type','>',-1)
            ->select('id','activity_id','type','price','intro','num','remark','surplus','name')
            ->get()
            ->toArray();

        return $return?:[];
    }

    /*
     * 获取最小值
     */
    static function getMinTicket($id)
    {
        //免费票,直播和现场票价都为0
        $freeCount = self::where('activity_id',$id)
            ->where('status',1)
            ->where('type','>',-1)
            ->where('price' ,0)
            ->count();

        $ticketCount = self::getTicketInfo($id);

        if($freeCount == count($ticketCount)){
            $return = '免费';
        }else{
            $return = self::where('activity_id',$id)
                ->where('status',1)
                ->where('type', 1)
                ->orderBy('price','desc')
                ->get()
                ->toArray();

            $min_price = end($return);
        }

        return is_array($return) ? ( count($return) ==1 ? abandonZero($min_price['price']) .'元' : abandonZero($min_price['price']) .'元起') : $return;

    }

    /**
     * 根据活动id获取直播票积分形式的价格
     * @param  $id  活动id
     */
    public static function getScorePrice($id)
    {
        $score = self::where('activity_id',$id)
                ->where('type',2)
                ->where('status',1)
                ->value('score_price');
        return $score;
    }

    /**
     * 根据活动id获取现场票积分形式的最小价格
     * @param  $id  活动id
     */
    public static function getMinScore($id)
    {
        $score = self::select(\DB::raw('MIN(score_price) as score_price'))
            ->where('activity_id',$id)
            ->where('type',1)
            ->where('status',1)
            ->value('score_price');
        return $score;
    }









}