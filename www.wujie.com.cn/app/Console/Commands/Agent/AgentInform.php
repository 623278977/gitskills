<?php namespace App\Console\Commands\Agent;

use App\Models\Agent\Agent;
use App\Models\Message;
use Illuminate\Console\Command;

class AgentInform extends Command
{
    protected $signature = "Agent:AgentInform";

    const MESSAGE_TYPE   = 12;  //经纪人通知ID
    const IS_READ        = 0;   //消息未读状态标记

    public function handle()
    {
        $message_result = Message::where('type', self::MESSAGE_TYPE)
            ->where('send_time', '<', time())
            ->where('is_read', self::IS_READ)
            ->select('id', 'title', 'content', 'url', 'type', 'agent_id')
            ->get();

        //对结果进行处理
        if ($message_result) {
            foreach ($message_result as $key => $vls) {
                if (!empty($vls->agent_id)) {
                    $agent = Agent::where('id', $vls->agent_id)
                        ->where('status', 1)->first();
                } else {
                    $agent = null;
                }

               //发送通知消息
               send_transmission(json_encode([
                        'type'  => 'new_message',
                        'style' => 'json',
                        'value' => [
                            'title'    => $vls->title,
                            'sendTime' => time(),
                        ],
                    ]), $agent, null, true);
            }
        }
    }
}