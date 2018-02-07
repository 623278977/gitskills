<?php
/**活动模型
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/5/5
 * Time: 11:10
 */
namespace App\Models\Live;

use App\Models\Activity\Entity as Activity;
use App\Models\Activity\Ticket;
use App\Models\Distribution\Action;
use App\Models\Guest\Relation;
use App\Models\Video;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use \DB;
use App\Models\Vip\Entity as Vip;
use App\Models\User\Entity as User;
use App\Models\Brand\Entity as Brand;
use App\Models\Brand\Goods;
use App\Models\News\Entity as News;
use App\Models\Distribution\Entity as Distribution;
use App\Models\Live\LiveBrandGoods;
class Entity extends Model
{

    protected $dateFormat = 'U';

    protected $table = 'live';

    //黑名单
    protected $guarded = [];


    //关联的活动
    public function activity(){
        return $this->belongsTo('App\Models\Activity\Entity', 'activity_id', 'id');
    }


    public function goods()
    {
        return $this->hasMany('App\Models\Brand\Goods', 'live_id', 'id');
    }

    public function liveBrandGoods(){
        return $this->hasMany('App\Models\Live\LiveBrandGoods','live_id', 'id');
    }

    public function hasManyVideo(){
        return $this->hasMany('App\Models\Video','live_id','id');
    }


    static function getRow($where)
    {
        return self::where($where)->first();
    }

    /**
     * 获取直播预告列
     *
     * @param all ，为1时包含正直播，0不 包含。
     *
     * @return array|bool
     */
    static function lists($uid = 0, $keywords = '', $page = 1, $pageSize = 10, $vip_id = 0, $cache = 1, $fetch_end=0 ,$hotwords = '')
    {
        $data = Cache::has('live' . 'lists' . $keywords . 'uid' . $uid) ? Cache::get('live' . 'lists' . $keywords . 'uid' . $uid) : false;
        if ($data === false || $cache) {
            $query = self::where('live.status', 0);
            if (isset($keywords) && $keywords != '') {
                $query->where('subject', 'like', '%' . $keywords . '%');
            }
            if ($vip_id) {
                $query->where('live.vip_id', $vip_id);
            }
            //热门关键字
            if ($hotwords) {
                $query->where(function($query) use($hotwords){
                    $query->where('subject', 'like', '%' . $hotwords . '%')
                        ->orWhere('keywords', 'like', '%' . $hotwords . '%')
                        ->orWhere('description', 'like', '%' . $hotwords . '%');
                });
            }

            $future_list = clone $query;
            $now_list = clone $query;

            $future_list = $future_list->where('live.begin_time', '>', time())->orderBy('begin_time', 'asc')->get()->toArray();

            $over_list = array();
            if($fetch_end==1 || $fetch_end == 'over'){
                $over_list = clone $query;
                //已经结束的直播
                $over_list = $over_list->where('live.end_time', '<', time())->orderBy('begin_time', 'desc')->get()->toArray();
            }

            //正在直播
            $now_list = $now_list->where('live.end_time', '>=', time())->orderBy('begin_time', 'asc')
                ->where('live.begin_time', '<=', time())->get()->toArray();

            $page_list = array_slice(array_merge($future_list, $over_list), ($page - 1) * $pageSize, $pageSize);

            if (isset($keywords) && $keywords != '') {
                $page_list = array_slice(array_merge($now_list, $future_list, $over_list), ($page - 1) * $pageSize, $pageSize);
            }

            if (isset($hotwords) && $hotwords != '') {
                $page_list = array_slice(array_merge($now_list, $future_list, $over_list), ($page - 1) * $pageSize, $pageSize);
            }

            if($fetch_end == 'default'){
                if(count($now_list)>0){
                    $page_list = array_slice($now_list, ($page - 1) * $pageSize, $pageSize);
                }elseif(count($future_list)>0){
                    $page_list = array_slice($future_list, ($page - 1) * $pageSize, $pageSize);
                }elseif(count($over_list)>0){
                    $page_list = array_slice($over_list, ($page - 1) * $pageSize, $pageSize);
                }
            }


            if($fetch_end == 'now'){
                $page_list = array_slice($now_list, ($page - 1) * $pageSize, $pageSize);
            }

            if($fetch_end == 'future'){
                $page_list = array_slice($future_list, ($page - 1) * $pageSize, $pageSize);
            }

            if($fetch_end == 'over'){
                $page_list = array_slice($over_list, ($page - 1) * $pageSize, $pageSize);
            }
            if($fetch_end == 'begin'){
                $page_list = array_slice(array_merge($now_list, $future_list), ($page - 1) * $pageSize, $pageSize);
            }

            $data = array();
            foreach ($page_list as $k => $v) {
                if ($uid != 0) {
                    $object = Subscribe::where('uid', $uid)->where('live_id', $v['id'])->where('status', 1)->first();
                    if (is_object($object)) {
                        $v['subscribe'] = 1;
                    } else {
                        $v['subscribe'] = 0;
                    }
                }
                if (time() < $v['end_time'] && time() > $v['begin_time']) {
                    $v['is_being'] = 1;
                } elseif (time() < $v['end_time']) {
                    $v['is_being'] = 0;
                } else {
                    $v['is_being'] = -1;
                }
                $v['list_img'] = getImage($v['list_img'], 'live', '', 0);
                $v['part_description'] = trim(cut_str(preg_replace('#<\w+(\s+.*?)?>|</\w+>|&nbsp;#','',  $v['description']), 60));//部分描述
                $v['detail_img'] = getImage($v['detail_img'], 'live', '', 0);
                $v['preview_time'] = date('m/d H:i', $v['preview_time']);
                $v['begin_time_format'] = date('m/d H:i', $v['begin_time']);
//                $v['begin_time'] = date('m/d H:i', $v['begin_time']);
                $v['end_time_stamp'] = $v['end_time'];
                $v['duration_hour'] = ceil(($v['end_time']-$v['begin_time'])/3600);
                $v['end_time'] = date('Y/m/d H:i', $v['end_time']);
                $v['keywords'] = $v['keywords'] ? strpos($v['keywords'],' ')!==FALSE ? explode(' ',$v['keywords']) : [$v['keywords']] : [];
                $v['brand_name'] = ($brand = DB::table('activity_brand as ab')
                    ->join('brand as b','ab.activity_id','=','b.id')
                    ->where('ab.activity_id',$v['activity_id'])
                    ->first()) ? $brand->name : '';

                if($v['activity_id']){
                    $ticket = Ticket::where('activity_id',$v['activity_id'])->where('type',2)->where('status',1)->min('price');
                    $v['min_price'] = $ticket?($ticket == '0.01' ? '0.01' : $ticket):0;
                    $count = Video::where('activity_id',$v['activity_id'])->orWhere('live_id', $v['id'])
                        ->where('status',1)->count();
                    $v['video_count'] = (int)$count?:0;
                }else{
                    $v['min_price'] = $v['video_count'] = 0;
                }
                $v['length'] = '';
                if($v['is_being'] == 1){
                    $length = changeTimeType(time() - $v['begin_time']);
                    $v['length'] = explode(':',$length)[0];
                }
                if($v['min_price'] == '0.00'){
                    $v['min_price'] = '0';
                }
                $data[] = $v;
            }

            Cache::put('live' . 'lists' . $keywords . 'uid' . $uid, $data, 1440);
        }

        return $data;
    }

