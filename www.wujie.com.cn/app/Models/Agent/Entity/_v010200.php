<?php namespace App\Models\Agent\Entity;


use App\Models\Agent\AgentCategory;
use App\Models\Agent\AgentCurrencyLog;
use App\Models\Agent\Score\AgentScoreLog;
use DB;
use foo\func;
use Illuminate\Database\Eloquent\Model;
use App\Models\Agent\Agent as _v010000;


class _v010200 extends _v010000
{
    public static function add($agent_id, $num, $type, $remark = '', $relation_id = 0, $operation = 1, $unique = false, $created_at = false, $updated_at = false, $add_score=0)
    {
        if(!$agent_id){
           return false;
        }

        if ($num == 0) return false;


        $agent = self::find($agent_id);

        //如果是减去积分
        if ($operation < 1 && $agent->score < $num) {
            return false;
        }

        if (!$unique || self::typeCount($agent_id, $type) == 0) {
            DB::beginTransaction();
            try {
//            （18）进行bug反馈。（意见反馈）
//              进行bug反馈或意见提交，每次获得1分。每日最高2分。
                if (16 == $type) {
                    $res = AgentScoreLog::where('agent_id', $agent_id)->where('type', 16)->
                    whereRaw("created_at BETWEEN UNIX_TIMESTAMP(CAST(SYSDATE()AS DATE)) and UNIX_TIMESTAMP(CAST(SYSDATE()AS DATE) + INTERVAL 1 DAY)")
                        ->sum('num');

                    if ($res >= 2) return false;
                }

//             （3）学习奖励。
//              阅读资讯、观看视频，或听随声听语音等，均能获得学习奖励，给予1分奖励。
//              该奖励为首次奖励，再次阅读同一篇资讯等均不给于重复奖励。此外，最高每日5分。
                if (in_array($type, [21, 22, 26, 27, 28])) {
                    $res = AgentScoreLog::where('agent_id', $agent_id)->whereIn('type', [21, 22, 26, 27, 28])->
                    whereRaw("created_at BETWEEN UNIX_TIMESTAMP(CAST(SYSDATE()AS DATE)) and UNIX_TIMESTAMP(CAST(SYSDATE()AS DATE) + INTERVAL 1 DAY)")
                        ->sum('num');

                    if ($res >= 5) return false;

                    $exist = AgentScoreLog::where('agent_id', $agent_id)->where('type', $type)->where('relation_id', $relation_id)->first();
                    if ($exist) return false;
                }


//              （2）点赞、评分。
//              对无界商圈资讯等进行点赞或评论留言，均能获得1分奖励，最高每日5分。
                if (in_array($type, [20, 23, 24])) {
                    $res = AgentScoreLog::where('agent_id', $agent_id)->whereIn('type', [20, 23, 24])->
                    whereRaw("created_at BETWEEN UNIX_TIMESTAMP(CAST(SYSDATE()AS DATE)) and UNIX_TIMESTAMP(CAST(SYSDATE()AS DATE) + INTERVAL 1 DAY)")
                        ->sum('num');

                    if ($res >= 5) return false;
                }


                if (in_array($type, [19])) {
                    $res = AgentScoreLog::where('agent_id', $agent_id)->where('type', 19)->
                    whereRaw("created_at BETWEEN UNIX_TIMESTAMP(CAST(SYSDATE()AS DATE)) and UNIX_TIMESTAMP(CAST(SYSDATE()AS DATE) + INTERVAL 1 DAY)")
                        ->sum('num');
                    if ($res >=5) return false;
                }

                if ($operation == 1) {
                    $agent->score = $agent->score + $num;
                } else {
                    $agent->score = $agent->score - $num;
                }
                $agent->save();

                $score = $agent->score;
                $created_at == false && $created_at = time();
                $updated_at == false && $updated_at = time();
                AgentScoreLog::create(compact('agent_id', 'num', 'remark', 'type', 'operation', 'relation_id', 'score', 'created_at', 'updated_at'));
                DB::commit();

                if ($num > 0 && $add_score!=-1) {
                    if($add_score==0) $add_score =$num;
                    $rs = send_transmission(json_encode([
                        'type' => 'get_score',
                        'style' => 'json',
                        'value' => [
                            'num' => $add_score,
                        ],
                    ]), $agent, null, true);
                }

                return true;
            } catch (Exception $e) {
                DB::rollBack();
            }
        }

        return false;
    }


