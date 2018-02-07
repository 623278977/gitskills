<?php

namespace App\Console\Commands;

use App\Models\Message;
use Illuminate\Console\Command;
use Illuminate\Foundation\Inspiring;
use DB;
use App\Models\CityPartner\Income;
use App\Models\CityPartner\Entity;
use \Mail;
use App\Services\Live;

class LiveBegin extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'live_begin';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '订阅的直播即将开始';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $query = DB::table('user_subscription')
            ->leftJoin('live', 'user_subscription.live_id', '=', 'live.id')
            ->leftJoin('user', 'user_subscription.uid', '=', 'user.uid')
            ->where('user_subscription.status', 1)
            ->select('user_subscription.id',
                'user_subscription.uid',
                'user_subscription.live_id',
                'live.subject',
                'live.live_url',
                'live.id as live_id',
                'user.non_reversible',
                'user.nation_code');

        $push_query = clone $query;
        $h5_query = clone $query;

        $lists = $query->where('user_subscription.path', 'app')
            ->where('user_subscription.is_send', 0)
            ->whereBetween('live.begin_time', [time() + 3300, time() + 3900])//防止脚本执行的时候漏掉，把时间左右偏移5分钟。
            ->get();

        $h5_lists = $h5_query->where('user_subscription.path', 'h5')
            ->where('user_subscription.is_send', 0)
            ->whereBetween('live.begin_time', [time() + 1500, time() + 2100])//防止脚本执行的时候漏掉，把时间左右偏移5分钟。
            ->get();

        $collection_lists = collect($lists)->groupBy('live_id')->toArray();
        $collection_h5_lists = collect($h5_lists)->groupBy('live_id')->toArray();

        //号码集
        $non_reversibles_lists = array_pluck($lists, 'nation_code', 'non_reversible');
        $non_reversibles_h5_lists = array_pluck($h5_lists, 'nation_code', 'non_reversible');

        //用户订阅id集
        $uids_lists = array_pluck($lists, 'id');
        $uids_h5_lists = array_pluck($h5_lists, 'id');

        foreach ($collection_lists as $k => $v) {

            $url = config('app.app_url') . 'live/detail/' . config('app.version') . '?pagetag=' . config('app.live_detail') . '&id=' . $v[0]->live_id . '&is_share=1';

            @SendsTemplateSMS('liveBegin', $non_reversibles_lists, 'liveBegin', ['name' => $v[0]->subject, 'url' => shortUrl($url)]);

        }

        foreach ($collection_h5_lists as $k => $v) {

            @SendsTemplateSMS('h5LiveBegin', $non_reversibles_h5_lists, 'h5LiveBegin', ['name' => $v[0]->subject]);

        }

        DB::table('user_subscription')
            ->whereIn('id', $uids_lists)
            ->update(['is_send' => 1, 'updated_at' => time()]);

        DB::table('user_subscription')
            ->whereIn('id', $uids_h5_lists)
            ->update(['is_send' => 1, 'updated_at' => time()]);

        $push_lists = $push_query
            ->where('user_subscription.is_push', 0)
            //防止脚本执行的时候漏掉，把时间左右偏移5分钟。
            ->whereBetween('live.begin_time', [time() + 1500, time() + 2100])
            ->get();

        foreach ($push_lists as $key => $val) {
            //发送推送信息
            $users = DB::table('user')->where('uid', $val->uid)->get();
            $live = \DB::table('live')->where('id', $val->live_id)->first();
            $users = new \Illuminate\Support\Collection(
                array_map(
                    function ($item) {
                        return new Entity((array)$item);
                    },
                    $users
                )
            );

            $liveService = new Live();
            //判断是不是招商会
            $is_invest = $liveService->isInvest($val->live_id);
            //有没有购买或者不需要购买
            $need_pay = $liveService->needPay($val->live_id, $users[0]->uid);
                //如果是招商会
                if ($is_invest) {
                    //如果需要购买
                    if ($need_pay) {
                        $title = "你订阅的{$live->subject}快要开始啦";
                        $content = "你订阅的{$live->subject}就要开始了，本次招商会为付费直播，赶快点击支付和我们一起观看直播";
                        //发送站内信
                        Message::create([
                            'title'=>'你订阅的直播30分钟后即将开始',
                            'uid'=>$users[0]->uid,
                            'content'=>"你订阅的{$live->subject}距离直播还有30分钟，该场招商会为付费直播，购买后可观看直播。",
                            'type'=>3,
                            'post_id'=>$val->live_id,
                            'send_time'=>time(),
                        ]);

                        //如果不需要购买
                    } else {

                        $title = "你订阅的{$live->subject}快要开始啦";
                        $content = "你订阅的{$live->subject}快要开始啦，1，2，3，抢到你就赚到~";
                        //发送站内信
                        Message::create([
                            'title'=>'你订阅的直播30分钟后即将开始',
                            'uid'=>$users[0]->uid,
                            'content'=>"你订阅的{$live->subject}距离直播还有30分钟",
                            'type'=>3,
                            'post_id'=>$val->live_id,
                            'send_time'=>time(),
                        ]);
                    }
                    //如果不是招商会
                } else {
                    //如果需要购买
                    if ($need_pay) {
                        $title = "{$live->subject}开放直播喽";
                        $content = "{$live->subject}开放直播喽，点击至直播详情，报名后就可观看直播了。";
                        //如果不需要购买
                    } else {
                        $title = "你订阅的{$live->subject}还有半个小时就要开始了";
                        $content = "你订阅的{$live->subject}还有半个小时就要开始了，准备好和我们一起零距离的跨域连线吧";
                    }
                }

                $result = send_notification(
                    $title,
                    $content,
                    json_encode(
                        [
                            'type'  => 'live_detail',
                            'style' => 'url',
                            'value' => "/webapp/live/detail?pagetag=04-9&id={$val->live_id}"
                        ]
                    ),
                    $users,
                    [date('Y-m-d H:i:s'), date('Y-m-d H:i:s', $live->begin_time)]
                );

                DB::table('user_subscription')->where('id', $val->id)->update(['is_push' => 1, 'updated_at' => time()]);
            }
    }
}