    /**
     * 获取今日直播列表
     *
     * @return array|bool
     */
    static function today($page = 1, $pageSize = 10, $vip_id = 0, $cache = 1)
    {
        $today = date('y-m-d');
        $data = Cache::has('live' . 'today' . $today) ? Cache::get('live' . 'today' . $today) : false;
        if ($data === false || $cache) {
            $query = self::where('begin_time', '<', time())
                ->where('end_time', '>', time())
                ->where('status', 0)
                ->orderBy('begin_time', 'asc')
                ->select('id', 'subject', 'list_img', 'live_url', 'view')
                ->skip(($page - 1) * $pageSize)
                ->take($pageSize);
            if ($vip_id > 0) {
                $query->where('vip_id', $vip_id);
            }

            $data = $query->get()->toArray();

            foreach ($data as $k => &$v) {
                $v['list_img'] = getImage($v['list_img'], 'live');
            }

            Cache::put('live' . 'today' . $today, $data, 1440);
        }

        return $data;
    }

    /**
     * 获取一个直播的详情
     *
     * @return array|bool
     */
    static function detail($id, $uid = 0, $cache = 1)
    {
            $list = DB::table('live')
                ->where('id', $id)
                ->select(
                    'live_url',
                    'subject',
                    'activity_id',
                    'chatroom_id',
                    'vip_id',
                    'begin_time',
                    'end_time',
                    'description',
                    'foreshow_url',
                    'foreshow_duration',
                    'sham_view as view',
                    'list_img',
                    'is_hot',//是否最热
                    'share_num',
                    'distribution_id',
                    'distribution_deadline',
                    'rebate')
                ->first();
            if (!is_object($list)) {
                $data = [];
            } else {
                $ticket = DB::table('activity_ticket')->where('activity_id', $list->activity_id)->where('type', 2)
                    ->first();
                is_object($ticket) ? $list->ticket = $ticket->price : $list->ticket = 0;
                if ($uid != 0) {
                    is_object($ticket) ? $ticket_id = $ticket->id : $ticket_id = 0;
                    $purchase = DB::table('user_ticket')->where('ticket_id', $ticket_id)->where('uid', $uid)->where('status', 1)->first();
                    $subscribe = DB::table('user_subscription')->where('uid', $uid)->where('live_id', $id)->where('status', 1)->first();
                    if (is_object($purchase)) {
                        $list->is_purchase = 1;
                    } else {
                        $list->is_purchase = 0;
                    }
                    if (is_object($subscribe)) {
                        $list->subscribe = 1;
                    } else {
                        $list->subscribe = 0;
                    }
                } else {
                    $list->is_purchase = 0;
                    $list->subscribe = 0;
                }
                $list->time_now = time();
                if($list->activity_id){
                    $activity = Activity::detail($list->activity_id);
                    $follow = Activity::follow($activity->id, $uid);
                    $activity->follow = $follow;
                    if ($activity->begin_time <= time() && $activity->end_time >= time()) {
                        $list->situation = 'is_living';
                    } elseif ($activity->begin_time > time()) {
                        $list->situation = 'future';
                    } else {
                        $list->situation = 'past';
                    }
                }


                //直播相关品牌
                $brand_ids = DB::table('activity_brand')->where('activity_id', $list->activity_id)->lists('brand_id');
                if($brand_ids==[]){
                    $brand_ids = Goods::where('live_id', $id)->groupBy('brand_id')->lists('brand_id')->toArray();
                }

                if(count($brand_ids)){
                    $list->is_brand_live=1;
                    $brands = Brand::singles()->where(['status' => 'enable'])->whereIn('id', $brand_ids)
                        ->addSelect('id','logo', 'name', 'investment_min', 'investment_max','keywords','summary')->get();
                    $brands = Brand::process($brands)->toArray();
                    $list->brands = $brands;
                }else{
                    $list->is_brand_live=0;
                }

                //直播品牌商品
                $good_ids = Goods::where('live_id', $id)->select('id','brand_id', 'price', 'num', 'code', 'status','title')->get();
                if(count($good_ids)){
                    $list->is_brand_good=1;
                    $goods = [];
                    foreach($good_ids as $k=>$v){
                        $good = Brand::singles()->where(['status' => 'enable'])->where('id', $v->brand_id)
                            ->addSelect('logo', 'name', 'investment_min', 'investment_max','keywords')->first();
                        if(!is_object($good)){
                            continue;
                        }
                        $good = Brand::process($good)->toArray();
                        $good['brand_id'] = $v->brand_id;
                        $good['id'] = $v->id;
                        $good['price'] = $v->price;
                        $good['num'] = $v->num;
                        $good['code'] = $v->code;
                        $good['status'] = $v->status;
                        $good['goods_title'] = $v->title;
                        $goods[] = $good;
                    }
                    $list->goods = $goods;

                }else{
                    $list->is_brand_good=0;
                }
                //该直播在线人数
                $log_uids = Log::where('vid', $id)->orderBy('created_at', 'desc')->lists('uid');

                $log_count = count($log_uids->toArray());
                $log_users = User::where('status',1)->whereIn('uid', $log_uids->toArray())->where('uid', '<>', $uid)
//                    ->orderBy('uid', 'desc')
                    ->select('avatar')->limit(5)->get()->toArray();
                $user = DB::table('user')->where('uid', $uid)->select('avatar')->first();
                array_unshift($log_users, (array)$user);
                if($log_count>6 && count($log_users)<6){
                    $count_diff = 6-count($log_users);
                    for($i=0;$i<$count_diff;$i++){
                        $log_users[] = ['avatar'=>''];
                    }
                }
                $log_users = array_map(function($v){return getImage($v,'avatar', '', 0);}, array_column($log_users, 'avatar'));
                $list->online_count = $log_count;
                $list->online_users = $log_users;


                $data['live'] = (array)$list;

                if(!$uid){
                    $data['live']['url'] = createUrl('live/detail', array('id' => $id, 'pagetag' => config('app.live_detail')));
                }else{
                    $data['live']['url'] = createUrl('live/detail', array('id' => $id, 'pagetag' => config('app.live_detail'),
                                                                      'share_mark' => makeShareMark($id, 'live', $uid)
                    ));
                }
                if($list->activity_id){
                    $data->with_activty=1;
                    $data['activity'] = (array)$activity;
                }else{
                    $data->with_activty=0;
                }
                $ticket = DB::table('activity_ticket')->where('activity_id', $list->activity_id)->where('type', 2)->first();
                if (isset($ticket->id)) {
                    $data['live']['ticket_id'] = $ticket->id;
                } else {
                    $data['live']['ticket_id'] = 0;
                }

                if ($list->vip_id) {
                    $data['live']['vip_id'] = $list->vip_id;
                    $vip = DB::table('vip')->where('id', $list->vip_id)->first();
                    $data['live']['vip_name'] = $vip->name;
                    $data['live']['is_authorize'] = Vip::valid($list->vip_id, $uid);
                } else {
                    unset($data['live']['vip_id']);
                }

                //该直播对应的嘉宾
                $data['live']['guests'] = Relation::getGuests($id, 'live');

                //活动显示形式
                if($list->activity_id && count($brand_ids)){
                    $data['live']['show_type'] = 'activityAndBrand';
                }elseif($list->activity_id && !count($brand_ids)){
                    $data['live']['show_type'] = 'activityNotBrand';
                }elseif(!$list->activity_id && !count($brand_ids)){
                    $data['live']['show_type'] = 'none';
                }elseif(count($data['live']['guests'])){
                    $data['live']['show_type'] = 'guest';
                }else{
                    $data['live']['show_type'] = 'error';
                }


                //相关视频
                $videos = DB::table('video')->where('activity_id', $list->activity_id)->where('status', 1)->get();
                if(count($videos)){
                    foreach($videos as $k=>$v){
                        $video = call_user_func(['App\Models\Video', 'singleVideo'], $v->id);
                        $data['videos'][$k]['video_image'] = $video->image;
                        $data['videos'][$k]['subject'] = $video->subject;
                        $data['videos'][$k]['description'] = $video->description;
                        $data['videos'][$k]['likes'] = $video->favor_count;
                        $data['videos'][$k]['view'] = $video->view;
                        $data['videos'][$k]['id'] = $video->id;
                        $data['videos'][$k]['small_image'] = $video->small_image;
                        $data['videos'][$k]['video_type'] = $video->type;
                        $data['videos'][$k]['is_recommend'] = $video->is_recommend;
                    }
                }
            }


        return $data;
    }


