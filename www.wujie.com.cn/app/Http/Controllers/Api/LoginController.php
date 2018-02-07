<?php
/**
 * 注册登陆控制器
 */
namespace App\Http\Controllers\Api;

use App\Models\Activity\Ticket;
use App\Models\Activity\Entity as Activity;
use App\Models\Identify;
use App\Models\User\Free;
use App\Models\User\Ticket as UserTicket;
use App\Models\Partner\Message;
use DB;
use App\Models\User\Entity;
use App\Models\User\Share;
use App\Models\User\Industry;
use Illuminate\Routing\Route;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Api\CommonController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use PhpParser\Node\Stmt\TryCatch;
use App\Models\ScoreLog;
use App\Models\User\Entity as User;
use App\Http\Requests\Api\Login\RegisterBy400Request;

class LoginController extends CommonController
{
    /**
     * 注册    ---疑似弃用  暂不处理  yaokai
     * @User yaokai
     * @param Request $request
     * @param null $version
     * @return string
     */
    public function postRegister(Request $request, $version = NULL)
    {
        //初始化
        $versionService = $this->init(__METHOD__, $version);

        if($versionService){
            $response = $versionService->bootstrap($request->all());
            return AjaxCallbackMessage($response['message'],$response['status']);
        }

        $is_outh = $request->input('is_outh') ? 1 : 0;  //是否是第三方注册
        $username = trim($request->input('username'));
        //登录方式
        $reg_method = $request->input('reg_method','pass');
        //头像
        $avatar = $request->input('avatar','');
        $needDoneInfo = 0;
        //密码注册
        if($reg_method == 'pass'){
            $password = trim($request->input('password'));
            if (!$username || !$password) {
                return AjaxCallbackMessage('账号密码不能为空', FALSE);
            }
            //if ($username == $password) {
            //    return AjaxCallbackMessage('账号密码不能相同', FALSE);
            //}
            if (!checkMobile(trim($username))) {
                return AjaxCallbackMessage('手机号格式不对', FALSE, '');
            }
            $nickname = $request->input('nickname');
            if (!$nickname) {
                return AjaxCallbackMessage('昵称不能为空', FALSE);
            }
            if (Entity::getCount(['username' => $username])) {
                return AjaxCallbackMessage('账号已存在', FALSE);
            }
            if (Entity::getCount(['nickname' => $nickname])) {
                return AjaxCallbackMessage('昵称已存在', FALSE);
            }
        }
        //短信注册
        else if($reg_method == 'sms') {
            if (!checkMobile(trim($username))) {
                return AjaxCallbackMessage('手机号格式不对', FALSE, '');
            }
            //已经存在的用户
            $user = Entity::where('username', $username)->first();
            $industry_ids = Industry::where('uid',$user->uid)->first();//用户行业

            //用户存在,登录
            if ($user) {
                //用户需要完善资料,添加标识$neddDoneInfo=1
                if(!$user->zone_id || !$user->nickname || !$industry_ids ){
                    $needDoneInfo = 1;
                }
                //不需要,直接登录,返回登录信息
                $data = Entity::loginSuccess($user);
                if (is_array($data)) {
                    return AjaxCallbackMessage($data, TRUE);
                }

            //用户不存在,注册流程
            }else{
                $nickname = trim($request->input('nickname'));
                $password = trim($request->input('password'));
                if (!$nickname) {
                    return AjaxCallbackMessage('昵称不能为空', FALSE);
                }
                if (!$password) {
                    return AjaxCallbackMessage('密码不能为空', FALSE);
                }
                //if ($username == $password) {
                //    return AjaxCallbackMessage('账号密码不能相同', FALSE);
                //}
                if(!$needDoneInfo){
                    if (Entity::getCount(['username' => $username])) {
                        return AjaxCallbackMessage('账号已存在', FALSE);
                    }
                }
                if (Entity::where('nickname', $nickname)->where('username',$username)->count()) {
                    return AjaxCallbackMessage('昵称已存在', FALSE);
                }
            }
        }
        else if($reg_method == 'setinfo')
        {
            $nickname = trim($request->input('nickname'));
            $password = trim($request->input('password'));
            $user = Entity::where('username', $username)->first();
            if (!$nickname) {
                return AjaxCallbackMessage('昵称不能为空', FALSE);
            }
            if (Entity::where('nickname', $nickname)->where('uid','<>',$user->uid)->count()) {
                return AjaxCallbackMessage('昵称已存在', FALSE);
            }
            if (!$password) {
                return AjaxCallbackMessage('密码不能为空', FALSE);
            }
            //if ($username == $password) {
            //    return AjaxCallbackMessage('账号密码不能相同', FALSE);
            //}
        }

        //$maker_id = intval($request->input('maker_id'));
        //if (!$maker_id) {
        //    return AjaxCallbackMessage('请选择OVO运营中心', FALSE);
        //}
        $industry_id = $request->input('industry_id');
        if (!is_array($industry_id)) {
            return AjaxCallbackMessage('请选择行业', FALSE);
        }

        //兼容老版本
        if($request->has('maker_id')){
            $maker_id = $request->input('maker_id');
            $maker = \App\Models\Maker\Entity::where('id',$maker_id)->first();
            $zone_id = $maker?$maker->zone_id:0;
        }else{
            $zone_id = intval($request->input('zone_id'));
            if (!$zone_id) {
                return AjaxCallbackMessage('请选择地区', FALSE);
            }
            //找到最近的网点,没有选择虚拟ovo
            $maker = \App\Models\Maker\Entity::findNearByMaker($zone_id);
            $maker_id = $maker->id;
        }

        if ($is_outh) {
            $type = $request->input('type');  //qq  wb  wx
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
            $outh_id = $request->input('outh_id');
            if (empty($type) || empty($outh_id)) {
                return AjaxCallbackMessage('参数有误', FALSE);
            }
            $avatar = $request->input('avatar');
            $key = $type . '_outh_id';
            $$key = $outh_id;
            $password = md5($password);
            if($reg_method == 'setinfo') {
                Entity::where('username', $username)->update(['password' => md5($password), 'nickname' => $nickname, 'maker_id' => $maker_id, 'zone_id' => $zone_id]);
                $user = Entity::where('username',$username)->first();
            }else{
                $user = Entity::create(compact("username", "password", "nickname", "maker_id", $key, 'source','zone_id' ,'avatar'));
            }
            if ($avatar) {
                $user->avatar = $avatar;
                $user->save();
            }
        } else {
            if($reg_method == 'setinfo'){
                Entity::where('uid',$user->uid)->update(['password'=>md5($password),'nickname'=>$nickname,'maker_id'=>$maker_id,'zone_id'=>$zone_id ]);
                $user = Entity::where('username',$username)->first();
            }else{
                if($needDoneInfo){
                    Entity::where('uid',$user->uid)->update(['password'=>md5($password),'nickname'=>$nickname,'maker_id'=>$maker_id,'zone_id'=>$zone_id]);
                    $user = Entity::where('username',$username)->first();
                }else{
                    $password = md5($password);
                    $user = Entity::create(compact("username", "password", "nickname", "maker_id",'zone_id'));
                    //$user->realname=$user->uid;
                    //$user->save();
                }
            }
        }
        Industry::dealUserIndustry($user, $industry_id);
        unset($user->industrys);
        Industry::cache($user, 1);//清除缓存

        @SendTemplateSMS('registerSuccess',$username,'register');
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
        createMessage(
            $user->uid,
            $title = '你已成功入驻' . $maker->subject,
            $content = '感谢你选择<a style="color:#1e8cd4" href="' . "wjsq://ovo?makerid={$maker_id}" . '">' . $maker->subject . '</a>，运营中心将努力为你提供本地活动、直播、商机对接等服务。',
            $ext = '',
            $end = '',
            $type = 1,
            $delay = 360
        );
        $uid = $user->uid;
        if(\App\Models\Maker\Member::where('uid',$user->uid)->first()){
            \App\Models\Maker\Member::where('uid',$user->uid)->update(compact('maker_id'));
        }else{
            \App\Models\Maker\Member::create(compact('uid', 'maker_id'));
        }
        //入驻成功发城市合伙人消息
        Message::newMemberJoinYou($user, $maker_id);
        //重新获取用户信息
        $data = Entity::loginSuccess($user);

        return AjaxCallbackMessage($data, TRUE);
    }

