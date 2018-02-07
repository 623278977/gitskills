<?php
/**评论模型
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/5/5
 * Time: 11:10
 */
namespace App\Models\Comment;

use App\Models\Agent\Agent;
use App\Models\Agent\Score\AgentScoreLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Database\Eloquent\Collection;
use App\Models\Comment\Commentmessage;
use App\Models\Comment;
use App\Models\Message;
use App\Models\User\Entity as User;
use DB;
use App\Models\Brand\Entity as Brand;
use App\Services\Brand as BrandService;
use App\Models\Comment\Images;
use App\Models\Comment\Zhan;
use App\Models\Agent\Entity\_v010200 as Agentv010200;

class  Entity extends Model
{

    protected $dateFormat = 'U';

    protected $table = 'comment';

    //黑名单
    protected $guarded = [];

    //关联用户表
    public function belongsToUser(){
        return $this->belongsTo( User::class,'uid','uid');
    }

    //关联评论图片表
    public function hasManyCommentImages()
    {
        return $this->hasMany(Images::class,'comment_id','id');
    }

    //关联评论点赞表
    public function comment_zhan(){
        return $this->hasMany(Zhan::class,'comment_id','id');
    }

    //关联经纪人表
    public function agent()
    {
        return $this->belongsTo(Agent::class, 'uid' , 'id');
    }
    public static function getRow()
    {
        $query = DB::table('comment')
            ->leftJoin('comment as pComment', 'comment.upid', '=', 'pComment.id')
            ->leftJoin('user', 'comment.uid', '=', 'user.uid')
            ->leftJoin('user as pUser', 'pComment.uid', '=', 'pUser.uid')
            ->leftJoin('zone', 'user.zone_id', '=', 'zone.id')
            ->where('comment.status', 1)
            ->select(
                'user.nickname as c_nickname',
                'pUser.nickname as p_nickname',
                'comment.created_at',
                'comment.content',
                'comment.nickname',
                'comment.form',
                'comment.status as c_status',
                'pComment.content as pContent',
                'pComment.status as p_status',
                'comment.likes',
                'comment.id',
                'pComment.id as pId',
                'user.avatar',
                'user.uid as c_uid',
                'pUser.uid as p_uid',
                'zone.name as zone_name'
            );

        return $query;
    }

    /**
     * 获取和经纪人相关
     *
     * @param $id
     * @param $type
     * @param int $uid
     * @param int $pre_id
     * @param int $page
     * @param int $pagesize
     * @param int $cache
     * @param int $section
     * @param string $use
     * @return array|bool
     */
    public static function getAgentRows()
    {
        $query = DB::table('comment')
            ->leftJoin('comment as pComment', 'comment.upid', '=', 'pComment.id')
            ->leftJoin('agent', 'comment.uid', '=', 'agent.id')
            ->leftJoin('agent as pUser', 'pComment.uid', '=', 'pUser.id')
            ->leftJoin('zone', 'agent.zone_id', '=', 'zone.id')
            ->where('comment.status', 1)
            ->select(
                'agent.nickname as c_nickname',
                'pUser.nickname as p_nickname',
                'comment.created_at',
                'comment.content',
                'comment.nickname',
                'comment.form',
                'comment.status as c_status',
                'pComment.content as pContent',
                'pComment.status as p_status',
                'comment.likes',
                'comment.id',
                'pComment.id as pId',
                'agent.avatar',
                'agent.id as c_uid',
                'pUser.id as p_uid',
                'zone.name as zone_name'
            );

        return $query;
    }


