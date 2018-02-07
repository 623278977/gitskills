<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Foundation\Inspiring;
use DB;
use App\Models\CityPartner\Income;
use \Mail;

class ExtraIncome extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'extra_income';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '收集超额收入';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $m = date('m');
//        dd((date('Y')+1));exit;
        if ($m < 7) {
            //如果是上半年,一月一号
            $begin = mktime(0, 0, 0, 1, 1, date('Y'));
            $start_months = array(date('Y') . '-01', date('Y') . '-02', date('Y') . '-03', date('Y') . '-04', date('Y') . '-05', date('Y') . '-06');
            $end_months = array(date('Y') . '-02', date('Y') . '-03', date('Y') . '-04', date('Y') . '-05', date('Y') . '-06', date('Y') . '-07');
            $start_month = date('Y') . '-01';
            $end_month = date('Y') . '-06';
            $end = mktime(0, 0, 0, 7, 1, date('Y'));
        } else {
            //如果是下半年，七月一号
            $begin = mktime(0, 0, 0, 7, 1, date('Y'));
            $start_months = array(date('Y') . '-07', date('Y') . '-08', date('Y') . '-09', date('Y') . '-10', date('Y') . '-11', date('Y') . '-12');
            $end_months = array(date('Y') . '-08', date('Y') . '-09', date('Y') . '-10', date('Y') . '-11', date('Y') . '-12', (date('Y') + 1) . '-01');
            $start_month = date('Y') . '-07';
            $end_month = date('Y') . '-12';
            $end = mktime(0, 0, 0, 1, 1, (date('Y') + 1));
        }

        //获取本期所有的个人业绩
        $lists = DB::table('city_partner_achievement')
            ->where('range', 'personal')
            ->where('created_at', '>', $begin)
            ->where('created_at', '<', $end)
            ->groupBy('partner_uid')
            ->select(DB::raw("lab_city_partner_achievement.partner_uid,
                sum(lab_city_partner_achievement.amount) as total_amount"))
            ->get();

//        $base = DB::table('city_partner_income')
//            ->where('type', 'base')
//            ->whereIn('start_month', $start_months)
//            ->whereIn('end_month', $end_months)
//            ->groupBy('partner_uid')
//            ->select(DB::raw("lab_city_partner_income.partner_uid,
//                sum(lab_city_partner_income.amount) as total_income"
//            ))->get();
//        print_r($lists);
//        echo '<br>';
//        print_r($base);exit;
        //开始事务

        DB::beginTransaction();
        try {
            foreach ($lists as $k => $v) {
                //如果有不正常的数据，发邮件，并写日志。
                if ($v->total_amount < 0 || $v->partner_uid == 0) {
                    $file = fopen(config('app.script_log'), 'a+');
                    fwrite($file, "超额收益脚本运行过程遇到不正常数据，该城市合伙人的uid为" . $v->partner_uid . "\r\n") || die('写入失败!');
                    fclose($file);
                    Mail::raw('超额收益脚本运行过程遇到不正常数据，该城市合伙人的uid为' . $v->partner_uid . '请快速查证。', function ($message) {
                        $message->to('tangjb@tyrbl.com')->subject('超额收益脚本运行过程遇到不正常数据');
                    });
                    continue;
                }

                $factor = $this->getFactor($v->total_amount);
                $extra = (($v->total_amount) * $factor) - ($v->total_amount * 0.15);
                $exist = Income::where('start_month', $start_month)
                    ->where('end_month', $end_month)
                    ->where('partner_uid', $v->partner_uid)
                    ->where('type', 'extra')->first();
                if (is_object($exist)) {
                    Income::where('start_month', $start_month)
                        ->where('end_month', $end_month)
                        ->where('partner_uid', $v->partner_uid)
                        ->where('type', 'extra')
                        ->update(['amount' => $extra]);
                } else {
                    Income::create([
                        'partner_uid' => $v->partner_uid,
                        'start_month' => $start_month,
                        'end_month' => $end_month,
                        'amount' => $extra,
                        'type' => 'extra',
                    ]);
                }
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            //发生异常了，回滚之后 写日志 发邮件
            $file = fopen(config('app.script_log'), 'a+');
            fwrite($file, "超额业绩收集脚本运行过程中出现问题，已经回滚,异常信息为" . $e->getMessage() . ",错误时间为" . date('Y-m-d H:i:s') . "\r\n") || die('写入失败！');;
            fclose($file);
            Mail::raw('超额业绩收集脚本运行过程中出现问题，已经回滚，异常信息为' . $e->getMessage() . '请快速查证。', function ($message) {
                $message->to('tangjb@tyrbl.com')->subject('超额业绩收集脚本运行过程中出现问题，已经回滚');
            });
        }
    }




    /**
     * 获取提成系数
     */
    private function getFactor($num)
    {
        $factor = 0.15;
        if ($num < 100000) {
            $factor = 0.15;
        } elseif ($num >= 100000 && $num < 250000) {
            $factor = 0.18;
        } elseif ($num >= 250000 && $num < 500000) {
            $factor = 0.21;
        } elseif ($num >= 500000 && $num < 1000000) {
            $factor = 0.24;
        } elseif ($num >= 1000000 && $num < 2500000) {
            $factor = 0.27;
        } elseif ($num >= 2500000 && $num < 5000000) {
            $factor = 0.3;
        } elseif ($num >= 5000000) {
            $factor = 0.33;
        }

        return $factor;
    }
}
