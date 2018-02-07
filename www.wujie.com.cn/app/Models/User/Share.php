<?php

namespace App\Models\User;

use Illuminate\Database\Eloquent\Model;

class Share extends Model
{
    protected $table = 'user_share';

    protected $guarded = [];

    public function activity()
    {
        return $this->hasOne('App\Models\Activity\Entity','id','content_id');
    }

    public function user()
    {
        return $this->hasOne('App\Models\User\Entity', 'uid','uid');
    }
    public function usershare()
    {
        return $this->hasMany('App\Models\User\Entity', 'user_share_id','id');
    }
    /*
    * 作用:获取分享记录ID
    * 参数:活动ID，用户ID
    *
    * 返回值:记录ID（int）
    */
    public static function getUserShareID($activity_id, $uid)
    {
        $share = self::where([
            'uid'=>$uid,
            'content_id'=>$activity_id,
        ])->first();
        return is_null($share) ? false : $share->id;
    }
    
    /*
    * 作用:判断是否已经满足赠票条件
    * 参数:
    * 
    * 返回值:
    */
    public static function canObtainTicket($code)
    {
        $code = self::with('activity','user')->where('code', $code)->first();
        if(is_null($code)){
            return false;
        }elseif($code->activity->invite_num > $code->usershare()->count()){
            return  false;
        }

        return true;
    }
}
