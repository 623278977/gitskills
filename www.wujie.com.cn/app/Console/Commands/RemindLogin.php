<?php

/*
 * 用户签到推送相关
 */

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputArgument;
use App\Models\Agent\Agent;
class RemindLogin extends Command {

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'remind_login';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '超过五天没有登录，发送推送';
    /*超过5天发送个推，只发送一次*/
    public function handle() {
        $nowTime = time();
        $agentInfo = Agent::where(function ($query)use($nowTime){
            $maxTimeZone = $nowTime - 5*86400;
            $minTimeZone = $nowTime - 6*86400;
            $query->where('status',1);
            $query->where('last_login','<=',$maxTimeZone);
            $query->where('last_login','>',$minTimeZone);
        })->get();
        try{
            foreach ($agentInfo as $oneAgent){
                $rand = mt_rand(0,2);
                SendTemplateNotifi('agent_title', [], "remind_login.{$rand}", [],
                    json_encode([
                    'type' => 'remind_login',
                    'style' => 'url',
                    'value' => $oneAgent['id'],
                ]), $oneAgent, null, 1);
//                $res = send_notification('无界商圈经纪人', $this->pushContent[$rand],
//                    json_encode(['type'=>'remind_login', 'style'=>'json',
//                        'value'=>"/agent/index/index/_v010000?agent_id={$oneAgent['id']}"]),
//                    $oneAgent,null,true);
            }
        }catch (Exception $e) {
            file_put_contents(storage_path().'/logs/sqy.log',$e->getMessage());
        }
    }
}
