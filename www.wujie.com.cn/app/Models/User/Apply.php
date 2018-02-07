<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/5/5
 * Time: 11:10
 */
namespace App\Models\User;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Apply extends Model
{
    protected function getDateFormat(){
        return date(time());
    }

    protected $table = 'user_apply';

    protected $guarded = [];

    static function getCount($where){
        return self::where($where)->count();
    }
}