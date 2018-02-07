<?php
/**活动模型
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/5/5
 * Time: 11:10
 */
namespace App\Models\Activity;

use App\Models\Distribution\Action;
use App\Models\User\Entity as User;
use App\Models\User\Share;
use App\Models\Distribution\Entity as Distribution;
use App\Models\User\Ticket as UserTicket;
use App\Models\Zone;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use App\Models\Vip\Entity as Vip;
use \DB;
use App\Models\ScoreLog;
use App\Models\Activity\Sign as ActivitySign;
use App\Models\Activity\Ticket as ActivityTicket;
use App\Models\Message;
use App\Models\Live\Entity as Live;
use App\Models\Brand\Entity as Brands;
use App\Models\Live\Subscribe;
class Entity extends Model
{

    protected $dateFormat = 'U';

    protected $table = 'activity';

    //黑名单
    protected $guarded = [];


    public function brand(){
        return $this->hasMany('App\Models\Activity\Brand', 'activity_id', 'id');
    }

    public function brands()
    {
        return $this->belongsToMany('App\Models\Brand\Entity','activity_brand', 'activity_id', 'brand_id');
    }


    public function favorite($uid = 0)
    {
        if ($uid) {
            return $this->hasMany('App\Models\User\Favorite', 'post_id')->where(
                array(
                    'model' => 'activity',
                    'uid'   => $uid
                )
            );
        } else {
            return $this->hasMany('App\Models\User\Favorite', 'post_id')->where(
                array(
                    'model' => 'activity'
                )
            );
        }
    }

    public function maker()
    {
        return $this->belongsTo('App\Models\Activity\Maker', 'id', 'activity_id');
    }

    public function activitymaker()
    {
        return $this->hasMany('App\Models\Activity\Maker', 'activity_id', 'id');
    }

    public function makers()
    {
        return $this->belongsToMany('App\Models\Maker\Entity', 'activity_maker', 'activity_id', 'maker_id');
    }

    public function ticket()
    {
        return $this->belongsTo('App\Models\Activity\Ticket', 'id', 'activity_id');
    }

    public function activity_tickets()
    {
        return $this->hasMany('App\Models\Activity\Ticket', 'activity_id');
    }

    public function users()
    {
        return $this->belongsToMany('App\Models\User\Entity', 'user_ticket', 'activity_id', 'uid')->where('user_ticket.status', 1);;
    }

    public function publisher()
    {
        return $this->hasOne('App\Models\Activity\Publisher', 'id', 'publisher_uid');
    }

    public function partner()
    {
        return $this->hasOne(\App\Models\CityPartner\Entity::class, 'uid', 'partner_uid');
    }


    //关联直播

    public function hasOneLive()
    {
        return $this->hasOne(Live::class,'activity_id','id');
    }

    static function getBase($activity, $type = 0, $uid=0)
    {
        if (!isset($activity->id)) {
            return array();
        }
        $data = array();
        $data['id'] = $activity->id;
        $data['vip_id'] = $activity->vip_id;
        $data['list_img'] = getImage($activity->list_img, 'activity', '');
        $data['share_image'] = getImage($activity->share_image ?: 'images/share_image.png', '', '');
        if(!$uid){
            $data['url'] = createUrl('activity/detail', array('id' => $activity->id, 'pagetag' => config('app.activity_detail')));
        }else{
            $data['url'] = createUrl('activity/detail', array('id' => $activity->id, 'pagetag' => config('app.activity_detail'),
                                                              'share_mark' => makeShareMark($activity->id, 'activity', $uid)
            ));
        }
        $data['subject'] = $activity->subject;
//        $data['activity_des'] = cut_str(strip_tags($activity->description), 30);
        $data['activity_des'] = cut_str(trim(preg_replace('#<\w+(\s+.*?)?>|</\w+>|&nbsp;#', '', $activity->description)),30);
        $data['begin_time'] = self::formatTime($activity->begin_time, $activity->end_time);
        $data['views'] = $activity->sham_view;  //假浏览量
        $data['is_recommend'] = $activity->is_recommend;
        $data['is_hot'] = $activity->is_hot;
        $data['price'] = self::getMinTicket($activity, 'min', $type);
        $data['zone'] = self::getZone($activity, $type);
        $data['is_over'] = $activity->end_time > time() ? 0 : 1;
        $data['begin_time_format'] = date('m月d日 H:i',$activity->begin_time);
        $data['ticket_num'] = self::getTicketNum($activity->id);
        $data['is_vip']           = $activity->vip_id == 0 ? 0:1;
        $data['is_new']           = self::isNewActivity($activity->created_at);
        $data['host_cities']      = self::getAllCitiesOfActivity($activity->id);
        $data['is_shareable']     = self::canShare($activity->id);
//            $activity->city             = str_replace('市','',$activity->city);
        $data['isOver']           = $activity->end_time < time() ? 1 : ($activity->end_time > time() && $activity->begin_time <= time() ? -1 : 0);
        $data['live_support']     = \DB::table('live')->where('activity_id',$activity->id)->count();
        $data['sign']             = $data['isOver'] != 1 ? 1 : 0;

        $data['share_num']  = $activity->share_num;
        $data['distribution_id']  = $activity->distribution_id;
        $data['distribution_deadline']  = $activity->distribution_deadline;
        $data['rebate']  = Distribution::Integer($activity->rebate);
        return $data;
    }

    /**
     * @param     $activity
     * @param int $maker_id
     * @param int $status
     * @return string
     * 网站获取活动地址
     */
    static function getdetailurl($activity, $maker_id = 0, $status = 0, $type)
    {
        if (!isset($activity->id)) {
            return '';
        }
        if ($user = \App\Models\CityPartner\Entity::getCurrentuser()) {
            $make = \App\Models\Maker\Entity::getRow(
                array(
                    'partner_uid' => $user->uid
                )
            );
            $maker_id = isset($make->id) ? $make->id : 0;
            $activityMaker = Maker::where('activity_id', $activity->id)
                ->where('maker_id', $maker_id)->first();
            $status = isset($activityMaker->status) ? $activityMaker->status : 0;
        }
        $url = '';
        if (!self::checkJoint($activity->id, $maker_id, $status)) {
            //1未合办
            $url = createUrl('citypartner/maker/activitydetail', array('id' => $activity->id, 'flag' => 1, 'type' => $type), "web");
        } else {
            if (!self::checkBegin($activity->begin_time)) {
                //2未举办
                $url = createUrl('citypartner/maker/activitydetail', array('id' => $activity->id, 'flag' => 2, 'type' => $type), "web");
            } elseif ($activity->end_time < time()) {
                //3已结束
                $url = createUrl('citypartner/maker/activitydetail', array('id' => $activity->id, 'flag' => 3, 'type' => $type), "web");
            } else {
                //其他情况
                $url = createUrl('citypartner/maker/activitydetail', array('id' => $activity->id, 'type' => $type), "web");
            }
        }

        return $url;
    }

    /**
     *获取全部活动的条件
     */
    static function getAllactivitywhere($uid, $maker_id)
    {
        $activity_id = Maker::where('maker_id', $maker_id)->lists('activity_id')->toArray();
        //我创建
        if (count($activity_id)) {
            $activity_id_str = implode(',', $activity_id);
            $where = "partner_uid=$uid or id in ($activity_id_str)";
        } else {
            $where = "partner_uid=$uid";
        }

        return $where;
    }

    /***
     * @param $begin_time
     * @return int
     * 判断活动是否开始
     * 1 已开始  0未开始
     */
    static function checkBegin($begin_time)
    {
        return $begin_time > time() ? 0 : 1;
    }

    /**格式化时间
     *
     * @param $begin_time
     * @param $end_time
    04/15 12：00--14：00
     * 04/15 12：00--04/16 14：00
     */
    static function formatTime($begin_time, $end_time)
    {
        if (date('Ymd', $begin_time) == date('Ymd', $end_time)) {
            return date('m月d日 H:i', $begin_time) . "-" . date('H:i', $end_time);
        }

        return date('m月d日 H:i', $begin_time) . "-" . date('m月d日 H:i', $end_time);
    }

    static function getStatus($activity, $maker_id)
    {
        if (!isset($activity->id)) {
            return;
        }
        $time_limit = strtotime($activity->time_limit);
        $joint = Maker::where('activity_id', $activity->id)->where('maker_id', $maker_id)->first();
        if (empty($joint)) {//未邀请
            return '未邀请';
        }
        /*
        未举行+等待处理的=我要合办
        未举行+我合办=通知会员
        未举行+过期未处理=已过期
        已举行+我合办=已举办    
        已举行+过期未处理=已结束  
         */
        $stop = self::checkBegin($activity->begin_time);
        if ($time_limit > time()) {//还在允许合办期间
            if ($joint->status == 1) {//已经合办
                return $stop ? '已举办' : '通知会员';
            }

            return $stop ? '已结束' : '我要合办';
        } else {//已经截至合办
            if ($joint->status == 1) {//已经合办
                return $stop ? '已举办' : '通知会员';
            }

            return $stop ? '已结束' : '已过期';
        }
//        if (self::checkBegin(min($activity->begin_time,strtotime($activity->time_limit)))) {
//            //已举办
//            if (self::checkJoint($activity->id, $maker_id, 1)) {
//                //合办
//                return '已举办';
//            }
//            return '已结束';
//        } else {
//            //未举办
//            if (self::checkJoint($activity->id, $maker_id, 1)) {
//                //合办
//                return '通知会员';
//            } elseif (self::checkJoint($activity->id, $maker_id, 0)) {
//                return '我要合办';
//            }
//            return '已过期';
//        }
    }

    /**
     *获取活动报名的个数
     */
    static function getApplyusersCount($where)
    {
        $count = UserTicket::where($where)
            ->where('user_ticket.status', 1)->count();

        return $count;
    }

    /**
     * @param $where
     * 获取活动报名的人
     */
    static function getApplyusers($where, $page = 1, $pageSize = 10)
    {
        $applys = UserTicket::where($where)
            ->leftJoin('user as u', 'u.uid', '=', 'user_ticket.uid')
            ->leftJoin('activity_sign as sa', 'sa.uid', '=', 'u.uid')
            ->select('u.nickname', 'u.username', 'user_ticket.created_at as apply_time', 'user_ticket.is_check', 'sa.created_at as sign_time')
            ->where('user_ticket.status', 1)->skip(($page - 1) * $pageSize)->take($pageSize)->groupBy('user_ticket.id')->get()->toArray();

        return $applys;
    }

    /**
     * @param $id
     * @param $maker_id
     * 判断是否合办某个活动
     * 1 合办  0等待处理
     */
    static function checkJoint($id, $maker_id, $status)
    {
        return Maker::where('activity_id', $id)->where('maker_id', $maker_id)->where('status', $status)->count();
    }

