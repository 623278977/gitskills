<?php
/**活动签到模型
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/5/5
 * Time: 11:10
 */
namespace App\Models\Activity;

use App\Models\Activity\Entity as Activity;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use App\Models\Maker\Entity as MakerModel;
use Closure;
use App\Models\Agent\Agent;
use App\Models\Agent\Activity\Sign as AgentActivitySign;

class Sign extends Model
{

    protected $dateFormat = 'U';

    protected $table = 'activity_sign';

    //黑名单
    protected $guarded = [];

    /**
     * 关联：活动
     */
   public function hasOneActity()
   {
       return $this->hasOne(Activity::class, 'id', 'activity_id');
   }

   /*
    * 关联：会场
    * */
   public function belongsToMaker(){
       return $this->belongsTo(MakerModel::class,'maker_id','id');
   }

   /*关联经纪人*/
   public function agent(){
       return $this->belongsTo(Agent::class,'agent_id','id');
   }

    public static function inviteSign($uid)
    {
        $count = self::
//        join('distribution_log', function ($join) {
//            $join->on('activity_sign.id', '=', 'distribution_log.genus_id')
//                ->where('distribution_log.genus_type', 'apply'); //只連接報名的
//                    })->
        where('source_uid', $uid)->where('status', 1)->count();

        return $count;
    }


    static function getCount($where)
    {
//        //新活动分标签统计
//        $countNew = self::where($where)
//            ->where('tag_id', '>', '0')
//            ->groupBy('tag_id')
//            ->count();
//        //旧活动直接统计
//        $countOld = self::where($where)
//            ->where('tag_id', '0')
//            ->count();
//
//        $count = $countNew+$countOld;
//
//        return $count;
        return self::where($where)->count();//这是原来的统计
    }


    public static function getRow($where ,Closure $callback = NULL)
    {
        $builder = self::where($where);
        if($callback){
            $builder = $callback($builder);
        }
        return $builder->first();
    }

    public static function getRows($where, $whereIn, Closure $callback = NULL)
    {
        $builder = self::where($where);
        if ($callback) {
            $builder = $callback($builder);
        }
        if ($whereIn) {
            $builder->whereIn($whereIn);
        }
//        return $builder->first();
        return $builder->get();
    }

    /**
     * 活动报名   --数据中心版
     * @User yaokai
     * @param $uid
     * @param $maker_id
     * @param $activity_id
     * @param $company
     * @param $job
     * @param $ticket_no
     * @param int $status
     * @param $name
     * @param $tel 伪手机号
     * @param $non_reversible  手机号加密的值
     * @param int $source_uid
     * @param int $is_invite
     * @param int $agent_id
     * @return int|static
     */
    static function apply($uid, $maker_id, $activity_id,$company, $job, $ticket_no,$status=-1,$name,$tel, $non_reversible, $source_uid=0, $is_invite = 0, $agent_id = 0)
    {
        $exist = self::where('uid', $uid)->where('maker_id', $maker_id)->where('ticket_no', $ticket_no)
            ->where('activity_id', $activity_id)->first();

        if (is_object($exist)) {
            return -1;
        }


        $result = self::create(
            [
                'uid' => $uid,
                'activity_id' => $activity_id,
                'company' => $company,
                'job' => $job,
                'maker_id' => $maker_id,
                'ticket_no' => $ticket_no,
                'status' => $status,
                'name' => $name,
                'tel' => $tel,
                'non_reversible' => $non_reversible,
                'source_uid' => $source_uid,
                'is_invite' => $is_invite,
                'agent_id' => $agent_id,
            ]
        );

        return $result;
    }


    /**
     * 查询某用户是不是首次成功报名
     */
    static function first($uid)
    {
        $apply = self::where('uid', $uid)->where('status', 0)->get();
        $sign = self::where('uid', $uid)->where('status', 1)->get();
        if(count($sign)==0 && count($apply)==1){
            return true;
        }else{
            return false;
        }
    }


    /**
     * 查询某用户有没有累积二十次报名活动
     */
    static function accumulate($uid)
    {
        $apply = self::where('uid', $uid)->groupBy('activity_id')->whereIn('status', [0,1])->get();
        if(count($apply)>=20){
            return true;
        }else{
            return false;
        }
    }


    /**
     * 更新某些字段
     */
    public static function updateBy(Array $where, Array $update)
    {
        $result = self::where($where)->update($update);

        return $result;
    }

	public function user()
    {
        return $this->hasOne('App\Models\User\Entity','uid','uid');
    }


    /**
     * 活动报名信息       --数据中心版
     * @User yaokai
     * @param $id
     * @param $uid
     * @return array
     */
    static function getSignUserData($id , $uid)
    {
        $builder = \DB::table('activity_sign as las')
            ->leftjoin('user as u','las.uid','=','u.uid')
            ->leftjoin('activity_ticket as at','at.id','=','las.ticket_id')
            ->leftjoin('user_ticket as ut','ut.ticket_no','=','las.ticket_no')
//            ->where('at.activity_id',$id)
            //->where('at.type','>',-1)
            ->where('las.activity_id',$id)
            ->whereIn('las.status',[0,1,'-3']);

        $data = $builder
            ->select(
                'las.name',
                'las.image',
                'u.realname',
                'u.nickname',
                'u.username',
                'u.non_reversible',
                'u.avatar',
                'las.created_at',
                'at.price',
                'at.type',
                'las.tel',
                'las.non_reversible',
                'ut.price as real_ticket_price',
                'ut.type as real_ticket_type'
                //\DB::raw("(select concat_ws('-',type,price) from lab_user_ticket as ut WHERE ut.activity_id = $id AND ut.uid = lab_u.uid and ut.type > -1) as user_ticket")
            )
            ->orderBy('las.created_at','desc')
            ->get();

        return $data?:[];
    }

    /**
     * 统计某个活动的报名数
     * @param $activity 活动id
     * return $count  报名数
     */
    public static function signCount($activity)
    {
        $count = self::where('activity_id',$activity)
            ->count();

        //经纪人端报名人数
        $sign_count = AgentActivitySign::where('activity_id',$activity)
            ->count();

        $count = $count+$sign_count;

        return $count;
    }


}