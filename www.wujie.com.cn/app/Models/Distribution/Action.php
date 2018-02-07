<?php
/**活动模型
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/5/5
 * Time: 11:10
 */
namespace App\Models\Distribution;

use App\Models\Activity\Entity as Activity;
use App\Models\Activity\Ticket;
use App\Models\CurrencyLog;
use App\Models\Guest\Relation;
use App\Models\ScoreLog;
use App\Models\Video;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use App\Models\Activity\Maker as ActivityMaker;
use \DB;
use App\Models\Vip\Entity as Vip;
use App\Models\User\Entity as User;
use App\Models\Brand\Entity as Brand;
use App\Models\Brand\Goods;
//use App\Models\Live\Log;
use App\Models\Distribution\Log as DistributionLog;
use App\Models\Share\Log as ShareLog;
use App\Models\Distribution\ActionBind;
class Action extends Model
{
    protected $dateFormat = 'U';

    protected $table = 'distribution_action';

    //黑名单
    protected $guarded = [];

    //获取某个目标的某个动作的分销信息
    public static function getDistributionByAction($content, $content_id, $action)
    {
        //如果是活动
        if ($content == 'activity') {
            $entity = \DB::table('activity')->where('id', $content_id)->first();
        }

        //如果是品牌
        if ($content == 'brand') {
            $entity = \DB::table('brand')->where('id', $content_id)->first();
        }

        //如果是直播
        if ($content == 'live') {
            $entity = \DB::table('live')->where('id', $content_id)->first();
        }

        //如果是视频
        if ($content == 'video') {
            $entity = \DB::table('video')->where('id', $content_id)->first();
        }

        //如果是新闻
        if ($content == 'news') {
            $entity = \DB::table('news')->where('id', $content_id)->first();
        }

        $binds = ActionBind::where('relation_type', $content)->where('relation_id', $content_id)->where('status', 'enable')->select('distribution_action_id')
            ->get()->toArray();

        if (isset($entity) && $entity->distribution_id > 0 && $entity->distribution_deadline>time() && count($binds)) {
            $action = \DB::table('distribution')
                ->join('distribution_action', 'distribution.id', '=', 'distribution_action.distribution_id')
                ->where('distribution.id', $entity->distribution_id)
                ->where('distribution_action.action', $action)
                ->whereIn('distribution_action.id', $binds)
                ->where('distribution_action.status', 'enable')
                ->where('distribution.status', 'enable')
                ->select('distribution_action.*')
                ->first();

            return $action;
        }

        return false;
    }

