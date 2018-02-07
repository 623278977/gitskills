<?php

namespace App\Console;

use App\Models\User\Entity;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Facades\DB;

class Kernel extends ConsoleKernel
{   
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        Commands\Inspire::class,
        'App\Console\Commands\ActivityAchieve',
//        'App\Console\Commands\BusinessAchieve',
        'App\Console\Commands\BaseIncome',
//        'App\Console\Commands\ExtraIncome',
        'App\Console\Commands\PeriodIncome',
        'App\Console\Commands\LiveBegin',
        'App\Console\Commands\ActivityLiveBegin',
        'App\Console\Commands\ActivitySiteBegin',
        'App\Console\Commands\CreateMessagesOfSMSVipExpiration',
        'App\Console\Commands\CreateMessagesOfOfficialVipExpiration',
        'App\Console\Commands\CreateMessagesOfActivityPublish',
        'App\Console\Commands\CreateMessagesOfVipLiveRecommend',
        'App\Console\Commands\CreateMessagesOfOrdinaryLiveRecommend',
        'App\Console\Commands\ActivitySiteBeginPush',
        'App\Console\Commands\ActivityEnd',
        'App\Console\Commands\UserSign',
        'App\Console\Commands\Unfreeze',
        'App\Console\Commands\Agent\SendOrder',
        'App\Console\Commands\RemindLogin',
        'App\Console\Commands\RemindActivityStart',
        'App\Console\Commands\Agent\TimeNotice',        //投资人保护时间过期提醒
        'App\Console\Commands\Agent\InviteNotice',      //对投资人进行邀请函还剩两天提醒
        'App\Console\Commands\Agent\InspectNotice',     //对经纪人进行考察邀请前一天的提醒
        //'App\Console\Commands\Agent\AgentStatus',       //对经纪人停止接单超过五天进行的提醒
        'App\Console\Commands\ContractOverTime',        //合同还有两天就要过期，给c端投资人发送过期提醒
        'App\Console\Commands\Agent\TailTimeOut',       //尾款超时未支付提醒
        'App\Console\Commands\Agent\AgentCustomerPush', //经纪人投资人通知提醒
        'App\Console\Commands\AddTeamLog',
        'App\Console\Commands\Agent\CollectSignCommission',
        'App\Console\Commands\Agent\SuccessInspectNotice',    //某个经纪人的邀请投资人将进行门店考察时，进行消息提醒
       //'App\Console\Commands\Agent\SuccessContractNotice',  //某个经纪人的邀请投资人成功加盟品牌以后，进行消息提醒
        'App\Console\Commands\Agent\NoteNotice',              //用红包抵扣考察邀请函过期时间快到（5天左右）时，提示
        'App\Console\Commands\Agent\AddScoreForPreviousUser',              //用红包抵扣考察邀请函过期时间快到（5天左右）时，提示
        'App\Console\Commands\Agent\RedExpireHandle',         //经纪人给投资人发送的红包，如果在有效期时间内投资人没有领取，改变过期状态 zhaoyf
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $parameters = ['--env' => \App::environment()];
        if (\App::environment() == 'production') {//线上才走
//            收集活动所产生的业绩
//            $schedule->command('activity_achieve', $parameters)
//                ->cron('* * * * *');

//            收集保底收入
//            $schedule->command('base_income', $parameters)
//                //                        ->everyMinute();
//                ->cron('0 1 1 * *');

//            收集超额收入、团队收入和特殊奖励
//            $schedule->command('period_income', $parameters)
//                //            ->everyMinute();
//                ->cron('0 1 1 * *');


//            订阅的直播即将开始 发短信
//            $schedule->command('live_begin', $parameters)
//                ->everyMinute();


            //报名的活动即将开始（直播票） 发短信
//            $schedule->command('activity_live_begin', $parameters)
//                ->everyMinute();

            //报名的活动即将开始（直播票） 发短信
            $schedule->command('activity_site_begin', $parameters)
                ->dailyAt('10:00');

            //若当天有活动发布者发布活动，发送推荐信息
            $schedule->command('publisher:createactivity', $parameters)
//            ->everyFiveMinutes();
                ->dailyAt('23:30');

            //专版会员过期
            $schedule->command('vip:official', $parameters)
//            ->everyFiveMinutes();
                ->dailyAt('0:30');

            //普通用户直播推荐
            $schedule->command('live:ordinaryrecommend', $parameters)
//            ->everyFiveMinutes();
                ->dailyAt('23:40');

            //专版过期发送短信提醒
            $schedule->command('vip:sms', $parameters)
//            ->everyFiveMinutes();
                ->dailyAt('9:30');

            //专版用户直播推荐
            $schedule->command('live:viprecommend', $parameters)
//            ->everyMinute();
                ->dailyAt('23:50');
            //监听队列
            $schedule->command('queue:work', $parameters)
                ->everyMinute()
                ->withoutOverlapping();


            //报名的活动即将开始（现场票）推送消息
            $schedule->command('activity_site_begin_push', $parameters)
                ->cron('* * * * *');

            //活动结束
            $schedule->command('activity_end', $parameters)
                ->dailyAt('0:30');

            //昨天签到了今天没有签到的
            $schedule->command('user_sign', $parameters + ['type' => 'yesterday'])
                ->dailyAt('20:00');

            //超过两天未签到的
            $schedule->command('user_sign', $parameters + ['type' => 'two-days'])
                ->dailyAt('16:00');

            //7天未签到
            $schedule->command('user_sign', $parameters + ['type' => 'seven-days'])
                ->dailyAt('20:00');

            //7解冻团队分佣佣金
            $schedule->command('unfreeze', $parameters)
                //分  时  日  月  周   每个季度的5号的凌晨1点跑一次
//            {minute} {hour} {day-of-month} {month} {day-of-week}
//                ->cron('0 1 5 1,4,7,10 *');
                ->cron('0  3  1  *  *');   //每个月的1号的凌晨3点
        }


