<?php
/**活动模型
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/5/5
 * Time: 11:10
 */

namespace App\Models\Activity;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use App\Models\Brand\Entity as Brands;
use \DB;

class Brand extends Model
{

    protected $timestamp = false;

    protected $table = 'activity_brand';

    //黑名单
    protected $guarded = [];

    public function brand()
    {
        return $this->hasOne('App\Models\Brand\Entity', 'id', 'brand_id');
    }

    /**
     * 获得活动关联的品牌名
     * @param $activity_id 活动id
     *
     * return 返回关联品牌名 $name string
     */
    public static function related_brand($activity_id)
    {
        $name = self::with(['brand' => function ($query) {
            $query->select('id', 'name');
        }])
            ->select('brand_id')
            ->where('activity_id', $activity_id)
            ->get();
        $names = '';
        foreach ($name as $k => $v) {
            $names .= $v->brand->name;
            if ($k>0){
                $names .= '、'.$v->brand->name;
            }
        }
        return $names;
    }

    /**
     * author zhaoyf
     *
     * 只获取活动关联的品牌是在启用的状态下数据
     */
    public static function gainEnableBrandRelevanceToActivityIds()
    {
        $activity_ids = array(); //最后返回的活动ID

        //查询出禁用品牌的ID
        $brand_id = Brands::where(['status' => 'disable', 'agent_status' => 0])
                    ->orwhere('status', 'disable')
                    ->orwhere('agent_status', 0)
                    ->get(['id'])
                    ->toArray();

        //对结果进行处理
        if ($brand_id) {
            $result_brand_id = array_flatten($brand_id);
            $activity_id     = self::whereIn('brand_id', $result_brand_id)
                               ->get(['activity_id'])
                               ->toArray();

            //对结果进行处理
            if ($activity_id) {
                $result_activity_id  = array_flatten($activity_id);
                $confirm_activity_id = self::whereNotIn('activity_id', $result_activity_id)->get(['activity_id']);
            }
        }

        //对结果进行处理
        if (isset($confirm_activity_id) && !empty($confirm_activity_id)) {
            foreach ($confirm_activity_id as $key => $vls) {
                $activity_ids[$key] = $vls->activity_id;
            }
        }

        return $activity_ids;
    }

}