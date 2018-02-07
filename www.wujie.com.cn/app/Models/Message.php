<?php
/**
 * 我的消息模型
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/5/5
 * Time: 11:10
 */
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Bus\Queueable;
use App\Jobs\SendRemindMessage;
use DB;
use App\Models\Activity\Entity as Activity;
use App\Models\Activity\Ticket;
class Message extends Model
{
    use DispatchesJobs, Queueable;

    protected $table = 'my_message';
    protected $guarded = array();
    public $timestamps = true;
    protected $dates = [];
    protected $dateFormat = 'U';


    /**
     * 生成系统消息
     * @param $uid
     * @param $title
     * @param $content
     * @param $ext
     * @param $end
     * @param int $type
     */
    public function createMessage($uid, $title, $content, $ext, $end, $type = 1, $delay = 0)
    {
        $param = [
            'uid' => $uid,
            'title' => $title,
            'content' => $content,
            'ext' => $ext,
            'end' => $end,
            'type' => $type,
        ];
        $job = (new SendRemindMessage($param))->delay($delay);
        $this->dispatch($job);
    }

    /**
     * 直播提醒
     * @param $uid
     * @param $page
     * @param $pageSize
     * @return mixed
     */
    static function LiveMessage($uid, $page, $pageSize)
    {
        $liveMessage = DB::table('my_message as m')
            ->join('live as l', 'm.post_id', '=', 'l.id')
            //->join('activity as a', 'l.activity_id', '=', 'a.id')
            ->join('activity_ticket as at', 'l.activity_id', '=', 'at.activity_id')
            ->where('m.uid', $uid)
            ->where('m.type', 3)
            ->where('at.type',2)
            ->where('at.status', 1)
            ->where('m.title', '<>', '号外,也许你会关注这些直播')
            ->where('m.send_time', '<', time())
            ->orderBy('m.created_at', 'desc')
            ->skip(($page-1) * $pageSize)
            ->take($pageSize)
            ->select('m.title', 'm.content', 'm.created_at', 'm.url',
                'm.post_id as live_id', 'l.subject', 'l.list_img', 'l.begin_time', 'at.type', 'at.price', 'at.num')
            ->get();
        foreach ($liveMessage as $item) {
            $item->is_over = 0;
            $item->begin_time = date('m/d H:i', $item->begin_time);
            $item->created_at = date('Y-m-d', $item->created_at);
            $item->list_img = getImage($item->list_img, '', 0);
            $item->type = self::humenType($item->type);
            if ($item->title == '入驻OVO中心有新直播') {
                $item->type = $item->price = $item->num = '';
            }else if($item->title == '很遗憾的通知你,你订阅的直播被临时取消'){
                $item->is_over = 1;
            }
        }
        return $liveMessage;
    }

