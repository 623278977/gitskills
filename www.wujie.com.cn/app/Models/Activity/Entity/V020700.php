<?php
/**活动模型
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/5/5
 * Time: 11:10
 */

namespace App\Models\Activity\Entity;

use App\Models\Activity\Sign;
use App\Models\Distribution\Action;
use App\Models\Zone;
use \DB;
use App\Models\Activity\Entity;
use App\Models\Distribution\Action\V020700 as ActionV020700;
use App\Models\Distribution\ActionBind;

class V020700 extends Entity
{
    public static function getPublicData($obj, $uid = 0, $withdistribution = 0)
    {
        $data = self::getBase($obj, 0, $uid);
        $return = [];
        if ($data) {
            $return['id'] = $data['id'];
            $return['list_img'] = getImage($obj->list_img, '', '');
            $return['url'] = $data['url'];
            $return['subject'] = $data['subject'];
            $return['begin_time_format'] = $data['begin_time_format'];
            $return['view'] = $data['views'];
            $return['share_num'] = $data['share_num'];
            $return['rebate'] = abandonZero($data['rebate']);
            $return['citys'] = implode('、', self::getAllCitiesOfActivity($data['id']));

            if ($withdistribution && $data['distribution_id'] > 0) {
                //获取分享规则
                $rules = ActionBind::getRules('activity', $data['id']);
                $return['rules'] = $rules;
            }
        }

        return $return;
    }

    /**
     * 获取分销详情的活动简要信息
     */
    public static function getDistributionSimple($id)
    {
        $activity = self::where('id', $id)->select('id', 'subject', 'begin_time', 'share_num', 'view', 'distribution_id', 'list_img')->first();
        $activity->host_cities = self::getAllCitiesOfActivity($activity->id);
        $activity->begin_time = date('m月d日 H:i', $activity->begin_time);
        $activity->type = 'activity';
        $activity->list_img = getImage($activity->list_img, 'activity', '');
        return $activity;
    }

    /**
     * 跟进活动id获取活动标签下的活动id
     */
    public static function getTagActivity($id)
    {
        //活动标签
        $tag_id = self::where('id', $id)
            ->value('tag_id');
        if ($tag_id == 0) {
            $activity_ids = [];
            return $activity_ids;//没有的活动标签
        }
        $activity_ids = self::select('id')
            ->where('tag_id', $tag_id)
            ->get()
            ->toArray();
        foreach ($activity_ids as $k=>&$v){
            if($v['id'] == $id){
                unset($v['id']);
            }
        }
        $activity_ids = array_flatten($activity_ids);
        return $activity_ids;
    }

    /**
     * 作用:判断活动状态
     * 参数:$btime 开始时间
     * 参数:$etime 结束时间
     *
     * 返回值:0,1,2  未开始-进行中-已结束
     */
    public static function ActivityStatus($btime, $etime)
    {
        if (time() > $btime && time() < $etime) {
            return 1;//进行中
        }elseif (time()>$etime){
            return 2;//已结束
        }
        return 0;//未开始
    }

    /**
     * 统计用户参加活动的次数
     * @User yaokai
     * @param $uid
     * @return mixed
     */
    public static function userActivitys($uid)
    {
        $count = Sign::where('uid',$uid)
            ->where('status','1')
            ->count();

        return $count?:'0';
    }



}