    //查看操作数
    public static function typeCount($agent_id, $type, $start_time = null, $end_time = null)
    {
        $count = AgentScoreLog::where('agent_id', $agent_id)
            ->where('type', $type);
        if ($start_time) {
            $count->where('created_at', '>=', strtotime($start_time));
        }
        if ($end_time) {
            $count->where('created_at', '<', strtotime($end_time));
        }

        return $count->count();
    }


    //查看一个用户是否完成验证
    public static function isComplete($agent_id)
    {
//        1、头像2、昵称 3、性别 4、年级 5、地区（包含GPS定位）6、关注行业
//        7、教育程度8、当前职业9、收入
        $agent = self::where('nickname', '<>', '')
            ->where('birth', '<>', '0000-00-00')->where('avatar', '<>', '')
            ->where('gender', '<>', '-1')
            ->where('zone_id', '<>', '')
            ->where('diploma', '<>', '')
            ->where('profession', '<>', '')
            ->where('earning', '<>', '')
            ->where('id', $agent_id)
            ->first();

        $category = AgentCategory::where('agent_id', $agent_id)->first();

        if (is_object($agent) && is_object($category)) {
            return true;
        } else {
            return false;
        }
    }


    /**
     * 添加育成奖励
     *
     * @param $id
     * @return array|string
     * @author tangjb
     */
    public static function addGrowthReward($agent_id)
    {
        //查询自己有多少个直属下级
        $agent = self::with('pAgent')->where('id',$agent_id)->where('is_verified', 1)
            ->where('status',1)->first();

        /* $count = self::where('register_invite', $agent->username)->where('is_verified', 1)
             ->whereIn('id', function($query){
                 $query->from('agent_brand')->where('status', 4)->lists('agent_id');
             })
             ->count();*/

        $count = self::where('register_invite', $agent->non_reversible)->where('is_verified', 1)->count();

        //查询自己及自己的团队完成了多少订单
        $total_achievement = AgentAchievement::where('agent_id', $agent_id)->sum('total_achievement');



        //三星主管
        //1、下级经纪人发展其自身下级达5人
        //2、下级经纪人及其团队合计完成1单
        //奖励上级2000元   内部经纪人不参与
        if ($count>=5 && $total_achievement && $agent->account_type!= self::INNER_AGENT) {
            AgentCurrencyLog::addCurrency($agent->id, 2000, 11, $agent_id, 1);
        }

        $lists = self::select('id', 'username', 'non_reversible','register_invite')->get()->toArray();
        $sons = self::sonTree($lists, $agent->non_reversible);
        $son_ids = array_pluck($sons, 'id');



        //查询自己及自己的团队有多少个三星主管
        $badge_count = self::where('badge', 1)->where(function ($query) use ($agent_id, $agent, $son_ids) {
            $query->WhereIn('id', $son_ids);
        })->count();



        //四星主管
        //1、下级经纪人发展其自身下级达10人
        //2、下级经纪人及其团队合计新完成3单
        //3、下级经纪人及其团队至少1人为三星主管
        //奖励上级4000元   内部经纪人不参与
        if ($count>=15 && $total_achievement>=3 && $badge_count>=1 &&  $agent->account_type!= self::INNER_AGENT) {
            AgentCurrencyLog::addCurrency($agent->id, 4000, 12, $agent_id, 1);
        }


        //五星主管
        //1、下级经纪人发展其自身下级达15人
        //2、下级经纪人及其团队合计新完成6单
        //3、下级经纪人及其团队至少2人为三星主管
        //奖励上级6000元      内部经纪人不参与
        if ($count>=30 && $total_achievement>=6 && $badge_count>=2 &&  $agent->account_type!= self::INNER_AGENT) {
            AgentCurrencyLog::addCurrency($agent->id, 6000, 13, $agent_id, 1);
        }

        return true;
    }


    /**
     * 获取给定时间至今的所有的月份
     *
     * @param $id
     * @return array|string
     * @author tangjb
     */
    public static function getMonths($start = '1508630400', $end_time = null)
    {
        if (!$end_time) $end_time = time();

        if ($end_time < $start) return false;

        $data = [];
        $start_year = date('Y', $start);
        $end_year = date('Y', $end_time);
        $start_month = date('m', $start);
        $end_month = date('m', $end_time);

        for ($i = $start_year; $i <= $end_year; $i++) {
            if ($i == $start_year) {
                $j = $start_month;
            } else {
                $j = '01';
            }

            if ($i == $end_year) {
                $end = $end_month;
            } else {
                $end = 12;
            }

            for ($j; $j <= $end; $j++) {
                if(!preg_match('/^0(\d)/', $j) &&$j<10){
                    $j = '0'.$j;
                }
                $data[] = "{$i}年{$j}月";
            }
        }

        return array_reverse($data);
    }






}
