<?php

namespace App\Models\Vip;

use App\Models\Zone;
use Illuminate\Database\Eloquent\Model;
use \Cache;
use \DB;
use \Auth;
use App\Models\Live\Entity as Live;
use App\Models\Activity\Entity as Activity;
use App\Models\User\Entity as User;
use App\Models\Maker\Entity as Maker;
use App\Models\Video;
use App\Http\Controllers\Api\ActivityController;
use Illuminate\Http\Request;

class Entity extends Model
{
    protected $table = 'vip';

    public function __construct()
    {
        parent::__construct();
        $this->user = Auth::user();
    }


    static function getRow($where)
    {
        return self::where($where)->first();
    }


    static function getRowByTerm($term_id)
    {
        return self::leftJoin('vip_term', 'vip.id', '=', 'vip_term.vip_id')->select('vip.status','vip.name')
        ->where(['vip_term.id'=>$term_id])->first();
    }

    /**
     *获取专版详情
     */
    static function recommend($vip_id, Array $array, $uid,$position_id = 0, $cache = 1)
    {
        $data = Cache::has('vip_detail_recommend' . $vip_id) ? Cache::get('vip_detail_recommend' . $vip_id) : false;
        $user = User::getRow(['uid'=>$uid]);
        $position_id >0 ?$city = Zone::getRow(['id'=>$position_id]):$city='';
        is_object($user) >0 ?$maker = Maker::getRow(['id'=>$user->maker_id]):$maker='';
        $city = $maker_name = '';
        if($position_id >0){
            $zone = Zone::getRow(['id'=>$position_id]);
            $city = str_replace('市','',$zone->name);;
        }

        if(is_object($user)){
            $maker = Maker::getRow(['id'=>$user->maker_id]);
            $maker_name = $maker->subject;
        }
        if ($data === false || $cache) {
            //该专版下的活动
            $activity_a = [];
            if(is_object($user)){
                $activity_a = Activity::getActivityOfYourMaker($user->maker_id, [], 1, $vip_id);
            }
            $exclusion = [];
            array_walk($activity_a, function ($item) use (&$exclusion) {
                $exclusion[] = $item->id;
            }
            );
            $activity_b = [];
            if($position_id>0){
                $activity_b = Activity::getActivityOfYourCity($position_id, $exclusion, 1, $vip_id);
            }
            //step4获取C类活动
            array_walk($activity_b, function ($item) use (&$exclusion) {
                $exclusion[] = $item->id;
            }
            );
            if(($array['activity']-count($activity_b)-count($activity_a))>0){
                $c_size = ($array['activity']-count($activity_b)-count($activity_a));
            }else{
                $c_size = 0;
            }


            $activity_c = Activity::getActivityOfAll($exclusion,$c_size, 1, $vip_id);
            $activities =  array_slice(array_merge($activity_a,$activity_b,$activity_c),0,$array['activity']);
//            $activities = call_user_func_array(array(new ActivityController(), 'postListthree'),[new Request(['maker_id'=>$user->maker_id,
//                                                                                      'position_id'=>$position_id, 'vip_id'=>$vip_id, 'is_call'=>1])]);




            //该专版下的视频
            $videos = Video::lists([], $vip_id, ['order'=>'zhineng'], $uid, 0, $array['video']);
            //该专版下的直播
            $lives = Live::lists($uid, '', 1, $array['live'], $vip_id,1,0,1);

            //该专版相关的专版
            $vips = self::vips($vip_id, $array['vip']);




            $data = array('activities' => $activities, 'videos' => $videos, 'lives' => $lives, 'vips' => $vips,'maker_name'=>$maker_name,'city'=>$city);
            Cache::put('vip_detail_recommend' . $vip_id, $data, 1440);
        }

        return $data;
    }