    /**
     * 登陆 -- 增加版本 _v020800 zhaoyf   ---疑似弃用  暂不处理   yaokai
     *
     * @param   Request $request
     * @param   null $version
     * @return  string
     */
    public function getAjaxlogin(Request $request, $version = null)
    {
        if ($version  && substr($version , 2) >= "020800") {

            $result = $request->input();
            //初始化
            $versionService = $this->init(__METHOD__, $version);

            if ($versionService) {
                $response = $versionService->bootstrap($result, ['request' => $request]);

                return AjaxCallbackMessage($response['message'], $response['status']);
            }
        }

        $username = trim($request->input('username'));
        $password = trim($request->input('password'));
        if (empty($username) || empty($password)) {
            return AjaxCallbackMessage('账号密码不能为空', FALSE);
        }
        if (!checkMobile(trim($username))) {
            return AjaxCallbackMessage('手机号格式不对', FALSE, '');
        }
        $user = Entity::getRow(['username' => $username]);
        if (!isset($user->uid)) {
            return AjaxCallbackMessage('该账号暂未注册', FALSE);
        }
        $data = Entity::checkLogin($user, $password);

        if (is_array($data)) {
            //$token['token'] = GainToken($user->uid, $user->realname ? $user->realname : $user->nickname, $user->avatar);
            //User::where('uid', $user->uid)->update($token);
            return AjaxCallbackMessage($data, TRUE);
        }

        return AjaxCallbackMessage($data, FALSE);
    }

