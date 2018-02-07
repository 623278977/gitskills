<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Foundation\Inspiring;
use DB;
use \Mail;

class BusinessAchieve extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'business_achieve';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '收集业务所产生的业绩';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle($id)
    {
        $payment =  DB::table('business_payment')->where('id', $id)->where('is_collect',0)->first();
        if(!is_object($payment)){
            return false;
        }
        $business = DB::table('business')->where('id', $payment->business_id)->first();
        $arr = DB::table('city_partner')->get();
        $trees = familyTree($arr, $business->partner_uid);
        $host=config('database.connections.mysql.read.host');

        //开始事务
        DB::beginTransaction();
        try {
            foreach ($trees as $k => $v) {
                $business = DB::table('business')->where('id', $payment->business_id)->first();
                if ($k == 0) {
                    $exsits = DB::table('city_partner_achievement')->where('source', 'business')->where('partner_uid', $v)
                        ->where('source_id', $payment->business_id)->where('range', 'personal')->where('arrival_at', $payment->pay_at)->first();
                    if (is_object($exsits)) {
                        DB::table('city_partner_achievement')->where('source', 'business')
                            ->where('source_id', $payment->business_id)->where('range', 'personal')
                            ->increment('amount', ($payment->amount) * ($business->ratio));
                    } else {
                        $data['amount'] = ($payment->amount) * ($business->ratio);
                        $data['partner_uid'] = $v;
                        $data['source_id'] = $payment->business_id;
                        $data['source'] = 'business';
                        $data['range'] = 'personal';
                        $data['title'] = $business->contract_name;
                        $data['status'] = 0;
                        $data['arrival_at'] = $payment->pay_at;
                        $data['created_at'] = time();
                        $data['updated_at'] = time();
                        if (isset($trees[$k + 1])) {
                            $data['p_uid'] = $trees[$k + 1];
                        } else {
                            $data['p_uid'] = 0;
                        }
                        DB::table('city_partner_achievement')->insert($data);
                    }
                } else {
                    $team_exsits = DB::table('city_partner_achievement')->where('source', 'business')->where('partner_uid', $v)
                        ->where('source_id', $payment->business_id)->where('arrival_at', $payment->pay_at)->where('range', 'team')->first();
                    if (is_object($team_exsits)) {
                        DB::table('city_partner_achievement')->where('source', 'business')->where('partner_uid', $v)
                            ->where('source_id', $payment->business_id)->where('range', 'team')
                            ->increment('amount', ($payment->amount) * ($business->ratio));
                    } else {
                        $data['amount'] = ($payment->amount) * ($business->ratio);
                        $data['partner_uid'] = $v;
                        $data['source_id'] = $payment->business_id;
                        $data['source'] = 'business';
                        $data['range'] = 'team';
                        $data['title'] = $business->contract_name;
                        $data['status'] = 0;
                        $data['arrival_at'] = $payment->pay_at;
                        $data['created_at'] = time();
                        $data['updated_at'] = time();
                        if (isset($trees[$k + 1])) {
                            $data['p_uid'] = $trees[$k + 1];
                        } else {
                            $data['p_uid'] = 0;
                        }
                        DB::table('city_partner_achievement')->insert($data);
                    }
                }
            }
            DB::table('business_payment')->where('id', $id)->update(['is_collect' => 1]);
            DB::commit();

        }catch(\Exception $e){
            DB::rollBack();
            //发生异常了，回滚之后 写日志 发邮件
            $file = fopen(config('app.script_log'),'a+');
            fwrite($file,"{$host}业务业绩收集脚本运行过程中出现问题，已经回滚,异常信息为".$e->getMessage().",错误时间为".date('Y-m-d H:i:s')."\r\n")|| die('写入失败！');;
            fclose($file);
            Mail::raw("{$host}业务业绩收集脚本运行过程中出现问题，已经回滚，异常信息为".$e->getMessage().'请快速查证。', function ($message) use($host) {
                $message->to('tangjb@tyrbl.com')->subject("{$host}业务业绩收集脚本运行过程中出现问题，已经回滚");
            });
        }
    }

}