    /*
     * 作用：获取评论
     * 参数：$id 目标id
     *      $type 目标类型(活动，视频，直播，商机)
     *      $uid 登录用户的uid
     *      $pre_id 需要特别排在第一位的评论的id
     *      $section 是否分段显示，视频，活动，商机需要分段显示，精彩+普通，直播不需要
     * 返回值：array
     */
    static function comments($id, $type, $uid = 0, $pre_id = 0, $page = 1, $pagesize = 10, $cache = 1, $section = 1 , $use='normal')
    {
        if ($pre_id != 0) {
            $comment = self::where('id', $pre_id)->first();
            //这种情况下不传目标id和目标类型
            $type = $comment->type;
            $id = $comment->post_id;
        }
        $pre = array();
        $return = Cache::has($type . 'comment' . $id . 'pre' . $pre_id . 'uid' . $uid) ? Cache::get($type . 'comment' . $id . 'pre' . $pre_id . 'uid' . $uid) : false;
        if ($return === false || $cache) {
            $return = array();
            if ($uid != 0) {
                $s_cids = DB::table('comment_zhan')->where('uid', $uid)->where('status', 1)->lists('comment_id');
            }
            if ($pre_id != 0) {
                $pre = self::getRow()->where('comment.id', $pre_id)->orderBy('comment.likes', 'desc')->first();
                $pre = array($pre);
            }
            $wonderfuls = DB::table('comment')->where('type', $type)->where('is_wonderful', 1)->where('post_id', $id)->get();
            $likes = DB::table('comment')->where('type', $type)->where('likes', '>', 10)->where('post_id', $id)
                ->orderBy('likes', 'desc')->take(2)->get();

            $likes_id = $wonderfuls_id = array();

            foreach ($likes as $k => $v) {
                $likes_id[] = $v->id;
            }
            foreach ($wonderfuls as $k => $v) {
                $wonderfuls_id[] = $v->id;
            }
            $diffs = array_diff($likes_id, $wonderfuls_id);
            $amaze_id = array_slice(array_merge($wonderfuls_id, $diffs), 0,5);

            $amaze = self::getRow()->whereIn('comment.id', $amaze_id)->orderBy('comment.likes', 'desc')->get();


            $all = self::getRow()->where('comment.type', $type)->where('comment.post_id', $id)
                ->orderBy('comment.created_at', 'desc')->get();

            self::formatTime($amaze, 'amaze');
            self::formatTime($all, 'all');
            if (isset($pre)) {
                self::formatTime($pre, 'pre');
            }
            
            //如果是大屏幕显示
            if($use=='bigScreen'){
                $section = 0;
                $all = self::getRow()->where('comment.type', $type)->where('comment.post_id', $id)
                    ->where('form','normal')->where('audit','adopt')->orderBy('comment.created_at', 'desc')->get();
            }

            if ($section == 1) {
                $data = array_merge($pre, $amaze, $all);
            } else {
                $data = $all;
            }



            $data = array_slice($data, ($page - 1) * $pagesize, $pagesize);
            $ids = [];
            foreach ($data as $k => $v) {
                if (isset($s_cids)) {
                    if (isset($s_cids) && in_array($v->id, $s_cids)) {
                        $v->is_zhan = 1;
                    } else {
                        $v->is_zhan = 0;
                    }
                }

                if($v->pId!=0 && !$v->p_nickname){
                    $p_comments = DB::table('comment')->where('id', $v->pId)->first();
                    $v->p_nickname = $p_comments->nickname;
                }

                if($v->c_uid==0){
                    $v->c_nickname = $v->nickname;
                }
//                $v->created_at = timeDiff($v->created_at);

                $v->avatar = getImage($v->avatar, 'avatar', '', 0);
                //获取该条评论的照片
                $v->images = self::getImages($v->id);
                $v->pImages = self::getImages($v->pId);
                $ids[] = $v->id;

                //如果是品牌商品购买
                if($v->form=='brand'){
                    $items = \DB::table('orders_items')
                        ->leftJoin('live_brand_goods', 'live_brand_goods.id', '=', 'orders_items.product_id')
                        ->select('orders_items.product_id','orders_items.price','live_brand_goods.title', 'live_brand_goods.brand_id')
                        ->where('orders_items.id', $v->content)->first();

                    $brand = Brand::singles()->where(['status' => 'enable'])->where('id', $items->brand_id)
                        ->addSelect('logo', 'name', 'investment_min', 'investment_max','keywords')->first();
                    $brand = Brand::process($brand)->toArray();
                    $brand['price'] = $items->price;
                    $brand['brand_id'] = $items->product_id;
                    $brand['goods_title'] = $items->title;
                    $v->brand_good = $brand;
                }
            }
            sort($ids);
            isset($ids[0])?$min = $ids[0]:$min = 0;
            isset($ids[0])?$max = end($ids):$max = 0;
            $return['amaze_count'] = count($amaze);
            $section==0 && $return['amaze_count'] = 0;
            $return['all_count'] = count($all);
            $return['data'] = $data;
            $return['min_id'] = $min;
            $return['max_id'] = $max;


            Cache::put($type . 'comment' . $id . 'pre' . $pre_id . 'uid' . $uid, $return, 1440);
        }

        return $return;
    }