    /**
     *获取专版下的专版列表
     */
    static function vips($vip_id, $limit = 0, $cache = 1)
    {
        $data = Cache::has('vip_detail_vips' . $vip_id) ? Cache::get('vip_detail_vips' . $vip_id) : false;
        if ($data === false || $cache) {
            //从活动表获取
            $query = DB::table('activity')->select(DB::raw("lab_activity.vip_id,max(lab_activity.created_at) as max_created_at"))
                ->orderBy('max_created_at', 'desc')->where('status', 1)->where('vip_id', '>', 0)->groupBy('vip_id');
            if ($limit > 0) {
                $activities = $query->limit($limit+1)->lists('vip_id', 'max_created_at');
            } else {
                $activities = $query->lists('vip_id', 'max_created_at');
            }
            //从视频表获取
            $query = DB::table('video')->select(DB::raw("lab_video.vip_id,max(lab_video.created_at) as max_created_at"))
                ->orderBy('max_created_at', 'desc')->where('status', 1)->where('vip_id', '>', 0)->groupBy('vip_id');
            if ($limit > 0) {
                $videos = $query->limit($limit+1)->lists('vip_id', 'max_created_at');
            } else {
                $videos = $query->lists('vip_id', 'max_created_at');
            }
            //从直播表获取
            $query = DB::table('live')->select(DB::raw("lab_live.vip_id,max(lab_live.created_at) as max_created_at"))
                ->orderBy('max_created_at', 'desc')->where('status', 1)->where('vip_id', '>', 0)->groupBy('vip_id');
            if ($limit > 0) {
                $lives = $query->limit($limit+1)->lists('vip_id', 'max_created_at');
            } else {
                $lives = $query->lists('vip_id', 'max_created_at');
            }
            $arr = array_merge(array_keys($activities), array_keys($videos), array_keys($lives));
            arsort($arr);
//            if ($limit > 0) {
//                $arr = array_slice($arr, 0, $limit+1);
//            }
            $vip_arr = array();
            foreach ($arr as $k => $v) {
                if (in_array($v, array_keys($activities))) {
                    $vip_arr[] = $activities[$v];
                }

                if (in_array($v, array_keys($videos))) {
                    $vip_arr[] = $videos[$v];
                }

                if (in_array($v, array_keys($lives))) {
                    $vip_arr[] = $lives[$v];
                }
            }
            $vip_arr = array_unique(array_values($vip_arr));

            //排除自己
            if (in_array($vip_id, $vip_arr)) {
                unset($vip_arr[array_search($vip_id, $vip_arr)]);
            }

            if ($limit > 0) {
                $vip_arr = array_slice($vip_arr, 0, $limit);
            }
            $vips = array();
            foreach ($vip_arr as $k => $v) {
                $vips[] = self::detail($v,0, 1);
            }
            $data = $vips;
            Cache::put('vip_detail_vips' . $vip_id, $data, 1440);
        }

        return $data;
    }

    /**
     *获取专版详情
     * 当$attach=1时获取该专版下的活动、视频、直播数目
     * 当$rights=1时获取该专版下的会员权益
     * 当$package=1时获取该专版下的套餐信息
     */
    static function detail($id,$uid = 0, $attach = 0, $agreement = 0, $package = 0, $cache = 1)
    {
        $data = Cache::has('vip_detail' . $id) ? Cache::get('vip_detail' . $id) : false;
        if ($data === false || $cache) {
            $data = new \stdClass();
            $data = DB::table('vip')
                ->where('id', $id);

            if ($agreement) {
                $data->select('id','name', 'detail', 'poster', 'agreement', 'subtitle', 'groupid', 'status');
            } else {
                $data->select('id','name', 'detail', 'poster', 'subtitle', 'groupid', 'status');
            }

            $data = $data->first();
            if ($attach) {
                $activity_count = DB::table('activity')->where('vip_id', $id)->where('status', 1)->count();
                $video_count = DB::table('video')->where('vip_id', $id)->where('status', 1)->count();
//                $live_count = DB::table('live')->where('vip_id', $id)->where('status', 1)->count();
                $lives = Live::lists($uid, '', 1, 10, $id,1,0,-1);
                $live_count = count($lives);
                $data->activity_count = $activity_count;
                $data->video_count = $video_count;
                $data->live_count = $live_count;
            }

            if ($package) {
                $vip = DB::table('vip')->where('id', $id)->first();
                $terms = DB::table('vip_term')->where('vip_id', $id)->select('is_recommend','id', 'name', 'price', 'number', 'unit', 'status')
                    ->get();
                //如果该专版禁止购买，则名下套餐全部返回不可用
                $vip->status=='disable'? $status=0: $status=1;
                $terms = self::dealTerms($terms, ['unit', 'number'],$status);
                $data->package = $terms;
            }
            if($uid>0){
                $data->is_valid = self::valid($id, $uid);
            }
            $data->poster = getImage($data->poster,'activity','',0);
            $data->status=='enable'?$data->status=1:$data->status=0;

            Cache::put('vip_detail' . $id, $data, 1440);
        }

        return $data;
    }

