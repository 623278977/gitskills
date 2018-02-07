<?php
/**
 * Created by PhpStorm.
 * User: yaokai
 * Date: 2017/8/29 0029
 * Time: 11:01
 */

namespace App\Http\Controllers\Agent;

use Illuminate\Http\Request;
use DB, Input;
use App\Http\Controllers\Api\CommonController;


class ActivityController extends CommonController
{
    /**
     * 活动列表
     */
    public function postList(Request $request, $version = null)
    {
        $versionService = $this->init(__METHOD__, $version, null, 'Agent');

        if ($versionService) {
            $response = $versionService->bootstrap($request->all());

            return AjaxCallbackMessage($response['message'], $response['status']);
        }

        return AjaxCallbackMessage('接口不存在', false);
    }

    /**
     * 活动列表--搜索需要
     *
     * @param Request $request
     * @param null $version
     * @return
     */
    public function postLists(Request $request, $version = null)
    {
        $result = json_decode($this->postList($request, $version), true);
        return $result['message'];
    }

    /**
     * 活动详情
     */
    public function postDetail(Request $request, $version = null)
    {
        $input = $request->all();

        if (empty($input['id'])) {
            return AjaxCallbackMessage('缺少活动id', false);
        }
        if (empty($input['agent_id'])) {
            return AjaxCallbackMessage('缺少经纪人id', false);
        }
        $versionService = $this->init(__METHOD__, $version, null, 'Agent');

        if ($versionService) {
            $response = $versionService->bootstrap($request->all());

            return AjaxCallbackMessage($response['message'], $response['status']);
        }

        return AjaxCallbackMessage('api接口不再维护', false);
    }

    /**
     * author zhaoyf
     *
     * 圣诞--活动详情 christmas-detail
     */
    public function postChristmasDetail(Request $request, $version = null)
    {
        $versionService = $this->init(__METHOD__, $version, null, 'Agent');

        if ($versionService) {
            $response = $versionService->bootstrap($request->all());

            return AjaxCallbackMessage($response['message'], $response['status']);
        }

        return AjaxCallbackMessage('api接口不再维护', false);
    }

    /**
     * 活动列表
     */
    public function postApply(Request $request, $version = null)
    {
        $data = $request->input();
        if (empty($data['id'])) {
            return AjaxCallbackMessage('经纪人id必传', false);
        }
        if (empty($data['activity_id'])) {
            return AjaxCallbackMessage('活动id必传', false);
        }

        if (empty($data['name'])) {
            return AjaxCallbackMessage('参会人姓名', false);
        }

        if (empty($data['tel'])) {
            return AjaxCallbackMessage('参会人手机号', false);
        }

        if (empty($data['maker_id'])) {
            return AjaxCallbackMessage('空间id必须传', false);
        }

        $versionService = $this->init(__METHOD__, $version, null, 'Agent');

        if ($versionService) {
            $response = $versionService->bootstrap($request->all());

            return AjaxCallbackMessage($response['data'], $response['status']);
        }

        return AjaxCallbackMessage('接口不存在', false);
    }


    /*
  * 获取活动的报名信息
 */
    public function postEnrollInfos(Request $request, $version = NULL)
    {
        $versionService = $this->init(__METHOD__, $version, null, 'Agent');

        if ($versionService) {
            $response = $versionService->bootstrap($request->all());
            return AjaxCallbackMessage($response['message'], $response['status']);
        }

        return AjaxCallbackMessage('api接口不再维护', false);
    }



    /*
* 获取活动的报名信息
*/
    public function postApplySuccess(Request $request, $version = NULL)
    {
        $versionService = $this->init(__METHOD__, $version, null, 'Agent');

        if ($versionService) {
            $response = $versionService->bootstrap($request->all());
            return AjaxCallbackMessage($response['message'], $response['status']);
        }

        return AjaxCallbackMessage('api接口不再维护', false);
    }


    /*
* 获取活动的报名信息
*/
    public function postSign(Request $request, $version = NULL)
    {
        $versionService = $this->init(__METHOD__, $version, null, 'Agent');

        if ($versionService) {
            $response = $versionService->bootstrap($request->all());
            return AjaxCallbackMessage($response['message'], $response['status']);
        }

        return AjaxCallbackMessage('api接口不再维护', false);
    }





}