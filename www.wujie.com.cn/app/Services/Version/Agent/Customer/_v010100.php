<?php namespace App\Services\Version\Agent\Customer;

use App\Models\Agent\AgentCustomer;
use App\Models\Agent\AgentCustomerLog;
use App\Models\Zone\Entity as Zone;

use Log;

class _v010100 extends _v010003
{

    //客户列表
    public function postList($input){
        $orderBy = trim($input['order_by']);
        $filter = trim($input['filter']);
        $agentId = intval($input['agent_id']);
        $nowTime = time();
        $agentCustomInfo = AgentCustomer::with('hasManyAgentCustomerLogs','hasManyActivitySign.hasOneActity','invitation','user','contracts')
            ->with(['hasManyAgentCustomerLogs' => function($query){
                $query->orderBy('created_at','desc')->select('agent_customer_id','remark');
//                $query->orderBy('created_at','desc');
            }])
            ->with(['hasManyActivitySign.hasOneActity'=>function($query){
                $query->select('id','begin_time','end_time');
            }])
            ->with(['hasManyActivitySign'=>function($query)use($agentId){
                $query->where('is_invite',1);
                $query->where('agent_id',$agentId);
                $query->select('uid','status','agent_id','activity_id');
            }])
            ->with(['invitation'=>function($query)use($agentId){
                $query->where('type',2);
                $query->where('agent_id',$agentId);
                $query->whereIn('status',[1, 2 , 3]);
                $query->select('agent_id','uid','type','inspect_time','status');
            }])
            ->with(['contracts'=>function($query)use($agentId){
                $query->where('agent_id',$agentId);
                $query->whereIn('status',[1,2]);
                $query->select('agent_id','uid','status');
            }])
            ->with(['user'=>function($query){
                $query->select('uid','nickname','avatar','zone_id','gender');
            }])
            ->where('agent_id',$agentId)->whereIn('source',[1,2,3,4,5,6,7])->get()->toArray();
        $data = [];
        //获取派单获客总人数 和 邀请客户总人数

        $groupAgentCustomer = collect($agentCustomInfo)->groupBy(function ($item) {
            if($item['source'] == 5){
                return 'send';
            }
            else if($item['source'] == 6 || $item['source'] == 7){
                return 'all';
            }
            else{
                return 'invite';
            }
        })->toArray();
        $data['send_customers'] = trim(count($groupAgentCustomer['send']) + count($groupAgentCustomer['all']));
        $data['invite_customers'] = trim(count($groupAgentCustomer['invite']) + count($groupAgentCustomer['all']));

        //获取活动提醒人数和考察提醒人数
        $activityReminds = 0;
        $inspectReminds = 0;

        foreach ($agentCustomInfo as $one){
            //判断有没有活动提醒
            $isActivtyTip = collect($one['has_many_activity_sign'])->filter(function($item)use($nowTime){
                return $item['status'] == 0 && $item['has_one_actity']['end_time'] > $nowTime;
            })->isEmpty();
            if(!$isActivtyTip){
                $activityReminds++;
            }
            //判断有没有考察提醒
            $isActivtyTip = collect($one['invitation'])->filter(function($item)use($nowTime){
                return $item['inspect_time'] > $nowTime;
            })->isEmpty();
            if(!$isActivtyTip){
                $inspectReminds++;
            }
        }
        $data['activity_reminds'] = trim($activityReminds);
        $data['inspect_reminds'] = trim($inspectReminds);

        $agentCustomerCollect = collect($agentCustomInfo);
        //排序
        if ($orderBy == 'intention') {
            $agentCustomerCollect = $agentCustomerCollect->sortByDesc(function ($item) {
                return $item['user']['invest_intention'];
            });
        } else if ($orderBy == 'active') {
            $agentCustomerCollect = $agentCustomerCollect->sortByDesc(function ($item) {
                return $item['user']['login_count'];
            });
        } else if ($orderBy == 'followed_time') {
            $agentCustomerCollect = $agentCustomerCollect->sortByDesc(function ($item) {
                return $item['created_at'];
            });
        }


        //过滤
        if ($filter == 'ovo') {
            $agentCustomerCollect = $agentCustomerCollect->filter(function ($item) {
                $noHave = collect($item['has_many_activity_sign'])->filter(function($one){
                    return $one['status'] == 1;
                })->isEmpty();
                return !$noHave;
            });
        } else if ($filter == 'inspected') {
            $agentCustomerCollect = $agentCustomerCollect->filter(function ($item) {
                if(!empty($item['invitation'])){
                    return true;
                }
                return false;
            });
        } else if ($filter == 'signed_contract') {
            $agentCustomerCollect = $agentCustomerCollect->filter(function ($item) {
                if(!empty($item['contracts'])){
                    return true;
                }
                return false;
            });
        }

        foreach ($agentCustomerCollect as $oneAgentCustomer) {
            $levelStr = "";
            switch ($oneAgentCustomer['level']) {
                case -1:
                    $levelStr = '遗失客户';
                    break;
                case 1:
                    $levelStr = '普通客户';
                    break;
                case 2:
                    $levelStr = '主要客户';
                    break;
                case 3:
                    $levelStr = '关键客户';
                    break;
            }
//            $lastRemark = '';
//            $customerLogs = AgentCustomerLog::where('agent_customer_id' , $oneAgentCustomer['id'])
//                ->orderBy('created_at','desc')->first();
//            $lastRemark = trim($customerLogs['remark']);
            $oneData = array(
                'avatar' => getImage($oneAgentCustomer['user']['avatar'], 'avatar' ,''),
                'nickname' => trim($oneAgentCustomer['user']['nickname']),
                'gender' => trim($oneAgentCustomer['user']['gender']),
                'city' => trim(Zone::getCityAndProvince($oneAgentCustomer['user']['zone_id'])),
                'level' => trim($levelStr),
                'remark' => empty($oneAgentCustomer['has_many_agent_customer_logs']) ? '' : trim($oneAgentCustomer[0]['remark']) ,
                'uid' => trim($oneAgentCustomer['uid']),
            );
            if (in_array($oneAgentCustomer['source'], [5, 6, 7])) {
                $data['send_customer_list'][] = $oneData;
            }
            if ($oneAgentCustomer['source'] != 5) {
                $data['invite_customer_list'][] = $oneData;
            }
        }
//        对结果按昵称的汉语拼音首字母正序排序
        if (!empty($data['send_customer_list'])) {
            $data['send_customer_list'] = collect($data['send_customer_list'])->sortBy(function ($item) {
                return getfirstchar($item['nickname']);
            })->toArray();
        }
        if (!empty($data['invite_customer_list'])) {
            $data['invite_customer_list'] = collect($data['invite_customer_list'])->sortBy(function ($item) {
                return getfirstchar($item['nickname']);
            })->toArray();
        }
        return ['message'=>$data ,'status'=>true];
    }

}