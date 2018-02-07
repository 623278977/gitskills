<?php namespace App\Services\Version\Agent\User;

use App\Http\Controllers\Api\ActivityController;
use App\Models\Activity\Maker;
use App\Models\Activity\Sign;
use App\Models\Agent\AgentAchievementLog;
use App\Models\Agent\AgentWithdraw;
use App\Models\Agent\Score\AgentScoreLog;
use App\Models\Brand\Enter;
use App\Services\Version\VersionSelect;
use DB;
use Validator;
use App\Models\Agent\Agent;
use App\Models\Agent\AgentAchievement;
use App\Models\Agent\AgentCurrencyLog;
use App\Models\Agent\CommissionLevelTemplate;
use App\Models\Orders\Items;
use App\Models\Contract\Contract;
use App\Models\Brand\Entity\V020800 as BrandAgent;
use App\Models\Agent\AgentBrand;
use App\Models\Brand\Entity as BrandModel;
use App\Models\Orders\Entity as Orders;
use App\Models\Agent\AgentCategory;
use App\Models\Agent\Invitation;
use App\Models\Activity\Entity;
use App\Services\Version\Message\_v020800;
use App\Models\Brand\BrandStore;
use App\Models\Agent\AgentCustomer;
use App\Models\User\Entity as User;
use App\Models\Agent\AgentFeedback;
use App\Models\Activity\Entity as Activity;
use App\Models\Agent\Entity\_v010200 as Agentv010200;

class _v010000 extends VersionSelect
{
    const ACTIVITY_TYPE = 1;      //活动类型
    const INSPECT_TYPE = 2;      //考察类型
    const DEFAULT_TIME = 5;      //默认邀请函的过期时间（天数）
    protected $dateFormat = 'U';

    /*
     * 我的佣金
     */
    public function postMyCommission($data = [])
    {
        $user = Agent::where('id', $data['agent_id'])->first();
        //可提现余额
        $currency = abandonZero($user->currency);
        $quarter_info = Agent::instance()->getQuarter(time());

        if (!empty($data['quarter_chioces'])) {
            $quarter = $data['quarter_chioces'];
        } else {
            $quarter = $quarter_info[0];
        }

        //如果还没有发生过佣金
        $history_currency = AgentCurrencyLog::where('agent_id', $data['agent_id'])
            ->where('operation', 1)->where('status', 2)->sum('num');
        if (!$history_currency) {
            return ['status' => false, 'message' => '你还没有获得过佣金'];
        }


        $agentAchievement = AgentAchievement::where('agent_id', $data['agent_id'])
            ->where('quarter', $quarter)->first();

        if (!$agentAchievement) {
            $agentAchievement = AgentAchievement::create([
                'agent_id' => $data['agent_id'],
                'quarter' => $quarter,
                'total_achievement' => 0,
                'my_achievement' => 0,
                'team_achievement' => 0,
                'my_commission' => 0,
                'team_commission' => 0,
                'frozen_commission' => 0,
                'total_commission' => 0,
            ]);
        }

//        if(!$agentAchievement){
//            return ['status' => false, 'message' => '该经纪人及其团队当前季度还没有成单'];
//        }

        //本季结算中（元）
        $frozen_currency = abandonZero($agentAchievement->frozen_commission);
        //累计提现（元）
        $total_currency = AgentCurrencyLog::where('agent_id', $data['agent_id'])
            ->where('operation', -1)->where('type', 1)->where('status', 2)->sum('num');

        //我完成的业绩
        $my_orders = $agentAchievement->my_achievement;
        //我下属的业绩
        $my_subordinate_orders = $agentAchievement->team_achievement;
        //当前业绩总和
        $total_orders = $agentAchievement->total_achievement;

        //当前所处梯度
        $template = CommissionLevelTemplate::where('min', '<=', $agentAchievement->total_achievement)
            ->where('max', '>=', $agentAchievement->total_achievement)
            ->first();
        $level = $template->name . '(' . $template->min . '~' . $template->max . ')';

        //总佣金
        $total_commission = abandonZero($agentAchievement->total_commission);
        //下属佣金
        $my_subordinate_commission = abandonZero($agentAchievement->team_commission);
        //我的佣金
        $my_commission = abandonZero($agentAchievement->my_commission);
        $page = isset($data['page']) ? $data['page'] : 1;
        $page_size = isset($data['page_size']) ? $data['page_size'] : 10;
        //佣金消费明细
        $detail = AgentCurrencyLog::getInstance()->details($data['agent_id'], $page, $page_size);


        //季度选项
        $quarter_chioces = Agent::instance()->getQuarterChoice(time(), $user->created_at->timestamp);

        $data = compact('currency', 'total_currency', 'frozen_currency',
            'my_orders', 'my_subordinate_orders', 'total_orders', 'level', 'total_commission',
            'my_commission', 'my_subordinate_commission', 'detail', 'quarter_chioces'
        );

        return ['status' => true, 'message' => $data];
    }


