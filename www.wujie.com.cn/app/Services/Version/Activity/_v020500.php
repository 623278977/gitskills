<?php

namespace App\Services\Version\Activity;

use App\Models\Activity\Live;
use App\Models\User\Praise;
use App\Services\Version\VersionSelect;
use App\Models\Activity\Entity as Activity;
use App\Models\Order\Entity as Order;
use App\Models\Activity\Sign;
use App\Models\ScoreLog;
use App\Models\Activity\Ticket;
use App\Models\Maker\Entity as Maker;
use App\Models\User\Entity as User;
use App\Models\Brand\Entity as Brand;
use App\Models\Vip\Entity as Vip;
use App\Models\Vip\User as Vip_User;
use App\Models\Vip\Term as ViP_term;
use App\Models\User\Ticket as User_Ticket;
use DB;
use App\Services\ActivityService;

class _v020500 extends _v020400
{


    /*
     * 活动详情页
     */
    public function postDetail($param = [], $tag = false)
    {
        $activity_id = (int) $param['id'];

        if (!$activity_id) {
            return ['status' => FALSE, 'message' => '活动id不能为空'];
        }

        if ($activity_id <= 0) {
            return AjaxCallbackMessage('活动id必须要为正整数', FALSE);
        }

        $detail = Activity::activityDetail_v020400($activity_id);

        if (count($detail) == 0) {
            return ['status' => false, 'message' => $detail];
        }

        $data['id'] = $activity_id;

        //活动名称
        $data['subject'] = $detail->subject;
        $data['distribution_id'] = $detail->distribution_id;
        //分销失效时间 _v020700使用
        $data['distribution_deadline'] = $detail->distribution_deadline;

        //收藏数 _v020700使用
        $data['likes'] = $detail->likes;

        //活动图片
        $data['detail_img'][] = getImage($detail->list_img ,'' ,'');

        //专版会员
        $data['is_vip'] = $detail->vip_id ? 1 : 0;
        $vip = Vip::find($detail->vip_id);
        $vipUser = Vip_User::where('uid', $param['uid'])->where('vip_id', $detail->vip_id)->first();
        $data['can_buy'] = $vipUser ? ($vipUser->end_time > time() ? 0 : 1) : 1;

        if ($data['is_vip'] == 0) {
            $data['can_buy'] = 0;
        }

        $data['vip_name'] = $vip ? $vip->name : '';
        $data['vip_id'] = $detail->vip_id ? 1 : 0;

        //是否可以邀请好友
        $data['is_shareable'] = Activity::canShare($activity_id);

        //是否已经收藏
        $data['is_collect'] = $param['uid'] ? Activity::isFavorite($activity_id, $param['uid']) : 0;

        //分享图标
        $data['share_image'] = getImage($detail->share_image ?: 'images/share_image.png', '', '');
        //分享文案 todo 如果tag为真，表示share_summary根据实际的值返回，没有就返回空 zhaoyf 2018-1-02
        if ($tag) {
            $data['share_summary'] = $detail->share_summary ?: '';
        } else {
            $data['share_summary'] =$detail->share_summary  ? $detail->share_summary  : strip_tags($detail->share_summary->description);
        }
        //活动时间
        //$data['begin_time'] = date('Y年m月d日 H:i', $detail->begin_time);
        $data['begin_time'] = $detail->begin_time;
        $data['end_time'] = $detail->end_time;

        $data['begin_time_content']['begin_time'] = $data['begin_time'];
        $data['begin_time_content']['time_explain'] = $detail->time_explain;

        $period = bcdiv(abs($detail->end_time - $detail->begin_time), 3600, 1);

        if (strrpos('0', $period) == 0) {
            $period = str_replace('.0', '', $period);
        }

        $data['begin_time_content']['period'] = $period;

        //活动地点
        $maker_ids = strpos($detail->maker_ids, ',') !== FALSE ? explode(',', $detail->maker_ids) : [$detail->maker_ids];
        $maker_info = Maker::getMakerInfo($maker_ids);

        $position = [];
//        dd($maker_info);

        foreach ($maker_info as &$maker) {
            if (!$maker['zone']) continue;

            $position[] = $maker['zone'] = str_replace('市', '', $maker['zone']);
            unset($maker['image'], $maker['logo'], $maker['image'], $maker['uid'], $maker['groupid'], $maker['alpha']);
        }

        $data['activity_location_arr'] = $maker_info;
        $data['activity_location'] = implode('、', count($position) > 5 ? array_slice($position, 0, 5) : $position);
        if (count($position) > 5) {
            $data['activity_location'] = $data['activity_location'] . '等';
        }

        //活动费用
        $ticket_info = Ticket::getMinTicket($activity_id);


        //价格最低的现场票
        $data['min_ticket_price'] = $ticket_info;
        $data['min_ticket_price_type'] = '现场票';//默认值

        if($data['min_ticket_price'] == '免费'){
            $data['ticket_id'] = Ticket::where('activity_id' , $activity_id)->where('type' , 1)->where('status' , 1)->first()->id;
        }else{
            $data['ticket_id'] = 0;
        }

        /*
        foreach ($ticket_price_arr as $key => $value) {
            if ($value == $data['min_ticket_price']) {
                $data['min_ticket_price_type'] = Ticket::getType($key);
                break;
            }
        }
        */

        //报名人数
        $data['sign_count'] = $detail->sign_count;

        $data['description'] = $detail->description;

        //活动详情 _v020700使用
        $data['content'] = $detail->description;

        //如果活动描述为空 则截取视频详情
        if (empty($data['description'])){

            $data['description'] = cut_str(preg_replace('/\s+/i','',str_replace('&nbsp;','',strip_tags($data['content']))),50);
        }
        //如果活动详情为空 则获取视频描述
//        if (empty($data['content'])){
//            $data['content'] = $data['description'];
//        }

        //活动嘉宾
        //$data['guest_info'] = Guest::getActivityGuests($activity_id);

        //相关品牌
        $brand = Brand::baseLists('activityBrandList', ['activity_id' => $activity_id], function ($builder) {
            $builder->select(
                'id',
                'uid',
                'slogan',
                'brand_summary',
                'logo',
                'name',
                'investment_min',
                'investment_max',
                'keywords',
                'details',
                'introduce',
                DB::raw('(select concat_ws(",",id,name) from lab_zone WHERE lab_zone.id = lab_brand.zone_id) as zone_name'),
                DB::raw('(select if(GROUP_CONCAT(activity_id), GROUP_CONCAT(activity_id), 0) from lab_activity_brand WHERE lab_activity_brand.brand_id = lab_brand.id) as activity_id'),
                DB::raw('(select name from lab_categorys as c where c.id = lab_brand.categorys1_id ) AS category_name')
            );

            return $builder;
        }, function ($data) {
            $obj = new \App\Services\Version\Brand\_v020400();
            foreach ($data as $item) {
                $item->investment_min = formatMoney($item->investment_min);
                $item->investment_max = formatMoney($item->investment_max);
                $item->logo = getImage($item->logo);
                $item->investment_arrange = $item->investment_min . '万-' . $item->investment_max . '万';
                $item->zone_name = $obj->formatZoneName($item->zone_name);
                //$item->remark = getBrandRemark($item->activity_id);
                //$item->industry_ids = $this->getBrandIndustry($item->industry_ids);
                if ($item->keywords) {
                    $item->keywords = strpos($item->keywords, ' ') !== FALSE ? explode(' ', $item->keywords) : [$item->keywords];
                } else {
                    $item->keywords = [];
                }
            }
            return $data;
        });

        $data['brand'] = $brand;

        //热度
        $data['view_count'] = $detail->sham_view;
        $data['zan_count'] = $detail->zan_count;
        $data['comment_count'] = $detail->comment_count;
        $data['share_count'] = $detail->share_num;
        $data['hot_count'] = $detail->view * 0.5 + $detail->zan_count * 0.5 + $detail->comment_count * 10 + $detail->share_num;


        $site_need_pay = \DB::table('activity_ticket')->where('type',1)->where('status', 1)->where('price', '>', 0)
            ->where('surplus', '>', 0)->where('activity_id', $activity_id)->first();
        //是否有收费的现场票
        $data['site_need_pay'] = is_object($site_need_pay) ?1:0;

        //点赞
        $zans = Praise::getActivityZan($activity_id);
        foreach ($zans as $zan) {
            $zan->avatar = getImage($zan->avatar ?: $zan->image);
        }
        $data['zans'] = $zans;
        //分享的得到的积分
        $data['share_reward_unit'] = $detail->share_reward_unit;
        $data['share_reward_num'] = $detail->share_reward_num;

        //活动banner
        $activityService = new ActivityService();
        $banners = $activityService->banners($activity_id);
        $data['banners'] = $banners;
        //分享标识码
        $data['share_mark'] = makeShareMark($activity_id, 'activity', $param['uid']);
        //是否已点赞
        $is_praise = \DB::table('user_praise')->where('uid', $param['uid'])->where('relation', 'activity')
            ->where('relation_id', $activity_id)->where('status', 'agree')->first();
        $is_praise?$data['is_praise']=1:$data['is_praise'] = 0;
//        $data['code'] = md5(uniqid().rand(1111,9999));

        //该用户对该目标点击缓存加1
        if($param['uid']){
            $origin_cache = \Cache::get('activity' . $activity_id . 'view' . $param['uid'], 0);
            \Cache::forever('activity' . $activity_id . 'view' . $param['uid'], $origin_cache+1);
        }

        //评论,调用以前接口
        return ['status' => TRUE, 'message' => $data];
    }


