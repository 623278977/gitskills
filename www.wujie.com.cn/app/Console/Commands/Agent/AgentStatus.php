<?php namespace App\Console\Commands\Agent;

use Illuminate\Console\Command;
use App\Models\Agent\Agent;

class AgentStatus extends Command
{
    protected $signature = "Agent:AgentStatus";

    const AGENT_STATUS   = 1;   //经纪人状态：1表示正常， -1表示禁用
    const IS_ONLINE      = 0;   //经纪人在线状态：0不在线，1在线

    public function handle()
    {
        $agent = Agent::where('status', self::AGENT_STATUS)
            ->where('is_online', self::IS_ONLINE)
            ->get();

        //对结果进行处理
        if ($agent) {
            foreach ($agent as $key => $vls) {
                $not_online_day = ceil((time() - $vls->last_login) / 86400 );
                if ($not_online_day > 5) {
                    send_notification(
                        '无界商圈经纪人',
                        '当前处于“停止派单”状态，赶紧切换状态，获得更多派单投资人，佣金赚不停！',
                        json_encode([
                        'type'  => 'not_online_notice',
                        'style' => 'json',
                        'value' => [
                            'title'    => '当前处于“停止派单”状态，赶紧切换状态，获得更多派单投资人，佣金赚不停！',
                            'sendTime' => time()
                        ]
                    ]), $vls, null, true);
                }
            }
        }
    }
}
