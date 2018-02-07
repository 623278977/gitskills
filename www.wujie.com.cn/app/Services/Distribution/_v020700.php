<?php
namespace App\Services\Distribution;

use App\Models\Activity\Sign;
use App\Models\Distribution\Log as DistributionLog;

use App\Models\ScoreLog;
use App\Models\Share\Log;
use App\Models\Activity\Entity as Activity;
use App\Models\Activity\Entity\V020700 as ActivityV020700;
use App\Models\Live\Entity as Live;
use App\Models\Live\Entity\V020700 as LiveV020700;
use App\Models\Video\Entity as Video;
use App\Models\Video as VideoModel;
use App\Models\Video\Entity\V020700 as VideoV020700;
use App\Models\News\Entity as News;
use App\Models\Brand\Entity as Brand;
use App\Models\Brand\Entity\V020700 as BrandV020700;
use App\Models\User\Entity as User;
use \DB;
use App\Services\News as NewsService;
use App\Models\User\Favorite;
use Illuminate\Http\Exception\HttpResponseException;
use App\Models\Distribution\Action\V020700 as ActionV020700;
use App\Models\Distribution\ActionBind;
class _v020700
{

    /**
     * 下级分销用户
     * @param $type 类型
     * @param $post_id 目标id
     * @param $uid  用户id
     *
     * @return bool
     */
    public function subordinate($uid, $type=null, $post_id = 0, $is_all = 0)
    {
        if(!in_array($type, ['activity', 'live', 'video', 'news', 'brand']) && $type!==null){
            return false;
        }
        //如果是活动，来源是报名
        if($type==='activity'){
            $users = $this->subordinateByActivity($post_id, $uid);
        }
        //如果是直播，来源是观看
        elseif($type==='live'){
            $users = $this->subordinateByLive($post_id, $uid);
        }
        //如果是视频,来源是观看
        elseif($type==='video'){
            $users = $this->subordinateByVideo($post_id, $uid);
        }
        //如果是资讯
        elseif($type==='news'){
            $users = $this->subordinateByNews($post_id, $uid);
        }
        //如果是品牌 品牌意向
        elseif($type==='brand'){
            $users = $this->subordinateByBrand($post_id, $uid);
        }else{
            $users = $this->subordinateAll($post_id, $uid);
        }

        if($is_all){
            return ['count'=>count($users), 'users'=>$users];
        }

        return ['count'=>count($users), 'users'=>array_slice($users, 0, 6)];
    }


    protected function subordinateByActivity($post_id, $uid)
    {
        $res = DistributionLog::with(['sourceUser'=>function($query){
            $query->select('uid', 'avatar', 'nickname');}])
            ->where(['uid'=>$uid, 'relation_type'=>'activity', 'relation_id'=>$post_id])
            ->whereNotIn('source_uid', [0, $uid])
            ->whereIn('genus_type', ['enroll', 'sign'])
            ->groupBy('source_uid')->orderBy('created_at', 'desc')->get();
        $source_users = array_pluck($res->toArray(), 'source_user');

        $source_users =  $this->formatUser($source_users);

        return $source_users;
    }


    protected function subordinateByLive($post_id, $uid)
    {
        $res =  DistributionLog::with(['sourceUser'=>function($query){
            $query->select('uid', 'avatar', 'nickname');}])->where(['uid'=>$uid, 'relation_type'=>'live', 'relation_id'=>$post_id])
            ->whereNotIn('source_uid', [0, $uid])
            ->groupBy('source_uid')->orderBy('created_at', 'desc')->get();

        $source_users = array_pluck($res->toArray(), 'source_user');

        $source_users =  $this->formatUser($source_users);

        return $source_users;
    }


    protected function subordinateByVideo($post_id, $uid)
    {
        $res =  DistributionLog::with(['sourceUser'=>function($query){
            $query->select('uid', 'avatar', 'nickname');}])->where(['uid'=>$uid, 'relation_type'=>'video', 'relation_id'=>$post_id])
            ->whereNotIn('source_uid', [0, $uid])
            ->groupBy('source_uid')->orderBy('created_at', 'desc')->get();

        $source_users = array_pluck($res->toArray(), 'source_user');

        $source_users =  $this->formatUser($source_users);

        return $source_users;
    }

