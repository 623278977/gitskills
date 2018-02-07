<?php

namespace App\Services\Version\Video;

use App\Services\Version\VersionSelect;
use App\Models\Activity\Entity as Activity;
use \DB;
use App\Models\Video;

class _v020500 extends VersionSelect
{

    /*
     * 点播列表
     */
    public function postList($data)
    {
        $type = $data['request']->input('selection', '');
        $rawData = $data['list'];

        if (empty($rawData)) {
            return ['message' => [], 'status' => true];
        }


        foreach($rawData as &$item){

            $item['is_free'] = 1;
            $item['guest_id'] = 0;
            if($item['activity_id']){
                $ticket_price = \DB::table('activity_ticket')->where('activity_id',$item['activity_id'])
                    ->where('type',-1)->where('status',1)->min('price');
                $item['is_free'] = $ticket_price ? 0 : 1;
            }else{
                $item['guest_id'] = ($guest = \DB::table('guest_relation')->where('relation_type','video')
                    ->where('relation_id',$item['id'])->first())?$guest->guest_id:0;
            }

        }

        $list = $single = $activityData = $guestInfo = $other = [];


        //推荐和热门按活动将数据分组
        if (in_array($type, ['is_recommend', 'is_hot'])) {
            $rawData = array_group_by_key($rawData, 'activity_id');

            foreach ($rawData as $key => $value) {
                $activity_name = Activity::find($key)->subject ?: '';
                if($activity_name){
                    $activityData['activity_name'] = $activity_name;
                    $activityData['data'] = $value;
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
                    array_unshift($list,$guestInfo);
                }else{
                    $other['other'] = '';
                    $other['data'] = $value;
                    $list[] = $other;
                }
            }
        }

        return ['message' => $list ?: ($rawData?:[]), 'status' => true];
    }

    /*
     * 点播详情
     */
    public function postDetail($param)
    {
        $list = Video::detail_v25($param['id'], $param['uid']);
        $rec = Video::recommend($param['id'], $param['uid'], 1, 10);
        $detail['self'] = $list;
        $detail['rec'] = $rec;
        $detail['page_url'] = createUrl('video/detail', array('id' => $param['id'], 'uid' => $param['uid'], 'pagetag' => config('app.video_detail')));
        //分享标识码
        $detail['share_mark'] = makeShareMark($param['id'], 'video', $param['uid']);
//        $detail['code'] = md5(uniqid().rand(1111,9999));
        //该用户对该目标点击缓存加1
        if($param['uid']){
            $origin_cache = \Cache::get('video' . $param['id'] . 'view' . $param['uid'], 0);
            \Cache::forever('video' . $param['id'] . 'view' . $param['uid'], $origin_cache+1);
        }

        return ['message' => $detail, 'status' => true];
    }

}