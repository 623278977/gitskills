<?php

namespace App\Models;

use App\Models\Distribution\Action;
use App\Models\Guest\Relation;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use \DB;
use App\Models\Activity\Entity as Activity;
use App\Models\Vip\Entity as Vip;
use App\Models\Brand\Entity as Brand;
use App\Models\Distribution\Entity as Distribution;

class Video extends Model
{
    protected $table = 'video';
    protected $dateFormat = 'U';



    /**
     * 获取写这本书的作者
     */
    public function favorite($uid = 0)
    {
        if ($uid) {
            return $this->hasMany('App\Models\User\Favorite', 'post_id')->where(
                array(
                    'model'  => 'video',
                    'status' => 1,
                    'uid'    => $uid
                )
            );
        } else {
            return $this->hasMany('App\Models\User\Favorite', 'post_id')->where(
                array(
                    'model'  => 'video',
                    'status' => 1,
                )
            );
        }
    }

    public function videoType()
    {
        return $this->hasOne('App\Models\VideoType', 'id', 'type');
    }


    public function brand()
    {
        return $this->hasOne('App\Models\Brand\Entity', 'id', 'brand_id');
    }

    public function activity()
    {
        return $this->hasOne(Activity::class, 'id', 'activity_id');
    }

    static function getRows($where, $vip_id = 0, $page = 0, $pageSize = 10, $params = array())
    {
        $query = self::with('favorite', 'videoType', 'activity')->leftJoin('video_type', 'video_type.id', '=', 'video.type')
            ->where($where)->where('video.status', 1)->where('video_type.status', 1);

        if ($vip_id) {
            $query->where('video.vip_id', $vip_id);
        }

        if (array_key_exists('keyword', $params)) {
            $query->where('video.subject', 'like', '%' . $params['keyword'] . '%');
        }

        //热门关键字
        if (array_key_exists('hotwords', $params)) {
            $query->where(function($query) use($params){
                $query->where('video.subject', 'like', '%' . $params['hotwords'] . '%')
                    ->orWhere('video.keywords', 'like', '%' . $params['hotwords'] . '%')
                    ->orWhere('video.description', 'like', '%' . $params['hotwords'] . '%');
            });
        }

        //品牌搜索
        if (array_key_exists('brand_id', $params)) {
            $query->where('video.brand_id', $params['brand_id']);
        }


        if (array_key_exists('selection', $params)) {
            if ($params['selection'] == 'is_recommend' || $params['selection'] == 'is_hot') {
                $query->where('video.' . $params['selection'], 1);
            } elseif ($params['selection'] == 'is_vip') {
                $query->where('video.vip_id', '>', 0);
            } elseif ($params['selection'] == 'zhaoshang'){
                $query->where('video.brand_id','>',0);
            } elseif ($params['selection'] == 'search'){
//                $query->where('video.brand_id','>',0);
            } else {
                $query->leftJoin('activity_maker as am', 'am.activity_id', '=', 'video.activity_id')
                    ->where('am.maker_id', $params['selection']);
            }

        }

        if(empty($params['selection']) || ($params['selection'] != 'zhaoshang' && $params['selection'] != 'search')){
            $query->where('video_type.code','!=','brand');
        }

        //排序
        if (array_key_exists('order', $params)) {
            if ($params['order'] == 'zhineng') {
                $query->orderBy('video.top', 'desc')
                    ->orderBy('video.sort', 'desc')
                    ->orderBy('video.is_recommend', 'desc')
                    ->orderBy('video.is_hot', 'desc')
                    ->orderBy('video.created_at', 'desc');
            } else if($params['order'] == 'created_at'){
                $query->orderBy('video.created_at', 'desc');
            }else {
                $query->orderBy('video.sort', 'desc')->orderBy("video." . $params['order'], 'desc');
            }
        }

        $collection = $query->skip($page * $pageSize)->take($pageSize)->get(['video.*']);

        $total = $query->count();

        $collection->dataCount = $total;

        return $collection;
    }

