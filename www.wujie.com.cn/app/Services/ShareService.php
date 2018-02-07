<?php

namespace App\Services;

use App\Models\ScoreLog;
use App\Models\Share\Log;
use App\Models\Distribution\Log as DistributionLog;
use App\Models\Activity\Entity as Activity;
use App\Models\Live\Entity as Live;
use App\Models\Video\Entity as Video;
use App\Models\Video as VideoModel;
use App\Models\News\Entity as News;
use App\Models\Brand\Entity as Brand;
use App\Models\User\Entity as User;
use App\Models\Distribution\Entity as Distribution;
use \DB;
use App\Services\News as NewsService;
use App\Services\Version\Index\_v020500;
use App\Models\User\Favorite;
class ShareService
{
    /**
     * 分享记录入库
     */
    public function createShare($uid, $content_id, $content, $source,$code, $source_uid = 0)
    {
//        $distribution_id = \DB::table($content)->where('id', $content_id)->value('distribution_id');
//        if ($distribution_id != 0){
            $exist = Log::where('code', $code)->first();
            if($exist){
                return false;
            }
//        }

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
            where uid=$uid
            group by content_id,content) as t";
        $num=DB::selectOne('select count(1) as num from '.$sql)->num;


        if($num==0 || $num<$start){
            return [];
        }
        $lists = [];
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
            }elseif ($type == 'news') {
                $target = array_pluck(News::whereIn('id', $content_ids)->get(['title','id']), 'title','id');
            }
            foreach ($item as $content){
                $data=[
                    'scores'=> 0,
                    'currency' => 0,
                    'content_name' => array_get($target, $content->content_id, ''),
                    'content' => $type,
                    'content_id' => $content->content_id,
                    'id' => $content->id,
                    'min_created_at' => $content->created_at,
                ];
                if($distribution->has($content->content_id)){
                    foreach($distribution[$content->content_id] as $entity){
                        if($entity->give_type=='score'){
                            $data['scores']=$entity->num;
                        }else{
                            $data['currency']=$entity->num;
                        }
                    }
                }
                $result[]=$data;
            }
        }
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

    /**
     * 我的分享记录  --疑似弃用  数据中心暂不处理
     * @User yaokai
     * @param $share_id
     * @param $page
     * @param $page_size
     * @return array
     */
    public function shareDetail($share_id, $page, $page_size)
    {
        $share = Log::find($share_id);
        $result=[];
        if(!$share){
            return $result;
        }
        $distributionLog = DistributionLog::where('relation_type', $share->content)
                ->where('relation_id', $share->content_id)
                ->where('uid', $share->uid)
                ->orderBy('id','desc')
                ->paginate($page_size);
        $types = ['score' => '积分', 'currency' => '无界币'];
        $share_ids = $sign_ids = $intent_ids = [];
        $action = array_pluck(\App\Models\Distribution\Action::whereIn('id', array_pluck($distributionLog, 'distribution_action_id'))
                        ->get(['id', 'action']), 'action', 'id');

        foreach($distributionLog as $item){
            $type='share';
            if(isset($action[$item->distribution_action_id])){
                switch($action[$item->distribution_action_id]){
                    case 'share':
                    case 'relay':
                    break;
                    case 'watch':
                        $type='share_watch_ten';
                        break;
                    case 'enroll':
                        $type='enroll_share';
                        break;
                    case 'sign':
                        $type='sign_share';
                        break;
                    case 'view':
                        $type='share_click';
                        break;
                    case 'intent':
                        $type='brand_message_share';
                        break;
                }
            }
            $data = [
                'source' => 'app',
                'num' => $item->num.array_get($types,$item->give_type),
                'type' => $type,
                'nickname' => '游客',
                'uid' => $item->uid,
            ];
            if($item->genus_type=='share'){
                $share_ids[$item->id]=$item->genus_id;
            }elseif($item->genus_type=='sign'){
                $sign_ids[$item->id]=$item->genus_id;
            }elseif($item->genus_type=='intent'){
                $intent_ids[$item->id]=$item->genus_id;
            }
            $result[$item->id]=$data;
        }


        $func = function($uids, $data)use(&$result, $share) {
            $uids = array_pluck($uids, 'uid', 'id');
            foreach ($result as $k=>$v){
                $distribution = DistributionLog::with('action')->where('id', $k)->first();
                $username = User::where('uid', $uids[$data[$k]])->first()->username;

                //观看和点击是无法记录操作者的
                if ($username && isset($distribution->action->action) && !in_array($distribution->action->action, ['watch','view'])) {
                    $result[$k]['nickname'] = '无界商圈用户 ' . mb_substr($username, 0, 4) . '****' . mb_substr($username, 8, 3);
                }

                if($share->source=='app'){
                    $source_uid = $share->uid;
                }else{
                    $source_uid = $share->source_uid;
                }
                //判断是不是自己 观看和点击是无法记录操作者的
                if ($source_uid==$uids[$data[$k]] && isset($distribution->action->action) && !in_array($distribution->action->action, ['watch','view'])) {
                    $result[$k]['nickname'] = '本人';
                }
            }
        };


        //处理用户名
        if (count($share_ids)) {//分享的
            $func(Log::whereIn('id', $share_ids)
                            ->where('uid', '>', '0')
                            ->get(['uid', 'id']), $share_ids);
        }
        if (count($sign_ids)) {//报名的
            $func(\App\Models\Activity\Sign::whereIn('id', $sign_ids)
                            ->where('uid', '>', '0')
                            ->get(['uid', 'id']), $sign_ids);
        }
        if (count($intent_ids)) {//意向的
            $func(\App\Models\Brand\Intent::whereIn('id', $intent_ids)
                            ->where('uid', '>', '0')
                            ->get(['uid', 'id']), $intent_ids);
        }
        return array_values($result);
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
            ->select('video.id', 'video.created_at', 'video.is_recommend', 'video.is_hot')
            ->addSelect(\DB::raw("'video' type"))
        ;

        $news = DB::table('news')
            ->join('distribution', 'distribution.id', '=', 'news.distribution_id')
            ->join('distribution_action', 'distribution.id', '=', 'distribution_action.distribution_id')
            ->where('distribution_action.status','enable')
            ->where('distribution_action.action','share')
            ->where('distribution.status', 'enable')
            ->where('news.distribution_id', '>', 0)
            ->where('news.status', 'show')
            ->where(function($query) use ($keyword){
                if($keyword){
                    $query->where('news.title', 'like', '%'.$keyword.'%');
                }
            })
            ->select('news.id', 'news.created_at', 'news.is_recommend', 'news.is_hot')
            ->addSelect(\DB::raw("'news' type"))
        ;

        $list= $activity
            ->union($live)
            ->union($brand)
            ->union($video)
            ->union($news)
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
                    $activity_obj = Activity::find($obj->id);
                    $obj->activity = $this->getActivityData($activity_obj, $uid);
                    break;
                case 'live':
                    $obj->live = $this->getLiveData($obj, $uid);
                    break;
                case 'video':
                    $obj->video = $this->getVideoData($obj, $uid);
                    break;
                case 'news':
                    $obj->news = $this->getNewsData($obj, $uid);
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
        $return = Activity::getPublicData($obj, $uid);
        $return['isFavorite'] = Favorite::isFavorite('activity',$obj->id, $uid);
        //分享相关
        $return['share_image'] = getImage($obj->share_image?:'images/share_image.png', '', '');
        return $return;
    }


    private function getLiveData($obj, $uid)
    {
        return Live::getPublicData($obj, $uid);
    }


    private function getVideoData($obj, $uid)
    {
        $return =  VideoModel::getPublicData($obj, $uid);
        $return['isFavorite'] = Favorite::isFavorite('video',$obj->id, $uid);
        return $return;
    }


    private function getNewsData($obj, $uid)
    {
        $news = new NewsService();
        return $news->getPublicData($obj, $uid);
    }


    private function getBrandData($obj, $uid)
    {
        $return =  Brand::getPublicData($obj, $uid);
        $return['isFavorite'] = Favorite::isFavorite('brand',$obj->id, $uid);
        return $return;
    }
    //生成分享地址
    public function detailShareUrl($id, $type, $uid) {
        $one = \Route::input('one');
        $pagetags = [
            'activity' => config('app.activity_detail'),
            'live' => config('app.live_detail'),
            'video' => config('app.video_detail'),
            'news' => '02-4',
        ];
        $urls = [
            'activity' => 'activity/detail',
            'live' => 'live/detail',
            'video' => 'vod/detail',
            'news' => 'headline/detail',
            'brand' => 'brand/detail',
        ];
        $pagetag=  array_get($pagetags, $type);
        $url=  array_get($urls, $type);
        if(!$url){
            return false;
        }
        $query=[
            'id' => $id,
            'uid' => 0,
            'pagetag' => $pagetag,
            'share_mark' => makeShareMark($id, $type, $uid),
//            'code' => md5(uniqid() . rand(1111, 9999)),
            'is_share' => 1,
        ];

        if($uid){
            $query['code'] = md5(uniqid() . rand(1111, 9999));
        }


        if($pagetag){
            $query['pagetag'] = $pagetag;
        }
        return createUrl($url . ($one ? '/' . $one : ''), $query);
    }

}