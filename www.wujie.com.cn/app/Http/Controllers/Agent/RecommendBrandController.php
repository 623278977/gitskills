<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017-11-16
 * Time: 17:40
 */

namespace App\Http\Controllers\Agent;
use App\Http\Controllers\Api\CommonController;
use Illuminate\Http\Request;

class RecommendBrandController extends CommonController
{
    //提交推荐
    public function postCommit(Request $request, $version=null)
    {
        $result = $request->input();
        $versionService = $this->init(__METHOD__, $version, null, 'Agent');

        if ($versionService) {
            $response = $versionService->bootstrap($result, ['request' => $request, 'version' => $version]);

            return AjaxCallbackMessage($response['message'], $response['status']);
        }

        return AjaxCallbackMessage('该接口不存在', false);
    }

    //推荐记录
    public function postRecord(Request $request, $version=null)
    {
        $result = $request->input();
        $versionService = $this->init(__METHOD__, $version, null, 'Agent');

        if ($versionService) {
            $response = $versionService->bootstrap($result, ['request' => $request, 'version' => $version]);

            return AjaxCallbackMessage($response['message'], $response['status']);
        }

        return AjaxCallbackMessage('该接口不存在', false);
    }
}