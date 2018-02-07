<?php
/**
 * Created by PhpStorm.
 * User: yaokai
 * Date: 2017/8/31 0031
 * Time: 11:58
 */

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\CommonController;
use App\Models\Brand\Entity\V020800 as BrandV020800;
use App\Models\Contract\Contract;
use Illuminate\Http\Request;
use DB, Input;
use App\Models\Agent\Agent;

class ContractController extends CommonController
{
    /**
     * 投资人电子合同概览
     * @User yaokai
     */
    public function postContract(Request $request, $version = null)
    {
        $input = $request->all();
        if (empty($input['uid'])) {
            return AjaxCallbackMessage('缺少客户id', false);
        }
        $versionService = $this->init(__METHOD__, $version);

        if ($versionService) {
            $response = $versionService->bootstrap($request->all());

            return AjaxCallbackMessage($response['message'], $response['status']);
        }

        return AjaxCallbackMessage('api接口不再维护', false);
    }


    /**
     * 投资人电子合同详情列表
     * @User yaokai
     */
    public function postContractDetail(Request $request, $version = null)
    {
        $input = $request->all();
        if (empty($input['uid'])) {
            return AjaxCallbackMessage('缺少客户id', false);
        }
        $versionService = $this->init(__METHOD__, $version);

        if ($versionService) {
            $response = $versionService->bootstrap($request->all());

            return AjaxCallbackMessage($response['message'], $response['status']);
        }

        return AjaxCallbackMessage('api接口不再维护', false);
    }


    /**
     * 合同详情
     */
    public function postDetail(Request $request, $version = null)
    {
        $input = $request->all();
        if (empty($input['contract_id'])) {
            return AjaxCallbackMessage('缺少合同id', false);
        } else {
            $contract = Contract::where('id', $input['contract_id'])->value('status');
            if (!isset($contract)) {
                return AjaxCallbackMessage('合同不存在！', false);
            }elseif($contract == '-3'){
                return AjaxCallbackMessage(['msg'=>'合同已关闭！','type'=>'contract_close'], false);
            }elseif($contract == '-4'){
                return AjaxCallbackMessage(['msg'=>'品牌已下架！','type'=>'brand_down'], false);
            } else{//判断合同品牌是否已下架
                $status = BrandV020800::brandAgentStatus('contract',$input['contract_id']);
                if (!$status && $contract == '0'){
                    return AjaxCallbackMessage(['msg'=>'品牌已下架！','type'=>'brand_down'], false);
                }
            }
        }

        $versionService = $this->init(__METHOD__, $version);

        if ($versionService) {
            $response = $versionService->bootstrap($request->all());

            return AjaxCallbackMessage($response['message'], $response['status']);
        }

        return AjaxCallbackMessage('api接口不再维护', false);
    }





    /**
     * 拒绝合同
     * @User yaokai
     */
    public function postDeny(Request $request, $version = null)
    {
        $input = $request->all();

        if (empty($input['contract_id']) || empty($input['uid'])) {
            return AjaxCallbackMessage('缺少合同/用户id', false);
        }
        $versionService = $this->init(__METHOD__, $version);
        if ($versionService) {
            $response = $versionService->bootstrap($request->all());

            return AjaxCallbackMessage($response['message'], $response['status']);
        }

        return AjaxCallbackMessage('api接口不再维护', false);

    }


    /**
     * 合同支付完成凭证上传
     *
     * @param Request $request
     * @param null $version
     * @return string
     * @author tangjb
     */
    public function postSuccess(Request $request, $version = null)
    {
        $versionService = $this->init(__METHOD__, $version);

        if ($versionService) {
            $response = $versionService->bootstrap($request->all());

            return AjaxCallbackMessage($response['message'], $response['status']);
        }

        return AjaxCallbackMessage('api接口不再维护', false);
    }


}