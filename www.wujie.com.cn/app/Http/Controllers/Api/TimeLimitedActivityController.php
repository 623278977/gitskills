<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018-2-1
 * Time: 15:35
 */

namespace App\Http\Controllers\Api;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class TimeLimitedActivityController extends CommonController
{
    //春节抽奖页面
    public function postNewYearLottery(Request $request, $version=null)
    {
        $data = $request->input();
        if ($version) {
            $versionService = $this->init(__METHOD__, $version);
            $result = $versionService->bootstrap($data);
            return AjaxCallbackMessage($result['message'], $result['status']);
        }

        return AjaxCallbackMessage('该接口停用', false);
    }
    //春节抽奖结果
    public function postNewYearLotteryResult(Request $request, $version=null)
    {
        $data = $request->input();
        if ($version) {
            $versionService = $this->init(__METHOD__, $version);
            $result = $versionService->bootstrap($data);
            return AjaxCallbackMessage($result['message'], $result['status']);
        }

        return AjaxCallbackMessage('该接口停用', false);
    }
    //春节抽奖实物奖品信息填写
    public function postUserInfo(Request $request, $version=null)
    {
        $data = $request->input();
        if ($version) {
            $versionService = $this->init(__METHOD__, $version);
            $result = $versionService->bootstrap($data);
            return AjaxCallbackMessage($result['message'], $result['status']);
        }

        return AjaxCallbackMessage('该接口停用', false);
    }

}