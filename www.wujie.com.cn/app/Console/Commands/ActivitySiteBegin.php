<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Foundation\Inspiring;
use DB;
use App\Models\CityPartner\Income;
use App\Models\CityPartner\Entity;
use \Mail;
use App\Models\Message;


class ActivitySiteBegin extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'activity_site_begin';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '报名的活动即将开始（现场票）';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {

        //明天凌晨
        $begin = mktime(0, 0, 0, date('m', time()), (date('d', time()) + 1), date('Y', time()));
        //后天凌晨
        $end = mktime(0, 0, 0, date('m', time()), (date('d', time()) + 2), date('Y', time()));
        $query = DB::table('user_ticket')
            ->leftJoin('activity', 'user_ticket.activity_id', '=', 'activity.id')
            ->leftJoin('user', 'user.uid', '=', 'user_ticket.uid')
            ->where('user_ticket.is_send_sms', 0)
            ->where('user_ticket.type', 1)//现场票
            ->where('user_ticket.status', 1)//已支付
            ->whereBetween('activity.begin_time', [$begin, $end])
            ->where('activity.status',1)
            ->select('user_ticket.id', 'user_ticket.uid', 'user_ticket.ticket_id', 'user_ticket.activity_id',
                'user_ticket.maker_id', 'activity.subject', 'activity.begin_time','user.non_reversible','user.nation_code');
        $lists_sms = $query->where('user_ticket.is_send_sms', 0)->get();

        $collection = collect($lists_sms)->groupBy('activity_id')->toArray();

        //号码集
        $non_reversibles = array_pluck($lists_sms, 'nation_code','non_reversible');

        //用户id集
        $uids = array_pluck($lists_sms, 'id');

        //门票id集
        $ticket_ids = array_pluck($lists_sms, 'ticket_id');

        //举办活动地点集
        $maker_ids = array_pluck($lists_sms, 'maker_id');

        foreach ($collection as $k => $v) {

            $url = config('app.app_url') . 'activity/detail/'.config('app.version').'?pagetag=02-2&id='.$v[0]->activity_id.'&is_share=1';

            @SendsTemplateSMS('activitySiteBegin',$non_reversibles,'activitySiteBegin',[
                'name' => $v->subject,
                'time' => date('m月d日 H点i分',$v[0]->begin_time),
                'url'=>shortUrl($url)
            ]);
        }

        DB::table('user_ticket')
            ->whereIn('uid', $uids)
            ->whereIn('ticket_id', $ticket_ids)
            ->whereIn('maker_id', $maker_ids)
            ->update(['is_send_sms' => 1, 'updated_at' => time()]);

    }


}
