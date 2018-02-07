<?php

namespace App\Http\Controllers\Agent;
use App\Http\Controllers\Api\CommonController;
use App\Http\Requests\Agent\Academy\StudyRequest;
use Illuminate\Http\Request;
use App\Http\Requests\Agent\Academy\IndexRequest;

/**
 * 新手学院
 *
 */
class AcademyController extends CommonController
{
    /*
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

    /**
     * author zhaoyf
     *
     * 学习 —— 题目列表 agent-study-topic-list
     */
    public function postAgentStudyTopicList(StudyRequest $request, $version = null)
    {
        $result = $request->input();

        $versionService = $this->init(__METHOD__, $version, null, 'Agent');

        if ($versionService) {
            $response = $versionService->bootstrap($result, ['request' => $request]);

            return AjaxCallbackMessage($response['message'],$response['status']);
        }

        return AjaxCallbackMessage('api接口不再维护', false);
    }

    /**
     * author zhaoyf
     *
     * 学习 —— 答题检验 agent-study-check-out
     */
    public function postAgentStudyCheckOut(Request $request, $version = null)
    {
        $result = $request->input();

        $versionService = $this->init(__METHOD__, $version, null, 'Agent');

        if ($versionService) {
            $response = $versionService->bootstrap($result, ['request' => $request]);

            return AjaxCallbackMessage($response['message'],$response['status']);
        }

        return AjaxCallbackMessage('api接口不再维护', false);
    }

    /**
     * author zhaoyf
     *
     * 问答数据列表 gain-answer-datas-list
     *
     */
    public function postGainAnswerDatasList(Request $request, $version = null)
    {
        $result = $request->input();

        $versionService = $this->init(__METHOD__, $version, null, 'Agent');

        if ($versionService) {
            $response = $versionService->bootstrap($result, ['request' => $request]);

            return AjaxCallbackMessage($response['message'],$response['status']);
        }

        return AjaxCallbackMessage('api接口不再维护', false);
    }
}