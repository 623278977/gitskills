<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class CreateMessagesOfVipLiveRecommend extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'live:viprecommend';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '有新直播发布的话,创建直播推荐消息,脚本每天跑一次';

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
        //如果用户购买了专版那么专版下有直播发布则推荐给用户

        //Step1:查找出购买了专版且专版未过期的的用户
        $users = \DB::table('user_vip')
            ->where('end_time','>',time())
            ->groupBy('uid') //购买多个专版的情况
            ->select(\DB::raw('group_concat(vip_id) as vip_ids,uid'))
            ->get();
        //Step2:如果当天有专版直播发布，则给对应专版用户推荐改直播
        foreach($users as $user){
            $vip_ids = explode(',',$user->vip_ids);
            $lives = \DB::table('live')
                ->join('vip','live.vip_id','=','vip.id')
                ->whereIn('vip_id',$vip_ids)
                ->where('live.created_at','>',time()-24*3600) //只推送一次
                ->where('live.end_time','>',time()) //只推送一次
                ->limit(3)
                ->orderBy('begin_time','asc')
                ->select('live.id','live.vip_id','live.subject','live.live_url','live.list_img','live.begin_time','vip.name as vip_name')
                ->get();
            //Step3:存在直播则给用户发送推荐消息
            $data = [];
            if(! empty($lives)){
                //有直播 推荐
                $latest_time = 0;
                foreach($lives as $key=>$live){
                    if($key==0){
                        $latest_time = $live->begin_time;
                    }
                    else{
                        $latest_time = $latest_time > $live->begin_time ? $live->begin_time : $latest_time;
                    }
                    $data['content'][$key]['subject']       = $live->subject;
                    $data['content'][$key]['live_url']      = $live->live_url;
                    $data['content'][$key]['list_img']      = $live->list_img;
                    $data['content'][$key]['begin_time']    = $live->begin_time;
                    $data['content'][$key]['vip_id']        = $live->vip_id;
                    $data['content'][$key]['live_id']       = $live->id;
                    $vip_name                               = $live->vip_name;
                }
                //直播前一小时发送消息
//                $data['type']       = 2;
//                $data['vip_name']   = $vip_name;
//                \DB::table('my_message')
//                    ->insert([
//                        'created_at'=>time(),
//                        'uid'=>$user->uid,
//                        'type'=>9,//直播号外
//                        'content'=>serialize($data),
//                        'send_time'=>$latest_time-3600
//                    ]);
                //直播推荐
                $data['type'] = 1;
                $data['vip_name']   = $vip_name;
                //插入消息
                \DB::table('my_message')
                    ->insert([
                        'created_at'=>time(),
                        'uid'=>$user->uid,
                        'type'=>9,//直播号外
                        'content'=>serialize($data),
                        'send_time'=>time()
                    ]);
            }
        }
    }
}
