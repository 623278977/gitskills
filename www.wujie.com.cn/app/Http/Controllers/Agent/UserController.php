<?php namespace App\Http\Controllers\Agent;

use App\Http\Controllers\Controller;
use App\Http\Requests\Agent\AgentRequest;
use App\Http\Requests\Agent\Bank\BankRequest;
use App\Http\Requests\Agent\BankCardRequest;
use App\Http\Requests\Agent\InspectInviteRequest;
use App\Http\Requests\Agent\User\BusinessBrandRequest;
use App\Http\Requests\Agent\User\InspectAuditRequest;
use App\Http\Requests\Agent\User\InspectOrderRequest;
use App\Http\Requests\Agent\User\LeagueOrderRequest;
use App\Http\Requests\Agent\User\PersonListRequest;
use App\Http\Requests\Agent\User\ShowAgentRelationRequest;
use App\Models\Agent\AgentAchievement;
use App\Models\Agent\AgentBankCard;
use App\Models\Agent\AgentCurrencyLog;
use App\Models\Agent\AgentWithdraw;
use App\Models\Agent\CommissionLevelTemplate;
use App\Models\Agent\Invitation;
use App\Models\Contract\Contract;
use Illuminate\Http\Request;
use App\Models\Agent\Agent;
use App\Models\Brand\Entity as BrandModel;
use DB, Input;
use App\Models\Brand\Entity\V020800 as BrandAgent;
use App\Models\Agent\AgentBrand;
use App\Http\Controllers\Api\CommonController;
use App\Http\Requests\Agent\AgentShareLike;
use App\Http\Requests\User\GetKeywordRequest;
use App\Http\Requests\User\CustomerRegisterRequest;
use App\Http\Requests\User\SubordinateRequest;
use App\Http\Requests\Agent\User\RegisterCustomerResult;
use App\Http\Requests\Agent\User\PhoneInviteRequest;
/**
 * 我的首页接口——石清源
 */
class UserController extends CommonController
{
    public $timestamps = true;

    //我的界面详情
    public function postIndex(Request $request,$version = null)
    {
        $versionService = $this->init(__METHOD__, $version, null, 'Agent');
        if($versionService){
            $response = $versionService->bootstrap($request->all());

            return AjaxCallbackMessage($response['message'],$response['status']);
        }
        return AjaxCallbackMessage('api接口不再维护',false);
    }

//    //获取指定表中指定字段
    public function postTableValue(Request $request,$version = null)
    {
        $versionService = $this->init(__METHOD__, $version, null, 'Agent');
        if($versionService){
            $response = $versionService->bootstrap($request->all());

            return AjaxCallbackMessage($response['message'],$response['status']);
        }
        return AjaxCallbackMessage('api接口不再维护',false);
    }

//    //获得经纪人邀请码
//    public function postGetAgentInvite(Request $request,$version = null){
//        $versionService = $this->init(__METHOD__, $version, null, 'Agent');
//        if($versionService){
//            $response = $versionService->bootstrap($request->all());
//
//            return AjaxCallbackMessage($response['message'],$response['status']);
//        }
//        return AjaxCallbackMessage('api接口不再维护',false);
//    }


    //编辑个人（经纪人）信息
    public function postEdit(Request $request,$version = null)
    {
        $versionService = $this->init(__METHOD__, $version, null, 'Agent');
        if($versionService){
            $response = $versionService->bootstrap($request->all());

            return AjaxCallbackMessage($response['message'],$response['status']);
        }
        return AjaxCallbackMessage('api接口不再维护',false);
    }

    /*
     * 经纪人意见反馈接口
     * */
    public function postAgentFeedback(Request $request,$version = null){
        $versionService = $this->init(__METHOD__, $version, null, 'Agent');
        if($versionService){
            $response = $versionService->bootstrap($request->all());

            return AjaxCallbackMessage($response['message'],$response['status']);
        }
        return AjaxCallbackMessage('api接口不再维护',false);
    }

