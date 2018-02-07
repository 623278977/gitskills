<?php

namespace App\Services\Version\Agent\Customer;

use App\Services\Version\VersionSelect;
use App\Models\Contract\Contract;
use App\Models\Agent\Agent;
use App\Models\User\Entity as User;
use App\Models\Agent\AgentCustomer;
use App\Services\Version\Message\_v020800;

class _v010000 extends VersionSelect
{
    /**
     * 作用:加盟合同概览
     *
     * 返回值:
     */
    public function postContracts($input = [])
    {
        $agent_id = $input['agent_id'];
        $status = $input['status'];
        $customer_id = $input['customer_id'];
        //相关合同统计
        $data = Contract::ContractDetail($agent_id, $status, $customer_id,'','',true);

        return ['message' => $data, 'status' => true];
    }

    /**
     * 作用:跟进情况-加盟合同
     *
     * 返回值:
     */
    public function postRecordsContract($input = [])
    {
        $agent_id = $input['agent_id'];
        $status = $input['status'];
        $customer_id = $input['customer_id'];
        $brand_id = $input['brand_id'];

        //相关合同统计
        $data = Contract::ContractDetail($agent_id, $status, $customer_id,$brand_id,'',true);

        return ['message' => $data, 'status' => true];

    }

    /*
     *客户搜索
     *
     * */
    public function postSearch($input = []){
        $agentId=intval($input['agent_id']);
        $type=trim($input['type']);
        $content=trim($input['content']);
        if(empty($agentId)){
            return ['message' => "请传递经纪人id",'status' => false];
        }
        if(empty($content)){
            return ['message' => "搜索内容不能为空",'status' => false];
        }
        if(!in_array($type,['brand','customer'])){
            return ['message' => "请输入正确的搜索类型",'status' => false];
        }
        $agentInfo=Agent::getSearchInfo($agentId,$type,$content);
        if(isset($agentInfo['error'])){
            return ['message' => $agentInfo['message'],'status' => false];
        }
        return ['message' => $agentInfo,'status' => true];
    }
/*
 *
 *
客户列表
要求实现：
    1、邀请客户，派单客户统计。
    2、活动邀请，考察邀请，保护期客户统计
    3、可以对客户进行给定条件排序、过滤、筛选
    4、对客户列表进行昵称首字母排序
 *
 *客户列表
 * */
    public function postList($input){
        $agentId = intval($input['agent_id']);
        $orderBy = trim($input['order_by']);
        $type = trim($input['type']);
        $filter = trim($input['filter']);
        if(empty($agentId)){
            return ['message' => '请传递经纪人id','status' => false];
        }
        if(!empty($orderBy)&&!in_array($orderBy,['letter','intention','active','followed_time'])){
            return ['message' => '请传递正确的排序参数','status' => false];
        }
        if(!empty($filter)&&!in_array($filter,['all','ovo','inspected','signed_contract'])){
            return ['message' => '请传递正确的过滤参数','status' => false];
        }
        if(!empty($type)&&!in_array($type,['all','protected'])){
            return ['message' => '请传递正确的范围参数','status' => false];
        }
        $agentInfo=Agent::getCustomerList($agentId,$orderBy,$type,$filter);
        if(isset($agentInfo['error'])){
            return ['message' => $agentInfo['message'],'status' => false];
        }
        return ['message' => $agentInfo,'status' => true];
    }

    //需要门店考察提醒的客户
    public function postProtected($input){
        $agentId=intval($input['agent_id']);
        if(empty($agentId)){
            return ['message' => "请传递经纪人id",'status' => false];
        }
        $agentInfo=User::getProtected($agentId);
        if(isset($agentInfo['error'])){
            return ['message' => $agentInfo['message'],'status' => false];
        }
        return ['message' => $agentInfo,'status' => true];
    }


/*
 * 跟进情况-活动邀请
 * */
    public function postRecordsActivity($input){
        $agentId=intval($input['agent_id']);
        $customerId=intval($input['customer_id']);
        $brandId=intval($input['brand_id']);
        if(empty($agentId)){
            return ['message' => "请传递经纪人id",'status' => false];
        }
        if(empty($customerId)){
            return ['message' => "请输入客户id",'status' => false];
        }
        if(empty($brandId)){
            return ['message' => "请输入品牌id",'status' => false];
        }
        $agentInfo=Agent::getRecordsActivity($agentId,$customerId,$brandId);
        if(isset($agentInfo['error'])){
            return ['message' => $agentInfo['message'],'status' => false];
        }
        return ['message' => $agentInfo,'status' => true];
    }