    /**
     *  获取和经纪人相关的评论
     *
     * @param $id
     * @param $type
     * @param int $uid
     * @param int $pre_id
     * @param int $page
     * @param int $pagesize
     * @param int $cache
     * @param int $section
     * @param string $use
     * @return array
     * @internal param $where
     * @internal param $fromId
     * @internal param int $fecthSize
     */
    static function agentComments($id, $type, $uid = 0, $pre_id = 0, $page = 1, $pagesize = 10, $cache = 1, $section = 1 , $use='normal')
    {
        if ($pre_id != 0) {
            $comment = self::where('id', $pre_id)->first();

            //这种情况下不传目标id和目标类型
            $type = $comment->type;
            $id = $comment->post_id;
        }
        $pre = array();
        $return = Cache::has($type . 'comment' . $id . 'pre' . $pre_id . 'uid' . $uid) ? Cache::get($type . 'comment' . $id . 'pre' . $pre_id . 'uid' . $uid) : false;
        if ($return === false || $cache) {
            $return = array();
            if ($uid != 0) {
                $s_cids = DB::table('comment_zhan')->where('uid', $uid)->where('status', 1)->lists('comment_id');
            }
            if ($pre_id != 0) {
                $pre = self::getAgentRows()->where('comment.id', $pre_id)->orderBy('comment.likes', 'desc')->first();
                $pre = array($pre);
            }
            $wonderfuls = DB::table('comment')->where('type', $type)->where('is_wonderful', 1)->where('post_id', $id)->get();
            $likes = DB::table('comment')->where('type', $type)->where('likes', '>', 10)->where('post_id', $id)
                ->orderBy('likes', 'desc')->take(2)->get();

            $likes_id = $wonderfuls_id = array();

            foreach ($likes as $k => $v) {
                $likes_id[] = $v->id;
            }
            foreach ($wonderfuls as $k => $v) {
                $wonderfuls_id[] = $v->id;
            }
            $diffs = array_diff($likes_id, $wonderfuls_id);
            $amaze_id = array_slice(array_merge($wonderfuls_id, $diffs), 0,5);

            $amaze = self::getAgentRows()->whereIn('comment.id', $amaze_id)->orderBy('comment.likes', 'desc')->get();


            $all = self::getAgentRows()->where('comment.type', $type)->where('comment.post_id', $id)
                ->orderBy('comment.created_at', 'desc')->get();

            self::formatTime($amaze, 'amaze');
            self::formatTime($all, 'all');
            if (isset($pre)) {
                self::formatTime($pre, 'pre');
            }

            //如果是大屏幕显示
            if($use=='bigScreen'){
                $section = 0;
                $all = self::getAgentRows()->where('comment.type', $type)->where('comment.post_id', $id)
                    ->where('form','normal')->where('audit','adopt')->orderBy('comment.created_at', 'desc')->get();
            }

            if ($section == 1) {
                $data = array_merge($pre, $amaze, $all);
            } else {
                $data = $all;
            }



            $data = array_slice($data, ($page - 1) * $pagesize, $pagesize);

            $ids = [];
            foreach ($data as $k => $v) {
                if (isset($s_cids)) {
                    if (isset($s_cids) && in_array($v->id, $s_cids)) {
                        $v->is_zhan = 1;
                    } else {
                        $v->is_zhan = 0;
                    }
                }

                if($v->pId!=0 && !$v->p_nickname){
                    $p_comments = DB::table('comment')->where('id', $v->pId)->first();
                    $v->p_nickname = $p_comments->nickname;
                }

                if($v->c_uid==0){
                    $v->c_nickname = $v->nickname;
                }

                $v->avatar = getImage($v->avatar, 'avatar', '', 0);
                //获取该条评论的照片
                $v->images = self::getImages($v->id);
                $v->pImages = self::getImages($v->pId);
                $ids[] = $v->id;

                //如果是品牌商品购买
                if($v->form=='brand'){
                    $items = \DB::table('orders_items')
                        ->leftJoin('live_brand_goods', 'live_brand_goods.id', '=', 'orders_items.product_id')
                        ->select('orders_items.product_id','orders_items.price','live_brand_goods.title', 'live_brand_goods.brand_id')
                        ->where('orders_items.id', $v->content)->first();

                    $brand = Brand::singles()->where(['status' => 'enable'])->where('id', $items->brand_id)
                        ->addSelect('logo', 'name', 'investment_min', 'investment_max','keywords')->first();
                    $brand = Brand::process($brand)->toArray();
                    $brand['price'] = $items->price;
                    $brand['brand_id'] = $items->product_id;
                    $brand['goods_title'] = $items->title;
                    $v->brand_good = $brand;
                }
            }
            sort($ids);
            isset($ids[0])?$min = $ids[0]:$min = 0;
            isset($ids[0])?$max = end($ids):$max = 0;
            $return['amaze_count'] = count($amaze);
            $section==0 && $return['amaze_count'] = 0;
            $return['all_count'] = count($all);
            $return['data'] = $data;
            $return['min_id'] = $min;
            $return['max_id'] = $max;


            Cache::put($type . 'comment' . $id . 'pre' . $pre_id . 'uid' . $uid, $return, 1440);
        }

        return $return;
    }