    /**
     * 报名不支付  --数据中心版
     * @User
     * @param $data
     * @return array
     */
    public function postApplyNoPay($data)
    {
        if(isset($data['share_mark']) && $data['share_mark']){
            $share_remark = \Crypt::decrypt($data['share_mark']);
            $md5 = substr($share_remark, 0,32);
            if($md5!=md5($_SERVER['HTTP_HOST'])){
                return ['message'=>'分享码有误', 'status'=>true];
            }
            $share_remark = explode('&', substr($share_remark, 44));
            $data['source_uid'] = $share_remark[2];
        }else{
            $data['source_uid'] = 0;
        }

        $non_reversible = encryptTel($data['tel']);

        //判断其是否已经报名
        $sign = Sign::whereIn('status', [0, 1])->where(['non_reversible' => $non_reversible, 'activity_id' => $data['activity_id']])->first();
        if (is_object($sign)) {
            return ['data' => '该手机号已报名', 'status' => false];
        }



        $activityService = new ActivityService();
        $result = $activityService->postApplyNoPay($data);


        return $result;
    }





    /*
     * 获取活动的报名信息
    */
    public function postEnrollInfos($param)
    {
        //活动信息
        $activity = \DB::table('activity')->where('id', $param['id'])->select('subject', 'begin_time', 'keywords', 'list_img', 'detail_img')->first();

        //场地信息
        $makers = \DB::table('activity_maker')
            ->leftJoin('maker', 'activity_maker.maker_id', '=', 'maker.id')
            ->leftJoin('zone', 'maker.zone_id', '=', 'zone.id')
            ->where('activity_maker.activity_id', $param['id'])
            ->where('activity_maker.status', 1)
            ->select('zone.name', 'maker.address', 'maker.tel', 'maker.subject','maker.id')
            ->get();

        $citys = [];
        foreach($makers as $k=>$v){
            $v->name = str_replace('市','',$v->name);
            $citys[] = $v->name;
        }
        $activity->host_cities = $citys;
        $activity->begin_time_format = date('Y年m月d日 H:i', $activity->begin_time);
        $activity->keywords ?$activity->keywords = explode(' ', $activity->keywords):$activity->keywords = [];
        $activity->list_img = getImage($activity->list_img,'activity', '', 0);
        $activity->detail_img = getImage($activity->detail_img,'activity', '', 0);


        //门票信息
        $ticket = \DB::table('activity_ticket')
            ->where('activity_id', $param['id'])
            ->where('status',1)
            ->select('is_recommend', 'name', 'price', 'original',
                'surplus as left', 'intro', 'type', 'id', 'remark')
            ->addSelect(\DB::raw("'$activity->subject' subject"))
            ->orderBy('type', 'desc')
            ->get()
        ;

        foreach($ticket as $k=>$v){
            if($v->left==0){
                $v->is_recommend = 0;
            }
            $v->price = abandonZero($v->price);
            $v->name ?:$v->name='直播票';
        }

        return ['status' => TRUE, 'message' => ['activity'=>$activity, 'makers'=>$makers, 'ticket'=>$ticket]];
    }





}