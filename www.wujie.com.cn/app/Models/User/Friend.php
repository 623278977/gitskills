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

class Friend extends Model
{
    protected $table = 'user_friend';

    public $timestamps = false;
    protected $guarded = [];

    static function getCount($where){
        return self::where($where)->count();
    }
    static function getRow($where){
        return self::where($where)->first();
    }
    static function getRemark($uid,$other_uid){
        if(!$uid||!$other_uid)
            return array();
        $where=array(
            'uid'=>$uid,
            'other_uid'=>$other_uid
        );
        $data=null;
        if(self::getCount($where)){
            $data=self::getRow($where)->toArray();
            $data['friend_tel']=explode(',',$data['friend_tel']);
        }
        return $data;
    }
}