    /**
     * 佣金详情   --数据中心版
     * @User
     * @param $data
     * @return array
     */
    public function postCommissionDetail($data)
    {
        //如果是成单奖励
        if ($data['type'] == 4) {
            $log = AgentAchievementLog::with('agent', 'contract.user', 'contract.brand', 'contract.fund', 'contract.invitation')
                ->where('id', $data['id'])->first();
            $commission = numFormatWithComma(abandonZero($log->commission));
            $customer_name = $log->contract->user->realname ? $log->contract->user->realname : $log->contract->user->nickname;
            $brand_title = $log->contract->brand->name;
            $amount = numFormatWithComma(abandonZero($log->contract->amount));
            $pre_pay = numFormatWithComma(abandonZero($log->contract->pre_pay));
            $discount_fee = numFormatWithComma(abandonZero($log->contract->fund->fund + $log->contract->invitation->default_money));
            $tail_pay = numFormatWithComma(abandonZero($log->contract->amount - $log->contract->pre_pay));
            $online_pay = numFormatWithComma(abandonZero($log->contract->pre_pay - ($log->contract->fund->fund + $log->contract->invitation->default_money)));
            $created_at = date('Y年m月d日 H:i:s', $log->contract->tail_pay_at);


            //相关的首付订单
            $order = Items::with('orders')->where('status', 'pay')
                ->where('type', 'contract')
                ->where('product_id', $log->contract_id)->first();

            $order->orders->buyer_id ? $pay_way = Orders::$_PAYWAY[$order->orders->pay_way] . '(' . $order->orders->buyer_id . ')' : $pay_way = Orders::$_PAYWAY[$order->orders->pay_way];

            $online_pay_at = date('Y-m-d H:i:s', $order->orders->pay_at);
            $bank_no = digitalStarReplace($log->contract->bank_no);
            $tail_pay_time = date('Y-m-d H:i:s', $log->contract->tail_pay_at);
            $agent_name = $log->agent->realname ? $log->agent->realname : $log->agent->nickname;


            $data = compact('commission', 'customer_name', 'brand_title', 'amount', 'pre_pay', 'created_at', 'agent_name',
                'tail_pay', 'discount_fee', 'online_pay', 'pay_way', 'online_pay_at', 'tail_pay', 'tail_pay_time', 'bank_no');
        }


        //如果是团队分佣 ，返回上个季度的团队分佣情况
        if ($data['type'] == 5) {
            //获取上个季度
            $this_quarter = Agent::instance()->getQuarter(time());
            $quarter = Agent::instance()->getQuarterWithBrackets($this_quarter[1] - 1);
            //获取业绩记录
            $achieviment = AgentAchievement::where('id', $data['id'])->first();

            $commission = abandonZero($achieviment->frozen_commission);
            $unfreeze_time = date('Y-m-d H:i:s', $this_quarter[2]);

            $my_orders = $achieviment->my_achievement;
            $my_subordinate_orders = $achieviment->team_achievement;
            $total_orders = $achieviment->total_achievement;

            //获取梯度
            $template = CommissionLevelTemplate::where('min', '<=', $achieviment->total_achievement)
                ->where('max', '>=', $achieviment->total_achievement)
                ->first();
            $level = $template->name . '(' . $template->min . '~' . $template->max . ')';
            $letter_quarter = Agent::instance()->getQuarterWithLetter($this_quarter[1] - 1);

            //我的下属获得的佣金
            $my_subordinate_commission = abandonZero($achieviment->team_commission);
            //我获得的佣金
            $my_commission = abandonZero($achieviment->my_commission);
            //我的团队获得的佣金
            $total_commission = abandonZero($achieviment->total_achievement);

            $data = compact('quarter', 'unfreeze_time', 'commission', 'my_orders',
                'my_subordinate_orders', 'total_orders', 'level', 'letter_quarter', 'total_commission',
                'my_subordinate_commission', 'my_commission');
        }


        //如果成单额外奖励邀请人
        if ($data['type'] == 6) {
            $contract = Contract::with('user', 'brand', 'user_fund', 'invitation', 'agent')->where('id', $data['id'])->first();


            //相关的首付订单
            $order = Items::with('orders')->where('status', 'pay')
                ->where('type', 'contract')
                ->where('product_id', $data['id'])->first();

            $commission = '1,000';
            $customer_name = $contract->user->realname ? $contract->user->realname : $contract->user->nickname;
            $customer_name .= "(您为{$customer_name}的邀请人)";
            $brand_title = $contract->brand->name;
            $amount = numFormatWithComma(abandonZero($contract->amount));
            $pre_pay = numFormatWithComma(abandonZero($contract->pre_pay));
            $tail_pay = numFormatWithComma(abandonZero($contract->amount - $contract->pre_pay));
            $discount_fee = numFormatWithComma(abandonZero($contract->user_fund->fund + $contract->invitation->default_money));
            $order->orders->buyer_id ? $pay_way = Orders::$_PAYWAY[$order->orders->pay_way] . '(' . $order->orders->buyer_id . ')' : $pay_way = Orders::$_PAYWAY[$order->orders->pay_way];

            $online_pay_at = date('Y-m-d H:i:s', $order->orders->pay_at);
            $created_at = date('Y年m月d日 H:i:s', $contract->created_at->timestamp);


            $online_pay = numFormatWithComma(abandonZero($contract->pre_pay - ($contract->user_fund->fund + $contract->invitation->default_money)));
//            前四位后四位显示中间*代替
            $bank_no = digitalStarReplace($contract->bank_no);
            $tail_pay_time = date('Y-m-d H:i:s', $contract->confirm_time);
            $agent_name = $contract->agent->realname;

            $data = compact('commission', 'customer_name', 'brand_title', 'amount', 'pre_pay', 'created_at',
                'discount_fee', 'online_pay', 'pay_way', 'online_pay_at', 'tail_pay', 'tail_pay_time', 'bank_no', 'agent_name');
        }


        //如果是提现
        if ($data['type'] == 1) {
            $withdraw = AgentWithdraw::where('id', $data['id'])->first();
            $commission = abandonZero($withdraw->money);
            $auth_time = $apply_time = date('m-d H:i', $withdraw->created_at->timestamp);
            if ($withdraw->status == 1) {
                $pay_time = '预计' . date('m-d', $withdraw->created_at->timestamp + 24 * 3 * 3600);
                $in_account_time = '预计3个工作日内';
            } elseif (2 == $withdraw->status) {
                $pay_time = date('m-d H:i', $withdraw->updated_at->timestamp);
                $in_account_time = date('Y-m-d H:i', $withdraw->updated_at->timestamp);
            } else {
                $pay_time = '审核失败';
                $withdraw->remark ? $in_account_time = '审核失败,' . $withdraw->remark : $in_account_time = '审核失败';
            }

            $account = $withdraw->bank_name;
            $created_time = date('Y-m-d H:i', $withdraw->created_at->timestamp);
            $withdraw_no = $withdraw->withdraw_no;
            $status = $withdraw->status;
            $fee = $withdraw->fee;

            $data = compact('commission', 'apply_time', 'auth_time', 'pay_time', 'in_account_time',
                'created_time', 'account', 'withdraw_no', 'status', 'fee');
        }


        //如果是团队发展    下载经纪人APP，激活账号,完成实名认证
        if (8 == $data['type']) {
            $log = AgentCurrencyLog::where('id', $data['log_id'])->first();
            $created_time = date('Y-m-d H:i', $log->created_at->timestamp);
            $commission = $log->num;
            $agent = Agent::find($data['id']);
            $realname = $agent->realname;
            $realname .= '(' . $agent->username . ')';
            $register_time = date('Y/m/d', $agent->created_at->timestamp);

            $data = compact('commission', 'realname', 'register_time', 'created_time');
        }


        //如果是团队成长    代理品牌数≥ 1   推荐成功投资客≥ 1
        if (9 == $data['type']) {
            $log = AgentCurrencyLog::where('id', $data['log_id'])->first();
            $created_time = date('Y-m-d H:i', $log->created_at->timestamp);
            $commission = $log->num;
            $agent = Agent::find($data['id']);
            $realname = $agent->realname;
            $realname .= '(' . $agent->username . ')';

            $data = compact('commission', 'realname', 'created_time');
        }


        //如果是发展投资人   自注册2天内，每天都查看品牌详情，且至少有3次不少于3分钟
        if (10 == $data['type']) {
            $log = AgentCurrencyLog::where('id', $data['log_id'])->first();
            $created_time = date('Y-m-d H:i', $log->created_at->timestamp);
            $commission = $log->num;
            $user = User::where('uid', $data['id'])->first();
            $register_time = date('Y/m/d', $user->created_at->timestamp);
            $user->realname ? $realname = $user->realname : $realname = $user->nickname;
            $realname .= '(' . $user->username . ')';

            $data = compact('commission', 'realname', 'created_time', 'register_time');
        }

        //如果是三星主管  四星主管或者五星主管
        if (in_array($data['type'], [11, 12, 13])) {
            $log = AgentCurrencyLog::where('id', $data['log_id'])->first();
            $created_time = date('Y-m-d H:i', $log->created_at->timestamp);
            $commission = $log->num;
            $type = $log->type;

            $data = compact('commission', 'type', 'created_time');
        }


        //活动邀约
        if (in_array($data['type'], [14])) {
            $log = AgentCurrencyLog::where('id', $data['log_id'])->first();
            //时间
            $created_time = date('Y-m-d H:i', $log->created_at->timestamp);
            //佣金
            $commission = $log->num;

            //活动名称
            $activity = Activity::find($data['id']);
            $activity_name = $activity->subject;
            $begin_time = date('Y-m-d', $activity->begin_time);

            //活动场地
            $infos = Activity::getMakerInfo($data['id']);
            $zone_names = $infos['zone_names'];
            $maker_names = $infos['maker_names'];

            //邀请参会人数
            $count = Sign::where('activity_id', $data['id'])->where('agent_id', $log->agent_id)->where('status', 1)->count();

            $data = compact('commission', 'created_time', 'activity_name', 'count', 'zone_names', 'maker_names', 'begin_time');
        }


        //如果是到票
        if (in_array($data['type'], [15])) {
            $log = AgentCurrencyLog::where('id', $data['log_id'])->first();

            $invitation = Invitation::with('belongsToAgent', 'hasOneStore.hasOneBrand.contactor', 'hasOneStore.hasOneZone', 'hasOneUsers')->where('id', $data['id'])->first();
            //投资人
            $invitation->hasOneUsers->nickname ? $realname = $invitation->hasOneUsers->nickname : $realname = $invitation->hasOneUsers->realname;
            //考察品牌
            $brand_name = $invitation->hasOneStore->hasOneBrand->name;
            //考察门店
            $store_name = $invitation->hasOneStore->name;
            $address = $invitation->hasOneStore->address;
            $zone_name = $invitation->hasOneStore->hasOneZone->name;

            //考察订金
            $money = $invitation->default_money;
            //支付情况
            $pay_time = date('m/d H:i:s', $invitation->pay_time);


            //考察时间
            $inspect_time = date('Y/m/d', $invitation->inspect_time);

            //品牌商务对接
            $agent_name = $invitation->hasOneStore->hasOneBrand->contactor->name . '(' . $invitation->hasOneStore->hasOneBrand->contactor->tel . ')';


            $created_time = date('Y-m-d H:i', $log->created_at->timestamp);
            $commission = $log->num;
            $type = $log->type;

            $data = compact('realname', 'brand_name', 'store_name', 'money',
                'inspect_time', 'pay_time', 'agent_name', 'created_time', 'commission', 'address', 'zone_name');
        }


//        品牌入驻
        if (in_array($data['type'], [16])) {
            $log = AgentCurrencyLog::where('id', $data['log_id'])->first();
            //时间
            $created_time = date('Y-m-d H:i:s', $log->created_at->timestamp);
            //佣金
            $commission = $log->num;
            //品牌入驻
            $enter = Enter::find($data['id']);
            $agent = Agent::find($enter->uid);
            $realname = $agent->realname;
            $realname .= '(' . $agent->username . ')';
            $brand_name = $enter->brand->name;
            $enter_time = date('Y/m/d', $enter->created_at->timestamp);

            $data = compact('commission', 'created_time', 'realname', 'brand_name', 'enter_time');
        }


        return ['status' => true, 'message' => $data];
    }


