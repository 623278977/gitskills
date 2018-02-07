<?php

/*
 * 用户签到推送相关
 */

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputArgument;
use App\Models\Agent\Agent;
use App\Models\User\Entity as User;
use App\Models\Agent\AgentDevelopTeamLog;
class AddTeamLog extends Command {

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'add_team_log';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '在lab_agent_develop_team_log添加一条记录';

    /*
     * 对user表  和  agent表  中的  邀请经纪人和邀请客户 ，对在lab_agent_develop_team_log其每一个上级都添加一条记录
     * */
    public function handle() {

//        Agent::where('id','<>','')->update(['is_handle'=>0]);
//        User::where('uid','<>','')->update(['is_handle'=>0]);
//        \DB::table('agent_develop_team_log')->truncate();
        $this->checkTable(Agent::class);
        $this->checkTable(User::class);
    }

    //检测 user表 和agent表，将其中符合条件的记录筛选出来，并对每个上级插入数据
    protected function checkTable($modelClass){
        $type = 1;
        if(strpos($modelClass,'Agent') === false){
            $type = 2;
        };
        $meetRecords = $modelClass::where('is_handle',0)->where('register_invite','<>','')->get();
        foreach ($meetRecords as $oneRecord){
            $theRecordId = intval($oneRecord['id']);
            //获取关系建立时间
            $relateTime = trim($oneRecord['created_at']);

            //如果检测user表时，邀请人是投资人，则返回
            if($type == 2){
                $inviterInfo = $modelClass::getMyInviterInfo($oneRecord['uid']);
                if(!isset($inviterInfo['role'])){
                    continue;
                }
                else if($inviterInfo['role'] == 1){
                    //将其邀请人为投资人的记录置为1，下次不再检测
                    if($inviterInfo['role'] == 1){
                        $modelClass::where('username',$oneRecord['username'])->update(['is_handle'=>1]);
                    }
                    continue;
                }
                $theRecordId = $oneRecord['uid'];
                $relateTime = trim($oneRecord['updated_at']);
            }
            //对上级经纪人及其上级进行数据插入
            $superiors = Agent::where('username',$oneRecord['register_invite'])->first();
            if(!is_object($superiors)){
                continue;
            }

            $this->insertToLogs($superiors['id'],$superiors['id'],$theRecordId , $type ,strtotime($relateTime));
            $modelClass::where('username',$oneRecord['username'])->update(['is_handle'=>1]);
        }
    }


    /*
     * 递归对一个经纪人以及其所有上级在lab_agent_develop_team_log表中插入数据
     * */
    protected function insertToLogs($agentId,$partyId,$downLineId ,$type ,$time){
        $agentInfo = Agent::find($agentId);
        if(!is_object($agentInfo)){
            return;
        }
        $scale = 'agent_scale';
        if($type == 2){
            $scale = 'custom_scale';
        }

        //获取该经纪人之前团队总人数
        $newestLog = AgentDevelopTeamLog::where('agent_id',$agentId)
            ->where('type',$type)->orderBy($scale ,'desc')->first();
        $scaleNum = intval($newestLog[$scale]);

        //组装数据，插入AgentDevelopTeamLog
        $data = [
            'agent_id'=> $agentId,
            'party_id'=> $partyId,
            'downline_id'=> $downLineId,
            'type'=> $type,
            'created_at'=> $time,
            $scale => $scaleNum + 1 ,
        ];
        AgentDevelopTeamLog::insert($data);

        //获取上级经纪人id
        $superiors = Agent::where('username',$agentInfo['register_invite'])->first();
        if(!is_object($superiors)){
            return;
        }
        else{
            $this->insertToLogs($superiors['id'],$partyId,$downLineId ,$type ,$time);
        }
    }
}