    public static function freshComments($id, $type, $uid, $where, $fromId, $fecthSize = 0, $use='normal')
    {
        if ($uid != 0) {
            $s_cids = DB::table('comment_zhan')->where('uid', $uid)->where('status', 1)->lists('comment_id');
        }

        if($use=='big_screen'){
            $all = self::getRow()->where('comment.type', $type)->where('comment.post_id', $id)->where('comment.form', 'normal')
                ->where('comment.audit','adopt')->orderBy('comment.created_at', 'asc');

        }else{
            $all = self::getRow()->where('comment.type', $type)->where('comment.post_id', $id)->orderBy('comment.created_at', 'desc');
        }
        if ($where == 'new') {
            $all = $all->where('comment.id', '>', $fromId);
        }

        if ($where == 'history') {
            $all = $all->where('comment.id', '<', $fromId);
        }

        if ($fecthSize) {
            $all = $all->take($fecthSize)->get();
        } else {
            $all = $all->get();
        }



        //如果此次没有返回数据，那么
        if($use=='big_screen' && count($all) == 0){
            $all = self::getRow()->where('comment.type', $type)->where('comment.post_id', $id)->where('comment.form', 'normal')
                ->where('comment.audit','adopt')->orderBy(\DB::raw('RAND()'))
                ->take(10)->get();
        }

        self::formatTime($all, 'all');

        $ids = [];
        foreach ($all as $k => $v) {
            if($use=='app'){
                $v->content=strip_tags($v->content);
            }

            if (isset($s_cids) && in_array($v->id, $s_cids)) {
                $v->is_zhan = 1;
            } else {
                $v->is_zhan = 0;
            }
            $v->avatar = getImage($v->avatar, 'avatar', '', 0);
            //获取该条评论的照片
            $v->images = self::getImages($v->id);
            $v->pImages = self::getImages($v->pId);
            $ids[] = $v->id;
            if($v->pId!=0 && !$v->p_nickname){
                $p_comments = DB::table('comment')->where('id', $v->pId)->first();
                $v->p_nickname = $p_comments->nickname;
            }

            if($v->c_uid==0){
                $v->c_nickname = $v->nickname;
            }

            //如果是品牌商品购买
            if($v->form=='brand'){
                $items = \DB::table('orders_items')
                    ->leftJoin('live_brand_goods', 'live_brand_goods.id', '=', 'orders_items.product_id')
                    ->select('orders_items.product_id','orders_items.price','orders_items.price' , 'live_brand_goods.title', 'live_brand_goods.brand_id')
                    ->where('orders_items.id', $v->content)->first();
                $brand = Brand::singles()->where(['status' => 'enable'])->where('id', $items->brand_id)
                    ->addSelect('logo', 'name', 'investment_min', 'investment_max','keywords')->first();
                $brand = Brand::process($brand)->toArray();
                $brand['price'] = $items->price;
                $brand['brand_id'] = $items->product_id;
                $brand['goods_title'] = $items->title;
                $v->brand_good = $brand;
            }

            //去掉市处理
            if($v->type!='pay'){
                $v->zone_name = starReplace(str_replace('市','',$v->zone_name), 4);
            }else{
                $v->zone_name = starReplace(str_replace('市','',$v->zone_name), 4);
            }

        }
        sort($ids);
        isset($ids[0])?$min = $ids[0]:$min = 0;
        isset($ids[0])?$max = end($ids):$max = $fromId;

        if($use=='big_screen') {
            $all_count = self::getRow()->where('comment.type', $type)->where('comment.post_id', $id)
                ->where('comment.status', 1)->where('comment.audit', 'adopt')->where('comment.form', 'normal')
                ->count();
        }else{
            $all_count = self::getRow()->where('comment.type', $type)->where('comment.post_id', $id)->where('comment.status', 1)->count();
        }
//        dd(count($all));
        return ['data'=>$all, 'min_id'=>$min, 'max_id'=>$max, 'all_count'=>$all_count];
    }

