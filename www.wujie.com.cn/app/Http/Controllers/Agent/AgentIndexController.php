<?php namespace App\Http\Controllers\Agent;

use App\Http\Controllers\Api\CommonController;
use App\Models\News\Entity as NewsModel;
use Illuminate\Http\Request;
use App\Models\Agent\Agent;
use DB;

/*********** 经纪人首页控制器--zhaoyf ***********/
class AgentIndexController extends CommonController
{
    /**
     * 经纪人首页显示
     * @param Request $request
     * @param null $version
     * @return string
     */
    public function postIndex(Request $request, $version = null)
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
     * 经纪人个人详情
     * @param Request $request
     * @param null $version
     * @return string
     */
    public function postAccount(Request $request, $version = null)
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
     * 经纪人全局搜索
     *
     * @param Request $request
     * @param null $version
     * @return string
     */
    public function postSearch(Request $request, $version = null)
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
     * 经纪人是否在线操作
     *
     * @param Request $request
     * @param null $version
     *
     * @return string
     */
    public function postToggle(Request $request, $version = null)
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
     * 接受派单
     *
     * @param Request $request
     * @param null $version
     * @return string
     * @author tangjb
     */
    public function postAcceptOrder(Request $request, $version = null)
    {
        $result = $request->input();


        if (empty($result['brand_id'])) {
            return AjaxCallbackMessage('缺少品牌id', false);
        }


        if (empty($result['uid'])) {
            return AjaxCallbackMessage('缺少用户id', false);
        }

        if (empty($result['agent_id'])) {
            return AjaxCallbackMessage('缺少经纪人id', false);
        }

        
        $versionService = $this->init(__METHOD__, $version, null, 'Agent');

        if ($versionService) {
            $response = $versionService->bootstrap($result, ['request' => $request, 'version' => $version]);

            return AjaxCallbackMessage($response['message'], $response['status']);
        }


        return AjaxCallbackMessage('该接口不存在', false);
    }

    /**
     * author zhayf
     *
     * 整合活动和直播显示（获取组合数据，根据类型区分） gain-combination-datas
     *
     */
    public function postGainCombinationDatas(Request $request, $version = null)
    {
        $result = $request->input();
        $versionService = $this->init(__METHOD__, $version, null, 'Agent');

        if ($versionService) {
            $response = $versionService->bootstrap($result, ['request' => $request, 'version' => $version]);

            return AjaxCallbackMessage($response['message'], $response['status']);
        }

        return AjaxCallbackMessage('该接口不在维护', false);
    }
}