    /**
     * 经纪人电子合同概览
     */
    public function postContract($input = [])
    {
        //相关合同统计
        $data = Contract::ContractCount($input['agent_id'], 'agent');

        return ['message' => $data, 'status' => true];
    }


    /**
     * 经纪人电子合同详情
     */
    public function postContractDetail($input = [])
    {
        $status = $input['status'];
        $agent_id = $input['agent_id'];

        //相关合同统计
        $data = Contract::ContractDetail($agent_id, $status);

        return ['message' => $data, 'status' => true];
    }


    /**
     * 经纪人申请代理中的品牌列表
     */
    public function postApplyBrands($input = [])
    {

        $page_size = $input['page_size'] ?: '10';

        //我代理的品牌id
        $ids = AgentBrand::getAgentBrandId($input['agent_id'], 'apply_brands');


        $builder = BrandModel::where('status', 'enable')
            ->where('agent_status', '1')
            ->whereIn('id', $ids);

        $builder = AgentBrand::selectList($builder);

        $result = $builder->paginate($page_size)->toArray();
        //格式化数据
        $data['brands'] = BrandAgent::format($result, $input, false);
        $data['apply_brands'] = count($data['brands']) ?: 0;

        return ['message' => $data, 'status' => true];
    }


    /**
     * 经纪人代理品牌列表
     */
    public function postAgentBrands($input = [])
    {
        $page_size = $input['page_size'] ?: '10';

        //我代理的品牌id
        $ids = AgentBrand::getAgentBrandId($input['agent_id'], 'agent_brand');

        $builder = BrandModel::where('status', 'enable')
            ->where('agent_status', '1')
            ->where('agent_status', '1')
            ->whereIn('id', $ids);

        $builder = AgentBrand::selectList($builder);

        $result = $builder->paginate($page_size)->toArray();
        //格式化数据
        $data['brands'] = BrandAgent::format($result, $input, true);
        $data['agent_brands'] = count($data['brands']) ?: 0;


        return ['message' => $data, 'status' => true];
    }