    /**
     * 获取一个直播的详情
     *
     * @return array|bool
     */
    static function detail_v25($id, $uid = 0, $cache = 1)
    {
        $list = DB::table('live')
            ->where('id', $id)
            ->select(
                'distribution_id',
                'distribution_deadline',
                'live_url',
                'subject',
                'activity_id',
                'chatroom_id',
                'vip_id',
                'begin_time',
                'end_time',
                'description',
                'foreshow_url',
                'foreshow_duration',
                'share_summary',         //todo 增加返回分享文案字段 zhaoyf
                'activity_id'
            )
            ->first();

        if (!is_object($list)) {
            $data = [];
        } else {
            $ticket = DB::table('activity_ticket')->where('activity_id', $list->activity_id)->where('type', 2)
                ->first();
            is_object($ticket) ? $list->ticket = $ticket->price : $list->ticket = 0;
            if ($uid != 0) {
                is_object($ticket) ? $ticket_id = $ticket->id : $ticket_id = 0;
                $purchase = DB::table('user_ticket')->where('ticket_id', $ticket_id)->where('uid', $uid)->where('status', 1)->first();
                $subscribe = DB::table('user_subscription')->where('uid', $uid)->where('live_id', $id)->where('status', 1)->first();
                if (is_object($purchase)) {
                    $list->is_purchase = 1;
                } else {
                    $list->is_purchase = 0;
                }
                if (is_object($subscribe)) {
                    $list->subscribe = 1;
                } else {
                    $list->subscribe = 0;
                }
            } else {
                $list->is_purchase = 0;
                $list->subscribe = 0;
            }
            $list->time_now = time();
            if($list->activity_id){
                $activity = Activity::detail($list->activity_id);
                $follow = Activity::follow($activity->id, $uid);
                $activity->follow = $follow;
            }


            if ($list->begin_time <= time() && $list->end_time >= time()) {
                $list->situation = 'is_living';
            } elseif ($list->begin_time > time()) {
                $list->situation = 'future';
            } else {
                $list->situation = 'past';
            }


            //分享奖励
            $reward = Action::where('distribution_id', $list->distribution_id)->where('action', 'share')->first();
            $watch_distribution = Action::getDistributionByAction('live', $id, 'watch');

            if(is_object($reward)){
                $list->share_reward_unit = $reward->give;
                $list->share_reward_num = $reward->trigger;
            }else{
                $list->share_reward_unit = 'none';
                $list->share_reward_num = 0;
            }

            if($watch_distribution){
                $list->watch_reward_long = $watch_distribution->base;
            }else{
                $list->watch_reward_long = 0;
            }

            //直播相关品牌
            $brand_ids = DB::table('activity_brand')->where('activity_id', $list->activity_id)->lists('brand_id');
            if($brand_ids==[]){
                $brand_ids = Goods::where('live_id', $id)->groupBy('brand_id')->lists('brand_id')->toArray();
            }

            if(count($brand_ids)){
                $list->is_brand_live=1;
                $brands = Brand::singles()->where(['status' => 'enable'])->whereIn('id', $brand_ids)
                    ->addSelect('id','logo', 'name', 'investment_min', 'investment_max','keywords','details as detail')->get();
                $brands = Brand::process($brands)->toArray();
                $list->brands = $brands;
            }else{
                $list->is_brand_live=0;
            }



            //直播品牌商品
            $good_ids = Goods::where('live_id', $id)->select('id','brand_id', 'price', 'num', 'code', 'status','title')->get();
            if(count($good_ids)){
                $list->is_brand_good=1;
                $goods = [];
                foreach($good_ids as $k=>$v){
                    $good = Brand::singles()->where(['status' => 'enable'])->where('id', $v->brand_id)
                        ->addSelect('logo', 'name', 'investment_min', 'investment_max','keywords','summary')->first();
                    if(!is_object($good)){
                        continue;
                    }
                    $good = Brand::process($good)->toArray();
                    $good['brand_id'] = $v->brand_id;
                    $good['id'] = $v->id;
                    $good['price'] = $v->price;
                    $good['num'] = $v->num;
                    $good['code'] = $v->code;
                    $good['status'] = $v->status;
                    $good['goods_title'] = $v->title;
                    $goods[] = $good;
                }
                $list->goods = $goods;

            }else{
                $list->is_brand_good=0;
            }
            //该直播在线人数
            $log_uids = Log::where('vid', $id)->orderBy('created_at', 'desc')->lists('uid');

            $log_count = count($log_uids->toArray());
            $log_users = User::where('status',1)->whereIn('uid', $log_uids->toArray())->where('uid', '<>', $uid)
//                    ->orderBy('uid', 'desc')
                ->select('avatar')->limit(5)->get()->toArray();
            $user = DB::table('user')->where('uid', $uid)->select('avatar')->first();
            array_unshift($log_users, (array)$user);
            if($log_count>6 && count($log_users)<6){
                $count_diff = 6-count($log_users);
                for($i=0;$i<$count_diff;$i++){
                    $log_users[] = ['avatar'=>''];
                }
            }
            $log_users = array_map(function($v){return getImage($v,'avatar', '', 0);}, array_column($log_users, 'avatar'));
            $list->online_count = $log_count;
            $list->online_users = $log_users;
            $list->foreshow_duration = changeTimeType($list->foreshow_duration);
            $data['live'] = (array)$list;
            if($list->activity_id){
                $data['with_activty']=1;
                $data['live']['share_image'] = $activity->share_image;
                $data['activity'] = (array)$activity;
            }else{
                $data['with_activty']=0;
                $data['live']['share_image']= getImage('images/share_image.png', '', '');
            }

            $ticket = DB::table('activity_ticket')->where('activity_id', $list->activity_id)->where('type', 2)->first();
            if (isset($ticket->id)) {
                $data['live']['ticket_id'] = $ticket->id;
            } else {
                $data['live']['ticket_id'] = 0;
            }

            if ($list->vip_id) {
                $data['live']['vip_id'] = $list->vip_id;
                $vip = DB::table('vip')->where('id', $list->vip_id)->first();
                $data['live']['vip_name'] = $vip->name;
                $data['live']['is_authorize'] = Vip::valid($list->vip_id, $uid);
            } else {
                unset($data['live']['vip_id']);
            }

            //该直播对应的嘉宾
            $data['live']['guests'] = Relation::getGuests($id, 'live');

            //有没有绑定活动
            $list->activity_id ?$data['live']['with_activity'] = 1:$data['live']['with_activity'] = 0;

            //有没有绑定品牌
            count($brand_ids)>0 ?$data['live']['with_brand'] = 1: $data['live']['with_brand'] = 0;

            //有没有绑定嘉宾
            count($data['live']['guests'])>0 ?$data['live']['with_guest'] = 1:$data['live']['with_guest'] = 0;


            //相关视频
            $videos = DB::table('video')->where('live_id', $id)
                ->orWhere(function($query) use($list){
                    if($list->activity_id){
                        $query->orWhere('activity_id', $list->activity_id);
                    }
                })
                ->where('status', 1)->get();

            if(count($videos)){
                foreach($videos as $k=>$v){
                    $video = call_user_func(['App\Models\Video', 'singleVideo'], $v->id);
                    $data['videos'][$k]['video_image'] = $video->image;
                    $data['videos'][$k]['subject'] = $video->subject;
                    $data['videos'][$k]['description'] = $video->description;
                    $data['videos'][$k]['likes'] = $video->favor_count;
                    $data['videos'][$k]['view'] = $video->view;
                    $data['videos'][$k]['id'] = $video->id;
                    $data['videos'][$k]['small_image'] = $video->small_image;
                    $data['videos'][$k]['video_type'] = $video->type;
                    $data['videos'][$k]['is_recommend'] = $video->is_recommend;
                    $data['videos'][$k]['duration']= $video->duration;
                    $data['videos'][$k]['keywords']= $video->keywords;
                    if($video->created_at instanceof Carbon){
                        $data['videos'][$k]['created_at']= date('Y-m-d',$video->created_at->timestamp);
                    }else{
                        $data['videos'][$k]['created_at']= date('Y-m-d',$video->created_at);
                    }
                }
            }

            //相关资讯 通过直播的关联的品牌id去找资讯
            $news = News::whereIn('relation_id', $brand_ids)->where('status', 'show')->where('type', 'brand')->get();

//            dd($news);
            $news = News::process($news);
            foreach($news as $k=>$v){
                $data['news'][$k]['title'] = $v->title;
                $data['news'][$k]['detail'] = $v->detail;
                $data['news'][$k]['author'] = $v->author;
                $data['news'][$k]['id'] = $v->id;
                $data['news'][$k]['logo'] = $v->logo;
            }
        }


        return $data;
    }

