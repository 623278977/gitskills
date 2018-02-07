<?php namespace App\Console\Commands\Agent;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class RedExpireHandle extends Command
{
    protected $signature = 'Agent:RedExpireHandle';


    public function handle()
    {
        //获取经纪人发送过的红包记录数据信息
         $builder     = DB::table('agent_send_red_record');
         $gain_result = $builder->orderBy('id', 'desc')->get();

        //对结果进行处理
        if ($gain_result) {
            foreach ($gain_result as $key => $vls) {
                if ($vls->expire_at < time() && $vls->is_get == 0) {
                    $builder->where('id', $vls->id)->update([
                        'expire_status' => 1,
                    ]);
                }
            }
        }
    }
}