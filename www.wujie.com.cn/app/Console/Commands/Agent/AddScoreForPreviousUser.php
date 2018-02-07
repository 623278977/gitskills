<?php

/*
 * 用户签到推送相关
 */

namespace App\Console\Commands\Agent;

use App\Models\Agent\AgentAchievement;
use App\Models\Agent\AgentCurrencyLog;
use App\Models\Agent\Score\AgentScoreLog;
use App\Models\Config;
use Illuminate\Console\Command;
use App\Models\Agent\Entity\_v010200 as Agentv010200;
use App\Models\Agent\AgentCategory;

class AddScoreForPreviousUser extends Command {

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'addscoreforprevioususer';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '为以前的用户添加积分';


    public function handle()
    {
        //查询没有给积分却已完善资料的用户
        $agents = Agentv010200::where('nickname', '<>', '')->where('birth', '<>', '')->where('avatar', '<>', '')
            ->where('gender', '<>', '-1')->where('zone_id', '<>', '')->where('diploma', '<>', '')
            ->where('profession', '<>', '')->where('earning', '<>', '')
            ->whereNotIn('id', function($query){
                return $query->from('agent_score_log')->where('type', 17)->lists('agent_id');
            })->get();


        foreach($agents as $k=>$v){
            Agentv010200::add($v->id, AgentScoreLog::$TYPES_SCORE[17], 17, '完善个人资料', 0, 1, 1, $v->created_at->timestamp, $v->created_at->timestamp);
        }


        //查询没有给积分却已实名认证的用户
        $agents = Agentv010200::where('is_verified', 1)
            ->whereNotIn('id', function($query){
                return $query->from('agent_score_log')->where('type', 18)->lists('agent_id');
            })->get();


        foreach($agents as $k=>$v){
            Agentv010200::add($v->id, AgentScoreLog::$TYPES_SCORE[18], 18, '实名认证', 0, 1,1, $v->created_at->timestamp, $v->created_at->timestamp);
        }

    }

}
