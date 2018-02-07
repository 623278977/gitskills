<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Foundation\Inspiring;
use DB;
use App\Models\CityPartner\Income;
use App\Models\CityPartner\Entity;
use \Mail;


class ActivityLiveBegin extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'activity_live_begin';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '报名的活动即将开始（直播票）';

    /**
     * Execute the console command. 数据中心版
     *
     * @return mixed
     */
    public function handle()
    {

        $lists = DB::table('user_ticket')
            ->leftJoin('activity', 'user_ticket.activity_id', '=', 'activity.id')
            ->leftJoin('live', 'live.activity_id', '=', 'activity.id')
            ->leftJoin('user', 'user.uid', '=', 'user_ticket.uid')
            ->where('user_ticket.is_send_sms', 0)
            ->where('user_ticket.type', 2)//直播票
            ->where('activity.status', 1)
            ->whereBetween('activity.begin_time', [time()+3300,time()+3900]) //防止脚本执行的时候漏掉，把时间左右偏移5分钟。
            ->select('user_ticket.id', 'user_ticket.uid', 'user_ticket.ticket_id', 'user.non_reversible','user.nation_code',
                'user_ticket.maker_id', 'activity.subject', 'live.live_url', 'live.id as live_id')
            ->get();


        $collection = collect($lists)->groupBy('live_id')->toArray();

        //号码集
        $non_reversibles = array_pluck($lists, 'nation_code','non_reversible');

        //用户id集
        $ids = array_pluck($lists, 'id');

        //门票id集
        $ticket_ids = array_pluck($lists, 'ticket_id');

        //举办活动地点集
        $maker_ids = array_pluck($lists, 'maker_id');

        foreach ($collection as $k => $v) {

            $live_url = config('app.app_url') . 'live/detail/' . config('app.version') . '?pagetag=' . config('app.live_detail') . '&id=' . $v['0']->live_id . '&is_share=1';
            @SendsTemplateSMS('activityLiveBegin', $non_reversibles, 'activityLiveBegin', ['name' => $v[0]->subject, 'url' => shortUrl($live_url)],'wjsq');

        }

        DB::table('user_ticket')
            ->whereIn('uid', $ids)
            ->whereIn('ticket_id', $ticket_ids)
            ->whereIn('maker_id', $maker_ids)
            ->update(['is_send_sms' => 1, 'updated_at' => time()]);

    }


}
