<?php namespace App\Http\Controllers\Agent;

use App\Http\Controllers\Api\CommonController;
use App\Http\Requests\Agent\AddFriendsRequest;
use App\Http\Requests\Agent\MessageAddLogRequest;
use App\Models\Agent\Agent;
use App\Models\Agent\AgentCustomer;
use App\Models\Agent\BrandContract;
use App\Models\Brand\BrandStore;
use Illuminate\Http\Request;

/**** 消息记录控制器 --zhaoyf ****/
class MessageController extends CommonController
{
    /**
     * 添加跟进记录
     *
     * @param MessageAddLogRequest $request
     * @param null $version
     * @return string
     */
    public function postAddLog(MessageAddLogRequest $request, $version = null)
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
     * 通讯录
     *
     * @param Request $request
     * @param null $version
     * @return string
     */
    public function postContactList(Request $request, $version = null)
    {
        $result = $request->input();

        $versionService = $this->init(__METHOD__, $version, null, 'Agent');
        if ($versionService) {
            $response = $versionService->bootstrap($result, ['request' => $request]);

            if ($response['message']) {
                return AjaxCallbackMessage($response['message'], $response['status']);
            }else {
                return json_encode(['message'=>new \stdClass(), 'status' => true]);
            }
        } else {
            return AjaxCallbackMessage('该接口不存在', false);
        }
    }


    /**
     * 经纪人添加好友 zhaoyf
     *
     * @param Request $request
     * @param null $version
     * @return string
     */
    public function postAddFriends(AddFriendsRequest $request, $version = null)
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
     * 经纪人的消息通知
     * @param   Request $request
     * @param null $version
     * @internal param 经纪人ID $agent_id
     *
     * @return data_list
     */
    public function postAgentMessage(Request $request, $version = null)
    {
        $result = $request->input();

        //对参数进行判断
        if (empty($result['agent_id']) || !intval($result['agent_id'])) {
            return AjaxCallbackMessage('缺少经纪人ID：agent_id，且只能是整形', false);
        }

        $versionService = $this->init(__METHOD__, $version, null, 'Agent');
        if ($versionService) {
            $response = $versionService->bootstrap($result, ['request' => $request]);

            return AjaxCallbackMessage($response['message'], $response['status']);
        } else {
            return AjaxCallbackMessage('该接口不存在', false);
        }
    }

    /**
     * 经纪人的跟单提醒
     * @param Request $request
     * @param null $version
     *
     * @return string
     */
    public function postDocumentaryHint(Request $request, $version = null)
    {
        $result = $request->input();

        //对参数进行判断
        if (empty($result['agent_id']) || !intval($result['agent_id'])) {
            return AjaxCallbackMessage('缺少经纪人ID：agent_id，且只能是整形', false);
        }

        $versionService = $this->init(__METHOD__, $version, null, 'Agent');
        if ($versionService) {
            $response = $versionService->bootstrap($result, ['request' => $request]);

            return AjaxCallbackMessage($response['message'], $response['status']);
        } else {
            return AjaxCallbackMessage('该接口不存在', false);
        }
    }

    /**
     * 经纪人--跟单提醒
     *
     * @param Request $request
     * @param null $version
     * @return string
     */
    public function postMessageRecord(Request $request, $version = null)
    {
        $result = $request->input();

        //对参数进行判断
        if (empty($result['agent_id']) || !intval($result['agent_id'])) {
            return AjaxCallbackMessage('缺少经纪人ID：agent_id，且只能是整形', false);
        }

        $versionService = $this->init(__METHOD__, $version, null, 'Agent');
        if ($versionService) {
            $response = $versionService->bootstrap($result, ['request' => $request]);

            return AjaxCallbackMessage($response['message'], $response['status']);
        } else {
            return AjaxCallbackMessage('该接口不存在', false);
        }
    }

    /**
     * 可以发送邀请的活动列表
     *
     * @param Request $request
     * @param null $version
     * @return string
     */
    public function postActivities(Request $request, $version = null)
    {
        $result = $request->input();

        $versionService = $this->init(__METHOD__, $version, null, 'Agent');

        if ($versionService) {
            $response = $versionService->bootstrap($result, ['request' => $request]);

            return AjaxCallbackMessage($response['message'], $response['status']);
        }

        return AjaxCallbackMessage('该接口不存在',false);
    }

