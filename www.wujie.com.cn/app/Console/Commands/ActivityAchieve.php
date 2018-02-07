<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Foundation\Inspiring;
use DB;
use \Mail;

class ActivityAchieve extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'activity_achieve';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '收集活动所产生的业绩';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        //获取到期的到期的官方活动
        $activity_ids = DB::table('activity')->where('end_time', '<', time())->where('partner_uid', 0)
            ->lists('id');

//        $lists = DB::table('user_ticket')
//            ->leftJoin('order', 'user_ticket.order_id', '=', 'order.id')
//            ->leftJoin('maker_member', 'maker_member.uid', '=', 'user_ticket.uid')
//            ->leftJoin('maker', 'maker_member.maker_id','=', 'maker.id')
//            ->leftJoin('city_partner', 'city_partner.uid', '=', 'maker.partner_uid')
//            ->leftJoin('activity', 'activity.id', '=', 'user_ticket.activity_id')
//            ->where('user_ticket.status', 1)
//            ->where('user_ticket.is_collect', 0)
//            ->where('user_ticket.type', 1)//现场票才收集，直播票不收集
//            ->whereIn('user_ticket.activity_id', $activity_ids)
//            ->select('user_ticket.price as cost', 'user_ticket.activity_id', 'user_ticket.maker_id','user_ticket.id',
//                'maker.partner_uid','activity.subject','city_partner.p_uid','activity.factor')
//            ->get();

        $lists = DB::table('user_ticket')
            ->leftJoin('order', 'user_ticket.order_id', '=', 'order.id')
            ->leftJoin('activity', 'activity.id', '=', 'user_ticket.activity_id')
            ->leftJoin('maker', 'maker.id', '=', 'user_ticket.maker_id')
            ->leftJoin('city_partner', 'city_partner.uid', '=', 'maker.partner_uid')
            ->where('user_ticket.status', 1)
            ->where('user_ticket.type', 1)//现场票才收集，直播票不收集
            ->where('user_ticket.is_collect', 0)
            ->where('user_ticket.maker_id', '>', 0)
            ->whereIn('user_ticket.activity_id', $activity_ids)
            ->select('user_ticket.price as cost', 'user_ticket.maker_id', 'user_ticket.activity_id', 'user_ticket.id', 'user_ticket.updated_at',
                'maker.partner_uid', 'activity.subject', 'city_partner.p_uid', 'activity.factor', 'activity.end_time')
            ->get();

        $arr = DB::table('city_partner')->get();
        $a_ids = array();
        $host=config('database.connections.mysql.read.host');

        //开始事务
        DB::beginTransaction();
        try {
            foreach ($lists as $k => $v) {
                //如果有不正常的数据，发邮件，并写日志。
                if (!isset($v->activity_id) || $v->activity_id == 0 || !isset($v->factor) || !isset($v->partner_uid)
                    || !isset($v->p_uid) || !isset($v->cost)
                ) {
                    Mail::raw("{$host}活动业绩收集脚本运行过程中遇到不正常的数据，该条票卷的id为" . $v->id . '请快速查证。', function ($message) use($host) {
                        $message->to('tangjb@tyrbl.com')->subject("{$host}活动业绩收集脚本运行过程中遇到不正常的数据");
                    });
                    throw new \Exception("{$host}活动业绩收集脚本运行过程中遇到不正常的数据，该条票卷的id为" . $v->id);
                }
                $a_ids[] = $v->activity_id;
                $exsits = DB::table('city_partner_achievement')->where('source', 'activity')->where('partner_uid', $v->partner_uid)
                    ->where('source_id', $v->activity_id)->where('range', 'personal')->first();
                //个人业绩
                if (is_object($exsits)) {
                    DB::table('city_partner_achievement')->where('source', 'activity')
                        ->where('source_id', $v->activity_id)->where('range', 'personal')->where('partner_uid', $v->partner_uid)
                        ->increment('amount', ($v->cost) * ($v->factor));
                } else {
                    DB::table('city_partner_achievement')->insert([
                        'amount' => ($v->cost) * ($v->factor),
                        'partner_uid' => $v->partner_uid,
                        'source' => 'activity',
                        'source_id' => $v->activity_id,
                        'range' => 'personal',
                        'title' => $v->subject,
                        'p_uid' => $v->p_uid,
                        'status' => 0,
                        'arrival_at' => $v->end_time,
                        'created_at' => time(),
                        'updated_at' => time(),
                    ]);
                }
                //上级的团队业绩
                $trees = $this->familyTree($arr, $v->p_uid);
                if (!is_array($trees)) {
                    Mail::raw("{$host}活动业绩收集脚本运行过程中遇到不正常的数据，城市合伙人" . $v->uid . '的家谱树不是数组请快速查证。', function ($message) use($host) {
                        $message->to('tangjb@tyrbl.com')->subject("{$host}活动业绩收集脚本运行过程中遇到不正常的数据");
                    });
                    throw new \Exception("{$host}活动业绩收集脚本运行过程中遇到不正常的数据，城市合伙人" . $v->uid);
                }
                foreach ($trees as $key => $val) {
                    $team_exsits = DB::table('city_partner_achievement')->where('source', 'activity')->where('partner_uid', $val)
                        ->where('source_id', $v->activity_id)->where('range', 'team')->first();

                    if (is_object($team_exsits)) {
                        DB::table('city_partner_achievement')->where('source', 'activity')
                            ->where('source_id', $v->activity_id)->where('range', 'team')->increment('amount', ($v->cost) * ($v->factor));
                    } else {
                        $data['amount'] = ($v->cost) * ($v->factor);
                        $data['partner_uid'] = $val;
                        $data['source_id'] = $v->activity_id;
                        $data['source'] = 'activity';
                        $data['range'] = 'team';
                        $data['title'] = $v->subject;
                        $data['status'] = 0;
                        $data['arrival_at'] = $v->end_time;
                        $data['created_at'] = time();
                        $data['updated_at'] = time();
                        if (isset($trees[$key + 1])) {
                            $data['p_uid'] = $trees[$key + 1];
                        } else {
                            $data['p_uid'] = 0;
                        }
                        DB::table('city_partner_achievement')->insert($data);
                    }
                }
                DB::table('user_ticket')->where('id', $v->id)->update(['is_collect' => 1, 'updated_at' => time(),
                ]);
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            //发生异常了，回滚之后 写日志 发邮件
            $file = fopen(config('app.script_log'), 'a+');
            fwrite($file, "{$host}活动业绩收集脚本运行过程中出现问题，已经回滚,异常信息为" . $e->getMessage() . ",错误时间为" . date('Y-m-d H:i:s') . "\r\n") || die('写入失败！');;
            fclose($file);
            Mail::raw("{$host}活动业绩收集脚本运行过程中出现问题，已经回滚，异常信息为" . $e->getMessage() . '请快速查证。', function ($message) use($host) {
                $message->to('tangjb@tyrbl.com')->subject("{$host}活动业绩收集脚本运行过程中出现问题，已经回滚");
            });
        }
    }


    private function familyTree($arr, $upid)
    {
        $trees = [];
        foreach ($arr as $k => $v) {

            if (is_object($v)) {
                $id = $v->uid;
                $vupid = $v->p_uid;
            } elseif (is_array($v)) {
                $id = $v['uid'];
                $vupid = $v['p_uid'];
            } else {
                return false;
            }

            if ($id == $vupid) {
                return false;
            }

            if ($id == $upid) {
                $trees[] = $v->uid;
                $trees = array_merge($trees, $this->familyTree($arr, $vupid));
            }
        }
        return $trees;
    }

}
