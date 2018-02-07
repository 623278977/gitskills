<?php


namespace App\Http\Controllers\Agent;
use App\Http\Controllers\Api\UploadController;
use App\Http\Controllers\Api\CommonController;
use App\Models\Agent\Agent;
use DB;
use Hash;
use Illuminate\Http\Request;
use \Cache;

/**
 * 登录注册接口——石清源
 *
 *
 */
class InspectorController extends CommonController
{

    /*
     * 注册
     *
     * */
    public function postRegister(Request $request, $version = NULL){


        if (!in_array($request->get('submit_flag'), ['first', 'end'], true)) {
            return AjaxCallbackMessage('接口标志不合法', false);
        }
        $data = $request->all();

        //判断是否是ios请求
        $data['platform'] = '';
        $header = $request->header('USER_AGENT');
        if(strpos($header, 'iPhone') !== false){
            $data['platform'] = 'ios';
        }
        $version = $version ? :'_v010000';
        $versionService = $this->init(__METHOD__, $version, null, 'Agent');
        if($versionService){
            $response = $versionService->bootstrap($data);

            return AjaxCallbackMessage($response['message'],$response['status']);
        }
        return AjaxCallbackMessage('api接口不再维护',false);
    }

    /*
     * 登录表
     *
     * */
    public function postLogin(Request $request,$version = NULL){
        $version = $version ? :'_v010000';

        $input = $request->all();

        if (empty($input['username'])) {
            return AjaxCallbackMessage('缺少用户名', false);
        } else {
            $agent = Agent::where('non_reversible', encryptTel($input['username']))->value('status');
            if (!$agent || $agent == '-1') {
                return AjaxCallbackMessage('经纪人不存在！', false);
            }else{//写入版本
                Agent::where('non_reversible', encryptTel($input['username']))->update(['version'=>$version]);
            }
        }

        $versionService = $this->init(__METHOD__, $version, null, 'Agent');
        if($versionService){
            $response = $versionService->bootstrap($input);

            return AjaxCallbackMessage($response['message'],$response['status']);
        }
        return AjaxCallbackMessage('api接口不再维护',false);
    }

    //找回密码
    public function postRetrieve(Request $request,$version = NULL){
        $version = $version ? :'_v010000';
        $versionService = $this->init(__METHOD__, $version, null, 'Agent');
        if($versionService){
            $response = $versionService->bootstrap($request->all());

            return AjaxCallbackMessage($response['message'],$response['status']);
        }
        return AjaxCallbackMessage('api接口不再维护',false);
    }

    /**
     * 退出登录   数据中心版
     * @User yaokai
     * @param Request $request 经纪人id
     * @param null $version
     * @return string
     */
    public function postOutlogin(Request $request, $version = NULL)
    {
        $id = $request->input('id');
        $data = Agent::where("id", $id)->update(['is_online' => 0, 'identifier' => '']);
        if ($data === false) {
            return AjaxCallbackMessage("退出失败", false);
        }
        return AjaxCallbackMessage("退出成功", true);
    }

    /*
     * 身份证识别
     *
     * */

    public function postIdentity(Request $request,$version = NULL){
        $type=intval($request->input("type",1));
        //参照原来的代码
        $uploadController=new UploadController();
        $imgInfo=$uploadController->postUp1($request);
        $frontUrl='./'.$imgInfo['saveUrl'];
        $idcardInfo=getIdCardIdentityInfo($frontUrl,$type);
        $ss = [];
        $ss[] = $idcardInfo;
        file_put_contents(storage_path('sfz') , json_encode($ss) , FILE_APPEND );
        if($idcardInfo === false){
            return AjaxCallbackMessage('身份证识别失败', false);
        }
        if($type==1){
            if(empty($idcardInfo['num'])){
                return AjaxCallbackMessage('请换个姿势重拍一下', false);
            }
            $idCardNum = $idcardInfo['num'];
            $isRight=idCardExpVerify($idCardNum);
            if(!$isRight){
                return AjaxCallbackMessage('身份证识别失败', false);
            }
            $agentInfo=Agent::where('identity_card',$idCardNum)->first();
            if(is_object($agentInfo)){
                return AjaxCallbackMessage('该身份证已经被注册', false);
            }
            $data=array(
                "identity_card_front"=>$imgInfo['saveUrl'],
                "gender"=>trim($idcardInfo['sex']),
                "realname"=>trim($idcardInfo['name']),
                "identity_card"=>$idCardNum,
                "birth"=>trim($idcardInfo['birth']),
            );

            //随后将路径和数据传递到前台
            return AjaxCallbackMessage($data, true);
        }
        else{
            $endDate=trim($idcardInfo['end_date']);
            if(empty($endDate)){
                return AjaxCallbackMessage("身份证识别失败", false);
            }
            $nowTime=time();
            $year=substr($endDate,0,4);
            $month=substr($endDate,4,2);
            $day=substr($endDate,6,2);
            $endDateTamp=strtotime($year.'-'.$month.'-'.$day);
            if($endDateTamp<=$nowTime){
                return AjaxCallbackMessage("该身份证已过期", false);
            }
            return AjaxCallbackMessage($imgInfo['saveUrl'], true);
        }
    }



    //经纪人注册成功欢迎页面  的接口
    //shiqy
    public function postMessageBack(Request $request,$version = NULL){
        $version = $version ? :'_v010000';
        $versionService = $this->init(__METHOD__, $version, null, 'Agent');
        if ($versionService) {
            $response = $versionService->bootstrap($request->all());
            return AjaxCallbackMessage($response['message'], $response['status']);
        }
        return AjaxCallbackMessage('api接口不再维护', false);
    }

    //保存认证信息  shiqy
    public function postSaveAuthInfo(Request $request,$version = NULL){
        $versionService = $this->init(__METHOD__, $version, null, 'Agent');
        if ($versionService) {
            $response = $versionService->bootstrap($request->all());
            return AjaxCallbackMessage($response['message'], $response['status']);
        }
        return AjaxCallbackMessage('api接口不再维护', false);
    }

}