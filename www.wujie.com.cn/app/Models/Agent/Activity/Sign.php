<?php
/**活动签到模型
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/5/5
 * Time: 11:10
 */
namespace App\Models\Agent\Activity;

use App\Models\Activity\Entity as Activity;
use App\Models\User\Entity as User;
use App\Models\Activity\Sign as ActivitySign;
use App\Models\Activity\Ticket as ActivityTicket;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use App\Models\Maker\Entity as MakerModel;
use Closure;
use App\Models\Agent\Agent;

class Sign extends Model
{
    protected $dateFormat = 'U';

    protected $table = 'agent_activity_sign';

    //黑名单
    protected $guarded = [];

    /**
     * 关联：活动
     */
   public function actity()
   {
       return $this->hasOne(Activity::class, 'id', 'activity_id');
   }

   /*
    * 关联：会场
    * */
   public function maker(){
       return $this->belongsTo(MakerModel::class,'maker_id','id');
   }



    public function ticket(){
        return $this->hasOne(ActivityTicket::class, 'id', 'ticket_id');
    }



    /**
     * 活动报名
     *
     * @return array|bool
     */
    static function apply($agent_id, $maker_id, $activity_id,$company, $job, $ticket_no, $ticket_id,$status=-1,$name,$tel,$non_reversible)
    {
        $exist = self::where('agent_id', $agent_id)->where('maker_id', $maker_id)->where('ticket_no', $ticket_no)
            ->where('activity_id', $activity_id)->first();

        if (is_object($exist)) {
            return -1;
        }

        $result = self::create(
            [
                'agent_id' => $agent_id,
                'activity_id' => $activity_id,
                'company' => $company,
                'job' => $job,
                'maker_id' => $maker_id,
                'ticket_no' => $ticket_no,
                'status' => $status,
                'name' => $name,
                'tel' => $tel,
                'non_reversible' => $non_reversible,
                'ticket_id' => $ticket_id,
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


	public function user()
    {
        return $this->hasOne('App\Models\User\Entity','uid','uid');
    }


    /**
     * 作用:根据activity_id， 获取已经报名了的会员头像  --数据中心版
     * 参数:$id 活动id
     *
     * 返回值:int
     */
    public static function getSignUsers($activity_id)
    {
        $anony_signs = ActivitySign::select('uid', 'image', 'name', 'tel','non_reversible', 'created_at')
            ->addSelect(\DB::raw("'user' type"))
            ->where('activity_id', $activity_id)
            ->where('status', -3);

        $agent_signs = self::select('agent_id as uid', 'image', 'name', 'tel', 'non_reversible', 'created_at')
            ->addSelect(\DB::raw("'agent' type"))
            ->where('activity_id', $activity_id)
            ->whereIn('status', [0, 1]);

        $signs = ActivitySign::select('uid', 'image', 'name', 'tel','non_reversible', 'created_at')
            ->where('activity_id', $activity_id)
            ->where('uid', '<>', 0)
            ->whereIn('status', [0, 1])
            ->union($anony_signs)
            ->union($agent_signs)
            ->orderBy('created_at', 'desc')
            ->addSelect(\DB::raw("'user' type"))
            ->get();


        $sign_users = [];
        foreach ($signs as $key => $val) {
            if ($val->uid > 0) {
                if($val->type=='user'){
                    $user = User::where('status', 1)
                        ->where('uid', $val->uid)->select('avatar', 'non_reversible')->first();
                }else{
                    $user = Agent::where('status', 1)
                        ->where('id', $val->uid)->select('avatar', 'non_reversible')->first();
                }
                if (is_object($user)) {
                    $sign_users[$key]['image'] = getImage($user->avatar, 'avatar', '', 0);
                    $user->avatar ? $sign_users[$key]['has_image'] = 1 : $sign_users[$key]['has_image'] = 0;

                    $sign_users[$key]['name'] = $val->name;
                    //如果 是代报名
                    if ($user->non_reversible != $val->non_reversible) {
                        $sign_users[$key]['image'] = \Illuminate\Support\Facades\URL::asset('/') . "images/default/avator-m.png";
                        $sign_users[$key]['has_image'] = 0;
                    }
                }
            } else {
                $val->image && $sign_users[$key]['image'] = getImage($val->image, 'user', '', 0);
                $val->image && $sign_users[$key]['has_image'] = 1;
                $val->image && $sign_users[$key]['name'] = $val->name;
            }
        }



        $sign_users = collect($sign_users)->sortByDesc('has_image');

        return $sign_users;
    }

    /**
     * author zhaoyf
     *
     * 根据指定经纪人ID获取活动，然后发送通知信息
     *
     * @param   param    经纪人ID array
     *
     * @return data_list
     */
    public static function sendInform($param)
    {
        $confirm_result = array();

        //根据指定经纪人ID获取活动报名信息
        $sign_result = self::with('actity', 'maker')
            ->where([
                'agent_id' => $param['agent_id'],
                'status'   => 0,
            ])->get();

        //对结果进行处理，然后返回结果
        if ($sign_result) {
            foreach ($sign_result as $key => $value) {
                $confirm_result[$key] = [
                    'time'  => strtotime($value->created_at),
                    'type'  => 'activity_info',
                    'cont'  => [
                        'title'             => '【活动报名】 成功报名：' .$value->actity->subject,
                        'activity_sign_id'  => $value->id,
                        'activity_id'       => $value->actity->id,
                        'activity_name'     => $value->actity->subject,
                        'activity_time'     => $value->actity->begin_time,
                        'activity_make'     => $value->maker->subject,
                        'activity_describe' => trans('notification.activity_describe'),
                    ]
                ];
            }
        }

        return $confirm_result;
    }

}