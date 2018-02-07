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
use \DB;

class Guest extends Model
{

    protected $dateFormat = 'U';

    protected $table = 'activity_guest';

    //黑名单
    protected $guarded = [];

    static function getActivityGuests($id)
    {
        $data = self::where('activity_id',$id)
            ->get();

        foreach($data as $item){
            $item->image = getImage($item->image);
        }

        return $data?:[];
    }


}