    public static function getMessagesOfLiveRemind($uid, $page, $pageSize ,$version = null)
    {
        $liveMessagesBuilder = DB::table('my_message')
            //直播提醒 和 直播号外
            ->whereIn('type',[3,11])
            //未读消息
            ->where('send_time','<',time())
            ->where('uid',$uid)
            ->orderBy('send_time','desc');

        if(in_array($version,['_v020500'])){
            $liveMessagesBuilder = $liveMessagesBuilder
                ->where('title','not like','%入驻%')
                ->where('title','not like','%号外%')
                ->where('title','not like','%OVO%');
        }

        $liveMessages = $liveMessagesBuilder->paginate($pageSize);
        $responseData = [];
        foreach ($liveMessages as $key=>$item) {
            self::where('id', $item->id)->update(['is_read'=>1]);
            $temp = [];
            if($item->type == 3){
                //普通消息
                //根据post_id获取消息关联信息
                $message = DB::table('live')
                    ->where('id',$item->post_id)
                    ->first();
                //获取门票信息
                $ticket = DB::table('activity_ticket')
                    ->where('activity_id',$message->activity_id)
                    ->where('type',2)
                    ->orderBy('price','asc')
                    ->groupBy('activity_id')
                    ->first();
                $user = DB::table('user as u')
                    ->join('maker as m','u.maker_id','=','m.id')
                    ->where('u.uid',$uid)
                    ->select('u.maker_id','m.subject')
                    ->first();

                $user_ticket =\DB::table('user_ticket')
                    ->where('activity_id', $message->activity_id)->where('type',2)
                    ->where('uid', $uid)->where('status', 1)->first();

                $temp['begin_time']         = date('m/d H:i', $message->begin_time);
                $temp['subject']            = $message->subject;
                $temp['live_id']            = $message->id;
                $temp['ticket_price']       = isset($ticket) ? $ticket->price :0;
                $temp['ticket_id']          = isset($user_ticket) ? $user_ticket->ticket_id :0;
                $temp['ticket_type']        = isset($ticket) ? self::humenType($ticket->type) :'';
                $temp['list_img']           = getImage($message->list_img,'live', '', 0);
                $temp['maker_id']           = $user->maker_id;
                $temp['ovo_name']           = $user->subject;

                if (strpos($item->title, '有新的直播')) {
                    $responseData[$key]['type'] = 1;
                    $responseData[$key]['created_at'] =date('Y-m-d', $item->created_at);
                    $responseData[$key]['live_messages'] = [$temp];
                }else if(strpos($item->title, '您订阅的直播被取消')){
                    $temp['tip']                            = "很遗憾通知你，你订阅的直播被临时取消\n购买的费用将按照原支付平台退还";
                    $temp['highlightSubstringOfTip']        = "很遗憾通知你，你订阅的直播被临时取消\n购买的费用将按照原支付平台退还";
                    $responseData[$key]['type'] = 2;
                    $responseData[$key]['created_at'] =date('Y-m-d', $item->created_at);
                    $responseData[$key]['live_messages'] = [$temp];
                }else if(strpos($item->title, '订阅/购买的直播被取消')){
                    $temp['tip']                            = "很遗憾通知你，你订阅/购买的直播被临时取消\n购买的费用将按照原支付平台退还";
                    $temp['highlightSubstringOfTip']        = "很遗憾通知你，你订阅/购买的直播被临时取消\n购买的费用将按照原支付平台退还";
                    $responseData[$key]['type'] = 2;
                    $responseData[$key]['created_at'] =date('Y-m-d', $item->created_at);
                    $responseData[$key]['live_messages'] = [$temp];
                }
                else if(strpos($item->title, '即将开始')){
                    $temp['tip']                            = '订阅的直播即将在1小时后开始，欢迎准时收看';
                    $temp['highlightSubstringOfTip']        = '1小时';
                    $responseData[$key]['type'] = 4;
                    $responseData[$key]['created_at'] =date('Y-m-d', $item->created_at);
                    $responseData[$key]['live_messages'] = [$temp];
                }else if(strpos($item->title, '30分钟后')){
                    $temp['tip']                            = '你订阅的直播30分钟后即将开始';
                    $temp['highlightSubstringOfTip']        = '30分钟';
                    $responseData[$key]['type'] = 5;
                    $responseData[$key]['created_at'] =date('Y-m-d', $item->created_at);
                    $responseData[$key]['live_messages'] = [$temp];
                }

            } elseif($item->type == 11){
                //号外消息
               $messages = unserialize($item->content);
                $temp = [];
                foreach($messages as $index=>$message){
                    $temp[$index]['subject']            = $message['subject'];
                    $temp[$index]['live_url']           = $message['live_url'];
                    $temp[$index]['begin_time']         = date("m/d H:i",$message['begin_time']);
                    $temp[$index]['list_img']           = getImage($message['list_img'],'live', '', 0);
                    $temp[$index]['live_id']            = $message['id'];
                }
                $responseData[$key]['live_messages']   = $temp;
                $responseData[$key]['created_at']       =date('Y-m-d', $item->created_at);
                $responseData[$key]['type']              = 3;
            }
        }
        return array_values($responseData);
    }




    /**
     * 格式化票类型
     * @param $type
     * @return mixed
     */
    static function humenType($type)
    {
        $tickets = [
            '1' => '现场票',
            '2' => '直播票',
            '0' => '免费票',
            '3' => 'vip票',
            '-1' => '点播票',
        ];
        foreach ($tickets as $code => $ticket) {
            if ($type == $code)
                return $ticket;
        }
    }