    protected function subordinateByNews($post_id, $uid)
    {
        $res = DistributionLog::with(['sourceUser'=>function($query){
            $query->select('uid', 'avatar', 'nickname');}])->where(['uid'=>$uid, 'relation_type'=>'news', 'relation_id'=>$post_id])
            ->whereNotIn('source_uid', [0, $uid])
            ->groupBy('source_uid')->orderBy('created_at', 'desc')->get();

        $source_users = array_pluck($res->toArray(), 'source_user');

        $source_users =  $this->formatUser($source_users);

        return $source_users;
    }


    protected function subordinateByBrand($post_id, $uid)
    {
        $res = DistributionLog::with(['sourceUser'=>function($query){
            $query->select('uid', 'avatar', 'nickname');}])->where(['uid'=>$uid, 'relation_type'=>'brand', 'relation_id'=>$post_id])
            ->whereNotIn('source_uid', [0, $uid])
            ->groupBy('source_uid')->orderBy('created_at', 'desc')->get();

        $source_users = array_pluck($res->toArray(), 'source_user');

        $source_users =  $this->formatUser($source_users);

        return $source_users;
    }


    public function subordinateAll($uid)
    {
        $res = DistributionLog::with(['sourceUser'=>function($query){
            $query->select('uid', 'avatar', 'nickname');}])
            ->whereNotIn('source_uid', [0, $uid])
            ->groupBy('source_uid')->orderBy('created_at', 'desc')->get();

        $source_users = array_pluck($res->toArray(), 'source_user');

        $source_users =  $this->formatUser($source_users);

        return $source_users;
    }


    protected function formatUser(Array $users)
    {
        $users =  array_map(function($item){
            return [
                'uid'=>$item['uid'],
                'nickname'=>$item['nickname'],
                'avatar'=> getImage($item['avatar'], 'avatar', 'large'),
            ];
        }, $users);

        return $users;
    }


    /**
     * 分享记录入库
     */
    public function createShare($uid, $content_id, $content, $source,$code, $source_uid = 0)
    {
        $exist = Log::where('code', $code)->first();
        if($exist){
            return false;
        }
        //分享记录入库
        $create = Log::create(
            [
                'uid'        => $uid,
                'content_id' => $content_id,
                'content'    => $content,
                'source'     => $source,
                'code'       => $code,
                'source_uid' => $source_uid
            ]
        );

        //相关目标实体share_num数加1
        \DB::table($content)->where('id', $content_id)->increment('share_num');


        return $create;
    }



    /**
     * 我的分享记录
     */
    public function myShare($uid, $page =1, $page_size=10)
    {
        $prefix=DB::getConfig('prefix');
        settype($uid, 'int');
        $start=($page-1)*$page_size;

        //取出分享日志中带分销的数据
        $sql="(select content_id,content,id,min(created_at) created_at from {$prefix}share_log
                left join (
                    select relation_id,relation_type from {$prefix}distribution_action_bind
                ) as bind
                on content=relation_type and content_id=relation_id
            where uid=$uid and content in ('activity', 'live', 'video', 'brand')
            group by content_id,content) as t";

        $num=DB::selectOne('select count(1) as num from '.$sql)->num;


        if($num==0 || $num<$start){
            return [];
        }
        $lists = [];

        //按照content的类型分组
        foreach(DB::select('select * from ' . $sql . " order by id desc limit $start,$page_size") as $item){
            $lists[$item->content][]=$item;
        }


