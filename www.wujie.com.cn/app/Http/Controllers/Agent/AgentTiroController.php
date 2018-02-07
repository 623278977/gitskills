<?php

namespace App\Http\Controllers\Agent;

use Illuminate\Http\Request;
use App\Http\Controllers\Api\CommonController;

class AgentTiroController extends CommonController
{
    public function postLists(Request $request, $version=null)
    {
        $input=$request->all();

        $versionService = $this->init(__METHOD__, $version, null, 'Agent');

        if ($versionService) {
            $response = $versionService->bootstrap($input,['request'=>$request]);

            return AjaxCallbackMessage($response['message'], $response['status']);
        }

        return AjaxCallbackMessage('api接口不再维护', false);
    }
}
