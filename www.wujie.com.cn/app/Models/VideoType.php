<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VideoType extends Model
{
    //

    protected $table = 'video_type';

    protected function getDateFormat()
    {
        return date(time());
    }

    /**
     * 返回缓存或者更新缓存
     *
     * @return array
     */

    static function cache($cache = 0)
    {
        $video_type = \Cache::has('video_type') ? \Cache::get('video_type') : false;

        if ($video_type === false || $cache) {
            $data = array();
            $video_type = self::where(
                array(
                    'status' => 1,
                )
            )->where('code','!=','brand')->orderBy('sort', 'desc')->get();
            foreach ($video_type as $k => $v) {
                $data[$k] = self::getBase($v);
            }
            $video_type = $data;
            \Cache::put('video_type', $data, 10);
        }

        return $video_type;
    }

    static function getBase($videoType)
    {
        if (!isset($videoType->id)) {
            return array();
        }
        $data = array();
        $data['id'] = $videoType->id;
        $data['subject'] = $videoType->subject;
        $data['code'] = $videoType->code;
        $data['big_image'] = getImage($videoType->big_image, 'videoType', '', 0);
        $data['is_hot'] = $videoType->is_hot;


        return $data;
    }

}