    /**
     * 某个ovo中心的活动
     *
     * @param int $type 0:获取所有，1：获取未来的活动， 2：获取过往的活动
     *
     * @param int $maker_id
     *
     * @return array|bool
     */
    static function makerActivity($maker_id, $type = 0, $keyword = '', $cache = 1)
    {
        $desc = 'desc';
        if ($type == 1) {
            $desc = 'asc';
        }

        $data = Cache::has('makerActivity' . $maker_id . 'type' . $type . 'kw' . $keyword) ? Cache::get('makerActivity' . $maker_id . 'type' . $type . 'kw' . $keyword) : false;
        $data = false;
        if ($data === false || $cache) {
            $lists = DB::table('activity')
                ->leftJoin('activity_maker', 'activity.id', '=', 'activity_maker.activity_id')
                ->leftJoin('maker', 'maker.id', '=', 'activity_maker.maker_id')
                ->leftJoin('zone', 'maker.zone_id', '=', 'zone.id')
                ->groupBy('activity.id')->where('activity.status', 1)
                ->where('activity_maker.maker_id', $maker_id)
                ->where('activity_maker.status', 1)
                ->orderBy('activity.begin_time', $desc)
                ->select(
                    DB::raw(
                        "lab_activity.id,lab_activity.subject, lab_activity.view,  lab_activity.list_img,lab_activity.description,
                lab_activity.begin_time,lab_activity.end_time,lab_activity.is_recommend,lab_activity.is_hot,lab_activity.vip_id as is_vip,
                group_concat(lab_zone.name separator'@') as city,
                group_concat(lab_activity_maker.maker_id separator'@') as maker_ids,
                group_concat(lab_activity_maker.type separator'@') as type "
                    )
                );

            //未来的活动
            if (isset($type) && $type == 1) {
                $lists->where('activity.end_time', '>', time());
            }

            //过往的活动
            if (isset($type) && $type == 2) {
                $lists->where('activity.end_time', '<', time());
            }
            if (isset($keyword) && $keyword != '') {
                $lists->where('activity.subject', 'like', '%' . $keyword . '%');
            }

            //活动排序
            $lists->orderBy('begin_time', 'desc')->orderBy('is_recommend', 'desc')->orderBy('is_vip', 'desc');
            $data = $lists->get();

            foreach ($data as $k => $v) {
                $tickets = DB::table('activity')->leftJoin('activity_ticket', 'activity_ticket.activity_id', '=', 'activity.id')
                    ->select(
                        DB::raw(
                            "group_concat(IFNULL(lab_activity_ticket.price,'') separator'@') as price
                    ,group_concat(IFNULL(lab_activity_ticket.type,'') separator'@') as ticket_type
                    "
                        )
                    )->where('activity.id', $v->id)->first();
                $v->is_vip = $v->is_vip == 0 ? 0 : 1;
                $v->price = $tickets->price;
                $v->begin_time = date('m/d', $v->begin_time);
                $v->ticket_type = $tickets->ticket_type;
                $v->min_price = min(explode('@', $tickets->price));
                $v->list_img = getImage($v->list_img, 'activity', '');
                $maker_ids = explode('@', $v->maker_ids);
                $citys = array();
                foreach (explode('@', $v->city) as $bond => $value) {
                    if ('市' == mb_substr($value, -1, 1)) {
                        $citys[] = mb_substr($value, 0, -1);
                    }
                    if ('区' == mb_substr($value, -1, 1)) {
                        $zone = DB::table('maker')->leftJoin('zone', 'maker.zone_id', '=', 'zone.id')
                            ->where('maker.id', $maker_ids[$bond])->first();
                        if ($zone->level == 2 && in_array($zone->upid, [1, 2, 9, 22])) {
                            $uzone = DB::table('zone')->where('id', $zone->upid)->first();
                            $citys[] = mb_substr($uzone->name, 0, -1);
                        }
                    }
                }
                $v->city = implode('@', $citys);
                $v->subject = $v->subject.'-'.$v->city.'站';
            }
        }
        Cache::put('makerActivity' . $maker_id . 'type' . $type . 'kw' . $keyword, $data, 1440);

        return $data;
    }

    /**
     * 某个地区的活动
     *
     * @param int $type 0:获取所有，1：获取未来的活动， 2：获取过往的活动
     *
     * @param int $activity_id
     *
     * @return array|bool
     */
    static function zoneActivity($zone_id, $type = 0, $keyword = '', $cache = 1)
    {
        $data = Cache::has('zoneActivity' . $zone_id . 'type' . $type . 'kw' . $keyword) ? Cache::get('zoneActivity' . $zone_id . 'type' . $type . 'kw' . $keyword) : false;
        if ($data === false || $cache) {
            $lists = DB::table('activity')
                ->leftJoin('activity_maker', 'activity.id', '=', 'activity_maker.activity_id')
                ->leftJoin('maker', 'maker.id', '=', 'activity_maker.maker_id')
                ->leftJoin('zone', 'maker.zone_id', '=', 'zone.id')
                ->where('zone.id', '=', $zone_id)
                ->groupBy('activity.id')
                ->select(
                    DB::raw(
                        "lab_activity.id,lab_activity.subject, lab_activity.view,lab_activity.description,
                lab_activity.list_img, lab_activity.begin_time,lab_activity.is_recommend,
                group_concat(lab_zone.name separator'@') as city,group_concat(lab_activity_maker.type separator'@') as type"
                    )
                );

            //未来的活动
            if (isset($type) && $type == 1) {
                $lists->where('activity.end_time', '>=', time());
            }

            //过往的活动
            if (isset($type) && $type == 2) {
                $lists->where('activity.end_time', '<', time());
            }

            //关键词搜索
            if (isset($keyword) && $keyword != '') {
                $lists->where('activity.subject', 'like', '%' . $keyword . '%');
            }

            $data = $lists->get();
            foreach ($data as $k => $v) {
                $tickets = DB::table('activity')->leftJoin('activity_ticket', 'activity_ticket.activity_id', '=', 'activity.id')->select(
                    DB::raw(
                        "
                    group_concat(IFNULL(lab_activity_ticket.price,'') separator'@') as price
                    ,group_concat(IFNULL(lab_activity_ticket.type,'') separator'@') as ticket_type
                    "
                    )
                )->where('activity.id', $v->id)->first();
                $v->price = $tickets->price;
                $v->min_price = min(explode('@', $tickets->price));

                $v->begin_time = date('m/d', $v->begin_time);
                $v->ticket_type = $tickets->ticket_type;
            }
            Cache::put('zoneActivity' . $zone_id . 'type' . $type . 'kw' . $keyword, $data, 1440);
        }

        return $data;
    }

    /**
     * 排除了某些条件的活动列表
     *
     * @param int $type 0:获取所有，1：获取未来的活动， 2：获取过往的活动
     *
     * @param int $activity_id
     *
     * @return array|bool
     */
    static function otherActivity($ids, $maker_id = 0, $zone_id = 0, $type = 0, $keyword = '', $cache = 1)
    {
        $data = Cache::has('otherActivity' . 'zone_id' . $zone_id . 'maker_id' . $maker_id . 'type' . $type . 'kw' . $keyword) ? Cache::get(
            'otherActivity' . 'zone_id' . $zone_id . 'maker_id' . $maker_id . 'type' . $type . 'kw' . $keyword
        ) : false;
        if ($data === false || $cache) {
            $other_lists = DB::table('activity')
                ->leftJoin('activity_maker', 'activity.id', '=', 'activity_maker.activity_id')
                ->leftJoin('maker', 'maker.id', '=', 'activity_maker.maker_id')
                ->leftJoin('zone', 'maker.zone_id', '=', 'zone.id')
                ->whereNotIn('activity.id', $ids)
                ->groupBy('activity.id')
                ->select(
                    DB::raw(
                        "lab_activity.id,lab_activity.subject, lab_activity.view,  lab_activity.list_img,lab_activity.description,
                lab_activity.begin_time,lab_activity.is_recommend,
                group_concat(lab_zone.name separator'@') as city,group_concat(lab_activity_maker.type separator'@') as type "
                    )
                );

            //未来的活动
            if (isset($type) && $type == 1) {
                $other_lists->where('activity.begin_time', '>', time());
            }

            //过往的活动
            if (isset($type) && $type == 2) {
                $other_lists->where('activity.begin_time', '<', time());
            }
            if (isset($keyword) && $keyword != '') {
                $other_lists->where('activity.subject', 'like', '%' . $keyword . '%');
            }
            $data = $other_lists->get();
            foreach ($data as $k => $v) {
                $tickets = DB::table('activity')->leftJoin('activity_ticket', 'activity_ticket.activity_id', '=', 'activity.id')->select(
                    DB::raw(
                        "
                    group_concat(IFNULL(lab_activity_ticket.price,'') separator'@') as price
                    ,group_concat(IFNULL(lab_activity_ticket.type,'') separator'@') as ticket_type
                    "
                    )
                )->where('activity.id', $v->id)->first();
                $v->price = $tickets->price;
                $v->begin_time = date('m/d', $v->begin_time);
                $v->ticket_type = $tickets->ticket_type;
                $v->min_price = min(explode('@', $tickets->price));
            }
            Cache::put('otherActivity' . 'zone_id' . $zone_id . 'maker_id' . $maker_id . 'type' . $type . 'kw' . $keyword, $data, 1440);
        }

        return $data;
    }

