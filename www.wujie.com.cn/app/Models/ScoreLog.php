<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use App\Models\User\Entity;
use Exception;

class ScoreLog extends Model
{
    //

    protected $table = 'score_log';
    protected $guarded = ['id'];

    protected function getDateFormat()
    {
        return date(time());
    }

    static function getRows($where, $page = 0, $pageSize = 10)
    {
        return self::where($where)->orderBy('created_at', 'desc')->skip($page * $pageSize)->take($pageSize)->get();
    }

    static function getType($type)
    {
        switch ($type) {
            case 'register':
                return '完成注册';
            case 'picture_upload':
                return '完成头像上传';
            case 'business_card':
                return '完成名片上传';
            case 'activity_sign_first':
                return '首次活动签到';
            case 'activity_sign_10':
                return '累计活动签到达到10场';
            case 'activity_sign':
                return '活动签到';
            case 'opportunity_abutment_first':
                return '首次对接商机';
            case 'comment_first':
                return '首次评论';
            case 'first_publish_opp_success':
                return '首次发布商机成功';
            case 'nopay_order_return':
                return '订单超时未支付返还积分';
            case 'ticket_buy':
                return '活动报名使用积分';
            case 'live_video_buy':
                return '视频或直播购买使用积分';
            case 'vip_term_buy':
                return '专版会员购买使用积分';
            case 'brand_goods_buy':
                return '品牌商品购买使用积分';
            case 'video_reward':
                return '视频打赏使用积分';
            case 'live_reward':
                return '直播打赏使用积分';
            case 'video_buy':
                return '视频购买使用积分';
            case 'user_sign':
                return '签到领取积分';
            case 'duiba_pay':
                return '积分兑换';
            case 'share_distribution':
                return '分享分销奖励';
            case 'relay_distribution':
                return '转发分销奖励';
            case 'watch_distribution':
                return '直播或者视频观看分销奖励';
            case 'enroll_distribution':
                return '活动报名分销奖励';
            case 'sign_distribution':
                return '活动签到分销奖励';
            case 'view_distribution':
                return '阅读分销奖励';
            case 'intent_distribution':
                return '品牌意向加盟分销奖励';
            case 'first_sign_success':
                return '首次成功报名活动';
            case 'sign_success':
                return '成功报名活动';
            case 'user_sign_reward':
                return '连续签到额外奖励';
            case 'user_sign_first':
                return '首次签到成功';
            case 'duiba_return':
                return '兑吧退回积分';
            case 'news_buy':
                return '资讯购买使用积分';
            case 'apply_activity':
                return '申请举办活动';
            case 'invite_register':
                return '邀请用户注册';
            case 'comment_agent':
                return '评价经纪人后，领取积分';
            case 'new_year_lottery':
                return '春节活动抽奖使用积分';
        }

        return '其它';
//		if($type=="dinge"){
//			return '定额发放';
//		}
//		return '日签到';
    }

    static function getBase($scorelog)
    {
        if (!isset($scorelog->id)) {
            return array();
        }
        $data = array();
        $data['remark'] = $scorelog->remark;
        $data['num'] = (($scorelog->operation == 1) ? '+' : '-') . $scorelog->num;
        $data['created_at'] = timeDiff(strtotime($scorelog->created_at));
        $data['type'] = self::getType($scorelog->type);

        return $data;
    }

    //写入数据
    public static function add($uid, $num, $type, $remark = '', $operation = 1, $unique = false, $relation_type = 'none', $relation_id = 0)
    {
        if ($num == 0) {
            return false;
        }

        $user = Entity::where('uid', $uid)->first();


        //如果是减去积分
        if($operation<1 && $user->score<$num){
            return false;
        }


        if (!$unique || self::typeCount($uid, $type) == 0) {
            $reuls = self::create(compact('uid', 'num', 'remark', 'type', 'operation', 'relation_type', 'relation_id'));
            if ($operation == 1) {
                return $reuls && Entity::where('uid', '=', $uid)->increment('score', $reuls->num);
            } else {
                return $reuls && Entity::where('uid', $uid)->where('score', '>=', $reuls->num)->decrement('score', $reuls->num);
            }
        }


        return false;
    }

    //查看操作数
    public static function typeCount($uid, $type, $start_time = null, $end_time = null)
    {
        $count = ScoreLog::where('uid', $uid)
            ->where('type', $type);
        if ($start_time) {
            $count->where('created_at', '>=', strtotime($start_time));
        }
        if ($end_time) {
            $count->where('created_at', '<', strtotime($end_time));
        }

        return $count->count();
    }

}
