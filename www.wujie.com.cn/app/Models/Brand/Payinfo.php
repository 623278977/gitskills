<?php
/**活动模型
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/5/5
 * Time: 11:10
 */
namespace App\Models\Brand;

use Illuminate\Database\Eloquent\Model;
use \DB;
class Payinfo extends Model
{

    protected $dateFormat = 'U';

    protected $table = 'brand_pay_info';

    //黑名单
    protected $guarded = [];


    public function user()
    {
        return $this->hasOne('App\Models\User\Entity', 'uid', 'uid');
    }

    public function brand()
    {
        return $this->hasOne('App\Models\Brand\Entity', 'id', 'brand_id');
    }


}