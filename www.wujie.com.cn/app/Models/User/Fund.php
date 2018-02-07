<?php
/**
 * Created by PhpStorm.
 */

namespace App\Models\User;

use Illuminate\Database\Eloquent\Model;

class Fund extends Model
{
    protected function getDateFormat()
    {
        return date(time());
    }

    protected $table = 'user_fund';

    protected $guarded = [];

    /*
        * 是否领取过基金
        */
    public static function fetchedFund($uid, $brand_id)
    {
        $exist = Fund::where(['uid' => $uid, 'brand_id' => $brand_id])
            ->where('created_at', '>', time() - (3600 * 24 * 180))
            ->first();
        if (is_object($exist) && $uid) {
            return true;
        } else {
            return false;
        }
    }

}