    /**
     * 获取某个活动的详情
     *
     * @param int $type 0:获取所有，1：获取未来的活动， 2：获取过往的活动
     *
     * @param int $activity_id
     *
     * @return array|bool
     */
    static function detail($id, $uid = 0,$maker_id=0, $cache = 1)
    {
        $data = Cache::has('activity' . 'detail' . $id) ? Cache::get('activity' . 'detail' . $id) : false;
        $data = false;
        if ($data === false || $cache) {
            $data = DB::table('activity')
                ->leftJoin('activity_maker', 'activity_maker.activity_id', '=', 'activity.id')
                ->leftJoin('maker', 'activity_maker.maker_id', '=', 'maker.id')
                ->leftJoin('zone', 'maker.zone_id', '=', 'zone.id')
                ->leftJoin('activity_publisher', 'activity_publisher.id', '=', 'activity.publisher_uid')
                ->leftJoin('group_chat', 'group_chat.activity_id', '=', 'activity.id')
                ->where('activity.id', $id)
                ->where('activity_maker.status',1)
                ->groupBy('activity.id')
                ->select(
                    DB::raw(
                    "lab_activity.id,lab_activity.keywords,lab_activity.subject, lab_activity.view,lab_activity.partner_uid,
                    lab_activity.wx_friend_summary,lab_activity.wx_title,lab_activity.wx_summary,lab_activity.wb_summary,
                    lab_activity.begin_time, lab_activity.end_time, lab_activity.description, lab_activity.share_image,
                    lab_activity.list_img,lab_activity.list_img, lab_activity.likes,lab_activity.content,lab_activity.vip_id,lab_activity.share_image,
                    lab_activity_publisher.nickname,lab_activity_publisher.avatar,lab_activity_publisher.id as pub_id,lab_group_chat.groupid as chat,
                    group_concat(IFNULL(lab_zone.name,'') separator'@') as city,
                    group_concat(IFNULL(lab_maker.id,'') separator'@') as maker_ids,
                    group_concat(IFNULL(lab_maker.description,'') separator'@') as descriptions,
                    group_concat(IFNULL(lab_maker.address,'') separator'@') as address,
                    group_concat(IFNULL(lab_maker.subject,'') separator'@') as name,
                    group_concat(IFNULL(lab_activity_maker.type,'') separator'@') as type "
                    )
                )
;
            $data = $data->first();
            if($data){
                $data->share_image = getImage($data->share_image?:'images/share_image.png', '', '');
            }
            //获取ovo信息
            if($maker_id != 0){
                $maker = DB::table('maker')
                    ->join('zone','maker.zone_id','=','zone.id')
                    ->where('maker.id',$maker_id)
                    ->select('maker.subject','maker.address','maker.description','zone.name')
                    ->first();
                $data->current_maker_address = $maker->address;
                $data->current_maker_subject = $maker->subject;
                $data->current_maker_city = str_replace('市','',$maker->name);
                $data->current_maker_description = $maker->description;
            }
            $data->city = implode('@',array_unique(explode('@',$data->city)));
            $data->city = str_replace('市','',$data->city);
            $data->has_received_tickets = self::hasReceivedTickets($id,$uid);

            $data->makerOfUser = DB::table('user')
                ->join('maker','user.maker_id','=','maker.id')
                ->where('user.uid',$uid)
                ->select('maker.subject')
                ->first()->subject;
            //专版活动
            if($data->vip_id){
                $vipInfo = DB::table('vip')
                    ->where('id',$data->vip_id)
                    ->select('id','name')->first();
                $data->vip_name = $vipInfo->name;
                //用户是否购买该活动所属的专版
                $data->belong_same_vip = DB::table('user_vip')
                    ->where('end_time','>',time())
                    ->where('uid',$uid)
                    ->where('vip_id',$data->vip_id)
                    ->count() ? 1:0;
            }else{
                unset($data->vip_id);
                $data->belong_same_vip = 0;
            }
            //用户已经购买的vip id

            $tickets = DB::table('activity')->leftJoin('activity_ticket', 'activity_ticket.activity_id', '=', 'activity.id')->select(
                DB::raw(
                    "
                    group_concat(IFNULL(lab_activity_ticket.price,'') separator'@') as price
                    "
                )
            )->where('activity.id', $id)->first();
//            $data->end_time= date('m/d H:i',$data->end_time);
//            $data->begin_time= date('m/d H:i',$data->begin_time);
            $data->price = $tickets->price;
            $data->list_img = getImage($data->list_img, "activity",'large',0);
            $data->avatar = getImage($data->avatar, "avatar",'', 0);
            $data->detail_img = getImage($data->list_img, "activity",'', 0);
            $user = DB::table('user')->where('uid', $uid)->first();
            if (is_object($user)) {
                $data->user_score = $user->score;
            }
            //用户是否时vip用户
            $data->is_vip_user = self::isVipUser($uid);
            if ($data->pub_id == 0) {
                $data->is_official = 0;
                $partner_user = DB::table('city_partner')
                    ->leftJoin('maker', 'city_partner.uid', '=', 'maker.partner_uid')->where('city_partner.uid', $data->partner_uid)
                    ->select('city_partner.realname', 'city_partner.avatar', 'maker.uid')->first();
                if (is_object($partner_user)) {
                    $data->nickname = $partner_user->realname;
                    $avatar = explode('/', $partner_user->avatar);
                    if (isset($avatar[3])) {
                        $avatar[3] = substr($avatar[3], 4);
                        $avatar = implode('/', $avatar);
                    } else {
                        $avatar = $partner_user->avatar;
                    }

                    $data->avatar = getImage($avatar, "avatar", false);
                    $data->c_uid = $partner_user->uid;
                }
                $data->c_uid = $data->partner_uid;
            } else {
                $data->is_official = 1;
            }

            $brand_ids = DB::table('activity_brand')->where('activity_id', $id)->lists('brand_id');
            if(count($brand_ids)){
                $data->is_brand_activity=1;
                $brands = Brands::singles()->where(['status' => 'enable'])->whereIn('id', $brand_ids)
                    ->addSelect('id','logo', 'name', 'investment_min', 'investment_max','keywords','summary')->get();
                $brands = Brands::process($brands)->toArray();
                $data->brands = $brands;
            }else{
                $data->is_brand_activity=0;
            }
            $data->keywords ?$data->keywords = explode(' ', $data->keywords):$data->keywords=[];

            Cache::put('activity' . 'detail' . $id, $data, 1440);
        }

        return $data;
    }

    /**
     * 获取某场活动相关的推荐活动
     */
    static function recommend($id, $cache = 1)
    {
        $data = Cache::has('activity' . $id . 'recommend') ? Cache::get('activity' . $id . 'recommend') : false;
        if ($data === false || $cache) {
            $activity = DB::table('activity')->where('id', $id)->first();
            //同一个ovo中心的
            $list_ovo_query = DB::table('activity')
                ->leftJoin('activity_maker', 'activity_maker.activity_id', '=', 'activity.id')
                ->leftJoin('activity_ticket', 'activity_ticket.activity_id', '=', 'activity.id')
                ->whereIn(
                    'activity_maker.maker_id',
                    function ($query) use ($activity) {
                        $query->from('activity_maker')->where('activity_id', $activity->id)->where('status', 1)->lists('maker_id');
                    }
                )
                ->where('activity_maker.status', 1)
                ->where('activity_ticket.surplus', '>', 0)
                ->where('activity_ticket.type', 2)
                ->where('activity.end_time', '>', time())
                ->where('activity.id', '<>', $id)
                ->select('activity.id', 'activity.subject', 'activity.list_img', 'activity_maker.maker_id as now_maker_id', 'activity_ticket.num')
                ->groupBy('activity.id')
                ->limit(3);
            $list_ovo = $list_ovo_query->get();
            $list_admin = array();
            if (count($list_ovo) < 3 && $activity->publisher_uid > 0) {

                //同一个发布者
                $list_admin = DB::table('activity')
                    ->leftJoin('activity_maker', 'activity_maker.activity_id', '=', 'activity.id')
                    ->leftJoin('activity_ticket', 'activity_ticket.activity_id', '=', 'activity.id')
                    ->where('activity.end_time', '>', time())
                    ->Where('activity.publisher_uid', $activity->publisher_uid)
                    ->where('activity.id', '<>', $id)
                    ->where('activity_ticket.surplus', '>', 0)
                    ->where('activity_ticket.type', 2)
                    ->where('activity.end_time', '>', time())
                    ->where('activity_maker.status', 1)
                    ->groupBy('activity.id');
                if (count($list_ovo)) {
                    $list_admin->whereNotIn('activity.id', $list_ovo_query->lists('id'));
                }
                $list_admin = $list_admin
                    ->select('activity.id', 'activity.subject', 'activity.list_img', 'activity_maker.maker_id as now_maker_id')
                    ->limit(3 - count($list_ovo))
                    ->get();
            }
            $data = array_merge($list_ovo, $list_admin);
            foreach ($data as $k => $v) {
                $v->list_img = getImage($v->list_img, 'activity', '');
                $v->subject = $v->subject . '-' . self::getZoneName($v->now_maker_id) . '站';
            }
            Cache::put('activity' . $id . 'recommend', $data, 1440);
        }

        return $data;
    }

    /**
     *判断某用户和某活动的报名关系
     *
     * @param int $type 0:获取所有，1：获取未来的活动， 2：获取过往的活动
     *
     * @param int $activity_id
     *
     * @return array|bool
     */
    static function follow($id, $uid, $cache = 1)
    {
        $data = Cache::has('activity' . $id . 'uid' . $uid) ? Cache::get('activity' . $id . 'uid' . $uid) : false;
        if ($data === false || $cache) {
            $list = DB::table('activity_sign')
                ->where('activity_id', $id)
                ->where('uid', $uid)
                ->whereIn('status', [0, 1])
                ->first();

            if (!is_object($list)) {
                $data = -1;
            } else {
                $data = 0;
            }
            Cache::put('activity' . $id . 'uid' . $uid . $id, $data, 1440);
        }

        return $data;
    }

    /**
     *判断某用户和某活动的报名关系
     *
     * @param int $type 0:获取所有，1：获取未来的活动， 2：获取过往的活动
     *
     * @param int $activity_id
     *
     * @return array|bool
     */
    static function isFavorite($id, $uid, $cache = 1)
    {
        $data = Cache::has('isFavorite' . $id . 'uid' . $uid) ? Cache::get('isFavorite' . $id . 'uid' . $uid) : false;
        if ($data === false || $cache) {
            $list = DB::table('user_favorite')
                ->where('post_id', $id)
                ->where('uid', $uid)
                ->where('model', 'activity')
                ->first();
            $data = 0;
            if (!is_object($list)) {
                $data = 0;
            } elseif (is_object($list) && $list->status == 1) {
                $data = 1;
                Cache::put('isFavorite' . $id . 'uid' . $uid . $id, $data, 1440);
            }

            return $data;
        }
    }

