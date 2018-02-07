<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Foundation\Inspiring;
use DB;
use App\Models\CityPartner\Income;
use App\Models\CityPartner\Entity;
use \Mail;


class PeriodIncome extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'period_income';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '收集超额收益、团队收入和特殊奖励';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $m = date('m');
        if ($m > 6) {
            //如果是下半年 那么算今年上半年的
            $begin = mktime(0, 0, 0, 1, 1, date('Y'));
            $start_months = array(date('Y') . '-01', date('Y') . '-02', date('Y') . '-03', date('Y') . '-04', date('Y') . '-05', date('Y') . '-06');
            $end_months = array(date('Y') . '-02', date('Y') . '-03', date('Y') . '-04', date('Y') . '-05', date('Y') . '-06', date('Y') . '-07');
            $start_month = date('Y') . '-01';
            $end_month = date('Y') . '-06';
            $end = mktime(0, 0, 0, 7, 1, date('Y'));
        } else {
            //如果是上半年 那么算去年下半年的
            $begin = mktime(0, 0, 0, 7, 1, (date('Y') - 1));
            $start_months = array((date('Y') - 1) . '-07', (date('Y') - 1) . '-08', (date('Y') - 1) . '-09', (date('Y') - 1) . '-10', (date('Y') - 1) . '-11', (date('Y') - 1) . '-12');
            $end_months = array((date('Y') - 1) . '-08', (date('Y') - 1) . '-09', (date('Y') - 1) . '-10', (date('Y') - 1) . '-11', (date('Y') - 1) . '-12', date('Y') . '-01');
            $start_month = (date('Y') - 1) . '-07';
            $end_month = (date('Y') - 1) . '-12';
            $end = mktime(0, 0, 0, 1, 1, date('Y'));
        }

        //获取上期所有的个人业绩
        $personal_lists = DB::table('city_partner_achievement')
            ->where('range', 'personal')
            ->where('arrival_at', '>', $begin)
            ->where('arrival_at', '<', $end)
            ->groupBy('partner_uid')
            ->select(DB::raw("lab_city_partner_achievement.partner_uid,
                sum(lab_city_partner_achievement.amount) as total_personal"))
            ->lists('total_personal', 'partner_uid');

        //获取上期所有的团队业绩
        $team_lists = DB::table('city_partner_achievement')
            ->where('range', 'team')
            ->where('arrival_at', '>', $begin)
            ->where('arrival_at', '<', $end)
            ->groupBy('partner_uid')
            ->select(DB::raw("lab_city_partner_achievement.partner_uid,
                sum(lab_city_partner_achievement.amount) as total_team"))
            ->lists('total_team', 'partner_uid');
        //获取总业绩
        $total_lsit = array();
        foreach ($personal_lists as $k => $v) {
            if (isset($team_lists[$k])) {
                $total = $v + $team_lists[$k];
            } else {
                $total = $v;
            }
            $total_lsit[$k] = $total;
        }

        //考虑特殊的有团队业绩没有个人业绩的情况
        foreach ($team_lists as $k => $v) {
            if (!isset($personal_lists[$k])) {
                $total_lsit[$k] = $v;
            }
        }
        $host=config('database.connections.mysql.read.host');


        DB::beginTransaction();
        try {
            //生成超额提成
            foreach ($total_lsit as $k => $v) {
                //如果有不正常的数据，发邮件，并写日志。
                if (isset($personal_lists[$k])) {
                    if ($personal_lists[$k] < 0 ) {
                        Mail::raw("{$host}超额收益脚本运行过程遇到不正常数据，该城市合伙人的uid为" . $k . '请快速查证。', function ($message) use($host) {
                            $message->to('tangjb@tyrbl.com')->subject("{$host}超额收益脚本运行过程遇到不正常数据");
                        });
                        throw new \Exception("{$host}超额收益脚本运行过程遇到不正常数据，该城市合伙人的uid为" . $k);
                    }

                    $factor = $this->getFactor($total_lsit[$k]);
                    $extra = (($personal_lists[$k]) * $factor) - ($personal_lists[$k] * 0.15);
                } else {
                    $extra = 0;
                }

                $exist = Income::where('start_month', $start_month)
                    ->where('end_month', $end_month)
                    ->where('partner_uid', $k)
                    ->where('type', 'extra')->first();
                if (is_object($exist)) {
                    Income::where('start_month', $start_month)
                        ->where('end_month', $end_month)
                        ->where('partner_uid', $k)
                        ->where('type', 'extra')
                        ->update(['amount' => $extra,'updated_at'=>time()]);
                } else {
                    Income::create([
                        'partner_uid' => $k,
                        'start_month' => $start_month,
                        'end_month' => $end_month,
                        'amount' => $extra,
                        'type' => 'extra',
                    ]);
                }

                //生成团队提成
                //如果有不正常的数据，发邮件，并写日志。
                if ($v <0) {
                    Mail::raw("{$host}团队收益脚本运行过程遇到不正常数据，该城市合伙人的uid为" . $k . '请快速查证。', function ($message) use($host) {
                        $message->to('tangjb@tyrbl.com')->subject('团队收益脚本运行过程遇到不正常数据');
                    });
                    throw new \Exception("{$host}团队收益脚本运行过程遇到不正常数据，该城市合伙人的uid为" . $k);
                }

                $trees = Entity::tree($k);
                //减去下属合伙人的提成
                $team = ($v * ($this->getFactor($v)));
                foreach ($trees as $key => $val) {
                    if (isset($total_lsit[$val])) {
                        $team = $team - ($total_lsit[$val] * ($this->getFactor($total_lsit[$val])));
                    }
                }


//            //减去自己已经获得的保底提成和超额提成
//            $base = DB::table('city_partner_income')
//                ->where('type', 'base')
//                ->whereIn('start_month', $start_months)
//                ->whereIn('end_month', $end_months)
//                ->where('partner_uid', $k)
//                ->first();
//
//            $extra = DB::table('city_partner_income')
//                ->where('type', 'extra')
//                ->whereIn('start_month', $start_months)
//                ->whereIn('end_month', $end_months)
//                ->where('partner_uid', $k)
//                ->first();
//            is_object($base) ? $base = $base->amount : $base = 0;
//            is_object($extra) ? $extra = $extra->amount : $extra = 0;

                //减去自己已经获得的保底提成和超额提成
                isset($personal_lists[$k]) ? $person = $personal_lists[$k] : $person = 0;
                $team = $team - $person * ($this->getFactor($total_lsit[$k]));

                if ($team < 0) {
                    Mail::raw("{$host}团队收益脚本运行过程遇到不正常数据，他的团队收入小于0，该城市合伙人的uid为" . $k . '请快速查证。', function ($message) use($host) {
                        $message->to('tangjb@tyrbl.com')->subject("{$host}团队收益脚本运行过程遇到不正常数据");
                    });
                    throw new \Exception("{$host}团队收益脚本运行过程遇到不正常数据，他的团队收入小于0，该城市合伙人的uid为" . $k);
                }

                $exist = Income::where('start_month', $start_month)
                    ->where('end_month', $end_month)
                    ->where('partner_uid', $k)
                    ->where('type', 'team')->first();
                if (is_object($exist)) {
                    Income::where('start_month', $start_month)
                        ->where('end_month', $end_month)
                        ->where('partner_uid', $k)
                        ->where('type', 'team')
                        ->update(['amount' => $team,'updated_at'=>time()]);
                } else {
                    Income::create([
                        'partner_uid' => $k,
                        'start_month' => $start_month,
                        'end_month' => $end_month,
                        'amount' => $team,
                        'type' => 'team',
                    ]);
                }

                //生成特殊提成
                //如果有不正常的数据，发邮件，并写日志。
                if ($v < 0) {
                    Mail::raw("{$host}特殊收益脚本运行过程遇到不正常数据，该城市合伙人的uid为" . $k . '请快速查证。', function ($message) use($host) {
                        $message->to('tangjb@tyrbl.com')->subject('特殊收益脚本运行过程遇到不正常数据');
                    });
                    throw new \Exception("{$host}特殊收益脚本运行过程遇到不正常数据，该城市合伙人的uid为" . $k);
                }
                $trees = Entity::tree($k);
                $trees_list = array();
                foreach ($trees as $key => $val) {
                    if (isset($total_lsit[$val])) {
                        $trees_list[] = $total_lsit[$val];
                    }
                }
                arsort($trees_list);
                $trees_list = array_values($trees_list);
                if ($v >= 100000000 && isset($trees_list[1]) && $trees_list[1] >= 50000000) {
                    $special = $v * 0.0145;
                } elseif ($v >= 50000000 && isset($trees_list[1]) && $trees_list[1] >= 20000000) {
                    $special = $v * 0.0135;
                } elseif ($v >= 25000000 && isset($trees_list[1]) && $trees_list[1] >= 10000000) {
                    $special = $v * 0.0120;
                } elseif ($v >= 10000000 && isset($trees_list[1]) && $trees_list[1] >= 5000000) {
                    $special = $v * 0.01;
                } else {
                    $special = 0;
                }


                $special_exist = Income::where('start_month', $start_month)
                    ->where('end_month', $end_month)
                    ->where('partner_uid', $k)
                    ->where('type', 'special')->first();

                if (is_object($special_exist)) {
                    Income::where('start_month', $start_month)
                        ->where('end_month', $end_month)
                        ->where('partner_uid', $k)
                        ->where('type', 'special')
                        ->update(['amount' => $special,'updated_at'=>time()]);
                } else {
                    Income::create([
                        'partner_uid' => $k,
                        'start_month' => $start_month,
                        'end_month' => $end_month,
                        'amount' => $special,
                        'type' => 'special',
                    ]);
                }
                !isset($extra) && $extra = 0;
                isset($personal_lists[$k]) ? $my = $personal_lists[$k] : $my = 0;
                $m_exists = DB::table('partner_message')->where('uid', $k)
                    ->where('title', $start_month . "--" . $end_month . '本期账单明细已出')->first();
                if (!is_object($m_exists)) {
                    //发消息
                    DB::table('partner_message')->insert([
                        'uid' => $k,
                        'title' => $start_month . "--" . $end_month . '本期账单明细已出',
                        'content' => "您" . $start_month . "--" . $end_month . "本期账单明细已出，本期总业绩<font color='#ff6633'>" . ($v / 10000) . "万元</font>，其中个人业绩<font color='#ff6633'>" . ($my / 10000) . "万元</font>，
                    团队业绩<font color='#ff6633'>" . (($v - $my) / 10000) . "万元</font>，您已达到提成比例<font color='#ff6633'>" . $this->getFactor($v, 1) . "</font>，该周期奖励收入总计<font color='#ff6633'>" . (($extra + $team + $special) / 10000) . "万元</font>；",
                        'created_at'=>time(),
                        'updated_at'=>time(),
                        'type'=>'periodBills',
                    ]);
                } elseif (is_object($m_exists) && $m_exists->is_read == 0) {
                    //发消息
                    DB::table('partner_message')->where('uid', $k)
                        ->where('title', $start_month . "--" . $end_month . '本期账单明细已出')
                        ->update([
                            'content' => "您" . $start_month . "--" . $end_month . "本期账单明细已出，本期总业绩<font color='#ff6633'>" . ($v / 10000) . "万元</font>，其中个人业绩<font color='#ff6633'>" . ($my / 10000) . "万元</font>，
                    团队业绩<font color='#ff6633'>" . (($v - $my) / 10000) . "万元</font>，您已达到提成比例<font color='#ff6633'>" . $this->getFactor($v, 1) . "</font>，该周期奖励收入总计<font color='#ff6633'>" . (($extra + $team + $special) / 10000) . "万元</font>；",
                    'updated_at'=>time(),
                        ]);
                }
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            //发生异常了，回滚之后 写日志 发邮件
            $file = fopen(config('app.script_log'), 'a+');
            fwrite($file, "{$host}周期提成收集脚本运行过程中出现问题，已经回滚,异常信息为" . $e->getMessage() . "出错代码行数为" . $e->getLine() . ",错误时间为" . date('Y-m-d H:i:s') . "\r\n") || die('写入失败！');;
            fclose($file);
            Mail::raw("{$host}周期提成收集脚本运行过程中出现问题，已经回滚，异常信息为" . $e->getMessage() . "出错代码行数为" . $e->getLine() . "请快速查证。", function ($message) use($host) {
                $message->to('tangjb@tyrbl.com')->subject("{$host}周期提成收集脚本运行过程中出现问题，已经回滚");
            });
        }
    }

    /**
     * 获取提成系数
     * $type 0返回小数， 1返回百分数
     */
    private function getFactor($num, $type = 0)
    {
        $type == 1 ? $factor = "15%" : $factor = 0.15;
        if ($num < 100000) {
            $type == 1 ? $factor = "15%" : $factor = 0.15;
        } elseif ($num >= 100000 && $num < 250000) {
            $type == 1 ? $factor = "18%" : $factor = 0.18;
        } elseif ($num >= 250000 && $num < 500000) {
            $type == 1 ? $factor = "21%" : $factor = 0.21;
        } elseif ($num >= 500000 && $num < 1000000) {
            $type == 1 ? $factor = "24%" : $factor = 0.24;
        } elseif ($num >= 1000000 && $num < 2500000) {
            $type == 1 ? $factor = "27%" : $factor = 0.27;
        } elseif ($num >= 2500000 && $num < 5000000) {
            $type == 1 ? $factor = "30%" : $factor = 0.3;
        } elseif ($num >= 5000000) {
            $type == 1 ? $factor = "33%" : $factor = 0.33;
        }

        return $factor;
    }
}
