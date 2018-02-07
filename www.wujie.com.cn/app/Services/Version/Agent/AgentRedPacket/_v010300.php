<?php
namespace App\Services\Version\Agent\AgentRedPacket;


use App\Models\Agent\Agent;
use App\Models\Agent\AgentAdd;
use App\Models\Agent\AgentGiveGetRedPacket;
use App\Models\Agent\RedPacketAgent;
use App\Models\RedPacket\RedPacket;
use App\Services\Version\VersionSelect;

class _v010300 extends VersionSelect
{
    /**
    *   作者：shiqy
    *   创作时间：2018/1/19 0019 上午 10:36
    *   功能描述：新年活动详情页
    */
    public function postActiveDetail($input){
        $data = [];
        //获取抽奖记录
        $logList = RedPacketAgent::with('agent')->whereIn('status',[0,1])->whereIn('type' , [5,7])->skip(0)->take(10)->get()->toArray();
        $data['draw_log'] = [];
        foreach ($logList as $one){
            $arr = [];
            $arr['name'] = empty(trim($one['agent']['realname'])) ? trim($one['agent']['nickname']) : encryptName($one['agent']['realname']) ;
            $arr['type'] = trim($one['type']);
            if($one['type'] == 5){
                $arr['cont'] = trim($one['remark']);
            }
            else{
                $arr['cont'] = trim(floatval($one['amount']));
            }
            $data['draw_log'][] = $arr;
        }

        //获取抽奖次数
        $agentAddInfo = AgentAdd::where('agent_id',$input['agent_id'])->first();
        $count = 1;
        if(!empty($agentAddInfo)){
            $count = trim($agentAddInfo['draw_num']);
        }
        $data['draw_num'] = $count;
        //获取该经纪人抽取的福卡信息
        $data['good_card_list'] = RedPacketAgent::getFuCardList($input['agent_id']);
        return ['message'=>$data , 'status'=>true];
    }

    //抽取新年红包接口
    public function postNewYearRedpacket($input){
        $agentId = intval($input['agent_id']);
        $agentAddInfo = AgentAdd::where('agent_id' , $agentId)->first();
        if(is_object($agentAddInfo) && $agentAddInfo['draw_num'] < 1){
            return ['message'=>'您可抽奖次数为0' , 'status'=>false];
        }
        //获取所有可抽取红包
        $redPacketList = RedPacket::showWhere()->where('red_source',2)->whereIn('type',[5,7])->get()->toArray();
        //制造红包池数据
        $redPacketPool = [];
        foreach ($redPacketList as $oneRedPacket){
            $arr = [];
            $arr['itemId'] = trim($oneRedPacket['id']);
            $arr['prob'] = trim($oneRedPacket['gain_probability']);
            $arr['type'] = trim($oneRedPacket['type']);
            $arr['minMount'] = trim($oneRedPacket['amount']);
            $arr['maxMount'] = trim($oneRedPacket['max_amount']);
            $arr['remark'] = trim($oneRedPacket['remark']);
            $arr['expire_at'] = trim($oneRedPacket['expire_at']);
            $redPacketPool[] = $arr;
        }
        if(empty($redPacketPool)){
            return ['message'=>'没有红包可供抽取' , 'status'=>false];
        }
        //随机抽取一个红包
        $selectedRedPacket = selectByProbability($redPacketPool);
        if(empty($selectedRedPacket)){
            return ['message'=>'随机抽取错误！' , 'status'=>false];
        }
        //封装数据
        $data = [];
        if($selectedRedPacket['type'] == 5){
            $data['type'] = '1';
            $data['cont'] = trim($selectedRedPacket['remark']);
        }
        else{
            $data['type'] = '2';
            $data['cont'] = trim(twoNumRand($selectedRedPacket['minMount'] , $selectedRedPacket['maxMount'] ));
        }
//        事务型 ： 1、在领取表中写上一条记录，2修改agent_add中的此人的可抽取次数
        try{
            \DB::beginTransaction();
            $agentRedLog = [];
            $agentRedLog['agent_id'] = $agentId;
            $agentRedLog['red_packet_id'] = $selectedRedPacket['itemId'];
            $agentRedLog['expire_at'] = $selectedRedPacket['expire_at'];
            $agentRedLog['type'] = $selectedRedPacket['type'];
            $agentRedLog['source'] = 2;
            if($selectedRedPacket['type'] == 5){
                $agentRedLog['remark'] = $data['cont'];
            }
            else{
                $agentRedLog['amount'] = $data['cont'];
                Agent::where('id',$agentId)->increment('currency' , $data['cont']);
            }
            RedPacketAgent::create($agentRedLog);
            RedPacket::where('id',$selectedRedPacket['itemId'])->increment('gives');
            AgentAdd::where('agent_id' , $agentId)->decrement('draw_num');
            $data['count'] = $agentAddInfo['draw_num'] - 1;
            \DB::commit();
        }catch (\Exception $e){
            \DB::rollBack();
            \Log::info($e->getMessage());
            return ['message'=> "数据存储失败！" , 'status'=>false];
        }
        //返回
        return ['message'=> $data , 'status'=>true];
    }