    /**
     *获取某场活动的门票
     *
     * @param int $type 0:获取所有，1：获取未来的活动， 2：获取过往的活动
     *
     * @param int $activity_id
     *
     * @return array|bool
     */
    static function tickets($id, $is_share = 0, $cache = 1, $uid = 0)
    {
        $data = Cache::has('activity' . $id . 'tickets') ? Cache::get('activity' . $id . 'tickets') : false;
        $data = false;
        if ($data === false || $cache) {
            $data = DB::table('activity_ticket')
                ->leftJoin('activity', 'activity_ticket.activity_id', '=', 'activity.id')
                ->whereIn('activity_ticket.type',[1,2])
                ->where('activity_ticket.activity_id', $id)
                ->where('activity_ticket.status', 1)
                ->orderBy('activity_ticket.type', 'desc')
                ->orderBy('activity_ticket.price', 'asc')
                ->where(
                    function ($query) use ($is_share) {
                        if ($is_share == 1) {
                            $query->where('activity_ticket.is_share', 'yes');
                        }
                    }
                )
                ->select('activity_ticket.*', 'activity.subject', 'activity.end_time', 'activity.begin_time', 'activity.vip_id as vip_id')
                ->get();


            $maker = \DB::table('activity')
                ->leftJoin('activity_maker', 'activity_maker.activity_id', '=', 'activity.id')
                ->leftJoin('maker', 'activity_maker.maker_id', '=', 'maker.id')
                ->where('activity_maker.status', 1)
                ->where('activity.id', $id)
                ->where('activity_maker.type', 'organizer')
                ->select('maker.id', 'maker.subject', 'maker.address', 'activity_maker.status', 'activity_maker.type')
                ->first();


            foreach ($data as $k => $v) {
                $count = DB::table('user_ticket')->where('ticket_id', $v->id)->whereIn('status', [0, 1])->count();
                $left = $v->num - $count;
                if ($left < 0) {
                    $left = 0;
                }
                $v->left = $left;

                if ($v->end_time < time()) {
                    $v->is_over = 1;
                } else {
                    $v->is_over = 0;
                }

                if ($v->vip_id > 0 && $uid > 0) {
                    $v->is_vip = Vip::valid($v->vip_id, $uid);
                } else {
                    $v->is_vip = 0;
                }
                $v->begin_time = date("Y-m-d H:i", $v->begin_time);
                $v->end_time = date("Y-m-d H:i", $v->end_time);

                $v->is_share=='yes'?$v->is_share=1:$v->is_share=0;
                $v->maker_id=$maker->id;
                $v->address =$maker->address;
                $v->maker_subject=$maker->subject;
            }


            Cache::put('activity' . $id . 'tickets', $data, 1440);
        }

        return $data;
    }

    /**
     * 获取主办方活动列表
     *
     * @param     $param
     * @param int $cache
     * @return bool
     */
    static function organizerList($param, $cache = 1)
    {
        $id = $param['organizer_id'];
        $data = DB::table('activity as a')
            ->join('activity_ticket as at', 'a.id', '=', 'at.activity_id')
            ->leftjoin('activity_maker as am', 'a.id', '=', 'am.activity_id')
            //将来的活动
            ->where(
                function ($query) use ($param) {
                    if (!empty($param['future_time']) && empty($param['past_time'])) {
                        $query->where('a.begin_time', '>', time());
                    }
                }
            )
            //过去的活动
            ->where(
                function ($query) use ($param) {
                    if (!empty($param['past_time']) && empty($param['future_time'])) {
                        $query->where('a.end_time', '<', time());
                    }
                }
            )
            ->where('publisher_uid', $id)
            ->groupBy('a.id')
            ->orderBy('a.created_at', 'desc')
            //->select('a.id','a.subject','a.begin_time','a.end_time','a.view','a.list_img','min(at.price)','am.maker_id')
            ->select(DB::raw(' min(price) as price,max(price) as max_price,lab_a.id,lab_a.vip_id,lab_a.is_recommend,lab_a.subject,lab_a.begin_time,lab_a.end_time,lab_a.view,lab_a.list_img,lab_am.maker_id'))
            ->get();

        return $data;
    }

    /**活动举办地
     *
     * @param $activity
     * @param $activity
     * @return array
     * 0代表是用orm查询来的activity,1代表用DB查询来的activity
     */
    static function getZone($activity, $type = 0)
    {
        if (!isset($activity->id)) {
            return array();
        }

            $data = array();
        if ($type == 0) {
            $makers=[];
            if (count($activity->makers)) {
                foreach ($activity->makers as $k => $v) {
                    if (isset($v->zone->name)) {
                        $data[] = Zone::getZone($v->zone->id);
                        $makers[$v->zone->id]=$v->id;
                    }
                }
            }
        } else {
            $makers = DB::table('activity_maker')
                ->leftJoin('maker', 'maker.id', '=', 'activity_maker.maker_id')
                ->leftJoin('zone', 'zone.id', '=', 'maker.zone_id')
                ->where('activity_maker.status', 1)->where('activity_maker.activity_id', $activity->id)
                ->lists('zone.id', 'activity_maker.maker_id');
            foreach (array_values($makers) as $k => $v) {
                $data[] = Zone::getZone($v);
            }
        }
//        print_r($makers);exit;

        $citys = [];
        foreach ($data as $bond => $value) {
            if ('市' == mb_substr($value, -1, 1)) {
                $citys[] = mb_substr($value, 0, -1);
            }
            if ('区' == mb_substr($value, -1, 1)) {
                $zone = DB::table('maker')->leftJoin('zone', 'maker.zone_id', '=', 'zone.id')
                    ->where('maker.id', (array_keys($makers)[$bond]))->first();
//                    ->where('maker.id', $activity->now_maker_id)->first();
                if (!$zone) {
                    continue;
                }
                if ($zone->level == 2 && in_array($zone->upid, [1, 2, 9, 22])) {
                    $uzone = DB::table('zone')->where('id', $zone->upid)->first();
                    $citys[] = mb_substr($uzone->name, 0, -1);
                } else {
                    $citys[] = $value;
                }
            }
        }

        return array_values(array_unique($citys));
    }

    /**
     * @param $activity
     * 活动最低票价
     * range  10~100
     * min    10
     * type 0表示 由orm查询的结果，1表示由DB查询的结果
     */
    static function getMinTicket($activity, $format = 'range', $type = 0)
    {
        if (!isset($activity->id)) {
            return array();
        }
        $data = array();
        if ($type == 0) {
            $tickets = $activity->activity_tickets;
        } else {
            $tickets = DB::table('activity_ticket')->where('status', 1)->where('activity_id', $activity->id)->get();
        }
        $min_price = -1;
        $max_price = -1;
        if (count($tickets)) {
            foreach ($tickets as $k => $v) {
                if ($min_price < 0 || $v->price < $min_price) {
                    $min_price = $v->price;
                }
                if ($v->price > $max_price) {
                    $max_price = $v->price;
                }
            }
            if(count($tickets)>1){
                $min_price = $min_price . '起';
            }
        }
        if ($max_price < 1) {//只有免费
            return -1;
        }
        if ($format == 'range') {//范围价格
            return $min_price == $max_price ? $min_price : $min_price . '~' . $max_price;
        }

        return $min_price;
    }

    /**
     * 即将开始的活动信息
     *
     * @param     $uid
     * @param int $cache
     * @return bool
     */
    static function activityRemind($uid, $page, $pageSize, $cache = 1)
    {
        $now = time();
        $data = Cache::has('activity' . $uid . 'remindlists') ? Cache::get('activity' . $uid . 'remindlists') : false;
        $data = false;
        if ($data === false || $cache) {
            $data = DB::table('user_ticket as ua')
                ->join('activity_ticket as at', 'ua.ticket_id', '=', 'at.id')
                ->join('activity as a', 'ua.activity_id', '=', 'a.id')
                ->join('maker as m', 'ua.maker_id', '=', 'm.id')
                ->where('ua.uid', $uid)
                ->where('begin_time', '>=', $now + 2 * 24 * 60 * 60)
                ->where('begin_time', '<', $now + 3 * 24 * 60 * 60)
                ->orWhere(
                    function ($query) use ($now, $uid) {
                        $query->where('ua.uid', $uid)
                            ->where('a.begin_time', '>=', $now + 1 * 24 * 60 * 60)
                            ->where('a.begin_time', '<', $now + 2 * 24 * 60 * 60);
                    }
                )
                ->orWhere(
                    function ($query) use ($now, $uid) {
                        $query->where('ua.uid', $uid)
                            ->where('a.begin_time', '>=', $now)
                            ->where('a.begin_time', '<', $now + 24 * 60 * 60);
                    }
                )
                ->where('ua.status', 1)
                ->where('at.status', 1)
                ->skip($page * $pageSize)
                ->take($pageSize)
                //->groupBy('ua.activity_id')
                ->orderBy('a.begin_time', 'asc')
                ->select('a.id', 'ua.activity_id', 'a.subject', 'a.begin_time', 'm.subject as ovo', 'm.zone_id', 'm.address', 'at.price', 'a.list_img', 'at.num', 'at.type')
                ->distinct()
                ->get();
            Cache::put('activity' . $uid . 'remindlists', $data, 1440);
        }

        return $data;
    }

