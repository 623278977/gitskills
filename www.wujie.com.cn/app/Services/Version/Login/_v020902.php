<?php

namespace App\Services\Version\Login;

use App\Models\Brand\BrandContactor;
use App\Models\Identify;
use App\Models\User\Entity as User;
use App\Services\Version\Agent\User\_v010000;


class _v020902 extends _v020800
{

    public function __construct($controllerName, $controllerMethod)
    {
        parent::__construct($controllerName, $controllerMethod);
    }

    /**
    *   作者：shiqy
    *   创作时间：2018/2/2 0002 上午 11:40
    *   功能描述：投资人400注册
    */
    public function postRegisterBy400($input){
        try{
            $phone = trim($input['username']);
            $nation_code = trim($input['nation_code']);
            $type = trim($input['type']);
            $code = trim($input['code']);

            //验证是否能够注册
            $isRegister = modelFactory(_v010000::class)->postIsregister(['phone'=>$phone , 'type'=>2, 'phone_code'=>$nation_code ]);
            if(!$isRegister['status']){
                throw new \Exception($isRegister['message']);
            }
            //md5加密手机号  生成唯一标识
            $non_reversible = encryptTel($phone);
            //伪号码
            $username = pseudoTel($phone);

            $indentResult = Identify::checkIdentify($non_reversible, $type, $code, $time = 900);
            if ($indentResult != 'success') {
                throw new \Exception($indentResult);
            }

            $nickname = $this->services->generateUniqueNickname(substr($phone, -4) . substr(uniqid(), -4), $phone);
            $password = md5($phone);
            $source = 6;
            //沉淀
            depositTel($phone, $non_reversible, 'wjsq', $nation_code);
            //分配商务
            $contactor = BrandContactor::selectContactor();
            $register_invite = trim($contactor['non_reversible']);
            $user = User::create(compact("username", 'non_reversible', "password", "nickname", 'nation_code','source','register_invite'));
            if(!is_object($user)){
                throw new \Exception('数据存储错误！');
            }
            return ['message'=>'注册成功', 'status'=>true ];
        }catch (\Exception $e){
            return ['message'=>$e->getMessage() , 'status'=>false];
        }


    }

}