    public function postRecordsAll($input)
    {
        $agentId = intval($input['agent_id']);
        $customerId = intval($input['customer_id']);
        $brandId = intval($input['brand_id']);
        if (empty($agentId)) {
            return ['message' => "请传递经纪人id",'status' => false];
        }
        if (empty($customerId)) {
            return ['message' => "请输入客户id",'status' => false];
        }
        if (empty($brandId)) {
            return ['message' => "请输入品牌id",'status' => false];
        }
        $agentInfo = Agent::getRecordsAll($agentId, $customerId, $brandId);
        if (isset($agentInfo['error'])) {
            return ['message' => $agentInfo['message'],'status' => false];
        }
        return ['message' => $agentInfo,'status' => true];
    }


   // 需要门店考察提醒的客户（自102版本起作废）
//    public function postProtected($input){
//        $agentId=intval($input['agent_id']);
//        if(empty($agentId)){
//            return ['message' => "请传递经纪人id",'status' => false];
//        }
//        $agentInfo=User::getProtected($agentId);
//        if(isset($agentInfo['error'])){
//            return ['message' => $agentInfo['message'],'status' => false];
//        }
//        return ['message' => $agentInfo,'status' => true];
//    }

    //派单客户概览
    public function postSendOverview($input){
        $agentId=intval($input['agent_id']);
        if(empty($agentId)){
            return ['message' => "请传递经纪人id",'status' => false];
        }
        $agentInfo=Agent::getCustomerStatistics($agentId);
        if(isset($agentInfo['error'])){
            return ['message' => $agentInfo['message'],'status' => false];
        }
        return ['message' => $agentInfo,'status' => true];
    }

        //邀请客户概览
    public function postInviteOverview($input){
        $agentId=intval($input['agent_id']);
        if(empty($agentId)){
            return ['message' => "请传递经纪人id",'status' => false];
        }
        $agentInfo=Agent::getInviteOverviewInfo($agentId);
        if(isset($agentInfo['error'])){
            return ['message' => $agentInfo['message'],'status' => false];
        }
        return ['message' => $agentInfo,'status' => true];
    }


    //客户管家
    public function postMaster($input){
        $agentId=intval($input['agent_id']);
        if(empty($agentId)){
            return ['message' => "请传递经纪人id",'status' => false];
        }
        $agentInfo=User::getRemindList($agentId);
        if(isset($agentInfo['error'])){
            return ['message' => $agentInfo['message'],'status' => false];
        }
        return ['message' => $agentInfo,'status' => true];
    }

    //需要活动提醒的客户
    public function postActivityRemind($input){
        $agentId=intval($input['agent_id']);
        if(empty($agentId)){
            return ['message' => "请传递经纪人id",'status' => false];
        }
        $agentInfo=Agent::getUserSainInfo($agentId);
        if(isset($agentInfo['error'])){
            return ['message' => $agentInfo['message'],'status' => false];
        }
        return ['message' => $agentInfo,'status' => true];
    }

    /**
     * 需要门店考察提醒的客户  -- 数据中心版
     * @User shiqy
     * @param $input
     * @return array
     */
    public function postInspectRemind($input) {
        $agentId=intval($input['agent_id']);
        if(empty($agentId)){
            return ['message' => "请传递经纪人id",'status' => false];
        }
        $agentInfo=User::getInspectRemind($agentId);
        if(isset($agentInfo['error'])){
            return ['message' => $agentInfo['message'],'status' => false];
        }
        return ['message' => $agentInfo,'status' => true];
    }

