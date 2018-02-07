<?php
/**活动模型
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/5/5
 * Time: 11:10
 */
namespace App\Models\Brand;

use App\Models\Zone;
use Illuminate\Database\Eloquent\Model;
use DB, Closure;

class Operation extends Model
{

    protected $dateFormat = 'U';

    protected $table = 'user_brand_apply';

    //黑名单
    protected $guarded = [];

    static function baseBuilder(array $param, Closure $callback = NULL)
    {
        $builder = self::where('uid', $param['uid'])
            ->where('brand_id', $param['brand_id'])
            ->where('type',$param['action'])
            ->whereIn('status',['pending','communication']);
        //if (isset($param['action'])) {
        //    $builder->where('type', $param['action']);
        //}
        if ($callback) {
            return $callback($builder);
        }

        return $builder->orderBy('created_at', 'desc')->first();
    }

    static function doJob(array $param)
    {
        return self::create([
            'uid' => $param['uid'],
            'brand_id' => $param['brand_id'],
            'type' => $param['action'],
            'status' => 'pending',
        ]);
    }

}