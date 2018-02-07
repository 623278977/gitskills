<?php

namespace App\Services\Version\Login;

use App\Models\User\Industry;
use App\Models\Activity\Entity as Activity;
use App\Models\User\Ticket as UserTicket;
use App\Services\Version\VersionSelect;
use Illuminate\Support\Facades\Input;
use App\Models\User\Entity as User;
use App\Models\Activity\Ticket;
use App\Models\User\Share;
use App\Models\User\Free;
use App\Models\ScoreLog;
use Illuminate\Support\Facades\Cache;
use App\Models\Partner\Message;
use \DB;
use Illuminate\Http\Request;

class _v020800 extends VersionSelect
{
    protected $services;

    public function __construct($controllerName, $controllerMethod)
    {
        parent::__construct($controllerName, $controllerMethod);
        $this->services = new _v020400();

    }

    /**
     * 登陆注册   --数据中心版
     * @User yaokai
     * @param array $param
     * @return array
     */
    public function postRegisteraccount($param = [])
    {
        $tel = trim(Input::get('username'));

        $nation_code = Input::get('nation_code', '86');

        if (!checkMobile(trim($tel), $nation_code)) {
            return ['status' => FALSE, 'message' => '手机号格式不对'];
        }

        //伪号码
        $username = pseudoTel($tel);
        //用户加密后的手机号
        $non_reversible = encryptTel($tel);
        //找出用户信息
        $user = User::where('non_reversible', $non_reversible)->first();
        //已经存在的用户
        if ($user) {
            if ($user->status == '-1'){
                return ['status' => FALSE, 'message' => '账号异常！请联系客服400-011-0061'];
            }
            if (Input::get('is_outh') ? 1 : 0) {
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
            if (empty($user->token) || !isset($user->token)) {
                $user_name = $user->realname ? $user->realname : $user->nickname;
                $token['token'] = GainToken($user->uid, $user_name, $user->avatar);
                User::where('uid', $user->uid)->update($token);
            }
            $data = User::loginSuccess($user, 0, 1);
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

            $user = User::create(compact("username", 'non_reversible', "password", "nickname", $key, 'source', 'nation_code'));
        } else {
            $user = User::create(compact("username", 'non_reversible', "password", "nickname", 'nation_code'));
        }
        @SendTemplateSMS('registerSuccess', $tel, 'register', [], $nation_code,'wjsq',false);
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


        //入驻成功发城市合伙人消息   --弃用  屏蔽  yaokai
//        Message::newMemberJoinYou($user, $maker_id);
        //重新获取用户信息
        $data = User::loginSuccess($user, 0, 1);
        $data['is_register'] = 0; //0代表新用户

        return ['status' => TRUE, 'message' => $data];
    }


    /**
     * 注册，同时增加用户的token值  --数据中心版
     * @User
     * @param array $param
     * @return array
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
                $data = User::loginSuccess($user, 0,1);

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
                $password = md5($tel);

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
                $user = User::create(compact("username", "non_reversible", "password", "nickname", "maker_id", $key, 'source', 'zone_id'));

                #############################################
                # 通过注册成功后的用户ID，用户名称获取token
                # 必须传参数：用户ID，用户名称，头像
                $token['token'] = GainToken($user->uid, $user->realname ? $user->realname : $user->nickname, $user->avatar);
                User::where('uid', $user->uid)->update($token);

                ################################################
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
                $user       = User::create(compact("username", "non_reversible","password", "nickname", "maker_id", 'zone_id'));

                #############################################
                # 通过注册成功后的用户ID，用户名称获取token
                # 必须传参数：用户ID，用户名称，头像
                $token['token'] = GainToken($user->uid, $user->realname ? $user->realname : $user->nickname, $user->avatar);
                User::where('uid', $user->uid)->update($token);
                ################################################
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
            @SendTemplateSMS('registerSuccess',$tel,'register',[],'86','wjsq',false);

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

        //入驻成功发城市合伙人消息    -- todo  弃用
//        Message::newMemberJoinYou($user, $maker_id);
        //重新获取用户信息
        $data = User::loginSuccess($user, false, true);

        return ['status' => TRUE, 'message' => $data];
    }

    /**
     * 获取ajax登陆   --数据中心版
     *
     * @param $result
     * @return array|string
     * @internal param Request $request
     * @internal param null $version
     */
    public function getAjaxlogin($result)
    {
        $username = trim($result['request']->input('username'));
        $password = trim($result['request']->input('password'));

        if (empty($username) || empty($password)) {
            return ['message' => '账号密码不能为空', 'status' => FALSE];
        }
        if (!checkMobile(trim($username))) {
            return ['message' => '手机号格式不对', 'status' => FALSE, ''];
        }
        $user = Entity::getRow(['username' => $username]);
        if (!isset($user->uid)) {
            return ['message' => '该账号暂未注册', 'status' => FALSE];
        }
        $data = Entity::checkLogin($user, $password);

        if (is_array($data)) {
            $token['token'] = GainToken($user->uid, $user->realname ? $user->realname : $user->nickname, $user->avatar);
            User::where('uid', $user->uid)->update($token);

            return ['message' => $data, 'status' => TRUE];
        }

        return ['message' => $data, 'status' => FALSE];
    }

    /**
     * @param $result
     * @return array|string
     * @internal param null $version
     * @internal param Request $request 登陆 * 登陆
     */
    public function postAjaxlogin($result)
    {
        //手机号
        $tel = trim($result['request']->input('username'));
        //md5加盐后的注册号码
        $non_reversible = encryptTel($tel);
        $password = trim($result['request']->input('password'));

        if (empty($tel) || empty($password)) {
            return ['message' => '账号密码不能为空', 'status' => FALSE];
        }
        if (!checkMobile(trim($tel))) {
            return ['message' => '手机号格式不对', 'status' => FALSE, ''];
        }
        $user = User::getRow(['non_reversible' => $non_reversible]);

        if (!isset($user->uid)) {
            return ['message' => '该账号暂未注册', 'status' => FALSE];
        }
        $data = User::checkLogin($user, $password);
        if (is_array($data)) {
            $token['token'] = GainToken($user->uid,  $user->realname ? $user->realname : $user->nickname, $user->avatar);
            User::where('uid', $user->uid)->update($token);
            return ['message' => $data, 'status' => TRUE];
        }

        return ['message' => $data, 'status' => FALSE];
    }

    /*
    * 作用:邀请注册   --弃用  数据中心不处理
    * 参数:
    *
    * 返回值:
    */
    public function postInviteregister($result)
    {
        $username = trim($result['request']->input('username'));
        $password = trim($result['request']->input('password'));

        $nation_code = $result['request']->input('nation_code', '86');
        if (!$username || !$password) {
            return ['message' => '账号密码不能为空', 'status' => FALSE];
        }
        if (!checkMobile(trim($username), $nation_code)) {
            return ['message' => '手机号格式不对', 'status' => FALSE, ''];
        }
        $password = md5($password);
        $nickname = $result['request']->input('nickname');
        if (!$nickname) {
            return ['message' => '昵称不能为空', 'status' => FALSE];
        }
        if (User::getCount(['username' => $username])) {
            return ['message' => '账号已存在', 'status' => FALSE];
        }
        if (User::getCount(['nickname' => $nickname])) {
            return ['status' => '昵称已存在', 'status' => FALSE];
        }

        $captcha_id     = $result['request']->get('captcha_id', '');
        $captcha_value  = $result['request']->get('captcha_value', '');

        if ($captcha_id == '') {
            return ['message' => '验证码id未传', 'status' => FALSE];
        } else {
            if (Cache::get($captcha_id) != $captcha_value) {
                return ['message' => '验证码错误', 'status' => FALSE];
            }
        }
        $code = $result['request']->get('code', '');
        if ($code == '') {
            return ['message' => '分享标识码未填', 'status' => FALSE];
        }
        $share         = Share::where('code', $code)->first();
        $user_share_id = isset($share) ? $share->id : 0;
        $share_user    = User::where('uid', $share->uid)->get();
        $user          = User::create(compact("username", "password", "nickname", "user_share_id") + ['source' => 5]);
        $new_uid       = $user->uid;

        #############################################
        # 通过注册成功后的用户ID，用户名称获取token
        # 必须传参数：用户ID，用户名称，头像
        $token['token'] = GainToken($user->uid, $user->realname ? $user->realname : $user->nickname, $user->avatar);
        User::where('uid', $user->uid)->update($token);

        //发送注册成功消息
        //亲爱的用户，欢迎加入无界商圈，我们致力将优质的资源带到你的城市！点击下载无界商圈××××××××。如有疑问，请致电服务热线
        $content['name'] = 'welcome';
        $content['tag']  = '';
        $type            = 'receiveTick';

        @SendTemplateSMS($content['name'], $username, $content['name'], [], $nation_code);

        //发送获得赠票信息
        //恭喜你获得“活动名称”的门票两张。请前往无界商圈完成现场门票的领取××××××××。直播门票已放入你手机号对应的账户门票，请及时查收。如有疑问，请致电服务热线 400-011-0061。
        $canObtainTicket = Share::canObtainTicket($code);
        if ($canObtainTicket) {
            //生成票并发送消息
            $activity_id = $share->content_id;
            $tickets = Ticket::where(
                [
                    'activity_id' => $activity_id,
                    'is_share'    => 'yes',
                    'status'      => 1,
                ]
            )->get();
            $hasOfflineTicket = 0;
            foreach ($tickets as $ticket) {
                if ($ticket->type == 1) {
                    //有现场票
                    $hasOfflineTicket = 1;
                } else {
                    //添加直播片到用户账号下
                    //直播票makerid
                    //获取一个makerid
                    $maker_id = DB::table('activity_maker')
                        ->where('activity_id', $activity_id)
                        ->groupBy('activity_id')
                        ->first()->maker_id;
                    UserTicket::createShareTicket($share->uid, $activity_id, $maker_id, 2);
                }
            }
            $num      = count($tickets);
            $activity = Activity::where('id', $activity_id)->first();

            //需要邀请人数等于已经邀请人数
            $needInviteNum = $activity->invite_num;
            $hasInvitedNum = DB::table('user')->where('user_share_id', $user_share_id)->count();

            if ($hasOfflineTicket && $needInviteNum == $hasInvitedNum) {
                $content['name'] = 'obtainTicket';
                $content['tag']  = ['name'=>$activity->subject,'num'=>$num];
                $type            = 'obtainTicket';

                //获取邀请人手机号
                $mobile = DB::table('user')->where('uid', $share->uid)->first()->username;
                @SendTemplateSMS($type, $mobile, $type, $content['tag'], $nation_code);

                //满足条件时  推送
                $result = send_notification('获得分享互动赢取门票活动的门票奖励', "你已完成好友邀请任务，活动活动门票，点击领取门票",
                    json_encode(['type' => 'ticket_receive', 'style' => 'id', 'value' => $activity_id]),
                    $share_user);
            }
        }

        //赠送一次免费抽奖机会
        Free::create(
            [
                'uid'=>$user->uid,
                'num'=>1,
                'use'=>0,
                'source'=>'invite',
                'source_id'=>$new_uid,
            ]
        );

        //入驻成功发城市合伙人消息
        Cache::forget($captcha_id);

        return ['message' => ['score' => 100, 'nickname' => $user->nickname, 'uid' => $user->uid], 'status' => TRUE];
    }
}