    /*
     * 作用：根据评论id获取该评论的id
     * 参数：$id 评论id
     * 返回值：
     */
    public static function getImages($id)
    {
        $images = Images::where('comment_id', $id)->select('url')->get();
        $images = $images->map(
            function ($image) {
                return getImage($image->url, '', '', 0);
            }
        );

        return $images;
    }

    /**
     * 格式化时间
     */
    public static function formatTime(Array $arr, $type)
    {
        foreach ($arr as $k => $v) {
            $v->created_at_time = $v->created_at;
            $v->created_at = timeDiff($v->created_at);
            $v->type = $type;
        }
    }

    /**
     * 新增一个评论
     *
     * @param $post_id
     * @param $uid
     * @param $type
     * @param $content
     * @param $upid
     * @param $nickname
     * @param $uid_at
     * @param array $images
     * @param string $form
     * @param string $audit
     * @param string $level
     * @param string $grade_num
     * @return static
     */
    static function add($post_id, $uid, $type, $content, $upid, $nickname, $uid_at, Array $images = array(), $form = 'normal', $audit = 'pending', $level = '', $grade_num = '')
    {
        $insert = self::create(
            [
                'post_id'  => $post_id,
                'uid'      => $uid,
                'type'     => $type,
                'content'  => $content,
                'upid'     => $upid,
                'nickname' => $nickname,
                'form'     => $form,
                'audit'    => $audit,
                'level'    => $level,
                'grade_num'=> $grade_num
            ]
        );

        foreach ($images as $k => $v) {
            Images::create(
                [
                    'comment_id' => $insert->id,
                    'url'        => $v,
                ]
            );
        }


        $url = '';
        if ($type == 'Video') {
            $url = createUrl('webapp/vod/detail', ['id' => $post_id, 'uid' => $uid, 'pagetag' => config('app.video_detail')], 'web');
        }

        if ($type == 'Activity') {
            $url = createUrl('webapp/activity/detail', ['id' => $post_id, 'uid' => $uid, 'maker_id' => 0], 'web');
        }

        if ($type == 'Opportunity') {
            $url = createUrl('webapp/business/goverment', ['id' => $post_id, 'uid' => $uid, 'pagetag' => config('app.opportunity_detail')], 'web');
        }

        if ($type == 'Live') {
            $url = createUrl('webapp/live/detail', ['id' => $post_id, 'uid' => $uid, 'pagetag' => config('app.live_detail')], 'web');
        }

        if ($type == 'News') {
            $url = createUrl('webapp/headline/detail', ['id' => $post_id, 'uid' => $uid, 'pagetag' => config('app.news_detail')], 'web');
        }

        if ($upid != 0) {
            $p_comment = self::where('id', $upid)->first();
            Message::create(
                [
                    'title'     => '回复了你的评论',
                    'uid'       => $p_comment->uid,
                    'content'   => $content,
                    'type'      => 4,
                    'reply_uid' => $uid,
                    'url'       => $url,
                    'post_id'   => $insert->id,
                    'send_time' => time(),
                ]
            );
        }

        if ($type == 'Lesson') {
            //给积分
            Agentv010200::add($uid, AgentScoreLog::$TYPES_SCORE[24], 24, '对视频课程留言', $post_id);
        }

        if (count($uid_at) > 0) {
            foreach ($uid_at as $k => $v) {
                Message::create(
                    [
                        'title'     => '评论@了你',
//                    'title' => $nickname . '评论@了你',
                        'uid'       => $v,
                        'content'   => $content,
                        'type'      => 6,
                        'reply_uid' => $uid,
                        'url'       => $url,
                        'post_id'   => $insert->id,
                        'send_time' => time(),
                    ]
                );
                DB::table('comment_at')->insert(
                    [
                        'comment_id' => $insert->id,
                        'uid'        => $v,
                        'created_at' => time(),
                        'updated_at' => time()
                    ]
                );
            }
        }

        return $insert;
    }

