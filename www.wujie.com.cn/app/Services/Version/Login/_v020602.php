<?php

namespace App\Services\Version\Login;

use App\Models\User\Industry;
use App\Services\Version\VersionSelect;
use Illuminate\Support\Facades\Input;
use App\Models\User\Entity as User;
use App\Models\ScoreLog;
use App\Models\Partner\Message;
use \DB;


class _v020602 extends VersionSelect
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
        //手机号
        $tel = trim(Input::get('username'));
        //伪号码
        $username = pseudoTel($tel);
        //md5加盐后的注册号码
        $non_reversible = encryptTel($tel);
        $user = User::where('non_reversible', $non_reversible)->first();
        $nation_code = Input::get('nation_code', '86');

        if (!checkMobile(trim($tel), $nation_code)) {
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
            $password = md5($tel);
            depositTel($tel, $non_reversible, 'wjsq', $nation_code);
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
            $nickname = $this->services->generateUniqueNickname($newNickname, $tel);
            $user = User::create(compact("username","non_reversible", "password", "nickname", $key, 'source','nation_code'));
        } else {
            $user = User::create(compact("username", "non_reversible", "password", "nickname", 'nation_code'));
        }
        @SendTemplateSMS('registerSuccess',$tel,'register', [], $nation_code,'wjsq',false);
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
        if (\App\Models\Maker\Member::where('uid', $user->uid)->first()) {
            \App\Models\Maker\Member::where('uid', $user->uid)->update(compact('maker_id'));
        } else {
            \App\Models\Maker\Member::create(['uid' => $user->uid, 'maker_id' => $maker_id]);
        }
        //入驻成功发城市合伙人消息  todo 这个已经弃用
//        Message::newMemberJoinYou($user, $maker_id);
        //重新获取用户信息
        $data = User::loginSuccess($user);
        $data['is_register'] = 0; //0代表新用户

        return ['status' => TRUE, 'message' => $data];
    }



}