    //判断一个手机号是否可以被邀请成投资人或经纪人
    public function postCanInvite($input = [])
    {
        $phoneStr = $input['mobile'];
        $phoneArr = explode(',', $phoneStr);
        $phoneArr = array_filter($phoneArr);
        $type = $input['type'];
        if (!is_array($phoneArr)) {
            return ['message' => 'mobile应为一个数组', 'status' => false];
        }
        $data = Agent::getCanInvite($phoneArr, $type);
        return ['message' => $data, 'status' => true];
    }


    /**
     * 获取季度业绩
     */
    public function postQuarterAchievement($data)
    {
        $achievement = AgentAchievement::where('agent_id', $data['agent_id'])
            ->where('quarter', $data['quarter'])->first();

        $template = CommissionLevelTemplate::where('min', '<=', $achievement->total_achievement)
            ->where('max', '>=', $achievement->total_achievement)
            ->first();
        $level = $template->name . '(' . $template->min . '~' . $template->max . ')';

        $my_orders = $achievement->my_achievement;
        //我下属的业绩
        $my_subordinate_orders = $achievement->team_achievement;
        //当前业绩总和
        $total_orders = $achievement->total_achievement;

        //总佣金
        $total_commission = abandonZero($achievement->total_commission);
        //下属佣金
        $my_subordinate_commission = abandonZero($achievement->team_commission);
        //我的佣金
        $my_commission = abandonZero($achievement->my_commission);

        $data = compact('my_orders', 'my_subordinate_orders',
            'total_orders', 'my_commission', 'my_subordinate_commission',
            'total_commission', 'level');

        return ['message' => $data, 'status' => true];
    }

