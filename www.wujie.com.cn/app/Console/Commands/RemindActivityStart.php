<?php

/*
 * 用户签到推送相关
 */

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputArgument;
use App\Models\Activity\Sign;
use App\Models\Activity\Entity as Activity;

class RemindActivityStart extends Command {

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'remind_activity_start';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '活动开始前一天发送提醒';


    /*活动开始前一天发送提醒*/
    public function handle() {
        $signInfo = Sign::with('hasOneActity','agent')
            ->whereHas('hasOneActity',function ($query){
                $minTime = strtotime('tomorrow');
                $maxTime = $minTime + 86400;
                $query->where('begin_time','<',$maxTime);
                $query->where('begin_time','>=',$minTime);
            })
            ->where(function($query){
            $query->where('status',0);
            $query->where('is_invite',1);
        })->get();
        foreach ($signInfo as $oneSign){
            $activeStartTime = date('H:i',$signInfo->getRelations()['has_one_actity']['begin_time']);


            SendTemplateNotifi('agent_title', [], 'remind_activity_start', [
                'actityName'=> trim($signInfo->getRelations()['has_one_actity']['subject']),
                'startTime'=> trim($activeStartTime),
            ], json_encode([
                'type' => 'activity_detail',
                'style' => 'url',
                'value' => $oneSign['has_one_actity']['id'],
            ]), $signInfo->getRelations()['agent'], null, 1);
//            send_notification('无界商圈经纪人', "温馨提示，你邀请了投资人参加的OVO活动 [ {$signInfo->getRelations()['has_one_actity']['subject']} ] 将于明天{$activeStartTime}开始。\\n记得提醒参会的小伙伴，不要迟到哟！",
//                json_encode(['type'=>'remind_activity_start', 'style'=>'json',
//                    'value'=>"/agent/customer/activity-remind/_v010000?agent_id={$oneSign['agent_id']}"]),
//                $signInfo->getRelations()['agent'],null,true);
        }
    }
}