    /**
     * 推荐的直播
     * @param $uid
     * @return mixed
     */
    static function recommendList($uid)
    {
        $return = [];
        $return['title'] = '号外,也许你会关注这些直播';
        $return['content'] = '根据你之前的订阅,我们向你推荐以下直播,也许你会对他们感兴趣.';
        $data = DB::table('my_message as mm')
            ->join('live as l', 'mm.post_id', '=', 'l.id')
            ->where('mm.uid', $uid)
            ->where('title', '号外,也许你会关注这些直播')
            ->orderBy('l.begin_time', 'desc')
            ->select('mm.title', 'mm.content', 'mm.created_at', 'mm.url',
                'mm.post_id as live_id', 'l.subject', 'l.list_img', 'l.begin_time')
            ->limit(3)
            ->get();
        if (empty($data)) return false;
        foreach ($data as $item) {
            $return['created_at'] = date('Y-m-d', $item->created_at);
            unset($item->title);
            unset($item->content);
            unset($item->created_at);
            $item->begin_time = date('m/d H:i', $item->begin_time);
            $item->list_img = getImage($item->list_img);
            $return[] = $item;
        }
        return objToArray($return);
    }


    /**
     * 活动提醒
     * @param $uid
     * @param $page
     * @param $pageSize
     * @return mixed
     */
    static function activityMessage($uid, $page, $pageSize)
    {
        $data = DB::table('my_message as m')
            ->join('activity as a', 'm.post_id', '=', 'a.id')
            ->join('user_ticket as ut','ut.activity_id','=','a.id')
            //->join('activity_ticket as at', 'a.id', '=', 'at.activity_id')
            //->join('activity_maker as am','ut.maker_id','=','am.id')
            ->join('maker as ovo', 'ut.maker_id', '=', 'ovo.id')
            ->where('ut.uid',$uid)
            ->where('ut.status', 1)
            ->where('ut.type',1) //只发现场票提醒
            ->where('m.uid', $uid)
            ->where('m.type', 2)
            ->where('m.title', '<>', '你关注的活动主办方有新活动')
            ->where('m.title', '<>', '入住的OVO中心有新的活动')
            ->where('m.send_time', '<', time())
            ->orderBy('m.created_at', 'desc')
            ->skip(($page-1) * $pageSize)
            ->take($pageSize)
            ->distinct()
            ->select('m.title', 'm.content', 'm.created_at', 'm.url',
                'm.post_id as activity_id', 'a.subject', 'a.begin_time',
                'ovo.subject as ovo', 'ovo.id as ovo_id','ovo.zone_id', 'ovo.address', 'ut.price', 'a.list_img', 'ut.type')
            ->get();
        foreach($data as $item){
            if(strpos($item->title,'报名成功')!==false){
                $item->remark = 1;
            }else if(strpos($item->title,'活动将于')!==false){
                $item->remark = 2;
            }else if(strpos($item->title,'抱歉,你报名的活动被临时取消')!==false){
                $item->remark = 3;
            }
        }
        return $data;
    }

