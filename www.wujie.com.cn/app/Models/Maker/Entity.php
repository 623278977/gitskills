<?php
/**ovo模型
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/5/5
 * Time: 11:10
 */
namespace App\Models\Maker;

use App\Models\Activity\Live;
use App\Models\Activity\Maker;
use App\Models\GroupChat;
use App\Models\Zone;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class Entity extends Model
{

    protected $dateFormat = 'U';

    protected $table = 'maker';

    //黑名单
    protected $guarded = [];


    public function zone()
    {
        return $this->belongsTo('App\Models\Zone', 'zone_id', 'id');
    }
    public function users()
    {
        return $this->belongsToMany('App\Models\User\Entity','maker_member','maker_id','uid');
    }
    public function citypartner()
    {
        return $this->hasOne('App\Models\CityPartner\Entity','uid','partner_uid');
    }
    static function getRows($where,$page=0,$pageSize=10){
        $query=self::where($where)->where('status',1);
        if($pageSize){
            return $query->skip($page*$pageSize)->take($pageSize)->get();
        }else{
            return $query->get();
        }
    }

    static function getRow($where){
        return self::where($where)->where('status',1)->first();
    }
    static function getBase($maker){
        if(!isset($maker->id)) return array();
        $data=array();
        $data['id']=$maker->id;
        $data['subject']=$maker->subject;
        $data['image']=$maker->id ? getImage($maker->image,'maker') : getImage($maker->image,'maker','');
        $data['logo']=$maker->id ? getImage($maker->logo,'maker') : getImage($maker->logo,'maker','');
        $data['address']=$maker->address;
        $data['tel']=$maker->tel;
        $data['description']=$maker->description;
        $data['uid']=$maker->uid;
        $data['groupid']=$maker->groupid;
        $data['zone_id']=$maker->zone_id;
        $data['alpha']=$maker->zone?$maker->zone->alpha:'';
        $data['zone']=Zone::getZone($maker->zone_id);
        return $data;
    }

    /**
     * @param $maker_id
     * @return array
     * 根据maker_id  获取直播数据
     */
    static function getLiveByMaker($maker_id){
        if(empty($maker_id) && $maker_id!=0) return array();
        $builder =DB::table('live')->where('live.begin_time','<',time())->where('live.end_time','>',time())
            ->Join('activity_maker','activity_maker.activity_id','=','live.activity_id');
        if($maker_id == 0){
            $lives = $builder->select('live.*')
                ->distinct()
                //->get();
                ->first();//暂时选择一个ovo
            if($lives){
                //$return = [];
                //foreach($lives as $live){
                //    if(isset($live->id)){
                //        $data=Live::getBase($live);
                //        $return[] = $data;
                //    }
                //}
                //return $return;
                if(isset($lives->id)){
                    $data=Live::getBase($lives);
                    return $data;
                }
            }
        }else{
            $live = $builder->where('activity_maker.maker_id',$maker_id)
                ->select('live.*')
                ->first();
            if(isset($live->id)){
                $data=Live::getBase($live);
                return $data;
            }
        }
        return array();
    }

    /**
     * @param $maker_id
     * 根据maker_id 获取群聊数据
     * $uid 用户ID，指定后将判断是否报名活动
     */
    static function getGroupChatByMaker($maker_id,$uid=false){
        if(empty($maker_id)) return array();
        $groupChats=DB::table('group_chat')
            ->join('activity_maker','activity_maker.activity_id','=','group_chat.activity_id')
            ->where('activity_maker.maker_id',$maker_id)
            ->orderBy('group_chat.sendtime','desc')
            ->get();
        $data=array();
        if(count($groupChats)){
            foreach($groupChats as $k=>$v){
                $data[$k]=GroupChat::getBase($v);
                if($uid){
                    $data[$k]['follow']= \App\Models\Activity\Entity::follow($v->activity_id, $uid);
                }
            }
        }
        return $data;
    }

    static function getMembers($where, $page,$page_size){
        $query=DB::table('user')
            ->leftjoin('maker_member','user.uid','=','maker_member.uid')->where($where);
        if(array_key_exists('user_industry.industry_id',$where)){
            $query->leftjoin('user_industry','user_industry.uid','=','maker_member.uid');
        }
        $count_query = clone $query;
        $members=$query->select('user.uid','user.nickname','user.avatar','user.username')
            ->skip(($page-1) * $page_size)->take($page_size)->get();
        $count = $count_query->count();
        $data=array();
        if(count($members)){
            foreach($members as $k=>$v){
                $data[$k]=\App\Models\User\Entity::getUser($v);
                $data[$k]['count']=(string)$count;
            }
        }
        return $data;
    }

    /*
     * 找到最近的网点,没有选择虚拟ovo
     */
    static function findNearByMaker($zone_id,$uid = 0){
        //if($uid){
        //    $maker = \DB::table('maker_member')
        //        ->join('maker','maker.id','=','maker_member.maker_id')
        //        ->where('maker_member.uid',$uid)
        //        ->first();
        //    if($maker){
        //        return $maker;
        //    }
        //}
        $maker = self::where('status',1)->where('zone_id',$zone_id)->first();
        if(!$maker){
            $sonids = Zone::getZoneIds($zone_id);
            if($sonids){
                foreach($sonids as $son){
                    $maker = self::where('status',1)->where('zone_id',$son)->first();
                    if($maker){
                        return $maker;
                    }
                }
            }
            if(!$maker){
                //return self::where('status',1)->where('subject','like','%杭州乐富智汇园%')->first();
                return (object)config('system.virtual_ovo');
            }

        }
        else if(count($maker)==1){
            return $maker;
        }else{
            //获取用户最近的网点
            //todo
            return $maker;
        }
    }

    /*
     * 获取网店信息
     */
    static function getMakerInfo($ids){

        $return = [];

        if(empty($ids)){
            return [];
        }

        foreach($ids as $id){

            if($obj = self::find($id)){
                $return[] = self::getBase($obj);
            }

        }

        return $return;
    }
}