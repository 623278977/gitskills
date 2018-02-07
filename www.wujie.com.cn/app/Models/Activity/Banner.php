<?php
/**活动签到模型
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/5/5
 * Time: 11:10
 */
namespace App\Models\Activity;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Closure;

class Banner extends Model
{

    protected $dateFormat = 'U';

    protected $table = 'activity_banner';

    //黑名单
    protected $guarded = [];

    static function getCount($where)
    {
        return self::where($where)->count();
    }


    public static function getRow($where ,Closure $callback = NULL)
    {
        $builder = self::where($where);
        if($callback){
            $builder = $callback($builder);
        }
        return $builder->first();
    }





}