    /**
     * 点赞或者取消点赞
     */
    static function zhan($id, $uid, $type)
    {
        $comment = DB::table('comment')->where('id', $id)->first();
        $zhan = DB::table('comment_zhan')->where('comment_id', $id)->where('uid', $uid)->first();

        if ($type == 1) {
            if (is_object($zhan)) {
                if ($zhan->status == 1) {
                    return 1;
                } elseif ($zhan->status == 0) {
                    DB::table('comment_zhan')->where('comment_id', $id)->where('uid', $uid)->update(
                        [
                            'status'     => 1,
                            'updated_at' => time()
                        ]
                    );
                    DB::table('comment')->where('id', $id)->increment('likes');
                }
            } else {
                DB::table('comment_zhan')->insert(
                    [
                        'comment_id' => $id,
                        'uid'        => $uid,
                        'status'     => 1,
                        'created_at' => time(),
                        'updated_at' => time()
                    ]
                );
                DB::table('comment')->where('id', $id)->increment('likes');
                $comment = DB::table('comment')->where('id', $id)->first();


                $url = '';
                if ($comment->type == 'Video') {
                    $url = createUrl('webapp/vod/detail', ['id' => $comment->post_id, 'uid' => $uid, 'pagetag' => '05-4'], 'web');
                }

                if ($comment->type == 'Activity') {
                    $url = createUrl('webapp/activity/detail', ['id' => $comment->post_id, 'uid' => $uid, 'maker_id' => 0], 'web');
                }

                if ($comment->type == 'Opportunity') {
                    $url = createUrl('webapp/business/goverment', ['id' => $comment->post_id, 'uid' => $uid, 'pagetag' => '12-2'], 'web');
                }


                if ($comment->type == 'Live') {
                    $url = createUrl('webapp/live/detail', ['id' => $comment->post_id, 'uid' => $uid, 'pagetag' => '04-9'], 'web');
                }

                if ($comment->type == 'News') {
                    $url = createUrl('webapp/headline/detail', ['id' => $comment->post_id, 'uid' => $uid, 'pagetag' => config('app.news_detail')], 'web');
                }


                $user = DB::table('user')->where('uid', $uid)->first();
                Message::create(
                    [
                        'title'     => '赞了你的评论',
//                    'title' => $user->nickname . '     赞了你的评论',
                        'uid'       => $comment->uid,
                        'content'   => $comment->content,
                        'type'      => 5,
                        'reply_uid' => $uid,
                        'url'       => $url,
                        'send_time' => time(),
                    ]
                );
            }
        }

        if ($type == 0) {
            if (!is_object($zhan)) {
                return 2;
            } else {
                if ($zhan->status == 0) {
                    return AjaxCallbackMessage('你已经取消过赞了，不需要重复点赞', false);
                } elseif ($zhan->status == 1) {
                    DB::table('comment_zhan')->where('comment_id', $id)->where('uid', $uid)
                        ->update(['status' => 0, 'updated_at' => time()]);
                    DB::table('comment')->where('id', $id)->decrement('likes');
                }
            }
        }

        return true;
    }

