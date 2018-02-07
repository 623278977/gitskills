<?php

namespace App\Http\Controllers\Agent;

use Illuminate\Http\Request;
use App\Http\Controllers\Api\CommonController;
use App\Models\Agent\AgentAd;

class AgentAdController extends CommonController
{
    /*
      * 根据不同type取ad数据
      * type 为数组
      */
    public function postList(Request $request, $version=null)
    {
        $data = $request->input();
        $type = $request->input('type') ?: 'agent_index_banner';//广告banner类型位置

        if ($version) {
            $versionService = $this->init(__METHOD__, $version, null, 'Agent');
            $result = $versionService->bootstrap($data, ['version' => $version ,'request' => $request]);
            return AjaxCallbackMessage($result['data'],$result['status']);
        }
        return AjaxCallbackMessage(AgentAd::getAds($type), true);
    }
}