    //福卡获得或赠送日志
    public function postFCardLog($input){
        $list = [];
        $data = [];
        $agentId = intval($input['agent_id']);
        $cardId = empty($input['card_id']) ? 0 : intval($input['card_id']);

        $data['card_name'] = '';
        $data['agent_get_red_id'] = trim($input['card_id']);
        //获取福卡获取日志，然后封装
        $builder = RedPacketAgent::where('agent_id' , $agentId)
            ->where('type',5)->where('source',2);
        if(!empty($cardId)){
            $builder = $builder->where('red_packet_id' , $cardId);
            $data['card_name'] = trim(RedPacket::find($cardId)->remark);
        }
        $agentRedPacketLog = $builder->orderBy('created_at','desc')->get()->toArray();
        $redPacketNum = 0;
        foreach ($agentRedPacketLog as $oneLog){
            $arr = [];
            $arr['card_name'] = trim($oneLog['remark']);
            $arr['time'] = trim($oneLog['created_at']);
            $arr['person_name'] = '';

            //共有三中情况1、自己领取   2、被赠送  3、赠送
            if($oneLog['status'] == 2){
                $arr['type'] = '3';
                $arr['person_name'] = Agent::unifiHandleName(Agent::find($oneLog['uid']) , '','agent');
            }
            else if($oneLog['how_get'] == 2){
                $oneLog['status'] == 0 && $redPacketNum++;
                $arr['type'] = '2';
                $arr['person_name'] = Agent::unifiHandleName(Agent::find($oneLog['give_id']) , '','agent');
            }
            else{
                $oneLog['status'] == 0 && $redPacketNum++;
                $arr['type'] = '1';
            }
            $list[] = $arr;
        }

        $data['can_use_num'] = $redPacketNum;
        $data['log'] = $list;
        return ['message'=> $data , 'status'=>true];
    }

    /**
    *   作者：shiqy
    *   创作时间：2018/1/19 0019 下午 7:51
    *   功能描述：领取开门大吉红包
    */
    public function postReceiveOpenRedpacket($input){
        $data = RedPacketAgent::receiveOpenRedpacket($input['agent_id']);
        return ['message'=>$data , 'status'=>true];
    }

    /**
     *   作者：shiqy
     *   创作时间：2018/1/26 0026 下午 2:10
     *   功能描述：经纪人赠送福字红包
     */

    public function postGiveFRedpacket($input){
        $giveAgentId = intval($input['give_agent_id']);
        $getAgentId = intval($input['get_agent_id']);
        $cardId = intval($input['card_id']);
        $nowTime = time();
        $redPacket = RedPacketAgent::where('agent_id' , $giveAgentId)->where('red_packet_id',$cardId)
            ->where('type',5)->where('source',2)->where('status',0)->first();
        if(!is_object($redPacket)){
            return ['message'=>'no_redpacket' , 'status'=>false];
        }
        try{
            \DB::beginTransaction();
            $redPacket->status = 2;
            $redPacket->uid = $getAgentId;
            $redPacket->give_time = $nowTime;
            $redPacket->save();
            $data = $redPacket->toArray();
            unset($data['id']);
            $data['agent_id'] = $getAgentId;
            $data['status'] = 0;
            $data['created_at'] = $nowTime;
            $data['updated_at'] = $nowTime;
            $data['give_time'] = $nowTime;
            $data['uid'] = 0;
            $data['how_get'] = 2;
            $data['give_id'] = $giveAgentId;
            RedPacketAgent::insert($data);
            \DB::commit();
        }catch (\Exception $e){
            \DB::rollBack();
            return ['message'=> $e->getMessage() , 'status'=>false];
        }
        return ['message'=>'ok' , 'status'=>true];
    }
}