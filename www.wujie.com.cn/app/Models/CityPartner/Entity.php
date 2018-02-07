<?php

namespace App\Models\CityPartner;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use Illuminate\Auth\Authenticatable;
use Illuminate\Auth\Passwords\CanResetPassword;
use DB, Crypt, Hash;

class Entity extends Model implements AuthenticatableContract, CanResetPasswordContract
{
    use Authenticatable, CanResetPassword;
    protected $primaryKey = 'uid';
    protected $dateFormat = 'U';
    protected $table = 'city_partner';
    protected $guarded = [];

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
            'password' => Hash::make($param['password']),
            'invite_token' => $param['invite_token'],
            'status' => 1
        ];
        $obj = self::create($data);
        if (count($obj)) return $obj->uid;
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
        $data = self::where('username', trim($param['phone']))->first();
        if (!$data) return ['status' => 0, 'message' => ['phone_error', '该账号尚未注册!']];
        if (!Hash::check($param['password'], $data->password))
            return ['status' => 0, 'message' => ['password_error', '密码错误!']];
        return ['status' => 1, 'message' => '验证成功', 'data' => $data];
    }


    /**
     * 获取某合伙人的下属
     */
    static function tree($uid)
    {
        return self::where('p_uid', $uid)->lists('uid')->toArray();
    }


    /**
     * 我的团队
     * @param $mid
     * @return mixed
     */
    static function myTeam($mid)
    {
        $data = DB::table('city_partner as cp')
            ->leftjoin('city_partner_achievement as cpa', 'cp.uid', '=', 'cpa.partner_uid')
            ->where('cp.p_uid', $mid)
            ->where('cp.status', 1)
            ->orderBy('amount', 'desc')
            ->orderBy('cp.created_at','asc')
            ->groupBy('cp.uid')
            ->select(DB::raw('sum(amount) as amount,lab_cp.uid,lab_cp.realname,lab_cp.zone_id,lab_cp.is_open_ovo,lab_cp.avatar'))
            ->get();
        return $data;
    }

    /**
     * 团队成员详情
     * @param $uid
     * @return mixed
     */
    static function detailINfo($uid)
    {
        $achievement = DB::table('city_partner_achievement as cpa')
            ->where('cpa.partner_uid', $uid)
            ->select('cpa.partner_uid', 'cpa.arrival_at', 'cpa.amount', 'cpa.title')
            ->paginate(20);
        $userInfo = self::where('uid', $uid)->first();
        $userInfo->network_id = DB::table('maker')->where('partner_uid',$uid)->where('status',1)->select('id')->first();
        return [$achievement, $userInfo];
    }

    /**
     * @return mixed
     * 获取当前用户
     */
    static function getCurrentuser()
    {
        //Session::get('userinfo') 正式
        return \App\Models\CityPartner\Entity::where('uid', 1)->first();//测试
    }

    /**
     * 更新登陆信息
     * @param $phone
     * @param $data
     */
    static function upDateLoginInfo($phone, $data)
    {
        $res = self::where('username', $phone)
            ->update($data);
    }
}
