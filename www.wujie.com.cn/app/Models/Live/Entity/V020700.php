<?php
/**活动模型
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/5/5
 * Time: 11:10
 */
namespace App\Models\Live\Entity;

use App\Models\Activity\Entity as Activity;
use App\Models\Distribution\Action;
use App\Models\Video;
use \DB;
use App\Models\Live\Entity;
use App\Models\Distribution\Action\V020700 as ActionV020700;
use App\Models\Distribution\ActionBind;

class V020700 extends Entity
{

    public static function getPublicData($obj, $uid=0, $withdistribution = 0)
    {
        $data = self::detail($obj->id, $uid);
        $return = [];
        if($data ){
            $return['id'] = $obj->id;
            $return['subject'] = $data['live']['subject'];
            $return['list_img'] = getImage($data['live']['list_img'],'','');
            $return['begin_time_format'] = date('m月d日 H:i',$data['live']['begin_time']);
            //带有分享码的地址
            $return['url'] = $data['live']['url'];
            $return['view'] = $data['live']['view'];
            $return['share_num'] = $data['live']['share_num'];
            $return['rebate'] =abandonZero($data['live']['rebate']);

            if($withdistribution && $data['live']['distribution_id']>0){
                //获取分享规则
                $rules = ActionBind::getRules('live', $obj->id);
                $return['rules'] = $rules;
            }
        }

        return $return;
    }


    /**
     * 获取分销详情的直播简要信息
     */
    public static function getDistributionSimple($id)
    {
        $live = self::where('id', $id)->select('id','subject', 'begin_time', 'description', 'share_num', 'view','list_img', 'distribution_id')->first();
        $live->description = cut_str(trim(preg_replace('#<\w+(\s+.*?)?>|</\w+>|&nbsp;#', '', $live->description)),30);
        $live->begin_time = date('m月d日 H:i', $live->begin_time);
        $live->type = 'live';
        $live->list_img = getImage($live->list_img, 'live', '');



        return $live;
    }


    /*
    * 作用:判断直播状态
    * 参数: $btime 开始时间
     *     $etime 结束时间
    *
    * 返回值:0未开始  1进行中 2已结束
    */
    public static function liveStatus($btime, $etime)
    {
        if (time() > $btime && time() < $etime) {
            return 1;
        }elseif (time()>$etime){
            return 2;
        }
        return 0;
    }


}