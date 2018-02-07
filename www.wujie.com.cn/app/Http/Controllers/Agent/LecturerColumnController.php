<?php

namespace App\Http\Controllers\Agent;

use App\Models\Agent\Academy\AgentLecturerColumns;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Api\CommonController;

class LecturerColumnController extends CommonController
{
    public function postList(Request $request, AgentLecturerColumns $lecturerColumns,$version=null)
    {
        $result = $request->input();

        $versionService = $this->init(__METHOD__, $version, null, 'Agent');
        if ($versionService) {
            $response = $versionService->bootstrap($result, ['request' => $request, 'lecturerColumn' => $lecturerColumns]);

            return AjaxCallbackMessage($response['message'], $response['status']);
        } else {
            return AjaxCallbackMessage('该接口不存在', false);
        }
    }

    public function postDetail(Request $request, AgentLecturerColumns $lecturerColumns,$version=null)
    {
        $result = $request->input();

        $versionService = $this->init(__METHOD__, $version, null, 'Agent');
        if ($versionService) {
            $response = $versionService->bootstrap($result, ['request' => $request, 'lecturerColumn' => $lecturerColumns]);

            return AjaxCallbackMessage($response['message'], $response['status']);
        } else {
            return AjaxCallbackMessage('该接口不存在', false);
        }
    }
}
