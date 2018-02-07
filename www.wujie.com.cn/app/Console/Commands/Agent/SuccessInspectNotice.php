<?php namespace App\Console\Commands\Agent;

use App\Models\Agent\AgentCustomer;
use App\Models\Agent\Invitation;
use Illuminate\Console\Command;
use App\Models\Zone\Entity as Zone;

class SuccessInspectNotice extends Command
{
    protected $signature = 'Agent:SuccessInspectNotice';

    const ACCEPT_TYPE  = 1;     //接受邀请函
    const INSPECT_TYPE = 2;     //考察邀请类型
    const BAD_TYPE     = -1;    //拒绝、失去类型
    static $INVITE_TYPE  = [1, 2, 3, 4, 6, 7];    //邀请类型

    /**
     * 某个经纪人的邀请投资人即将进行门店考察时，进行消息提示
     */
    public function handle()
    {
        //执行发送数据信息
        $this->sendInfos();
    }

    /**
     * 获取投资人的邀请经纪人信息
     */
    protected function gainCustomerInviteAgent($uid)
    {
        $query_result = AgentCustomer::where('uid', $uid)
            ->where('level',  '<>', self::BAD_TYPE)
            ->where('status', '<>', self::BAD_TYPE)
            ->whereIn('source', self::$INVITE_TYPE)
            ->first();

        //对结果进行处理
        if (is_object($query_result)) {
            return $query_result;
        } else {
            return false;
        }
    }

    /**
     * 获取投资人接受考察，将进行门店考察信息
     */
    protected function customerStoreInspectInfoNotice()
    {
        $query_result = Invitation::with('hasOneStore.hasOneBrand',
            'hasOneUsers')->where([
            'type'   => self::INSPECT_TYPE,
            'status' => self::ACCEPT_TYPE,
        ])
        ->where('expiration_time', '>', time())
        ->get();

        //对结果进行处理
        if ($query_result) {
            return $query_result;
        } else {
            return false;
        }
    }

    /**
     * 发送消息
     */
    protected function sendInfos()
    {
        //对结果进行处理
        $query_result = $this->customerStoreInspectInfoNotice();
        if ($query_result) {
            foreach ($query_result as $key => $vls) {

                //获取投资人的邀请经纪人
                $agent_result = $this->gainCustomerInviteAgent($vls->hasOneUsers->uid);

                //如果获取到投资人对应的邀请经纪人后，进行消息的发送
                if ($agent_result) {
                    if (!is_null($vls->hasOneStore->hasOneBrand) &&
                        $vls->hasOneStore->hasOneBrand->agent_status == self::ACCEPT_TYPE) {

                        //数组需要发送的数据信息
                        $_info = trans('tui.tui_inspect_info_confirm_notice', [
                            'customer_name'     => $vls->hasOneUsers->realname ?: $vls->hasOneUsers->nickname,
                            'zone_name'         => Zone::pidNames([$vls->hasOneUsers->zone_id]),
                            'inspect_time'      => date("Y年m月d日", $vls->inspect_time),
                            'brand_name'        => $vls->hasOneStore->hasOneBrand->name,
                            'inspect_zone_name' => Zone::pidNames([$vls->hasOneStore->zone_id])
                        ]);

                        //发送透传消息
                        send_notification('无界商圈', $_info, json_encode([
                            'type'  => 'success_inspect_notice',
                            'style' => 'json',
                            'value' => $vls->hasOneUsers->uid
                        ]), $agent_result, true);
                    }
                }
            }
        }
    }

}