    static function recommend($id, $cache = 1)
    {
        $data = Cache::has('live' . 'recommend') ? Cache::get('live' . 'recommend') : false;
        if ($data === false || $cache) {
            $data = DB::table('live')->where('end_time', '>', time())->where('id','<>', $id)->where('status', 0)
                ->orderBy('begin_time', 'asc')->select('id', 'live_url', 'subject', 'list_img', 'live_url')->take(3)->get();

            foreach ($data as $k => $v) {
                $v->list_img = getImage($v->list_img, 'live', '', 0);
            }

            Cache::put('live' . 'recommend', $data, 1440);
        }

        return $data;
    }

    /*
    * 作用:专版直播搜索
    * 参数:
    * 
    * 返回值:
    */
    public static function getVipLive($vip_id, $keywords, $pageSize = 15)
    {
        $lives = self::where('vip_id', $vip_id)
            ->where('subject', 'like', '%' . $keywords . '%')
            ->where('status', 0)
            ->where('end_time', '>', time())
            ->orderBy('begin_time', 'asc')
            ->paginate($pageSize);
//        dd($lives,$vip_id);
        //['id', 'subject', 'live_url','list_img', 'description','begin_time']
        $data = [];
        foreach ($lives as $key => $live) {
            $data[$key]['list_img'] = getImage($live->list_img, 'live');
            $data[$key]['begin_time'] = date("m/d H:i", $live->begin_time);
            $data[$key]['begin_time_format'] = date("m/d H:i", $live->begin_time);
            $data[$key]['id'] = $live->id;
            $data[$key]['subject'] = $live->subject;
            $data[$key]['live_url'] = $live->live_url;
            $data[$key]['description'] = $live->description;
            $data[$key]['view'] = $live->view;
            $data[$key]['is_being'] = $live->begin_time > time() ? 0 : 1;
            $data[$key]['is_live'] = self::isLive($live->begin_time, $live->end_time);
        }

        return $data;
    }

