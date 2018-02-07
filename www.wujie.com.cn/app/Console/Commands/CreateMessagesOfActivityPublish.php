<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class CreateMessagesOfActivityPublish extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'publisher:createactivity';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '发布者发布活动时,定时生成消息';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $today = mktime(0, 0, 0, date('m', time()), (date('d', time())), date('Y', time()));
        $tomorrow = mktime(0, 0, 0, date('m', time()), (date('d', time()) + 1), date('Y', time()));


        //获取所有发布者的关注人
        $fans = \DB::table('activity_publisher as ap')
            ->join('activity_organizer_follow as aof','ap.id','=','aof.organizer_id')
//            ->select('ap.id','aof.uid')
            ->select(\DB::raw('group_concat(lab_ap.id) as publishers,lab_aof.uid'))
            ->groupBy('aof.uid')
            ->get();
        //给每个关注人发送新活动推荐
        //当天如果有新活动创建则推荐给用户
        //每个人最多推送三个按照活动的开始时间顺序展示
        foreach($fans as $fan){
            $publishers = explode(',',$fan->publishers);
            //获取推荐的活动最多三个
            $activities = \DB::table('activity')
                ->whereIn('activity.publisher_uid',$publishers)
                ->whereBetween('activity.created_at',[$today, $tomorrow]) //只发送今天发布的活动
                ->select('id as activity_id','subject','begin_time','list_img')
                ->limit(3)
                ->orderBy('begin_time','asc')
                ->get();
            $data = [];
            if(!empty($activities)){
                $activity_id = 0;
                foreach($activities as $key => $activity){
                    $activity_id                = $activity->activity_id;
                    $data[$key]['activity_id']  = $activity->activity_id;
                    $data[$key]['list_img']     = $activity->list_img;
                    $data[$key]['subject']      = $activity->subject;
                    $data[$key]['begin_time']   = $activity->begin_time;
                    $data[$key]['price']        = \App\Models\Activity\Ticket::getLowestTicketPriceOfActivity($activity->activity_id);
                }
                //插入消息
                \DB::table('my_message')
                    ->insert([
                        'created_at'=>time(),
                        'updated_at'=>time(),
                        'uid'=>$fan->uid,
                        'type'=>10,
                        'content'=>serialize($data),
                        'post_id'=>$activity_id, //连表时要用道post_id
                        'send_time'=>time()+3600*8 //八小时后发送
                ]);
            }
        }
    }
}
