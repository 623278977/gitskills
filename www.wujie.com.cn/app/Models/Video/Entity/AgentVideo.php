<?php
/**活动模型
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/5/5
 * Time: 11:10
 */

namespace App\Models\Video\Entity;

use App\Models\Agent\AgentBrand;
use App\Models\Brand\BrandVideo;
use App\Models\VideoType;
use \DB, Closure, Input;
use App\Models\Brand\Entity;
use App\Models\Video;

class AgentVideo extends Entity
{
    /**
     * 格式化获取投资人课程视频数据
     */
    static public function detailFormat($brand_id,$agent_id)
    {
        $video  = BrandVideo::where('is_delete','0')
            ->where('brand_id',$brand_id)
            ->get();

        foreach ($video as $k => $v) {
//            $video[$k]['id'] = $v->id;
            $video[$k]['title'] = $v->subject;
            $video[$k]['image'] = getImage($v->image,'video');
//            $video[$k]['created_at'] = $v->created_at;
//            $video[$k]['summary'] = $v->summary;//描述
            $video[$k]['is_read'] = AgentBrand::isRead($agent_id,$brand_id, $v->id, 'video');
            unset($v['subject'],$v['is_delete']);
        }

        return $video;

    }



}