    /**
     * 根据某个键自增
     */
    public static function incre(Array $incre, Array $field)
    {
        self::where($field)->increment(array_keys($incre)[0], array_values($incre)[0]);
    }

    /*
    * 作用:判断视频是否真在直播
    * 参数:
    *
    * 返回值:
    */
    public static function isLive($btime, $etime)
    {
        if (time() > $btime && time() < $etime) {
            return 1;
        }

        return 0;
    }

    /*
     * 首页展示数据
     */
    static function getPublicData($obj, $uid=0)
    {
        $data = self::detail($obj->id, $uid);
        $base = self::getRow(['id'=>$obj->id]);
        $return = [];
        if($data && $base){
            $return['id'] = $obj->id;
            $return['subject'] = $data['live']['subject'];
            $return['detail_img'] = getImage($base->detail_img,'','');
            $return['list_img'] = getImage($base->list_img,'','');
            $return['activity_id'] = $data['live']['activity_id'];
            $return['description'] = trim(preg_replace('#<\w+(\s+.*?)?>|</\w+>|&nbsp;#', '', $data['live']['description']));
            $return['begin_time_format'] = date('m月d日 H:i',$data['live']['begin_time']);
            $return['lenth'] = '';
            $return['rebate'] = Distribution::Integer($data['live']['rebate']);
            //带有分享码的地址
            $return['url'] = $data['live']['url'];
            if($uid){
                $return['share_reward_num'] = Action::getDistributionByAction('live', $obj->id, 'share')->trigger;
                $return['share_reward_unit'] = Action::getDistributionByAction('live', $obj->id, 'share')->give;
            }
            $return['is_recommend'] = $base->is_recommend;
            $return['is_hot'] = $base->is_hot;
            $return['subscribe'] = $data['live']['subscribe'];
            $return['is_distribution'] = Distribution::IsDeadline($data['live']['distribution_id'],$data['live']['distribution_deadline']);//分销是否失效;
            $return['share_score'] = Distribution::shareScore($data['live']['distribution_id']);//分销分享得的积分
            if ($base->begin_time <= time() && $base->end_time >= time()) {
                $return['is_being'] = 1;
            } elseif ($base->begin_time > time()) {
                $return['is_being'] = 0;
            } else {
                $return['is_being'] = -1;
            }

            //获取5天前的时间戳
            $threeTime = time() - 3600 * 24 * 5;

            //是否最新
            if (strtotime($data->created_at) > $threeTime) {
                $return['is_new'] = 1;//最新
            } else {
                $return['is_new'] = 0;//非最新
            }

        }
        return $return;
    }