    /**
     * @param Request $request   ---疑似弃用  暂不处理  yaokai
     * 登陆
     * @param null $version
     * @return string
     */
    public function postAjaxlogin(Request $request, $version = null)
    {
        if ($version  && substr($version , 2) >= "020800") {

            $result = $request->input();
            //初始化
            $versionService = $this->init(__METHOD__, $version);

            if ($versionService) {
                $response = $versionService->bootstrap($result, ['request' => $request]);

                return AjaxCallbackMessage($response['message'], $response['status']);
            }
        }

        $username = trim($request->input('username'));
        $password = trim($request->input('password'));
        if (empty($username) || empty($password)) {
            return AjaxCallbackMessage('账号密码不能为空', FALSE);
        }
        if (!checkMobile(trim($username))) {
            return AjaxCallbackMessage('手机号格式不对', FALSE, '');
        }
        $user = Entity::getRow(['username' => $username]);

        if (!isset($user->uid)) {
            return AjaxCallbackMessage('该账号暂未注册', FALSE);
        }
        $data = Entity::checkLogin($user, $password);
        if (is_array($data)) {
            //$token['token'] = GainToken($user->uid,  $user->realname ? $user->realname : $user->nickname, $user->avatar);
            //User::where('uid', $user->uid)->update($token);
            return AjaxCallbackMessage($data, TRUE);
        }

        return AjaxCallbackMessage($data, FALSE);
    }

    /**
     * 第三方登陆
     * type=  qq  wb  wx
     * @param Request $request
     * @return string
     * @internal param null $version
     */
    public function postOuthlogin(Request $request)
    {
        $type = $request->input('type');  //qq  wb  wx
        $outh_id = $request->input('outh_id');
        if (empty($type) || empty($outh_id)) {
            return AjaxCallbackMessage('参数有误', FALSE);
        }
        $user = Entity::getRow([$type . "_outh_id" => $outh_id]);


        if (isset($user->uid)) {
            //已经登陆过
            $data = Entity::loginSuccess($user);
            //$token['token'] = GainToken($user->uid, $user->realname ? $user->realname : $user->nickname, $user->avatar);
            //User::where('uid', $user->uid)->update($token);
            $data['is_register']=1;
            return AjaxCallbackMessage($data, TRUE);
        } else {
            //还未登陆过
            return AjaxCallbackMessage('请先绑定手机号', FALSE);
        }
    }

    /**
     * 忘记密码type 1  修改密码type 2     --弃用  不处理  yaokai   2017.12.14
     *
     */
    public function postDopassword(Request $request)
    {
        $type = $request->input('type');
        if (empty(intval($type))) {
            return AjaxCallbackMessage('类型不能为空', FALSE);
        }
        $newpassword = $request->input('newpassword');
        if (empty($newpassword)) {
            return AjaxCallbackMessage('密码不能为空', FALSE);
        }
        $username = $request->input('username');
        if (empty($username)) {
            return AjaxCallbackMessage('手机号不能为空', FALSE);
        }
        $password = $request->input('password');
        if ($type == 1 && $password != $newpassword) {
            return AjaxCallbackMessage('两次密码不相同', FALSE);
        }
        if ($type == 2) {
            $uid = $request->input('uid');
            if (!Entity::checkAuth($uid)) {
                return AjaxCallbackMessage('账号异常', TRUE);
            }
        }
        if (Entity::changePassword($username, $newpassword)) {
            return AjaxCallbackMessage('密码修改成功', TRUE);
        }

        return AjaxCallbackMessage('密码修改失败', FALSE);
    }

