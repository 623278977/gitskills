<?php
namespace App\Services\Version\Agent\Inspector;


use App\Models\Agent\Score\AgentScoreLog;
use App\Models\Message;
use App\Services\Version\VersionSelect;
use Validator;
use App\Models\Agent\Agent;
use App\Models\Agent\AgentBrand;
use Hash;
use App\Models\Agent\AgentCategory;
use App\Models\Agent\AgentAchievement;
use App\Models\Agent\AgentLevel;
use App\Models\Identify as duanIdentify;
use App\Models\Zone\Entity as Zone;
use App\Models\Agent\Entity\_v010200 as Agentv010200;

class _v010001 extends _v010000{

    /**
     * 注册   --数据中心版
     * @User
     * @param $input
     * @return array
     */
    public function postRegister($input){
        $submitFlag=trim($input['submit_flag']);
        //手机号
        $tel = $input['username'];
        //伪号码
        $username = pseudoTel($tel);
        //md5加盐后的注册号码
        $non_reversible = encryptTel($tel);
        //邀请号码
        $inviter_phone = $input['inviter_phone'];
        //md5加盐后的邀请号码
        $inviter_tel = encryptTel($inviter_phone);
        if(empty($submitFlag)){
            return ['message'=>"请填写调用接口标志" ,'status'=>false];
        }
        if($submitFlag=='first'){
            $input['password_confirmation'] = $input['confirm_password'];
            $validator = \Validator::make($input,[
                'code' => 'required',
                'password' => 'required|confirmed',
                'username' => 'required',
                'inviter_phone' => 'required',
            ]);
            if($validator->fails()){
                $warnings = $validator->messages();
                $show_warning = $warnings->first();
                return ['message'=>$show_warning ,'status'=>false];
            }
            $checkPass = checkPassword($input['password']);
            if(!$checkPass['status']){
                return $checkPass;
            }
            $input['nation_code'] = empty($input['nation_code'])? '86' : trim($input['nation_code']) ;
            if (!checkMobile(trim($input['username']),$input['nation_code'])) {
                return ['message'=>"手机号格式不对" ,'status'=>false];
            }
            $rel=Agent::where("non_reversible",$non_reversible)->first();
            if(is_object($rel)){
                return ['message'=>"该手机号已注册，不能重复注册" ,'status'=>false];
            }
            $inviterInfo = Agent::where(function ($query) use($inviter_tel,$inviter_phone){
                return $query->where('non_reversible',$inviter_tel)->orWhere('my_invite',$inviter_phone);
            })->where('status', 1)->where('is_verified', 1)->first();
            if(!is_object($inviterInfo)){
                return ['message'=>"该邀请人无效" ,'status'=>false];
            }
            //如果是6位邀请码，就把邀请人手机号码作为邀请码
            if(strlen($input['inviter_phone']) == 6){
                $input['inviter_phone'] = trim($inviterInfo['non_reversible']);
            }

            $checkResult=duanIdentify::checkIdentify($non_reversible,'agent_register',$input['code'],$time=900,'agent');
            //验收环境任意验证码都能通过
            if (app()->environment() === 'beta') {
                $checkResult = 'success';
            }
            if($checkResult!='success'){
                return ['message'=>"验证码错误" ,'status'=>false];
            }
            $inviteNum  = Agent::createInviteNum(Agent::class,'my_invite');

            $nickname = 'a'.time();
            $data=array(
                "username"=> $username,
                "non_reversible"=> $non_reversible,
                "password"=>Hash::make($input['password']),
                "register_invite"=>$inviter_tel,
                'nickname' => $nickname,
                "my_invite" => $inviteNum,
                'agent_level_id' => 1,
                'nation_code'=> trim($input['nation_code']),
            );
            $agentObj = Agent::create($data);
            //修改短信日志中的明文手机号
//            \DB::table('log_sms')->where('non_reversible',$non_reversible)->where('phone', '<>' ,$username)->update(['phone'=> $username]);

            //给积分
            Agentv010200::add($inviterInfo->id, AgentScoreLog::$TYPES_SCORE[13], 13, '发展团队', $agentObj->id, 1);

            if(!is_object($agentObj)){
                return ['message'=>"保存数据失败" ,'status'=>false];
            }
            $rel = $agentObj->id;

            //生成token
            $user_token = GainToken('agent'.$rel, $agentObj['nickname'], '');
            Agent::where('id', $rel)->update(['token' => $user_token]);

            Agent::instance()->sendInfo($input['username'], $rel);
            $data = [];
            $data['agent_id'] = $rel;
            $data['nickname'] = $nickname;

            return ['message'=>$data ,'status'=>true];
        }
        else if($submitFlag=='end'){
            $agentId=intval($input['agent_id']);
            $isSkip=intval($input['is_skip']);
            if(empty($agentId)){
                return ['message'=>"请传递经纪人id" ,'status'=>false];
            }
            if(!in_array($isSkip,[0,1])){
                return ['message'=>"请输入正确的is_skip值" ,'status'=>false];
            }
            $agentInfo=Agent::where("id",$agentId)->where('status','<>',-1)->first();
            if(!is_object($agentInfo)){
                return ['message'=>"请输入有效的经纪人id" ,'status'=>false];
            }
            if(!$isSkip){
                $authResult = Agent::saveAuthInfo($input);
                if(isset($authResult['error'])){
                    return ['message'=>$authResult['message'] ,'status'=>false];
                }
            }
            if($input['platform'] != 'ios' ){
                Agent::where('id',$agentId)->increment('login_count',1,['last_login'=>time()]);
            }
            $data=[];
            $data=self::afterLoginBack('',$agentId);
            //注册成功发送推送
            try{
                $res = send_transmission(json_encode(['type'=>'new_message', 'style'=>'json',
                    'value'=>
                        [
                            'title'=>'从今天起，你就是无界商圈的专业经纪人',
                            'sendTime'=>time(),
                        ]
                ]),
                    $agentInfo,null, 1);
            }catch (\Exception $e){
                return ['message'=>$e->getMessage() ,'status'=>false];
            }
            return ['message'=>$data ,'status'=>true];
        }
    }

