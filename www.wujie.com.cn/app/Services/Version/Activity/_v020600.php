<?php

namespace App\Services\Version\Activity;

use App\Models\News\Entity as News;
use App\Models\Brand\Entity as Brand;
use App\Models\Video\Entity as Video;

class _v020600 extends _v020500
{
    /*
     * 活动详情页
     */
    public function postDetail($param = [], $tag = false) {
        $result = parent::postDetail($param, $tag);
        //判断是否为过期活动
        if (isset($result['message']['end_time']) && $result['message']['end_time'] < time()) {//结束的活动增加相关视频和资讯
            $videos = Video::where('activity_id', '=', $result['message']['id'])
                    ->where('status', 1)
                    ->limit(10)
                    ->get();
            $result['message']['videos'] = [];
            foreach ($videos as $video) {
                //如果视频描述为空 则截取视频详情
                $description = $video->description;
                if (empty($description)){
                    $description = cut_str(preg_replace('/\s+/i','',str_replace('&nbsp;','',strip_tags($video->content))),50);
                }
                $result['message']['videos'][] = [
                    'image' => getImage($video->image, 'video', ''),
                    'id' => $video->id,
                    'subject' => $video->subject,
                    'duration' => $video->duration,
                    'description' => $description,
                    'keywords' => explode(' ', $video->keywords),
                    'created' =>$video->created_at->timestamp,
                ];
            }
            //获取相关资讯
            $result['message']['news'] = [];
            $news = News::where('type', 'brand')
                    ->whereIn('relation_id', function($query)use($result) {
                        $query->from('activity_brand')
                        ->where('activity_id', '=', $result['message']['id'])
                        ->select('brand_id');
                    })
                    ->limit(10)
                    ->get();
            foreach ($news as $new) {
                $result['message']['news'][] = [
                    'logo' => getImage($new->logo, 'video', ''),
                    'id' => $new->id,
                    'title' => $new->title,
                    'author' => $new->author,
                    'keywords' => explode(' ', $new->keywords),
                    'detail' => $new->detail,
                ];
            }
        }
        return $result;
    }

}
