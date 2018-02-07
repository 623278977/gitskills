<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/5/5
 * Time: 11:10
 * 管理对方不看我的好友表
 */
namespace App\Models\Share;

use Illuminate\Database\Eloquent\Model;
use Cache;
class Log extends Model
{
    protected function getDateFormat()
    {
        return date(time());
    }

    protected $table = 'share_log';

    protected $guarded = [];


    static function getCount($where){
        return self::where($where)->count();
    }

    public static function getOne($id)
    {
        if(Cache::has('share_log'.$id)){
            return  Cache::get('share_log'.$id);
        }
        $log = self::where('id', $id)->first();

        //不存在编辑的情况，所以存永久
        Cache::forever('share_log'.$id, $log);

        return $log;
    }




}