    /**
     *获取所有活动
     */
    static function allLists($maker_id = 0, $m_ids = [], $type = 0, $keyword = '', $exclusion = [], $interested = 0, $withStation=1, $cache = 1, $vip_id = 0)
    {
        $last_data = Cache::has('allActivity' . 'maker_id' . $maker_id . 'm_ids' . implode('@', $m_ids) . 'type' . $type . 'keyword' . $keyword) ? Cache::get(
            'allActivity' . 'maker_id' . $maker_id . 'm_ids' . implode('@', $m_ids) . 'type' . $type . 'keyword' . $keyword
        ) : false;
        $last_data = false;
        if ($last_data === false || $cache) {
            $lists = DB::table('activity')
                ->leftJoin('activity_maker', 'activity.id', '=', 'activity_maker.activity_id')
                ->leftJoin('maker', 'maker.id', '=', 'activity_maker.maker_id')
                ->leftJoin('zone', 'maker.zone_id', '=', 'zone.id')
                ->where('activity_maker.status', 1)
                ->whereNotIn('activity.id', $exclusion)
                ->groupBy('activity.id')->where('activity.status', 1)->where('activity.end_time', '>', time())
                ->select(
                    DB::raw(
                        "lab_activity.id,lab_activity.subject, lab_activity.view,  lab_activity.list_img,lab_activity.description,
                lab_activity.begin_time,lab_activity.end_time,lab_activity.is_recommend,lab_activity.is_hot,lab_activity.vip_id as is_vip,
                group_concat(lab_zone.name separator'@') as city,
                group_concat(lab_activity_maker.maker_id separator'@') as maker_ids,
                group_concat(lab_activity_maker.type separator'@') as type "
                    )
                );
            if ($vip_id) {
                $lists->where('activity.vip_id', $vip_id);
            }

            //未来的活动
            if (isset($type) && $type == 1) {
                $lists->where('activity.end_time', '>', time());
            }
            //过往的活动
            if (isset($type) && $type == 2) {
                $lists->where('activity.end_time', '<', time());
            }

            if (isset($keyword) && $keyword != '') {
                $lists->where('activity.subject', 'like', '%' . $keyword . '%');
            }
            //活动排序
            $lists->orderBy('is_recommend', 'desc')->orderBy('is_vip', 'desc');
            $data = $lists->get();
            $return_data = $maker_data = $position_data = $other_data = array();
            foreach ($data as $k => $v) {
                $tickets = DB::table('activity')->leftJoin('activity_ticket', 'activity_ticket.activity_id', '=', 'activity.id')
                    ->select(
                        DB::raw(
                            "group_concat(IFNULL(lab_activity_ticket.price,'') separator'@') as price
                    ,group_concat(IFNULL(lab_activity_ticket.type,'') separator'@') as ticket_type
                    "
                        )
                    )->where('activity.id', $v->id)->first();
                $v->is_vip = $v->is_vip == 0 ? 0 : 1;
                $v->price = $tickets->price;
                $v->begin_time = date('m/d', $v->begin_time);
                $v->ticket_type = $tickets->ticket_type;
                $v->min_price = min(explode('@', $tickets->price));
                $maker_ids = explode('@', $v->maker_ids);
                $citys = array();
                foreach (explode('@', $v->city) as $bond => $value) {
                    if ('市' == mb_substr($value, -1, 1)) {
                        $citys[] = mb_substr($value, 0, -1);
                        continue;
                    }
                    if ('区' == mb_substr($value, -1, 1)) {
                        $zone = DB::table('maker')->leftJoin('zone', 'maker.zone_id', '=', 'zone.id')
                            ->where('maker.id', $maker_ids[$bond])->first();
                        if (isset($zone->level) && $zone->level == 2 && in_array($zone->upid, [1, 2, 9, 22])) {
                            $uzone = DB::table('zone')->where('id', $zone->upid)->first();
                            $citys[] = mb_substr($uzone->name, 0, -1);
                        } else {
                            $citys[] = $value;
                        }
                        continue;
                    }
                    $citys[] = $value;
                }
                $v->city = implode('@', $citys);
                foreach (explode('@', $v->city) as $key => $val) {
                    $n_v = clone $v;
                    $n_v->now_city = $val;
                    $n_v->subject = $withStation == 1 ? $v->subject . '-' . $n_v->now_city . '站': $v->subject ;
                    $n_v->now_maker_id = $maker_ids[$key];
                    $return_data[] = $n_v;
                    break;
                }
            }

            foreach ($return_data as $value) {
                if ($maker_id != 0 && $value->now_maker_id == $maker_id) {
                    $maker_data[] = $value;
                } elseif (count($m_ids) != 0 && in_array($value->now_maker_id, $m_ids)) {
                    $position_data[] = $value;
                } else {
                    $other_data[] = $value;
                }
            }
            if ($interested == 1) {
                $last_data = array_merge($maker_data, $position_data, $other_data);
            } elseif (!empty($m_ids)) {
                $last_data = $position_data;
            } else {
                $last_data = $return_data;
            }
            Cache::put('allActivity' . 'maker_id' . $maker_id . 'm_ids' . implode('@', $m_ids) . 'type' . $type . 'keyword' . $keyword, $last_data, 1440);
        }

        return $last_data;
    }

    static function applyLists($uid = 0, $cache = 1)
    {
        $data = Cache::has('applyLists' . 'uid' . $uid) ? Cache::get('applyLists' . 'uid' . $uid) : false;
        if ($data === false || $cache) {
//            $a_ids = DB::table('activity_sign')->where('uid', $uid)->lists('activity_id');
            $a_ids = \App\Models\User\Ticket::where('uid', $uid)->where('status', 1)->get()->toArray();
            $a_ms = array();
            foreach ($a_ids as $k => $v) {
                $a_ms[] = $v['activity_id'] . '@' . $v['maker_id'];
            }

            $a_ms = array_unique($a_ms);
            $a_ms_a = array();
            foreach ($a_ms as $k => $v) {
                $a_ms_a[] = explode('@', $v);
            }

//            print_r($a_ms_a);exit;
            $lists = DB::table('activity')
                ->leftJoin('activity_maker', 'activity.id', '=', 'activity_maker.activity_id')
                ->leftJoin('maker', 'maker.id', '=', 'activity_maker.maker_id')
                ->leftJoin('zone', 'maker.zone_id', '=', 'zone.id')
                ->groupBy('activity.id')->whereIn('activity.id', array_column($a_ms_a, 0))
                ->orderBy('activity.begin_time','desc')
                ->select(
                    DB::raw(
                        "lab_activity.id,lab_activity.subject, lab_activity.view,  lab_activity.list_img,lab_activity.description,
                lab_activity.begin_time,lab_activity.is_recommend,lab_activity.end_time,lab_activity.vip_id,
                group_concat(lab_zone.name separator'@') as city,
                group_concat(lab_activity_maker.maker_id separator'@') as maker_ids,
            group_concat(lab_activity_maker.type separator'@') as type "
                    )
                );

            $data = $lists->get();

            foreach ($data as $k => $v) {
                $tickets = DB::table('activity')->leftJoin('activity_ticket', 'activity_ticket.activity_id', '=', 'activity.id')
                    ->select(
                        DB::raw(
                            "group_concat(IFNULL(lab_activity_ticket.price,'') separator'@') as price
                    ,group_concat(IFNULL(lab_activity_ticket.type,'') separator'@') as ticket_type
                    "
                        )
                    )->where('activity.id', $v->id)->first();
                $v->price = $tickets->price;
                $v->begin_time = date('m/d', $v->begin_time);
                $v->ticket_type = $tickets->ticket_type;
                $v->min_price = min(explode('@', $tickets->price));
                $maker_ids = explode('@', $v->maker_ids);
                $citys = array();
                foreach (explode('@', $v->city) as $bond => $value) {
                    if ('市' == mb_substr($value, -1, 1)) {
                        $citys[] = mb_substr($value, 0, -1);
                        continue;
                    }
                    if ('区' == mb_substr($value, -1, 1)) {
                        $zone = DB::table('maker')->leftJoin('zone', 'maker.zone_id', '=', 'zone.id')
                            ->where('maker.id', $maker_ids[$bond])->first();
                        if ($zone->level == 2 && in_array($zone->upid, [1, 2, 9, 22])) {
                            $uzone = DB::table('zone')->where('id', $zone->upid)->first();
                            $citys[] = mb_substr($uzone->name, 0, -1);
                        }
                        continue;
                    }
                    $citys[] = $value;
                }
                $v->city = implode('@', $citys);
                $v->now_maker_id =
                $m_ids = array();
                foreach (array_column($a_ms_a, 0) as $key => $val) {
                    if ($val == $v->id) {
                        $m_ids[] = array_column($a_ms_a, 1)[$key];
                    }
                }
                rsort($m_ids);
                $m_ids[0] == 0 ? $v->now_maker_id = '' : $v->now_maker_id = $m_ids[0];
                $v->end_time > time() ? $v->is_over = 0 : $v->is_over = 1;
                $v->vip_id >0? $v->is_vip = 1 : $v->is_vip = 0;

                if ($v->now_maker_id != 0) {
                    $v->subject = $v->subject . '-' . self::getZoneName($v->now_maker_id) . '站';
                }
            }

            Cache::put('applyLists' . 'uid' . $uid, $data, 1440);
        }

        return $data;
    }

    static function getZoneName($maker_id)
    {
        $zone = DB::table('maker')->leftJoin('zone', 'maker.zone_id', '=', 'zone.id')->where('maker.id', $maker_id)->first();
        if (!is_object($zone)) {
            return '';
        }
        $name = $zone->name;

        if ('市' == mb_substr($zone->name, -1, 1)) {
            $name = mb_substr($zone->name, 0, -1);
        }
        if ('区' == mb_substr($zone->name, -1, 1)) {
            if (isset($zone->level) && $zone->level == 2 && in_array($zone->upid, [1, 2, 9, 22])) {
                $uzone = DB::table('zone')->where('id', $zone->upid)->first();
                $name = mb_substr($uzone->name, 0, -1);
            } else {
                $name = $zone->name;
            }
        }

        return $name;
    }

    /*
    * 作用:活动是否可以分享
    * 参数:活动ID：activity_id
    *
    * 返回值:可以分享 1 不可分享 0
    */
    public static function canShare($activity_id)
    {
        $shareTicketNum = DB::table('activity_ticket')
            ->where(
                [
                    'activity_id' => $activity_id,
                    'is_share'    => 'yes'
                ]
            )->count();

        return $shareTicketNum == 0 ? 0 : 1;
    }

    /*
    * 作用:获取专版信息
    * 参数:活动ID
    *
    * 返回值:
    */
    public static function  getVipInfo($activity_id)
    {
        $vip = DB::table('vip')
            ->whereIn(
                'id',
                function ($query) use ($activity_id) {
                    $query->from('activity')
                        ->where('id', $activity_id)
                        ->lists('vip_id');
                }
            )
            ->select('id as vip_id', 'name as vip_name')
            ->get();

        return $vip;
    }

    /*
    * 作用:获取有效活动
    * 参数:活动ID
    *
    * 返回值:活动有效 返回活动（obj）活动无效 返回(false)
    */
    public static function hasAvailableActivity($activity_id)
    {
        if ($activity_id == '') {
            return false;
        }
        $activity = DB::table('activity')->where(
            [
                'id' => $activity_id,
                'status' => 1,
            ]
        )->first();

        return is_null($activity) ? false : $activity;
    }

    public static function getInviteResponseData($activity, $user,$maker_id)
    {
        $data = [];
        $user_share_id = Share::getUserShareID($activity->id, $user->uid);
        $user_share_id = $user_share_id==false ? -1 :$user_share_id;
        $data['vip_id'] = $activity->vip_id;
        $data['need_invite_num'] = $activity->invite_num;
        $data['has_invited_num'] = \App\Models\User\Entity::getFollowedInvitationNum($user_share_id);
        $data['tickets'] = \App\Models\Activity\Ticket::getShareTickets($activity->id,$user_share_id,$maker_id);

        return $data;
    }

    /*
    * 作用:专版活动搜索
    * 参数:
    * 
    * 返回值:
    */
    public static function getVipActivity($vip_id, $keywords,$pageSize=15)
    {
        $activity = self::with('activitymaker', 'activitymaker.maker.zone')
            ->where('vip_id' , $vip_id)
            ->where('subject', 'like', '%' . $keywords . '%')
            ->where('end_time','>',time())
            ->orderBy('begin_time','asc')
            ->where('status', 1)->paginate($pageSize);

        $data = [];
        foreach ($activity as $key => $a) {
            $data[$key]['id']                   = $a->id;//活动ID
            $data[$key]['maker_id']             = self::getActivityMakerID($a->id) ;
            $data[$key]['subject']              = $a->subject;
            $data[$key]['list_img']             = getImage($a->list_img,'activity', '');
            $data[$key]['price']                = self::getCheapestTicket($a->id);
            $data[$key]['view']                 = $a->view;
            $data[$key]['ticket_num']           = self::getTicketNum($a->id);
            $data[$key]['begin_time']           = date('m/d', $a->begin_time);
            $data[$key]['isOver']               = $a->end_time < time() ? 1 : 0;
            foreach ($a->activitymaker as $k => $maker) {
                if(isset($maker->maker)){
                    $data[$key]['host_cities'][$k] = str_replace('市','',$maker->maker->zone->name);
                }
            }
        }

        return $data;
    }
    