    //我的界面详情
    public function postIndex($input)
    {
        $agentId = intval($input['agent_id']);
        $data = Agent::getStatisticInfo($agentId);
        if ($data === false) {
            return ['message' => "用户不存在", 'status' => false];
        }
        return ['message' => $data, 'status' => true];
    }

    //个人详情页（名片）
    public function postCard($input)
    {
        $agentId = intval($input['agent_id']);
        if (!empty($agentId)) {
            $agentInfo = Agent::getAgentCard($agentId);
            if ($agentInfo !== false) {
                return ['message' => $agentInfo, 'status' => true];
            }
            return ['message' => "获取失败", 'status' => false];
        } else {
            return ['message' => "请传递用户id", 'status' => false];
        }
    }

    //编辑个人（经纪人）信息

    /**
     * @param $input
     * @return array
     */
    public function postEdit($input)
    {
        $agentId = intval($input['agent_id']);
        if (!$agentId) {
            return ['message' => "请输入经纪人id", 'status' => false];
        }
        $agentInfo = Agent::where('id', $agentId)->where('status', 1)->first();
        if (!is_object($agentInfo)) {
            return ['message' => "该经纪人无效", 'status' => false];
        }
        $data = [];
        isset($input['gender']) && $data['gender'] = intval($input['gender']);
        isset($input['zone_id']) && $data['zone_id'] = intval($input['zone_id']);
        isset($input['avatar']) && $data['avatar'] = trim($input['avatar']);
        isset($input['birth']) && $data['birth'] = trim($input['birth']);
        isset($input['edu']) && $data['diploma'] = trim($input['edu']);
        isset($input['profession']) && $data['profession'] = trim($input['profession']);
        isset($input['earning']) && $data['earning'] = trim($input['earning']);
        isset($input['signature']) && $data['sign'] = trim($input['signature']);
        Agent::where("id", $agentId)->update($data);
        if (isset($input['industryIds'])) {
            $industryIdsStr = trim($input['industryIds']);
            $industryIdArr = [];
            if (!empty($industryIdsStr)) {
                $industryIdArr = explode(",", $industryIdsStr);
            }
            $arr = [];
            foreach ($industryIdArr as $industryId) {
                $arr[] = ['agent_id' => $agentId, 'category_id' => $industryId];
            }
            AgentCategory::where("agent_id", $agentId)->delete();
            $rel = AgentCategory::insert($arr);
        }

        //释放资源
        unset($agentInfo);

        // todo 经纪人修改昵称时，在融云服务端更新经纪人之前的数据为当前更改后的最新昵称数据
        // todo changeAuthor: zhaoyf 2017-12-15 13:00

        //注：这里需要获取到更新后的最新值，$agentInfo里存储的是更新前的值，所有这里需要重新获取下
        $agent_result = Agent::where(['id' => $agentId, 'status' => 1])->first();
        if (is_object($agent_result)) {

            //todo  如果经纪人昵称为空，就取真实名称
            $img  = $agent_result->avatar           ?  getImage($agent_result->avatar) : '';
            $name = !empty($agent_result->nickname) ?  $agent_result->nickname : $agent_result->realname;

            GainToken('agent' . $agent_result->id, $name, $img, 'user_refresh');

            //释放资源
            unset($agent_result);
        }

        //是否已完善资料
        $complete = Agentv010200::isComplete($agentId);

        if($complete){
            //给积分
            Agentv010200::add($agentId, AgentScoreLog::$TYPES_SCORE[17], 17, '完善个人资料', 0, 1, 1);
        }

        return ['message' => "保存成功", 'status' => true];
    }


