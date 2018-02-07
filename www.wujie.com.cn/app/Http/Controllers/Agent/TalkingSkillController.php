<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017-11-15
 * Time: 14:01
 */

namespace App\Http\Controllers\Agent;
use App\Models\Agent\Academy\AgentTalkingSkills;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Api\CommonController;

class TalkingSkillController extends CommonController
{
    public function postList(Request $request, AgentTalkingSkills $talkingSkill,$version=null)
    {
        $result = $request->input();

        $versionService = $this->init(__METHOD__, $version, null, 'Agent');
        if ($versionService) {
            $response = $versionService->bootstrap($result, ['request' => $request, 'talkingSkill' => $talkingSkill]);

            return AjaxCallbackMessage($response['message'], $response['status']);
        } else {
            return AjaxCallbackMessage('该接口不存在', false);
        }
    }

    public function postDetail(Request $request, AgentTalkingSkills $talkingSkill,$version=null)
    {
        $result = $request->input();

        $versionService = $this->init(__METHOD__, $version, null, 'Agent');
        if ($versionService) {
            $response = $versionService->bootstrap($result, ['request' => $request, 'talkingSkill' => $talkingSkill]);

            return AjaxCallbackMessage($response['message'], $response['status']);
        } else {
            return AjaxCallbackMessage('该接口不存在', false);
        }
    }
}