<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018-1-24
 * Time: 17:42
 */

namespace App\Http\Controllers\Api;


use Illuminate\Http\Request;

class RedPacketController extends CommonController
{
    /*
     * 领取后台推送红包
     */
    public function postReceivePush(Request $request, $version=null)
    {
        $data = $request->input();

        //初始化
        $versionService = $this->init(__METHOD__, $version);

        if ($versionService) {
            $response = $versionService->bootstrap($data);
            return AjaxCallbackMessage($response['message'], $response['status']);
        }

        return AjaxCallbackMessage('api接口不再维护', false);
    }
    /*
     * 领取品牌页面红包
     */
    public function postReceiveBrand(Request $request, $version=null)
    {
        $data = $request->input();

        //初始化
        $versionService = $this->init(__METHOD__, $version);

        if ($versionService) {
            $response = $versionService->bootstrap($data);
            return AjaxCallbackMessage($response['message'], $response['status']);
        }

        return AjaxCallbackMessage('api接口不再维护', false);
    }

    /*
     * 领取分享页面红包
     */
    public function postReceiveShare(Request $request, $version=null)
    {
        $data = $request->input();

        //初始化
        $versionService = $this->init(__METHOD__, $version);

        if ($versionService) {
            $response = $versionService->bootstrap($data);
            return AjaxCallbackMessage($response['message'], $response['status']);
        }

        return AjaxCallbackMessage('api接口不再维护', false);
    }

    /*
     * 领取分享页面红包
     */
    public function postReceiveSuccess(Request $request, $version=null)
    {
        $data = $request->input();

        //初始化
        $versionService = $this->init(__METHOD__, $version);

        if ($versionService) {
            $response = $versionService->bootstrap($data);
            return AjaxCallbackMessage($response['message'], $response['status']);
        }

        return AjaxCallbackMessage('api接口不再维护', false);
    }
}