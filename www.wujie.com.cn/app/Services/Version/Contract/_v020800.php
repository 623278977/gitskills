<?php
/**
 * Created by PhpStorm.
 * User: yaokai
 * Date: 2017/8/30 0031
 * Time: 11:09
 */

namespace App\Services\Version\Contract;

use App\Models\Agent\AgentCustomer;
use App\Models\Agent\AgentCustomerLog;
use App\Models\Agent\BrandContract;
use App\Services\Version\VersionSelect;
use App\Models\Contract\Contract;
use DB, Input;


class _v020800 extends VersionSelect
{
    /**
     * 投资人电子合同概览
     * @User yaokai
     */
    public function postContract($input = [])
    {
        //相关合同统计
        $data = Contract::ContractCount($input['uid'],'wjsq');

        return ['message' => $data, 'status' => true];
    }

    /**
     * 经纪人电子合同详情
     * @User yaokai
     */
    public function postContractDetail($input = [])
    {
        $status = $input['status'];
        $uid = $input['uid'];

        //相关合同统计
        $data = Contract::ContractDetail('', $status,$uid,'','',true);

        return ['message' => $data, 'status' => true];
    }


    /**
     * 根据合同id获取合同信息
     * @User yaokai
     * @param array $input
     * @return array
     */
    public function postDetail($input = [])
    {
        $contract_id = $input['contract_id'];
        //相关合同信息
        $data = Contract::ContractDetail('', '', '','',$contract_id);

        return ['message' => $data, 'status' => true];
    }


    /**
     * 拒绝合同
     * @User yaokai
     */
    public function postDeny($input = [])
    {
        $uid = $input['uid'];
        $remark = $input['remark'];
        $contract_id = $input['contract_id'];
        //发生时间
        $time = time();
        //跟据品牌合同id找相关数据
        $contract = Contract::where('id', $contract_id)
            ->where('uid',$uid)
            ->where('status','0')
            ->first();
        if (!$contract) {
            return ['message' => '合同不存在！', 'status' => false];
        }
        //找出经纪人客户id写入日志
        $agent_custoemr_id = AgentCustomer::where('agent_id',$contract->agent_id)
            ->where('uid',$uid)
            ->where('status','0')
            ->value('id');
        //写入
        DB::transaction(function () use ($uid, $remark, $contract_id,$time,$contract,$agent_custoemr_id) {
            //修改合同状态
            $u_contract = Contract::where('id', $contract_id)->update([
                'status' => '-1',//改为拒绝状态
                'remark' => $remark,//备注
                'confirm_time' => $time,//确认时间
            ]);
            //写入客户跟进日志
            AgentCustomerLog::create([
                'agent_customer_id' => $agent_custoemr_id,//经纪人客户id
                'action' => '10',//拒绝合同
                'post_id' => $contract_id,//合同id
                'remark' => $remark,//拒绝原因
                'brand_id' => $contract->brand_id,//品牌id
                'agent_id' => $contract->agent_id,//经纪人ID
                'created_at' => $time,//创建时间
                'uid' => $uid,//用户id
            ]);
        });
        return ['message' => '合同已拒绝，期待下次合作！', 'status' => true];
    }


}