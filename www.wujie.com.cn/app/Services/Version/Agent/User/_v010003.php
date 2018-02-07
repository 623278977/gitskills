<?php namespace App\Services\Version\Agent\User;

use App\Services\Version\VersionSelect;
use DB;
use Validator;
use App\Models\Agent\Agent;


class _v010003 extends _v010002
{

    /*
     * 被邀请的情况下注册成经纪人
     * 功能添加：添加push通知
     * shiqy
     * */
    public function postAgentRegister($input){
        empty($input['phone_code']) &&  $input['phone_code'] = '86';
        if(empty($input['code'])){
            return ['message'=>'验证码不能为空','status'=>false];
        }
        $password = empty($input['password'])? '' : trim($input['password']) ;
        $checkPass = checkPassword($password);
        if(!$checkPass['status']){
            return $checkPass;
        }
        if(!empty(strcmp($input['password'],$input['password_confirmation']))){
            return ['message'=>'确认密码错误','status'=>false];
        }
        //判断是否能注册
        $result = $this->postIsregister(['phone'=>$input['username'] , 'type'=>1, 'phone_code'=>$input['phone_code'] ]);
        if($result['status'] == false){
            return $result ;
        }
        return  parent::postAgentRegister($input);
    }

    /*
     * 被邀请的情况下注册成投资人
     * shiqy
     * 添加：给经纪人发送通知
     * */
    public function postCustomerRegister($input){
        //判断是否能注册
        empty($input['phone_code']) &&  $input['phone_code'] = '86';
        if(empty($input['code'])){
            return ['message'=>'验证码不能为空','status'=>false];
        }
        $result = $this->postIsregister(['phone'=>$input['username'] , 'type'=>2 , 'phone_code'=>$input['phone_code']]);
        if($result['status'] == false){
            return $result ;
        }
        return parent::postCustomerRegister($input);
    }

}