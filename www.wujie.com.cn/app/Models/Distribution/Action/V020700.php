<?php
/**活动模型
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/5/5
 * Time: 11:10
 */
namespace App\Models\Distribution\Action;

use App\Models\Activity\Entity as Activity;
use App\Models\Activity\Ticket;
use App\Models\CurrencyLog;
use App\Models\Distribution\ActionBind;
use App\Models\Guest\Relation;
use App\Models\ScoreLog;
use App\Models\Video;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use App\Models\Activity\Maker as ActivityMaker;
use \DB;
use App\Models\Vip\Entity as Vip;
use App\Models\User\Entity as User;
use App\Models\Brand\Entity as Brand;
use App\Models\Brand\Goods;
use App\Models\Distribution\Log as DistributionLog;
use App\Models\Share\Log as ShareLog;
use App\Models\Distribution\Action;

class V020700 extends Action
{
    /**
     * 按照分销id获取下面的动作，并排序好再返回
     * @param $distribution_id
     *
     * @return mixed
     */
    public static function getRules($distribution_id)
    {
       $actions =  self::where('distribution_id', $distribution_id)->where('status', 'enable')
            ->select('action', 'give', 'trigger', 'describe')->get()->toArray();

        $arr = ['share', 'relay', 'view', 'watch', 'enroll', 'sign', 'intent'];

        $func = function ($before, $after) use ($arr)
        {
            return (array_search($before['action'], $arr) < array_search($after['action'], $arr)) ? -1 : 1;
        };
        //排序
        usort($actions, $func);

        foreach($actions as $k=>&$v){
            $v['describe'] = trim($v['describe']);
        }

        return $actions;
    }



    /**
     * 分销说明
     * $param $id 分销 类型id
     * $param $relation_type 关联类型'live','video','brand','news','activity'
     * $param $relation_id 关联ID
     *
     * 定义排序[
     * '分享'         =>'share',
     * '转发二次分享'  =>'relay',
     * '点击查看'      =>'view',
     * '视频观看'      =>'watch',
     * '报名'         =>'enroll',
     * '签到'         =>'sign',
     * '品牌意向'       =>'intent'];
     */
    public static function getDescribe($id, $relation_type, $relation_id)
    {
        //找出对应类型设置的不同分销id
        $action_ids = ActionBind::select('distribution_action_id')
            ->where('relation_type',$relation_type)
            ->where('relation_id',$relation_id)
            ->where('status','enable')
            ->get()
            ->toArray();

        //找出分销明细
        $order = ['share', 'relay', 'view', 'watch', 'enroll', 'sign', 'intent'];
        $describe = self::select('id','describe')
            ->where('distribution_id',$id)
            ->whereIn('id',$action_ids)
            ->where('status', 'enable')
            ->where('describe', '!=', '')
            ->orderByRaw(DB::raw("FIND_IN_SET(action, '" . implode(',', $order) . "'" . ')'))//按照指定顺序排序
            ->get();
        return $describe;
    }

}