    //我的等级
    public function postLevel($input)
    {
        $agentId = intval($input['agent_id']);
        if (!empty($agentId)) {
            $agentInfo = Agent::getAgentLevel($agentId);
            if ($agentInfo !== false) {
                return ['message' => $agentInfo, 'status' => true];
            }
            return ['message' => "获取失败", 'status' => false];
        } else {
            return ['message' => "请传递用户id", 'status' => false];
        }
    }

    //我的下线
    public function postSubordinate($input)
    {
        $agentId = intval($input['agent_id']);
        if (!empty($agentId)) {
            $agentInfo = Agent::getSubordinateList($agentId);
            if ($agentInfo !== false) {
                return ['message' => $agentInfo, 'status' => true];
            }
            return ['message' => "获取失败", 'status' => false];
        } else {
            return ['message' => "请传递用户id", 'status' => false];
        }
    }

    //团队业绩
    public function postTeamSales($input)
    {
        $agentId = intval($input['agent_id']);
        if (!empty($agentId)) {
            $agentInfo = Agent::getTeamSales($agentId);
            if ($agentInfo !== false) {
                return ['message' => $agentInfo, 'status' => true];
            }
            return ['message' => "获取失败", 'status' => false];
        } else {
            return ['message' => "请传递用户id", 'status' => false];
        }
    }

    //业绩明细
    public function postSalesDetail($input)
    {
        $agentId = intval($input['agent_id']);
        $type = trim($input['type']);
        $page = empty($input['page']) ? 1 : intval($input['page']);
        $pageSize = empty($input['page_size']) ? 10 : intval($input['page_size']);
        if (empty($agentId)) {
            return ['message' => "请传递经纪人id", 'status' => false];
        }
        if (empty($type)) {
            return ['message' => "请传递请求类型", 'status' => false];
        }
        $salesInfo = Agent::salesDetail($agentId, $type, $page, $pageSize);
        if (isset($salesInfo['error'])) {
            return ['message' => $salesInfo["message"], 'status' => false];
        }
        return ['message' => $salesInfo, 'status' => true];
    }

    //活动邀请函
    public function postActivityInvitation($input)
    {
        $agentId = intval($input['agent_id']);
        $type = trim($input['type']);
        $page = empty($input['page']) ? 1 : intval($input['page']);
        $pageSize = empty($input['page_size']) ? 10 : intval($input['page_size']);
        if (empty($agentId)) {
            return ['message' => "请传递经纪人id", 'status' => false];
        }
        if (!in_array($type, [-1, 0, 1])) {
            return ['message' => "请传递正确的请求类型", 'status' => false];
        }
        $salesInfo = Agent::getInvitationResult($agentId, $type, $page, $pageSize);
        if (isset($salesInfo['error'])) {
            return ['message' => $salesInfo["message"], 'status' => false];
        }
        return ['message' => $salesInfo, 'status' => true];
    }

