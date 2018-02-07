<?php namespace App\Http\Controllers\Agent;

use App\Http\Controllers\Api\CommonController;
use App\Http\Controllers\Power\Controller;
use App\Http\Requests\Agent\NewAgentRequest;
use Illuminate\Http\Request;

class NewAgentDetailsController extends CommonController
{
    /**
     * author zhaoyf new-agent-details
     *
     * 经纪人新手专区--详情页
     */
    public function postNewAgentDetails(NewAgentRequest $request, $version = null)
    {
        $result = $request->input();

        $versionService = $this->init(__METHOD__, $version, null, 'Agent');
        if ($versionService) {
            $response = $versionService->bootstrap($result, ['request' => $request]);

            return AjaxCallbackMessage($response['message'], $response['status']);
        } else {
            return AjaxCallbackMessage('该接口不存在', false);
        }
    }

    /**
     * author zhaoyf new-agent-zans
     *
     * 新手专区详情点赞
     */
    public function postNewAgentZans(NewAgentRequest $request, $version = null)
    {
        $result = $request->input();

        $versionService = $this->init(__METHOD__, $version, null, 'Agent');
        if ($versionService) {
            $response = $versionService->bootstrap($result, ['request' => $request]);

            return AjaxCallbackMessage($response['message'], $response['status']);
        } else {
            return AjaxCallbackMessage('该接口不存在', false);
        }
    }

    /**
     * author zhaoyf new-agent-comments
     *
     * 新手专区详情评论
     */
    public function postNewAgentComments(NewAgentRequest $request, $version = null)
    {
        $result = $request->input();

        $versionService = $this->init(__METHOD__, $version, null, 'Agent');
        if ($versionService) {
            $response = $versionService->bootstrap($result, ['request' => $request]);

            return AjaxCallbackMessage($response['message'], $response['status']);
        } else {
            return AjaxCallbackMessage('该接口不存在', false);
        }
    }

    /**
     * 新手专区详情分享次数记录
     */
    public function postNewAgentShards(Request $request, $version = null)
    {
        $result = $request->input();

        $versionService = $this->init(__METHOD__, $version, null, 'Agent');
        if ($versionService) {
            $response = $versionService->bootstrap($result, ['request' => $request]);

            return AjaxCallbackMessage($response['message'], $response['status']);
        } else {
            return AjaxCallbackMessage('该接口不存在', false);
        }
    }
}