        //订阅的直播即将开始 发短信
        $schedule->command('live_begin', $parameters)
            ->everyMinute();


        //报名的活动即将开始（直播票） 发短信
        $schedule->command('activity_live_begin', $parameters)
            ->everyMinute();


        //派单
        $schedule->command('sendorder', $parameters)
            ->everyMinute()
            ->withoutOverlapping();

       /* //向经纪人推送投资人即将过保护期消息
        $schedule->command('Agent:timeNotice', $parameters)
            ->dailyAt('10:00')
            ->withoutOverlapping();*/

        //向投资人推送邀请函即将过期的消息
        $schedule->command('Agent:InviteNotice', $parameters)
            ->cron('0,120 10-14 * * *')
            ->withoutOverlapping();

        //向经纪人推送考察邀请快到时间的消息提示
        $schedule->command('Agent:InspectNotice', $parameters)
            ->cron('00 10 * * *')
            ->withoutOverlapping();

        /*//向经纪人推送不在线超过五天的消息提示
        $schedule->command('Agent:AgentStatus', $parameters)
            ->dailyAt('10:00')
            ->withoutOverlapping();*/

        //合同还有两天就要过期，给c端投资人发送过期提醒
        $schedule->command('contract_over_time', $parameters)
            ->dailyAt('12:00')
            ->withoutOverlapping();

        //投资人线下尾款超出支付预定时间，则进行推送提醒
        $schedule->command('tail_time_out', $parameters)
            ->dailyAt('14:00')
            ->withoutOverlapping();

        //超过5天未登录，发送登录提醒
        $schedule->command('remind_login', $parameters)
            ->dailyAt('11:45')->withoutOverlapping();

        //经纪人端活动提醒
        $schedule->command('remind_activity_start', $parameters)
            ->dailyAt('9:40')->withoutOverlapping();

        //推荐投资人消息通知
        $schedule->command('Agent:AgentCustomerPush', $parameters)
            ->everyMinute()->withoutOverlapping();


        //对user表  和  agent表  中的  邀请经纪人和邀请客户 ，对在lab_agent_develop_team_log其每一个上级都添加一条记录
        $schedule->command('add_team_log', $parameters)
            ->everyMinute()
            ->withoutOverlapping();

        //每天晚上两点收集活动邀约佣金
        $schedule->command('Agent:CollectSignCommission', $parameters)
            ->dailyAt('02:00');

        //投资人对门店进行考察时，对其邀请的经纪人进行提醒
        $schedule->command('Agent:SuccessInspectNotice', $parameters)
            ->cron('0,30 * * * *')
            ->withoutOverlapping();

        //直播快到时提示（红点提示）每天凌晨执行一次
        $schedule->command('Agent:FastBeginLiveNotice', $parameters)
            ->daily()
            ->withoutOverlapping();

        //用红包抵扣考察邀请函过期时间快到（5天左右）时，提示
        $schedule->command('Agent:NoteNotice', $parameters)
            ->daily()
            //->everyMinute()
            ->withoutOverlapping();

        //经纪人给投资人发送的红包，如果在有效期时间内投资人没有领取，改变过期状态 zhaoyf
        $schedule->command('Agent:RedExpireHandle', $parameters)
            ->everyMinute()
            ->withoutOverlapping();

//        //投资人加盟品牌以后，对其邀请的经纪人进行消息的提醒
//        $schedule->command('Agent:SuccessContractNotice', $parameters)
//            ->cron('0,120 * * * *')
//            ->withoutOverlapping();
    }
}
