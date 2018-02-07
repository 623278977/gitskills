<?php namespace App\Http\Controllers\Agent;

use App\Http\Controllers\Api\CommonController;
use Illuminate\Http\Request;

class LuckyBagController extends CommentController
{
    /**
     * author zhaoyf
     *
     * 获取经纪人拥有的福袋红包数据信息 agent-lucky-bag-red-lists
     *
     * @param $agent_id     经纪人ID arrays
     *
     * @return arrays
     */
    public function postAgentLuckyBagRedLists(Request $request, $version = NULL)
    {
        $versionService = $this->init(__METHOD__, $version, null, 'Agent');

        if ($versionService) {
            $response = $versionService->bootstrap($request->all());

            return AjaxCallbackMessage($response['message'],$response['status']);
        }

        return AjaxCallbackMessage('api接口不再维护', false);
    }

    /**
     * 经纪人进行红包抽奖（福袋红包）agent-red-extracts
     *
     * @param $agent_id     经纪人ID arrays
     *
     * @return arrays
     */
    public function postAgentRedExtracts(Request $request, $version = NULL)
    {
        $versionService = $this->init(__METHOD__, $version, null, 'Agent');

        if ($versionService) {
            $response = $versionService->bootstrap($request->all());

            return AjaxCallbackMessage($response['message'],$response['status']);
        }

        return AjaxCallbackMessage('api接口不再维护', false);
    }

    /**
     * author zhaoyf
     *
     * 查看红包详情 look-red-details
     *
     * @param $request    请求集合参数 arrays
     *
     * @return arrays
     */
    public function postLookRedDetails(Request $request, $version = NULL)
    {
        $versionService = $this->init(__METHOD__, $version, null, 'Agent');

        if ($versionService) {
            $response = $versionService->bootstrap($request->all());

            return AjaxCallbackMessage($response['message'],$response['status']);
        }

        return AjaxCallbackMessage('api接口不再维护', false);
    }

    /**
     * author zhaoyf
     *
     * 获取某个投资人的红包数据列表信息 gain-one-customer-red-datas
     *
     * @param $request    请求集合参数 arrays
     *
     * @return arrays
     */
    public function postGainOneCustomerRedDatas(Request $request, $version = NULL)
    {
        $versionService = $this->init(__METHOD__, $version, null, 'Agent');

        if ($versionService) {
            $response = $versionService->bootstrap($request->all());

            return AjaxCallbackMessage($response['message'],$response['status']);
        }

        return AjaxCallbackMessage('api接口不再维护', false);
    }

    /**
     * author zhaoyf
     *
     * 处理： 经纪人发送指定红包给指定投资人--设置发送红包后，红包的有效时间（五个小时）
     *
     * 方法名：set-send-red-later-of-valid-time
     *
     * @param $agent_get_red_id 经纪人获取的红包对应表的ID
     * @param $red_id           经纪人发送给投资人的红包ID
     * @param $uid              投资人ID
     * @param $agent_id         经纪人ID
     *
     * @return bool
     */
    public function postSetSendRedLaterOfValidTime(Request $request, $version = NULL)
    {
        $versionService = $this->init(__METHOD__, $version, null, 'Agent');

        if ($versionService) {
            $response = $versionService->bootstrap($request->all());

            return AjaxCallbackMessage($response['message'],$response['status']);
        }

        return AjaxCallbackMessage('api接口不再维护', false);
    }
}