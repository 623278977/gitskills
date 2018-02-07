<?php namespace App\Services\Version\User;

use App\Http\Controllers\Api\CommonController;
use App\Models\Activity\Sign as ActivitySign;
use App\Models\Agent\AgentBrand;
use App\Models\Agent\AgentCurrencyLog;
use App\Models\Agent\AgentCustomer;
use App\Models\Agent\AgentCustomerLog;
use App\Models\Agent\Invitation;
use App\Models\Agent\Score\AgentScoreLog;
use App\Models\Categorys;
use App\Models\Contract\Contract;
use App\Models\Fund;
use App\Models\User\Entity as User;
use App\Models\Agent\Agent;
use App\Models\User\Industry;
use App\Models\Industry as Industrs;
use App\Models\User\Entity;
use App\Models\CacheTool;
use App\Models\ScoreLog;
use App\Models\Brand\Entity as Brand;
use App\Models\Brand\Entity\V020800 as BrandV020800;
use App\Models\User\UserFondCate;
use App\Models\Zone\Entity as Zone;
use App\Models\AgentScore;
use DB;
use App\Models\Orders\Entity as Orders;
use App\Models\User\Free;
use App\Models\SendInvestor\V020800 as SendInvestor;
use App\Models\Agent\Entity\_v010200 as Agentv010200;


class _v020802 extends _v020800
{
    /**
     * 等待经纪人接单   --数据中心版
     *
     * @param $param
     * @return array|string
     * @author tangjb
     */
    public function postWaitAccept($param)
    {
        //判断是否在保护期内
//        $exist = AgentCustomer::with('belongsToAgent')->where('source', 1)->where('uid', $param['uid'])
//            ->where('protect_time', '>=', time())->first();

        $user = User::where('uid', $param['uid'])->first();
        $agent = Agent::where('non_reversible', $user->register_invite)->first();
        if($agent){
            $exist = AgentBrand::where('agent_id', $agent->id)->where('brand_id', $param['brand_id'])
                ->where('status', 4)->first();
        }

        $in_protect =  0;
        // 查看是否在保护
        if (isset($exist) && is_object($exist)) {
            $agentCustomer = AgentCustomer::where(
                [
                    'agent_id'=>$agent->id,
                    'uid'=>$param['uid'],
                ]
            )->first();

            if(!$agentCustomer){
                $agentCustomer = AgentCustomer::create([
                    'agent_id' => $agent->id,
                    'uid' => $param['uid'],
                    'source' => 1,
                    'brand_id' => 0,
                    'has_tel' => 1,
                ]);
            }

            $in_protect = 1;
            //新增一条对接记录
            $log = AgentCustomerLog::where([
                'agent_customer_id' => $agentCustomer->id,
                'action' => 1,
                'post_id' => 0,
                'brand_id' => $param['brand_id'],
                'agent_id' => $agentCustomer->agent_id,
                'uid' => $param['uid'],
            ])->first();

            //没有就创建
            if (!$log) {
                $log = AgentCustomerLog::create([
                    'agent_customer_id' => $agentCustomer->id,
                    'action' => 1,
                    'post_id' => 0,
                    'brand_id' => $param['brand_id'],
                    'agent_id' => $agentCustomer->agent_id,
                    'uid' => $param['uid'],
                    'created_at' => time()
                ])->first();
            }

            !$agent->is_public_realname ? $nickname = $agent->nickname : $nickname = $agent->realname;

            return ['message' => ['in_protect' => $in_protect,
                'agent_name' => $nickname,
                'agent_id' => $agent->id], 'status' => true];
        }

        //查看是否已经形成派单关系
        $is_accept = AgentCustomerLog::with('agent')
            ->whereHas('agent_customer', function ($query) {
                $query->where('status', '>', -1);
            })
            ->where('action', 0)
            ->where('uid', $param['uid'])
            ->where('brand_id', $param['brand_id'])
            ->orderBy('id', 'desc')
            ->first();


        if ($is_accept) {
            $in_protect = 1;
            return ['message' => ['in_protect' => $in_protect,
                'agent_name' => $is_accept->agent->nickname,
                'agent_id' => $is_accept->agent->id], 'status' => true];
        }

        //获取所有经纪人
        $agentlists = Agent::with('hasOneZone')
            ->whereIn('id', function ($query) use ($param) {
                $query->from('agent_brand')->where('status', 4)
                    ->where('brand_id', $param['brand_id'])->lists('agent_id');
            })
            ->select('id', 'avatar', 'zone_id')->get();

        //如果为空，那么就派给自己的经纪人
        if(!count($agentlists)){
            $agentlists = Agent::with('hasOneZone')
                ->where('account_type', 3)
                ->select('id', 'avatar', 'zone_id')->get();
        }


//        $user = User::where('uid', $param['uid'])->first();

        foreach ($agentlists as $k => $v) {
            $sort = 0;
            //就近原则
            if ($v->zone_id == $user->zone_id) {
                $sort += 100;
            }
            //闲忙原则
            $counts = AgentCustomer::where('agent_id', $v->id)->where('uid', $param['uid'])
                ->where('created_at', '>', strtotime(date('Y-m-d')))->count();
            $sort += -$counts;
            //成交原则 成单率
            $success = Contract::where('status', 1)->where('brand_id', $param['brand_id'])->where('agent_id', $v->id)->count();
            $all = Contract::where('brand_id', $param['brand_id'])->where('agent_id', $v->id)->count();
            $rate = $all == 0 ? 0 : $success / $all;
            $sort += $rate;
            $v->sort = $sort;
            $v->avatar = getImage($v->avatar, 'avatar', '');
        }

        $count = count($agentlists);
        $agents = $agentlists->take(10);

        $agents = $agents->sortByDesc('sort');
        $brand = Brand::where('id', $param['brand_id'])->select('name', 'logo')->first();
        $title = $brand->name;
        $logo = getImage($brand->logo, 'activity', '');

        //判断是否已经派过单
        $user_accept = SendInvestor::where('brand_id',$param['brand_id'])
            ->where('uid',$param['uid'])
            ->where('status','0')
            ->value('id');

        //已经派过单的不再派单
        if ($user_accept){
            $data = compact('title', 'logo','user_accept', 'in_protect', 'is_accept_order');
            return ['message' => $data, 'status' => true];
        }


        $send_investor_id = BrandV020800::instances()->setSendQueue($param['brand_id'], $param['uid']);

        $data = compact('agents', 'count', 'title', 'logo', 'send_investor_id', 'in_protect', 'is_accept_order');

        return ['message' => $data, 'status' => true];
    }





