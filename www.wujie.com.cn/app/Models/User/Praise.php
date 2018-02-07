<?php
/**用户关注行业模型
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/5/5
 * Time: 11:10
 */
namespace App\Models\User;

use App\Models\Agent\Agent;
use App\Models\Agent\Score\AgentScoreLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Closure;
use App\Models\Agent\Entity\_v010200 as Agentv010200;

class Praise extends Model
{
    protected $table = 'user_praise';

    protected $dateFormat = 'U';

    protected $guarded = [];

    static function getActivityZan($activity_id)
    {
        $data = self::where('relation', 'activity')
            ->where('relation_id', $activity_id)
            ->where('status', '<>', 'cancel')
            ->select(
                'image',
                \DB::raw("(select avatar from lab_user as u WHERE  u.uid = lab_user_praise.uid) as avatar")
            )
            ->orderBy('created_at', 'desc')
            ->get();

        return $data ?: [];
    }


    static function baseBuilder(array $where = [] , Closure $callback = NULL)
    {
        $builder = self::query();

        if($where){
            $builder = $builder->where($where);
        }

        if($callback){
            $builder = $callback($where);
        }

        return $builder;
    }
    
    static function add($uid, $relation, $relation_id, $status = 'agree') {
        $where = compact('uid', 'relation', 'relation_id');
        $res = Praise::baseBuilder($where)->first();
        if ($res) {
            if ($res->status == $status) {
                return '已经' . ($status == 'agree' ? '点赞' : '取消') . '过';
            }
            $res->update(compact('status'));
        } else {
            if ($status == 'cancel') {
                return '取消失败';
            }
            Praise::create($where + [
                'image' => Entity::find($uid, ['avatar'])->avatar ? : ''
            ]);
        }
        return Praise::where(compact('relation', 'relation_id'), function ($builder) {
                    return $builder->where('status', '<>', 'cancel');
                })->count();
    }

    /**
     * author zhaoyf
     *
     * 经纪人资讯点赞
     *
     * @param $id
     * @param string $relation
     * @return mixed
     */
    public static function agentAdds($uid, $relation, $relation_id, $status = 'agree')
    {
        $where = compact('uid', 'relation', 'relation_id');

        //添加经纪人类型
        $where['type'] = 'agent';

        $result = self::where($where)->first();

        if ($result) {
            if ($result->status == $status) {
                return '已经' . ($status == 'agree' ? '点赞' : '取消') . '过';
            }
            $result->update(compact('status'));
        } else {
            if ($status == 'cancel') return '取消失败';

            $where['image'] = Agent::find($uid, ['avatar'])->avatar ?: '';

            //添加点赞数据
            self::create($where);
        }


        if($relation=='news'){
            //给积分
            Agentv010200::add($uid, AgentScoreLog::$TYPES_SCORE[20], 20, '对资讯点赞', $relation_id);
        }

        if($relation=='we_chat'){
            //给积分
            Agentv010200::add($uid, AgentScoreLog::$TYPES_SCORE[25], 25, '对微信营销点赞', $relation_id);
        }



        //根据条件获取点赞总个数
        return self::where([
            'relation'    => $where['relation'],
            'relation_id' => $where['relation_id'],
            'type'        => $where['type'],
        ])->where('status', '<>', 'cancel')->count();
    }

    //获取指定id下类型的点赞数
    static function ZanCount($id,$relation = 'news'){
        $count = self::where('relation', $relation)
            ->where('relation_id', $id)
            ->where('status', '!=', 'cancel')
            ->count();
        return $count;

    }

}