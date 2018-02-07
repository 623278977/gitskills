<?php

namespace App\Services\Version\Login;

use App\Models\User\Industry;
use App\Services\Version\VersionSelect;
use Illuminate\Support\Facades\Input;
use App\Models\User\Entity as User;
use App\Models\ScoreLog;
use App\Models\Partner\Message;
use \DB;


class _v020500 extends VersionSelect
{
    private $services;

    public function __construct($controllerName, $controllerMethod)
    {
        parent::__construct($controllerName, $controllerMethod);
        $this->services = new _v020400();

    }

    /**
     * 注册    --数据中心版
     * @User
     * @param array $param
     * @return array
     */
    public function postRegisteraccount($param = [])
    {
        $tel = trim(Input::get('username'));
        //伪号码
        $username = pseudoTel($tel);
        //md5加盐后的注册号码
        $non_reversible = encryptTel($tel);
        $user = User::where('non_reversible', $non_reversible)->first();

        if (!checkMobile(trim($tel))) {
            return ['status' => FALSE, 'message' => '手机号格式不对'];
        }

        //已经存在的用户
        if ($user) {

            if(Input::get('is_outh') ? 1 : 0){

                $type = Input::get('type');  //qq  wb  wx
                $outh_id = Input::get('outh_id');

                if (empty($type) || empty($outh_id)) {
                    return ['status' => FALSE, 'message' => '参数有误'];
                }

                $key = $type . "_outh_id";
                $user->$key = $outh_id;
                $user->save();
            }

            //不需要,直接登录,返回登录信息
            $data = User::loginSuccess($user);
            $data['is_register'] = 1;

            if (is_array($data)) {
                return ['status' => TRUE, 'message' => $data];
            }

            //用户不存在,注册流程
        } else {
            if (User::getCount(['non_reversible' => $non_reversible])) {
                return ['status' => FALSE, 'message' => '账号已存在'];
            }

            //账号密码
            $nickname = $this->services->generateUniqueNickname(substr($tel, -4) . substr(uniqid(), -4), $tel);
            $password = md5($username);
            depositTel($tel, $non_reversible, 'wjsq');
        }

        //虚拟ovo
        $maker = (object)config('system.virtual_ovo');
        $maker_id = $maker->id;

        if (Input::get('is_outh') ? 1 : 0) {

            $type = Input::get('type');  //qq  wb  wx
            $source = 0;

            switch ($type) {
                case 'qq':
                    $source = 2;
                    break;
                case 'wb':
                    $source = 3;
                    break;
                case 'wx':
                    $source = 1;
                    break;
            }

            $outh_id = Input::get('outh_id');

            if (empty($type) || empty($outh_id)) {
                return ['status' => FALSE, 'message' => '参数有误'];
            }

            $key = $type . '_outh_id';
            $$key = $outh_id;

            $newNickname = uniqid('wjsq_');
            //三方登录,昵称存在,随机昵称
//            $newNickname = Input::get('nickname', $nickname);
//            //过滤emoji表情
//            $newNickname = preg_replace('/\xEE[\x80-\xBF][\x80-\xBF]|\xEF[\x81-\x83][\x80-\xBF]/', '', $newNickname);
//
//            $encode = mb_detect_encoding($newNickname, ["ASCII", 'UTF-8', "GB2312", "GBK", 'BIG5']) ?: 'GBK';
//
//            if ($encode != "UTF-8") {
//                $newNickname = @iconv($encode, 'UTF-8//TRANSLIT//IGNORE', $newNickname);
//            }

            $nickname = $this->services->generateUniqueNickname($newNickname, $username);

            $user = User::create(compact("username", "password", "nickname", $key, 'source','non_reversible'));


        } else {

            $user = User::create(compact("username", "password", "nickname",'non_reversible'));

        }

//        @SendSMS($username, trans('sms.registerSuccess'), 'register', 3);
        @SendTemplateSMS('registerSuccess',$tel,'register','','','wjsq',false);

        //系统消息
        createMessage(
            $user->uid,
            $title = '欢迎加入无界商圈',
            $content = '无界商圈联合分布全国各地OVO运营中心，为你创建一个基于本地、面向全国，充满商机活力的线上商圈。点击查看<a href="' . "wjsq://newuser" . '">.新用户介绍手册.</a>',
            $ext = '',
            $end = '<p>如有疑问，请致电服务热线<span>400-011-0061</span></p>',
            $type = 1,
            $delay = 300
        );

        //赠送积分
//        ScoreLog::add($user->uid, 1888, 'register', '完成注册');

        /*
        createMessage(
            $user->uid,
            $title = '你已成功入驻' . $maker->subject,
            $content = '感谢你选择<a style="color:#1e8cd4" href="' . "wjsq://ovo?makerid={$maker_id}" . '">' . $maker->subject . '</a>，运营中心将努力为你提供本地活动、直播、商机对接等服务。',
            $ext = '',
            $end = '',
            $type = 1,
            $delay = 360
        );
        */

        if (\App\Models\Maker\Member::where('uid', $user->uid)->first()) {
            \App\Models\Maker\Member::where('uid', $user->uid)->update(compact('maker_id'));
        } else {
            \App\Models\Maker\Member::create(['uid' => $user->uid, 'maker_id' => $maker_id]);
        }

        //入驻成功发城市合伙人消息  --弃用
//        Message::newMemberJoinYou($user, $maker_id);
        //重新获取用户信息
        $data = User::loginSuccess($user);
        $data['is_register'] = 1; //0代表新用户

        return ['status' => TRUE, 'message' => $data];
    }


}