    /***
     *  校验  --疑似弃用  暂不处理   yaokai
     * @param Request $request
     * username  对应检验一个username是否存在
     * password  对应检验一个uid的password是否正确
     * @return string
     */
    public function postCheckuser(Request $request)
    {
        $data = $request->only('username', 'password', 'uid');
        if ($data['username']) {
            $count = Entity::getCount(
                [
                    'username' => $data['username'],
                ]
            );

            return AjaxCallbackMessage($count, TRUE);
        }
        if ($data['password']) {
            if (!Entity::checkAuth($data['uid'])) {
                return AjaxCallbackMessage('账号异常', FALSE);
            }
            $count = Entity::getCount(
                [
                    'uid' => $data['uid'],
                    'password' => md5($data['password']),
                ]
            );

            return $count > 0 ? AjaxCallbackMessage('密码正确', TRUE) : AjaxCallbackMessage('密码错误', FALSE);
        }

        return AjaxCallbackMessage('参数有误', FALSE);
    }


    /**
     * 第三方登陆绑定已有账号    --疑似弃用  暂不处理   yaokai
     * @User yaokai
     * @param Request $request
     * @return string
     */
    public function postOuthbang(Request $request)
    {
        $username = $request->input('username');
        $user = Entity::getRow(['username' => $username]);
        if (!isset($user->uid)) {
            return AjaxCallbackMessage('手机号不存在', FALSE);
        }
        $type = $request->input('type');  //qq  wb  wx
        $outh_id = $request->input('outh_id');
        if (empty($type) || empty($outh_id)) {
            return AjaxCallbackMessage('参数有误', FALSE);
        }
        $key = $type . "_outh_id";
        $user->$key = $outh_id;
        $user->save();

        return AjaxCallbackMessage(Entity::loginSuccess($user), TRUE);
    }

    /**
     * 登陆注册v020500新增   数据中心版
     * @User yaokai
     * @param Request $request
     * @param null $version
     * @return string
     */
    public function postRegisteraccount(Request $request , $version = null)
    {
        $data = $request->input();

        //用户加密后的手机号
        $non_reversible = encryptTel($data['username']);

        User::where('non_reversible', $non_reversible)->update(['version'=>$version]);

        //初始化
        $versionService = $this->init(__METHOD__, $version);

        if($versionService){
            $response = $versionService->bootstrap($data, ['request' => $request]);
            return AjaxCallbackMessage($response['message'], $response['status']);
        }

        return AjaxCallbackMessage('api接口不再维护', false);

    }


    /**
     * @return string
     * 退出
     */
    public function postLogout(Request $request)
    {
        $uid=(int)$request->get('uid');
//        $uid = Auth::user()->uid;
        //将推送的cid改写成空
        if($uid>0){
            Entity::where('uid', $uid)->update(['platform' => 'other', 'identifier' => '']);
        }
//        Auth::logout();

        return AjaxCallbackMessage('退出成功', TRUE);
    }

    /**
     * 验证是否允许注册字段   --疑似弃用  不处理  yaokai
     * @User yaokai
     * @param Request $request
     * @return string
     */
    public function postAllowRegister(Request $request)
    {
        $field = (string)$request->input('field');
        $val = (string)$request->input('val');
        if (!in_array($field, ['nickname', 'username'], TRUE) || empty($val)) {
            return AjaxCallbackMessage('无法验证！', FALSE);
        }
        if ($request->has('username')){
            $username = $request->input('username');
            $return = (bool)!Entity::where($field, '=', $val)->where('username','<>',$username)->count();
        }else{
            $return = (bool)!Entity::where($field, '=', $val)->count();
        }

        return AjaxCallbackMessage($return, TRUE);
    }

