<?php

/*
 * 用户签到推送相关
 */

namespace App\Console\Commands;

use App\Models\Agent\AgentAchievement;
use App\Models\Agent\AgentCurrencyLog;
use Illuminate\Console\Command;
use App\Models\Agent\Agent;
use Symfony\Component\Console\Input\InputArgument;

class Unfreeze extends Command {

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'unfreeze';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '每个月解冻经纪人的冻结佣金';


    public function handle()
    {
        //开始事务
        \DB::beginTransaction();
        try {
//            $quarter_info = Agent::instance()->getQuarter(time());
            $month_info=date('Y年m月',strtotime(date('Y',time()).'-'.(date('m',time())-1)));
            $frozen_commissions = AgentAchievement::where('month', $month_info)
                ->where('frozen_commission', '>', 0)->select('id', 'frozen_commission', 'agent_id')->where('unfreeze', 0)
                ->get();

            foreach ($frozen_commissions as $k => $v) {
                $agent = Agent::find($v->agent_id);
                $agent->currency = $agent->currency + $v->frozen_commission;
                $agent->save();


                AgentCurrencyLog::create(
                    [
                        'agent_id' => $v->agent_id,
                        'operation' => 1,
                        'num' => $v->frozen_commission,
                        'type' => 5,
                        'post_id' => $v->id,
                        'currency' => $agent->currency,
                    ]
                );

                send_transmission(json_encode(['type'=>'new_message', 'style'=>'json',
                    'value'=> ['sendTime' => time()]]),
                    $agent,null, 1);

            }
            \DB::commit();
        } catch (\Exception $e) {
            \DB::rollBack();
            throw(new \RuntimeException($e->getMessage()));
        }
    }

}