    //客户详情-活动邀请
    public function postActivityInvite($input){
        $agentId=intval($input['agent_id']);
        $customerId=intval($input['customer_id']);
        $type=intval($input['type']);
        if(empty($agentId)){
            return ['message' => "请传递经纪人id",'status' => false];
        }
        if(empty($customerId)){
            return ['message' => "请输入客户id",'status' => false];
        }
        if(!in_array($type,[-1,0,1,2])){
            return ['message' => "请输入正确的type类型",'status' => false];
        }
        $agentInfo=Agent::getActivityInviteInfo($agentId,$customerId,$type);
        if(isset($agentInfo['error'])){
            return ['message' => $agentInfo['message'],'status' => false];
        }
        return ['message' => $agentInfo,'status' => true];
    }

    /**
     * 客户详情 zhaoyf       --数据中心版
     *
     * @param $param
     * @return array|string
     * @internal param CustomerRequest $request
     * @internal param null $version
     * @internal param 经济人ID $agent_id
     * @internal param 投资人ID $customer_id return detail_data*
     */
    public function postDetailInfos($param)
    {
        $result = $param['request']->input();

        $results = AgentCustomer::with(['user' => function($query) {
            $query->select('uid', 'realname', 'nickname', 'username','non_reversible', 'avatar', 'gender',
                'last_login', 'diploma', 'profession', 'earning', 'invest_intention', 'investment_min',
                'investment_max', 'created_at as create_ats', 'updated_at as updated_ats', 'zone_id');
        }, 'user.zone', 'user.hasManyCategory.hasOneCategorys'])
            ->where('agent_id', $result['agent_id'])
            ->where('uid', $result['customer_id'])
            ->first();

        if (!$results) {
            return ['message' => '没有相关信息', 'status' => false];
        }

        //获取城市
        $zone      = new \App\Models\Zone\Entity();
        $zone_name = $zone->pidNames([$results['user']['zone']['id']]);

        //结果
        $user      = User::where('uid', $result['customer_id'])->first();

        //获取邀请人姓名（经纪人、投资人）
        $agent_name = Agent::where('non_reversible', $user->register_invite)->value('nickname');
        $user_name  = User::where('non_reversible',  $user->register_invite)->first();

        //当存在经纪人与经纪人分享自己的投资人的时候，获取到分享到对方经纪人的ID
        //如果不为空，就对当前的经纪人ID和投资人进行关系查询，返回两个结果：
        // 1、当当前分享过来的投资人在当前查看这个投资人的经纪人的通讯录里时，返回 1
        // 2、当不存在时，返回 0
//        if (!empty($result['customer_agent_id'] && is_numeric($result['customer_agent_id']))) {
            $is_relation = AgentCustomer::where('agent_id', $result['agent_id'])
                ->where('uid', $result['customer_id'])
                ->where('status', '<>', -1)
                ->where('level', '<>', -1)
                ->first();
//        }


        //星座
        $constellation  = getStarsignByMonth(substr($user->birth, 5, 2), substr($user->birth, 8, 2));
        //几零后
        $customer_time  = getTime($user->birth, 'birth_time');
        //哪里人
        $customer_zone  = abandonProvince($user->zone->name);
        if($customer_zone) {
            if ('区' == mb_substr($customer_zone, -1, 1)) {
                $customer_zone = mb_substr($customer_zone, 0, -1) . '人';
            } elseif ('地区' == mb_substr($customer_zone, -2, 2)) {
                $customer_zone = mb_substr($customer_zone, 0, -2) . '人';
            } else {
                $customer_zone = $customer_zone . '人';
            }
        } else {
            $customer_zone = '';
        }
        //投资意向
        $intention      = User::$IFIntention[$user->invest_intention];
        //投资额度
        $customer_money = abandonZero($user->investment_min) . '~' . abandonZero($user->investment_max);
        if ($user->investment_min > 0 && $user->investment_max > 0 ) {
            $customer_money = '投资额度：' . $customer_money;
        } elseif ($user->investment_min <= 0 && $user->investment_max > 0) {
            $customer_money = '投资额度：' . abandonZero($user->investment_max);
        } elseif ($user->investment_min > 0 && $user->investment_max <= 0) {
            $customer_money = abandonZero($user->investment_min);
        } else {
            $customer_money = '';
        }

        //判断当前用户是是投资人邀请的还是经纪人邀请的
        if (!empty($agent_name)) {
            $invite_name  = $agent_name;
            $invite_tags  = 1;   //经纪人邀请
        } elseif (!empty($user_name)) {
            $invite_name  = $user_name->realname ?: $user_name->nickname;
            $invite_tags  = 2;   //投资人邀请
        } else {
            $invite_name  = '';
            $invite_tags  = 0;
        }

        //获取用户感兴趣的行业分类
        foreach ($results['user']['hasManyCategory'] as $key => $vls) {
            $user_font_cate[$key] = $vls['hasOneCategorys']['name'];
        }

        //组合用户标签
        $tagss = [
            'customer_time'  => !empty($customer_time)  ?   $customer_time  : '',
            'constellation'  => !empty($constellation)  ?   $constellation  : '',
            'customer_zone'  => !empty($customer_zone)  ?   $customer_zone  : '',
            'intention'      => !empty($intention)      ?   $intention      : '',
            'customer_money' => !empty($customer_money) ?   $customer_money : ''
        ];
        $tagss['customer_cate'] = $user_font_cate ?  $user_font_cate : '';

        //组合数据
        $data = [
            'realname'             => $results['user']['realname'] ?  $results['user']['realname'] : $results['user']['nickname'],
            'nickname'             => $results['user']['nickname'] ?  $results['user']['nickname'] : $results['user']['realname'],
            'remark'               => $results['remark'] ? $results['remark'] : '',
            'relation_tel'         => $results->has_tel == 1 ?  ($results['user']['non_reversible'] ?  Agent::getRealPhone($results['user']['non_reversible'],'wjsq') : '') : $results['user']['username'],
            'non_reversible'       => $results['user']['non_reversible'] ? $results['user']['non_reversible'] : '',
            'avatar'               => $results['user']['avatar'] ? getImage($results['user']['avatar'], 'avatar', '') : getImage('', 'avatar', ''),
            'gender'               => $results['user']['gender'] == -1 ?  '未知' : ($results['user']['gender'] == 1 ?  '男' : '女'),
            'last_login'           => $results['user']['last_login'],
            'city'                 => $zone_name ? $zone_name : '',
            'diploma'              => $results['user']['diploma'] ?  $results['user']['diploma'] : '',
            'positions'            => $results['user']['profession'] ?  $results['user']['profession'] : '',
            'earning'              => $results['user']['earning'] ? $results['user']['earning'] : '',
            'interest_industries'  => $user_font_cate ?  $user_font_cate : '',
            'invest_intention'     => User::$IFIntention[$results['user']['invest_intention']] ?  User::$IFIntention[$results['user']['invest_intention']] : '',
            'invest_quota'         => abandonZero($results['user']['investment_min']) >= 100 ? abandonZero($results['user']['investment_min']) . '万元以上' : abandonZero($results['user']['investment_min']).' - '.abandonZero($results['user']['investment_max']).'万元',
            'relation'             => AgentCustomer::$relation[$results['source']] ?  AgentCustomer::$relation[$results['source']] : '',
            'created_at'           => $results['user']['create_ats'],
            'invite_agent'         => $invite_name,
            'invite_tags'          => $invite_tags,
            'has_tel'              => $results['has_tel'],
            'is_relation'          => $is_relation ?  1 : 0,
            'user_level_id'        => $results['level'],
            'tags'                 => $tagss
        ];

        return ['message' => $data, 'status' => true];
    }


