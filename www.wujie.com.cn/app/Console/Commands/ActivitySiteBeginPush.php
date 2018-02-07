<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Foundation\Inspiring;
use DB;
use App\Models\CityPartner\Income;
use App\Models\CityPartner\Entity;
use App\Models\User\Entity as User;
use \Mail;
use App\Models\Message;


class ActivitySiteBeginPush extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'activity_site_begin_push';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '报名的活动即将开始（现场票）推送消息';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $lists_push = DB::table('user_ticket')
            ->leftJoin('activity', 'user_ticket.activity_id', '=', 'activity.id')
            ->where('user_ticket.is_send_push', 0)
            ->where('user_ticket.type', 1)//现场票
            ->where('user_ticket.status', 1)
            ->where('activity.status', 1)
            ->whereBetween('activity.begin_time', [time()+1600, time()+2000])//左右偏离200秒，防止有漏网之鱼
            ->select('user_ticket.id', 'user_ticket.uid', 'user_ticket.ticket_id', 'user_ticket.activity_id','user_ticket.id as user_ticket_id',
                'user_ticket.maker_id', 'activity.subject', 'activity.begin_time')
            ->get();

        foreach ($lists_push as $k => $v) {
            //给所有报名了该活动的用户发送推送
            $user = User::where('uid', $v->uid)->get();
            $activity = \DB::table('activity')->where('id', $v->activity_id)->first();
//            $user=new \Illuminate\Support\Collection(array_map(function($item){
//                return new Entity((array)$item);
//            },$user));
            send_notification('报名的活动在30分钟以后即将开始', '你报名的活动将在30分钟后开始，赶紧验票进场吧，点击查看更多',
                json_encode(['type'=>'ticket_detail', 'style'=>'id', 'value'=>$v->user_ticket_id]),
                $user
                ,[date('Y-m-d H:i:s'), date('Y-m-d H:i:s', $activity->begin_time)]
            );

            DB::table('user_ticket')
                ->where('id', $v->id)
                ->update(['is_send_push' => 1, 'updated_at' => time()]);
        }
    }


}