    /*
    * 作用:获取所在ovo的活动
    * 参数:maker_id ovo中心ID
     *     type 1 将来的活动 2过去的活动 0 所有活动
    * 
    * 返回值:
    */
    public static function getActivityOfYourMaker($maker_id,$exclusion=[], $type = 1,$vip_id=0 ,$version = null)
    {
        $queryBuilder = DB::table('activity_maker')
            ->join('activity','activity_maker.activity_id','=','activity.id')
            ->join('maker','activity_maker.maker_id','=','maker.id')
            ->join('zone','maker.zone_id','=','zone.id')
            ->where([
                'activity_maker.maker_id'=>$maker_id,
                'activity_maker.status'=>1
            ])
            ->whereNotIn('activity.id',$exclusion)
            ->where('activity.status',1)
            ->limit(4)
            ->orderBy('activity.is_recommend','desc')
            ->orderBy('activity.vip_id','desc')
            ->orderBy('activity.begin_time','asc')
            ->select(
                'activity.id','activity.subject','activity.keywords', 'activity.list_img','activity.begin_time','activity.created_at','activity.is_recommend','activity.is_hot','activity.vip_id','activity.view',
                'activity_maker.maker_id','maker.subject as maker_name',
                'zone.name as city');

        if($type == 1){
            $queryBuilder->where('activity.end_time', '>' ,time());
        }

        if($vip_id > 0){
            $queryBuilder->where('activity.vip_id', $vip_id);
        }

        $activities = $queryBuilder->get();
        foreach($activities as $activity){
            $price                          = self::getCheapestTicket($activity->id , $version);
            $activity->subject              = $activity->subject.'-'.self::getCityWithSuffix($activity->city);
            $activity->price                = $price;
            $activity->ticket_num           = self::getTicketNum($activity->id ,$version);
            $activity->list_img             = getImage($activity->list_img,'activity', '');
            $activity->is_vip               = $activity->vip_id == 0 ? 0:1;
            $activity->host_cities          = self::getAllCitiesOfActivity($activity->id);
            $activity->begin_time           = date("m/d",$activity->begin_time);
            $activity->is_new               = self::isNewActivity($activity->created_at);
            $activity->category             = 'A';
            $activity->city                 = str_replace('市','',$activity->city);
            $activity->is_shareable         = self::canShare($activity->id);
            $activity->url                  = createUrl('activity/detail', array('id' => $activity->id, 'pagetag' => config('app.activity_detail')));
            $activity->keywords             = $activity->keywords ? strpos($activity->keywords,' ')!==FALSE ? explode(' ',$activity->keywords) : [$activity->keywords] : [];

            unset($activity->created_at,$activity->vip_id);
        }
        return $activities;
    }

    /*
    * 作用:获取所在城市的活动
    * 参数:$position_id 城市ID
     *     $exclusion 要排除的活动
    *
    * 返回值:
    */
    public static  function getActivityOfYourCity($position_id,$exclusion =[],$type=1, $vip_id=0 ,$version = null)
    {
        $queryBuilder = DB::table('maker')
            ->join('activity_maker','maker.id','=','activity_maker.maker_id')
            ->join('activity','activity_maker.activity_id','=','activity.id')
            ->join('zone','maker.zone_id','=','zone.id')
            ->where([
                'maker.zone_id'=>$position_id,
                'activity_maker.status'=>1,
            ])
            ->whereNotIn('activity.id',$exclusion)
            ->where('activity.status',1)
            ->groupBy('activity.id')
            ->orderBy('activity.is_recommend','desc')
            ->orderBy('activity.vip_id','desc')
            ->orderBy('activity.begin_time','asc')
            ->select(
                'activity.id','activity.subject', 'activity.keywords','activity.list_img','activity.begin_time','activity.created_at','activity.is_recommend','activity.is_hot','activity.vip_id','activity.view',
                'activity_maker.maker_id','maker.subject as maker_name',
                'zone.name as city');
        if($type == 1){
            $queryBuilder->where('activity.end_time', '>' ,time());
        }

        if($vip_id > 0){
            $queryBuilder->where('activity.vip_id', $vip_id);
        }

        $activities = $queryBuilder->get();
        foreach($activities as $activity){
            $price                          = self::getCheapestTicket($activity->id,$version);
            $activity->subject              = $activity->subject.'-'.self::getCityWithSuffix($activity->city);
            $activity->price                = $price;
            $activity->ticket_num           = self::getTicketNum($activity->id);
            $activity->list_img             = getImage($activity->list_img,'activity', '');
            $activity->is_vip               = $activity->vip_id == 0 ? 0:1;
            $activity->begin_time           = date("m/d",$activity->begin_time);
            $activity->is_new               = self::isNewActivity($activity->created_at);
            $activity->host_cities          = self::getAllCitiesOfActivity($activity->id);
            $activity->category             = 'B';
            $activity->location             = str_replace('市','',DB::table('zone')->where('id',$position_id)->first()->name);
            $activity->city                 = str_replace('市','',DB::table('zone')->where('id',$position_id)->first()->name);
            $activity->is_shareable         = self::canShare($activity->id);
            $activity->url                  = createUrl('activity/detail', array('id' => $activity->id, 'pagetag' => config('app.activity_detail')));
            $activity->keywords             = $activity->keywords ? strpos($activity->keywords,' ')!==FALSE ? explode(' ',$activity->keywords) : [$activity->keywords] : [];

            unset($activity->created_at,$activity->vip_id);
        }
        return $activities;
    }

    /*
    * 作用:获取OVO活动列表
    * 参数:$maker_id ovoID type 活动类型
    * @$count 经纪人端为了区分结束的活动和未开始、进行的活动
    * @$is_agent 仅获取有绑定品牌的活动
    * 返回值:
    */
    public static function getActivityListOfMaker($maker_id, $type = 1,$pageSize=15, $vip_id = 0,$keywords = '', $page=1 ,$version = null ,$hotwords = '',$count = '',$is_agent = false)
    {
        $data = [];
        $queryBuilder = DB::table('maker')
            ->join('activity_maker','maker.id','=','activity_maker.maker_id')
            ->join('activity','activity_maker.activity_id','=','activity.id')
            ->join('zone','maker.zone_id','=','zone.id')
//            ->where([
//                'activity_maker.status'=>1,
//            ])
            //->where('activity.subject','like','%'.$keywords.'%')
            ->where('activity_maker.status',1)
            ->where('activity.status',1)
            ->groupBy('activity.id')

            ->select(
                'activity.id','activity.subject','activity.keywords','activity.end_time', 'activity.list_img','activity.begin_time','activity.created_at','activity.is_recommend','activity.is_hot','activity.vip_id','activity.view',
                'activity_maker.maker_id',
                'activity.share_image',
                'zone.name as city');

        //热门关键字
        if ($hotwords) {
            $queryBuilder->where(function($query) use($hotwords){
                $query->where('activity.subject', 'like', '%' . $hotwords . '%')
                    ->orWhere('activity.keywords', 'like', '%' . $hotwords . '%')
                    ->orWhere('activity.content', 'like', '%' . $hotwords . '%');
            });
        }

        //仅获取有绑定品牌的活动 经纪人端使用
        if ($is_agent) {

            // todo 测试提出：如果活动关联的品牌处于禁用的话，不要显示
            // todo changePerson zhaoyf 2018-1-04
            $activity_ids = Brand::gainEnableBrandRelevanceToActivityIds();

            //对结果进行处理
            if (is_null($activity_ids)) return '该活动没有对应绑定的品牌';

            $queryBuilder->whereIn('activity.id',$activity_ids);
        }

        if ($keywords) {
            $queryBuilder->where('activity.subject','like','%'.$keywords.'%');
        }

        if($maker_id>0){
            $queryBuilder->where('activity_maker.maker_id',$maker_id);
        }

        if($vip_id>0){
            $queryBuilder->where('activity.vip_id',$vip_id);

        }
        if ($type == 1) {
            $queryBuilder->where('activity.end_time', '>', time())
                ->orderBy('activity.is_recommend', 'desc')
                ->orderBy('activity.begin_time', 'asc')
                ->orderBy('activity.id', 'desc');
        } elseif ($type == 2) {//取过去的活动时，是倒序排列。
            $queryBuilder->where('activity.end_time', '<', time())
                ->orderBy('activity.begin_time', 'desc');
        }

        if($type>0){
            //针对活动 区分结束的活动和未开始、进行的活动
            if ($count){
                if($page*$pageSize -$count>0 && $page*$pageSize -$count<$pageSize){
                    $take = $page*$pageSize -$count;
                    $skip = 0;
                }elseif($page*$pageSize -$count>0 && $page*$pageSize -$count>$pageSize){
                    $take = $pageSize;
                    $skip= $page*$pageSize -$count;
                }else{
                    $take =0;
                    $skip=0;
                }
                $activities = $queryBuilder->skip($skip)->take($take)->get();
            }else{
                $activities = $queryBuilder->paginate($pageSize);
            }
        }else{
            $endQueryBuilder = clone $queryBuilder;
            $funtureActivities = $queryBuilder->where('activity.end_time', '>' ,time())
                ->orderBy('activity.is_recommend','desc')
                ->orderBy('activity.begin_time','asc')
                ->orderBy('activity.id','desc')
                ->get();
            $endActivityies = $endQueryBuilder->where('activity.end_time', '<' ,time())
                ->orderBy('activity.is_recommend','desc')
                ->orderBy('activity.begin_time','desc')
                ->orderBy('activity.id','desc')
                ->get();
            $activities = array_merge($funtureActivities, $endActivityies);
            $dataCount = count($activities);
            $activities =array_slice($activities, ($page-1) * $pageSize, $pageSize);
        }

        //为了经纪人活动数量的统计
        $total = $queryBuilder->paginate(1);

        foreach($activities as $activity){
            $price                      = self::getCheapestTicket($activity->id , $version);
            if($version && $version< '_v020502'){
                $activity->subject          = $activity->subject.'-'.self::getCityWithSuffix($activity->city);
            }
            $activity->price            = $price;
            $activity->ticket_num       = self::getTicketNum($activity->id);
            $activity->list_img         = getImage($activity->list_img,'activity','');
            $activity->is_vip           = $activity->vip_id == 0 ? 0:1;
            $begin_time                 = $activity->begin_time;
            $activity->begin_time       = date("m/d",$activity->begin_time);
            $activity->is_new           = self::isNewActivity($activity->created_at);
            $activity->host_cities      = self::getAllCitiesOfActivity($activity->id);
            $activity->is_shareable     = self::canShare($activity->id);
//            $activity->city             = str_replace('市','',$activity->city);
            $activity->isOver           = $activity->end_time < time() ? 1 : ($activity->end_time > time() && $begin_time <= time() ? -1 : 0);
            $activity->url              = createUrl('activity/detail', array('id' => $activity->id, 'pagetag' => config('app.activity_detail')));
            $activity->keywords         = $activity->keywords ? strpos($activity->keywords,' ')!==FALSE ? explode(' ',$activity->keywords) : [$activity->keywords] : [];
            $activity->brand_name       = ($brand = \DB::table('activity_brand as ab')
                ->join('brand as b','ab.activity_id','=','b.id')
                ->where('ab.activity_id',$activity->id)
                ->first()) ? $brand->name : '';
            $activity->begin_time_format= date("m/d H:i",$begin_time);
            $activity->begin_time_origin = $begin_time;
            $activity->live_support     = \DB::table('live')->where('activity_id',$activity->id)->count();
            $activity->sign             = $activity->isOver != 1 ? 1 : 0;
//            $activity->dataCount        = $dataCount;
            $activity->dataCount        = $dataCount?:$total->total();
            if($activity->isOver==-1){
                $activity->detail_img   = Banner::where('activity_id',$activity->id)->value('src');
                $activity->detail_img   = getImage($activity->detail_img, 'activity', '');
            }
            $data[]                     = $activity;
        }

        return $data;
    }
    /*
    * 作用:获取所有活动
    * 参数:
    * 
    * 返回值:
    */
    public static function getActivityOfAll($exclusion = [] ,$pageSize =15, $type = 1, $vip_id=0 ,$version = null)
    {
        $data = [];
        $queryBuilder = DB::table('maker')
            ->join('activity_maker','maker.id','=','activity_maker.maker_id')
            ->join('activity','activity_maker.activity_id','=','activity.id')
            ->join('zone','maker.zone_id','=','zone.id')
            ->where([
                'activity_maker.status'=>1,
            ])
            ->whereNotIn('activity.id',$exclusion)
            ->where('activity.status',1)
            ->groupBy('activity.id')
            ->orderBy('activity.is_recommend','desc')
            ->orderBy('activity.vip_id','desc')

            ->select(
                'activity.id','activity.subject','activity.keywords', 'activity.list_img','activity.begin_time','activity.end_time','activity.created_at','activity.is_recommend','activity.is_hot','activity.vip_id','activity.view',
                'activity_maker.maker_id',
                'zone.name as city');
        if($type == 1){
            $queryBuilder->where('activity.end_time', '>' ,time())->orderBy('activity.begin_time','asc');
        }

        if($type == 2){
            $queryBuilder->where('activity.end_time', '<' ,time())->orderBy('activity.begin_time','desc');
        }

        if($vip_id > 0){
            $queryBuilder->where('activity.vip_id', $vip_id);
        }

        $activities = $queryBuilder->get();

        foreach($activities as $activity){
            $price                      = self::getCheapestTicket($activity->id ,$version);
            $activity->list_img         = getImage($activity->list_img,'activity');
            $activity->price            = $price;
            $activity->ticket_num       = self::getTicketNum($activity->id);
            $activity->is_vip           = $activity->vip_id == 0 ? 0:1;
            $activity->isOver           = $activity->end_time > time() ? 0:1;
            $activity->begin_time       = date("m/d",$activity->begin_time);
            $activity->is_new           = self::isNewActivity($activity->created_at);
            $activity->host_cities      = self::getAllCitiesOfActivity($activity->id);
            $activity->category         = 'C';
            $activity->city             = str_replace('市','',$activity->city);
            $activity->is_shareable     = self::canShare($activity->id);
            $activity->url              = createUrl('activity/detail', array('id' => $activity->id, 'pagetag' => config('app.activity_detail')));
            $activity->keywords         = $activity->keywords ? strpos($activity->keywords,' ')!==FALSE ? explode(' ',$activity->keywords) : [$activity->keywords] : [];
            $data[]                     = $activity;
        }

        return $data;
    }





