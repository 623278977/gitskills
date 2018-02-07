<?php

namespace App\Http\Controllers\Agent;
use App\Http\Controllers\Api\CommonController;
use Illuminate\Http\Request;
use App\Http\Requests\Agent\WorkSpace\IndexRequest;
use App\Models\Agent\Exhibition\WeChat;
use App\Models\Agent\Exhibition\AgentPoster;
/**
 * 经纪人展业夹
 *
 */
class WorkSpaceController extends CommonController
{

    /**
     * 首页
     * shiqy
     * */
    public function postIndex(IndexRequest $request, $version = NULL){
        $versionService = $this->init(__METHOD__, $version, null, 'Agent');
        if($versionService){
            $response = $versionService->bootstrap($request->all());
            return AjaxCallbackMessage($response['message'],$response['status']);
        }
        return AjaxCallbackMessage('api接口不再维护',false);
    }

    //微信营销列表
    public function postWeChatList(Request $request,WeChat $chat, $version=null)
    {
        $result = $request->input();

        $versionService = $this->init(__METHOD__, $version, null, 'Agent');
        if ($versionService) {
            $response = $versionService->bootstrap($result, ['request' => $request, 'chat' => $chat]);

            return AjaxCallbackMessage($response['message'], $response['status']);
        } else {
            return AjaxCallbackMessage('该接口不存在', false);
        }
    }
    //微信营销详情
    public function postWeChatDetail(Request $request,WeChat $chat ,$version=null)
    {
        $result = $request->input();

        $versionService = $this->init(__METHOD__, $version, null, 'Agent');
        if ($versionService) {
            $response = $versionService->bootstrap($result, ['request' => $request, 'chat' => $chat]);

            return AjaxCallbackMessage($response['message'], $response['status']);
        } else {
            return AjaxCallbackMessage('该接口不存在', false);
        }
    }
    //微信营销分享
    public function postWeChatShare(Request $request,WeChat $chat ,$version=null)
    {
        $result = $request->input();

        $versionService = $this->init(__METHOD__, $version, null, 'Agent');
        if ($versionService) {
            $response = $versionService->bootstrap($result, ['request' => $request, 'chat' => $chat]);

            return AjaxCallbackMessage($response['message'], $response['status']);
        } else {
            return AjaxCallbackMessage('该接口不存在', false);
        }
    }

    //海报列表
    public function postPosterList(Request $request,AgentPoster $poster,$version=null)
    {
        $result = $request->input();

        $versionService = $this->init(__METHOD__, $version, null, 'Agent');
        if ($versionService) {
            $response = $versionService->bootstrap($result, ['request' => $request, 'poster' => $poster]);

            return AjaxCallbackMessage($response['message'], $response['status']);
        } else {
            return AjaxCallbackMessage('该接口不存在', false);
        }
    }

    //海报分享
    public function postPosterShare(Request $request,AgentPoster $poster,$version=null)
    {
        $result = $request->input();

        $versionService = $this->init(__METHOD__, $version, null, 'Agent');
        if ($versionService) {
            $response = $versionService->bootstrap($result, ['request' => $request, 'poster' => $poster]);

            return AjaxCallbackMessage($response['message'], $response['status']);
        } else {
            return AjaxCallbackMessage('该接口不存在', false);
        }
    }


}