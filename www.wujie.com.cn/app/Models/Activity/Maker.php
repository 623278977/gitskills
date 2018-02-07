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
use App\Models\Zone\Entity as Zone;
class Maker extends Model
{

    protected $dateFormat = 'U';

    protected $table = 'activity_maker';

    //黑名单
    protected $guarded = [];

    public function maker()
    {
        return $this->belongsTo('App\Models\Maker\Entity', 'maker_id', 'id');
    }


    /*
    * 作用:获取活动场地信息
    * 参数:活动ID
    *
    * 返回值:
    */
    public static function getMakers($activity_id, $pageSize)
    {
        $makers = self::where('activity_id', $activity_id)->with('maker','maker.zone')->where('status',1)
            ->paginate($pageSize);
        $data = [] ;
        foreach($makers as $key=>$maker){
            $data[$key]['activity_id'] = $maker->activity_id;
            $data[$key]['maker_id'] = $maker->maker_id;
            $data[$key]['subject'] = $maker->maker->subject;
            $data[$key]['address'] = $maker->maker->address;
            $data[$key]['city'] = str_replace('市','',$maker->maker->zone->name);
            $data[$key]['tel'] = $maker->maker->tel;
            $data[$key]['description'] = $maker->maker->description;
        }

        return $data;
    }
}