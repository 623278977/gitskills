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

class _v010000 extends VersionSelect{

    //经纪人注册成功欢迎页面  的接口
    //shiqy
    public function postMessageBack($input, $datas = []) {
        $validator = Validator::make($input,[
            'agent_id' => 'required|exists:agent,id',
        ]);
        if($validator->fails()){
            $warnings = $validator->messages();
            $show_warning = $warnings->first();
            return ['message'=>$show_warning ,'status'=>false];
        }
        $agentInfo = Agent::where('id',$input['agent_id'])->first()->toArray();
        $data = [];
        $arr = [];
        //首次注册成功
        $arr['time'] = trim($agentInfo['created_at']);
        $arr['type'] = 'first_register';
        $arr['cont'] = array(
            'realname' => trim($agentInfo['nickname']),
            'new_id' => '257',
        );
        $data[] = $arr;

        //返回代理成功第一个品牌的数据
        $arr = [];
        $agentBrandInfo = AgentBrand::with('brand')
            ->where('agent_id',$input['agent_id'])
            ->where('status',4)
            ->orderBy('updated_at','asc')
            ->first();
        if(is_object($agentBrandInfo)){
            $arr['time'] = trim(strtotime($agentBrandInfo['updated_at']));
            $arr['type'] = 'brand_success';
            $arr['cont'] = array(
                'brand_name' => trim($agentBrandInfo['brand']['name']),
                'brand_slogan' => trim($agentBrandInfo['brand']['slogan']),
                'new_id' => '257',
            );
            $data[] = $arr;
        }

        ######### 后台自定义通知消息显示--zhaoyf ##########
        $inform_result_data = Message::GainAgentInformInfo($input['agent_id']);
        if($inform_result_data) {
            $_array = [];
            foreach ($inform_result_data as $key => $vls) {
                $_array['time'] = $vls->created_at->getTimestamp();
                $_array['type'] = 'my_message';
                $_array['cont'] = [
                    'title'    => trim($vls->title),
                    'content'  => trim(preg_replace('#<\w+(\s+.*?)?>|</\w+>|&nbsp;#', '', $vls->content)),
                    'url'      => trim($vls->url),
                    'image'    => "",
                ];
                $data[] = $_array;
            }
        }
        ################# end #########################

        //兼容迭代数据
        if ($datas) {
            $confirm_data = array_merge($data, $datas);
        } else {
            $confirm_data = $data;
        }

        $data = collect($confirm_data)->sortByDesc(function($item){
            return $item['time'];
        })->groupBy(function($item){
            return trim(date('m/d',$item['time']));
        })->map(function($item , $key){
            $box['confirm_day'] = $key;
            $box['result'] = $item;
            return $box;
        })->values();
        return ['message'=>$data ,'status'=>true];
    }


