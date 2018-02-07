<?php

namespace App\Console\Commands;

use App\Models\Distribution\Action;
use Illuminate\Console\Command;
use Illuminate\Foundation\Inspiring;
use DB;
use \Mail;

class ActivityEnd extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'activity_end';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '活动结束后收集';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        //昨天凌晨
        $yes_zero = mktime(0, 0, 0, date('m', time()), (date('d', time()) - 1), date('Y', time()));
        //今天凌晨
        $today_zero = mktime(0, 0, 0, date('m', time()), (date('d', time())), date('Y', time()));

        $signs = \DB::table('activity_sign')
            ->join('activity', 'activity_sign.activity_id', '=', 'activity.id')
            ->whereIn('activity_sign.status', [0,1])
            ->where('activity_sign.source_uid', '>', 0)
            ->whereBetween('activity.end_time', [$yes_zero, $today_zero])
            ->select(
                'activity_sign.uid',
                'activity_sign.id',
                'activity_sign.status',
                'activity_sign.source_uid',
                'activity_sign.activity_id',
                'activity.distribution_id'
            )
            ->get();


        foreach ($signs as $k => $v) {
            $enroll_effective = Action::isEffective('activity', $v->activity_id, 'enroll', $v->source_uid, 0);
            $sign_effective = Action::isEffective('activity', $v->activity_id, 'sign', $v->source_uid, 0);

            //收集报名奖励
            if (is_object($enroll_effective)) {
                $this->collectEnrollReward($v,  $enroll_effective);
            }

            //收集签到奖励
            if (is_object($sign_effective)  && $v->status==1) {
                $this->collectSignReward($v, $sign_effective);
            }
        }
    }

    /**
     * 收集活动报名的奖励
     */
    public function collectEnrollReward($v,$enroll_effective)
    {
        //如果是积分
        if ($enroll_effective->give == 'score') {
            //查询是否已经奖励过
            $exists = \DB::table('score_log')->where(
                [
                    'uid'         => $v->source_uid,
                    'type'        => 'enroll_distribution',
                    'trigger_uid' => $v->uid,
                    'relation_id' => $v->activity_id
                ]
            )->first();

            if (!is_object($exists) && $enroll_effective && $v->source_uid!=$v->uid) {
                Action::obtainReward(
                    $v->source_uid,
                    $enroll_effective,
                    $v->id,
                    '活动报名分销获得奖励',
                    $v->uid,
                    'activity',
                    $v->activity_id,
                    $v->id,
                    'enroll'
                );
            }
        }

        //如果是无界币
        if ($enroll_effective->give == 'currency') {
            //查询是否已经奖励过
            $exists = \DB::table('currency_log')->where(
                [
                    'uid'         => $v->source_uid,
                    'action'      => 'enroll_distribution',
                    'trigger_uid' => $v->uid,
                    'relation_id' => $v->activity_id
                ]
            )->first();
            if (!is_object($exists) && $enroll_effective && $v->source_uid!=$v->uid) {
                Action::obtainReward(
                    $v->source_uid,
                    $enroll_effective,
                    $v->id,
                    '活动报名分销获得奖励',
                    $v->uid,
                    'activity',
                    $v->activity_id,
                    $v->id,
                    'enroll'
                );
            }
        }
    }

    /**
     * 收集活动签到产生的奖励
     */
    public function collectSignReward($v,$sign_effective)
    {
        //如果是积分
        if ($sign_effective->give == 'score') {
            //查询是否已经奖励过
            $exists = \DB::table('score_log')->where(
                [
                    'uid'         => $v->source_uid,
                    'type'        => 'sign_distribution',
                    'trigger_uid' => $v->uid,
                    'relation_id' => $v->activity_id
                ]
            )->first();

            if (!is_object($exists) && $sign_effective&& $v->source_uid!=$v->uid) {
                Action::obtainReward(
                    $v->source_uid,
                    $sign_effective,
                    $v->id,
                    '活动签到分销获得奖励',
                    $v->uid,
                    'activity',
                    $v->activity_id,
                    $v->id,
                   'sign'
                );
            }
        }

        //如果是无界币
        if ($sign_effective->give == 'currency') {
            //查询是否已经奖励过
            $exists = \DB::table('currency_log')->where(
                [
                    'uid'         => $v->source_uid,
                    'action'      => 'sign_distribution',
                    'trigger_uid' => $v->uid,
                    'relation_id' => $v->activity_id
                ]
            )->first();
            if (!is_object($exists) && $sign_effective && $v->source_uid!=$v->uid) {
                Action::obtainReward(
                    $v->source_uid,
                    $sign_effective,
                    $v->id,
                    '活动签到分销获得奖励',
                    $v->uid,
                    'activity',
                    $v->activity_id,
                    $v->id,
                    'sign'
                );
            }
        }
    }

}
