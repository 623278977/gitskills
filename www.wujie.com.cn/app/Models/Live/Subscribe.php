<?php
/**直播订阅模型
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/5/5
 * Time: 11:10
 */
namespace App\Models\Live;

use App\Models\Agent\Agent;
use App\Models\Agent\AgentCustomer;
use App\Models\Agent\TraitBaseInfo\RongCloud;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use App\Models\Activity\Maker as ActivityMaker;
use \DB;
use App\Models\Message;

class Subscribe extends Model
{
    use RongCloud;

    protected $dateFormat = 'U';

    protected $table = 'user_subscription';

    //黑名单
    protected $guarded = [];

    public function Live()
    {
        return $this->belongsTo('App\Models\Live\Entity', 'live_id', 'id');
    }

    /**
     * 订阅一个直播
     *
     * @return array|bool
     */
    static function subscribe(Array $data)
    {
        isset($data['path'])?$path = $data['path']:$path ='app';
        if (1 == $data['type']) {
            $exist = self::where('uid', $data['uid'])->where('live_id', $data['live_id'])->first();
            if (is_object($exist)&& $exist->status==1) {
                $result =self::where('uid', $data['uid'])->where('live_id', $data['live_id'])->update(['created_at'=>time(), 'path'=>$path]);
                return -1;
            }
            if(is_object($exist)&& $exist->status==0){
                $result =self::where('uid', $data['uid'])->where('live_id', $data['live_id'])->update(['status'=>1,'path' => $path,'created_at'=>time()]);
            }else{
                $result = self::create(
                    [
                        'uid'     => $data['uid'],
                        'live_id' => $data['live_id'],
                        'status' => 1,
                        'path' => $path,
                    ]
                );
            }

            $live = DB::table('live')->where('id', $data['live_id'])->first();
            $begin = date('Y-m-d',$live->begin_time);
            $begin = explode('-', $begin);
            $send =  mktime(10,0,0,$begin[1],($begin[2]-1),$begin[0]);
            if($send>time()){
                //发消息
                Message::create([
                    'title'=>'你订阅的直播即将开始',
                    'uid'=>$data['uid'],
//                    'content'=>'订阅的直播将于明天开播请届时准点观看',
                    'content'=>'您订阅的直播将于明天开播，准时收看喲',
                    'type'=>3,
                    'post_id'=>$data['live_id'],
                    'send_time'=>$send,
                ]);
            }

//            //todo 订阅成功发送融云消息 zhaoyf
//            //todo 获取当前投资人是否存在邀请经纪人，如果存在给对方发送消息
//            $gain_result = AgentCustomer::instance()->gainCustomerAgentRelationDatas($data['uid']);
//
//            //发送融云消息
//            if ($gain_result) {
//                $agent_result = Agent::where('id', $gain_result->agent_id)->first();
//                Subscribe::gatherInfoSends([
//                    $data['uid'],
//                    'agent'. $gain_result->agent_id, [
//                        'live_name'  => $live->subject,
//                        'begin_time' => date('Y年m月d日 H点i分', $live->begin_time),
//                    ]
//                ], 'subscribe-info-notice', 'text', 'true', 'user');
//            }

            return $result;
        }elseif(0 == $data['type']){
            $exist = self::where('uid', $data['uid'])->where('live_id', $data['live_id'])->first();
            DB::table('my_message')->where('uid', $data['uid'])->where('type',3)->where('send_time','>',time())
                ->where('post_id',$data['live_id'])->delete();
            if (!is_object($exist)) {
                return -2;
            }
            if($exist->status==0){
                return -3;
            }
            $result = self::where('uid', $data['uid'])->where('live_id', $data['live_id'])->update(['status'=>0]);
            return $result;
        }

        return false;
    }

    /**
     * 获取用户订阅列表
     *
     * @return array|bool
     */
    static function user($uid,$keywords = '',$page=1,$pageSize=10, $cache = 1,$vip_id=0)
    {
        $data = Cache::has('subscribe' . 'user' . $uid) ? Cache::get('subscribe' . 'user' . $uid) : false;
        if ($data === false || $cache) {

            $query =  DB::table('user_subscription')
                ->leftJoin('live', 'user_subscription.live_id', '=', 'live.id')
                ->where('uid', $uid)
                ->where('live.end_time','>' ,time())
                ->where('user_subscription.status',1)
                ->select('live.list_img','live.subject','live.begin_time','live.id')
                ->orderBy('live.begin_time','asc')
                ;

            if($keywords!=''){
                $query->where('subject', 'like', '%'.$keywords.'%');
            }

            if($vip_id>0){
                $query->where('live.vip_id', $vip_id);
            }

            $tommorrow  = mktime(0,0,0, date('m'),date('d')+1, date('Y'));

            $today_query = clone $query;
            $today_list = $today_query->where('begin_time','<',$tommorrow)->get();
            $today_list_ids = [];
            foreach($today_list as $k=>$v){
                $today_list_ids[] = $v->id;
            }


            if($page>1){
                $list = $query->skip(($page-1) * $pageSize)->take($pageSize)->get();
            }else{
                $list = $query->skip(($page-1) * $pageSize)
                    ->whereNotIn('live.id',$today_list_ids)
                    ->take(($pageSize-count($today_list)))->get();
                $list = array_merge($today_list,$list);
            }

            $data = array();
            foreach($list as $k=>$v){
                $v->subscribe= 1;
                $v->list_img=getImage($v->list_img,'live', '', 0);
                if($v->begin_time<$tommorrow){
                    $v->is_today=1;
                }else{
                    $v->is_today=0;
                }
                $v->begin_time_format = date('m/d H:i', $v->begin_time);
                $data[] = $v;
            }
            Cache::put('subscribe' . 'user' . $uid, $data, 1440);
        }
        return $data;
    }
}