<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/5/5
 * Time: 11:10
 * 管理对方不看我的好友表
 */
namespace App\Models\User;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Guard extends Model
{
    protected function getDateFormat(){
        return date(time());
    }

    protected $table = 'user_guard';

    protected $guarded = [];

    public $timestamps = false;

    static function getCount($where){
        return self::where($where)->count();
    }
}