<?php


namespace App\Http\Controllers\Agent;
use App\Http\Controllers\Api\CommonController;
use Illuminate\Http\Request;
use App\Models\Live\Entity as LiveModel;


/**
 * 首页——石清源
 */
class LiveController extends CommonController
{

    /*
     * 直播大厅接口
     *
     * */
    public function postList(Request $request, $version = NULL){
        $versionService = $this->init(__METHOD__, $version, null, 'Agent');
        if($versionService){
            $response = $versionService->bootstrap($request->all());
            return AjaxCallbackMessage($response['message'],$response['status']);
        }
        return AjaxCallbackMessage('api接口不再维护',false);
    }

    /*
     *
     * 直播详情接口
     *
     * */
    public function postDetail(Request $request, $version = NULL){
        $versionService = $this->init(__METHOD__, $version, null, 'Agent');
        if($versionService){
            $response = $versionService->bootstrap($request->all());
            return AjaxCallbackMessage($response['message'],$response['status']);
        }
        return AjaxCallbackMessage('api接口不再维护',false);
    }


}