    /**
     * 客户详情--跟进品牌页 zhaoyf     --数据中心版
     *
     * @param CustomerRequest $request
     * @param null $version
     * @return array
     * @internal param 经纪人ID $agent_id
     * @internal param 经济人ID $customer_id
     *
     */
    public function postDetailBrands($param)
    {
        $result = $param['request']->input();

        $customer_and_brand_result = AgentCustomer::instance()->DetailBrand($result);

        return ['message' => $customer_and_brand_result, 'status' => true];
    }


    /*
     *
     * 编辑客户跟单日志
     *
     * */
    public function postEditRemark($input){
        $id=intval($input['id']);
        $levelId=intval($input['level_id']);
        $remark=trim($input['remark']);
        if(empty($id)){
            return ['message' => "跟单日志id不能为空",'status' => false];
        }
        if(empty($levelId)){
            return ['message' => "客户等级不能为空",'status' => false];
        }
        $agentInfo=Agent::getEditRemark($id,$levelId,$remark);
        if(isset($agentInfo['error'])){
            return ['message' => $agentInfo['message'],'status' => false];
        }
        return ['message' => $agentInfo,'status' => true];
    }

    /*
     *
     * 删除客户跟单日志
     *
     * */
    public function postDeleteRemark($input){
        $id=intval($input['id']);
        if(empty($id)){
            return ['message' => "跟单日志id不能为空",'status' => false];
        }
        $agentInfo=Agent::getDeleteRemark($id);
        if(isset($agentInfo['error'])){
            return ['message' => $agentInfo['message'],'status' => false];
        }
        return ['message' => $agentInfo,'status' => true];
    }