    /**
     * 注册  --数据中心版
     * @User
     * @param $input
     * @return array
     */
    public function postRegister($input)
    {
        $submitFlag = trim($input['submit_flag']);
        if (empty($submitFlag)) {
            return ['message' => "请填写调用接口标志", 'status' => false];
        }

        //手机号
        $tel = trim($input['username']);
        //邀请号码
        $inviter_phone = trim($input['inviter_phone']);

        //伪号码
        $username = pseudoTel($tel);
        //md5加盐后的注册号码
        $non_reversible = encryptTel($tel);
        //md5加盐后的邀请号码
        $inviter_tel = encryptTel($inviter_phone);

        if ($submitFlag == 'first') {
            $code = trim($input['code']);
            $password = trim($input['password']);
            $confirm_password = trim($input['confirm_password']);

            if (empty($tel)) {
                return ['message' => "手机号码不能为空", 'status' => false];
            }
            if (!checkMobile(trim($tel))) {
                return ['message' => "手机号格式不对", 'status' => false];
            }
            $rel = Agent::where("non_reversible", $non_reversible)->first();
            if (is_object($rel)) {
                return ['message' => "该手机号已注册，请更换手机号", 'status' => false];
            }
            if (empty($password)) {
                return ['message' => "密码不能为空", 'status' => false];
            }
            if ($password != $confirm_password) {
                return ['message' => "两个密码不一致", 'status' => false];
            }
            if (empty($inviter_phone)) {
                return ['message' => "推荐人号码不能为空", 'status' => false];
            }
            if (!checkMobile(trim($inviter_phone))) {
                return ['message' => "推荐人手机号格式不对", 'status' => false];
            }

            $is_have = Agent::where(function ($query) use ($inviter_tel,$inviter_phone) {
                $query->where('non_reversible', $inviter_tel)->orWhere('my_invite', $inviter_phone);
            })->where('status', 1)->where('is_verified', 1)->first();


            if (!is_object($is_have)) {
                return ['message' => "该推荐人不存在", 'status' => false];
            }
            if ($inviter_phone == $tel) {
                return ['message' => "推荐人不能重复注册", 'status' => false];
            }
            $checkResult = duanIdentify::checkIdentify($non_reversible, 'agent_register', $code, $time = 900, 'agent');
            if ($checkResult != 'success') {
                return ['message' => "验证码错误", 'status' => false];
            }

            $data = array(
                "username" => $username,
                "non_reversible" => $non_reversible,
                "password" => Hash::make($password),
                "register_invite" => $inviter_tel,
                'agent_level_id' => 1,
            );
            $agentObj = Agent::create($data);

            //给积分
            Agentv010200::add($is_have->id, AgentScoreLog::$TYPES_SCORE[13], 13, '发展团队', $agentObj->id, 1);
            if(!is_object($agentObj)){
                return ['message'=>"保存数据失败" ,'status'=>false];
            }
            $rel = $agentObj->id;
            Agent::instance()->sendInfo($tel, $rel);
            return ['message' => $rel, 'status' => true];
        } else if ($submitFlag == 'end') {
            $agentId = intval($input['agent_id']);
            if (empty($agentId)) {
                return ['message' => "请传递经纪人id", 'status' => false];
            }
            $agentInfo = Agent::where("id", $agentId)->where('status', '<>', -1)->first();
            if (!is_object($agentInfo)) {
                return ['message' => "请输入有效的经纪人id", 'status' => false];
            }
            $idcardFrontUrl = trim($input['identity_card_front']);
            $idcardBackUrl = trim($input['identity_card_reverse']);
            $gender = intval($input['gender']);
            $cardId = trim($input['identity_card']);
            $realname = trim($input['realname']);
            $birth = trim($input['birth']);
            if (!($idcardFrontUrl && $idcardBackUrl)) {
                return ['message' => "身份证正反面不能为空", 'status' => false];
            }
            $agentCard = Agent::where('identity_card', $cardId)->first();
            if (is_object($agentCard)) {
                return ['message' => "该身份证已注册过，请更换身份证", 'status' => false];
            }
            $data = array(
                'identity_card_front' => $idcardFrontUrl,
                'identity_card_reverse' => $idcardBackUrl,
                'gender' => $gender,
                'identity_card' => $cardId,
                'realname' => $realname,
                'birth' => $birth,
                'last_login' => time()
            );
            $rel=Agent::where("id",$agentId)->increment('login_count',1,$data);
            $data=[];
            $data=self::afterLoginBack('',$agentId);

            //注册成功发送推送
            $agentInfo = Agent::where("id", $agentId)->where('status', '<>', -1)->first();
            try {
                $res = send_transmission(json_encode(['type' => 'new_message', 'style' => 'json',
                    'value' =>
                        [
                            'title' => '从今天起，你就是无界商圈的专业经纪人',
                            'sendTime' => time(),
                        ]
                ]),
                    $agentInfo, null, 1);

            } catch (\Exception $e) {
                return ['message' => $e->getMessage(), 'status' => false];
            }
            return ['message' => $data, 'status' => true];
        }
    }

