<?php namespace App\Console\Commands\Agent;

use Illuminate\Console\Command;
use App\Models\Agent\Invitation;
use App\Models\Zone\Entity as Zones;
use App\Models\Agent\Agent;

class InspectNotice extends Command
{
    protected $signature = 'Agent:InspectNotice';

    const INVITE_STATUS  = 0;   //邀请函状态： 0 表示待确认
    const INSPECT_TYPE   = 2;   //考察邀请函类型
    //const INSPECT_URL    = "<a href='{host}/webapp/agent/customer/ins-remind?agent_id={agent_id}'>";

    public function handle()
    {
        //获取邀请函里面的数据信息，状态 = 0， 过期时间大于现在时间
        $invite_result = Invitation::with('belongsToAgent', 'hasOneUsers',
            'hasOneStore.hasOneBrand', 'hasOneStore.hasOneZone'
        )
         ->where('type', self::INSPECT_TYPE)
         ->where('status', self::INVITE_STATUS)
         ->where('expiration_time', '>', time())
            ->orderBy('agent_id', 'asc')
         ->get();

        //对结果进行处理
        if (!empty($invite_result)) {
            foreach ($invite_result as $key => $vls) {
                $notice_time = ceil(($vls->expiration_time - time()) / 86400);
                if ($notice_time > 0 && $notice_time < 2) {
                    $user_name = $vls->hasOneUsers->realname ? $vls->hasOneUsers->realname : $vls->hasOneUsers->nickname;
                    $_info = trans('sms.invite_info_notice', [
                        'customer_name' => $vls->hasOneUsers->gender == -1 ?  "未知" : ($vls->hasOneUsers->gender == 0 ?  substr($user_name, 0 , 3) . ' 女士' : substr($user_name, 0 ,3) . ' 先生'),
                        'zone_name'     => Zones::pidNames([$vls->hasOneStore->hasOneZone->id]),
                        'brand_name'    => $vls->hasOneStore->hasOneBrand->name,
                        'times'         => date("m月d日", $vls->expiration_time),
                    ]);

                    //根据经纪人ID获取一条经纪人数据
                    $agent = Agent::where('id', $vls->belongsToAgent->id)->first();

                    //发送（通知）消息
                   send_notification('无界商圈经纪人', $_info, json_encode([
                        'type'  => 'inspect_invite_notice',
                        'style' => 'json',
                        'value' => $_info
                    ]), $agent, null, true);
                }
            }
        }

    }
}