        $result=[];
        foreach($lists as $type=>$item){
            $content_ids=array_pluck($item, 'content_id');
            //取出当前用户所拥有的分销日志数据
            $distribution=DistributionLog::where('uid',$uid)
                ->where('relation_type',$type)
                ->whereIn('relation_id',$content_ids)
                ->groupBy('give_type','relation_id')
                ->get([DB::raw('sum(num) as num'),'give_type','relation_id'])
                ->groupBy('relation_id');
            if ($type == 'activity') {
                $target = array_pluck(Activity::whereIn('id', $content_ids)->get(['subject','id']), 'subject','id');
            }elseif ($type == 'brand') {
                $target = array_pluck(Brand::whereIn('id', $content_ids)->get(['name','id']), 'name','id');
            }elseif ($type == 'live') {
                $target = array_pluck(Live::whereIn('id', $content_ids)->get(['subject','id']), 'subject','id');
            }elseif ($type == 'video') {
                $target = array_pluck(Video::whereIn('id', $content_ids)->get(['subject','id']), 'subject','id');
            }else{
                return false;
            }


            //item 是个数组
            foreach ($item as $content){
                if ($type == 'activity') {
                    $deadline = Activity::where('id', $content->content_id)->first()->distribution_deadline;
                }elseif ($type == 'brand') {
                    $deadline = Brand::where('id', $content->content_id)->first()->distribution_deadline;
                }elseif ($type == 'live') {
                    $deadline = Live::where('id', $content->content_id)->first()->distribution_deadline;
                }elseif ($type == 'video') {
                    $deadline = Video::where('id', $content->content_id)->first()->distribution_deadline;
                }else{
                    return false;
                }

                $data=[
                    'scores'=> 0,
                    'currency' => 0,
                    'content_name' => array_get($target, $content->content_id, ''),
                    'content' => $type,
                    'content_id' => $content->content_id,
                    'id' => $content->id,
                    'is_valid' =>$deadline>time()? 1 : 0
                ];

                //并不是覆盖，而是寻找give_type,这个字段
                if($distribution->has($content->content_id)){
                    foreach($distribution[$content->content_id] as $entity){
                        if($entity->num<=0){
                            continue;
                        }
                        if($entity->give_type=='score'){
                            $data['scores']=$entity->num;
                        }else{
                            $data['currency']=$entity->num;
                        }
                    }
                    //获得了分销的才进入最后结果
                    $result[]=$data;
                }
            }
        }

        $result = collect($result)->sortByDesc('id');

