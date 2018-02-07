<?php

namespace App\Services;

use App\Models\Video;
class VideoService
{
    /*
     * 点播详情
     */
    public function detail($video_id, $uid)
    {
        $list = Video::detail($video_id, $uid);
        $rec = Video::recommend($video_id, $uid);
        $detail['self'] = $list;
        $detail['rec'] = $rec;
        $detail['page_url'] = createUrl('video/detail',array('id'=>$video_id,'uid'=>$uid,'pagetag'=>config('app.video_detail')));


        return $detail;
    }




}