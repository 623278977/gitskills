<?php namespace App\Services\Version\Agent\User;

use App\Models\Agent\AgentScreenCapture;
use App\Services\Version\VersionSelect;
use DB;
use Validator;
use App\Models\Agent\Agent;
use App\Models\Zone\Entity as Zone;
use App\Models\Live\Entity as Live;
use App\Models\Activity\Ticket;
use App\Models\User\Entity as User;
use App\Models\Video;
use App\Models\Agent\AgentTicket;
use App\Models\Agent\Activity\Sign;




use App\Models\Activity\Sign as ActivitySign;
use App\Models\Agent\AgentAchievementLog;
use App\Models\Agent\AgentWithdraw;
use App\Models\Brand\Enter;
use App\Models\Agent\AgentAchievement;
use App\Models\Agent\AgentCurrencyLog;
use App\Models\Agent\CommissionLevelTemplate;
use App\Models\Orders\Items;
use App\Models\Contract\Contract;
use App\Models\Orders\Entity as Orders;
use App\Models\Agent\Invitation;
use App\Models\Activity\Entity as Activity;


class _v010005 extends _v010003
{

    /**
     * 我的下级  接口添加下级经纪人拉新数据
     * @User shiqy
     * @param $input
     * @return array
     */
    public function postSubordinate($input)
    {
        $agentInfo = Agent::find($input['agent_id']);
        if($agentInfo['status'] != 1){
            return ['message'=>'经纪人无效','status'=>false];
        }

        //获取经纪人上级
        $superior = 0;
        $registerInvite = trim($agentInfo['register_invite']);
        if (!empty($registerInvite)) {
            $superiorInfo = Agent::where('non_reversible', $registerInvite)->where('status',1)->first();
            if (is_object($superiorInfo)) {
                $superior = $superiorInfo['id'];
                $super['id'] = $superiorInfo['id'];
                $super['avatar'] = getImage($superiorInfo["avatar"]);
                $super['nickname'] = trim($superiorInfo['nickname']);
                $super['realname'] = trim($superiorInfo['realname']);
                $super['city'] = Zone::getCityAndProvince($superiorInfo["zone"]['id']);
            }
        }
        //获取下级信息
        $downLines = Agent::with('zone','hasOneAgentLevel','agent_develop_team_log','c_agent')
                ->with(['agent_develop_team_log'=>function($query){
                    $query->where('type',1)->select('id','agent_id');
                }])
                ->where('register_invite', $agentInfo['non_reversible'])
                ->get()->toArray();

        $downLineList = array();
        foreach ($downLines as $downLineInfo) {
            $downLineList[] = array(
                "id" => intval($downLineInfo["id"]),
                'gender' => trim($downLineInfo["gender"]),
                'avatar' => getImage($downLineInfo["avatar"]),
                'nickname' => trim($downLineInfo["nickname"]),
                'realname' => trim($downLineInfo["realname"]),
                'level' => trim($downLineInfo["has_one_agent_level"]['name']),
                'city' => Zone::getCityAndProvince($downLineInfo["zone"]['id']),
                'created_at' => trim($downLineInfo["created_at"]),
                'downlines'=> trim(count($downLineInfo["c_agent"])),
                'teams'=>trim(count($downLineInfo['agent_develop_team_log']) + 1),
            );
        }
        $count = count($downLineList);
        $data = array(
            "list" => $downLineList,
            "count" => $count,
            'superior' => trim($superior),
            'super' => $super,
        );
        return ['message'=>$data , 'status'=>true];
    }




    /*
 * 在经纪人我的门票列表
 * */

