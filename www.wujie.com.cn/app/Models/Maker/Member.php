<?php
/**ovo模型
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/5/5
 * Time: 11:10
 */
namespace App\Models\Maker;

use App\Models\Activity\Live;
use App\Models\Activity\Maker;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class Member extends Model
{

    public $timestamps = false;

    protected $table = 'maker_member';

    //黑名单
    protected $guarded = [];

    static function getCount($where){
        return self::where($where)->count();
    }
}