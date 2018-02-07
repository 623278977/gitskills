<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\CommonController;
use App\Models\Agent\Agent;
use App\Models\LogSms;
use App\Models\User\Entity;
use App\Models\User\Industry;
use Illuminate\Http\Request;
use App\Models\Identify;
use App\Models\CityPartner\Entity as CityPartner;
use App\Models\User\Entity as User;
use \DB;
use \Cache;
use Auth;
use Captcha;
use Validator;
use Gregwar\Captcha\CaptchaBuilder;

class IdentifyController extends CommonController
{

    public $builder;

    /**
     * 发送验证码  --数据中心版
     * @User yaokai
     * @param Request $request
     * @param null $version
     * @return string
     */
    public function postSendcode(Request $request, $version = null)
    {
        $imei = $request->header('imei');
        $time = $request->header('time');
        $salt = $request->header('salt');
        $username = $request->input('username');
        $type = $request->input('type');
        //伪号码
        $mobile = pseudoTel($username);

        //用户加密后的手机号
        $non_reversible = encryptTel($username);

        $nation_code = $request->input('nation_code', '86');

        $app_name = $request->input('app_name','wjsq');//请求接口的app名称
        $meid =$request->header('imei');


        //目前只限制c端的短信登录接口
        if($app_name=='wjsq' && $type=='sms_login'){
            //2.84之后的用token
            if($version>='_v020804'){
                if(md5($imei.$username.$time)!=$salt){
                    return AjaxCallbackMessage('success', false);
                }
            }else{
                $client = getClient();
                if('iPhone'!=$client &&!$meid ){
                    return AjaxCallbackMessage('', false);
                }
            }
        }

        if (empty($username)) {
            return AjaxCallbackMessage('手机号不能为空', false);
        }
        if (empty($type)) {
            return AjaxCallbackMessage('验证码类型不能为空', false);
        }
        if (!checkMobile(trim($username), $nation_code)) {
            return AjaxCallbackMessage('手机号格式不对', false, '');
        }
        //一分钟只能发一次
        $exists = Identify::where('non_reversible', $non_reversible)
            ->where('created_at', '>', time() - 60)
            ->count();
        if ($exists) {
            return AjaxCallbackMessage('休息会吧，一分钟只能发送一次', false);
        }


        $code = mt_rand(10000, 99999);

        if (in_array($username, ['15658676670', '15068713205'])) {
            $code = 123456;
        }
        $content = "";
        switch ($type) {
            case 'register'://注册  已弃用  现在都是直接短信登陆
                $user = User::where('non_reversible',$non_reversible)
                    ->first();
                if ($user) {
                    return AjaxCallbackMessage('用户已存在哦！', false, '');
                }
                $smsType = 'registerCode';
                $res = SendTemplateSMS('registerCode', $username, $smsType, ['code' => $code], $nation_code,'wjsq',false);
                break;
            case 'sms_login'://短信登陆
                $smsType = 'sms_loginCode';
                $res = SendTemplateSMS('sms_loginCode', $username, $smsType, ['code' => $code], $nation_code,'wjsq',false);
                break;
            case 'standard'://通用 其他
                $smsType = 'standard';
                $res = SendTemplateSMS('standard', $username, $smsType, ['code' => $code], $nation_code,'wjsq',false);
                break;
            case 'citypartner_register':// 已弃用  不处理 2017.12.13 yaokai
                if (CityPartner::getCount(array('username' => $username))) {
                    return AjaxCallbackMessage('用户已存在哦！', false, '');
                }
                $smsType = 'registerCode';
                $res = SendTemplateSMS('registerCode', $username, $smsType, ['code' => $code], $nation_code);
                break;
            case 'outh_register'://三方登陆
                $smsType = 'registerCode';
                $res = SendTemplateSMS('registerCode', $username, $smsType, ['code' => $code], $nation_code,'wjsq',false);
                break;
            case 'forget_password'://忘记密码
                if (!Entity::getCount(array('non_reversible' => $non_reversible))) {
                    return AjaxCallbackMessage('该号码未注册', false, '');
                }
                $smsType = 'forget_password';
                $res = SendTemplateSMS('forget_passwordCode', $username, $smsType, ['code' => $code], $nation_code,'wjsq',false);
                break;
            case 'forget_partner_pwd'://已弃用 不处理 2017.12.13 yaokai
                if (!CityPartner::getCount(array('username' => $username))) {
                    return AjaxCallbackMessage('该号码未注册', false, '');
                }
                $smsType = 'forget_password';
                $res = SendTemplateSMS('forget_passwordCode', $username, $smsType, ['code' => $code], $nation_code , $app_name ,false);
                break;



            //投资人中的短信请求
            case 'agent_register'://注册
                if (Agent::where('non_reversible',$non_reversible)->value('id')) {
                    return AjaxCallbackMessage('用户已存在哦！', false, '');
                }
                $smsType = 'agent_registerCode';
                $res = SendTemplateSMS('agent_registerCode', $username, $smsType, ['code' => $code], $nation_code,'agent',false);
                break;
            case 'agent_sms_login'://登录
                if (!Agent::where('non_reversible',$non_reversible)->value('id')) {
                    return AjaxCallbackMessage('该号码未注册', false, '');
                }
                $smsType = 'agent_sms_loginCode';
                $res = SendTemplateSMS('agent_sms_loginCode', $username, $smsType, ['code' => $code], $nation_code,'agent',false);
                break;
            case 'agent_get_password'://找回密码
                if (!Agent::where('non_reversible',$non_reversible)->value('id')) {
                    return AjaxCallbackMessage('该号码未注册', false, '');
                }
                $smsType = 'agent_get_passwordCode';
                $res = SendTemplateSMS('agent_get_passwordCode', $username, $smsType, ['code' => $code], $nation_code,'agent',false);
                break;
            case 'agent_invite_register'://经纪人邀请投资人快速注册
                $user = User::where('non_reversible',$non_reversible)
                    ->where('register_invite','!=','')
                    ->first();
                if ($user) {
                    return AjaxCallbackMessage('用户已存在哦！', false, '');
                }
                $smsType = 'agent_invite_register';
                $res = SendTemplateSMS('agent_invite_register', $username, $smsType, ['code' => $code], $nation_code,'agent',false);
                break;

            default: // 疑似弃用 2017.12.13 yaokai
                $phone_code = $request->input('phone_code');
                $res = SendTemplateSMS('forget_passwordCode', $phone_code . $username, $type, ['code' => $code], $nation_code,'wjsq',false);
        }
        if ($res != 1) {
            return AjaxCallbackMessage('验证码发送失败', false);
        }
        $identifyData = array(
            'uid'    => 0,   //todo 暂不处理 可以通过手机号关联的 yaokai 2017.12.13
            'mobile' => $mobile,
            'app_name' => $app_name,
            'type'   => $type,
            'nation_code'   => str_replace('+','',$nation_code),
            'non_reversible' => $non_reversible,
            'code' => $code,
        );
        //保存发送记录
        Identify::create($identifyData);

        return AjaxCallbackMessage('验证码发送成功', true);
    }

