<?php namespace App\Console\Commands\Agent;

use App\Models\Agent\Invitation;
use Illuminate\Console\Command;
use App\Models\Zone\Entity as Zones;

class InviteNotice extends Command
{
    protected $signature = 'Agent:InviteNotice';

    const INVITE_STATUS  = 0;   //邀请函状态： 0 表示待确认
    const ACTIVITY_TYPE  = 1;   //活动邀请函类型
    const INSPECT_TYPE   = 2;   //考察邀请函类型
    const ACTIVITY_URL   = "<a href='{host}/webapp/actinlist/detail/_v020800?uid={uid}'>";
    const INSPECT_URL    = "<a href='{host}/webapp/actinvestlist/detail/_v020800?uid={uid}'>";

    public function handle()
    {
        //获取邀请函里面的数据信息，状态 = 0， 过期时间大于现在时间
        $invite_result = Invitation::with('belongsToAgent', 'hasOneUsers',
            'hasOneStore.hasOneBrand', 'hasOneStore.hasOneZone',
            'hasOneActivity.brands.zone')
            ->where('status', self::INVITE_STATUS)
            ->where('expiration_time', '>', time())
            ->get();

        //对数据进行处理
        foreach ($invite_result as $keys => $vls) {
            $confirm_day = ceil(($vls->expiration_time - time()) / 86400);
            if ($confirm_day > 1 && $confirm_day < 3) {
                if ($vls->type == self::ACTIVITY_TYPE) {
                    $this->_gainConfigInfo($vls, self::ACTIVITY_TYPE);
                } elseif ($vls->type == self::INSPECT_TYPE) {
                    $this->_gainConfigInfo($vls, self::INSPECT_TYPE);
                }
            }
        }
    }

    /**
     * 获取数据，进行数据推送
     *
     * @param $vls            数据集合
     * @param $invite_type    邀请函类型（1：活动； 2：考察）
     */
    private function _gainConfigInfo($vls, $invite_type)
    {
        //根据不同类型（活动 | 考察）生成要跳转的url
        if ($invite_type == self::ACTIVITY_TYPE) {
            //$_url  = str_replace(["{host}", "{uid}"], [env('app_post'), $vls->hasOneUsers->uid], self::ACTIVITY_URL);
            $_info = trans('tui.activity_info_notice', [
               // 'start_a'       => $_url,
                'name'          => $vls->belongsToAgent->is_public_realname ?  $vls->belongsToAgent->realname : $vls->belongsToAgent->nickname,
                'zone_name'     => Zones::pidNames([$vls->hasOneActivity->brands->zone->id]),
                'activity_name' => $vls->hasOneActivity->subject,
               // 'end_a'         => '</a>',
            ]);

            $_type     = 'activity_info_notice';
            $notice_id = $vls->id;

        } elseif ($invite_type == self::INSPECT_TYPE) {
            //$_url  = str_replace(["{host}", "{uid}"], [env('app_post'), $vls->hasOneUsers->uid], self::INSPECT_URL);
            $_info = trans('tui.inspect_info_notice', [
               // 'start_a'    => $_url,
                'name'       => $vls->belongsToAgent->is_public_realname ?  $vls->belongsToAgent->realname : $vls->belongsToAgent->nickname,
                'zone_name'  => Zones::pidNames([$vls->hasOneStore->hasOneZone->id]),
                'brand_name' => $vls->hasOneStore->hasOneBrand->name,
               // 'end_a'      => '</a>',
            ]);

            $_type     = 'inspect_info_notice';
            $notice_id = $vls->id;
        }

        //如果用户存在，发送通知消息
        if ($vls->hasOneUsers) {
            send_notification('无界商圈', $_info, json_encode([
                'type' => $_type,
                'style' => 'json',
                'value' => $notice_id
            ]), $vls->hasOneUsers, null);

        }
    }
}