    //获取某个目标的该次分销动作是否有效
    public static function isEffective($content, $content_id, $action, $uid, $watch_long = 0)
    {
        $distribution = self::getDistributionByAction($content, $content_id, $action);
        if (!$distribution) {
            return false;
        }
        //基数限制
        //如果是分享
        if ($action == 'share') {
            //查询分享了多少次
            $share_count = \DB::table('share_log')->where('content', $content)->where('uid', $uid)
                ->where('source', 'app')->where('content_id', $content_id)->count();

            //去缓存里面取该目标的分享对该用户已经产生了多少次的奖励
            $reward_count = \Cache::get($content . $content_id . 'share_distribution' . $uid, 0);

            if ($distribution->base > 0 && floor($share_count / $distribution->base) <= $reward_count) {
                return false;
            }
        }

        //如果是转发
        if ($action == 'relay') {
            //查询转发了多少次
            $relay_count = \DB::table('share_log')->where('content', $content)->where('source_uid', $uid)
                ->where('source', '<>', 'app')->where('content_id', $content_id)->count();

            //去缓存里面取该目标的转发对该用户已经产生了多少次的奖励
            $reward_count = \Cache::get($content . $content_id . 'relay_distribution' . $uid, 0);

            if ($distribution->base > 0 && floor($relay_count / $distribution->base) <= $reward_count) {
                return false;
            }
        }

        //如果是报名
        if ($action == 'enroll') {
            //查询报名了多少次
            $enroll_count = \DB::table('activity_sign')->whereIn('status', [0, 1])->where('activity_id', $content_id)
                ->where('source_uid', $uid)->count();
            //去缓存里面取该目标的报名对该用户已经产生了多少次的奖励

            $reward_count = \Cache::get($content . $content_id . 'enroll_distribution' . $uid, 0);
            if ($distribution->base > 0 && floor($enroll_count / $distribution->base) <= $reward_count) {
                return false;
            }
        }

        //如果是签到
        if ($action == 'sign') {
            $enroll_count = \DB::table('activity_sign')->whereIn('status', [0, 1])->where('activity_id', $content_id)
                ->count();

            //去缓存里面取该目标的签到对该用户已经产生了多少次的奖励
            $reward_count = \Cache::get($content . $content_id . 'sign_distribution' . $uid, 0);
            if ($distribution->base > 0 && floor($enroll_count / $distribution->base) <= $reward_count) {
                return false;
            }
        }
        //如果是点击
        if ($action == 'view') {
            //去缓存里面取该用户对该目标已经产生了多少次的点击
            $view_count = \Cache::get($content . $content_id . 'view' . $uid, 0);

            //去缓存里面取该目标的签到对该用户已经产生了多少次的奖励
            $reward_count = \Cache::get($content . $content_id . 'view_distribution' . $uid, 0);
            if ($distribution->base > 0 && floor($view_count / $distribution->base) <= $reward_count) {
                return false;
            }
        }

        //如果是品牌留言  后台做了
        //如果是观看
        if ($action == 'watch') {
            //查询观看了多久
            if ($distribution->base > 0 && $watch_long < $distribution->base) {
                return false;
            }
        }
        //总数限制
        $total = \DB::table('distribution_log')->where('distribution_action_id', $distribution->id)
            ->where('give_type', $distribution->give)->where('relation_type', $content)
            ->where('relation_id', $content_id)
            ->sum('num');
        if ($distribution->total > 0 && $total >= $distribution->total) {
            return false;
        }
        //个人限制
        $personal_num = \DB::table('distribution_log')
            ->where('distribution_action_id', $distribution->id)
            ->where('give_type', $distribution->give)
            ->where('relation_type', $content)
            ->where('uid', $uid)
            ->where('relation_id', $content_id)
            ->sum('num');
        if ($distribution->everyone > 0 && $personal_num >= $distribution->everyone) {
            return false;
        }
        //有效转化率限制
        if ($content = 'activity' && in_array($action, ['enroll', 'sign'])) {
            //查询签到了多少次
            $sign_count = \DB::table('activity_sign')->where('status', 1)->where('activity_id', $content_id)
                ->where('source_uid', $uid)->count();
            $enroll_count = \DB::table('activity_sign')->whereIn('status', [0, 1])->where('activity_id', $content_id)
                ->where('source_uid', $uid)->count();

            //有效转化率限制
            $rate = ($sign_count / $enroll_count) * 100;

            if ($rate < $distribution->effective_rate) {
                return false;
            }
        }

        return $distribution;
    }

    //奖励入库
    public
    static function obtainReward(
        $uid, $action, $code, $remark, $trigger_uid, $content, $content_id, $genus_id, $genus_type = 'share'
    ) {
        //根据code找到原始分享的id
        if (in_array($action->action, ['share', 'relay', 'watch', 'view'])) {
            $firstShare = ShareLog::where('code', $code)
                //->where('source','<>','app')
                ->first();
            if (!$firstShare) {
                return false;
            }
            $genus_id = $relation_id = $firstShare->id;
        } else {
            $relation_id = $code;
        }

        if ($action->give == 'score') {
            //积分日志
            $log = ScoreLog::create(
                [
                    'uid'         => $uid,
                    'operation'   => 1,
                    'num'         => $action->trigger,
                    'type'        => $action->action . '_distribution',
                    'relation_id' => $content_id,
                    'relation_type' => $content,
                    'remark'      => $remark,
                    'trigger_uid' => $trigger_uid,
                ]
            );
        } else {
            $log = CurrencyLog::create(
                [
                    'uid'         => $uid,
                    'operation'   => 1,
                    'num'         => $action->trigger,
                    'action'      => $action->action . '_distribution',
                    'relation_id' => $content_id,
                    'relation_type' => $content,
                    'remark'      => $remark,
                    'trigger_uid' => $trigger_uid,
                ]
            );
        }

        //奖励入库
        User::where('uid', $uid)->increment($action->give, $action->trigger);

        //分销日志
        DistributionLog::create(
            [
                'uid'                    => $uid,
                'distribution_id'        => $action->distribution_id,
                'distribution_action_id' => $action->id,
                'give_type'              => $action->give,
                'give_id'                => $log->id,
                'relation_type'          => $content,
                'relation_id'            => $content_id,
                'num'                    => $action->trigger,
                'genus_type'             => $genus_type,
                'genus_id'               => $genus_id,
                'source_uid'             => $trigger_uid,
            ]
        );

        //写入缓存  该目标该动作对该用户的奖励次数加1
        $origin_cache = \Cache::get($content . $content_id . $action->action . '_distribution' . $uid, 0);
        \Cache::forever($content . $content_id . $action->action . '_distribution' . $uid, $origin_cache + 1);

        return true;
    }
}