        return $result;
    }



    public function content($content, $content_id)
    {
        if ($content == 'activity') {
            $target = Activity::where('id', $content_id)->first()->subject;
        }

        if ($content == 'brand') {
            $target = Brand::where('id', $content_id)->first()->name;
        }

        if ($content == 'live') {
            $target = Live::where('id', $content_id)->first()->subject;
        }

        if ($content == 'video') {
            $target = Video::where('id', $content_id)->first()->subject;
        }

        if ($content == 'news') {
            $target = News::where('id', $content_id)->first()->title;
        }

        return $target;
    }

    /**
     * 我的分享积分收益
     */
    public function myShareScore($uid, $relation_type, $relation_id)
    {
        //该用户因该目标所产生的积分 分享 转发  观看  点击 报名 签到 品牌意向
        $share_ids = Log::where('uid', $uid)->where('content', $relation_type)
            ->where('content_id', $relation_id)->lists('id')->toArray();

        //活动报名id
        if($relation_type=='activity'){
            $sign_ids = \DB::table('activity_sign')->where('activity_id', $relation_id)->where('source_uid', $uid)->whereIn('status', [0,1])
                ->lists('id');
        }else{
            $sign_ids = [];
        }

        //品牌留言id
        if($relation_type=='brand'){
            $intent_ids = \DB::table('brand_intent')->where('source_uid', $uid)->where('brand_id', $relation_id)->lists('id');
        }else{
            $intent_ids = [];
        }



        $logs = ScoreLog::
        orWhere(function($query) use($share_ids, $uid){
            $query->whereIn('type',['share_distribution','enroll_distribution','watch_distribution','view_distribution'])
                ->whereIn('relation_id', $share_ids)->where('uid', $uid)->where('operation', 1);
        })
            ->orWhere(function($query) use($sign_ids, $uid){
                $query->whereIn('type',['enroll_distribution','sign_distribution'])->whereIn('relation_id', $sign_ids)
                    ->where('uid', $uid)->where('operation', 1);
            })
            ->orWhere(function($query) use($intent_ids, $uid){
                $query->where('type','intent_distribution')
                    ->whereIn('relation_id', $intent_ids)->where('uid', $uid)->where('operation', 1);
            })
            ->get();
        $scores = $logs->sum('num');

        return $scores;
    }

    /**
     * 我的无界币收益
     */
    public function myShareCurrency($uid, $relation_type, $relation_id)
    {
        //该用户因该目标所产生的积分 分享 转发  观看  点击 报名 签到 品牌意向
        $share_ids = Log::where('uid', $uid)->where('content', $relation_type)
            ->where('content_id', $relation_id)->lists('id')->toArray();

        //活动报名id
        if($relation_type=='activity'){
            $sign_ids = \DB::table('activity_sign')->where('activity_id', $relation_id)->where('source_uid', $uid)->whereIn('status', [0,1])
                ->lists('id');
        }else{
            $sign_ids = [];
        }

        //品牌留言id
        if($relation_type=='brand'){
            $intent_ids = \DB::table('brand_intent')->where('source_uid', $uid)->where('brand_id', $relation_id)->lists('id');
        }else{
            $intent_ids = [];
        }

        $logs = ScoreLog::
        orWhere(function($query) use($share_ids, $uid){
            $query->whereIn('type',['share_distribution','enroll_distribution','watch_distribution','view_distribution'])
                ->whereIn('relation_id', $share_ids)->where('uid', $uid)->where('operation', 1);
        })
            ->orWhere( function($query) use($sign_ids, $uid){
                $query->whereIn('type',['enroll_distribution','sign_distribution'])->whereIn('relation_id', $sign_ids)
                    ->where('uid', $uid)->where('operation', 1);
            })
            ->orWhere(function($query) use($intent_ids, $uid){
                $query->where('type','intent_distribution')
                    ->whereIn('relation_id', $intent_ids)->where('uid', $uid)->where('operation', 1);
            })
            ->get();
        $currency = $logs->sum('num');

        return $currency;
    }


    public function getEntity($content, $content_id)
    {
        if($content == 'activity'){
            $entity = ActivityV020700::getDistributionSimple($content_id);
        }elseif($content == 'live'){
            $entity = LiveV020700::getDistributionSimple($content_id);
        }elseif($content == 'video'){
            $entity = VideoV020700::getDistributionSimple($content_id);
        }elseif($content == 'brand'){
            $entity = BrandV020700::getDistributionSimple($content_id);
        }else{
            return false;
        }

        return $entity;
    }



    /**
     * 分享详情
     */
    public function shareDetail($share_id, $page, $page_size)
    {
        $shareLog = Log::getOne($share_id);

        //实体信息
        $entity = $this->getEntity($shareLog->content, $shareLog->content_id);

        //分销信息
        $actions = ActionBind::getRules($shareLog->content, $shareLog->content_id);
        //收益信息
        $achieves = DistributionLog::getAchieve($shareLog, $entity);
        //下级分销用户
        $subordinates = $this->subordinate($shareLog->uid, $shareLog->content, $shareLog->content_id);
        //分销明细
        $details = DistributionLog::getDetail($shareLog->uid, $shareLog->content_id, $shareLog->content);
//dd($entity);
        return ['entity'=>$entity, 'actions'=>$actions, 'achieves'=>$achieves, 'subordinates'=>$subordinates, 'details'=>$details];
    }


    /**
     * 分享有奖列表
     */
    public function shareList($uid, $page=1, $page_size=15, $keyword='', $expire=false)
    {
        //列表顺序,按照create_at倒序排列
        $activity = DB::table('activity')
            ->join('distribution', 'distribution.id', '=', 'activity.distribution_id')
            ->join('distribution_action', 'distribution.id', '=', 'distribution_action.distribution_id')
            ->where('distribution_action.status','enable')
            ->where('distribution_action.action','share')
            ->where('distribution.status', 'enable')
            ->where('activity.status', 1)
//            ->where('activity.end_time' , '>' ,time())
            ->where('activity.distribution_id', '>', 0)
            ->where(function($query) use ($keyword){
                if($keyword){
                    $query->where('activity.subject', 'like', '%'.$keyword.'%');
                }
            })
            ->where('activity.distribution_deadline', '>', time())
            ->select('activity.id', 'activity.created_at', 'activity.is_recommend', 'activity.is_hot')
            ->addSelect(\DB::raw("'activity' type"))
        ;

        $live = DB::table('live')
            ->join('distribution', 'distribution.id', '=', 'live.distribution_id')
            ->join('distribution_action', 'distribution.id', '=', 'distribution_action.distribution_id')
            ->where('distribution_action.status','enable')
            ->where('distribution_action.action','share')
            ->where('distribution.status', 'enable')
//            ->where('live.end_time' , '>' ,time())
            ->where('live.distribution_id', '>', 0)
            ->where('live.status', 0)
            ->where(function($query) use ($keyword){
                if($keyword){
                    $query->where('activity.subject', 'like', '%'.$keyword.'%');
                }
            })
            ->where('live.distribution_deadline', '>', time())
            ->select('live.id', 'live.created_at', 'live.is_recommend', 'live.is_hot')
            ->addSelect(\DB::raw("'live' type"))
        ;
        if(!$expire){
            $activity->where('activity.end_time' , '>' ,time());
            $live->where('live.end_time' , '>' ,time());
        }
        $brand = DB::table('brand')
            ->join('distribution', 'distribution.id', '=', 'brand.distribution_id')
            ->join('distribution_action', 'distribution.id', '=', 'distribution_action.distribution_id')
            ->where('distribution_action.status','enable')
            ->where('distribution_action.action','share')
            ->where('distribution.status', 'enable')
            ->where('brand.distribution_id', '>', 0)
            ->where('brand.status', 'enable')
            ->where(function($query) use ($keyword){
                if($keyword){
                    $query->where('brand.name', 'like', '%'.$keyword.'%');
                }
            })
            ->where('brand.distribution_deadline', '>', time())
            ->select('brand.id', 'brand.created_at', 'brand.is_recommend', 'brand.is_hot')
            ->addSelect(\DB::raw("'brand' type"))
        ;

        $video = DB::table('video')
            ->join('distribution', 'distribution.id', '=', 'video.distribution_id')
            ->join('distribution_action', 'distribution.id', '=', 'distribution_action.distribution_id')
            ->where('distribution_action.status','enable')
            ->where('distribution_action.action','share')
            ->where('distribution.status', 'enable')
            ->where('video.distribution_id', '>', 0)
            ->where('video.status', 1)
            ->where(function($query) use ($keyword){
                if($keyword){
                    $query->where('video.subject', 'like', '%'.$keyword.'%');
                }
            })
            ->where('video.distribution_deadline', '>', time())
            ->select('video.id', 'video.created_at', 'video.is_recommend', 'video.is_hot')
            ->addSelect(\DB::raw("'video' type"))
        ;

        $list= $activity
            ->union($live)
            ->union($brand)
            ->union($video)
            ->skip(($page-1) * $page_size)
            ->take($page_size)
            ->orderBy('created_at', 'desc')
            ->orderBy('is_recommend','desc')
            ->orderBy('is_hot','desc')
            ->get()
        ;


        $list = array_map($this->formatData($uid), $list);
        return $list;
    }


    /*
    * 格式化数据
    */
    private function formatData($uid)
    {
        $func = function ($obj) use ($uid) {

            switch ($obj->type) {
                case 'activity':
                    $activity_obj = ActivityV020700::find($obj->id);
                    $obj->activity = $this->getActivityData($activity_obj, $uid);
                    break;
                case 'live':
                    $obj->live = $this->getLiveData($obj, $uid);
                    break;
                case 'video':
                    $obj->video = $this->getVideoData($obj, $uid);
                    break;
                case 'brand':
                    $obj->brand = $this->getBrandData($obj, $uid);
                    break;
                default:
                    break;
            }
            return $obj;
        };

        return $func;
    }


    private function getActivityData($obj, $uid)
    {
        $return = ActivityV020700::getPublicData($obj, $uid, 1);
        $return['isFavorite'] = Favorite::isFavorite('activity',$obj->id, $uid);
        //分享相关
        $return['share_image'] = getImage($obj->share_image?:'images/share_image.png', '', '');
        return $return;
    }


    private function getLiveData($obj, $uid)
    {
        return LiveV020700::getPublicData($obj, $uid, 1);
    }


    private function getVideoData($obj, $uid)
    {
        $return =  VideoV020700::getPublicData($obj, $uid, 1);
        $return['isFavorite'] = Favorite::isFavorite('video',$obj->id, $uid);
        return $return;
    }


    private function getBrandData($obj, $uid)
    {
        $return =  BrandV020700::getPublicData($obj, $uid, 1);
        $return['isFavorite'] = Favorite::isFavorite('brand', $obj->id, $uid);
        return $return;
    }
}