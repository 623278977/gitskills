<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Vip\User as UserVip;
use App\Models\User\Entity as User;
use \DB;
class CreateMessagesOfSMSVipExpiration extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'vip:sms';

    /**
     * The console command description.   TODO：专版弃用  --数据中心 2017.12.13
     *
     * @var string
     */
    protected $description = 'To check if ths viper is out of date and send short message';

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
        //大后天
        $threeDaysLater = mktime(0,0,0,date('m'),(date('d')+2),date('y'));
        $tomorrow = mktime(0,0,0,date('m'),(date('d')+1),date('y'));
        $today = mktime(0,0,0,date('m'),(date('d')),date('y'));
        $vips = \DB::select(\DB::raw('select * from (select * from lab_user_vip order by end_time desc) as user_vip group by user_vip.uid,user_vip.vip_id'));
        foreach($vips as $vip){
            if($vip->end_time == $threeDaysLater){
                $temp = \DB::table('vip')
                    ->where('id',$vip->vip_id)
                    ->first();
                $user = \DB::table('user')
                    ->where('uid',$vip->uid)
                    ->first();
                if(empty($user) || empty($temp)){
                    continue;
                }

                SendTemplateSMS('vipExpiration',$user->username,'vipExpiration',['name'=> $temp->name],$user->nation_code);
            }
        }
        $lists = \DB::table('user_vip')
            ->groupBy('uid')
            ->groupBy('vip_id')
            ->select(\DB::raw("lab_user_vip.uid,lab_user_vip.vip_id,
                max(lab_user_vip.end_time) as max_end_time"))
//            ->having('max_end_time','>', $today)
//              ->having('max_end_time','<=', $tomorrow)
            ->havingRaw("max_end_time > {$tomorrow} and max_end_time <= {$threeDaysLater}")
            ->get();

            //专版会员即将在1天后到期
            foreach($lists as $k=>$v){
                $user =User::where('uid', $v->uid)->get();
                $vip =\DB::table('vip')->where('id', $v->vip_id)->first();
                send_notification('专版会员即将在1天后到期', "你的「{$vip->name}」即将到期，请及时续费",
                    json_encode(['type'=>'vip_term_detail', 'style'=>'id', 'value'=>$v->vip_id]),
                    $user);
            }




    }
}