    public static function getMessagesOfActivityRemind($uid,$page, $pageSize ,$version = null)
    {
        $activity_messages_builder = DB::table('my_message')
            ->where('uid',$uid)
//            ->where('is_read',0)  //未读取的
            ->whereIn('type',[2,10])
            ->where('send_time','<',time())
            ->orderBy('created_at','desc');

        if(in_array($version,['_v020500'])){
            $activity_messages_builder = $activity_messages_builder
                ->where('title','not like','%入驻%')
                ->where('title','not like','%号外%')
                ->where('title','not like','%入住的OVO%');
        }

        $activity_messages = $activity_messages_builder
            ->paginate($pageSize);

//        dd($activity_messages);
        $responseData = [];
        foreach($activity_messages as $index => $activity_message){
            self::where('id', $activity_message->id)->update(['is_read'=>1]);
                //普通消息
            if($activity_message->type == 2){
                $item = DB::table('user_ticket as ut')
                    ->join('activity as a','ut.activity_id','=','a.id')
                    ->join('maker as ovo', 'ut.maker_id', '=', 'ovo.id')
                    ->join('zone as z','z.id','=','ovo.zone_id')
                    ->join('order as od','od.id','=','ut.order_id')
                    ->where('ut.id',substr($activity_message->url,15))
                    ->select('a.subject','a.id as activity_id','a.list_img','ut.id as ticket_id','a.begin_time','a.end_time','ovo.subject as ovo',
                        'ovo.id as ovo_id','ovo.address as address','ut.price as price','ut.score_price','z.name as city','ut.type','od.pay_way')
                    ->first();

                if(strpos($activity_message->title,'报名成功')!==false){
                    $data['subject']                        = $item->subject.'-'.\App\Models\Activity\Entity::getCityWithSuffix($item->city);
                    $data['activity_id']                    = $item->activity_id;
                    $data['begin_time']                     = date('m/d H:i',$item->begin_time);
                    $data['ovo_name']                       = $item->ovo;
                    $data['ovo_id']                         = $item->ovo_id;
                    $data['ovo_address']                    = $item->address;
                    $data['price']                          = $item->price;
                    $data['score_price']                          = $item->score_price;
                    $data['pay_way']                          = $item->pay_way;
//                    $data['price']                          = Ticket::getRow(['activity_id'=>$item->activity_id,'type'=>$item->type])->price;
                    $data['ticket_num']                     = \App\Models\Activity\Entity::getTicketNum($item->activity_id);
                    $data['ticket_id']                      = $item->ticket_id;
                    $data['list_img']                       = getImage($item->list_img,'activity', '', 0);
                    $data['tip']                            = $item->price != 0 ? '你已成功订购活动门票,收取活动费用'.$item->price."元\n届时请准点参加活动" : "你已成功订购活动门票,届时请准点参加活动";
                    $data['highlightSubstringOfTip']        = $item->price != 0 ? '你已成功订购活动门票,收取活动费用'.$item->price."元\n届时请准点参加活动" : "你已成功订购活动门票,届时请准点参加活动";
                    $responseData[$index]['type'] = 1;
                    $responseData[$index]['created_at'] = date('Y-m-d',$item->created_at);
                    $responseData[$index]['activities'] = [$data];
                }else if(strpos($activity_message->title,'活动将于')!==false){
                    $data['subject']                        = $item->subject.'-'.\App\Models\Activity\Entity::getCityWithSuffix($item->city);
                    $data['activity_id']                    = $item->activity_id;
                    $data['begin_time']                     = date('m/d H:i',$item->begin_time);
                    $data['ovo_name']                       = $item->ovo;
                    $data['ovo_id']                         = $item->ovo_id;
                    $data['ovo_address']                    = $item->address;
                    $data['price']                          = $item->price;
                    $data['score_price']                          = $item->score_price;
                    $data['pay_way']                          = $item->pay_way;
//                    $data['price']                          = Ticket::getRow(['activity_id'=>$item->activity_id,'type'=>$item->type])->price;
                    $data['ticket_id']                      = $item->ticket_id;
                    $data['ticket_num']                     = \App\Models\Activity\Entity::getTicketNum($item->activity_id);
                    $data['list_img']                       = getImage($item->list_img,'activity', '', 0);
                    $data['tip']                            = self::getActivityTips($item->begin_time, $item->end_time);
                    $data['highlightSubstringOfTip']        = self::getHumanTime($item->begin_time, $item->end_time);
                    $responseData[$index]['type'] = 2;
                    $responseData[$index]['created_at'] = date('Y-m-d H:i',$item->created_at);
                    $responseData[$index]['activities'] = [$data];
                }else if(strpos($activity_message->title,'你报名的活动被临时取消')!==false){
                    $data['subject']                        = $item->subject.'-'.\App\Models\Activity\Entity::getCityWithSuffix($item->city);
                    $data['activity_id']                    = $item->activity_id;
                    $data['begin_time']                     = date('m/d H:i',$item->begin_time);
                    $data['ovo_name']                       = $item->ovo;
                    $data['ovo_id']                         = $item->ovo_id;
                    $data['ovo_address']                    = $item->address;
                    $data['price']                          = $item->price;
                    $data['score_price']                          = $item->score_price;
                    $data['pay_way']                          = $item->pay_way;
//                    $data['price']                          = Ticket::getRow(['activity_id'=>$item->activity_id,'type'=>$item->type])->price;
                    $data['ticket_num']                     = \App\Models\Activity\Entity::getTicketNum($item->activity_id);
                    $data['ticket_id']                      = $item->ticket_id;
                    $data['list_img']                       = getImage($item->list_img,'activity', '', 0);
                    $data['tip']                            = '很遗憾的通知你，活动因各种原因而被迫终止购买的费用将按照原支付平台返还';
                    $data['highlightSubstringOfTip']        = '很遗憾的通知你，活动因各种原因而被迫终止购买的费用将按照原支付平台返还';
                    $responseData[$index]['type'] = 3;
                    $responseData[$index]['created_at'] = date('Y-m-d',$item->created_at);
                    $responseData[$index]['activities'] = [$data];
                }elseif(strpos($activity_message->title,'OVO中心有新的活动')!==false){
                    $messages = unserialize($activity_message->content);
                    $message = $messages['activities'];
                    $data = [];
                        $data['subject']                        = $message['subject'];
                        $data['activity_id']                    = $message['activity_id'];
                        $data['begin_time']                     = date('m/d',$message['begin_time']);
                        $data['ovo_name']                       = $message['ovo_name'];
                        $data['ovo_id']                         = $message['ovo_id'];
                        $data['ovo_address']                    = $message['ovo_address'];
                        $data['price']                          = $message['price'];
//                        $data['score_price']                    = $message['score_price'];
                        $data['ticket_num']                     = \App\Models\Activity\Entity::getTicketNum($message['activity_id']);
                        $data['list_img']                       = getImage($message['list_img']);
                        $data['tip']                            = $message['tip'];
                        $data['highlightSubstringOfTip']        = $message['highlightSubstringOfTip'];

                    $responseData[$index]['type']               = $messages['type'] ;
                    $responseData[$index]['created_at']         = date('Y-m-d',$activity_message->created_at);
                    $responseData[$index]['activities']         = [$data];
                }
            }elseif($activity_message->type == 10){
                $messages = unserialize($activity_message->content);
//                dd($messages);

                $data = [];
                foreach($messages as $key => $message){
                    $data[$key]['subject']                      = $message['subject'];
                    $data[$key]['activity_id']                  = $message['activity_id'];
                    $data[$key]['price']                        = $message['price'];
//                    $data[$key]['score_price']                          = $message['score_price'];
                    $data[$key]['ticket_num']                         = \App\Models\Activity\Entity::getTicketNum($message['activity_id']);
                    //调整地址
                    $data[$key]['url']                          = createUrl('activity/detail', array('id' => $message['activity_id'], 'pagetag' => config('app.activity_detail')));
                    $data[$key]['list_img']                     = getImage($message['list_img'],'activity','',0);
                    $data[$key]['begin_time']                   = date('m/d H:i',$message['begin_time']);
                }

                $responseData[$index]['activities']             = array_values($data);
                $responseData[$index]['type']                   = 4;
                $responseData[$index]['created_at']             = date('Y-m-d',$activity_message->created_at);
            }
        }
//        dd($responseData);

        return $responseData;
    }
    /**
     * 关注的主办方活动
     * @param $uid
     * @return mixed
     */
    static function organizerMessage($uid){
        $data = DB::table('my_message as m')
            ->join('activity as a', 'm.post_id', '=', 'a.id')
            ->join('activity_ticket as at', 'a.id', '=', 'at.activity_id')
            ->join('activity_maker as am','am.activity_id','=','a.id')
            ->join('maker as ovo', 'am.maker_id', '=', 'ovo.id')
            ->where('m.uid', $uid)
            ->where('m.type', 2)
            ->where('at.status', 1)
            ->where('title', '你关注的活动主办方有新活动')
            ->where('m.send_time', '<', time())
            ->orderBy('m.created_at', 'desc')
            ->distinct()
            ->groupBy('a.id')
            ->select('m.title', 'm.content', 'm.created_at', 'm.url',
                'm.post_id as activity_id', 'a.subject', 'a.begin_time',
                'ovo.subject as ovo', 'ovo.zone_id', 'ovo.address', 'a.list_img', 'at.num', 'at.type',DB::raw('min(price) as min_price'))
            ->limit(3)
            ->get();
        return $data;
    }