    /*
    * 作用:获取感兴趣的活动
    * 参数:activity_id 排除的活动ID
    *       position_id 城市ID
    * 返回值:
    */
    public static function getActivityOfInterested($activity_id,$maker_id,$position_id)
    {
            $exclusion = [];
            $activity_a = [];
            if($maker_id !=0){
                $activity_a = self::getActivityOfYourMaker($maker_id,[$activity_id]);
                array_walk($activity_a,function($item) use(&$exclusion){
                    $exclusion[] = $item->id;
                });
            }
            $activity_b  = [];
            if($position_id != 0){
                $activity_b = self::getActivityOfYourCity($position_id,$exclusion);
                array_walk($activity_b,function($item) use(&$exclusion){
                    $exclusion[] = $item->id;
                });
            }
            $activity_c = self::getActivityOfAll($exclusion);
            $activities = array_merge($activity_a,$activity_b,$activity_c);

        //最多只返回四个
        return array_slice($activities,0,4);
    }
    /*
    * 作用:获取活动最便宜的门票
    * 参数:activity 活动id
    *
    * 返回值:
    */
    public static function getCheapestTicket($activity_id,$version = null)
    {
        if(empty($activity_id)){
            return 0;
        }

        if($version){

            if(empty($activity_id)){
                return 0;
            }

            //直播票价,只有一张票而且为0返回-1, 其他情况返回最小价格
            $live_ticket = Ticket::where('activity_id',$activity_id)
                ->where('status',1)
                ->where('type',1)
                ->orderBy('price','desc')
                ->get()
                ->toArray();

            $min_price = end($live_ticket);

            if(count($live_ticket)>1){

                return $min_price['price'];
            }

            if(count($live_ticket) == 1){

                return $min_price['price'] == 0 ? -1 : $min_price['price'] ;
            }

        }

        return DB::table('activity_ticket')
            ->where([
                'activity_id'=>$activity_id,
                'status'=>1
            ])
            ->min('price');


    }
    /*
    * 作用:是否为新活动
    * 参数:活动时间
    *
    * 返回值:
    */
    public static function isNewActivity($created_at)
    {
        if(is_object($created_at)){
            return $created_at->timestamp > time()- 3600*24*5 ? 1 : 0;
        }
        return $created_at > time()- 3600*24*5 ? 1 : 0;
    }
    /*
    * 作用:获得举办活动的所有城市
    * 参数:
    *
    * 返回值:
    */
    public static function getAllCitiesOfActivity($activity_id)
    {
        $cities =  DB::table('activity_maker')
            ->join('maker','activity_maker.maker_id','=','maker.id')
            ->join('zone','zone.id','=','maker.zone_id')
            ->where([
                'activity_maker.activity_id'=>$activity_id,
                'activity_maker.status'=>1
            ])->lists('zone.name');
        $cities = array_unique($cities);
        $data = [];
        foreach($cities as $city){
            $data[] = str_replace('市','',$city);
        }
        return $data;
    }
    /*
    * 作用:获取地址后缀
    * 参数:cityname
    * 
    * 返回值:
    */
    public static function getCityWithSuffix($cityname)
    {
        if(stripos($cityname,'市')){
            return str_replace('市','站',$cityname);
        }


        return $cityname.'站';
    }


    /**
     * 根据某个键自增
     */
    public static function incre(Array $incre, Array $field)
    {
        $result =  self::where($field)->increment(array_keys($incre)[0], array_values($incre)[0]);

        return $result;
    }



    public static function getRow(Array $where)
    {
        $result =  self::where($where)->first();

        return $result;
    }

    
    /*
    * 作用:判断用户是否为专版会员
    * 参数:
    * 
    * 返回值:
    */
    public static function isVipUser($uid)
    {
        return \DB::table('user_vip')
            ->where('uid',$uid)
            ->where('end_time','>',time())
            ->count() ? 1 : 0;
    }

    /**
     * 加积分
     *
     * @param $uid
     *
     */
    public static function addScore($uid, $sign_id=0)
    {
        self::addFirstScore($uid, $sign_id);
        self::addSignScore($uid, $sign_id);
        self::addAccumulateScore($uid, $sign_id);
    }

    /**
     * 如果是首次报名成功，就新增20个无界币
     */
    public static function addFirstScore($uid, $sign_id=0)
    {
        $sign = Sign::first($uid);
        return true;
    }

    /**
     * 如果报名成功，就新增10个无界币
     */
    public static function addSignScore($uid, $sign_id=0)
    {
        return true;
    }

    /**
     * 累积报名成功20次，就新增50个无界币
     */
    public static function addAccumulateScore($uid, $sign_id=0)
    {
        $accumulate = Sign::accumulate($uid);

        return true;
    }

    /**
     * 报名成功后发短信    --数据中心版
     * @User yaokai
     * @param $activity
     * @param $user 用户信息
     * @param $type  类型  activityLiveSign activitySiteSign
     */
    public static function sendSmsAfterSign($activity, $user,$type)
    {
        if($type==='activityLiveSign'){
            //订阅该直播
            $live = Live::where('activity_id', $activity->id)->first();
            $url = config('app.app_url') . 'live/detail/'.config('app.version').'?pagetag='.config('app.live_detail').'&id='.$live->id.'&is_share=1';
        }else{
            $url = config('app.app_url') . 'activity/detail/'.config('app.version').'?pagetag=02-2&id='.$activity->id.'&is_share=1';
        }
        @SendTemplateSMS('activityLiveSign',$user->non_reversible,$type,[
            'name' => $activity->subject,
            'time' => date('m月d日 H点i分',$activity->begin_time),
            'url'=>shortUrl($url)
        ],$user->nation_code);
    }