    /**
     * 邀请注册    --疑似弃用   不处理  yaokai
     * @User yaokai
     * @param Request $request
     * @param null $version
     * @return string
     */
    public function postInviteregister(Request $request, $version = null)
    {

        if ($version && substr($version, 2) >= '020800') {
            $result = $request->input();

            //初始化
            $versionService = $this->init(__METHOD__, $version);

            if ($versionService) {
                $response = $versionService->bootstrap($result, ['request' => $request]);

                return AjaxCallbackMessage($response['message'], $response['status']);
            }
        }

        $username = trim($request->input('username'));
        $password = trim($request->input('password'));
        $nation_code = $request->input('nation_code', '86');
        if (!$username || !$password) {
            return AjaxCallbackMessage('账号密码不能为空', FALSE);
        }
        if (!checkMobile(trim($username), $nation_code)) {
            return AjaxCallbackMessage('手机号格式不对', FALSE, '');
        }
        $password = md5($password);
        $nickname = $request->input('nickname');
        if (!$nickname) {
            return AjaxCallbackMessage('昵称不能为空', FALSE);
        }
        if (Entity::getCount(['username' => $username])) {
            return AjaxCallbackMessage('账号已存在', FALSE);
        }
        if (Entity::getCount(['nickname' => $nickname])) {
            return AjaxCallbackMessage('昵称已存在', FALSE);
        }
        $captcha_id = $request->get('captcha_id', '');
        $captcha_value = $request->get('captcha_value', '');
        if ($captcha_id == '') {
            return AjaxCallbackMessage('验证码id未传', FALSE);
        } else {
            if (Cache::get($captcha_id) != $captcha_value) {
                return AjaxCallbackMessage('验证码错误', FALSE);
            }
        }
        $code = $request->get('code', '');
        if ($code == '') {
            return AjaxCallbackMessage('分享标识码未填', FALSE);
        }
        $share = Share::where('code', $code)->first();
        $user_share_id = isset($share) ? $share->id : 0;
        $share_user = Entity::where('uid', $share->uid)->get();
        $user = Entity::create(compact("username", "password", "nickname", "user_share_id") + ['source' => 5]);
        $new_uid = $user->uid;

        #############################################
        # 通过注册成功后的用户ID，用户名称获取token
        # 必须传参数：用户ID，用户名称，头像

        //发送注册成功消息
        $content['name'] = 'welcome';
        $content['tag'] = '';
        $type = 'receiveTick';

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
                    'is_share' => 'yes',
                    'status' => 1,
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
            $num = count($tickets);
            $activity = Activity::where('id', $activity_id)->first();
            //需要邀请人数等于已经邀请人数
            $needInviteNum = $activity->invite_num;
            $hasInvitedNum = \DB::table('user')->where('user_share_id', $user_share_id)->count();
            if ($hasOfflineTicket && $needInviteNum == $hasInvitedNum) {
                $content['name'] = 'obtainTicket';
                $content['tag'] = ['name'=>$activity->subject,'num'=>$num];
                $type = 'obtainTicket';
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

        return AjaxCallbackMessage(['score' => 100, 'nickname' => $user->nickname, 'uid' => $user->uid], TRUE);
    }

   /**
    * 作用:获取用户名和积分
    * 参数:
    *
    * 返回值:
    */
    public function postUserinfo(Request $request)
    {
        $uid = $request->get('uid', '');
        $user = DB::table('user')->where(
            [
                'uid' => $uid,
                'status' => 1,
            ]
        )->first();
        if (is_null($user)) {
            return AjaxCallbackMessage("用户不存在", TRUE);
        }

        return AjaxCallbackMessage(['nickname' => $user->nickname, 'score' => 100], TRUE);
    }

    /**
     * 作用：登入页获得口号
     *  返回值：口号
     */
    public function postSlogan(){
        $slogan = DB::table('config')
            ->where('code','login_slogan')
            ->select('value')
            ->get();
        return AjaxCallbackMessage($slogan,true);
    }

    /**
    *   作者：shiqy
    *   创作时间：2018/2/2 0002 上午 11:42
    *   功能描述：投资人400注册
    */
    public function postRegisterBy400(RegisterBy400Request $request, $version = null){
        //初始化
        $versionService = $this->init(__METHOD__, $version);

        if($versionService){
            $response = $versionService->bootstrap($request->all());
            return AjaxCallbackMessage($response['message'], $response['status']);
        }

        return AjaxCallbackMessage('api接口不再维护', false);
    }


}
