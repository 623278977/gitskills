<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use DB;
class CreateMessagesOfOfficialVipExpiration extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'vip:official';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '用户专版会员还有5天过期发送消息';

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
        //5天后的凌晨
        $sixDaysLater = mktime(0,0,0,date('m'),(date('d')+5),date('y'));

        $expirations = \DB::select(\DB::raw('select * from (select * from lab_user_vip order by end_time desc) as user_vip group by user_vip.uid,user_vip.vip_id'));
        foreach($expirations as $ep){
            if($ep->end_time == $sixDaysLater ){
                $expiration = \DB::table('user_vip')
                    ->join('user','user_vip.uid', '=', 'user.uid')
                    ->join('vip', 'user_vip.vip_id', '=','vip.id')
                    ->select(\DB::raw("lab_vip.name,lab_vip.id,lab_user.username,lab_user_vip.uid,max(lab_user_vip.end_time) as max_end_time"))
                    ->where('user_vip.id',$ep->id)
                    ->first();
                if(!empty($expiration->uid)){
                    DB::table('my_message')
                        ->insert([
                            'uid'=>$expiration->uid,
                            'content'=>serialize($expiration),
                            'type'=>8,
                            'post_id'=>$expiration->id,
                            'created_at'=>time(),
                            'updated_at'=>time(),
                            'send_time'=>time()
                        ]);
                }
            }
        }
    }
}