    /**
     * 门票支付成功后更新对应的记录
     *
     * @param $order
     * @param $third_no
     *
     * @return bool
     */
    public static function activityAfterPay($order, $third_no, $order_status=1)
    {
        $user = User::getRow(['uid'=>$order->uid]);
        if(is_object($order)&&$order->status==1){
            return false;
        }
        //改变订单状态
        DB::table('order')->where('order_no', $order->order_no)
            ->update(['status' => $order_status, 'third_no' => $third_no, 'updated_at' => time()]);
        //改变用户票券状态
        DB::table('user_ticket')->where('order_id', $order->id)->where('uid', $order->uid)
            ->update(['status' => 1, 'updated_at' => time()]);
        //改变报名状态
        $ticket = UserTicket::getRow(['order_id' => $order->id]);
        $activity_sign  =  ActivitySign::where('ticket_no', $ticket->ticket_no)->first();
        if (is_object($ticket)) {
            //活动报名
            $result = ActivitySign::where('ticket_no', $ticket->ticket_no)->update(['status' => 0, 'updated_at' => time()]);
            //票减少1
            ActivityTicket::incre(['id' => $ticket->ticket_id], ['surplus' => -1]);
        }
        $activity = ActivityTicket::getInfo($order->ticket_id);
        if (is_object($activity) && $activity->type == 2) {//直播票
            //发短信
            self::sendSmsAfterSign($activity, $user, 'activityLiveSign');
            //提醒
            self::sendMessage($order->uid, $activity->id, $ticket->id);
            //加积分
//            self::addScore($order->uid);
            //订阅该直播
            $live = Live::where('activity_id', $activity->id)->first();
            Subscribe::subscribe(['uid'=>$user->uid,'live_id'=>$live->id,'type'=>1]);
        }elseif(is_object($activity) && $activity->type == 1) {//现场票
            self::sendSmsAfterSign($activity, $user, 'activitySiteSign');
            //发消息
            $param = [
                'title'     => $activity->subject . '报名成功',
                'uid'       => $order->uid,
                'content'   => '您已成功报名 ' . $activity->subject,
                'type'      => 2,
                'post_id'   => $activity->id,
                'url'       => 'user_ticket_id=' . $ticket->id,
                'send_time' => time(),
            ];
            Message::create($param);
            //提醒
            self::sendMessage($order->uid, $activity->id, $ticket->id);
            //加积分
            is_object($activity_sign) ?$sign_id = $activity_sign->id:$sign_id = 0;
            self::addScore($order->uid, $sign_id);
        }


        return true;
    }



    public static function sendMessage($uid,$activity_id, $ticket_id)
    {
        $user = DB::table('user')->where('uid', $uid)->first();
        $activity = DB::table('activity')->where('id', $activity_id)->first();
        //当天十点
        $today_ten = mktime(10, 0, 0, date('m', $activity->begin_time), (date('d', $activity->begin_time)), date('Y', $activity->begin_time));
        //当天凌晨
        $today_zero = mktime(0, 0, 0, date('m', $activity->begin_time), (date('d', $activity->begin_time)), date('Y', $activity->begin_time));
        //昨天十点
        $yesterday_ten = $today_ten - 3600 * 24;
        //昨天凌晨
        $yesterday_zero = $today_zero - 3600 * 24;
        //前天十点
        $qiantian_ten = $today_ten - 3600 * 48;
        //前天凌晨
        $qiantian_zero = $today_zero - 3600 * 48;

        //提前一天提醒
        if ($user->activity_remind == 2 && time()<$today_zero) {
            $title = '活动将于明天举办';
            $content = '活动将于 明天 举行，请届时准时赴会参加';
            self::addMessage($uid, $activity_id, $ticket_id, $title, $yesterday_ten, $content);
        }elseif($user->activity_remind == 2 && time()>$today_zero) {
            $title = '活动将于今天举办';
            $content = '活动将于 今天 举行，请届时准时赴会参加';
            self::addMessage($uid, $activity_id, $ticket_id, $title, $today_ten, $content);
        }

        //提前两天提醒
        if ($user->activity_remind == 3 && time()<$yesterday_zero) {
            $title = '活动将于后天举办';
            $content = '活动将于 后天 举行，请届时准时赴会参加';
            self::addMessage($uid, $activity_id, $ticket_id, $title, $qiantian_ten, $content);
        }elseif($user->activity_remind == 3 && time()<$today_zero) {
            $title = '活动将于明天举办';
            $content = '活动将于 明天 举行，请届时准时赴会参加';
            self::addMessage($uid, $activity_id, $ticket_id, $title, $yesterday_ten, $content);
        }elseif($user->activity_remind == 3 && time()>$today_zero) {
            $title = '活动将于今天举办';
            $content = '活动将于 今天 举行，请届时准时赴会参加';
            self::addMessage($uid, $activity_id, $ticket_id, $title, $today_ten, $content);
        }

        //当天提醒
        if ($user->activity_remind == 1) {
            $title = '活动将于今天举办';
            $content = '活动将于 今天 举行，请届时准时赴会参加';
            self::addMessage($uid, $activity_id, $ticket_id, $title, $today_ten, $content);
        }
    }



    public static function addMessage($uid, $a_id, $t_id, $title, $send_time, $content)
    {
        //发站内信
        Message::create(
            [
                'title'     => $title,
                'uid'       => $uid,
                'content'   => $content,
                'type'      => 2,
                'post_id'   => $a_id,
                'url'       => 'user_ticket_id=' . $t_id,
                'send_time' => $send_time,
            ]
        );

        DB::table('user_ticket')
            ->where('id', $t_id)
            ->update(['is_send_message' => 1, 'updated_at' => time()]);
    }



    /*
    * 作用:获取门票数量
    * 参数:
    * 
    * 返回值:
    */
    public static function getTicketNum($activity_id)
    {
        return \DB::table('activity_ticket')
            ->where('activity_id',$activity_id)
            ->where('type',1)
            ->count();
    }
    /*
    * 作用:判断是否已经领取了分享门票
    * 参数:
    *
    * 返回值:
    */
    public static function hasReceivedTickets($activity_id,$uid)
    {
        $myTicketsNum = DB::table('user_ticket')
            ->where('activity_id',$activity_id)
            ->where('uid',$uid)
            ->where('form','share')
            ->count();
        $availableTicketsNum = DB::table('activity_ticket')
            ->where('activity_id',$activity_id)
            ->where('is_share','yes')
            ->count();
        if($uid == 0){
            return 0;
        }
        return $myTicketsNum == $availableTicketsNum ? 1 :0;
    }

    /*
    * 作用:获取OVO中心ID
    * 参数:
    *
    * 返回值:
    */
    public static function getActivityMakerID($activity_id)
    {
        return DB::table('activity_maker')
            ->where('activity_id',$activity_id)
            ->first()->maker_id;
    }

    /*
     * 活动详情_v020400
     */
    static function activityDetail_v020400($activity_id)
    {
        $data = self::where('id',$activity_id)
            ->select(
            'distribution_id',
            'distribution_deadline',
            'id',
            'subject',
            'banner',
            'detail_img',
            'list_img',
            'begin_time',
            'end_time',
            'time_explain',
            'share_num',//分享转发次数
            'likes',//收藏次数
            'share_image',//
            'view',
            'sham_view',//假浏览数
            'vip_id',
            'description',
            'content',
            'share_summary',//分享文案
            DB::raw("(select count(*) from lab_user_praise AS up WHERE up.relation = 'activity' and up.relation_id = $activity_id and status in ('agree','forgery')) as zan_count"),//点赞
            DB::raw("(select count(*) from lab_comment as c where c.type = 'Activity' and post_id = $activity_id ) as comment_count"),
            DB::raw("(select IFNULL(group_concat(maker_id),'') from lab_activity_maker AS lam WHERE lam.activity_id = $activity_id) as maker_ids"),
            DB::raw("(select IFNULL(group_concat(at.id),'') from lab_activity_ticket as at where at.activity_id = $activity_id ) as ticket_arr"),
            DB::raw("(select count(*) from lab_activity_sign as las where las.activity_id = $activity_id and las.status in (0,1,-3)) as sign_count")
            )
            ->first();

        $reward = Action::where('distribution_id', $data->distribution_id)->where('action', 'share')->first();

        //如果该活动是有直播支持的并且已经结束，那么参与人数要加上直播观看次数
        $live=\DB::table('live')->where('activity_id', $activity_id)->first();
        if($data->end_time<time()&& $live){
            $data->sign_count = $data->sign_count+$live->view;
        }

        if(is_object($reward)){
            $data->share_reward_unit = $reward->give;
            $data->share_reward_num = $reward->trigger;
        }else{
            $data->share_reward_unit = 'none';
            $data->share_reward_num = 0;
        }



        return $data?:[];
    }

    static function getPublicData($obj, $uid=0)
    {
        $data = self::getBase($obj, 0, $uid);
        //dd($data);
        //$data = $data->join('activity_analysid','activity_analysid.activity_id','=','activity.id')->get();
        $return = [];
        if($data){
            $return['id'] = $data['id'];
            $return['list_img'] = getImage($obj->list_img ,'','');
            $return['url'] = $data['url'];
            $return['share_image'] = $data['share_image'];
            $return['subject'] = $data['subject'];
            $return['activity_des'] = $data['activity_des'];
            $return['begin_time_format'] = $data['begin_time_format'];
            $return['live_support'] = \DB::table('live')->where('activity_id',$obj->id)->count();
            $return['sign']= $data['is_over'] ? 0 : 1;
            $return['rebate']  = Distribution::Integer($data['rebate']);
            $return['citys'] = implode('、', self::getAllCitiesOfActivity($data['id']));
            $return['sign_count'] = self::SignCount($data['id']);//参与人数
            if($uid){
                $return['share_reward_num'] = Action::getDistributionByAction('activity', $obj->id, 'share')->trigger;
                $return['share_reward_unit'] = Action::getDistributionByAction('activity', $obj->id, 'share')->give;
            }

            $return['is_recommend'] = $data['is_recommend'];//是否推荐
            $return['is_hot'] = $data['is_hot'];//是否热门
            $return['is_new'] = $data['is_new'];//是否最新
            $return['is_distribution'] = Distribution::IsDeadline($data['distribution_id'],$data['distribution_deadline']);//分销是否失效
            $return['share_score'] = Distribution::shareScore($data['distribution_id']);//分销分享得的积分
        }

        return $return;
    }

    /**获取参与活动的人数*/
    public static function SignCount($activity_id)
    {
        return Sign::where('activity_id',$activity_id)->count();
        //如果该活动是有直播支持的并且已经结束，那么参与人数要加上直播观看次数
//        $live=\DB::table('live')->where('activity_id', $activity_id)->first();
//        if($data->end_time<time()&& $live){
//            $data->sign_count = $data->sign_count+$live->view;
//        }
    }


    /**
     * 获取活动举办场地信息
     */
    public static  function  getMakerInfo($activity_id)
    {
        $types = ['assistance', 'organizer'];
        $qs = array_fill(0,count($types),'?');
        $lists = Maker::with('maker.zone')->where('activity_id', $activity_id)->orderByRaw("FIELD(type,". implode(',', $qs).")", $types)->get()->toArray();

//        dd($lists);
        $maker_names = array_pluck($lists, 'maker.subject');
        $zone_names = array_pluck($lists, 'maker.zone.name');
        $zone_names = array_map(function($i){
            return abandonProvince($i);
        }, $zone_names);


        return compact('zone_names','maker_names');
    }


}