    /**
     * 根据品牌ID获取合同
     *
     * @param Request $request
     * @param null $version
     * @return string
     */
    public function postContracts(Request $request, $version = null)
    {
        $result = $request->input();

        $versionService = $this->init(__METHOD__, $version, null, 'Agent');

        if ($versionService) {
            $response = $versionService->bootstrap($result, ['request' => $request]);

            return AjaxCallbackMessage($response['message'], $response['status']);
        }

        return AjaxCallbackMessage('该接口不存在',false);
    }

    /**
     * 可以用来发送邀请的品牌列表
     *
     * @param Request $request
     * @param null $version
     * @return string
     */
    public function postBrands(Request $request, $version = null)
    {
        $result = $request->input();

        $versionService = $this->init(__METHOD__, $version, null, 'Agent');

        if ($versionService) {
            $response = $versionService->bootstrap($result, ['request' => $request]);

            return AjaxCallbackMessage($response['message'], $response['status']);
        }

        return AjaxCallbackMessage('该接口不存在',false);
    }

    /**
     * 获取经纪人的客户列表
     * @internal param  经纪人ID $agent_id
     * @param   Request $request
     *
     * @param null $version
     * @return array|string
     */
    public function postAgentCustomerList(Request $request, $version = null)
    {
        $result = $request->input();

        $versionService = $this->init(__METHOD__, $version, null, 'Agent');

        if ($versionService) {
            $response = $versionService->bootstrap($result, ['request' => $request]);

            return AjaxCallbackMessage($response['message'], $response['status']);
        }

        return AjaxCallbackMessage('该接口不存在',false);
    }

    /*
     *
     *展示被指定的活动邀请函
     * */
    public function anyShowActiveInvitation(Request $request ,$version = NULL){
        $versionService = $this->init(__METHOD__, $version, null, 'Agent');
        if($versionService){
            $response = $versionService->bootstrap($request->all());

            return AjaxCallbackMessage($response['message'],$response['status']);
        }
        return AjaxCallbackMessage('api接口不再维护',false);
    }

    /**
     * 显示指定的考察邀请函 zhaoyf
     *
     * @param Request $request
     * @param null $version
     *
     * @return data_list
     */
    public function anyShowInspectInvitation(Request $request, $version = null)
    {
        $result = $request->input();

        $versionService = $this->init(__METHOD__, $version, null, 'Agent');

        if ($versionService) {
            $response = $versionService->bootstrap($result, ['request' => $request]);

            return AjaxCallbackMessage($response['message'], $response['status']);
        }

        return AjaxCallbackMessage('该接口不存在',false);
    }

    /**
     *  author zhaoyf
     *
     * 获取经纪人和投资人融云聊天数据信息
     *
     * @param Request $request
     * @return success|\App\Models\Agent\success
     */
    public function anyReceiveRongChatInfo(Request $request)
    {
        $result = $request->input();

        return AgentCustomer::instance()->anyReceiveRongChatInfos($result);
    }

    /**
     * 消息 -- 推荐投资人列表 zhaoyf
     *
     * @param Request $request
     * @param null $version
     *
     * @return string
     */
    public function postRecommendCustomer(Request $request, $version = null)
    {
        $result = $request->input();

        $versionService = $this->init(__METHOD__, $version, null, 'Agent');

        if ($versionService) {
            $response = $versionService->bootstrap($result, ['request' => $request]);

            return AjaxCallbackMessage($response['message'], $response['status']);
        }

        return AjaxCallbackMessage('该接口不在维护',false);
    }

    /**
     * 改变推荐投资人按钮状态 zhaoyf
     *
     * @param Request $request
     * @param null $version
     *
     * @return bool
     */
    public function postChangeCustomerButtonStatus(Request $request, $version = null)
    {
        $result = $request->input();

        $versionService = $this->init(__METHOD__, $version, null, 'Agent');

        if ($versionService) {
            $response = $versionService->bootstrap($result, ['request' => $request]);

            return AjaxCallbackMessage($response['message'], $response['status']);
        }

        return AjaxCallbackMessage('该接口不在维护',false);
    }
}