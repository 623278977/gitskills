<?php
/**活动模型
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/5/5
 * Time: 11:10
 */
namespace App\Models\Distribution;

use App\Models\Video;
use Illuminate\Database\Eloquent\Model;
use \DB;
use App\Models\Activity\Sign;
use App\Models\Distribution\Log as DistributionLog;
use App\Models\Share\Log as ShareLog;

class Log extends Model
{
    protected $dateFormat = 'U';

    protected $table = 'distribution_log';

    //黑名单
    protected $guarded = [];

    public function action()
    {
        return $this->hasOne('App\Models\Distribution\Action', 'id', 'distribution_action_id');
    }

    public function sourceUser()
    {
        return $this->hasOne('App\Models\User\Entity', 'uid', 'source_uid');
    }

    /**
     * 获取对某一用户，对某一目标分享产生的成绩
     *
     * $shareLog  分享记录
     * $entity  目标数据 活动，直播，视频，品牌
     */
    public static function getAchieve($shareLog, $entity)
    {
        $distributionLog = DistributionLog::where('relation_type', $shareLog->content)
            ->where('relation_id', $shareLog->content_id)
            ->where('uid', $shareLog->uid)
            ->orderBy('id', 'desc')
            ->get();
        $arr = $distributionLog->groupBy('give_type')->toArray();

        $scoreNum = $currencyNum = 0;
        if (isset($arr['score'])) {
            $num = array_pluck($arr['score'], 'num');
            $scoreNum = array_sum($num);
        }

        if (isset($arr['currency'])) {
            $num = array_pluck($arr['currency'], 'num');
            $currencyNum = array_sum($num);
        }

        $data['score_num'] = $scoreNum;
        $data['currency_num'] = $currencyNum;

        //总分享次数
        $data['share_num'] = ShareLog::where('uid', $shareLog->uid)->where('content', $shareLog->content)
            ->where('content_id', $shareLog->content_id)->count();

        //产生有效浏览
        $data['view_num'] = DistributionLog::where('relation_type', $shareLog->content)
            ->where('relation_id', $shareLog->content_id)
            ->where('uid', $shareLog->uid)
            ->where('genus_type', 'share_view')
            ->orderBy('id', 'desc')
            ->count();
        $data['order_num'] = 0;

        if ($shareLog->content == 'activity') {
            //产生用户报名
            $data['apply_num'] = DistributionLog::where('uid', $shareLog->uid)
                ->where('relation_type', $shareLog->content)->where('relation_id', $shareLog->content_id)
                ->where('genus_type', 'enroll')->count();

            //产生用户签到
            $data['sign_num'] = DistributionLog::where('uid', $shareLog->uid)
                ->where('relation_type', $shareLog->content)->where('relation_id', $shareLog->content_id)
                ->where('genus_type', 'sign')->count();

            //有效成单
        } elseif ($shareLog->content == 'live') {
            //产生有效观看直播（仅限直播当日统计）  todo 仅限当日统计
            $data['watch_num'] = DistributionLog::where('relation_type', $shareLog->content)
                ->where('relation_id', $shareLog->content_id)
                ->where('uid', $shareLog->uid)
                ->where('genus_type', 'share_watch')
                ->orderBy('id', 'desc')
                ->count();
            //最终成单单数
        } elseif ($shareLog->content == 'video') {
            //产生有效观看视频
            $data['watch_num'] = DistributionLog::where('relation_type', $shareLog->content)
                ->where('relation_id', $shareLog->content_id)
                ->where('uid', $shareLog->uid)
                ->where('genus_type', 'share_watch')
                ->orderBy('id', 'desc')
                ->count();
            //最终成单单数
        } elseif ($shareLog->content == 'brand') {
            //最终成单单数
        } else {
            return false;
        }

        return $data;
    }

    /**
     * 分销明细
     *
     * @param $uid
     * @param $content_id
     * @param $content
     *
     */
    public static function getDetail($uid, $content_id, $content)
    {
        $logs = self::where('uid', $uid)->where('relation_id', $content_id)->where('relation_type', $content)
            ->select(DB::raw('count(1) as time_count, sum(num) as sum, give_type, genus_type'))
            ->addSelect(DB::raw('from_unixtime(created_at,"%m-%d") as day'))
            ->groupBy('day')
            ->groupBy('genus_type')
            ->get();


        $logs = $logs->sortBy('day , desc')->toArray();

        $days = array_unique(array_pluck($logs, 'day'));

        $data = [];

        $arr = ['share', 'show_relay', 'show_view', 'show_watch', 'enroll', 'sign', 'intent'];
        $func = function ($before, $after) use ($arr) {
            return (array_search($before, $arr) < array_search($after, $arr)) ? -1 : 1;
        };


        foreach ($days as $k => $v) {
            foreach($logs as $key => $val){
                if($v == $val['day']){
                    $data[$k][$val['genus_type']] = $val;
                }
            }

            uksort($data[$k], $func);
            $data[$k]['day'] = $v;
        }

        return $data;
    }

}