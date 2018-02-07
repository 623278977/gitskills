<?php

/*
 * 用户签到推送相关
 */

namespace App\Console\Commands\Agent;

use App\Models\Activity\Sign;
use App\Models\Agent\AgentAchievement;
use App\Models\Agent\AgentCurrencyLog;
use App\Models\Agent\AgentCustomer;
use App\Models\Activity\Entity as Activity;
use App\Models\Config;
use Illuminate\Console\Command;
use App\Models\Agent\Agent;
use App\Models\SendOrderQueue\V020800 as SendOrderQueue;
use App\Models\User\Entity as User;
use App\Models\Zone\Entity as Zone;
use App\Models\Brand\Entity\V020800 as Brand;

class CollectSignCommission extends Command
{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'Agent:CollectSignCommission';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '收集活动邀约佣金';


    public function handle()
    {
        //没有考虑tag
        $lists = Sign::where('collect_commission', 0)->where('status', 1)->orderBy('id', 'desc')
            ->get();


        foreach($lists as $k=>$v){
            //已处理过，并不代表一定给钱了
            Sign::where('id', $v->id)->update(['collect_commission' => '1']);

            //找出该投资人的邀请经纪人
            $agentCustomer = AgentCustomer::whereIn('source', [1,6])->where('uid', $v->uid)->first();
            if(!$agentCustomer){
                continue;
            }

            //判断是否有给过该经纪人活动签到奖励
            $sign_ids = Sign::where('status', 1)->where('uid', $v->uid)->get();
            $sign_ids = array_pluck($sign_ids, 'id');

            $exists = AgentCurrencyLog::where('agent_id', $agentCustomer->agent_id)->where('type', 14)->whereIn('post_id', $sign_ids)->first();

            if($exists){
                continue;
            }

            //加个逻辑 之前的活动报名不算 2017年12月18日之前的活动报名都不算
            $activity  = Activity::find($v->activity_id);

            if(!is_object($activity) || $activity->begin_time<strtotime('2017-12-18')){
                continue;
            }

            AgentCurrencyLog::addCurrency($agentCustomer->agent_id, 80, 14, $v->id, 1);
        }
    }

}