    /**
     * 获取某会员对某专版是否还处于有效期之内
     */
    static function valid($vip_id, $uid)
    {
        $vips = DB::table('user_vip')->where('vip_id', $vip_id)->where('uid', $uid)->lists('end_time');
        rsort($vips);

        if (isset($vips[0]) && $vips[0] > time()) {
            $result = 1;
        } else {
            $result = 0;
        }

        return $result;
    }

    /*
    * 作用:获取专版列表
    * 参数:
    * 
    * 返回值:
    */
    public static function getAllLists($uid)
    {
        $vips = self::select('id', 'name', 'subtitle', 'poster', 'detail','backdrop')->orderBy('sort', 'desc')->get();
        foreach ($vips as $vip) {
            $vip->poster = getImage($vip->backdrop);
            $vip->vip_url = createUrl('special/detail', array('vip_id' =>$vip->id, 'uid' => $uid, 'pagetag' => config('app.special_detail')));
        }

        return $vips;
    }

    static function dealTerms(Array $array, Array $except, $status=1)
    {
        $data = array();
        $periods = array('day' => '天', 'week' => '周', 'month' => '个月', 'year' => '年');
        foreach ($array as $k => $v) {
            foreach ($v as $key => $val) {
                if ($val == 'yes' || $val == 'enable' && $status==1) {
                    $v->$key = 1;
                } elseif ($val == 'disable' || $val == 'no'||$status==0) {
                    $v->$key = 0;
                }

                if (!in_array($key, $except)) {
                    $data[$k][$key] = $v->$key;
                }

                if (isset($v->number) && isset($v->unit)) {
                    $data[$k]['period'] = $v->number . $periods[$v->unit];
                }
            }
        }

        return $data;
    }

    /**
     * 获取某人的专版套餐购买记录
     */
    public static function records($uid, $vip = 0)
    {
        $periods = array('day' => '天', 'week' => '周', 'month' => '个月', 'year' => '年');

        $query = DB::table('user_vip')
            ->leftJoin('vip', 'user_vip.vip_id', '=', 'vip.id')
            ->leftJoin('vip_term', 'user_vip.vip_term_id', '=', 'vip_term.id')
            ->select(
                'vip.name',
                'vip.id',
                'user_vip.start_time',
                'user_vip.end_time',
                'user_vip.vip_term_id',
                'vip_term.name as vip_term_name'
            )
            ->where('user_vip.uid', $uid)
            ->orderBy('user_vip.end_time', 'desc');
        if ($vip) {
            $query->where('user_vip.vip_id', $vip);
        }

        $records = $query->get();
        foreach ($records as $k => $v) {
            $v->start_time = date('Y/m/d', $v->start_time);
            ($v->end_time<time()) ?$v->is_expire=1 : $v->is_expire=0;
            $v->end_time = date('Y/m/d', $v->end_time);
            $v->package_name = $v->vip_term_name;
            if(time()<=$v->end_time && time()>=$v->begin_time){
                $v->is_using=1;
            }else{
                $v->is_using=0;
            }
            unset($v->vip_term_id);
        }

        return $records;
    }

}
