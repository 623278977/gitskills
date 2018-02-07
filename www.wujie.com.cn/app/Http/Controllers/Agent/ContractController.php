<?php
/**
 * Created by PhpStorm.
 * User: yaokai
 * Date: 2017/8/30 0030
 * Time: 11:08
 */

namespace App\Http\Controllers\Agent;

use App\Http\Controllers\Api\CommonController;
use App\Models\Contract\Contract;
use Illuminate\Http\Request;
use DB, Input;
use App\Models\Agent\Agent;
use App\Models\Brand\Entity\V020800 as BrandV020800;

class ContractController extends CommonController
{
    /**
     * 发送合同
     */
    public function postSend(Request $request, $version = null)
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
        if (empty($input['brand_contract_id']) || empty($input['uid'])) {
            return AjaxCallbackMessage('缺少合同/用户id', false);
        }
        $versionService = $this->init(__METHOD__, $version, null, 'Agent');

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

        $versionService = $this->init(__METHOD__, $version, null, 'Agent');

        if ($versionService) {
            $response = $versionService->bootstrap($request->all());

            return AjaxCallbackMessage($response['message'], $response['status']);
        }

        return AjaxCallbackMessage('api接口不再维护', false);
    }


    /**
     * 撤回合同
     * @User yaokai
     * @param Request $request
     * @return string
     */
    public function postRevoke(Request $request)
    {
        $contract_id = $request->get('contract_id','');
        if (empty($contract_id)) {
            return AjaxCallbackMessage('缺少合同id', false);
        } else {
            $contract = Contract::where('id', $contract_id)->where('status','0')->value('id');
            if (!$contract) {
                return AjaxCallbackMessage('已拒绝的合同或同意的合同不能撤回！', false);
            }
        }

        //修改对应合同状态
        Contract::where('id',$contract_id)->update(['status'=>'-3','remark'=>'经纪人主动撤回合同']);

        return AjaxCallbackMessage('撤回成功！', true);
    }

}