    /**
     * 添加客户备注 zhaoyf
     *
     * @param $param
     * @internal param CustomerRequest|MessageRequest $request
     * @internal param null $version
     *
     * @return array|string
     */
    public function postAddRemark($param)
    {
        $result = $param['request']->input();

        //默认初始化调用时，返回数据
        if ($result['tags'] === 'default') {

            //获取数据信息
            $get_customer_info = AgentCustomer::with('hasManyAgentCustomerLogs.hasOneBrand')
                ->where('agent_id', $result['agent_id'])
                ->where('uid',      $result['customer_id'])
                ->first();

            //对查询结果进行处理
            if (!$get_customer_info) return ['message' => '没有查询到相关信息', 'status' => false];

            foreach ($get_customer_info['hasManyAgentCustomerLogs'] as $key => $results) {
                $show_result[] = [
                    'id'         => $results['hasOneBrand']['id'],
                    'brand_name' => $results['hasOneBrand']['name'],
                ];
            }

            //去除重复
            $show_result = Agent::instance()->_removeRepitition($show_result);

            //组织需要返回的数据
            $data = [
                'customer_level'      => AgentCustomer::$customerLevel[$get_customer_info['level']],
                'customer_level_id'   => $get_customer_info['level'],
                'customer_level_list' => AgentCustomer::$customerLevel,
                'brand_list'          => $show_result,
            ];

            return ['message' => $data, 'status' => true];

        //提交添加备注信息时，进行添加保存数据
        } elseif ($result['tags'] === 'submits') {

           /* //组织数据
            $data = [
                'agent_id' => $result['agent_id'],
                'uid'      => $result['customer_id'],
                'level'    => $result['level_id'],
                'remark'   => $result['remark'],
                'brand_id' => $result['id'],
                'status'   => 0,
                'created_at' => time(),
            ];*/

           //更新经纪人客户表的级别等级
            AgentCustomer::where('agent_id', $result['agent_id'])->where('uid', $result['customer_id'])->update(['level' => $result['level_id']]);

            //获取经纪人信息为了日记表的添加
            $get_customer_info = AgentCustomer::where('agent_id', $result['agent_id'])->where('uid', $result['customer_id'])->first();

            //添加数据到日记表里
            $add_log_result = _v020800::instance()->addAgentCustomerLog($get_customer_info, $result['id'], 12, $result['remark'], 'other');

            if ($add_log_result['status']) {
                return ['message' => '添加备注成功', 'status' => true];
            } else {
                return ['message' => $add_log_result['message'], 'status' => $add_log_result['status']];
            }
        }  else {
            return ['message' => '缺少有效标记', 'status' => false];
        }
    }

}