    /**
     * 登录    --数据中心版
     * @User shiqy
     * @param $input
     * @return array
     */
    public function postLogin($input){
        $loginWay=trim($input["login_way"]);
        //手机号
        $tel = trim($input['username']);

        //md5加盐后的注册号码
        $non_reversible = encryptTel($tel);
        if(empty($tel)){
            return ['message'=>"手机号码不能为空" ,'status'=>false];
        }
        if (!checkMobile(trim($tel))) {
            return ['message'=>"手机号格式不对" ,'status'=>false];
        }
        $userInfo=Agent::where("non_reversible",$non_reversible)->where('status',1)->first();
        if(!is_object($userInfo)){
            return ['message'=>"无效的用户名" ,'status'=>false];
        }
        if($loginWay=="note_login"){
            $code=trim($input['code']);
            $checkResult=duanIdentify::checkIdentify($non_reversible,'agent_sms_login',$code,$time=900,'agent');
            if($checkResult!='success'){
                return ['message'=>$checkResult ,'status'=>false];
            }
        }
        else if($loginWay=="pwd_login"){
            $password=trim($input['password']);
            if(!Hash::check($password,$userInfo["password"])){
                return ['message'=>"密码错误" ,'status'=>false];
            }
            $type=intval($input['type']);
            if(!in_array($type,[0,1])){
                return ['message'=>"请输入正确的type参数" ,'status'=>false];
            }
            if($type){
                $code=trim($input['code']);
                $oldCode = \Cache::get($tel);
                if($oldCode!=$code){
                    return ['message'=>"验证码错误" ,'status'=>false];
                }
                \Cache::forget($tel);
            }
        }
        if($loginWay != 'fast_login'){
            Agent::where("non_reversible",$non_reversible)->update(['is_online'=>1]);
        }
        Agent::where("non_reversible",$non_reversible)->increment('login_count',1,['last_login'=>time()]);
        $data=[];
        $data=self::afterLoginBack($non_reversible,'');
        Agent::createLog($userInfo);
        return ['message'=>$data ,'status'=>true];
    }

    /**
     * 找回密码  --数据中心版
     * @User
     * @param $input
     * @return array
     */
    public function postRetrieve($input){
        $validator = \Validator::make($input,[
            'username' => 'required|exists:agent,username',
            'password' => 'required',
        ]);
        if($validator->fails()){
            $warnings = $validator->messages();
            $show_warning = $warnings->first();
            return ['message'=>$show_warning ,'status'=>false];
        }
        $username = trim($input['username']);

        //md5加盐后的注册号码
        $non_reversible = encryptTel($username);
        $password=trim($input['password']);
        Agent::where("non_reversible",$non_reversible)->update(['password'=> Hash::make($password)]);
        return ['message'=>"修改成功" ,'status'=>true];
    }


    /*
     * 登录后返回信息
     * 根据手机号md5或者agentid，两者只能有一个，另一个为“”
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
            "username"=>$userInfo["username"],
            "nickname"=>$userInfo["nickname"],
            "is_online"=>$userInfo["is_online"],
            "avatar"=> getImage($userInfo["avatar"]),
            "firstcharter"=>$firstcharter,
            "gender"=>$userInfo["gender"],
            "realname"=>$userInfo["realname"],
            "signature"=>empty($userInfo["sign"])? Agent::getSign() : trim($userInfo['sign']) ,
            "zone_id"=>$userInfo["zone_id"],
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
        );

        if(empty($data['token']) && !empty($data['realname'])){
            try{
                $user_token = GainToken('agent'. $data['agent_id'], $data['realname'], $data['avatar']);
                Agent::where('id', $data['agent_id'])->update(['token' => $user_token]);
                $data['token'] = $user_token;
            }catch (\Exception $e){
                return ['message'=>$e->getMessage() ,'status'=>false];
            }
        }
        return $data;
    }





}