    public function postUserticketlist($param)
    {
        if (empty($param['id'])) {
            return ['message' => '经纪人id必填', 'status' => false];
        }

        if (!Agent::find($param['id'])) {
            return ['message' => '非法的id', 'status' => false];
        }

        $page = array_get($param, 'page', 1);
        $pageSize = array_get($param, 'pageSize', 15);


        $where = array();
        $where['agt.agent_id'] = $param['id'];

        $return = [];

        $tickets = AgentTicket::getTicketsList($where, $page, $pageSize);



        if ($tickets) {
            $_NOW=time();
            foreach ($tickets as $k => $item) {
                $return[$item['group']][$k]['group'] = $item['group'];
                $return[$item['group']][$k]['subject'] = $item['subject'];
                $return[$item['group']][$k]['begin_time'] = date('Y年m月d日 H:i', $item['begin_time_raw']);
                $return[$item['group']][$k]['surplus_time'] = $item['begin_time_raw'] > $_NOW ? $item['begin_time_raw'] - $_NOW : -1;
//                $return[$item['group']][$k]['ticket_type'] = $item['type'];
//                $return[$k]['score_price'] = '免费';
//                $return[$k]['online_money'] = $item['online_money'];
                $ticket = Ticket::find($item['aid']);
                $ticket_name = $ticket ? $ticket->name : '';
                $return[$item['group']][$k]['ticket_name'] = $ticket_name?"现场票({$ticket_name})":"现场票";
                $return[$item['group']][$k]['ticket_status'] = $this->getTicketStatus($item, $param['id']);
                $return[$item['group']][$k]['activity_id'] = $item['activity_id'];
                $return[$item['group']][$k]['is_sign'] = $item['is_sign'];
                $return[$item['group']][$k]['ticket_url'] = $item['ticket_url'];
                $return[$item['group']][$k]['is_over'] = $item['is_over'];
                $return[$item['group']][$k]['maker_id'] = $item['maker_id'];
                $return[$item['group']][$k]['maker_subject'] = $item['maker_subject'];
                $return[$item['group']][$k]['address'] = $item['address'];
                $return[$item['group']][$k]['tel'] = $item['tel'];
                $return[$item['group']][$k]['city'] = [$item['city']];
                if($item['upid'] && $zone=\App\Models\Zone::find($item['upid'], ['name'])){
                    array_unshift($return[$item['group']][$k]['city'], $zone->name);
                }
                $video = Video::where('activity_id', $item['activity_id'])->where('status', 1)->where('agent_status', 1)->first();
                $return[$item['group']][$k]['video_id'] = $video ? $video->id : 0;
                $return[$item['group']][$k]['ticket_id'] = $item['id'];
                $return[$item['group']][$k]['is_check'] = $item['is_check'];
                $return[$item['group']][$k]['activity_ticket_id'] = $item['aid'];
                $return[$item['group']][$k]['can_sign'] = date('Y-m-d') ==date('Y-m-d', $item['begin_time_raw']) ?1:0;

                if(date('Y-m-d') ==date('Y-m-d', $item['begin_time_raw']) && time()<$item['end_time_raw'] && !$item['is_check']){
                    $return[$item['group']][$k]['can_sign'] = 1;
                }else{
                    $return[$item['group']][$k]['can_sign'] = 0;
                }
            }
        }


        return ['message' => $return, 'status' => true];
    }



    /*
 * 获取票券状态
 */
    protected function getTicketStatus(&$item, $uid)
    {
        $group = $item['group'];
        $item['is_sign'] = 0;

        switch ($group) {
            case 'starting':
                if ($item['type'] == '现场票') {
                    $sign = Sign::where('agent_id', $uid)
                        ->where('activity_id', $item['activity_id'])
                        ->first();
                    if (@$sign->status == 0) {
                        return '未签到，请及时赴会签到';
                    } elseif ($sign->status == 1) {
                        $item['is_sign'] = 1;
                        return '活动已开始';
                    }
                }


                break;
            case 'no_start':
                if ($item['is_check']) {
                    return '已签到';
                }
                return '未使用';
                break;
            case 'end':
                if ($item['type'] == '现场票') {
                    $sign = Sign::where('agent_id', $uid)
                        ->where('activity_id', $item['activity_id'])
                        ->first();
                    if (@$sign->status == 0) {
                        return '已过期 (未签到)';
                    } elseif ($sign->status == 1) {
                        $item['is_sign'] = 1;
                        return '已签到';
                    }
                }

                break;
            default:
                return '';
                break;
        }
    }

   /*
    * 我的界面详情
    * 添加：2天内是否有活动要参加
    * shiqy
    * */
    public function postIndex($input)
    {
        $result = parent::postIndex($input);
        if(!$result['status']){
            return $result;
        }
        $data = $result['message'];
        $isHave = 0;
        AgentTicket::isHaveActiveTwoDay($input['agent_id']) && $isHave = 1;
        $data['is_have_active'] = $isHave;
        return ['message' => $data, 'status' => true];
    }

