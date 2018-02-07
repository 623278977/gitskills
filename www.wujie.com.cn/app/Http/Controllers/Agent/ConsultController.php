<?php
/**
 * Created by PhpStorm.
 * Title：咨询任务相关
 * User: yaokai
 * Date: 2017/11/7 0007
 * Time: 16:42
 */

namespace  App\Http\Controllers\Agent;

use App\Models\Agent\Agent;
use Illuminate\Http\Request;
use DB, Input;
use App\Http\Controllers\Api\CommonController;

class ConsultController extends CommonController
{

    /**
     * 咨询任务列表
     * @User yaokai
     * @param Request $request
     * @param null $version
     * @return string
     */
    public function postLists(Request $request, $version = null)
    {
        $input = $request->all();

        if (empty($input['agent_id'])) {
            return AjaxCallbackMessage('缺少经纪人id', false);
        } else {
            $agent = Agent::where('id', $input['agent_id'])->value('status');
            if (!$agent || $agent == '-1') {
                return AjaxCallbackMessage('经纪人不存在！', false);
            }
        }

        $versionService = $this->init(__METHOD__, $version, null, 'Agent');

        if ($versionService) {
            $response = $versionService->bootstrap($request->all());

            return AjaxCallbackMessage($response['message'], $response['status']);
        }

        return AjaxCallbackMessage('api接口不再维护', false);
    }

    /**
     * 咨询任务详情
     * @User yaokai
     * @param Request $request
     * @param null $version
     * @return string
     */
    public function postDetail(Request $request, $version = null)
    {
        $input = $request->all();

        if (empty($input['agent_id'])) {
            return AjaxCallbackMessage('缺少经纪人id', false);
        } else {
            $agent = Agent::where('id', $input['agent_id'])->value('status');
            if (!$agent || $agent == '-1') {
                return AjaxCallbackMessage('经纪人不存在！', false);
            }
        }

        if (empty($input['id'])) {
            return AjaxCallbackMessage('缺少咨询任务id', false);
        }

        $versionService = $this->init(__METHOD__, $version, null, 'Agent');

        if ($versionService) {
            $response = $versionService->bootstrap($request->all());

            return AjaxCallbackMessage($response['message'], $response['status']);
        }

        return AjaxCallbackMessage('api接口不再维护', false);
    }


    public function postRefuseAccept(Request $request, $version = null)
    {
        $input = $request->all();

        if (empty($input['agent_id'])) {
            return AjaxCallbackMessage('缺少经纪人id', false);
        } else {
            $agent = Agent::where('id', $input['agent_id'])->value('status');
            if (!$agent || $agent == '-1') {
                return AjaxCallbackMessage('经纪人不存在！', false);
            }
        }

        if (empty($input['id'])) {
            return AjaxCallbackMessage('缺少咨询任务id', false);
        }

        $versionService = $this->init(__METHOD__, $version, null, 'Agent');

        if ($versionService) {
            $response = $versionService->bootstrap($request->all());

            return AjaxCallbackMessage($response['message'], $response['status']);
        }

        return AjaxCallbackMessage('api接口不再维护', false);
    }
    
    
    


}