    /**
     * 创建邀请函
     *
     * @param $param
     * @return array|string
     * @internal param AgentRequest $request
     */
    public function postCreateInvitation($param)
    {
        $result = $param['request']->input();

        //计算活动和考察的过期时间（默认过期时间为5天）
        $default_time = strtotime(date("Y-m-d H:i:s", time()) . "+" . self::DEFAULT_TIME . " day");
        if ($result['type'] == self::ACTIVITY_TYPE) {
            $activity_end_time = Entity::where('id', $result['post_id'])->value('end_time');
            $activity_time_day = ceil(($activity_end_time - time()) / 86400);

            if ($activity_time_day < self::DEFAULT_TIME) {
                if ($activity_time_day > 0) {
                    $result['expiration_time'] = $activity_end_time;
                } else {
                    $result['expiration_time'] = 0;
                }
            } else {
                $result['expiration_time'] = $default_time;
            }
        } elseif ($result['type'] == self::INSPECT_TYPE) {
            $inspect_time = strtotime($result['inspect_time'] . ' 23:59:59');
            $inspect_time_day = ceil(($inspect_time - time()) / 86400);

            if ($inspect_time_day < self::DEFAULT_TIME) {
                if ($inspect_time_day > 0) {
                    $result['expiration_time'] = $default_time;
                } else {
                    $result['expiration_time'] = 0;
                }
            } else {
                $result['expiration_time'] = $default_time;
            }
            $result['inspect_time'] = $inspect_time; //考察邀请函的考察时间
        }
        $result['status'] = 0;             //创建邀请函时默认状态 0
        $result['created_at'] = time();        //创建邀请函当前时间
        $result['updated_at'] = time();        //创建邀请函添加当前的更新时间

        $create_result = Invitation::insertGetId($result);
        if ($create_result) {
            $get_one_invite = Invitation::find($create_result);

            //根据创建邀请函的类型往经纪人客户日记表里插入数据
            if ($get_one_invite->type == self::ACTIVITY_TYPE) {

                $activity_brand = Entity::with('brand')
                    ->where('id', $get_one_invite->post_id)
                    ->first();
                $brand_id = $activity_brand['brand'][0]['brand_id'];

                //添加日记记录数据
                $add_result = _v020800::instance()->addAgentCustomerLog($get_one_invite, $brand_id, 3, null, 'other');
                if ($add_result['status']) {
                    Agentv010200::add($result['agent_id'], AgentScoreLog::$TYPES_SCORE[4], 4, '发送活动邀请函', $create_result);
                }


            } elseif ($get_one_invite->type == self::INSPECT_TYPE) {
                $inspect_brand_id = BrandStore::where('id', $get_one_invite->post_id)->value('brand_id');

                //添加日记记录数据
                $add_result = _v020800::instance()->addAgentCustomerLog($get_one_invite, $inspect_brand_id, 6, null, 'other');

                if ($add_result['status']) {
                    Agentv010200::add($result['agent_id'], AgentScoreLog::$TYPES_SCORE[5], 5, '发送考察邀请函', $create_result);
                }
            }

            //组织数据返回创建邀请函成功后的结果
            $data = [
                'invite_id' => $get_one_invite->id,
                'agent_id' => $get_one_invite->agent_id,
                'customer_id' => $get_one_invite->uid,
                'type' => $get_one_invite->type
            ];

            return ['message' => $data, 'status' => true];
        } else {
            return ['message' => '创建邀请函失败', 'status' => false];
        }

    }

    /*
     * shiqy
     * 邀请口号
     * */
    public function postInviteSlogan($input)
    {
        $agentId = intval($input['agent_id']);
        $type = trim($input['type']);
        if (empty($agentId)) {
            return ['message' => '经纪人id不能为空', 'status' => false];
        }
        if (!in_array($type, ['agent', 'customer'])) {
            return ['message' => '请输入正确的邀请类型', 'status' => false];
        }
        $data = Agent::getInviteSlogan($agentId, $type);
        if (isset($data['error'])) {
            return ['message' => $data['message'], 'status' => false];
        }
        return ['message' => $data, 'status' => true];
    }

    /**
     * 被邀请的情况下注册成经纪人   --数据中心版
     * @User shiqy
     * @param $input
     * @return array
     */

