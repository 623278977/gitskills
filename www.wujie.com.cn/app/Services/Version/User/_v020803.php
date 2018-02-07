<?php
/**
 * Created by PhpStorm.
 * Title：
 * User: yaokai
 * Date: 2017/11/9 0009
 * Time: 10:12
 */
namespace App\Services\Version\User;

use App\Models\Agent\AgentBrand;
use App\Models\Agent\AgentCustomer;
use App\Models\Agent\AgentCustomerLog;
use App\Models\User\Entity as User;
use App\Models\Brand\Entity as Brand;
use App\Models\Agent\Agent;
use App\Models\Brand\Entity\V020800 as BrandV020800;
use DB;
use App\Models\SendInvestor\V020800 as SendInvestor;


class _v020803 extends _v020802
{
    /**
     * 等待经纪人接单
     * @User yaokai
     * @param $param
     * @return array
     */
    public function postWaitAccept($param)
    {
        //投资人信息
        $user = User::where('uid', $param['uid'])->first();
        //找出邀请经纪人信息
        $agent = Agent::where('non_reversible', $user->register_invite)->first();
        //品牌相关信息
        $brand = Brand::where('id', $param['brand_id'])->select('name', 'logo')->first();
        $title = $brand->name;
        //如果有经纪人，找出是否代理该品牌
        if ($agent) {
            $exist = AgentBrand::where('agent_id', $agent->id)
                ->where('brand_id', $param['brand_id'])->where('status', 4)->first();
        }

        $in_protect = 0;
        // 查看是否在保护
        if (isset($exist) && is_object($exist)) {
            $agentCustomer = AgentCustomer::where(
                [
                    'agent_id' => $agent->id,
                    'uid' => $param['uid'],
                ]
            )->first();

            if (!$agentCustomer) {
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

            return ['message' => ['in_protect' => $in_protect,'title' => $title,
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
            return ['message' => ['in_protect' => $in_protect,'title' => $title,
                'agent_name' => $is_accept->agent->nickname,
                'agent_id' => $is_accept->agent->id], 'status' => true];
        }



        $logo = getImage($brand->logo, 'activity', '');


        //判断是否已经派过单
        $user_accept = SendInvestor::where('brand_id',$param['brand_id'])
            ->where('uid',$param['uid'])
            ->where('status', '0')
            ->value('id');

        //已经派过单的不再派单
        if ($user_accept){
            $data = compact('title', 'logo','user_accept', 'in_protect', 'is_accept_order');
            return ['message' => $data, 'status' => true];
        }


        $send_investor_id = BrandV020800::instances()->setSendQueue($param['brand_id'], $param['uid']);

        $data = compact('title', 'logo','send_investor_id', 'in_protect', 'is_accept_order');

        return ['message' => $data, 'status' => true];
    }


}