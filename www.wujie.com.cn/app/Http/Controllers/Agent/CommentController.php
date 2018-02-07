<?php namespace App\Http\Controllers\Agent;

use App\Http\Requests\Agent\CommentRequest;
use \App\Http\Controllers\Api\CommonController;
use Illuminate\Http\Request;

class CommentController extends CommonController
{
    /**
     * 资讯评论 zhaoyf
     *
     * @param   CommentRequest $request
     * @param   null $version
     *
     * @return  string
     */
    public function postNewsAddComment(CommentRequest $request, $version = null)
    {
        $result = $request->input();
        $versionService = $this->init(__METHOD__, $version, null, 'Agent');

        if ($versionService) {
            $response = $versionService->bootstrap($result, ['request' => $request, 'version' => $version]);

            return AjaxCallbackMessage($response['message'], $response['status']);
        }

        return AjaxCallbackMessage('该接口不存在', false);
    }

    /**
     * 对资讯点赞 zhaoyf
     *
     * @param Request $request
     * @param null $version
     * @return string
     */
    public function postNewsAddZan(Request $request, $version = null)
    {
        $result = $request->input();
        $versionService = $this->init(__METHOD__, $version, null, 'Agent');

        if ($versionService) {
            $response = $versionService->bootstrap($result, ['request' => $request, 'version' => $version]);

            return AjaxCallbackMessage($response['message'], $response['status']);
        }

        return AjaxCallbackMessage('该接口不存在', false);
    }

    /**
     * 获取指定资讯所有的评论数据列表
     *
     * @param Request $request
     * @param null $version
     *
     * @return string
     */
    public function postAssignNewsAllCommentList(Request $request, $version = null)
    {
        $result = $request->input();
        $versionService = $this->init(__METHOD__, $version, null, 'Agent');

        if ($versionService) {
            $response = $versionService->bootstrap($result, ['request' => $request, 'version' => $version]);

            return AjaxCallbackMessage($response['message'], $response['status']);
        }

        return AjaxCallbackMessage('该接口不存在', false);
    }

    /**
     * author zhaoyf
     *
     * 对指定用户的评论进行点赞
     *
     * @param Request $request
     * @param null $version
     * @return string
     * assign-user-comment-add-zan
     */
    public function postAssignUserCommentAddZan(Request $request, $version = null)
    {
        $result = $request->input();
        $versionService = $this->init(__METHOD__, $version, null, 'Agent');

        if ($versionService) {
            $response = $versionService->bootstrap($result, ['request' => $request, 'version' => $version]);

            return AjaxCallbackMessage($response['message'], $response['status']);
        }

        return AjaxCallbackMessage('该接口不存在', false);
    }
    //添加评论
    public function postAddComment(Request $request, $version = null)
    {
        $result = $request->input();
        $versionService = $this->init(__METHOD__, $version, null, 'Agent');

        if ($versionService) {
            $response = $versionService->bootstrap($result, ['request' => $request, 'version' => $version]);

            return AjaxCallbackMessage($response['message'], $response['status']);
        }

        return AjaxCallbackMessage('该接口不存在', false);
    }

    //评论列表
    public function postCommentList(Request $request, $version = null)
    {
        $result = $request->input();
        $versionService = $this->init(__METHOD__, $version, null, 'Agent');

        if ($versionService) {
            $response = $versionService->bootstrap($result, ['request' => $request, 'version' => $version]);

            return AjaxCallbackMessage($response['message'], $response['status']);
        }

        return AjaxCallbackMessage('该接口不存在', false);
    }

    //指定用户的评论被回复列表
    public function postReplyList(Request $request, $version = null)
    {
        $result = $request->input();
        $versionService = $this->init(__METHOD__, $version, null, 'Agent');

        if ($versionService) {
            $response = $versionService->bootstrap($result, ['request' => $request, 'version' => $version]);

            return AjaxCallbackMessage($response['message'], $response['status']);
        }

        return AjaxCallbackMessage('该接口不存在', false);
    }
    //通用点赞
    public function postAddZan(Request $request, $version = null)
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