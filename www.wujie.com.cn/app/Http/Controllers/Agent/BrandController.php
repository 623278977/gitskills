<?php
/**
 * Created by PhpStorm.
 * User: yaokai
 * Date: 2017/8/29 0029
 * Time: 11:01
 */

namespace App\Http\Controllers\Agent;

use App\Models\Agent\Agent;
use Illuminate\Http\Request;
use DB, Input;
use App\Models\Brand\Entity as BrandModel;
use App\Http\Controllers\Api\CommonController;

class BrandController extends CommonController
{
    /**
     * 品牌列表
     * @param Request $request
     * @param null $version
     * @return string
     */
    public function postList(Request $request, $version = null)
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
     * 品牌列表--搜索需要
     *
     * @param Request $request
     * @param null $version
     */
    public function postLists(Request $request, $version = null)
    {
        $result = json_decode($this->postList($request, $version), true);
        return $result['message'];
    }

    /**
     * 品牌详情
     */
    public function postDetail(Request $request, $version = null)
    {

        $input = $request->all();

        if (empty($input['id'])) {
            return AjaxCallbackMessage('缺少品牌id', false);
        } else {
            $status = BrandModel::where('id', $input['id'])->value('agent_status');
            if (!$status) {
                return AjaxCallbackMessage('异常，该品牌不存在，或者已经下架', false);
            }
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
     * 品牌申请页面详情
     */
    public function postApplyDetail(Request $request, $version = null)
    {
        $input = $request->all();

        if (empty($input['brand_id'])) {
            return AjaxCallbackMessage('缺少品牌id', false);
        } else {
            $status = BrandModel::where('id', $input['brand_id'])->value('agent_status');
            if (!$status) {
                return AjaxCallbackMessage('异常，该品牌不存在，或者已经下架', false);
            }
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
     * 申请代理品牌
     */
    public function postApply(Request $request, $version = null)
    {
        $input = $request->all();

        if (empty($input['brand_id'])) {
            return AjaxCallbackMessage('缺少品牌id', false);
        } else {
            $status = BrandModel::where('id', $input['brand_id'])->value('agent_status');
            if (!$status) {
                return AjaxCallbackMessage('异常，该品牌不存在，或者已经下架', false);
            }
        }

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
     * 品牌跟进的客户
     */
    public function postBrandCustomer(Request $request, $version = null)
    {
        $input = $request->all();

        if (empty($input['contract_id'])) {
            return AjaxCallbackMessage('缺少合同id', false);
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
     * 申请代理品牌进度跟进
     */
    public function postApplyStatus(Request $request, BrandModel $brand = null, $version = null)
    {
        $input = $request->all();

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
     * 品牌提成详情
     *
     * @param Request $request
     * @param null $version
     * @return string
     * @author tangjb
     */
    public function postCommission(Request $request, $version = null)
    {
        $input = $request->all();

        if (empty($input['contract_id'])) {
            return AjaxCallbackMessage('缺少合同id', false);
        }

        $versionService = $this->init(__METHOD__, $version, null, 'Agent');

        if ($versionService) {
            $response = $versionService->bootstrap($request->all());

            return AjaxCallbackMessage($response['message'], $response['status']);
        }

        return AjaxCallbackMessage('api接口不再维护', false);

    }


    public function postColumnList(Request $request, $version = null)
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

    /*
     * 章节列表
     * shiqy
     * */
    public function postChapterList(Request $request, $version = null){
        $versionService = $this->init(__METHOD__, $version, null, 'Agent');
        if($versionService){
            $response = $versionService->bootstrap($request->all());

            return AjaxCallbackMessage($response['message'],$response['status']);
        }
        return AjaxCallbackMessage('api接口不再维护',false);
    }


}