    public function postCard(Request $request,$version = null)
    {
        $versionService = $this->init(__METHOD__, $version, null, 'Agent');
        if($versionService){
            $response = $versionService->bootstrap($request->all());

            return AjaxCallbackMessage($response['message'],$response['status']);
        }
        return AjaxCallbackMessage('api接口不再维护',false);
    }

    public function postDetail(Request $request,$version = null)
    {
        $agentId = intval($request->input('agent_id'));
        if (!empty($agentId)) {
            $agentInfo = Agent::getAgentDetail($agentId);
            if ($agentInfo !== false) {
                return AjaxCallbackMessage($agentInfo, true);
            }
            return AjaxCallbackMessage("获取失败", false);
        } else {
            return AjaxCallbackMessage("请传递用户id", false);
        }
    }

    /**
     * 经济人二维码实现 zhaoyf   --弃用   数据中心不处理
     */
    public function postQrcode(Request $request)
    {
        $result = $request->input('agent_id', '');

        if (!$result) {
            return AjaxCallbackMessage('缺少经济人ID：agent_id', false);
        }

        $qrcode = Agent::instance()->createQrCode($result);

        return AjaxCallbackMessage($qrcode, true);
    }

    /**
     * 经纪人提现
     *
     * @param Request $request
     * @return string
     * @internal param 经济人ID $agent_id
     * @internal param 银行名称 $bank_name
     * @internal param 银行卡号 $account
     * @internal param 金额 $currency return bool*
     * return bool
     */
    public function postWithdraw(Request $request)
    {
         return DB::transaction(function() use($request)  {

            $result = $request->input();

            if (empty($result['agent_id']) || !isset($result['agent_id'])) {
                $notice_info = '缺少经纪人ID';
            } elseif (empty($result['bank_name']) || !isset($result['bank_name'])) {
                $notice_info = '缺少银行名称';
            } elseif (empty($result['account']) || !isset($result['account'])) {
                $notice_info = '请选择银行卡';
            } elseif (empty($result['currency']) || !isset($result['currency'])) {
                $notice_info = '缺少提现金额';
            }  elseif (!Agent::where('status', 1)->where('id', $result['agent_id'])->where('is_verified', 1)->first()) {
                $notice_info = '异常';
            }else {
                //判断经纪人提现的金额是否小于拥有的金额
                $agent_data = Agent::where('id', $result['agent_id'])->first();
                if ($agent_data->currency < abondonComma($result['currency'])) {
                    return AjaxCallbackMessage('提现额度超过了拥有的余额', false);
                }

                //黑名单或者内容经纪人不能体现 zhaoyf 备注于12-4 todo tangjb加于11-20日
                if ($agent_data->status == -1) {
                    return AjaxCallbackMessage('提现失败', false);
                }

                $fee = $this->fee(abondonComma($result['currency']));
                $currency = $agent_data->currency-abondonComma($result['currency']);



                //组织数据
                $data = [
                    'agent_id'    => $result['agent_id'],
                    'bank_name'   => $result['bank_name'],
                    'account'     => $result['account'],
                    'money'       => abondonComma($result['currency']),
                    'created_at'  => time(),
                    'updated_at'  => time(),
                    'withdraw_no' => $this->_withdraw_num($result['agent_id']),
                    'fee' => $fee,
                    'currency' => $currency,
                ];

                $create_result = AgentWithdraw::insertGetId($data);
                if ($create_result) {
                    //提现成功往提现日记表插入数据
                    $insert_data = [
                        'agent_id'   => $result['agent_id'],
                        'operation'  => -1,
                        'num'        => abondonComma($result['currency']),
                        'type'       => 1,
                        'post_id'    => $create_result,
                        'created_at' => time(),
                        'updated_at' => time(),
                        'status'     => 1,
                        'currency' => $currency,
                    ];
                    AgentCurrencyLog::insert($insert_data);

                    //减掉余额
                    Agent::where('id', $result['agent_id'])->decrement('currency', abondonComma($result['currency']));

                    return AjaxCallbackMessage('提现申请成功', true);
                } else {
                    return AjaxCallbackMessage('提现申请失败', false);
                }
            }

            return AjaxCallbackMessage($notice_info, false);
        });
    }


