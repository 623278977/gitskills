<?php
/**用户关注行业模型
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/5/5
 * Time: 11:10
 */
namespace App\Models\User;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class Lottery extends Model
{
    protected $table = 'user_lottery';

    protected $dateFormat = 'U';

    protected $guarded = [];

    public static $_NEW_YEAR_REWARD=[
        '1' => '谢谢参与',
        '2' => '100积分',
        '3' => '通用红包1888元',
        '4' => '爱奇艺vip月卡',
        '5' => '100元京东购物卡',
    ];



}