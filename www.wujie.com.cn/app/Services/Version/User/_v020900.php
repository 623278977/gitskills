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
use App\Models\Contract\Contract;
use App\Models\RedPacket\RedPacketPerson;
use App\Models\RedPacket\RedPacket;
use App\Models\User\Entity as User;
use App\Models\Brand\Entity as Brand;
use App\Models\Agent\Agent;
use App\Models\Brand\Entity\V020800 as BrandV020800;
use DB;
use App\Models\SendInvestor\V020800 as SendInvestor;


class _v020900 extends _v020803
{
    /**
     * 我的基金列表
     *
     * @param $param
     * @return array
     * @author tangjb
     */
    public function postPackagelist($param)
    {
        $validator = \Validator::make($param, [
            'uid' => 'required|exists:user,uid',
            'contact_id' => 'sometimes|exists:contract,id',
        ], [], [
            'uid' => '用户id',
            'contact_id' => '合同id',
        ]);

        if ($validator->fails()) {
            $warnings = $validator->messages()->all();
            return ['message' => $warnings[0], 'status' => false];
        }

        $page = array_get($param, 'page', 1);
        $pageSize = array_get($param, 'pageSize', 15);
        $status = array_get($param, 'status', 0);

        //把到期的红包的状态改成-1
        RedPacketPerson::where('expire_at', '<', time())->where('expire_at', '<>', -1)->where('status', 0)->update(['status' => -1]);

        //在有合同id的时候需要按照本品牌来排序
        if (!empty($param['contact_id'])) {
            $contract = Contract::where('id', $param['contact_id'])->first();

            $ids = RedPacket::where(function ($query) {
                $query->where('post_id', 0)->where('type', 1);
            })->orWhere(function ($query) use ($contract) {
                $query->where('post_id', $contract->brand_id)->where('type', 2);
            })->orWhere('type', 3)
                ->lists('id')->toArray();

            $lists = RedPacketPerson::with('red_packet')->where('status', 0)
                ->where('receiver_id', $param['uid'])->where(function($query){
                    $query->where('expire_at', '>', time())->orWhere('expire_at', -1);
                })
                ->whereIn('red_packet_id', $ids)->orderBy('type', 'desc')//专属品牌红包放前面，全场放后面
                ->forPage($page, $pageSize)->get();

        } else {
            $lists = RedPacketPerson::with('red_packet')->where('status', $status)
                ->where('receiver_id', $param['uid'])->forPage($page, $pageSize)->get();
        }



        $data = [];
        foreach ($lists as $k => $v) {
            $data[$k]['id'] = $v->id;
            //类型
            $data[$k]['type'] = $v->red_packet->type;
            //数额
            $data[$k]['num'] = abandonZero($v->red_packet->amount);

            if (in_array($v->red_packet->type, [1, 2])) {
                //开始时间
                $data[$k]['begin_time'] = date('Y/m/d', $v->created_at->timestamp);

                //结束时间或过期时间
                $data[$k]['expire_at'] = date('Y/m/d', $v->expire_at);
            }


            //已使用
            if ($status == 1) {
                $data[$k]['expire_at'] = date('Y/m/d', $v->used_at);
            }


            if (2 == $v->red_packet->type) {
                //品牌标题
                $data[$k]['brand_name'] = $v->red_packet->brand->name;
                $data[$k]['brand_id'] = $v->red_packet->brand->id;
            }

            if(!empty($data[$k]['expire_at']) && $data[$k]['expire_at']=='1970/01/01'){
                $data[$k]['expire_at'] = '不限期限';
            }

            if(!empty($data[$k]['begin_time']) && $data[$k]['begin_time']=='1970/01/01'){
                $data[$k]['begin_time'] = '不限期限';
            }

        }


        return ['message' => $data, 'status' => true];
    }


    /**
     * 兑换红包
     *
     * @param $param
     * @return array
     * @author tangjb
     */
    public function postSwapPackage($param)
    {
        $validator = \Validator::make($param, [
            'uid' => 'required|exists:user,uid',
            'code' => 'required',
        ], [], [
            'uid' => '用户id',
            'code' => '兑换码'
        ]);

        if ($validator->fails()) {
            $warnings = $validator->messages()->all();
            return ['message' => $warnings[0], 'status' => false];
        }


        //查询该兑换码对应的红包
        $packet = RedPacket::where('redeem_code', $param['code'])
            ->where(function($query){
                $query->where('total', '>', 0)->orWhere('total', -1);
            })
            ->where(function ($query) {
                $query->where('end_distribute_at', '>', time())->orWhere('end_distribute_at', -1);
            })
            ->first();


        if (!$packet) {
            return ['message' => '你的兑换码输入有误或已失效', 'status' => false];
        }

        if($packet->gives>=$packet->total && $packet->total!=-1){
            return ['message' => '该红包已被领完', 'status' => false];
        }


        if ($packet->start_distribute_at>time() && $packet->start_distribute_at!=-1) {
            return ['message' => '该兑换码，还没到领取的时间', 'status' => false];
        }

        //查询是否已经领取过
        $redPacketPerson = RedPacketPerson::where('receiver_id', $param['uid'])->where('red_packet_id', $packet->id)->first();

        if ($redPacketPerson) {
            return ['message' => '你已领取过该红包', 'status' => false];
        }

        $packet->gives =  $packet->gives+1;
        $packet->save();


        if ($packet->expire_type == 1) {
            $expire_at = time() + $packet->expire_at;
        } else {
            $expire_at = $packet->expire_at;
        }

        if ($packet->expire_at == -1) {
            $expire_at = -1;
        }

        RedPacketPerson::create(
            [
                'receiver_id' => $param['uid'],
                'red_packet_id' => $packet->id,
                'expire_at' => $expire_at,
                'amount' => $packet->amount,
                'type' => $packet->type,
            ]
        );


        return ['message' => '领取成功', 'status' => true];
    }



}