    static function getBase($video, $uid=0)
    {
        if (!isset($video->id)) {
            return array();
        }
        $data = array();
        $data['id'] = $video->id;
        $data['view'] = $video->sham_view;//返回假数据
        $data['image'] = getImage($video->image, 'video' ,'');
        $data['subject'] = $video->subject;
        $data['is_recommend'] = $video->is_recommend;
        $data['is_hot'] = $video->is_hot;
        $data['content'] = $video->content;
        $data['description'] = str_replace('"','',$video->description);//$video->activity?cut_str($video->activity->description, 50):'';
        //如果视频描述为空 则截取视频详情
        if (empty($data['description'])){

            $data['description'] = cut_str(preg_replace('/\s+/i','',str_replace('&nbsp;','',strip_tags($data['content']))),50);
        }
        //如果视频详情为空 则获取视频描述
        if (empty($data['content'])){
            $data['content'] = $video->description;
        }
        $data['type'] = isset($video->videoType->subject) ? $video->videoType->subject : '';
        if(!$uid){
            $data['url'] = createUrl('vod/detail', array('id' => $video->id, 'pagetag' => config('app.video_detail')));
        }else{
            $data['url'] = createUrl('vod/detail', array('id' => $video->id, 'pagetag' => config('app.video_detail'),
                                                         'share_mark' => makeShareMark($video->id, 'video', $uid)));
        }

        $data['activity_id'] = $video->activity_id;
        $data['share_score'] = $video->share_score;
        $data['record_at'] = date('Y-m-d',$video->created_at->timestamp);
        $data['length'] = $video->duration?changeTimeType($video->duration):0;
        $data['share_num'] = $video->share_num;
        $data['distribution_id'] = $video->distribution_id;
        $data['distribution_deadline'] = $video->distribution_deadline;
        $data['rebate'] = Distribution::Integer($video->rebate);
        $data['score_price'] = $video->score_price;
        $data['sort'] = $video->sort;
        $data['top'] = $video->top;
        $data['is_hot'] = $video->is_hot;
        $data['created_at'] = $video->created_at->timestamp;
        //关键字
        $data['keywords'] = $video->keywords ? strpos($video->keywords,' ')!==FALSE ? explode(' ',$video->keywords) : [$video->keywords] : [];

        $data['keywords'] = array_filter($data['keywords'], function($item){return $item;});

        return $data;
    }

