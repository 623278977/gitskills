<?php
namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use DB, Closure, Input;
use App\Models\Headlines\Headlines;


class IndexController extends CommonController
{
    public function postPubliclist(Request $request, $version = null)
    {
        $data = $request->input();

        //今日头条上报
        $idfa = '';
        foreach ($request->headers as $k => $v) {
            if ($k == 'idfa') {
                $idfa = $v;
            }
        }
        //如果存在就去调今日头条转化接口二
        if ($idfa){
            $idfa = implode('',$idfa);
            if ($idfa != '00000000-0000-0000-0000-000000000000'){
                Headlines::handle($idfa);
            }
        }
        //初始化
        $versionService = $this->init(__METHOD__, $version);

        if ($versionService) {
            $response = $versionService->bootstrap($data, ['request' => $request ,'version' => $version]);
            return AjaxCallbackMessage($response['message'], $response['status']);
        }

        return AjaxCallbackMessage('api接口不再维护', false);
    }



    /*
 * 接收用户填写邀请码
 */
    public function postCode(Request $request ,$version = null)
    {
        $data = $request->input();

        //初始化
        $versionService = $this->init(__METHOD__, $version);

        if($versionService){
            $response = $versionService->bootstrap($data, ['request' => $request ,'_uid' => $this->_uid]);
            return AjaxCallbackMessage($response['message'], $response['status']);
        }

        return AjaxCallbackMessage('api接口不再维护', false);
    }
    //获取指定数据部分数据源
    public function postData(Request $request ,$version = null){
        $data = $request->input();
        //初始化
        $versionService = $this->init(__METHOD__, $version);
        if($versionService){
            $response = $versionService->bootstrap($data, ['request' => $request ,'type' => $request['type']]);
            return AjaxCallbackMessage($response['message'], $response['status']);
        }
        return AjaxCallbackMessage('api接口不再维护', false);
    }


    /*
    * 查询是否有正在进行的直播
    */
    public function postIfLiving(Request $request ,$version = null)
    {
        $data = $request->input();

        //初始化
        $versionService = $this->init(__METHOD__, $version);


        if($versionService){
            $response = $versionService->bootstrap($data, ['request' => $request ]);
            return AjaxCallbackMessage($response['message'], $response['status']);
        }

        return AjaxCallbackMessage('api接口不再维护', false);
    }

}