    /**
     * 获取直播下品牌商品信息
     */
    public static function Goodsdetail($live_id)
    {
        $datas = Goods::with('brand')->where('live_id', $live_id)
            ->select(DB::raw('count(brand_id) as counts, brand_id'))
            ->groupBy('brand_id')
            ->get()
            ->toArray();

        $data = [];
        foreach ($datas as $k => $v) {
            $data[$k]['count'] = $v['counts'];
            $data[$k]['brand_name'] = array_get($v, 'brand.name');
            $data[$k]['brand_logo'] = array_get($v, 'brand.logo');
        }
        return $data;
    }

    /*
     * 获取直播列表，包括未来的，和过往的列表
     *
     *liveBrandGoods
     * */

    public static function getLiveList($page,$pageCount=10){
        $nowTime=time();
        $start=($page-1)*$pageCount;

        $futureData = [];
        //绑定品牌的直播
        $boundLiveIdArr=LiveBrandGoods::select('live_id')->distinct()->get()->toArray();
        $boundLiveIds=collect($boundLiveIdArr)->flatten()->toArray();

        /**临时代码开始**单独添加新闻发布会直播，该直播不绑定品牌*****/
        $boundLiveIds[] = 235;
        /**临时代码结束**单独添加新闻发布会直播，该直播不绑定品牌*****/


        if($page == 1){
            $future=self::where('end_time','>',$nowTime)->where("status",0)->orderBy("begin_time",'asc')
                ->whereIn('id',$boundLiveIds)
                ->select('id','list_img','subject as title','begin_time','end_time','description as detail')->get();
            $futureData =self::showLiveList($future,$nowTime);
        }

        $past=self::select('id','list_img','subject as title','begin_time','end_time','description as detail')
            ->whereIn('id',$boundLiveIds)
            ->where('end_time','<=',$nowTime)->where("status",0)->orderBy("begin_time",'desc')
            ->skip($start)->take($pageCount)->get();
        $pastData =self::showLiveList($past,$nowTime);
        $data = array_merge($futureData,$pastData);
        if(empty($data)){
            $data = array(
                'future' =>[],
                'progress' =>[],
                'past' =>[],
            );
        }
        return $data;
    }