    /**
     * 佣金详情   --数据中心版
     * @User   tangjb
     * @param $data
     * @return array
     */
    public function postCommissionDetail($data)
    {
        //如果是成单奖励
        if ($data['type'] == 4) {
            $log = AgentAchievementLog::with('agent', 'contract.user', 'contract.brand','contract.brand_contract', 'contract.fund', 'contract.invitation')
                ->where('id', $data['id'])->first();
            $commission = numFormatWithComma(abandonZero($log->commission));
            $customer_name = $log->contract->user->realname ?$log->contract->user->realname:$log->contract->user->nickname;
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

            $order->orders->buyer_id? $pay_way = Orders::$_PAYWAY[$order->orders->pay_way] .'('. $order->orders->buyer_id.')':$pay_way = Orders::$_PAYWAY[$order->orders->pay_way] ;

            $online_pay_at = date('Y-m-d H:i:s', $order->orders->pay_at);
            $bank_no = digitalStarReplace($log->contract->bank_no);
            $tail_pay_time = date('Y-m-d H:i:s', $log->contract->tail_pay_at);
            $agent_name = $log->agent->realname ? $log->agent->realname :$log->agent->nickname;
            //加盟方式
            $league_type = $log->contract->brand_contract->getleague();
            //佣金可提成金额

            //费用结清日期
            $in_account_time= date('Y/m/d', $log->created_at->timestamp);

            $data = compact('commission', 'customer_name', 'brand_title', 'amount', 'pre_pay','created_at',
                'agent_name', 'tail_pay', 'discount_fee', 'online_pay', 'pay_way', 'online_pay_at',
                'tail_pay', 'tail_pay_time', 'bank_no', 'league_type', 'in_account_time');
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
            $contract = Contract::with('user', 'brand', 'user_fund','brand_contract', 'invitation', 'agent')->where('id', $data['id'])->first();


            //相关的首付订单
            $order = Items::with('orders')->where('status', 'pay')
                ->where('type', 'contract')
                ->where('product_id', $data['id'])->first();

            $commission = '1,000';
            $customer_name =$contract->user->realname?$contract->user->realname:$contract->user->nickname;
            $customer_name.= "(您为{$customer_name}的邀请人)";
            $brand_title = $contract->brand->name;
            $amount = numFormatWithComma(abandonZero($contract->amount));
            $pre_pay = numFormatWithComma(abandonZero($contract->pre_pay));
            $tail_pay = numFormatWithComma(abandonZero($contract->amount - $contract->pre_pay));
            $discount_fee = numFormatWithComma(abandonZero($contract->user_fund->fund + $contract->invitation->default_money));
            $order->orders->buyer_id? $pay_way = Orders::$_PAYWAY[$order->orders->pay_way] .'('. $order->orders->buyer_id.')':$pay_way = Orders::$_PAYWAY[$order->orders->pay_way] ;

            $online_pay_at = date('Y-m-d H:i:s', $order->orders->pay_at);
            $created_at = date('Y年m月d日 H:i:s', $contract->created_at->timestamp);


            $online_pay = numFormatWithComma(abandonZero($contract->pre_pay - ($contract->user_fund->fund + $contract->invitation->default_money)));
//            前四位后四位显示中间*代替
            $bank_no = digitalStarReplace($contract->bank_no);
            $tail_pay_time = date('Y-m-d H:i:s', $contract->confirm_time);
            $agent_name = $contract->agent->realname;
            //加盟方式
            $league_type = $contract->brand_contract->getleague();

            $data = compact('commission', 'customer_name', 'brand_title', 'amount', 'pre_pay','created_at','league_type',
                'discount_fee', 'online_pay', 'pay_way', 'online_pay_at', 'tail_pay', 'tail_pay_time', 'bank_no', 'agent_name');
        }


        //如果是提现
        if ($data['type'] == 1) {
            $withdraw = AgentWithdraw::where('id', $data['id'])->first();
            $commission = abandonZero($withdraw->money);
            $auth_time = $apply_time = date('m-d H:i', $withdraw->created_at->timestamp);
            if ($withdraw->status == 1) {
                $pay_time ='预计'.date('m-d', $withdraw->created_at->timestamp + 24 * 3 * 3600);
                $in_account_time = '预计3个工作日内';
            } elseif(2==$withdraw->status) {
                $pay_time = date('m-d H:i', $withdraw->updated_at->timestamp);
                $in_account_time = date('Y-m-d H:i', $withdraw->updated_at->timestamp);
            }else{
                $pay_time ='审核失败';
                $withdraw->remark ?  $in_account_time = '审核失败,'.$withdraw->remark :$in_account_time = '审核失败';
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
            $realname .= '('.$agent->username.')';
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
            $realname .= '('.$agent->username.')';

            $data = compact('commission', 'realname', 'created_time');
        }


        //如果是发展投资人   自注册2天内，每天都查看品牌详情，且至少有3次不少于3分钟
        if (10 == $data['type']) {
            $log = AgentCurrencyLog::where('id', $data['log_id'])->first();
            $created_time = date('Y-m-d H:i', $log->created_at->timestamp);
            $commission = $log->num;
            $user = User::where('uid', $data['id'])->first();
            $register_time = date('Y/m/d', $user->created_at->timestamp);
            $user->realname?$realname = $user->realname:$realname = $user->nickname;
            $realname .= '('.$user->username.')';

            $data = compact('commission', 'realname', 'created_time', 'register_time');
        }

        //如果是三星主管  四星主管或者五星主管
        if (in_array($data['type'], [11,12,13])) {
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
            $commission = abandonZero($log->num);

            //活动名称
            $sign = ActivitySign::find($data['id']);
            $activity_name = $sign->hasOneActity->subject;
            $begin_time = date('Y-m-d', $sign->hasOneActity->begin_time);
            $sign_time = date('Y-m-d', $sign->sign_time);

            //签到会场
            $maker_name = str_replace('市', '',  $sign->belongsToMaker->zone->name).' '.$sign->belongsToMaker->subject;

            //签到投资人
            $sign->user->realname ?$username = $sign->user->realname:$username = $sign->user->nickname;

            $username .='('.$sign->user->username.')';

            $data = compact('commission', 'created_time', 'activity_name', 'begin_time', 'sign_time', 'maker_name', 'username');
        }



        //如果是到票
        if (in_array($data['type'], [15])) {
            $log = AgentCurrencyLog::where('id', $data['log_id'])->first();

            $invitation = Invitation::with('belongsToAgent', 'hasOneStore.hasOneBrand.contactor', 'hasOneStore.hasOneZone', 'hasOneUsers')->where('id', $data['id'])->first();
            //投资人
            $invitation->hasOneUsers->nickname?$realname = $invitation->hasOneUsers->nickname:$realname = $invitation->hasOneUsers->realname;
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


            if($invitation->hasOneStore->hasOneBrand->contactor->name){
                $contactor_name = $invitation->hasOneStore->hasOneBrand->contactor->name;
            }else{
                $agent = Agent::find($invitation->hasOneStore->hasOneBrand->contactor->agent_id);
                $contactor_name = $agent->realname;
            }


            //品牌商务对接
            $agent_name = $contactor_name.'('.$invitation->hasOneStore->hasOneBrand->contactor->tel.')';


            $created_time = date('Y-m-d H:i', $log->created_at->timestamp);
            $commission = abandonZero($log->num);
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
            $realname .= '('.$agent->username.')';
            $brand_name = $enter->brand->name;
            $enter_time = date('Y/m/d', $enter->created_at->timestamp);

            $data = compact('commission', 'created_time', 'realname', 'brand_name', 'enter_time');
        }


        //点赞截屏
        if (in_array($data['type'], [17])) {
            $log = AgentCurrencyLog::where('id', $data['log_id'])->first();

            //佣金
            $commission = 5;
            //点赞截屏
            $enter = AgentScreenCapture::find($data['id']);

            //上传时间
            $created_time = $enter->created_at->timestamp;
            $check_at = $enter->check_at;

            $data = compact('commission', 'created_time', 'check_at');
        }



        return ['status' => true, 'message' => $data];
    }
}