    /**
     * 入驻的ovo新活动
     * @param $uid
     * @return mixed
     */
    static function ovoMessage($uid){
        $data = DB::table('my_message as m')
            ->join('activity as a', 'm.post_id', '=', 'a.id')
            //->join('activity_ticket as at', 'a.id', '=', 'at.activity_id')
            ->join('activity_maker as am','am.activity_id','=','a.id')
            ->join('maker as ovo', 'am.maker_id', '=', 'ovo.id')
            ->where('m.uid', $uid)
            ->where('m.type', 2)
            //->where('at.status', 1)
            ->where('title', '入住的OVO中心有新的活动')
            ->where('m.send_time', '<', time())
            ->orderBy('m.created_at', 'desc')
            ->distinct()
            //->groupBy('a.id')
            ->select('m.title', 'm.content', 'm.created_at', 'm.url',
                'm.post_id as activity_id', 'a.subject', 'a.begin_time',
                'ovo.subject as ovo', 'ovo.zone_id', 'ovo.address', 'a.list_img')
            ->limit(3)
            ->get();
        return $data;
    }
    
    /*
    * 作用:获取专版消息
    * 参数:$uid
    * 
    * 返回值:
    */
    public static function getVipMessages($uid,$pageSize)
    {
        $messages = self::where([
            'uid'=>$uid,
            'type'=>9
        ])->where('send_time','<',time())->orderBy('send_time','desc')->paginate($pageSize);
        $total = $messages->total();
        //专版类别
        //type = 1  专版下有直播预告 vipliverecommend 脚本产生
        //type = 2  专版下直播马上开始 vipliverecommend 脚本产生
        //type = 3  专版下有新活动发布 后台创建活动时发送
        //专版类别下的直播
        $responseData = [];
        $responseData['total'] = $total;
       foreach($messages as $key => $message){
           $content = unserialize($message->content);
           if($content){
               $message->is_read = 1;
               $message->save(); //消息标记为已经读
               if($content['type'] == 1){

                   $temp   = [];
                   foreach($content['content'] as $index=>$c){
                       $temp[$index]['subject']     = $c['subject'];
                       $temp[$index]['live_url']    = $c['live_url'];
                       $temp[$index]['list_img']    = getImage($c['list_img'],'live', '', 0);
                       $temp[$index]['begin_time']  = date('m/d H:i',$c['begin_time']);
                       $temp[$index]['vip_id']      = $c['vip_id'];
                       $temp[$index]['live_id']     = $c['live_id'];
                       $temp[$index]['maker_id']    = self::getMakerIDByLiveID($c['live_id']);
                   }
                   $responseData['vipmessages'][] = [
                       'type'           =>  1,
                       'vip_name'       =>  $content['vip_name'],
                       'send_time'      =>  date('Y-m-d H:i',$message->send_time),
                       'messages'       =>  $temp,
                   ];
               }elseif($content['type'] == 2){

                   $temp   = [];
                   foreach($content['content'] as $index=>$c){
                       $temp[$index]['subject']     = $c['subject'];
                       $temp[$index]['live_url']    = $c['live_url'];
                       $temp[$index]['list_img']    = getImage($c['list_img'],'live', '', 0);
                       $temp[$index]['begin_time']  = date('m/d H:i',$c['begin_time']);
                       $temp[$index]['vip_id']      = $c['vip_id'];
                       $temp[$index]['live_id']     = $c['live_id'];
                       $temp[$index]['maker_id']    = self::getMakerIDByLiveID($c['live_id']);
                       $temp[$index]['price']       = '￥'.self::getLivePriceByLiveID($c['live_id']);
                   }
                   $responseData['vipmessages'][] = [
                       'type'           =>  2,
                       'vip_name'       =>  $content['vip_name'],
                       'send_time'      =>  date('Y-m-d H:i',$message->send_time),
                       'messages'       =>  $temp
                   ];
               }elseif($content['type'] == 3){

                   $temp   = [];
                   $temp['subject']                     = $content['content']['subject'];
                   $temp['vip_id']                      = $content['content']['vip_id'];
                   $temp['price']                       = '￥'.Activity::getCheapestTicket($content['content']['activity_id']).'元起';
                   $temp['activity_id']                 = $content['content']['activity_id'];
                   $temp['list_img']                    = getImage($content['content']['list_img'],'live', '', 0);
                   $temp['begin_time']                  = date('m/d H:i',$content['content']['begin_time']);
                   $temp['type']                        = 3;
                   $temp['vip_name']                    = $content['content']['vip_name'];
                   $temp['send_time']                   = date('Y-m-d H:i',$message->send_time);
                   $temp['maker_id']                    = DB::table('activity_maker')->where('activity_id',$temp['activity_id'])->first()->maker_id;
                   $responseData['vipmessages'][] = $temp;
               }
           }
       }

        return $responseData;
    }
    /*
    * 作用:获取易读时间
    * 参数:
    *
    * 返回值:
    */
    public static function getHumanTime($begin_time,$end_time)
    {
        //今天凌晨
        $today = strtotime(date('Y-m-d'));
        $tomorrow = strtotime(date('Y-m-d',strtotime('+1 day')));

        $begin_diff = ceil(($begin_time-$today)/(24*3600));
        $end_diff = ceil(($end_time-$today)/(24*3600));

        if($begin_diff >= 3){
            return '后天';
        }elseif($begin_diff ==2){
            return '明天';
        }elseif($begin_diff == 1){
            return '今天';
        }elseif($begin_diff == 0 &&  $end_diff==1){
            return '今天';
        }else{
            return '';
        }
    }
    /*
    * 作用:活动tips
    * 参数:
    *
    * 返回值:
    */
    public static function getActivityTips($begin_time,$end_time)
    {
        $today = strtotime(date('Y-m-d'));

        $begin_diff = ceil(($begin_time-$today)/(24*3600));
        $end_diff = ceil(($end_time-$today)/(24*3600));

        if($begin_diff >= 3){
            return '活动将于后天举行，请届时准时赴会参加';
        }elseif($begin_diff ==2){
            return '活动将于明天举行，请届时准时赴会参加';
        }elseif($begin_diff == 1){
            return '活动将于今天举行，请届时准时赴会参加';
        }elseif($end_diff>=1){
            return '活动已于今天举行，请届时准时赴会参加';
        }else{
            return '活动已经结束';
        }
    }

