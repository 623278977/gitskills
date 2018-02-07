<?php

namespace App\Services\Version\Video;

use App\Models\Distribution\Action;
use App\Models\User\Fund;
use App\Models\Video\Entity as Video;
use App\Models\Activity\Entity as Activity;
use App\Models\Video\Entity;
use App\Models\Distribution\Entity as Distribution;
use App\Models\User\Entity as User;
use App\Models\Video\Entity\V020700 as VideoV020700;
use App\Models\Orders\Items;


class _v020700 extends _v020600
{
    /**
     * 视频详情
     */
    public function postDetail($param)
    {
        $detail = parent::postDetail($param)['message'];
        //如果视频详情为空 则获取视频描述
        if (empty($detail['self']->content)){
            $detail['self']->content = $detail['self']->description;
        }
        if (empty($detail['self']->description)){
            $detail['self']->description = mb_substr(preg_replace('/\s+/i','',str_replace('&nbsp;','',strip_tags($detail['self']->content))),0,50);
        }
        $distribution_id = $detail['self']->distribution_id;//分销关联
        $brand_id = $detail['self']->brand_id;//品牌id
        $activity_id = $detail['self']->activity->id;//活动id
        //相同活动标签下的活动id
        $activity_ids = Activity\V020700::getTagActivity($activity_id);
        //推荐视频
        if(empty($activity_id)){
            $detail['tag_video'] = [];
        }else{
            $detail['tag_video'] = Video\V020700::getVideos($activity_ids);
            foreach($detail['tag_video'] as $k=>$v){
                //如果视频详情为空 则获取视频描述
                    $v['description'] = mb_substr(preg_replace('/\s+/i','',str_replace('&nbsp;','',strip_tags($v['description']))),0,50);
            }
        }
        $detail['distribution'] = Action\V020700::getDescribe($distribution_id,'video',$param['id']);//分享描述
        $detail['fetched_fund'] = Fund::fetchedFund($param['uid'], $brand_id);//是否领取基金
        $detail['fund'] = Video::brand_fund($brand_id);//创业基金数
        $detail['is_distribution'] = Distribution::IsDeadline($detail['self']->distribution_id,$detail['self']->distribution_deadline);//分销是否失效

        return ['message' => $detail, 'status' => true];
    }



    /**
     * 获取录播购买信息
     */
    public function postBuyInfo($param)
    {
        $video = VideoV020700::where('id', $param['id'])
            ->select('id', 'content', 'description', 'image', 'subject', 'created_at', 'score_price')->first();
        //价格信息
        $video->list_img = getImage($video->image, 'video', '');
        $video->record_at = date('m月d日 H:i', $video->created_at);

        $video->description= ($video->description ?$video->description:extractText($video->content));
        //用户积分
        $user = User::where('uid', $param['uid'])->select('score', 'username', 'nickname','realname')->first();
        !$user->realname && $user->realname = $user->nickname;
        unset($video->content, $video->image,$video->created_at,$user->nickname);
        //已售
        $sold_num =  Items::where('type', 'video')->where('product_id', $param['id'])->where('status', 'pay')->sum('num');
        $video->sold_num = $sold_num;
        return ['data'=>['user'=>$user, 'video'=>$video], 'status'=>true];
    }


    /*
 * 点播列表
 */
    public function postList($data)
    {
        $type = $data['request']->input('selection', '');
        $order = $data['request']->input('order', '');
        $rawData = $data['list'];
        if (empty($rawData)) {
            return ['message' => [], 'status' => true];
        }


        foreach($rawData as &$item){
            $item['is_free'] = 1;
            $item['guest_id'] = 0;
            if($item['activity_id']){
                $item['is_free'] = $item['score_price'] ? 0 : 1;
            }else{
                $item['guest_id'] = ($guest = \DB::table('guest_relation')->where('relation_type','video')
                    ->where('relation_id',$item['id'])->first())?$guest->guest_id:0;
            }
        }

        $list = $single = $activityData = $guestInfo = $other = $guestData = [];


        //推荐和热门按活动将数据分组
        if (in_array($type, ['is_recommend', 'is_hot'])) {
            $rawData = array_group_by_key($rawData, 'activity_id');

            foreach ($rawData as $key => $value) {
                $activity_name = Activity::find($key)->subject ?: '';
                if($activity_name){
                    $activityData['activity_name'] = $activity_name;
                    $activityData['data'] = $value;
                    $sorts = array_pluck($activityData['data'], 'sort');
                    $tops = array_pluck($activityData['data'], 'top');
                    $hots = array_pluck($activityData['data'], 'is_hot');
                    $created_ats = array_pluck($activityData['data'], 'created_at');
                    $sorts = array_map(function($item, $k, $hot, $creat_at){
                        return $item*1000+$k+$hot/10+$creat_at/100000000000;
                    }, $tops,$sorts, $hots, $created_ats);
                    $activityData['sort'] = max($sorts);
                    $list[] = $activityData;
                }else{
                    $guestData = $value;
                }
            }

            $rawGuestData = array_group_by_key($guestData ,'guest_id');

            foreach ($rawGuestData as $key => $value) {
                $guestobj = \DB::table('guest')->where('id',$key)->first();

                if($guestobj){
                    $guestInfo['guest_info'] = [
                        'name' => $guestobj->name,
                        'image' => getImage($guestobj->image),
                        'brief' => strip_tags($guestobj->brief),
                    ];
                    $guestInfo['data'] = $value;
                    $guestInfo['sort'] = 100;

                    $sorts = array_pluck($guestInfo['data'], 'sort');
                    $tops = array_pluck($guestInfo['data'], 'top');
                    $hots = array_pluck($guestInfo['data'], 'is_hot');
                    $created_ats = array_pluck($guestInfo['data'], 'created_at');

                    $sorts = array_map(function($item, $k, $hot, $creat_at){
                        return $item*1000+$k+$hot/10+$creat_at/100000000000;
                    }, $tops,$sorts, $hots, $created_ats);

                    $guestInfo['sort'] = max($sorts);
                    array_unshift($list,$guestInfo);
                }else{
//                    $other['other'] = '';
//                    $other['data'] = $value;
//                    $list[] = $other;


                    foreach($value as $k=>$v){
                        $other['other'] = '';
                        $other['sort'] = ($v['top'])*1000+$v['sort']+$v['is_hot']/10+$v['created_at']/100000000000;
                        $other['data'] = [$v];
                        $list[] = $other;
                    }

                }
            }
        }

        if($order && $order=='created_at'){
            $list = collect($list);
        }else{
            $list = collect($list)->sortByDesc('sort');
        }


        return ['message' => $list->count() ?$list: ($rawData?:[]), 'status' => true];
    }


}