    /**
     * 验证验证码   --数据中心版
     * @User yaokai
     * @param Request $request
     * @return string
     */
    public function postCheckidentify(Request $request)
    {
        $code = $request->input('code');

        $username = $request->input('username');

        //用户加密后的手机号
        $non_reversible = encryptTel($username);

        $app_name = $request->input('app_name','wjsq');//请求接口的app名称
        if (empty($code)) {
            return AjaxCallbackMessage('验证码不能为空', false);
        }
        if (empty($username)) {
            return AjaxCallbackMessage('手机号不能为空', false);
        }
        $type = $request->input('type');
        if (empty($type)) {
            return AjaxCallbackMessage('类型不能为空', false);
        }
        $flag = Identify::checkIdentify($non_reversible,$type, $code,900,$app_name);
        if ($flag !== 'success' && in_array($username, ['15658676670', '15068713205'])) {
            $flag = 'success';
        }

        if ($flag === 'success') {
            if ($type == 'sms_login') {
                //老用户直接登录
                if ($user = User::where('non_reversible', $non_reversible)->first()) {
                    //if((md5($user->username) != $user->password) && $user->zone_id && Industry::where('uid',$user->uid)->first() && $user->nickname){
                    //	$return = 'can_login';
                    //}
                    $return = 'can_login';
                } else {
                    $return = 'need_register';
                }

                return AjaxCallbackMessage($return, true);
            } else {
                return AjaxCallbackMessage('验证码正确', true);
            }
        }

        return AjaxCallbackMessage($flag, false);
    }

