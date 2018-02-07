<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class CreateMessagesOfOrdinaryLiveRecommend extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'live:ordinaryrecommend';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '普通用户发送直播推荐';

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
        //Step1:获取用户和用户关注的活动发布者
        $users = \DB::table('user_subscription as us')
            ->join('live as l','l.id','=','us.live_id')
            ->join('activity as a','a.id','=','l.activity_id')
            ->where('us.status',1)
            ->groupBy('uid')
            ->select(\DB::raw('group_concat(lab_a.publisher_uid) as publishers,lab_us.uid'))
            ->get();
        //Step2:若活动发布者有活动发布，则给关注他的每个用户发送直播推荐
        foreach($users as $user){
            $publishers = explode(',',$user->publishers);
            $lives = \DB::table('live')
                ->join('activity','live.activity_id','=','activity.id')
                ->where('live.status',1)
                ->where('live.created_at','>',time()-24*3600) //只推送一次
                ->where('live.end_time','>',time()) //只推送一次
                ->whereIn('activity.publisher_uid',$publishers)
                ->select('live.subject','live.begin_time','live.id','live.live_url','live.list_img')
                ->limit(3)
                ->get();
            //构建消息
            $message = [];
            if(!empty($lives)){
                foreach($lives as $key=>$live)
                {
                    $message[$key]['subject']       = $live->subject;
                    $message[$key]['begin_time']    = $live->begin_time;
                    $message[$key]['id']            = $live->id;
                    $message[$key]['live_url']      = $live->live_url;
                    $message[$key]['list_img']      = $live->list_img;
                }
                //消息入库
                \DB::table('my_message')
                    ->insert([
                        'created_at'=>time(),
                        'uid'=>$user->uid,
                        'type'=>11,//直播号外
                        'content'=>serialize($message),
                        'send_time'=>time()
                    ]);
            }
        }
    }
}
