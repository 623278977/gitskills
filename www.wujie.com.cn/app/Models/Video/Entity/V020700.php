<?php

namespace App\Models\Video\Entity;

use App\Models\Distribution\Action;
use \DB;
use App\Models\Activity\Entity as Activity;
use App\Models\Video;
use App\Models\Distribution\Action\V020700 as ActionV020700;
use App\Models\Distribution\ActionBind;

class V020700 extends Video
{

    public function getDates()
    {
        return array();
    }

    /*
     * 首页展示
     */
    static function getPublicData($obj, $uid = 0, $withdistribution = 0)
    {
        $data = self::getBase(Video::find($obj->id), $uid);
        $return = [];
        if ($data) {
            $return['id'] = $data['id'];
            $return['image'] = $data['image'];
            $return['subject'] = $data['subject'];
            $return['url'] = $data['url'];
            $return['record_at'] = $data['record_at'];
            $return['share_num'] = $data['share_num'];
            $return['view'] = $data['view'];
            $return['rebate'] = abandonZero($data['rebate']);
            if ($withdistribution && $data['distribution_id'] > 0) {
                //获取分享规则
                $rules = ActionBind::getRules('video', $data['id']);
                $return['rules'] = $rules;
            }
        }


        return $return;
    }


    /**
     * 获取分销详情的视频简要信息
     */
    public static function getDistributionSimple($id)
    {
        $video = self::where('id', $id)->select('id', 'subject', 'created_at', 'description', 'share_num', 'image', 'view', 'distribution_id')->first();
        $video->description = cut_str(trim(preg_replace('#<\w+(\s+.*?)?>|</\w+>|&nbsp;#', '', $video->description)), 30);
        $video->created_at = date('m月d日 H:i', $video->created_at);
        $video->type = 'video';
        $video->list_img = getImage($video->image, 'video', '');
        unset($video->image);

        return $video;
    }

    /**
     * 根据活动id获取相关视频
     * @ids array 活动id
     * return 相关视频
     */
    public static function getVideos($ids)
    {
        $data = self::select(
            'id',
            'activity_id',
            'image',
            'subject',
            'video_url',
            'keywords',
            'description',
            'created_at',
            'duration'
        )
            ->whereIn('activity_id', $ids)
            ->get();
        return $data;
    }

}