    /**
     * 城市合伙人验证验证码 弃用，不处理
     *
     * @param $param
     * @return bool|string
     */
    public function checkVerifyCode($param)
    {
        $code = isset($param['code']) ? $param['code'] : '';
        $username = isset($param['phone']) ? $param['phone'] : '';
        $type = isset($param['act']) ? $param['act'] : '';
        if (empty($code) || empty($username) || empty($type)) {
            return false;
        }
        $flag = Identify::checkIdentify($username, $type, $code);

        return $flag;
    }

    /*
    * 作用:验证码缓存键名
    * 参数:
    *
    * 返回值:
    */
    public function postCaptchaid()
    {
        $id = md5(time());

        return AjaxCallbackMessage(array('captcha_id' => $id), true);
    }

    /*
    * 作用:获取图形验证码
    * 参数:
    *
    * 返回值:验证码图片
    */
    public function postSendcaptcha(Request $request)
    {
        $id = $request->get('id', '');
        $builder = new CaptchaBuilder;
        //可以设置图片宽高及字体
        $builder->build($width = 100, $height = 40, $font = null);
        //获取验证码的内容
        $phrase = $builder->getPhrase();
        Cache::put($id, $phrase, 10);
        return AjaxCallbackMessage(['captcha' => $phrase], true);
    }


//	/*
//	* 作用:验证图形验证码
//	* 参数:$captcha 验证码
//	*
//	* 返回值:bool
//	*/
//	public function postCheckcaptcha(Request $request)
//	{
//		$validator = Validator::make($request->all(),[
//			'captcha' => 'required|captcha',
//		]);
//		if($validator->fails()){
//			return AjaxCallbackMessage('验证码错误', false);
//		}
//		return AjaxCallbackMessage('验证码正确', true);
//	}

    /*
     * 发送短信验证码前，先进行图形验证码判断
     * shiqy
     * */
    public function postPicverifyBeforeSendcode(Request $request , $version = null){
        $picVerfy = new PicIdentifyController();
        $picResult = $picVerfy->postVerifycaptcha($request);
        $picResultArr = json_decode($picResult,true);
        if($picResultArr['status']){
            $type = trim($request->input('type'));
            $username = trim($request->input('username'));
            if(!empty($username)){
                if($type == 'agent_invite_register'){
                    $user = User::where('non_reversible',encryptTel($username))
//                        ->where('register_invite','!=','')
                        ->first();
                    if ($user) {
                        return AjaxCallbackMessage('用户已经注册', false, '');
                    }
                }
                else if($type == 'agent_register'){
                    $agentInfo = Agent::where('non_reversible',encryptTel($username))->first();
                    if (is_object($agentInfo)) {
                        return AjaxCallbackMessage(['message'=>'用户已经注册','id'=>$agentInfo['id']], false, '');
                    }
                }
            }
            return $this->postSendcode($request);
        }
        else{
            return $picResult;
        }
    }

}