    /**
     * 登录   --数据中心版
     * @User
     * @param $input
     * @return array
     */
    public function postLogin($input)
    {
        $loginWay = trim($input["login_way"]);
        //手机号
        $tel = trim($input['username']);
        //md5加盐后的注册号码
        $non_reversible = encryptTel($tel);
        if (empty($tel)) {
            return ['message' => "手机号码不能为空", 'status' => false];
        }
        $userInfo = Agent::where("non_reversible", $non_reversible)->where('status', 1)->first();
        if (!is_object($userInfo)) {
            return ['message' => "无效的用户名", 'status' => false];
        }
        if ($loginWay == "note_login") {
            $code = trim($input['code']);
            $checkResult = duanIdentify::checkIdentify($non_reversible, 'agent_sms_login', $code, $time = 900, 'agent');
            if ($checkResult != 'success') {
                return ['message' => $checkResult, 'status' => false];
            }
        } else if ($loginWay == "pwd_login") {
            $password = trim($input['password']);
            if (!Hash::check($password, $userInfo["password"])) {
                return ['message' => "密码错误", 'status' => false];
            }
            $type = intval($input['type']);
            if (!in_array($type, [0, 1])) {
                return ['message' => "请输入正确的type参数", 'status' => false];
            }
            if ($type) {
                $code = trim($input['code']);
                $oldCode = \Cache::get($tel);
//                $ss[] = $code;
//                $ss[] = $oldCode;
                if ($oldCode != $code) {
                    return ['message' => "验证码错误", 'status' => false];
//                    return AjaxCallbackMessage( $ss, false);
                }
                \Cache::forget($tel);
            }
        }
        if ($loginWay != 'fast_login') {
            Agent::where("non_reversible", $non_reversible)->update(['is_online' => 1]);
        }

        Agent::where("non_reversible", $non_reversible)->increment('login_count', 1, ['last_login' => time()]);
        $data = [];
        $data = self::afterLoginBack($non_reversible, '');
        $data['is_first_login'] = '0';
        if (intval($userInfo['login_count']) == 0) {
            $data['is_first_login'] = '1';
        }
        Agent::createLog($userInfo);

        return ['message'=>$data ,'status'=>true];
    }


