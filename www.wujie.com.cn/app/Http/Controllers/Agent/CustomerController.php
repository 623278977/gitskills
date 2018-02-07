<?php

namespace App\Http\Controllers\Agent;

use App\Http\Controllers\Api\CommonController;
use App\Http\Requests\Agent\CustomerRequest;
use App\Http\Requests\Agent\MessageRequest;
use App\Http\Requests\CustomerAgentRequest;
use App\Models\Agent\AgentCustomer;
use App\Models\Agent\AgentCustomerLog;
use App\Models\Agent\Invitation;
use App\Models\Contract\Contract;
use App\Services\Version\Message\_v020800;
use Illuminate\Http\Request;
use App\Models\Agent\Agent;
use App\Models\User\Entity as User;
use DB, Input;
use App\Models\Brand\Entity as BrandModel;
use App\Http\Requests\Agent\Customer\ListRuquest;

/**
 * 用户——石清源
 */
class CustomerController extends CommonController
{
    const REMARK_TYPE = 12; //备注类型

    /**
     * 加盟合同
     */
    public function postContracts(Request $request, $version = null)
    {
        $agent_id = $request->get('agent_id');
        $customer_id = $request->get('customer_id');
        if (empty($agent_id) || empty($customer_id)) {
            return AjaxCallbackMessage('缺少经纪人/客户id', false);
        } else {
            $agent = Agent::where('id', $agent_id)->value('status');
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
     * 跟进情况-加盟合同
     */
    public function postRecordsContract(Request $request, $version = null)
    {
        $agent_id = $request->get('agent_id');
        $customer_id = $request->get('customer_id');
        $brand_id = $request->get('brand_id');
        if (empty($agent_id) || empty($customer_id)) {
            return AjaxCallbackMessage('缺少经纪人/客户id', false);
        } else {
            $agent = Agent::where('id', $agent_id)->value('status');
            if (!$agent || $agent == '-1') {
                return AjaxCallbackMessage('经纪人不存在！', false);
            }
        }
        if (empty($brand_id)) {
            return AjaxCallbackMessage('缺少品牌id', false);
        } else {
            $brand = BrandModel::where('id', $brand_id)->value('status');
            if (!$brand || $brand == 'disable') {
                return AjaxCallbackMessage('异常，该品牌不存在，或者已经下架', false);
            }
        }
        $versionService = $this->init(__METHOD__, $version, null, 'Agent');

        if ($versionService) {
            $response = $versionService->bootstrap($request->all());

            return AjaxCallbackMessage($response['message'], $response['status']);
        }

        return AjaxCallbackMessage('api接口不再维护', false);

    }

    //派单客户概览
    public function postSendOverview(Request $request, $version = NULL){
        $versionService = $this->init(__METHOD__, $version, null, 'Agent');
        if ($versionService) {
            $response = $versionService->bootstrap($request->all());
            return AjaxCallbackMessage($response['message'], $response['status']);
        }
        return AjaxCallbackMessage('api接口不再维护', false);
    }

    public function postInviteOverview(Request $request, $version = NULL){
        $versionService = $this->init(__METHOD__, $version, null, 'Agent');
        if($versionService){
            $response = $versionService->bootstrap($request->all());
            return AjaxCallbackMessage($response['message'],$response['status']);
        }
        return AjaxCallbackMessage('api接口不再维护',false);
    }


    /*
     *
     * 客户列表
     *
     * */
    public function postList(ListRuquest $request, $version = NULL){
        $versionService = $this->init(__METHOD__, $version, null, 'Agent');
        if($versionService){
            $response = $versionService->bootstrap($request->all());
            return AjaxCallbackMessage($response['message'],$response['status']);
        }
        return AjaxCallbackMessage('api接口不再维护',false);
    }

    //客户管家
    public function postMaster(Request $request, $version = NULL){
        $versionService = $this->init(__METHOD__, $version, null, 'Agent');
        if($versionService){
            $response = $versionService->bootstrap($request->all());
            return AjaxCallbackMessage($response['message'],$response['status']);
        }
        return AjaxCallbackMessage('api接口不再维护',false);
    }

    /**
     * 取消对客户的时间保护 zhaoyf
     *
     * @param CustomerRequest   $request
     * @internal param 经济人ID  $agent_id
     * @internal param 客户ID    $customer_id
     * @internal param int       $protect_result
     *
     * @return bool
     */
    public function postIfProtect(CustomerRequest $request)
    {
        $result = $request->input();

        if (isset($result['protect_result']) && $result['protect_result'] === "0") {
           $change_result = AgentCustomer::where('agent_id', $result['agent_id'])
                ->where('uid', $result['customer_id'])
                ->update(['protect_time' => 0]);

           if ($change_result) {
               return AjaxCallbackMessage('取消成功', true);
           } else {
               return AjaxCallbackMessage('没有发生更新变化', false);
           }
        } else {
            return AjaxCallbackMessage('传递参数有误', false);
        }
    }

    /**
     * 客户详情 zhaoyf
     *
     * @param CustomerRequest $request
     * @param null $version
     * @return string
     * @internal param 经济人ID $agent_id
     * @internal param 投资人ID $customer_id return detail_data*
     *
     */
    public function postDetailInfos(CustomerRequest $request, $version = null)
    {
        $result = $request->input();
        $versionService = $this->init(__METHOD__, $version, null, 'Agent');
        if ($versionService) {
            $response = $versionService->bootstrap($result, ['request' => $request]);

            return AjaxCallbackMessage($response['message'], $response['status']);
        } else {
            return AjaxCallbackMessage('改接口不再维护', false);
        }
    }

    /**
     * 客户详情--跟进品牌页 zhaoyf
     *
     * @param CustomerRequest $request
     * @param null $version
     * @return array
     * @internal param 经纪人ID $agent_id
     * @internal param 经济人ID $customer_id
     *
     */
    public function postDetailBrands(CustomerRequest $request, $version = null)
    {
        //新增加1.0.2版本
        $result = $request->input();
        $versionService = $this->init(__METHOD__, $version, null, 'Agent');

        if ($versionService) {
            $response = $versionService->bootstrap($result, ['request' => $request]);

            return AjaxCallbackMessage($response['message'], $response['status']);
        } else {
            return AjaxCallbackMessage('改接口不再维护', false);
        }
    }

    /**
     * 客户详情--跟单备注 zhaoyf
     *
     * @param CustomerRequest $request
     * @return data_list
     * @internal param 经纪人ID $agent_id
     * @internal param 投资人ID $customer_id
     *
     */
    public function postDetailRemarks(CustomerRequest $request)
    {
        $result = $request->input();

        $get_result = AgentCustomerLog::with('belongsToAgentCustomer', 'hasOneBrand')
            ->where('agent_id', $result['agent_id'])
            ->where('uid', $result['customer_id'])
            ->where('action', self::REMARK_TYPE)
            ->where('is_delete', 0)
            ->whereIn('post_id', [0, 12])
            ->orderBy('created_at', 'desc')
            ->get()
            ->toArray();

        //对查询结果进行判断
        if (!$get_result) return AjaxCallbackMessage(['message' => '没有查询到对应的备注信息', 'totals' => 0], true);

        //获取总的备注个数
        $detail_data = AgentCustomerLog::where('uid', $result['customer_id'])
            ->where('agent_id', $result['agent_id'])
            ->where('action', self::REMARK_TYPE)
            ->where('is_delete', 0)
            ->get();
        $detail_remark_num = 0;
        foreach ($detail_data as $key => $vls) {
            if ( empty($vls->remark) || !isset($vls->remark)) {
                continue;
            } else { ++$detail_remark_num; }
        }

        //循环数据进行整合
        $detail_remarks_result = array_map(function ($res) {

                return [
                    'id'             => $res['id'],
                    'month'          => date("m月份", $res['created_at']),
                    'content'        => $res['remark'],
                    'brand_title'    => $res['has_one_brand']['name'],
                    'level_describe' => AgentCustomer::$customerLevel[$res['belongs_to_agent_customer']['level']],
                    'level'          => $res['belongs_to_agent_customer']['level'],
                    'created_at'     => $res['created_at'],
                ];

            }, $get_result);

        //对月份相同的数据进行归类处理
        $array = array();
        foreach ($detail_remarks_result as $key => $vls) {
            $array[$vls['month']]['month']          = $vls['month'];
            $array[$vls['month']]['remark_list'][]  = $detail_remarks_result[$key];
        }
        rsort($array);

        return AjaxCallbackMessage(['totals' => $detail_remark_num, 'list' => $array], true);
    }

    /**
     * 客户详情--考察邀请 zhaoyf
     *
     * @param CustomerRequest $request
     * @return string
     */
    public function postInspectInvite(CustomerRequest $request)
    {
        $result = $request->input();

        $agent_customer = new AgentCustomer();
        $result_list    = $agent_customer->inspectInvites($result);

        return AjaxCallbackMessage($result_list, true);
    }

    /**
     * author zhaoyf
     *
     * 客户详情 -- 意向记录 1.0.2 版本； customer-intention-record
     *
     * @param CustomerRequest|Request $request
     * @param null $version 版本号
     *
     * @return string 返回结果
     */
    public function postCustomerIntentionRecord(CustomerRequest $request, $version = NULL)
    {
        $result = $request->input();
        $versionService = $this->init(__METHOD__, $version, null, 'Agent');

        if ($versionService) {
            $response = $versionService->bootstrap($result, ['request' => $request]);

            return AjaxCallbackMessage($response['message'], $response['status']);
        }

        return AjaxCallbackMessage('该接口不再维护', false);
    }

    /**
     * 跟进情况--考察邀请 zhaoyf
     *
     * @param CustomerRequest $request
     * @return string
     */
    public function postRecordsInspect(CustomerRequest $request)
    {
        $result = $request->input();

        $agent_customer = new AgentCustomer();
        $result_list    = $agent_customer->recordsInspects($result);

        if (!empty($result_list['message'])) {
            return AjaxCallbackMessage($result_list['message'], $result_list['status']);
        }

        return AjaxCallbackMessage($result_list, true);
    }

    /**
     * 添加客户备注 zhaoyf
     *
     * @param CustomerRequest|MessageRequest $request
     * @param null $version
     * @return string
     */
    public function postAddRemark(MessageRequest $request, $version = null)
    {
        $result         = $request->input();
        $versionService = $this->init(__METHOD__, $version, null, 'Agent');

        if ($versionService) {
            $response = $versionService->bootstrap($result, ['request' => $request]);

            return AjaxCallbackMessage($response['message'], $response['status']);
        }

        return AjaxCallbackMessage('该接口不存在', false);
    }

    /**
     * author zhaoyf
     *
     * 添加通过分享过来的客户与经纪人的数据信息
     *
     * @param CustomerRequest $request
     * @param null $version
     * @internal param $param
     *
     * @return string
     */
    public function postAddShareCustomerInfo(CustomerRequest $request, $version = null)
    {
        $result         = $request->input();
        $versionService = $this->init(__METHOD__, $version, null, 'Agent');

        if ($versionService) {
            $response = $versionService->bootstrap($result, ['request' => $request]);

            return AjaxCallbackMessage($response['message'], $response['status']);
        }

        return AjaxCallbackMessage('该接口不存在', false);
    }

    /**
     * 修改客户信息 （姓名备注，等级）zhaoyf
     *
     * @param CustomerRequest $request
     * @param tags      编辑标记（编辑时，tags=edits；提交编辑时，tags=submit_edits）
     * @internal param 客户ID $customer_id
     * @internal param 经纪人ID $agent_id
     *
     * @return string | bool
     */
    public function postEditName(CustomerRequest $request)
    {
        $result = $request->input();

        if (empty($result['tags']) || !isset($result['tags'])) {
            return AjaxCallbackMessage('缺少编辑标记：tags', false);
        }

        //tags等于edits时，获取用等级返回
        if ($result['tags'] && $result['tags'] === "edits") {
           $gain_result = AgentCustomer::where('agent_id', $result['agent_id'])
                ->where('uid', $result['customer_id'])
                ->select('id', 'remark', 'level')
                ->first();

         $result_data['remark']         = $gain_result->remark;
         $result_data['current_level']  = AgentCustomer::$customerLevel[$gain_result->level];
         $result_data['current_levels'] = $gain_result->level;
         $result_data['level']          = AgentCustomer::$customerLevel;

          return AjaxCallbackMessage($result_data, true);

        //tags=submit_edits时，提交编辑
        } elseif ($result['tags'] && $result['tags'] === "submit_edits") {

            //返回编辑后的结果
            $change_result = AgentCustomer::where('agent_id', $result['agent_id'])
                ->where('uid', $result['customer_id'])
                ->update(
                        ['remark' => $result['remark'],
                        'level'   => $result['level'] ]);

            //对结果进行处理
            if ($change_result) {
                return AjaxCallbackMessage('编辑成功', true);
            } else {
                return AjaxCallbackMessage('编辑失败', false);
            }
        }
    }

    //需要活动提醒的客户
    public function postActivityRemind(Request $request, $version = NULL){
        $versionService = $this->init(__METHOD__, $version, null, 'Agent');
        if($versionService){
            $response = $versionService->bootstrap($request->all());
            return AjaxCallbackMessage($response['message'],$response['status']);
        }
        return AjaxCallbackMessage('api接口不再维护',false);
    }

    //需要门店考察提醒的客户
    public function postInspectRemind(Request $request, $version = NULL) {
        $versionService = $this->init(__METHOD__, $version, null, 'Agent');
        if($versionService){
            $response = $versionService->bootstrap($request->all());
            return AjaxCallbackMessage($response['message'],$response['status']);
        }
        return AjaxCallbackMessage('api接口不再维护',false);
    }

    //需要门店考察提醒的客户
    public function postProtected(Request $request, $version = NULL){
        $versionService = $this->init(__METHOD__, $version, null, 'Agent');
        if($versionService){
            $response = $versionService->bootstrap($request->all());
            return AjaxCallbackMessage($response['message'],$response['status']);
        }
        return AjaxCallbackMessage('api接口不再维护',false);
    }

    //客户详情-活动邀请
    public function postActivityInvite(Request $request, $version = NULL){
        $versionService = $this->init(__METHOD__, $version, null, 'Agent');
        if($versionService){
            $response = $versionService->bootstrap($request->all());

            return AjaxCallbackMessage($response['message'],$response['status']);
        }
        return AjaxCallbackMessage('api接口不再维护',false);
    }


    public function postRecordsActivity(Request $request, $version = NULL){
        $versionService = $this->init(__METHOD__, $version, null, 'Agent');
        if($versionService){
            $response = $versionService->bootstrap($request->all());

            return AjaxCallbackMessage($response['message'],$response['status']);
        }
        return AjaxCallbackMessage('api接口不再维护',false);
    }

    public function postSearch(Request $request, $version = NULL){
        $versionService = $this->init(__METHOD__, $version, null, 'Agent');
        if($versionService){
            $response = $versionService->bootstrap($request->all());
            return AjaxCallbackMessage($response['message'],$response['status']);
        }
        return AjaxCallbackMessage('api接口不再维护',false);
    }

    public function postRecordsAll(Request $request, $version = NULL){
        $versionService = $this->init(__METHOD__, $version, null, 'Agent');
        if($versionService){
            $response = $versionService->bootstrap($request->all());

            return AjaxCallbackMessage($response['message'],$response['status']);
        }
        return AjaxCallbackMessage('api接口不再维护',false);
    }

    /*
     *
     * 编辑客户跟单日志
     *
     * */
    public function postEditRemark(Request $request, $version = NULL){
        $versionService = $this->init(__METHOD__, $version, null, 'Agent');
        if($versionService){
            $response = $versionService->bootstrap($request->all());
            return AjaxCallbackMessage($response['message'],$response['status']);
        }
        return AjaxCallbackMessage('api接口不再维护',false);
    }

    /*
     *
     * 删除客户跟单日志
     *
     * */
    public function postDeleteRemark(Request $request, $version = NULL){
        $versionService = $this->init(__METHOD__, $version, null, 'Agent');
        if($versionService){
            $response = $versionService->bootstrap($request->all());
            return AjaxCallbackMessage($response['message'],$response['status']);
        }
        return AjaxCallbackMessage('api接口不再维护',false);
    }

    /*
     * 创建品牌加盟函s1
     */
    public function postContractStep1(Request $request, $version = null)
    {
        $versionService = $this->init(__METHOD__, $version, null, 'Agent');
        if($versionService){
            $response = $versionService->bootstrap($request->all());
            return AjaxCallbackMessage($response['message'],$response['status']);
        }
        return AjaxCallbackMessage('api接口不再维护',false);
    }

    /*
    * 创建品牌加盟函s2
    */
    public function postContractStep2(Request $request, $version = null)
    {
        $versionService = $this->init(__METHOD__, $version, null, 'Agent');
        if($versionService){
            $response = $versionService->bootstrap($request->all());
            return AjaxCallbackMessage($response['message'],$response['status']);
        }
        return AjaxCallbackMessage('api接口不再维护',false);
    }

    /*
    * 创建品牌加盟函s3
    */
    public function postContractStep3(Request $request, $version = null)
    {
        $versionService = $this->init(__METHOD__, $version, null, 'Agent');
        if($versionService){
            $response = $versionService->bootstrap($request->all());
            return AjaxCallbackMessage($response['message'],$response['status']);
        }
        return AjaxCallbackMessage('api接口不再维护',false);
    }

    /*
    * 创建品牌加盟函s4
    */
    public function postContractStep4(Request $request, $version = null)
    {
        $versionService = $this->init(__METHOD__, $version, null, 'Agent');
        if($versionService){
            $response = $versionService->bootstrap($request->all());
            return AjaxCallbackMessage($response['message'],$response['status']);
        }
        return AjaxCallbackMessage('api接口不再维护',false);
    }
    /*
     * 创建品牌加盟函s3客户列表
     */
    public function postCustomerList(Request $request, $version = null)
    {
        $versionService = $this->init(__METHOD__, $version, null, 'Agent');
        if($versionService){
            $response = $versionService->bootstrap($request->all());
            return AjaxCallbackMessage($response['message'],$response['status']);
        }
        return AjaxCallbackMessage('api接口不再维护',false);
    }
    /*
     * 创建品牌加盟函s4等待客户确认的
     */
    public function postWaitConfirm(Request $request, $version = null)
    {
        $versionService = $this->init(__METHOD__, $version, null, 'Agent');
        if($versionService){
            $response = $versionService->bootstrap($request->all());
            return AjaxCallbackMessage($response['message'],$response['status']);
        }
        return AjaxCallbackMessage('api接口不再维护',false);
    }
}