    private  function fee($money)
    {
        if($money<200){
            $fee = 10;
        }else{
            $fee = 0;
        }

        return $fee;
    }

    /**
     * 内部调用--根据当前时间和经纪人ID和提现单数生成提现的流水单号
     *
     * @param $agent_id
     * @return string
     */
    private function _withdraw_num($agent_id)
    {
        $start = strtotime(date('Y-m-d 00:00:00'));
        $end   = strtotime(date('Y-m-d H:i:s'));
        $withdraw_num = AgentWithdraw::where('agent_id', $agent_id)
            ->where('created_at', '>', $start)
            ->where('created_at', '<', $end)
            ->count();

       return date("YmdHi") . "000" . $agent_id . "0000" . ($withdraw_num + 1);
    }

    /**
     * 银行卡识别信息获取
     * @param Request $request
     * @return string
     */
    public function postGainBankcard(Request $request)
    {
        $result = $request->input();

        if (empty($result['bank_img']) || !isset($result['bank_img'])) {
            return AjaxCallbackMessage('缺少银行卡图片信息：bank_img', false);
        }
//        \Log::info(json_encode($result));

        $get_result = getBankCardInfo($result['bank_img']);
//        \Log::info(json_encode($get_result));
        if (is_null($result)) {
            return AjaxCallbackMessage('银行卡识别失败', false);
        } elseif (is_string($get_result) && $get_result === "data_null") {
            return AjaxCallbackMessage('银行卡识别失败', false);
        }

        $data = [
            'bank_name' => trim($get_result['cardname']),
            'card_no' => str_replace(' ', '', $get_result['cardnumber']),
            'card_type' => trim($get_result['cardtype']),
        ];

//        \Log::info(json_encode($data));

        return AjaxCallbackMessage($data, true);
    }

    /**
     * 获取添加银行卡信息
     *
     * return bool
     * @param BankCardRequest $request
     * @return string
     */
    public function postAddBankcard(BankCardRequest $request)
    {
        $result = $request->input();

        if ($result['card']) {
            if (Agent::where('id', $request['agent_id'])->first()->realname !== $result['card']) {
                return AjaxCallbackMessage('请填写当前登录账号的真实姓名', false);
            }
        }

        //对当前添加的银行卡进行判断处理
        $check_result = AgentBankCard::where('agent_id', $result['agent_id'])
            ->where('card_no', trim($result['card_no']))
            ->where('is_delete', 0)
            ->first();

        if (is_object($check_result)) {
            return AjaxCallbackMessage('该银行卡已经存在，请换一张银行卡', false);
        }

        //组织数据进行添加
        $data = [
            'agent_id'  => $result['agent_id'],
            'bank_name' => trim($result['bank_name']),
            'card_no'   => $result['card_no'],
            'card_type' => trim($result['card_type']),
            'open_band_name' => trim($result['bank_subname']),
            'account_name'   => $result['card'] ?  trim($result['card']) : ""
        ];

        //对添加的结果进行处理
        $add_result = AgentBankCard::insert($data);
        if ($add_result) {
            return AjaxCallbackMessage('添加银行卡成功', true);
        } else {
            return AjaxCallbackMessage('添加银行卡失败', false);
        }
    }

    /**
     * author zhaoyf
     *
     * 经纪人删除银行卡 agent-delete-banks
     */
    public function postAgentDeleteBanks(BankRequest $request,$version = null)
    {
        $versionService = $this->init(__METHOD__, $version, null, 'Agent');

        if ($versionService) {
            $response = $versionService->bootstrap($request->all());

            return AjaxCallbackMessage($response['message'],$response['status']);
        }

        return AjaxCallbackMessage('api接口不再维护', false);
    }

