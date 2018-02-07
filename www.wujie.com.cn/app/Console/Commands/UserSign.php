<?php

/*
 * 用户签到推送相关
 */

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User\Entity as User;
use Symfony\Component\Console\Input\InputArgument;

class UserSign extends Command {

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user_sign {type}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '用户签到推送提醒';

    /**
     * 执行命令
     * 
      签到赢积分功能上线啦！
      签到功能已上线、小礼品@你并说：快来带我走吧！（针对从未签到的用户，2.6签到功能上线后 全网推；时间点:10：00

      温馨提醒！
      今天你还没有签到哦，赶快戳>>（时间点:第二天 20:00

      一般人我不告诉他！
      连续签到累积积分，会有超值礼包等你兑换 （超过2天未签到；时间点:16:00

      工作在忙也要适当休息！
      推荐：积分兑换商城&幸运大转盘希望能帮你解压~（7天未签到；时间点:20:00

      这么勤快，额外再赠送50积分！
      好腻害！已经连续签到15天了，去积分商城看看吧！（连续签到10天 时间点：连续10天签到的触发推送
     * 
     */
    public function handle() {
        switch ($this->argument('type')) {
            case 'all'://全部推送没有签到的用户
                $users = $this->notSignAllPush();
                $title = '签到赢积分功能上线啦！';
                $text = '签到功能已上线、小礼品@你并说：快来带我走吧！';
                break;
            case 'yesterday'://昨天签到了今天没有签到的
                $users = $this->notSignPush(1);
                $title = '温馨提醒！';
                $text = '今天你还没有签到哦，赶快戳>>';
                break;
            case 'two-days'://超过两天未签到的
                $users = $this->notSignPush(2);
                $title = '一般人我不告诉他！';
                $text = '连续签到累积积分，会有超值礼包等你兑换';
                break;
            case 'seven-days'://7天未签到
                $users = $this->notSignPush(7);
                $title = '工作在忙也要适当休息！';
                $text = '推荐：积分兑换商城&幸运大转盘希望能帮你解压~';
                break;
            default:
                return 0;
        }
        $this->info($users->count());
        send_notification($title, $text, json_encode(['type' => 'my_index']), $users);
    }

    //全部推送没有签到的用户
    public function notSignAllPush() {
        return User::leftJoin('score_log', function($join) {
                            $join->on('score_log.uid', '=', 'user.uid')
                            ->where('score_log.type', '=', 'user_sign');
                        })
                        ->where('user.identifier', '!=', '')
                        ->whereNull('score_log.uid')
                        ->get(['user.uid', 'user.platform', 'user.identifier']);
    }

    //几天未签到的
    public function notSignPush($day) {
        $start = strtotime(date('Y-m-d', time() - 86400 * $day));
        return User::leftJoin('score_log', 'score_log.uid', '=', 'user.uid')
                        ->where('user.identifier', '!=', '')
                        ->where('score_log.type', '=', 'user_sign')
                        ->where('score_log.created_at', '>=', $start)
                        ->groupBy('user.uid')
                        ->having('created', '<', $start + 86400)
                        ->get(['user.uid', 'user.platform', 'user.identifier', \DB::raw('max(lab_score_log.created_at) as created')]);
    }

}