    /**
     * 被邀请人填写邀请码
     *
     * @param $param
     * @return array
     * @author tangjb
     */
    public function postWritecode($param)
    {
        if (empty($param['_uid'])) {
            return ['message' => 'uid必填', 'status' => false];
        }

        if (!($user = User::where('uid', $param['_uid'])->first())) {
            return ['message' => '非法的uid', 'status' => false];
        }

        $request = $param['request'];
        $invite_code = trim($request->input('invite_code', ''));
        //md5加盐后的邀请号码
        $inviter_tel = encryptTel($invite_code);

        if (empty($invite_code)) {
            return ['message' => '邀请码不能为空', 'status' => false];
        }

        if (!preg_match("/(^1[34578]\d{9}$)|(^(\d){6}$)|(^(\d){8}$)|(^(\d){10}$)/", $invite_code)) {
            return ['message' => '邀请码为不合法的手机号', 'status' => false];
        }

        if ($inviter_tel == $user->non_reversible) {
            return ['message' => '邀请码不能为自己手机号', 'status' => false];
        }


        $invite_user = User::where('non_reversible', $inviter_tel)->orWhere('my_invite', $invite_code)->first();

        //双轨制
        $agent = Agent::where(function ($query) use($invite_code,$inviter_tel){
            return $query->where('non_reversible',$inviter_tel)->orWhere('my_invite',$invite_code);
        })->where('status', 1)->first();
        if (!$invite_user && !$agent) {
            return ['message' => '邀请码有误，请核对', 'status' => false];
        }

        if(isset($agent->username) && $agent->non_reversible == $user->non_reversible){
            return ['message' => '自己不能是自己的邀请经纪人', 'status' => false];
        }


        if(isset($invite_user->my_invite) && $invite_user->non_reversible == $user->non_reversible){
            return ['message' => '自己不能是自己的邀请人', 'status' => false];
        }


        if ($user->register_invite) {
            return ['message' => '已经输入过邀请码', 'status' => false];
        }



        //那就强制把邀请码改为手机号，减轻双轨制的复杂度
        if($agent){
            $inviter_tel = $agent->non_reversible;
        }




        //写入数据
        $update = [
            'register_invite' => $inviter_tel
        ];

        $res = User::where('uid', $param['_uid'])->update($update);


        if ($agent) {
            $agentCustomer = AgentCustomer::where('agent_id', $agent->id)->where('uid', $param['_uid'])->first();

            if(!$agentCustomer){
                $agentCustomer = AgentCustomer::create([
                    'agent_id' => $agent->id,
                    'uid' => $param['_uid'],
                    'protect_time' => time() + 30 * 24 * 3600,
                    'source' => 1,
                    'brand_id' => 0,
                    'has_tel' => 1,
                ]);
            }


            AgentCustomerLog::create(
                [
                    'agent_customer_id' => $agentCustomer->id,
                    'action' => 2,
                    'post_id' => 0,
                    'brand_id' => 0,
                    'agent_id' => $agent->id,
                    'uid' => $param['_uid'],
                    'created_at' => time(),
                ]
            );


            $log = AgentCustomerLog::create(
                [
                    'agent_customer_id' => $agentCustomer->id,
                    'action' => 14,
                    'post_id' => 0,
                    'brand_id' => 0,
                    'agent_id' => $agent->id,
                    'uid' => $param['_uid'],
                    'created_at' => time(),
                ]
            );


            //给经纪人发
            $res = send_transmission(json_encode(['type' => 'bind', 'style' => 'json', 'value' => ['username' => $agent->username, 'id' => $user->uid, 'realname' => $user->realname, 'nickname' => $user->nickname]]), $agent, null, 1);
            //给C端用户发
            $res = send_transmission(json_encode(['type' => 'bind', 'style' => 'json', 'value' => ['agent_id' => $agent->id]]), $user);


            //代理品牌数≥ 1  推荐成功投资客≥ 1 给他的直接上级奖励80元
            $agent = Agent::with('pAgent')->where('id', $agent->id)->first();

            //给积分
            Agentv010200::add($agent->id, AgentScoreLog::$TYPES_SCORE[12], 12, '邀请投资人', $log->id, 1);


            return ['message' => "已成功匹配经纪人{$agent->realname}", 'status' => true];
        }


        $name = $invite_user->realname ?: ($invite_user->nickname ?: '');
        //邀请人获得100积分
        if ($invitor = User::where('non_reversible', $inviter_tel)->first()) {
            //系统消息
            createMessage(
                $invitor->uid,
                $title = '获得了100积分',
                $content = "恭喜你,($name) 填写了你的邀请码并注册了账号,100积分已到账户中,打开app查看我的积分",
                $ext = '',
                $end = '<p>如有疑问，请致电服务热线<span>400-011-0061</span></p>',
                $type = 1,
                $delay = 300
            );

//            $content_sms = trans('sms.invite_score',['name'=>$name]);

            //给邀请人赠送积分
            ScoreLog::add($invitor->uid, 100, 'invite_register', '邀请用户注册');

            //短信
            @SendTemplateSMS('invite_score', $invitor->non_reversible, 'invite', ['name' => $name], $invitor->nation_code);
        }


        if ($res !== false) {
            return ['message' => "已成功匹配{$name}", 'status' => true];
        }

        return ['message' => '操作失败', 'status' => false];
    }





}