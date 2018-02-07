<?php namespace App\Console\Commands\Agent;

use App\Models\Agent\Agent;
use App\Models\Agent\Message\AgentCustomerMessage;
use App\Models\Agent\AgentCustomer;
use App\Models\User\Entity as User;
use App\Models\Zone\Entity as Zone;
use Illuminate\Console\Command;

class AgentCustomerPush extends Command
{
    protected $signature = 'Agent:AgentCustomerPush';

    const IS_READ           = 0;   //消息未读状态标记
    const LOST_TYPE         = -1;  //失去客户标记
    const CONFIRM_TYPE      = 1;   //自己已经接单标记
    const OTHER_PERSON_TYPE = 2;   //其他人已经接单
    const AWAIT_TYPE        = 0;   //等待接单状态标记

    //处理数据
    public function handle()
    {
        //获取需要发送通知数据信息
        $agent_customer_result = AgentCustomerMessage::where('send_time', '<', time())
            ->where('is_read', self::IS_READ)
            ->select('id', 'customer', 'active', 'activity', 'fond_brand', 'agent_id', 'customer_id', 'status', 'is_read')
            ->get();

        //对结果进行处理
        if ($agent_customer_result) {
            foreach ($agent_customer_result as $key => $vls) {
                $agent = Agent::where('id', $vls->agent_id)->first();
                $agent_result = AgentCustomer::where('uid', $vls->customer_id)
                    ->where('agent_id', $vls->agent_id)
                    ->where('level',  '<>', self::LOST_TYPE)
                    ->where('status', '<>', self::LOST_TYPE)
                    ->first();

                //判断当前推荐的投资人和经纪人是否存在关系
                if (is_object($agent_result)) {
                    AgentCustomerMessage::where([
                        'agent_id'    => $vls->agent_id,
                        'customer_id' => $vls->customer_id,
                    ])->update(['status' => 1]);
                }

                //发送通知信息
                $send_notice_result = send_transmission(json_encode([
                    'type'  => 'recommend_investor',
                    'style' => 'json',
                    'value' => [
                        'title'    => '推荐投资人通知',
                        'sendTime' => time(),
                    ],
                ]), $agent, null, true);

                //发送成功改变发送状态
                if (isset($send_notice_result['result']) && $send_notice_result['result'] == 'ok') {

                    //更新时间，更新为已读
                    $data = ['is_read' => 1, 'updated_at' => time()];
                    AgentCustomerMessage::where([
                        'agent_id'    => $vls->agent_id,
                        'customer_id' => $vls->customer_id,
                    ])->update($data);
                }
            }
        }
    }
}