    /**
     * 我的银行卡列表
     *
     * @param Request $request
     * @return string
     * @internal param $agent_id return card_list*
     */
    public function postBankcardList(Request $request)
    {
        $result = $request->input('agent_id', '');
        if (!isset($result) || empty($result)) {
            return AjaxCallbackMessage('缺少经济人ID：agent_id', false);
        }
        if (!is_numeric($result)) {
            return AjaxCallbackMessage('经济人ID只能是整数', false);
        }

        //获取指定经济人添加的银行卡
        $card_list = AgentBankCard::where('agent_id', $result)
            ->where('is_delete', 0)
            ->select('id as bank_id', 'bank_name', 'card_no', 'card_type')
            ->get()->toArray();

        //对获取的结果进行处理
        if (!$card_list) {
            return AjaxCallbackMessage('您还没有添加任何银行卡', false);
        } else {
            return AjaxCallbackMessage($card_list, true);
        }

    }

    //我的等级
    public function postLevel(Request $request,$version = null)
    {
        $versionService = $this->init(__METHOD__, $version, null, 'Agent');
        if($versionService){
            $response = $versionService->bootstrap($request->all());

            return AjaxCallbackMessage($response['message'],$response['status']);
        }
        return AjaxCallbackMessage('api接口不再维护',false);
    }

    /**
     * 创建邀请函
     *
     * @param AgentRequest $request
     * @param null $version
     * @return string
     */
    public function postCreateInvitation(AgentRequest $request, $version = null)
    {
        $result = $request->input();

        $versionService = $this->init(__METHOD__, $version, null, 'Agent');
        if ($versionService) {
            $response = $versionService->bootstrap($result, ['request' => $request]);

            return AjaxCallbackMessage($response['message'], $response['status']);
        }
        return AjaxCallbackMessage('该接口不存在', false);
    }

    /**
     * 获取考察邀请函（1：接受，0：待确认，-1：拒绝）
     *
     * @param InspectInviteRequest $request
     * @return string
     */
    public function postInspectInvitation(InspectInviteRequest $request)
    {
        $result = $request->input();

        if (empty($result['page'])      || !isset($result['page'])) $result['page'] = 1;
        if (empty($result['page_size']) || !isset($result['page_size'])) $result['page_size'] = 10;

        $results = Agent::instance()->getAgentInvites($result);

        return AjaxCallbackMessage($results, true);

    }

    //我的下线
    public function postSubordinate(SubordinateRequest $request,$version = null)
    {
        $versionService = $this->init(__METHOD__, $version, null, 'Agent');
        if($versionService){
            $response = $versionService->bootstrap($request->all());

            return AjaxCallbackMessage($response['message'],$response['status']);
        }
        return AjaxCallbackMessage('api接口不再维护',false);
    }

    //团队业绩
    public function postTeamSales(Request $request,$version = null)
    {
        $versionService = $this->init(__METHOD__, $version, null, 'Agent');
        if($versionService){
            $response = $versionService->bootstrap($request->all());

            return AjaxCallbackMessage($response['message'],$response['status']);
        }
        return AjaxCallbackMessage('api接口不再维护',false);
    }

