<?php

namespace App\Models\Vip;

use Illuminate\Database\Eloquent\Model;
use \Cache;
use \DB;
use \Auth;
use App\Models\Live\Entity as Live;
use App\Models\Video;

class User extends Model
{
    protected $table = 'user_vip';
    protected $guarded = ['price'];
    protected $dateFormat = 'U';


    /**
     *根据uid获取某用户对某专版的购买记录, 并把最后的失效时间返回。
     */
    static function getByUid($uid, $vip_id)
    {
        $records = DB::table('user_vip')->where('uid', $uid)->where('vip_id', $vip_id)->get();

        $vips = DB::table('user_vip')->where('vip_id', $vip_id)->where('uid', $uid)->lists('end_time');
        rsort($vips);

        if (isset($vips[0])) {
            $end_time = $vips[0];
        } else {
            $end_time = 0;
        }

        return array('records' => $records, 'end_time' => $end_time);
    }

    static function getRow($where){
        return self::where($where)->first();
    }

}