    /**
     * 获取回放详情
     */
    static function detail($id, $uid, $cache = 1)
    {
        $data = Cache::has('video' . 'detail' . $id . 'uid' . $uid) ? Cache::get('video' . 'detail' . $id . 'uid' . $uid) : false;
        if ($data === false || $cache) {
            $video = self::where('video.id', $id)->leftJoin('video_type', 'video.type', '=', 'video_type.id')
                ->select(
                    DB::raw("lab_video.distribution_id,lab_video.brand_id,lab_video.video_url,lab_video.description,lab_video.subject,lab_video.image,
                 lab_video.activity_id,lab_video.view,lab_video.favor_count,lab_video.is_recommend,
                lab_video.id,lab_video.price,lab_video.vip_id,lab_video_type.subject as type,
                lab_video_type.small_image")
                )->first();

            //分销信息
            $distribution = Action::getDistributionByAction('video', $id, 'share');
            if($distribution){
                $video->share_reward_num = $distribution->trigger;
                $video->share_reward_unit = $distribution->give;
            }else{
                $video->share_reward_num = 0;
                $video->share_reward_unit = 'none';
            }

            //如果该视频已经绑定活动
            if ($video->activity_id > 0) {
                $activity = Activity::detail($video->activity_id);
                unset($activity->vip_id, $activity->vip_name);
                $ticket = DB::table('activity_ticket')->where('activity_id', $video->activity_id)->where('type', -1)->first();
                if ($ticket) {
                    $activity->ticket_id = $ticket->id;
                } else {
                    $activity->ticket_id = 'video_id'.$id;
                }

                $favorite = DB::table('user_favorite')->where('model', 'video')->where('post_id', $video->id)->where('uid', $uid)
                    ->where('status', 1)->first();

                if (isset($favorite) && is_object($favorite)) {
                    $is_favorite = 1;
                } else {
                    $is_favorite = 0;
                }
                $activity->price = $video->price;
                $activity->is_purchase = self::isPurchase($id, $uid);
                $activity->is_favorite = $is_favorite;
                $activity->video_url = $video->video_url;
                $activity->v_subject = $video->subject;
                $activity->is_recommend = $video->is_recommend;
                $activity->v_description = $video->description;
                $activity->video_type = $video->type;
                $activity->likes = $video->favor_count;
                $activity->view = $video->view;
                $activity->small_image = getImage($video->small_image, '', '', 0);
                $activity->video_image = getImage($video->image, 'video', '', 0);
                $activity->video_id = $id;
                $activity->activity_keywords = $activity->keywords;
                $activity->share_reward_unit = $video->share_reward_unit;
                $activity->share_reward_num = $video->share_reward_num;
            }

            if (isset($activity)) {
                $data = $activity;
                $data->with_activity=1;
            } else {
                $data = $video;
                $data->with_activity=0;
            }

            if ($video->vip_id) {
                $data->vip_id = $video->vip_id;
                $vip = DB::table('vip')->where('id', $video->vip_id)->first();
                $data->vip_name = $vip->name;
                $data->is_authorize = Vip::valid($video->vip_id, $uid);
            }

            //该视频品牌信息
            if($video->brand_id){
                $data->with_brand=1;
                $brand = Brand::singles()->where(['status' => 'enable'])->where('id', $video->brand_id)
                    ->addSelect('id','logo', 'name', 'investment_min', 'investment_max','keywords','summary','details')->first();
                $brand = Brand::process($brand, $uid)->toArray();
                $data->brand = $brand;
            }else{
                $data->with_brand=0;
            }

            //该视频的嘉宾信息
            $data->guests = Relation::getGuests($id, 'video');

            Cache::put('video' . 'detail' . $id . 'uid' . $uid, $data, 1440);
        }

        return $data;
    }



    /**
     * 获取回放详情
     */
    static function detail_v25($id, $uid, $cache = 1)
    {
        $video = Cache::has('video' . 'detail' . $id . 'uid' . $uid) ? Cache::get('video' . 'detail' . $id . 'uid' . $uid) : false;
        if ($video === false || $cache) {
            $video = self::where('video.id', $id)->leftJoin('video_type', 'video.type', '=', 'video_type.id')
                ->select(
                    DB::raw("
                    lab_video.id,
                    lab_video.distribution_id,
                    lab_video.distribution_deadline,
                    lab_video.brand_id,
                    lab_video.video_url,
                    lab_video.description,
                    lab_video.content,
                    lab_video.subject,
                    lab_video.image,
                    lab_video.activity_id,
                    lab_video.sham_view as view,
                    lab_video.favor_count,
                    lab_video.is_recommend,
                    lab_video.price,
                    lab_video.vip_id,
                    lab_video.score_price,
                    lab_video.created_at,
                    lab_video_type.subject as type,
                    lab_video_type.small_image
                    ")
                )->first();

            //分销信息
            $distribution = Action::getDistributionByAction('video', $id, 'share');
            $watch_distribution = Action::getDistributionByAction('video', $id, 'watch');
            if($distribution){
                $video->share_reward_num = $distribution->trigger;
                $video->share_reward_unit = $distribution->give;
            }else{
                $video->share_reward_num = 0;
                $video->share_reward_unit = 'none';
            }

            if($watch_distribution){
                $video->watch_reward_long = $watch_distribution->base;
            }else{
                $video->watch_reward_long = 0;
            }

            //如果该视频已经绑定活动
            if ($video->activity_id > 0) {
                $activity = Activity::detail($video->activity_id);
                unset($activity->vip_id, $activity->vip_name);
                $ticket = DB::table('activity_ticket')->where('activity_id', $video->activity_id)->where('type', -1)->first();
                if ($ticket) {
                    $activity->ticket_id = $ticket->id;
                } else {
                    $activity->ticket_id = 'video_id'.$id;
                }

                $activity->video_id = $id;
                $activity->activity_keywords = $activity->keywords;
            }

            if (isset($activity)) {
                $video->activity = $activity;
                $video->with_activity=1;
            } else {
                $video->with_activity=0;
            }

            if ($video->vip_id) {
                $vip = DB::table('vip')->where('id', $video->vip_id)->first();
                $video->vip_name = $vip->name;
                $video->is_authorize = Vip::valid($video->vip_id, $uid);
            }


            $favorite = DB::table('user_favorite')->where('model', 'video')->where('post_id', $video->id)->where('uid', $uid)
                ->where('status', 1)->first();

            $video->is_purchase = self::isPurchase($id, $uid);
            $video->is_favorite = is_object($favorite) ? 1: 0;

            $video->small_image = getImage($video->small_image, '', '', 0);
            $video->image = getImage($video->image, 'video', '', 0);

            //该视频品牌信息
            if($video->brand_id){
                $video->with_brand=1;
                $brand = Brand::singles()->where(['status' => 'enable'])->where('id', $video->brand_id)
                    ->addSelect('id','logo', 'name', 'investment_min', 'investment_max','keywords','summary','details')->first();
                $brand = Brand::process($brand, $uid)->toArray();
                $video->brand = $brand;
            }else{
                $video->with_brand=0;
            }

            //该视频的嘉宾信息
            $video->guests = Relation::getGuests($id, 'video');

            Cache::put('video' . 'detail' . $id . 'uid' . $uid, $video, 1440);
        }

        return $video;
    }


    /**
     * 单纯的获得视频详情，不考虑关联的活动
     *
     * @return mixed
     */
    public static function singleVideo($id)
    {
        $video = \DB::table('video')->where('video.id', $id)->leftJoin('video_type', 'video.type', '=', 'video_type.id')
            ->select(
                DB::raw(
                    "lab_video.video_url,lab_video.description,lab_video.subject,lab_video.image,lab_video.description,lab_video.content,
                 lab_video.activity_id,lab_video.view,lab_video.favor_count,lab_video.is_recommend,lab_video.duration,
                lab_video.id,lab_video.price,lab_video.vip_id,lab_video_type.subject as type,lab_video.created_at,
                lab_video_type.small_image,lab_video.keywords"
                )
            )->first();

        $video->small_image = getImage($video->small_image, '', '', 0);
        $video->image = getImage($video->image, 'video', '', 0);
        $video->keywords ?$video->keywords = explode(' ', $video->keywords):$video->keywords = [];
        $video->duration = changeTimeType($video->duration);
        !$video->description &&  $video->description = extractText($video->content);

        return $video;
    }

    /*
     * 作用：通过某直播id和用户id获取该用户是否已经购买该直播
     * 参数：$id 直播id  $uid 用户uid
     *
     * 返回值：boolean
     */
    public static function isPurchase($id, $uid)
    {
//        $old_purchase = \DB::table('order')
//            ->leftJoin('activity_ticket', 'order.ticket_id', '=', 'activity_ticket.id')
//            ->leftJoin('activity', 'activity_ticket.activity_id', '=', 'activity.id')
//            ->leftJoin('video', 'video.activity_id', '=', 'activity.id')
//            ->where('video.id', $id)
//            ->where('order.uid', $uid)
//            ->where('order.status', 1)
//            ->where('activity_ticket.type', -1)
//            ->first();

        $video =  \DB::table('video')->where('id', $id)->first();
        $ticket = DB::table('activity_ticket')->where('activity_id', $video->activity_id)->where('type', -1)->first();
        $old_purchase = DB::table('order')->where('ticket_id', $ticket->id)->where('uid', $uid)->where('status', 1)->first();

        $new_purchase = \DB::table('orders_items')->leftJoin('orders', 'orders_items.order_id', '=', 'orders.id')
            ->where('orders.uid', $uid)
            ->where('orders_items.type', 'video')
            ->where('orders.status', 'pay')
            ->where('orders_items.product_id', $id)
            ->first();
//        dd($old_purchase);

        if(is_object($old_purchase) || is_object($new_purchase)){
            return 1;
        }else{
            return 0;
        }
    }

    /**
     * 获取回放的相关视频  绑定的活动，包含所入驻的ovo中心，
     */
    static function recommend($id, $uid, $cache = 1, $num=3)
    {
        $data = Cache::has('video' . 'recommend' . $id) ? Cache::get('video' . 'recommend' . $id) : false;
        if ($data === false || $cache) {
            $video = self::where('id', $id)->first();
            $user = \DB::table('user')->where('uid', $uid)->first();

            //属于同一个活动的视频
            if($video->activity_id){
                $activity_videos = self::where('activity_id', $video->activity_id)
                    ->select('id', 'image', 'subject', 'video_url', 'keywords', 'created_at', 'duration')
                    ->where('id', '<>', $id)
                    ->where('status', 1)
                    ->orderBy(\DB::raw('RAND()'))
                    ->take($num)
                    ->get()
                    ->toArray();
            }else{
                $activity_videos = [];
            }


//            //同一个ovo下面的视频
//            $ovo_videos = array();
//            if (is_object($user) && count($activity_videos)<$num) {
//                $ovo_videos = self::whereIn(
//                    'activity_id',
//                    function ($query) use ($user) {
//                        $query->from('activity_maker')->where('status', 1)
//                            ->where('maker_id', $user->maker_id)
//                            ->lists('activity_id');
//                    }
//                )
//                    ->select('id', 'image', 'subject', 'video_url', 'keywords', 'created_at', 'duration')
//                    ->where('id', '<>', $id)
//                    ->whereNotIn('id', array_column($activity_videos, 'id'))
//                    ->where('status', 1)
//                    ->orderBy(\DB::raw('RAND()'))
//                    ->take($num-count($activity_videos))
//                    ->get()
//                    ->toArray();
//            }


            //同一个分类下的
            $rec = array();
            if ((count($activity_videos)) < $num) {
                $rec = self::where('type', $video->type)
                    ->select('id', 'image', 'subject', 'video_url', 'keywords', 'created_at', 'duration')
                    ->where('id', '<>', $id)
//                    ->whereNotIn('id', array_column($ovo_videos, 'id'))
                    ->whereNotIn('id', array_column($activity_videos, 'id'))
                    ->where('status', 1)
                    ->orderBy(\DB::raw('RAND()'))
                    ->take($num -count($activity_videos))
                    ->get()
                    ->toArray();
            }

            //如果还不够，再补足
            $add = array();
            if ((count($rec) +count($activity_videos)) < $num) {
                $add = self::orderBy('created_at', 'desc')
                    ->select('id', 'image', 'subject', 'video_url', 'keywords', 'created_at', 'duration')
                    ->whereNotIn('id', array_column($rec, 'id'))
                    ->whereNotIn('id', array_column($activity_videos, 'id'))
                    ->where('id', '<>', $id)
                    ->where('status', 1)
                    ->take($num - (count($rec) +count($activity_videos)))
                    ->orderBy(\DB::raw('RAND()'))
                    ->get()
                    ->toArray();
            }

            $data = array_merge($activity_videos, $rec, $add);
            $data = array_map(
                function ($v) {
                    $v['image'] = getImage($v['image'], 'video', '', 0);
                    $v['duration'] = changeTimeType($v['duration']);
                    $v['keywords'] ? $v['keywords'] = explode(' ', $v['keywords']):$v['keywords'] = [];
                    return $v;
                },
                $data
            );
            Cache::put('video' . 'recommend' . $id, $data, 1440);
        }

        return $data;
    }

    static function comments($id, $cache = 1)
    {
        $data = Cache::has('video' . 'comment' . $id) ? Cache::get('video' . 'comment' . $id) : false;
        if ($data === false || $cache) {
            $wonderfuls = DB::table('comment')->where('type', 'Video')->where('is_wonderful', 1)->where('post_id', $id)->get();
            $likes = DB::table('comment')->where('type', 'Video')->where('likes', '>', 10)->where('post_id', $id)
                ->orderBy('likes', 'desc')->take(2)->get();
            $likes_id = $wonderfuls_id = array();
            foreach ($likes as $k => $v) {
                $likes_id[] = $v->id;
            }

            foreach ($wonderfuls as $k => $v) {
                $wonderfuls_id[] = $v->id;
            }
            $diffs = array_diff($likes_id, $wonderfuls_id);
            $amaze_id = array_merge($wonderfuls_id, $diffs);

            $amaze = DB::table('comment')
                ->leftJoin('comment as pComment', 'comment.upid', '=', 'pComment.id')
                ->leftJoin('user', 'comment.uid', '=', 'user.uid')
                ->leftJoin('user as pUser', 'pComment.uid', '=', 'pUser.uid')
                ->whereIn('comment.id', $amaze_id)
                ->select(
                    'user.nickname as c_nickname',
                    'pUser.nickname as p_nickname',
                    'comment.created_at',
                    'comment.content',
                    'pComment.content as pContent',
                    'comment.likes',
                    'comment.id',
                    'pComment.id as pId',
                    'user.avatar',
                    'user.uid as c_uid',
                    'pUser.uid as p_uid'
                )
                ->orderBy('comment.likes', 'desc')->get();

            $all = DB::table('comment')
                ->leftJoin('comment as pComment', 'comment.upid', '=', 'pComment.id')
                ->leftJoin('user', 'comment.uid', '=', 'user.uid')
                ->leftJoin('user as pUser', 'pComment.uid', '=', 'pUser.uid')
                ->where('comment.type', 'Video')->where('comment.post_id', $id)
                ->select(
                    'user.nickname as c_nickname',
                    'pUser.nickname as p_nickname',
                    'comment.created_at',
                    'comment.content',
                    'pComment.content as pContent',
                    'comment.likes',
                    'comment.id',
                    'pComment.id as pId',
                    'user.avatar',
                    'user.uid as c_uid',
                    'pUser.uid as p_uid'
                )
                ->orderBy('comment.created_at', 'desc')->get();

            $today = strtotime(date('Y-m-d', strtotime('today')));
            $yestoday = strtotime(date('Y-m-d', strtotime('-1 day')));
            $f_yestoday = strtotime(date('Y-m-d', strtotime('-2 day')));
            $beginThismonth = mktime(0, 0, 0, date('m'), 1, date('Y'));
            $beginThisyear = mktime(0, 0, 0, 1, 1, date('Y'));

            foreach ($amaze as $k => $v) {
                if ($v->created_at < time() && $v->created_at > $today) {
                    $v->created_at = '今天' . date('H:i', strtotime($v->created_at));
                } elseif ($v->created_at < $today && $v->created_at > $yestoday) {
                    $v->created_at = '昨天' . date('H:i', strtotime($v->created_at));
                } elseif ($v->created_at < $yestoday && $v->created_at > $f_yestoday) {
                    $v->created_at = '前天' . date('H:i', strtotime($v->created_at));
                } elseif ($v->created_at < $f_yestoday && $v->created_at > $beginThisyear) {
                    $v->created_at = date('m月d日 H:i', strtotime($v->created_at));
                } elseif ($v->created_at < $beginThisyear) {
                    $v->created_at = date('Y年m月d日 H:i', strtotime($v->created_at));
                }

                foreach ($all as $k => $v) {
                    if ($v->created_at < time() && $v->created_at > $today) {
                        $v->created_at = '今天' . date('H:i', strtotime($v->created_at));
                    } elseif ($v->created_at < $today && $v->created_at > $yestoday) {
                        $v->created_at = '昨天' . date('H:i', strtotime($v->created_at));
                    } elseif ($v->created_at < $yestoday && $v->created_at > $f_yestoday) {
                        $v->created_at = '前天' . date('H:i', strtotime($v->created_at));
                    } elseif ($v->created_at < $f_yestoday && $v->created_at > $beginThisyear) {
                        $v->created_at = date('m月d日 H:i', strtotime($v->created_at));
                    } elseif ($v->created_at < $beginThisyear) {
                        $v->created_at = date('Y年m月d日 H:i', strtotime($v->created_at));
                    }
                }
                $data['amaze'] = $amaze;
                $data['all'] = $all;
                Cache::put('video' . 'comment' . $id, $data, 1440);
            }

            return $data;
        }
    }

    static function lists($where, $vip_id = 0, $params = array(), $uid = 0, $page = 0, $pageSize = 10, $cache = 1)
    {
        $rows = self::getRows($where, $vip_id, $page, $pageSize, $params);

        $list = array();
        foreach ($rows as $k => $v) {
            $list[$k] = Video::getBase($v);
        }

        foreach ($list as $k => $v) {
            $list[$k]['small_image'] = isset($rows[$k]->videoType->small_image) ? getImage($rows[$k]->videoType->small_image, '', '', 0) : "";
            $list[$k]['favorite_count'] = $rows[$k]->favorite->count();
            $list[$k]['is_favorite'] = $rows[$k]->favorite($uid)->count();
            strtotime($rows[$k]->created_at->toDateTimeString()) > (time() - 24 * 3600 * 5) ? $list[$k]['is_new'] = 1 : $list[$k]['is_new'] = 0;
        }

        return $list;
    }

    /*
     * 首页展示
     */
    static function getPublicData($obj, $uid=0)
    {
        $data = self::getBase(Video::find($obj->id), $uid);
        $return = [];
        if($data){
            $return['id'] = $data['id'];
            $return['image'] = $data['image'];
            $return['subject'] = $data['subject'];
            $return['description'] = preg_replace('/\s+/i','',str_replace('&nbsp;','',strip_tags($data['description'])));
            $return['url'] = $data['url'];
            $return['activity_id'] = $data['activity_id'];
            $return['record_at'] = $data['record_at'];
            $return['length'] = $data['length'];
            $return['video_type'] = $data['type'];
            $return['rebate'] = Distribution::Integer($data['rebate']);
            $return['view'] = $data['view'];
            //如果视频详情为空 则获取视频描述
            if (empty($return['description'])){
                $return['description'] = preg_replace('/\s+/i','',str_replace('&nbsp;','',strip_tags($data['content'])));
            }

            //评论
            $return['count_comment'] = Comment\Entity::ConmmentCount($return['id'],'Video');
            $return['is_distribution'] = Distribution::IsDeadline($data['distribution_id'],$data['distribution_deadline']);//分销是否失效
            $return['share_score'] = Distribution::shareScore($data['distribution_id']);//分销分享得的积分
            if($uid){
                $return['share_reward_num'] = Action::getDistributionByAction('video', $obj->id, 'share')->trigger;
                $return['share_reward_unit'] = Action::getDistributionByAction('video', $obj->id, 'share')->give;
            }

        }

        return $return;
    }

}