    public function postAgentRegister($input)
    {
        $agentId = intval($input['agent_id']);
        $username = trim($input['username']);
        $code = trim($input['code']);
        $type = trim($input['type']);
        if (empty($agentId)) {
            return ['message' => '经纪人id不能为空', 'status' => false];
        }
        if (empty($username)) {
            return ['message' => '手机号不能为空', 'status' => false];
        }
        if (empty($type)) {
            return ['message' => 'type值不能为空', 'status' => false];
        }
        $data = Agent::getAgentRegister($agentId, $username, $code, $type);
        if (isset($data['error'])) {
            return ['message' => $data['message'], 'status' => false];
        }
        return ['message' => $data, 'status' => true];
    }

    /**
     * 被邀请的情况下注册成投资人   --数据中心版
     * @User shiqy
     * @param $input
     * @return array
     */

    public function postCustomerRegister($input)
    {
        $agentId = intval($input['agent_id']);
        $username = trim($input['username']);
        $code = trim($input['code']);
        $type = trim($input['type']);

        if (empty($agentId)) {
            return ['message' => '经纪人id不能为空', 'status' => false];
        }
        if (empty($username)) {
            return ['message' => '手机号不能为空', 'status' => false];
        }
        if (empty($type)) {
            return ['message' => 'type值不能为空', 'status' => false];
        }
        $data = Agent::getCustomerRegister($agentId, $username, $code, $type);
        if (isset($data['error'])) {
            return ['message' => $data['message'], 'status' => false];
        }
        return ['message' => $data, 'status' => true];
    }


    /**
     * 判断投资人或经纪人是否注册过   --数据中心版
     * @User shiqy
     * @param $input
     * @return array
     */
    public function postIsregister($input)
    {
        $phone = trim($input['phone']);

        //md5加盐后的注册号码
        $non_reversible = encryptTel($phone);

        $type = intval($input['type']);
        $phoneCode = empty($input['phone_code']) ? '86' : trim($input['phone_code']);
        if (empty($phone)) {
            return ['message' => '手机号不能为空', 'status' => false];
        }
        if (!in_array($type, [1, 2])) {
            return ['message' => '请输入正确的type值', 'status' => false];
        }

        if (!checkMobile($phone, $phoneCode)) {
            return ['message' => '手机号格式不正确', 'status' => false];
        };
        if($type == 1){
           //md5唯一值查找
           $data = Agent::where('non_reversible', $non_reversible)->first();
           if(is_object($data)){
               return ['message'=>'has_register','status'=>false];
           }
        }
        else{
            //md5唯一值查找
            $data = User::where('non_reversible', $non_reversible)->first();
            if(is_object($data)){
                return ['message'=>'该账号已被注册','status'=>false];
            }
        }
        return ['message' => 'ok', 'status' => true];
    }




    /**
     * 佣金记录
     *
     * @param Request $request
     * @param null $version
     * @return string
     * @author tangjb
     */
    public function postCommissionRecords($data)
    {
        $records = AgentAchievement::where('agent_id', $data['agent_id'])->select('quarter', 'my_commission')->get();


        $res = [];
        foreach ($records as $k => $v) {
            $quarter = AgentAchievement::getInstance()->transformQuarter($v['quarter']);
            $arr = [
                'quarter' => $quarter,
                'my_commission' => $v['my_commission']
            ];

            $res[] = $arr;
        }

        return ['message' => $res, 'status' => true];

    }

    /*
     * 经纪人意见反馈接口
     * */
    public function postAgentFeedback($input)
    {
        $validator = Validator::make($input, [
            'agent_id' => 'required|exists:agent,id',
            'content' => 'required',
        ]);
        if ($validator->fails()) {
            $warnings = $validator->messages();
            $show_warning = $warnings->first();
            return ['message' => $show_warning, 'status' => false];
        }

        $rel = AgentFeedback::create($input);
        if (!is_object($rel)) {
            return ['message' => '保存失败', 'status' => false];
        }
        return ['message' => '保存成功', 'status' => true];
    }


    /*
    * 经纪人分享得积分
    */
    public function postShareGetScore($input)
    {
        $validator = Validator::make($input, [
            'agent_id' => 'required|exists:agent,id',
        ]);

        if ($validator->fails()) {
            $warnings = $validator->messages();
            $show_warning = $warnings->first();
            return ['message' => $show_warning, 'status' => false];
        }


        //给积分  太多了，relation_id暂时存0
        $rel = Agentv010200::add($input['agent_id'], 1, 19, '分享', 0);
        if (!$rel) {
            return ['message' => '操作失败', 'status' => false];
        }

        return ['message' => '操作成功', 'status' => true];
    }


}