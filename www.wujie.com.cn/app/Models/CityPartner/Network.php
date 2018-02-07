<?php

namespace App\Models\CityPartner;

use Illuminate\Database\Eloquent\Model;

class Network extends Model
{
    protected $table = 'city_partner_network';
    protected $guarded = [];
    public static $_DEVICES = [
        1 => "TY10-SMM-A",
        2 => "TY10-SMM-B",
        3 => "TY10-SMM-C",
        4 => "TY20-DMM-A",
        5 => "TY20-DMM-B",
        6 => "TY20-TS",
        7 => "TY20-TS-12X"
    ];
    /**
     * 邀请码验证
     * @param $realname
     * @param $invitecode
     * @return bool
     */
    static function checkLeader($realname, $invitecode)
    {
        $obj = self::where('realname', $realname)
            ->where('invite_token', $invitecode)
            ->first();
        if (isset($obj))
            return $obj->uid;
        return false;
    }

    /**
     * 注册
     * @param $param
     * @return bool|mixed
     */
    static function register($param)
    {
        $data = [
            'username' => $param['phone'],
            'p_uid' => $param['p_uid'],
            'password' => sha1($param['password']),
        ];
        $obj = self::create($data);
        if (count($obj)) return $obj->id;
        return false;
    }

    /**
     * 是否已注册
     * @param $where
     * @return mixed
     */
    static function getCount($where)
    {
        return self::where($where)->count();
    }

    /**
     * 验证账号
     * @param $param
     * @return mixed
     */
    static function checkUser($param)
    {
        $data = self::where('username', $param['phone'])->first();
        if (!$data) return ['status' => 0, 'message' => '该账号未注册'];
        if ($data->password !== md5($param['password']))
            return ['status' => 0, 'message' => '密码错误'];
        return ['status' => 1, 'message' => '验证成功', 'data' => $data];
    }


    /**
     * 获取某合伙人的下属
     */
    static function tree($uid)
    {
        return self::where('p_uid', $uid)->lists('uid')->toArray();
    }
}
