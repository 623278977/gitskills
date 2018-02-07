<?php

namespace App\Services\Version\Login;

use App\Models\User\Browse;
use App\Models\User\Industry;
use App\Services\Version\VersionSelect;
use Illuminate\Support\Facades\Input;
use App\Models\User\Entity as User;
use App\Models\ScoreLog;
use App\Models\Partner\Message;
use \DB;


class _v020400 extends VersionSelect
{
    /*
     * 注册，同时增加用户的token值  --数据中心版
     */
    public function postRegister($param = [])
    {
        //手机号
        $tel = trim(Input::get('username'));
        //伪号码
        $username = pseudoTel($tel);
        //md5加盐后的注册号码
        $non_reversible = encryptTel($tel);
        $reg_method = Input::get('reg_method', 'sms');
        $user       = User::where('non_reversible', $non_reversible)->first();

        //短信注册
        if ($reg_method == 'sms') {

            if (!checkMobile(trim($tel))) {
                return ['status' => FALSE, 'message' => '手机号格式不对'];
            }

            //已经存在的用户
            if ($user) {

                //不需要,直接登录,返回登录信息,客户端通过返回数据判断是否走setinfo流程
                $data = User::loginSuccess($user);

                if (is_array($data)) {
                    return ['status' => TRUE, 'message' => $data];
                }

                //用户不存在,注册流程
            } else {

                if (User::getCount(['non_reversible' => $non_reversible])) {
                    return ['status' => FALSE, 'message' => '账号已存在'];
                }

                //账号密码
                $nickname = $this->generateUniqueNickname(substr($tel, -4) . substr(uniqid(), -4),$tel);
                $password = $tel;

            }
        } else if ($reg_method == 'setinfo') {

            if (!$user->nickname) {
                $nickname = $this->generateUniqueNickname(substr($tel, -4) . substr(uniqid(), -4),$tel);
            }

        }

        $zone_id = intval(Input::get('zone_id'));
        if (!$zone_id) {
            return ['status' => FALSE, 'message' => '请选择地区'];
        }

        $industry_id = Input::get('industry_id');
        if (!is_array($industry_id)) {
            return ['status' => FALSE, 'message' => '请选择行业'];
        }

        //找到最近的网点,没有选择虚拟ovo
        $maker = \App\Models\Maker\Entity::findNearByMaker($zone_id);

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
//            $newNickname = Input::get('nickname',$nickname);
//            //过滤emoji表情
//            $newNickname = preg_replace('/\xEE[\x80-\xBF][\x80-\xBF]|\xEF[\x81-\x83][\x80-\xBF]/', '', $newNickname);
//
//            $encode = mb_detect_encoding($newNickname, ["ASCII",'UTF-8',"GB2312","GBK",'BIG5'])?:'GBK';
//
//            if($encode != "UTF-8") {
//                $newNickname = @iconv($encode ,'UTF-8//TRANSLIT//IGNORE',$newNickname);
//            }
            
            $nickname = $this->generateUniqueNickname($newNickname ,$tel);

            if ($reg_method == 'sms') {

                $password = md5($password);
                $user = User::create(compact("username","non_reversible", "password", "nickname", "maker_id", $key, 'source', 'zone_id'));
            }
            if ($reg_method == 'setinfo') {

                $update_data = [
                    'maker_id' => $maker_id,
                    'zone_id' => $zone_id,
                    $key => $outh_id,
                    'source' => $source
                ];

                if ($nickname) $update_data['nickname'] = $nickname;

                User::where('non_reversible', $non_reversible)->update($update_data);

                $user = User::where('non_reversible', $non_reversible)->first();

            }

        } else {

            if ($reg_method == 'sms') {

                $password   = md5($password);
                $user       = User::create(compact("username", "non_reversible", "password", "nickname", "maker_id", 'zone_id'));
            }

            if ($reg_method == 'setinfo') {

                $update_data = [
                    'maker_id' => $maker_id,
                    'zone_id' => $zone_id,
                ];

                if ($nickname) $update_data['nickname'] = $nickname;

                User::where('uid', $user->uid)->update($update_data);

                $user = User::where('non_reversible', $non_reversible)->first();

            }

        }

        Industry::dealUserIndustry($user, $industry_id);
        unset($user->industrys);
        Industry::cache($user, 1);//清除缓存

        if ($reg_method == 'sms') {

            //这个接口已经不用了，所以不加国际短信逻辑，todo  但是还是需要确认 2017-06-06
//            @SendTemplateSMS('registerSuccess',$username,'register');

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

        }

        createMessage(
            $user->uid,
            $title = '你已成功入驻' . $maker->subject,
            $content = '感谢你选择<a style="color:#1e8cd4" href="' . "wjsq://ovo?makerid={$maker_id}" . '">' . $maker->subject . '</a>，运营中心将努力为你提供本地活动、直播、商机对接等服务。',
            $ext = '',
            $end = '',
            $type = 1,
            $delay = 360
        );

        if (\App\Models\Maker\Member::where('uid', $user->uid)->first()) {
            \App\Models\Maker\Member::where('uid', $user->uid)->update(compact('maker_id'));
        } else {
            \App\Models\Maker\Member::create(['uid' => $user->uid, 'maker_id' => $maker_id]);
        }

        //入驻成功发城市合伙人消息
        Message::newMemberJoinYou($user, $maker_id);
        //重新获取用户信息
        $data = User::loginSuccess($user);

        return ['status' => TRUE, 'message' => $data];
    }

    /*
     * 生成唯一的昵称
     */
    public function generateUniqueNickname($nickname , $username)
    {
        if (User::getCount(['nickname' => $nickname])) {
            $nickname = substr($username, -4) . substr(uniqid(), -4);
            return $this->generateUniqueNickname($nickname , $username);
        }

        return $nickname;
    }


}