    /*
     * 登录后返回信息
     * 根据手机号md5加盐值或者agentid，两者只能有一个，另一个为“”
     * */
    protected static function afterLoginBack($non_reversible,$agentId){
        if(!empty($non_reversible)){
            $userInfo=Agent::where("non_reversible", $non_reversible)->first();
        }
        if(!empty($agentId)){
            $userInfo=Agent::where("id", $agentId)->first();
        }
        if(!is_object($userInfo)){
            return [];
        }
        $firstcharter="";
        $zone_name="";
        $zone_id=intval($userInfo["zone_id"]);
        if($zone_id){
            $zoneInfo=Zone::where("id",$zone_id)->first();
            $zone_name=$zoneInfo["name"];
        }
        $agentBrandInfos=AgentBrand::where("agent_id",$userInfo["id"])->get();
        $is_brand=0;
        $brand_name=array();
        if(!empty($agentBrandInfos->items)){
            $is_brand==1;
            $brand_name=$agentBrandInfos;
        }
        $agentCategoryInfo = AgentCategory::with('categorys')
            ->where('agent_id',$userInfo["id"])->get();
        $agentCategoryArr = [];
        foreach ($agentCategoryInfo as $oneCategory){
            $agentCategoryArr[] = [
                'id'=> trim($oneCategory['categorys']['id']),
                'name'=> trim($oneCategory['categorys']['name']),
            ];
        }
        //总成单数
        $agentTotalOrder=trim(AgentAchievement::where("agent_id",$userInfo["id"])->sum('total_achievement'));

        $birthday=trim($userInfo["birth"]);
        $data=array(
            "agent_id"=>$userInfo["id"],
            "token"=>trim($userInfo["token"]),
            "username"=> getRealTel($userInfo['non_reversible'] , 'agent'),
            "nickname"=>$userInfo["nickname"],
            "is_online"=>$userInfo["is_online"],
            "avatar"=> getImage($userInfo["avatar"]),
            "firstcharter"=>$firstcharter,
            "gender"=>$userInfo["gender"],
            "realname"=>$userInfo["realname"],
            "signature"=>empty($userInfo["sign"])? Agent::getSign() : trim($userInfo['sign']) ,
            "zone_id" => empty($userInfo["zone_id"])? '' : trim($userInfo["zone_id"]),
            "zone"=>$zone_name,
            "is_brand"=>$is_brand,
            "brand_name"=>$brand_name,
            "total_orders" => $agentTotalOrder,
            "industry"=>$agentCategoryArr,
            "level" => AgentLevel::getLevelName(intval($userInfo['agent_level_id'])),
            "birth"=>$birthday,
            "edu"=>$userInfo["diploma"],
            "earning"=>$userInfo["earning"],
            "profession"=>$userInfo["profession"],
            "qcode"=>$userInfo["qcode"],
            "identity_card"=> idCardEncrypt($userInfo["identity_card"]),
            "email"=>$userInfo["email"],
            "is_public_realname" => trim($userInfo["is_public_realname"]),
            'auth_status'=> trim($userInfo['is_verified']),
        );

        $agent_result     = Agent::where('id', $userInfo["id"])->first();
        //如果is_first_enter = 0: 表示不是； 等于1: 表示是。
        if ($agent_result->is_fist_page_shown == 0) {
            //是否已完善资料
            $complete = Agentv010200::isComplete($userInfo["id"]);
            if($complete){
                //给积分
                Agentv010200::add($userInfo["id"], AgentScoreLog::$TYPES_SCORE[17], 17, '完善个人资料', 0, 1, 1);
            }
        }

        if(empty($data['token']) && !empty($data['nickname'])){
            try{
                $user_token = GainToken('agent'. $data['agent_id'], $data['nickname'], $data['avatar']);
                Agent::where('id', $data['agent_id'])->update(['token' => $user_token]);
                $data['token'] = $user_token;
            }catch (\Exception $e){
                return ['message'=>$e->getMessage() ,'status'=>false];
            }
        }

        return $data;
    }

    //保存认证信息  shiqy
    public function postSaveAuthInfo($input){
        $saveResult = Agent::saveAuthInfo($input);
        if(isset($saveResult['error'])){
            return ['message' => $saveResult['message'] ,'status'=>false];
        }
        return ['message' => '认证成功' ,'status'=>true];
    }

    /**
     * 找回密码  --数据中心版
     * @User
     * @param $input
     * @return array
     */
    public function postRetrieve($input){
        $validator = \Validator::make($input,[
            'username' => 'required',
            'password' => 'required',
            'type' => 'in:find,reset',
            'old_password' => 'required_if:type,reset',
        ]);
        if($validator->fails()){
            $warnings = $validator->messages();
            $show_warning = $warnings->first();
            return ['message'=>$show_warning ,'status'=>false];
        }
        $username = trim($input['username']);
        //md5加盐后的注册号码
        $non_reversible = encryptTel($username);
        $agentInfo = Agent::where('non_reversible',$non_reversible)->where('status',1)->first();
        if(!is_object($agentInfo)){
            return ['message'=>"经纪人账号无效" ,'status'=>false];
        }

        $type  = trim($input['type']);
        if($type == 'find'){
//            $code  = trim($input['code']);
//            $auType  = trim($input['au_type']);
//            $checkResult=duanIdentify::checkIdentify($username,$auType,$code,$time=900,'agent');
//            if($checkResult!='success'){
            //              return ['message'=>"验证码错误" ,'status'=>false];
//            }
        }
        else{
            $oldPassword  = trim($input['old_password']);
            if(!Hash::check($oldPassword,$agentInfo['password'])){
                return ['message'=>"密码错误" ,'status'=>false];
            }
        }
        $password=trim($input['password']);
        Agent::where("non_reversible",$non_reversible)->update(['password'=> Hash::make($password)]);
        return ['message'=>"修改成功" ,'status'=>true];
    }

}