    /**
     * 删除一个评论
     *
     * @return array|bool
     */
    static function deleteComment($id, $uid)
    {
        $exist = self::where('id', $id)->first();
        if (!is_object($exist)) {
            return false;
        }
        if ($exist->uid != $uid || $uid == 0) {
            return false;
        }
        DB::table('comment')->where('id', $id)->where('uid', $uid)->delete();

        return true;
    }

    /**
     *根据评论id获取他有多少兄弟姐妹（包含自己）
     */
    static function totalComments($id)
    {
        $self = DB::table('comment')->where('id', $id)->first();
        $count = DB::table('comment')->where('type', $self->type)->where('post_id', $self->post_id)->count();

        return $count;
    }

    /**
     *根据目标id及目标类型获取其目前含有多少评论
     */
    static function commentsCount($type, $post_id)
    {
        $count = DB::table('comment')->where('type', $type)->where('post_id', $post_id)->count();

        return $count;
    }

    /**
     *留言墙
     */
    public static function freshComments_ly($id, $type, $uid, $where, $fromId, $fecthSize = 0, $use='normal')
    {
        if ($uid != 0) {
            $s_cids = DB::table('comment_zhan')->where('uid', $uid)->where('status', 1)->lists('comment_id');
        }

        if($use=='big_screen'){
            $all = self::getRow()->where('comment.type', $type)->where('comment.post_id', $id)->where('comment.form', 'normal')
                ->where('comment.audit','adopt')->orderBy('comment.created_at', 'asc');
        }else{
            $all = self::getRow()->where('comment.type', $type)->where('comment.post_id', $id)->orderBy('comment.created_at', 'desc');
        }
        if ($where == 'new') {
            $all = $all->where('comment.id', '>', $fromId);
        }

        if ($where == 'history') {
            $all = $all->where('comment.id', '<', $fromId);
        }

        if ($fecthSize) {
            $all = $all->take($fecthSize)->get();
        } else {
            $all = $all->get();
        }

        //如果此次没有返回数据，那么
//        if($use=='big_screen' && count($all) == 0){
//            $all = self::getRow()->where('comment.type', $type)
//                ->where('comment.post_id', $id)
//                ->where('comment.form', 'normal')
//                ->where('comment.audit','adopt')
//                ->take(10)->get();
//        }

        self::formatTime($all, 'all');

        $ids = [];
        foreach ($all as $k => $v) {
            if($use=='app'){
                $v->content=strip_tags($v->content);
            }

            if (isset($s_cids) && in_array($v->id, $s_cids)) {
                $v->is_zhan = 1;
            } else {
                $v->is_zhan = 0;
            }
            $v->avatar = getImage($v->avatar, 'avatar', '', 0);
            //获取该条评论的照片
            $v->images = self::getImages($v->id);
            $v->pImages = self::getImages($v->pId);
            $ids[] = $v->id;
            if($v->pId!=0 && !$v->p_nickname){
                $p_comments = DB::table('comment')->where('id', $v->pId)->first();
                $v->p_nickname = $p_comments->nickname;
            }

            if($v->c_uid==0){
                $v->c_nickname = $v->nickname;
            }

            //如果是品牌商品购买
            if($v->form=='brand'){
                $items = \DB::table('orders_items')
                    ->leftJoin('live_brand_goods', 'live_brand_goods.id', '=', 'orders_items.product_id')
                    ->select('orders_items.product_id','orders_items.price','orders_items.price' , 'live_brand_goods.title', 'live_brand_goods.brand_id')
                    ->where('orders_items.id', $v->content)->first();
                $brand = Brand::singles()->where(['status' => 'enable'])->where('id', $items->brand_id)
                    ->addSelect('logo', 'name', 'investment_min', 'investment_max','keywords')->first();
                $brand = Brand::process($brand)->toArray();
                $brand['price'] = $items->price;
                $brand['brand_id'] = $items->product_id;
                $brand['goods_title'] = $items->title;
                $v->brand_good = $brand;
            }

            //去掉市处理
            $v->zone_name = str_replace('市','',$v->zone_name);

        }
        sort($ids);
        isset($ids[0])?$min =$ids[0]:$min = 0;
        isset($ids[0])?$max = end($ids):$max = $fromId;

        if($use=='big_screen') {
            $all_count = self::getRow()->where('comment.type', $type)->where('comment.post_id', $id)
                ->where('comment.status', 1)->where('comment.audit', 'adopt')->where('comment.form', 'normal')
                ->count();
        }else{
            $all_count = self::getRow()->where('comment.type', $type)->where('comment.post_id', $id)->where('comment.status', 1)->count();
        }
        return ['data'=>$all, 'min_id'=>$min, 'max_id'=>$max, 'all_count'=>$all_count];
    }

