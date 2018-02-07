<?php
namespace App\Models\User;

use Illuminate\Database\Eloquent\Model;

class Withdraw extends Model
{
    protected $table = 'user_withdraw';

    protected $guarded = [];

    protected $dateFormat = 'U';

    /*
     * 提现列表
     */
    static function lists($param)
    {
        $data = self::where('uid' , $param['uid'])
            ->where('source' , 'currency')
            ->whereIn('status' ,['pending' ,'success'])
            ->paginate($param['pageSize']);

        return $data;
    }

    /*
     * 提现详情
     */
    static function detail($param)
    {
        $data = self::where('uid' , $param['uid'])
            ->where('id',$param['id'])
            ->where('source' , 'currency')
            ->whereIn('status' ,['pending' ,'success'])
            ->first();

        return $data;
    }


}