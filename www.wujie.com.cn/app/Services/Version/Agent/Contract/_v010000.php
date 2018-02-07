<?php
/**
 * Created by PhpStorm.
 * User: yaokai
 * Date: 2017/8/30 0031
 * Time: 11:09
 */

namespace App\Services\Version\Agent\Contract;

use App\Models\Agent\AgentCustomer;
use App\Models\Agent\AgentCustomerLog;
use App\Models\Agent\BrandContract;
use App\Models\Agent\Invitation;
use App\Models\Agent\Score\AgentScoreLog;
use App\Models\Brand\BrandContractCost;
use App\Models\Contract\BrandContractFee;
use App\Services\Version\VersionSelect;
use App\Models\Contract\Contract;
use DB, Input;
use App\Models\Agent\Entity\_v010200 as Agentv010200;

class _v010000 extends VersionSelect
{
    /**
     * 发送合同
     * @User yaokai
     * @agent_id 经纪人id
     * @brand_contract_id 合同模板id
     * @uid 经纪人客户id
     * return 合同id
     */
    public function postSend($input = [])
    {
        $agent_id = $input['agent_id'];
        $uid = $input['uid'];
        $brand_contract_id = $input['brand_contract_id'];
        //跟据品牌合同模板id找相关数据
        $brand_contract = BrandContract::where('id', $brand_contract_id)->first();
        if (!$brand_contract) {
            return ['message' => '模板不存在！', 'status' => false];
        }

        //找出经纪人客户id即agent_customer
        $agent_custoemr_id = AgentCustomer::where('agent_id', $agent_id)
            ->where('uid', $uid)
            ->value('id');
        if (!$agent_custoemr_id) {
            return ['message' => '经纪人客户不存在！', 'status' => false];
        }

        //查看是否已有存在合同
        $created_at = Contract::where('brand_contract_id', $brand_contract_id)
            ->where('agent_id', $agent_id)
            ->where('uid', $uid)
            ->where('status','>=','0')
            ->value('created_at');

        //找出考察邀请信息
//        $invitation = Invitation::getBrandInviId($agent_id,$brand_contract->brand_id,$uid);

//        $invitation_id = $invitation?:'0';
        $invitation_id = 0;

        //定义闭包返回相关合同信息
        $contract = null;

        if ($created_at) {
            //如果存在合同，则判断合同是否过期
            $is_timeout = Contract::IsTimeout(strtotime($created_at));

            //如果返回正数则订单在有效期内
            if ($is_timeout > 0) {
                return ['message' => '合同已存在！', 'status' => false];
            } else {
                DB::transaction(function () use ($brand_contract_id, $agent_id, $uid, $brand_contract, $agent_custoemr_id,$invitation_id, &$contract) {
                    //否则合同过期重新创建合同
                   $contract = Contract::create([
                        'brand_contract_id' => $brand_contract_id,//合同模板id
                        'agent_id' => $agent_id,//经纪人ID
                        'uid' => $uid,//用户id
                        'name' => $brand_contract->name,//合同名称
                        'contract_no' => '',//合同号未处理
                        'status' => '0',//状态待签订
                        'brand_id' => $brand_contract->brand_id,//品牌id
                        'amount' => $brand_contract->amount,//合同金额
                        'pre_pay' => $brand_contract->pre_pay,//首付金额
                        'invitation_id' => $invitation_id,//邀请函id
                        'address' => $brand_contract->address,//合同地址
                    ]);
                    AgentCustomerLog::create([
                        'agent_customer_id' => $agent_custoemr_id,//经纪人客户id
                        'action' => '9',//发送合同
                        'post_id' => $contract->id,//合同id
                        'brand_id' => $brand_contract->brand_id,//品牌id
                        'agent_id' => $agent_id,//经纪人ID
                        'created_at' => time(),//创建时间
                        'uid' => $uid,//用户id
                    ]);
                });
            }
        } else {
            DB::transaction(function () use ($brand_contract_id, $agent_id, $uid, $brand_contract, $agent_custoemr_id,$invitation_id, &$contract) {
                //如果不存在合同直接创建合同
                $contract = Contract::create([
                    'brand_contract_id' => $brand_contract_id,//合同模板id
                    'agent_id' => $agent_id,//经纪人ID
                    'uid' => $uid,//用户id
                    'name' => $brand_contract->name,//合同名称
                    'contract_no' => '',//合同号未处理
                    'status' => '0',//状态待签订
                    'brand_id' => $brand_contract->brand_id,//品牌id
                    'amount' => $brand_contract->amount,//合同金额
                    'pre_pay' => $brand_contract->pre_pay,//首付金额
                    'invitation_id' => $invitation_id,//邀请函id
                    'address' => $brand_contract->address,//合同地址
                ]);
                $contract_no = Contract::getInstance()->produceNo($contract->id);
                $contract->contract_no = $contract_no;
                $contract->save();
                AgentCustomerLog::create([
                    'agent_customer_id' => $agent_custoemr_id,//经纪人客户id
                    'action' => '9',//发送合同
                    'post_id' => $contract->id,//合同id
                    'brand_id' => $brand_contract->brand_id,//品牌id
                    'agent_id' => $agent_id,//经纪人ID
                    'created_at' => time(),//创建时间
                    'uid' => $uid,//用户id
                ]);


                $costs = BrandContractCost::where('brand_contract_id', $brand_contract_id)
                    ->where('is_delete', 0)->get();
                $fees = [];
                foreach($costs as $k=>$v){
                    $fees[$k]['contract_id'] = $contract->id;
                    $fees[$k]['brand_contract_cost_id'] = $v->id;
                    $fees[$k]['cost_type'] = $v->cost_type;
                    $fees[$k]['cost_limit'] = $v->cost_limit;
                    $fees[$k]['is_commission'] = $v->is_commission;
                    $fees[$k]['created_at'] = time();
                    $fees[$k]['updated_at'] = time();
                }

                BrandContractFee::insert($fees);
            });
        }
        if ($contract) {
            $data = $contract->id;//合同id
            Agentv010200::add($agent_id, AgentScoreLog::$TYPES_SCORE[6], 6, '发放加盟合同', $data);
            return ['message' => $data, 'status' => true];
        } else {
            return ['message' => '发送失败，请重试！', 'status' => false];
        }

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
        $data = Contract::ContractDetail('', '', '','',$contract_id,true);

        return ['message' => $data, 'status' => true];
    }


}