    /**
     *获取指定id下资讯的评论数
     */
    static function ConmmentCount($id,$relation = 'News')
    {
        $count = self::where('type', $relation)
            ->where('post_id', $id)
            ->where('audit', '!=', 'reject')
            ->count();
        return $count;

    }

    /*
     *品牌评论
     *
     * */

    public static function getCommentList($brandId,$uid,$page,$pageSize){
        $brandInfo = Brand::where('id',$brandId)->where('status','enable')->first();
        if(!is_object($brandInfo)){
            return array(
                'message' => '请输入有效的品牌id',
                'error' => 1
            );
        }
        $startIndex = ($page-1)*$pageSize;
        $data = [];

        $commentList = self::with('belongsToUser','hasManyCommentImages','comment_zhan')
            ->with(['hasManyCommentImages'=>function($query){
                $query->orderBy('created_at','asc');
            }])
            ->with(['comment_zhan'=>function($query)use($uid){
                $query->where('uid',$uid);
                $query->where('status',1);
            }])
            ->where(function($query)use($brandId){
                $query->where('post_id',$brandId);
                $query->where('type','Brand');
                $query->where('status',1);
                $query->where('audit','adopt');
            })
            ->orderBy('top','desc')
            ->orderBy('created_at','desc')
            ->skip($startIndex)->take($pageSize)
            ->get()->toArray();

        foreach ($commentList as $oneComment){
            $arr = [];
            $imgArr = [];
            foreach ($oneComment['has_many_comment_images'] as $oneImgInfo){
                $imgArr[] = getImage($oneImgInfo['url'], '', '');
                if(count($imgArr) >= 3){
                    break;
                }
            }
            $arr = array(
                'id' => trim($oneComment['id']),
                'avatar' => getImage($oneComment['belongs_to_user']['avatar'], 'avatar', ''),
                'nickname' => trim($oneComment['nickname']),
                'created_at' => trim($oneComment['created_at']),
                'content' => trim($oneComment['content']),
                'img_url' => $imgArr,
                'likes' => trim($oneComment['likes']),
                'views' => '0',
                'grade' => trim($oneComment['grade_num']),
                'is_zan' => empty($oneComment['comment_zhan']) ? '1' : '0',
            );
            $data[] = $arr;
        }
        return $data;
    }

    /*
     * 获取该品牌下评论总数
     *
     * */
    public static function getBrandCommentCount($brandId){
        $count = self::where(function ($query)use($brandId){
            $query->where('type','Brand');
            $query->where('post_id',$brandId);
            $query->where('status',1);
            $query->where('audit','adopt');
        })->count();
        return $count;
    }

}