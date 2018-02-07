<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017-12-5
 * Time: 17:25
 */

namespace App\Http\Controllers\Agent;
use App\Http\Controllers\Api\CommonController;
use Illuminate\Http\Request;
use App\Models\Agent\Exhibition\TalkingExercise;

class TalkingExerciseController extends CommonController
{
    public function postList(Request $request, TalkingExercise $talking,$version=null)
    {
        $result = $request->input();

        $versionService = $this->init(__METHOD__, $version, null, 'Agent');
        if ($versionService) {
            $response = $versionService->bootstrap($result, ['request' => $request, 'talking' => $talking]);

            return AjaxCallbackMessage($response['message'], $response['status']);
        } else {
            return AjaxCallbackMessage('该接口不存在', false);
        }
    }

    public function postDetail(Request $request, TalkingExercise $talking,$version=null)
    {
        $result = $request->input();

        $versionService = $this->init(__METHOD__, $version, null, 'Agent');
        if ($versionService) {
            $response = $versionService->bootstrap($result, ['request' => $request, 'talking' => $talking]);

            return AjaxCallbackMessage($response['message'], $response['status']);
        } else {
            return AjaxCallbackMessage('该接口不存在', false);
        }
    }
}