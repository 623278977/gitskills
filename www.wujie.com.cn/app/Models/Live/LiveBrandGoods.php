<?php
/**活动模型
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/5/5
 * Time: 11:10
 */
namespace App\Models\Live;

use Illuminate\Database\Eloquent\Model;

class LiveBrandGoods extends Model
{
    protected $table = 'live_brand_goods';

    public function brandInfo(){
        return $this->hasOne('App\Models\Brand\Entity','id','brand_id');
    }
}