    /**
     * 经纪人代理品牌列表
     *
     * @param Request $request
     * @param null $version
     * @return string
     */
    public function postAgentBrands(Request $request, $version = null)
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
     * 经纪人申请代理中的品牌列表
     *
     * @param Request $request
     * @param null $version
     * @return string
     */
    public function postApplyBrands(Request $request, $version = null)
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
     * 经纪人电子合同概览
     */
    public function postContract(Request $request, $version = null)
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
     * 经纪人电子合同详情列表
     */
    public function postContractDetail(Request $request, $version = null)
    {
        $agent_id = $request->get('agent_id');
        if (empty($agent_id)) {
            return AjaxCallbackMessage('缺少经纪人id', false);
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

    //业绩明细
    public function postSalesDetail(Request $request,$version = null)
    {
        $versionService = $this->init(__METHOD__, $version, null, 'Agent');
        if($versionService){
            $response = $versionService->bootstrap($request->all());

            return AjaxCallbackMessage($response['message'],$response['status']);
        }
        return AjaxCallbackMessage('api接口不再维护',false);
    }

    //活动邀请函
    public function postActivityInvitation(Request $request,$version = null)
    {
        $versionService = $this->init(__METHOD__, $version, null, 'Agent');
        if($versionService){
            $response = $versionService->bootstrap($request->all());

            return AjaxCallbackMessage($response['message'],$response['status']);
        }
        return AjaxCallbackMessage('api接口不再维护',false);
    }


    /**
     * 我的佣金
     */
    public function postMyCommission(Request $request, $version = null)
    {
        $data = $request->input();

        if (empty($data['agent_id'])) {
            return AjaxCallbackMessage("请传递经纪人id", false);
        }

        $versionService = $this->init(__METHOD__, $version, null, 'Agent');

        if ($versionService) {
            $response = $versionService->bootstrap($request->all());

            return AjaxCallbackMessage($response['message'], $response['status']);
        }

        return AjaxCallbackMessage('api接口不再维护', false);
    }


    /**
     * 佣金详情
     */
    public function postCommissionDetail(Request $request, $version = null)
    {
        $data = $request->input();

        if (empty($data['type'])) {
            return AjaxCallbackMessage("请传递类型参数", false);
        }

        if (empty($data['id'])) {
            return AjaxCallbackMessage("请传递id参数", false);
        }

        $versionService = $this->init(__METHOD__, $version, null, 'Agent');

        if ($versionService) {
            $response = $versionService->bootstrap($request->all());

            return AjaxCallbackMessage($response['message'], $response['status']);
        }

        return AjaxCallbackMessage('api接口不再维护', false);
    }

    /*
     * 判断一个手机号是否可以被邀请成投资人或经纪人
     *
     * */
    public function postCanInvite(Request $request, $version = null)
    {
        $phoneArr = $request->input('mobile');
        if (empty($phoneArr)) {
            return AjaxCallbackMessage('手机号不能为空', false);
        }
        $type = trim($request->input('type'));
        if (empty($type)) {
            return AjaxCallbackMessage('请输入调用类型', false);
        }
        if (!in_array($type, [1, 2])) {
            return AjaxCallbackMessage('请输入正确的调用类型', false);
        }

        $versionService = $this->init(__METHOD__, $version, null, 'Agent');

        if ($versionService) {
            $response = $versionService->bootstrap($request->all());

            return AjaxCallbackMessage($response['message'], $response['status']);
        }

        return AjaxCallbackMessage('api接口不再维护', false);
    }


    /**
     * 获取季度业绩
     */
    public function postQuarterAchievement(Request $request, $version = null)
    {
        $data = $request->input();

        if (empty($data['quarter'])) {
            return AjaxCallbackMessage("请传递quarter参数", false);
        }

        if (empty($data['agent_id'])) {
            return AjaxCallbackMessage("请传递agent_id参数", false);
        }

        $versionService = $this->init(__METHOD__, $version, null, 'Agent');

        if ($versionService) {
            $response = $versionService->bootstrap($request->all());

            return AjaxCallbackMessage($response['message'], $response['status']);
        }


        return AjaxCallbackMessage('api接口不再维护', false);
    }

    /*
     *
     * shiqy
     * 邀请口号
     *
     * */

    public function postInviteSlogan(Request $request, $version = null){
        $versionService = $this->init(__METHOD__, $version, null, 'Agent');
        if($versionService){
            $response = $versionService->bootstrap($request->all());
            return AjaxCallbackMessage($response['message'],$response['status']);
        }
        return AjaxCallbackMessage('api接口不再维护',false);
    }

    /*shiqy
     * 被邀请的情况下注册成经纪人
     * */

    public function postAgentRegister(Request $request, $version = null){
        $versionService = $this->init(__METHOD__, $version, null, 'Agent');
        if($versionService){
            $response = $versionService->bootstrap($request->all());
            return AjaxCallbackMessage($response['message'],$response['status']);
        }
        return AjaxCallbackMessage('api接口不再维护',false);
    }

    /*shiqy
    * 被邀请的情况下注册成投资人
    * */

    public function postCustomerRegister(CustomerRegisterRequest $request, $version = null){
        $versionService = $this->init(__METHOD__, $version, null, 'Agent');
        if($versionService){
            $response = $versionService->bootstrap($request->all());
            return AjaxCallbackMessage($response['message'],$response['status']);
        }
        return AjaxCallbackMessage('api接口不再维护',false);
    }


    /**
     * 佣金记录
     *
     * @param Request $request
     * @param null $version
     * @return string
     * @author tangjb
     */
    public function postCommissionRecords(Request $request, $version = null)
    {
        $data = $request->input();


        if (empty($data['agent_id'])) {
            return AjaxCallbackMessage("请传递agent_id参数", false);
        }

        $versionService = $this->init(__METHOD__, $version, null, 'Agent');

        if ($versionService) {
            $response = $versionService->bootstrap($request->all());

            return AjaxCallbackMessage($response['message'], $response['status']);
        }

        return AjaxCallbackMessage('api接口不再维护', false);
    }

    /*shiqy
     * 判断投资人或经纪人是否注册过
     * */
    public function postIsregister(Request $request, $version = null){
        $versionService = $this->init(__METHOD__, $version, null, 'Agent');
        if($versionService){
            $response = $versionService->bootstrap($request->all());
            return AjaxCallbackMessage($response['message'],$response['status']);
        }
        return AjaxCallbackMessage('api接口不再维护',false);
    }

    /*shiqy
     * 撤销考察、活动邀请函
     * */
    public function postBackOut(Request $request, $version = null){
        $versionService = $this->init(__METHOD__, $version, null, 'Agent');
        if($versionService){
            $response = $versionService->bootstrap($request->all());
            return AjaxCallbackMessage($response['message'],$response['status']);
        }
        return AjaxCallbackMessage('api接口不再维护',false);
    }

    /*shiqy
     * 设置经纪人信息是否公开
     * */
    public function postAgentPublic(Request $request, $version = null){
        $validator = \Validator::make($request->all(),[
            'agent_id' => 'required|exists:agent,id',
            'is_public_realname' => 'in:0,1',
        ]);
        if($validator->fails()){
            $warnings = $validator->messages();
            $show_warning = $warnings->first();
            return ['message'=>$show_warning ,'status'=>false];
        }
        $agentId = intval($request->input('agent_id'));
        $rel = self::editList($request,Agent::class,'id',$agentId,['is_public_realname']);
        if($rel){
            return AjaxCallbackMessage('修改成功',true);
        }
        return AjaxCallbackMessage('修改失败',false);
    }

    /*
     * 获取所有关键词
     * */
    public function postKeywords(GetKeywordRequest $request,$version = null)
    {
        $versionService = $this->init(__METHOD__, $version, null, 'Agent');
        if($versionService){
            $response = $versionService->bootstrap($request->all());

            return AjaxCallbackMessage($response['message'],$response['status']);
        }
        return AjaxCallbackMessage('api接口不再维护',false);
    }

    /*
    * 在经纪人分享页，点赞提交接口
    * */
    public function postShareLike(AgentShareLike $request,$version = null)
    {
        $versionService = $this->init(__METHOD__, $version, null, 'Agent');
        if($versionService){
            $response = $versionService->bootstrap($request->all());

            return AjaxCallbackMessage($response['message'],$response['status']);
        }
        return AjaxCallbackMessage('api接口不再维护',false);
    }



    /*
* 在经纪人我的门票列表
* */
    public function postUserticketlist(Request $request,$version = null)
    {
        $versionService = $this->init(__METHOD__, $version, null, 'Agent');
        if($versionService){
            $response = $versionService->bootstrap($request->all());


            if ($response['message']) {
                return AjaxCallbackMessage($response['message'], $response['status']);
            }else {
                return json_encode(['message'=>new \stdClass(), 'status' => true]);
            }
        }
        return AjaxCallbackMessage('api接口不再维护',false);
    }

    //邀请注册投资人返回接口
    public function postRegisterCustomerResult(RegisterCustomerResult $request,$version = null){
        $versionService = $this->init(__METHOD__, $version, null, 'Agent');
        if($versionService){
            $response = $versionService->bootstrap($request->all());
            if ($response['message']) {
                return AjaxCallbackMessage($response['message'], $response['status']);
            }else {
                return json_encode(['message'=>new \stdClass(), 'status' => true]);
            }
        }
        return AjaxCallbackMessage('api接口不再维护',false);
    }


    /**
     * shiqy
     * 投资人领取红包接口
     */
    public function postCustomReceiveRedpacket(Request $request,$version = null){
        $versionService = $this->init(__METHOD__, $version, null, 'Agent');
        if($versionService){
            $response = $versionService->bootstrap($request->all());
            if ($response['message']) {
                return AjaxCallbackMessage($response['message'], $response['status']);
            }else {
                return json_encode(['message'=>new \stdClass(), 'status' => true]);
            }
        }
        return AjaxCallbackMessage('api接口不再维护',false);
    }


    /**
     * 经纪人红包列表
     */
    public function postPackageList(Request $request,$version = null){
        $versionService = $this->init(__METHOD__, $version, null, 'Agent');
        if($versionService){
            $response = $versionService->bootstrap($request->all());

            return AjaxCallbackMessage($response['message'],$response['status']);
        }
        return AjaxCallbackMessage('api接口不再维护',false);
    }

    /**
     * 经纪人红包详情
     */
    public function postPackageDetail(Request $request,$version = null){
        $versionService = $this->init(__METHOD__, $version, null, 'Agent');
        if($versionService){
            $response = $versionService->bootstrap($request->all());

            return AjaxCallbackMessage($response['message'],$response['status']);
        }
        return AjaxCallbackMessage('api接口不再维护',false);
    }


    /**
     * 经纪人集赞截屏上传
     */
    public function postScreenCapture(Request $request,$version = null){
        $versionService = $this->init(__METHOD__, $version, null, 'Agent');
        if($versionService){
            $response = $versionService->bootstrap($request->all());

            return AjaxCallbackMessage($response['message'],$response['status']);
        }
        return AjaxCallbackMessage('api接口不再维护',false);
    }


    /*
     * 邀请经纪人投资人是的通讯录导入过滤优化
     * */
    public function postPhoneInvite(PhoneInviteRequest $request,$version = null){
        $versionService = $this->init(__METHOD__, $version, null, 'Agent');
        if($versionService){
            $response = $versionService->bootstrap($request->all());

            return AjaxCallbackMessage($response['message'],$response['status']);
        }
        return AjaxCallbackMessage('api接口不再维护',false);
    }

    /**
     * 经纪人积分列表
     */
    public function postScoreList(Request $request,$version = null){
        $versionService = $this->init(__METHOD__, $version, null, 'Agent');
        if($versionService){
            $response = $versionService->bootstrap($request->all());

            return AjaxCallbackMessage($response['message'],$response['status']);
        }
        return AjaxCallbackMessage('api接口不再维护',false);
    }




    /**
     * 获取经纪人或者投资人token
     */
    public function postInfo(Request $request,$version = null){
        $versionService = $this->init(__METHOD__, $version, null, 'Agent');
        if($versionService){
            $response = $versionService->bootstrap($request->all());

            return AjaxCallbackMessage($response['message'],$response['status']);
        }
        return AjaxCallbackMessage('api接口不再维护',false);
    }


    /*
     * 经纪人新手任务
     */
    public function postTask(Request $request, $version = null)
    {
        $versionService = $this->init(__METHOD__, $version, null, 'Agent');
        if($versionService){
            $response = $versionService->bootstrap($request->all());

            return AjaxCallbackMessage($response['message'],$response['status']);
        }
        return AjaxCallbackMessage('api接口不再维护',false);
    }


    /*
    * 经纪人分享得积分
    */
    public function postShareGetScore(Request $request, $version = null)
    {
        $versionService = $this->init(__METHOD__, $version, null, 'Agent');
        if($versionService){
            $response = $versionService->bootstrap($request->all());

            return AjaxCallbackMessage($response['message'],$response['status']);
        }
        return AjaxCallbackMessage('api接口不再维护',false);
    }

    /**
    *   作者：shiqy
    *   创作时间：2018/1/22 0022 下午 2:42
    *   功能描述：商务负责品牌
    */

    public function postBusinessBrand(BusinessBrandRequest $request, $version = null)
    {
        $versionService = $this->init(__METHOD__, $version, null, 'Agent');
        if($versionService){
            $response = $versionService->bootstrap($request->all());

            return AjaxCallbackMessage($response['message'],$response['status']);
        }
        return AjaxCallbackMessage('api接口不再维护',false);
    }

    /**
    *   作者：shiqy
    *   创作时间：2018/1/25 0025 下午 2:51
    *   功能描述：获取考察订单
    */

    public function postInspectOrder(InspectOrderRequest $request, $version = null)
    {
        $versionService = $this->init(__METHOD__, $version, null, 'Agent');
        if($versionService){
            $response = $versionService->bootstrap($request->all());

            return AjaxCallbackMessage($response['message'],$response['status']);
        }
        return AjaxCallbackMessage('api接口不再维护',false);
    }

    /**
    *   作者：shiqy
    *   创作时间：2018/1/25 0025 下午 5:15
    *   功能描述：获取加盟合同订单列表
    */
    public function postLeagueOrder(LeagueOrderRequest $request, $version = null)
    {
        $versionService = $this->init(__METHOD__, $version, null, 'Agent');
        if($versionService){
            $response = $versionService->bootstrap($request->all());

            return AjaxCallbackMessage($response['message'],$response['status']);
        }
        return AjaxCallbackMessage('api接口不再维护',false);
    }

    /**
    *   作者：shiqy
    *   创作时间：2018/1/26 0026 下午 4:12
    *   功能描述：展示经纪人或投资人列表
    */
    public function postPersonList(PersonListRequest $request, $version = null){
        $versionService = $this->init(__METHOD__, $version, null, 'Agent');
        if($versionService){
            $response = $versionService->bootstrap($request->all());

            return AjaxCallbackMessage($response['message'],$response['status']);
        }
        return AjaxCallbackMessage('api接口不再维护',false);
    }

    /**
    *   作者：shiqy
    *   创作时间：2018/1/30 0030 下午 3:12
    *   功能描述：获取一个经纪人的关系
    */
    public function postAgentRelation(ShowAgentRelationRequest $request, $version = null){
        $versionService = $this->init(__METHOD__, $version, null, 'Agent');
        if($versionService){
            $response = $versionService->bootstrap($request->all());

            return AjaxCallbackMessage($response['message'],$response['status']);
        }
        return AjaxCallbackMessage('api接口不再维护',false);
    }


    /**
    *   作者：shiqy
    *   创作时间：2018/2/2 0002 下午 3:07
    *   功能描述：考察邀请函审核
    */
    public function postInspectAudit(InspectAuditRequest $request, $version = null){
        $versionService = $this->init(__METHOD__, $version, null, 'Agent');
        if($versionService){
            $response = $versionService->bootstrap($request->all());

            return AjaxCallbackMessage($response['message'],$response['status']);
        }
        return AjaxCallbackMessage('api接口不再维护',false);
    }



}