    public static function showLiveList($list,$nowTime){
        $data = [];
        $oneData=[];
        foreach ($list as $oneVideo){

            /**临时代码开始******单独添加新闻发布会直播，该直播不绑定品牌*/
            if($oneVideo['id'] == 235){
                $oneData=$oneVideo->toArray();
                $oneData["relative_brand"] = [];
            }
            else{
            /**临时代码结束****单独添加新闻发布会直播，该直播不绑定品牌*/
                $brands=$oneVideo->liveBrandGoods()->select('brand_id')->distinct()->get();
                $oneData=$oneVideo->toArray();
                foreach ($brands as $oneBrand){
                    $brandInfo=$oneBrand->brandInfo()->select('id','name')->first()->toArray();
                    $oneData["relative_brand"][]=$brandInfo;
                }
            /**临时代码开始******单独添加新闻发布会直播，该直播不绑定品牌*/
            }
            /**临时代码结束******单独添加新闻发布会直播，该直播不绑定品牌*/
            $oneData['list_img'] = getImage($oneData['list_img'],'live','');
            $oneData['detail'] = extractText($oneData['detail']);
            if($oneData['begin_time']>$nowTime){
                $data["future"][]=$oneData;
            }else if($nowTime>=$oneData['begin_time'] && $nowTime<$oneData['end_time']){
                $data["progress"][]=$oneData;
            } else{
                $data["past"][]=$oneData;
            }
        }
        return $data;
    }

