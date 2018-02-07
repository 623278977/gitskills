<?php

namespace App\Http\Controllers\Agent;

use App\Http\Requests\Agent\AgentRedPacket\GiveFRedpacket;
use App\Http\Requests\Agent\AgentRedPacket\postActiveDetailRequset;
use App\Http\Requests\Agent\AgentRedPacket\PostFCardLogRequest;
use App\Http\Requests\Agent\AgentRedPacket\PostNewYearRedpacketRequest;
use App\Http\Requests\Agent\AgentRedPacket\postReceiveOpenRedpacketRequest;
use Illuminate\Http\Request;
use App\Http\Controllers\Api\CommonController;

class AgentRedPacketController extends CommonController
{
    public function postActiveDetail(postActiveDetailRequset $request, $version = NULL){
        $versionService = $this->init(__METHOD__, $version, null, 'Agent');
        if($versionService){
            $response = $versionService->bootstrap($request->all());
            return AjaxCallbackMessage($response['message'],$response['status']);
        }
        return AjaxCallbackMessage('api接口不再维护',false);
    }


    //抽取新年红包接口
    public function postNewYearRedpacket(PostNewYearRedpacketRequest $request, $version = NULL){
        $versionService = $this->init(__METHOD__, $version, null, 'Agent');
        if($versionService){
            $response = $versionService->bootstrap($request->all());
            return AjaxCallbackMessage($response['message'],$response['status']);
        }
        return AjaxCallbackMessage('api接口不再维护',false);
    }

    //福卡获得或赠送日志
    public function postFCardLog(PostFCardLogRequest $request, $version = NULL){
        $versionService = $this->init(__METHOD__, $version, null, 'Agent');
        if($versionService){
            $response = $versionService->bootstrap($request->all());
            return AjaxCallbackMessage($response['message'],$response['status']);
        }
        return AjaxCallbackMessage('api接口不再维护',false);
    }

    /**
     *   作者：shiqy
     *   创作时间：2018/1/19 0019 下午 7:51
     *   功能描述：领取开门大吉红包
     */
    public function postReceiveOpenRedpacket(postReceiveOpenRedpacketRequest $request, $version = NULL){
        $versionService = $this->init(__METHOD__, $version, null, 'Agent');
        if($versionService){
            $response = $versionService->bootstrap($request->all());
            return AjaxCallbackMessage($response['message'],$response['status']);
        }
        return AjaxCallbackMessage('api接口不再维护',false);
    }

    /**
    *   作者：shiqy
    *   创作时间：2018/1/26 0026 下午 2:10
    *   功能描述：经纪人赠送福字红包
    */

    public function postGiveFRedpacket(GiveFRedpacket $request, $version = NULL){
        $versionService = $this->init(__METHOD__, $version, null, 'Agent');
        if($versionService){
            $response = $versionService->bootstrap($request->all());
            return AjaxCallbackMessage($response['message'],$response['status']);
        }
        return AjaxCallbackMessage('api接口不再维护',false);
    }
}
