<?php
namespace App\Http\Controllers\Agent;

use App\Models\Agent\Academy\AgentArticlesKnowledge;
use Illuminate\Http\Request;

use App\Http\Controllers\Api\CommonController;

class ArticleController extends CommonController
{
    public function postList(Request $request, AgentArticlesKnowledge $articles,$version=null)
    {
        $result = $request->input();

        $versionService = $this->init(__METHOD__, $version, null, 'Agent');
        if ($versionService) {
            $response = $versionService->bootstrap($result, ['request' => $request, 'article' => $articles]);

            return AjaxCallbackMessage($response['message'], $response['status']);
        } else {
            return AjaxCallbackMessage('该接口不存在', false);
        }
    }

    public function postDetail(Request $request, AgentArticlesKnowledge $articles,$version=null)
    {
        $result = $request->input();

        $versionService = $this->init(__METHOD__, $version, null, 'Agent');
        if ($versionService) {
            $response = $versionService->bootstrap($result, ['request' => $request, 'article' => $articles]);

            return AjaxCallbackMessage($response['message'], $response['status']);
        } else {
            return AjaxCallbackMessage('该接口不存在', false);
        }
    }

    public function postTypes(Request $request, AgentArticlesKnowledge $articles,$version=null)
    {
        $result = $request->input();

        $versionService = $this->init(__METHOD__, $version, null, 'Agent');
        if ($versionService) {
            $response = $versionService->bootstrap($result, ['request' => $request, 'article' => $articles]);

            return AjaxCallbackMessage($response['message'], $response['status']);
        } else {
            return AjaxCallbackMessage('该接口不存在', false);
        }
    }

    public function postCommentList(Request $request, AgentArticlesKnowledge $articles,$version=null)
    {
        $result = $request->input();

        $versionService = $this->init(__METHOD__, $version, null, 'Agent');
        if ($versionService) {
            $response = $versionService->bootstrap($result, ['request' => $request, 'article' => $articles]);

            return AjaxCallbackMessage($response['message'], $response['status']);
        } else {
            return AjaxCallbackMessage('该接口不存在', false);
        }
    }
}