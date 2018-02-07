<?php namespace App\Services\Version\Agent\User;

use App\Models\Agent\Agent;
use App\Models\Agent\AgentBrand;
use App\Models\Agent\AgentCustomerLog;
use App\Models\Agent\ContractPayLog;
use App\Models\Agent\Invitation;
use App\Models\Brand\BrandContract;
use App\Models\Brand\Contactor;
use App\Models\Contract\Contract;
use App\Models\Brand\Entity as Brand;
use App\Models\User\Entity as User;
use App\Models\Zone\Entity as Zone;
use DB;
use Validator;
use App\Models\Agent\Entity\_v010200 as Agentv010200;
use App\Models\Agent\AgentAchievement;
use App\Models\Agent\AgentCurrencyLog;
use App\Models\Agent\CommissionLevelTemplate;




class _v010300 extends _v010200
{

    /*
     * 我的佣金
     */
    public function postMyCommission($data = [])
    {
        $user = Agent::where('id', $data['agent_id'])->first();
        //可提现余额
        $currency = abandonZero($user->currency);
        $quarter_info = Agent::instance()->getQuarter(time());

//        if (!empty($data['quarter_chioces'])) {
//            $quarter = $data['quarter_chioces'];
//        } else {
//            $quarter = $quarter_info[0];
//        }


        if (!empty($data['month_chioces'])) {
            $month = $data['month_chioces'];
        } else {
            $month = date('Y年m月');
        }

        //如果还没有发生过佣金
        $history_currency = AgentCurrencyLog::where('agent_id', $data['agent_id'])
            ->where('operation', 1)->where('status', 2)->sum('num');
        if (!$history_currency) {
            return ['status' => false, 'message' => '你还没有获得过佣金'];
        }


        $agentAchievement = AgentAchievement::where('agent_id', $data['agent_id'])
            ->where('month', $month)->first();

        if (!$agentAchievement) {
            $agentAchievement = AgentAchievement::create([
                'agent_id' => $data['agent_id'],
                'month' => $month,
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

        $sum_achievement = AgentAchievement::where('agent_id', $data['agent_id'])
            ->sum('total_achievement');

        //当前所处梯度
        $template = CommissionLevelTemplate::where('min', '<=', $sum_achievement)
            ->where('max', '>=', $sum_achievement)
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

//        //季度选项
//        $quarter_chioces = Agent::instance()->getQuarterChoice(time(), $user->created_at->timestamp);

        //月份选项
        $month_chioces = Agentv010200::getMonths($user->created_at->timestamp);

        $data = compact('currency', 'total_currency', 'frozen_currency',
            'my_orders', 'my_subordinate_orders', 'total_orders', 'level', 'total_commission',
            'my_commission', 'my_subordinate_commission', 'detail', 'month_chioces'
        );

        return ['status' => true, 'message' => $data];
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
        $records = AgentAchievement::where('agent_id', $data['agent_id'])->where('month', '<>', '')
            ->select('month', 'my_commission')
            ->get();

        $records = $records->sortBy(function($v){
            preg_match('/(\d+)年(\d+)月/', $v->month, $match);
            return ($match[1]*100+$match[2]);
        });



//        $res = [];
//        foreach ($records as $k => $v) {
//            $quarter = AgentAchievement::getInstance()->transformQuarter($v['quarter']);
//            $arr = [
//                'quarter' => $quarter,
//                'my_commission' => $v['my_commission']
//            ];
//
//            $res[] = $arr;
//        }

        return ['message' => $records, 'status' => true];

    }

    /**
     *   作者：shiqy
     *   创作时间：2018/1/22 0022 下午 2:42
     *   功能描述：商务负责品牌
     */

    public function postBusinessBrand($input)
    {
        $nowTime = time();
        $data = [];
        $agentId = intval($input['agent_id']);
        //获取该商务负责的所有的品牌id
        $brandIds = Contactor::where('agent_id' , $agentId)->lists('brand_id')->toArray();
        //获取所有的待确认和已确认的考察订单
        $allPayInvites = Invitation::with(['hasOneStore'=>function($query){
            $query->select('id','brand_id');
        }])->where('type',2)->whereIn('status',[1,2])
            ->whereHas("hasOneStore" , function($query)use($brandIds){
                $query->whereIn('brand_id' ,$brandIds );
            })->select('id','post_id','type','status','is_audit','uid')->get()->toArray();
        //待确认到票的票数
        $data['no_confirm_invites'] = trim(collect($allPayInvites)->filter(function($item){
            return $item['is_audit'] == 0;
        })->count());

        //获取所有加盟订单数
        $allContracts = Contract::whereIn('status' , [1,2,3,4,5,6])->whereIn('brand_id' , $brandIds)->select('id','status','uid')->get()->toArray();
        $groupContract = collect($allContracts)->groupBy(function($item){
            if(in_array($item['status'] , Contract::$hasPayStatus)){
                return 'complete';
            }
            else{
                return 'noComplete';
            }
        });
        //获取所有的未完成和已完成加盟订单数
        $data['no_complete_orders'] = empty($groupContract['noComplete']) ? '0' : trim($groupContract['noComplete']->count());
        $data['complete_orders'] = empty($groupContract['complete']) ? '0' : trim($groupContract['complete']->count());

        $data['brands'] = [];
        //获取单个品牌的相关信息
        foreach ($brandIds as $brandId){
            $agentBrandInfo = Brand::with(['categorys1'=>function ($query){
                $query->select('id','name');
            }])->where('id',$brandId)
                ->where('status','enable')->where('agent_status',1)
                ->select('id','categorys1_id','name','slogan','logo','agency_way')->first();
            if(!is_object($agentBrandInfo)){
                continue;
            }
            $arr = [];
            $arr['base_info']['brand_id'] = trim($brandId);
            $arr['base_info']['brand_img'] = getImage($agentBrandInfo['logo']);
            $arr['base_info']['brand_name'] = trim($agentBrandInfo['name']);
            $arr['base_info']['brand_slogan'] = trim($agentBrandInfo['slogan']);
            $arr['base_info']['brand_categorys'] = trim($agentBrandInfo->categorys1->name);
            $agencyWay = $agentBrandInfo->agentWay();
            $arr['base_info']['brand_area'] = trim($agencyWay['area']);
            $arr['base_info']['brand_channel'] = trim($agencyWay['channel']);

            $agentBrandLog = AgentBrand::with(['agent'=>function($query){
                $query->select('id','account_type')->where('account_type',3);
            }])->where('brand_id',$brandId)->select('agent_id','status')->get()->toArray();
            $collctAgentBrandLog = collect($agentBrandLog)->groupBy(function($item){
                if( in_array($item['status'] , [1,2,3])){
                    return 'study';
                }
                else if($item['status'] == 4){
                    return 'complete';
                }
                else{
                    return 'fail';
                }
            })->toArray();
            $innerAgents = count(collect($agentBrandLog)->filter(function($item){
                return !empty($item['agent']);
            })->pluck('agent_id')->unique()->count());

            //品牌学习经纪人
            $arr['agent']['brand_studys'] = '0';
            $arr['agent']['brand_study_ids'] = '';
            if(!empty($collctAgentBrandLog['study'])){
                $brandStudyArr = collect($collctAgentBrandLog['study'])->pluck('agent_id')->unique()->toArray();
                $arr['agent']['brand_studys'] = trim(count($brandStudyArr));
                $arr['agent']['brand_study_ids'] = trim(implode(',',$brandStudyArr));
            }
            $arr['agent']['brand_agents'] = '0';
            $arr['agent']['brand_agent_ids'] = '';
            if(!empty($collctAgentBrandLog['complete'])){
                $AgentbrandArr = collect($collctAgentBrandLog['complete'])->pluck('agent_id')->unique()->toArray();
                $arr['agent']['brand_agents'] = trim(count($AgentbrandArr));
                $arr['agent']['brand_agent_ids'] = trim(implode(',',$AgentbrandArr));
            }
            $totalPerson = collect($agentBrandLog)->pluck('agent_id')->unique()->count();
            $arr['agent']['passing_rate'] = trim($arr['agent']['brand_agents']/$totalPerson);
            $arr['agent']['inner_agents'] = trim($innerAgents);

            //总咨询人数，和最近7天咨询人数
            $allConsultInfo = AgentCustomerLog::where('brand_id',$brandId)->where('action' , 0)->select('uid','created_at')->get();
            $allConsultArr = collect($allConsultInfo)->pluck('uid')->unique()->toArray();
            $arr['customer']['accumulative_inquiries_num'] = trim(count($allConsultArr));
            $arr['customer']['accumulative_inquiries_ids'] = trim(implode(',',$allConsultArr));

            //该品牌已确认到票的人数
            $confirmInvites = collect($allPayInvites)->filter(function($item)use($brandId){
                return $item['is_audit'] == 1 && $item['has_one_store']['brand_id'] == $brandId;
            })->pluck('uid')->unique()->toArray();
            $arr['customer']['confirm_invites'] = trim(count($confirmInvites));
            $arr['customer']['confirm_invite_ids'] = trim(implode(',',$confirmInvites));

            //获取该品牌的加盟人数
            $arr['customer']['success_joins'] = '0';
            $arr['customer']['success_join_ids'] = '';
            if(!empty($groupContract['complete'])){
                $successJoinArr = $groupContract['complete']->filter(function($item)use($brandId){
                    return $item['brand_id'] == $brandId;
                })->pluck('uid')->unique()->toArray();
                $arr['customer']['success_joins'] = trim(count($successJoinArr));
                $arr['customer']['success_join_ids'] = trim(implode(',',$successJoinArr));
            }


            $sevenAgo = $nowTime - 86400 * 7;
            $latelyInquiriesNum = trim(collect($allConsultInfo)->filter(function($item)use($sevenAgo){
                return $item['created_at'] >= $sevenAgo;
            })->pluck('uid')->unique()->count());
            $arr['customer']['lately_inquiries_num'] = trim($latelyInquiriesNum);

            //获取合同范本信息
            $brandContractInfo = BrandContract::where('brand_id',$brandId)->where('is_delete',0)->select('league_type_id')->get()->toArray();
            $arr['contract']['area_contracts'] = trim(collect($brandContractInfo)->filter(function($item){
                return $item['league_type_id'] == 2;
            })->count());
            $arr['contract']['channel_contracts'] = trim(collect($brandContractInfo)->filter(function($item){
                return $item['league_type_id'] == 4;
            })->count());
            $data['brands'][] = $arr;
        }
        return ['message'=>$data , 'status'=>true];
    }

    /**
     *   作者：shiqy
     *   创作时间：2018/1/25 0025 下午 2:51
     *   功能描述：获取考察订单
     */

    public function postInspectOrder($input){
        $agentId = intval($input['agent_id']);
        $type = empty($input['type']) ? 0 :intval($input['type']);
        $page = empty($input['page']) ? 1 :intval($input['page']);
        $pageSize = empty($input['page_size']) ? 10 :intval($input['page_size']);

        $nowTime = time();

        //获取该商务负责的品牌
        $brandIdArr = Contactor::where('agent_id' , $agentId)->lists('brand_id')->toArray();

        $builder = Invitation::with(['hasOneOrderItems.orders'=>function($query){
                $query->select('order_no','pay_way','status','amount','id')
                    ->where('status','pay');
            }])
            ->with(['hasOneOrderItems'=>function($query){
                $query->select('order_id','type','product_id','id')
                    ->where('status','pay');
            }])
            ->with(['hasOneStore.hasOneBrand'=>function($query){
                $query->where('status','enable')->where('agent_status',1)
                    ->select('name','id');
            }])
            ->with(['hasOneUsers'=>function($query){
                $query->whereIn('status',[1,2,3])->select('uid','username','nickname','realname','non_reversible');
            }])
            ->with(['belongsToAgent'=>function($query){
                $query->where('status',1)->select('id','username','nickname','realname','non_reversible');
            }])
            ->where(function ($query){
                $query->where('type',2)->whereIn('status',Invitation::$afterComfirmStatus);
            })
            ->whereHas('hasOneStore', function($query)use($brandIdArr){
                $query->where('is_delete',0)->whereIn('brand_id',$brandIdArr);
            })
            ->orderBy('created_at','desc')
            ->skip($page)->take($pageSize);
        if($type){
            $builder = $builder->where('is_audit',1);
        }
        else{
            $builder = $builder->where('is_audit',0);
        }
        $invitationList = $builder->get()->toArray();
        $data = [];
        foreach ($invitationList as $oneInvite){
            $arr = [];
            $arr['id'] = trim($oneInvite['id']);
            $arr['order_no'] = trim($oneInvite['has_one_order_items'][0]['orders']['order_no']);
            $arr['remain_time'] = '';
            $arr['brand_name'] = trim($oneInvite['has_one_store']['has_one_brand']['name']);
            $arr['user_name'] = $oneInvite['has_one_users']['realname'] ?  : $oneInvite['has_one_users']['nickname'];
            $arr['user_phone'] = trim($oneInvite['has_one_users']['non_reversible']) ;
            $arr['agent_name'] = $oneInvite['belongs_to_agent']['realname'] ? : $oneInvite['belongs_to_agent']['nickname'];
            $arr['agent_phone'] = trim($oneInvite['belongs_to_agent']['non_reversible']);
            $arr['payment'] = doFormatMoney($oneInvite['default_money']);
            $arr['pay_way'] = trim($oneInvite['has_one_order_items'][0]['orders']['pay_way']);
            $arr['store_name'] = trim($oneInvite['has_one_store']['name']);
            $arr['inspect_time'] = trim($oneInvite['inspect_time']);
            $isAudit = intval($oneInvite['is_audit']);
            if($isAudit == 1){
                $arr['status'] = '1';
            }
            else if($isAudit == -1){
                $arr['status'] = '2';
            }
            else{
                if($nowTime > $arr['inspect_time']){
                    $arr['status'] = '4';
                }
                else{
                    $arr['status'] = '3';
                    $remaindTime = ($arr['inspect_time'] - $nowTime)/86400;
                    $arr['remain_time'] = trim(floor($remaindTime));
                }
            }
            $data[] = $arr;
        }
        return ['message'=>$data ,'status'=>true];
    }

    /**
     *   作者：shiqy
     *   创作时间：2018/1/25 0025 下午 5:15
     *   功能描述：获取加盟合同订单列表
     */
    public function postLeagueOrder($input){
        $agentId = intval($input['agent_id']);
        $type = empty($input['type']) ? 0 :intval($input['type']);
        $page = empty($input['page']) ? 1 :intval($input['page']);
        $pageSize = empty($input['page_size']) ? 10 :intval($input['page_size']);

        //获取该商务负责的品牌
        $brandIdArr = Contactor::where('agent_id' , $agentId)->lists('brand_id')->toArray();
        $builder = Contract::with(['orders_items.orders'=>function($query){
                    $query->select('order_no','pay_way','status','amount','id')
                        ->where('status','pay');
                }])
                ->with(['orders_items'=>function($query){
                    $query->select('order_id','type','product_id','id','created_at')
                        ->where('status','pay');
                }])
                ->with(['brand'=>function($query){
                    $query->where('status','enable')->where('agent_status',1)
                        ->select('name','id');
                }])
                ->with(['user'=>function($query){
                    $query->whereIn('status',[1,2,3])->select('uid','username','nickname','realname','non_reversible');
                }])
                ->with(['agent'=>function($query){
                    $query->where('status',1)->select('id','username','nickname','realname','non_reversible');
                }])
                ->with(['brand_contract'=>function($query){
                    $query->where('is_delete',0)->select('id','league_type_id');
                }])
                ->whereIn('brand_id',$brandIdArr)
                ->whereHas('orders_items' , function ($query){
                    $query->where('type','contract');
                });
        if($type){
            $builder = $builder->whereIn('status',[2,4,5]);
        }
        else{
            $builder = $builder->whereIn('status',[0,3]);
        }
        $contractList = $builder->skip($page)->take($pageSize)->get()->toArray();
        $data = [];
        foreach ($contractList as $oneContract){
            $arr = [];
            $arr['id'] = trim($oneContract['id']);
            $arr['order_no'] = trim($oneContract['orders_items'][0]['orders']['order_no']);
            $arr['contract_no'] = trim($oneContract['contract_no']);
            $arr['brand_name'] = trim($oneContract['brand']['name']);
            $arr['user_name'] = empty($oneContract['user']['realname']) ? trim($oneContract['user']['nickname']) :trim($oneContract['user']['realname'])  ;
            $arr['user_phone'] = trim($oneContract['user']['non_reversible']);
            $arr['agent_name'] = empty($oneContract['agent']['realname']) ? trim($oneContract['agent']['nickname']) :trim($oneContract['agent']['realname'])  ;
            $arr['agent_phone'] = trim($oneContract['agent']['non_reversible']);
            $arr['league_type'] = trim($oneContract['brand_contract']['league_type_id']);
            $arr['total_amount'] = doFormatMoney(floatval($oneContract['amount']));
            $arr['produce_time'] = trim($oneContract['orders_items'][0]['created_at']);
            $arr['status'] = trim($type);
            $arr['settle_time'] = '';
            if($type){
                $arr['settle_time'] = trim($oneContract['tail_pay_at']);
            }

            //获取支付清单信息
            $discountsPayLog = ContractPayLog::getPayDetailByType($oneContract['id'],ContractPayLog::$_REFUND_TYPES , 'success');
            $arr['discounts'] = doFormatMoney(floatval($discountsPayLog['total']));
            $posPayLog = ContractPayLog::getPayDetailByType($oneContract['id'],ContractPayLog::$_PAY_TYPES , 'success');
            $arr['total_pay'] = doFormatMoney(floatval($posPayLog['total']));
            $arr['residual'] = doFormatMoney(floatval($oneContract['amount'] - $posPayLog['total']));
            $data[] = $arr;
        }
        return ['message'=>$data ,'status'=>true];
    }

    /**
     *   作者：shiqy
     *   创作时间：2018/1/26 0026 下午 4:12
     *   功能描述：展示经纪人或投资人列表
     */
    public function postPersonList($input){
        $idsStr = trim($input['person_ids']);
        $type = intval($input['type']);
        $ids = explode(',',$idsStr);
        $ids = array_filter($ids);
        if(empty($ids)){
            return ['message'=>'请传入有效的id字符串','status'=>false];
        }
        $data = [];
        if($type == 1){
            $agentList = Agent::whereIn('id',$ids)->where('status',1)
                ->select('id','avatar','gender','zone_id','realname','nickname','is_public_realname')->get()->toArray();
            foreach ($agentList as $oneAgent){
                $arr = [];
                $arr['id'] = trim($oneAgent['id']);
                $arr['avatar'] = getImage($oneAgent['avatar']);
                $arr['gender'] = trim($oneAgent['gender']);
                $arr['zone'] = Zone::getCityAndProvince($oneAgent['zone_id']);
                $arr['name'] = Agent::unifiHandleName($oneAgent , '','agent');
                $data[] = $arr;
            }
        }
        else{
            $userList = User::whereIn('uid',$ids)->whereIn('status',[1,2,3])
                ->select('uid','avatar','gender','zone_id','realname','nickname')->get()->toArray();
            foreach ($userList as $oneUser){
                $arr = [];
                $arr['id'] = trim($oneUser['uid']);
                $arr['avatar'] = getImage($oneUser['avatar']);
                $arr['gender'] = trim($oneUser['gender']);
                $arr['zone'] = Zone::getCityAndProvince($oneUser['zone_id']);
                $arr['name'] = empty($oneUser['realname']) ? trim($oneUser['nickname']) : trim($oneUser['realname']);
                $data[] = $arr;
            }
        }
        //名字按照首字母分组
        $data = collect($data)->groupBy(function($item){
            return getfirstchar($item['name']);
        })->sortBy(function($item , $key){
            return $key;
        })->toArray();
        $list = [];
        foreach ($data as $key=>$val){
            $arr = [];
            $arr['first_letter'] = trim($key);
            $arr['list'] = $val;
            $list[] = $arr;
        }
        return ['message'=> $list,'status'=>true];
    }

    /**
     *   作者：shiqy
     *   创作时间：2018/1/30 0030 下午 3:12
     *   功能描述：获取一个经纪人的关系
     */
    public function postAgentRelation($input){
        $typeStr = trim($input['type']);
        $agentId = intval($input['agent_id']);
        $typeArr = explode(',',$typeStr);
        $typeArr = array_filter($typeArr);
        if(empty($typeArr)){
            return ['message'=>'类型错误' , 'status'=>false];
        }
        $data = [];
        $agentInfo = Agent::find($agentId);
        foreach ($typeArr as $type){
            //获取上级
            if($type == 1){
                $pAgentInfo = Agent::where('non_reversible',$agentInfo['register_invite'])->where('status',1)->first();
                $data['up'] = [];
                if(!is_object($pAgentInfo)){
                    continue;
                }
                $data['up'][] = $pAgentInfo->toArray();
            }
            else if($type == 2){
                //获取直接下级
                $downLine = Agent::instance()->getDirectlySubordinate($agentInfo['non_reversible']);
                $data['down'] = $downLine->toArray();
            }
            else if($type == 3){
                $data['friend'] = Agent::agentAllFriends($agentId);
            }
        }
        $only = ['id','username','avatar','gender','zone_id','realname','nickname'];

        //统一封装
        foreach ($data as $key => &$val){
            foreach ($val as &$one){
                $one = array_only($one , $only);
                $one['avatar'] = getImage($one['avatar']);
                $one['zone'] = Zone::getCityAndProvince($one['zone_id']);
            }
        }
        return ['message'=>$data , 'status'=>true];
    }

    /**
     *   作者：shiqy
     *   创作时间：2018/2/2 0002 下午 3:07
     *   功能描述：考察邀请函审核
     */
    public function postInspectAudit($input){
        try{
            $auditorId = intval($input['auditor_id']);
            $invitationId = intval($input['invitation_id']);
            $status = intval($input['status']);
            $data = [];
            $data['is_audit'] = $status;
            $data['auditor_id'] = $auditorId;
            $data['auditor_type'] = 1;
            Invitation::where('id' ,$invitationId )->update($data);
            return ['message'=>'审核成功' , 'status'=>true];
        }
        catch (\Exception $e){
            return ['message'=>$e->getMessage() , 'status'=>false];
        }
    }

}