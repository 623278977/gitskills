<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Foundation\Inspiring;
use DB;
use App\Models\CityPartner\Income;
use App\Models\CityPartner\Achievement;
use \Mail;

class BaseIncome extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'base_income';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '收集保底收入';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $m = date('m');
        if (in_array($m, [2, 3, 4, 5, 6, 7])) {
            //如果是上半年
            $begin = mktime(0, 0, 0, 1, 1, date('Y'));
            $end = mktime(0, 0, 0, 7, 1, date('Y'));
        } elseif (in_array($m, [8, 9, 10, 11, 12])) {
            //如果是下半年
            $begin = mktime(0, 0, 0, 7, 1, date('Y'));
            $end = mktime(0, 0, 0, 1, 1, (date('Y') + 1));
        } else {
            //如果是一月份
            $begin = mktime(0, 0, 0, 7, 1, date('Y') - 1);
            $end = mktime(0, 0, 0, 1, 1, (date('Y')));
        }

        //获取上个月1号凌晨的时间戳
        $last_first = mktime(0, 0, 0, date('m') - 1, 1, date('Y'));
        //获取上个月最后一天凌晨的时间戳
        $last_last = mktime(0, 0, 0, date('m'), 1, date('Y'));

        $lists = DB::table('city_partner_achievement')->where('range', 'personal')->whereBetween('arrival_at', [$last_first, $last_last])
            ->where('status', 0)->get();
        $host=config('database.connections.mysql.read.host');

        //开始事务
        DB::beginTransaction();
        try {
            foreach ($lists as $k => $v) {
                //如果有不正常的数据，发邮件，并写日志。
                if ($v->amount < 0 || $v->partner_uid == 0) {
                    Mail::raw("{$host}基本收益脚本运行过程遇到不正常数据，该条条业绩的id为" . $v->id . '请快速查证。', function ($message) use($host) {
                        $message->to('tangjb@tyrbl.com')->subject("{$host}基本收益脚本运行过程遇到不正常数据");
                    });
                    throw new \Exception("{$host}基本收益脚本运行过程遇到不正常数据，该条条业绩的id为" . $v->id);
                }
                $exist = Income::where('start_month', date('Y') . '-' . str_pad((date('m') - 1), 2, 0, STR_PAD_LEFT))
                    ->where('partner_uid', $v->partner_uid)
                    ->where('end_month', date('Y') . '-' . date('m'))
                    ->where('type', 'base')
                    ->first();
                if (is_object($exist)) {
                    Income::where('start_month', date('Y') . '-' . str_pad((date('m') - 1), 2, 0, STR_PAD_LEFT))
                        ->where('partner_uid', $v->partner_uid)
                        ->where('end_month', date('Y') . '-' . date('m'))
                        ->where('type', 'base')
                        ->increment('amount', ($v->amount) * 0.15);
                } else {
                    Income::create([
                        'partner_uid' => $v->partner_uid,
                        'start_month' => date('Y') . '-' . str_pad((date('m') - 1), 2, 0, STR_PAD_LEFT),
                        'end_month' => date('Y') . '-' . date('m'),
                        'amount' => ($v->amount) * 0.15,
                        'type' => 'base',
                    ]);
                }

                Achievement::where('id', $v->id)->update(['status' => 1, 'updated_at' => time()]);
                //团队业绩
                $team = DB::table('city_partner_achievement')
                    ->where('range', 'team')
                    ->whereBetween('arrival_at', [$last_first, $last_last])
                    ->where('partner_uid', $v->partner_uid)
                    ->groupBy('partner_uid')
                    ->select(DB::raw("lab_city_partner_achievement.partner_uid,
                sum(lab_city_partner_achievement.amount) as total"))
                    ->get();
                //个人业绩
                $personal = DB::table('city_partner_achievement')
                    ->where('range', 'personal')
                    ->whereBetween('arrival_at', [$last_first, $last_last])
                    ->where('partner_uid', $v->partner_uid)
                    ->groupBy('partner_uid')
                    ->select(DB::raw("lab_city_partner_achievement.partner_uid,
                sum(lab_city_partner_achievement.amount) as total"))
                    ->get();
                //累积业绩
                $all = DB::table('city_partner_achievement')
                    ->whereBetween('arrival_at', [$begin, $end])
                    ->where('partner_uid', $v->partner_uid)
                    ->select(DB::raw("lab_city_partner_achievement.partner_uid,
                sum(lab_city_partner_achievement.amount) as total"))
                    ->get();
                $factor = $this->getFactor($all[0]->total);
                //发消息查看详情

                isset($personal[0]->total) ? $my = $personal[0]->total : $my = 0;
                isset($team[0]->total) ? $team_income = $team[0]->total : $team_income = 0;
                $m_exists = DB::table('partner_message')->where('uid', $v->partner_uid)
                    ->where('title', (date('m') - 1) . '月份账单明细已出')->first();

                $content = "您" . (date('m') - 1) . "月份账单明细已出，" . (date('m') - 1) . "月份总业绩<font color='#23a4f8'>" . (($my + $team_income) / 10000) . "万元</font>，其中个人业绩<font color='#23a4f8'>" . ($my / 10000) . "万元</font>，团队业绩<font color='#23a4f8'>" . ($team_income / 10000) . "万元</font>，本月保底提成可提取<font color='#23a4f8'>" . ($my / 10000 * 0.15) . "万元</font>；
该周期业绩累积<font color='#23a4f8'>" . ($all[0]->total / 10000) . "万元</font>,周期提成比例已达<font color='#ff6633'>" . $factor . "</font> ，周期奖励请等待周期结束；";
                if ($m == 1 || $m == 7) {
                    $content = "您" . (date('m') - 1) . "月份账单明细已出，" . (date('m') - 1) . "月份总业绩<font color='#23a4f8'>" . (($my + $team_income) / 10000) . "万元</font>，其中个人业绩<font color='#23a4f8'>" . ($my / 10000) . "万元</font>，团队业绩<font color='#23a4f8'>" . ($team_income / 10000) . "万元</font>，本月保底提成可提取<font color='#23a4f8'>" . ($my / 10000 * 0.15) . "万元</font>；
该周期业绩累积<font color='#23a4f8'>" . ($all[0]->total / 10000) . "万元</font>,周期提成比例已达<font color='#ff6633'>" . $factor . "</font> ，周期奖励，会另行发放；";
                }
                if (!is_object($m_exists)) {
                    DB::table('partner_message')->insert([
                        'uid' => $v->partner_uid,
                        'title' => (date('m') - 1) . '月份账单明细已出',
                        'content' => $content,
                        'created_at' => time(),
                        'updated_at' => time(),
                        'type' => 'monthBills',
                    ]);
                } elseif (is_object($m_exists) && $m_exists->is_read == 0) {
                    //发消息
                    DB::table('partner_message')->where('uid', $v->partner_uid)
                        ->where('title', (date('m') - 1) . '月份账单明细已出')
                        ->update([
                            'content' => $content,
                            'updated_at' => time(),
                        ]);
                }
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            //发生异常了，回滚之后 写日志 发邮件
            $file = fopen(config('app.script_log'), 'a+');
            fwrite($file, "{$host}基本收益收集脚本运行过程中出现问题，已经回滚,异常信息为" . $e->getMessage() . ",错误代码行号为" . $e->getLine() . "错误时间为" . date('Y-m-d H:i:s') . "\r\n") || die('写入失败！');;
            fclose($file);
            Mail::raw("{$host}基本收益收集脚本运行过程中出现问题，已经回滚，异常信息为" . $e->getMessage() . ",错误代码行号为" . $e->getLine() . "请快速查证。", function ($message) use($host) {
                $message->to('tangjb@tyrbl.com')->subject("{$host}基本收益收集脚本运行过程中出现问题，已经回滚");
            });
        }
        exit;
    }


    /**
     * 获取提成系数
     */
    private function getFactor($num)
    {
        $factor = '15%';
        if ($num < 100000) {
            $factor = '15%';
        } elseif ($num >= 100000 && $num < 250000) {
            $factor = '18%';
        } elseif ($num >= 250000 && $num < 500000) {
            $factor = '21%';
        } elseif ($num >= 500000 && $num < 1000000) {
            $factor = '24%';
        } elseif ($num >= 1000000 && $num < 2500000) {
            $factor = '27%';
        } elseif ($num >= 2500000 && $num < 5000000) {
            $factor = '30%';
        } elseif ($num >= 5000000) {
            $factor = '33%';
        }

        return $factor;
    }

}
