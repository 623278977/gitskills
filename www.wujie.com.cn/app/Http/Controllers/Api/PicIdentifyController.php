<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\CommonController;
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

class PicIdentifyController extends CommonController
{
    /**
     * 作用:获取图形验证码    --不用处理  不影响数据中心
     * 参数:
     *
     * 返回值:验证码图片
     */
    public function anyPiccaptcha(Request $request)
    {
        $builder = new CaptchaBuilder;
        $this->builder = $builder;
        $width = $request->get('width', 100);
        $height = $request->get('height', 40);
        $platform = $request->get('platform', 'web');
        //可以设置图片宽高及字体
        $builder->build($width, $height, $font = null);
        //获取验证码的内容
        $phrase = $builder->getPhrase();

        if($platform=='web'){
            //把内容存入session
            $res = \Session::put('wjsqcaptcha', $phrase);
        }else{
            $username = $request->get('username');
            if(!$username){
                return AjaxCallbackMessage('缺少参数username', false);
            }
            Cache::put($username, $phrase, 10);
        }
        

        $res = Cache::get($username);

        //生成图片
        header("Cache-Control: no-cache, must-revalidate");
        header('Content-Type: image/jpeg');
        $builder->output();
    }



    /*
    * 作用:验证图形验证码
    * 参数:
    *
    * 返回值:验证码图片
    */
    public function postVerifycaptcha(Request $request)
    {
        $userInput = trim($request->get('captcha'));
        if(empty($userInput)){
            return AjaxCallbackMessage('请输入图形验证码', false);
        }
        else if (\Session::get('wjsqcaptcha') == $userInput) {
            return AjaxCallbackMessage('验证码正确', true);
        }
        else {
            return AjaxCallbackMessage('图形验证码错误', false);
        }
    }


}