    /*
    * 作用:根据直播ID找到OVOID
    * 参数:
    *
    * 返回值:
    */
    public static function getMakerIDByLiveID($live_id)
    {
        $activity_id = DB::table('live')->where('id',$live_id)->first()->activity_id;
        $maker_id = DB::table('activity_maker')->where('activity_id',$activity_id)->first()->maker_id;
        return $maker_id;
    }
    /*
    * 作用:获取直播票价
    * 参数:
    * 
    * 返回值:
    */
    public static function getLivePriceByLiveID($live_id)
    {
        $activity_id = DB::table('live')->where('id',$live_id)->first()->activity_id;
        $live_ticket = DB::table('activity_ticket')->where('activity_id',$activity_id)->where('type',2)->first();
        return empty($live_ticket) ? 0 :$live_ticket->price;
    }


    /*
    * 作用:获取未读消息数
    * 参数:
    *
    * 返回值:
    */
    public static function unReadcounts($uid)
    {
        $data = Message::where('uid', $uid)
            ->where('is_read', 0)
            ->where('send_time', '<', time())
            ->whereIn('type', [1, 2, 3 ,7 ,8, 9])
            ->count();
        //未读取的评论
        $comments = self::getComments($uid);
        $data = (int)$data + $comments;
        return $data;
    }


    public static function getComments($uid)
    {
        $count = Message::where('uid', $uid)
            ->where('is_read', 0)
            ->whereIn('type', [4, 5, 6])
            ->get()
            ->count();

        return $count;
    }

    /**
     * author zhaoyf
     *
     * 获取后台自定义消息通知数据信息; type: agent
     * @param $agent_id
     *
     * @return array
     */
    public static function GainAgentInformInfo($agent_id)
    {
        $confirm_result = self::where('agent_id', $agent_id)
            ->where('type', 12)
            //->where('is_read', 0)
            ->select('id', 'title', 'content', 'url', 'type', 'agent_id', 'created_at')
            ->get();

        //更新已经查看过的经纪人通知消息标记
        self::where('agent_id', $agent_id)
            ->where('type', 12)
            ->update(['is_read' => 1]);

        return $confirm_result ?  $confirm_result : [];
    }

}