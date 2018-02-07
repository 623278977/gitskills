<?php namespace App\Http\Controllers\Agent\TemporaryHold;

use App\Http\Controllers\Api\CommonController;
use Illuminate\Http\Request;

class AgentBrandActivityController extends CommonController
{
    /**
     * author zhaoyf
     *
     * 壹Q鲜|台湾奶茶黑马品牌——临时活动 temporary-brand-activitys
     * 获取所有加盟品牌的所有投资人
     */
    public function postTemporaryBrandActivitys(Request $request, $version = null)
    {
        $result = $request->input();

        $versionService = $this->init(__METHOD__, $version, null, 'Agent\\TemporaryHold');

        if ($versionService) {
            $response = $versionService->bootstrap($request->all());

            return AjaxCallbackMessage($response['message'], $response['status']);
        }

        return AjaxCallbackMessage('api接口不再维护', false);
    }

    /**
     * author zhaoyf
     *
     * 春节临时活动——答题送红包 answer-give-reds
     */
    public function postAnswerGiveReds(Request $request, $version = null)
    {
        $result = $request->input();

        $versionService = $this->init(__METHOD__, $version, null, 'Agent\\TemporaryHold');

        if ($versionService) {
            $response = $versionService->bootstrap($request->all());

            return AjaxCallbackMessage($response['message'], $response['status']);
        }

        return AjaxCallbackMessage('api接口不再维护', false);
    }

    /**
     * author zhaoyf handle-agent-answer
     *
     * 处理经纪人的活动答题
     */
    public function postHandleAgentAnswer(Request $request, $version = null)
    {
        $result = $request->input();

        $versionService = $this->init(__METHOD__, $version, null, 'Agent\\TemporaryHold');

        if ($versionService) {
            $response = $versionService->bootstrap($request->all());

            return AjaxCallbackMessage($response['message'], $response['status']);
        }

        return AjaxCallbackMessage('api接口不再维护', false);
    }

    /**
     * author zhaoyf
     *
     * 返回已经获得答题红包的用户信息 return-get-answer-red-use-datas
     */
    public function postReturnGetAnswerRedUseDatas(Request $request, $version = null)
    {
        $result = $request->input();

        $versionService = $this->init(__METHOD__, $version, null, 'Agent\\TemporaryHold');

        if ($versionService) {
            $response = $versionService->bootstrap($request->all());

            return AjaxCallbackMessage($response['message'], $response['status']);
        }

        return AjaxCallbackMessage('api接口不再维护', false);
    }
}