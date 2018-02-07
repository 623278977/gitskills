<?php namespace App\Console\Commands\Agent;

use App\Models\Agent\Agent;
use App\Models\Agent\AgentCustomer;
use Illuminate\Console\Command;

class TimeNotice extends Command
{
    protected $signature = 'Agent:timeNotice';

    const LOSE_CUSTOMER  = -1;  //失去客户

    public function handle()
    {
        //查询出满足条件的数据
        $result_data = AgentCustomer::where('protect_time', '>', 0)
            ->where('level',  '<>', self::LOSE_CUSTOMER)
            ->where('status', '<>', self::LOSE_CUSTOMER)
            ->get();

        if ($result_data) {

            //循环获取经纪人和投资人的信息
            foreach ($result_data as $key => $vls) {
                $time = ceil(($vls->protect_time - time()) / 86400);
                if ($time > 0 && $time <= 3) {
                   $notice_customer_num = AgentCustomer::where('agent_id', $vls->agent_id)->count();
                   $agent = Agent::where('id', $vls->agent_id)->first();
                   send_notification(
                       '无界商圈经纪人',
                       "你有{$notice_customer_num}个邀请投资人即将过保护期，请及时确认。",
                       json_encode([
                           'type'  => 'message_notice',
                           'style' => 'url',
                           'value' => shortUrl('https://' .env("APP_HOST"). '/webapp/agent/customer/pro-remind?agent_id='.$vls['agent_id'])
                       ]), $agent, null, true);
                }
            }
        }
    }

}