    /*
     *
     * 直播详情
    */
    public static function getLiveDetail($liveId){
        $liveInfo=self::where("id",$liveId)->where('status',0)->first();
        $data=[];
        $nowTime=time();
        if(is_object($liveInfo)){
            $data=array(
                "id"=>$liveInfo["id"],
                "title"=>$liveInfo["subject"],
                "foreshow_url"=>$liveInfo["foreshow_url"],
                "begin_time"=>$liveInfo["begin_time"],
                "detail"=>$liveInfo["description"],
                "live_url"=>$liveInfo["live_url"],
                'live_img' => getImage($liveInfo["list_img"]),
                'share_summary'=>$liveInfo['share_summary']?$liveInfo['share_summary']:strip_tags($liveInfo['description']),
            );

            if($nowTime<$liveInfo['begin_time']){
                $data['situation']='future';
            }
            else if($nowTime>=$liveInfo['begin_time'] && $nowTime<=$liveInfo['end_time']){
                $data['situation']='is_living';
            }
            else{
                $data['situation']='past';
            }
            //直播相关品牌
            $brand_ids = DB::table('activity_brand')->where('activity_id', $liveInfo['activity_id'])->lists('brand_id');
            if($brand_ids==[]){
                $brand_ids = Goods::where('live_id', $liveInfo['id'])->groupBy('brand_id')->lists('brand_id')->toArray();
            }
            if(count($brand_ids)){
                $data['is_brand_live']=1;
            }else{
                $data['is_brand_live']=0;
            }
            $brandIds=$liveInfo->liveBrandGoods()->select('brand_id')->distinct()->get();

            foreach ($brandIds as $oneBrand){
                $brandInfo=$oneBrand->brandInfo()->select('id','name','logo','categorys1_id','investment_min','investment_max','brand_summary','keywords')
                    ->first()->toArray();
                $categoryInfo=DB::table("categorys")->where("id",$brandInfo["categorys1_id"])->select('name')->first();
                $keywords=explode(" ",$brandInfo["keywords"]);
                $data['brands'][]=array(
                    "id"=>$brandInfo["id"],
                    "title"=>$brandInfo["name"],
                    "logo"=> getImage($brandInfo["logo"]),
                    "category_name"=>$categoryInfo->name,
                    "investment_min"=>$brandInfo["investment_min"],
                    "investment_max"=>$brandInfo["investment_max"],
                    "brand_summary"=>$brandInfo["brand_summary"],
                    "keywords"=>$keywords
                );
                if($liveInfo['end_time']<$nowTime){
                    $newInfos=News::where('type','brand')->where("status",'show')->where('relation_id',$brandInfo['id'])->get();
                    foreach ($newInfos as $newInfo){
                        $detail = extractText($newInfo['detail']);
                        $data['news'][]=array(
                            'id'=>trim($newInfo['id']),
                            'title'=>trim($newInfo['title']),
                            'detail'=> $detail,
                            'author'=>trim($newInfo['author']),
                            'logo'=> getImage($newInfo['logo'],'news'),
                        );
                    }
                }
            }
            if($liveInfo['end_time']<$nowTime){
                $videoInfos=$liveInfo->hasManyVideo()->leftJoin('video_type','video_type.id','=','video.type')
                    ->where('video.status',1)
                    ->where('video.agent_status',1)
                    ->select('video.id','video.image',
                        'video.subject','video.description','video.favor_count'
                        ,'video.sham_view','video.is_recommend','video.keywords'
                        ,'video.duration','video.created_at','video_type.small_image','video_type.subject as typesub')
                    ->get();
                foreach ($videoInfos as $videoInfo){
                    $keywordArr=explode(' ',$videoInfo['keywords']);
                    $duration = intval($videoInfo['duration']);
                    $durationStr = changeTimeType($duration);
                    $data['videos'][]=array(
                        'id'=>trim($videoInfo['id']),
                        'video_image'=> getImage($videoInfo['image'],'video'),
                        'subject'=>trim($videoInfo['subject']),
                        'description'=>trim($videoInfo['description']),
                        'likes'=>trim($videoInfo['favor_count']),
                        'view'=>trim($videoInfo['sham_view']),
                        'small_image'=> getImage($videoInfo['small_image'],'video'),
                        'video_type'=>trim($videoInfo['typesub']),
                        'is_recommend'=>trim($videoInfo['is_recommend']),
                        'keywords'=>$keywordArr,
                        'duration'=> $durationStr,
                        'created_at'=>trim(strtotime($videoInfo['created_at'])),
                    );
                }
            }
            return $data;
        }
        else{
            return array(
                'message'=>"没有发现该直播",
                'error'=>1
            );
        }
        return $data;
    }

}