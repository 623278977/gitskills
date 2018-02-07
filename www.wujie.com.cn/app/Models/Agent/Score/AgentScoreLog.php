<?php

namespace App\Models\Agent\Score;

use App\Models\Agent\Agent;
use Illuminate\Database\Eloquent\Model;
use DB;

class AgentScoreLog extends Model
{
    protected $table = 'agent_score_log';
    protected $dateFormat = 'U';
    protected $guarded = [];


    //类型
    //'积分进出来源,1.每日打卡  2.报名OVO活动 3.参加OVO活动（完成签到）4.发送活动邀请函
    // 5.发放考察邀请函 6.发放加盟合同 7.获得投资人手机号 8.成功邀请投资人参加活动
    // 9.成功邀请投资人考察品牌 10.成功邀请投资人加盟品牌 11.接受派单咨询任务12.邀请投资人 13发展团队
    // 14.完成品牌学习 15 获得品牌代理 16.进行bug反馈  17.完善个人资料 18.实名认证',
    public static $TYPES = [
        '1' => '每日打卡',
        '2' => '报名OVO活动',
        '3' => '参加OVO活动',
        '4' => '发送活动邀请函',
        '5' => '发送考察邀请函',
        '6' => '发送加盟合同',
        '7' => '获得投资人手机号',
        '8' => '成功邀请投资人参加活动',
        '9' => '成功邀请投资人考察品牌',
        '10' => '成功邀请投资人加盟品牌',
        '11' => '接受派单咨询任务',
        '12' => '邀请投资人',
        '13' => '发展团队',
        '14' => '完成品牌学习',
        '15' => '获得品牌代理',
        '16' => '进行bug反馈',
        '17' => '完善个人资料',
        '18' => '实名认证',
        '19' => '分享资讯、视频等',
        '20' => '点赞',
        '21' => '学习视频',
        '22' => '学习资讯',
        '23' => '对资讯留言',
        '24' => '对视频课程留言',
        '25' => '对微信营销点赞',
        '26' => '学习商圈热文或专栏资讯',
        '27' => '学习视频课堂或专栏视频',
        '28' => '学习话术随身听',
    ];


    public static $DESCRI = [
        '1' => '签到',
        '2' => '报名活动',
        '3' => '完成签到',
        '4' => '邀请',
        '5' => '邀请',
        '6' => '合同',
        '7' => '获得手机号',
        '8' => '邀请成功',
        '9' => '邀请成功',
        '10' => '加盟成功',
        '11' => '派单咨询',
        '12' => '邀请投资人',
        '13' => '邀请经纪人',
        '14' => '品牌学习',
        '15' => '获得代理',
        '16' => '意见反馈',
        '17' => '完善资料',
        '18' => '实名认证',
        '19' => '分享',
        '20' => '点赞',
        '21' => '学习',
        '22' => '学习',
        '23' => '留言',
        '24' => '留言',
        '25' => '点赞',
        '26' => '学习',
        '27' => '学习',
        '28' => '学习',
    ];

    //部分类型对应积分（固定积分的类型）
    public static $TYPES_SCORE =[
        '2' => 1,
        '3' => 20,
        '4' => 1,
        '5' => 2,
        '6' => 2,
        '7' => 5,
        '8' => 5,
        '9' => 20,
        '10' => 50,
        '11' => 2,
        '12' => 2,
        '13' => 3,
        '14' => 1,
        '15' => 5,
        '16' => 1,
        '17' => 2,
        '18' => 5,
        '19' => 1,
        '20' => 1,
        '21' => 1,
        '22' => 1,
        '23' => 1,
        '24' => 1,
        '25' => 1,
        '26' => 1,
        '27' => 1,
        '28' => 1,
    ];


    //经纪人每日登陆打卡记录
    public static function everyDayPunch($agentId){
        $agentInfo = Agent::where('id',$agentId)->where('status',1)->first();
        if(!is_object($agentInfo)){
            return ['message'=> '该经纪人无效' , 'status'=>false];
        }
        $today = strtotime('today');
        $punchInfo = self::where('created_at','>=',$today - 86400)
            ->where('type',1)->where('operation','1')->where('agent_id',$agentId)->get()->toArray();
        if(count($punchInfo) >= 2 || (count($punchInfo) == 1 && $punchInfo[0]['created_at'] >= $today )){
            return ['message'=> ['isFirstLogin' => 0 ] , 'status'=>true];
        }
        $data = [];
        $data['agent_id'] = $agentId;
        $data['operation'] = 1;
        $data['remark'] = '每日打卡';
        $data['type'] = 1;
        if( count($punchInfo) == 0 || $punchInfo[0]['num'] == 7 ){
            $data['num'] = 1;
        }
        else{
            $data['num'] = $punchInfo[0]['num'] + 1;
        }
        $data['score'] = $agentInfo['score'] + $data['num'];
        $log = DB::transaction(function ()use($data, $agentId, $today){
            $agent = Agent::find($agentId);
            //查询昨日有没有打卡
            $yestoday = self::where('type',1)->where('operation','1')->where('agent_id',$agentId)->where('created_at','>=',$today - 86400)
                ->where('created_at','<=',$today)->first();

            if($yestoday){
                $data['relation_id'] = $agent->serial_sign+1;
                $agent->serial_sign  +=1;
            }else{
                $agent->serial_sign =  $data['relation_id'] = 1;
            }

            $agent->save();
            $log = self::create($data);
            Agent::where('id',$data['agent_id'])->update(['score' => $data['score']]);

            return $log;
        });

        return ['message'=> ['isFirstLogin' => 1 , 'score'=> $log['num']] , 'status'=> true];
    }



    public function getType()
    {
        if(in_array($this->type, array_keys(self::$TYPES))){
            return self::$TYPES[$this->type];
        }

        return '';
    }


    public function getDescri()
    {
        if(in_array($this->type, array_keys(self::$DESCRI))){
            return self::$DESCRI[$this->type];
        }

        return '';
    }

}
