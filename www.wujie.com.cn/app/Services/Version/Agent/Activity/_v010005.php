<?php

namespace App\Services\Version\Agent\Activity;

use App\Models\Activity\Entity\AgentActivity as ActivityAgent;
use App\Models\Agent\Agent;
use App\Exceptions\ExecuteException;

use App\Models\Agent\AgentCustomer;
use App\Models\Agent\Entity\_v010200 as Agentv010200;
use App\Models\Agent\Score\AgentScoreLog;
use Illuminate\Support\Facades\DB;
use App\Models\Activity\Ticket as ActivityTicket;
use App\Models\Activity\Entity as Activity;
use App\Models\Activity\Sign as ActivitySign;
use App\Models\Agent\Activity\Sign as AgentActivitySign;
use App\Models\Agent\AgentTicket;

use App\Models\Activity\Live;
use App\Models\Activity\Sign;
use App\Models\Maker\Entity as Maker;
use App\Models\User\Entity as User;


class _v010005 extends _v010000
{
    /**
     * 经纪人活动报名  --数据中心版
     * @User tangjb
     * @param array $data
     * @return array
     */
    public function postApply($data = [])
    {
        //伪号码
        $username = pseudoTel($data['tel']);

        //用户加密后的手机号
        $non_reversible = encryptTel($data['tel']);

        //判断其是否已经报名
        $sign = ActivitySign::whereIn('status', [0, 1])->where(['non_reversible' => $non_reversible, 'activity_id' => $data['activity_id']])->first();
        $agent_sign = AgentActivitySign::whereIn('status', [0, 1])->where(['non_reversible' => $non_reversible, 'activity_id' => $data['activity_id']])->first();


        if (is_object($sign) || is_object($agent_sign)) {
            return ['data' => '该号码已报名活动', 'status' => false];
        }

        $user = Agent::find($data['id']);

        //随机的给他一张票，都是免费
        $ticket = ActivityTicket::where('type', 1)->where('surplus', '>', 0)->where('activity_id', $data['activity_id'])->first();

        if (!$ticket) {
            return ['data' => '票已售完', 'status' => false];
        }

        $activity = Activity::getRow(['id' => $data['activity_id']]);

        //判断活动是否已结束
        if ($activity->end_time < time()) {
            return ['data' => '该场活动已经结束', 'status' => false];
        }


        $data['product'] = $activity->subject;
        $data['body'] = $activity->subject;
        empty($data['name']) && $data['name'] = $user->nickname;
        empty($data['company']) && $data['company'] = '';
        empty($data['job']) && $data['job'] = '';
        $data['score_num'] = 0;


        if (empty($data['tel'])){
            $non_reversible =$user->non_reversible;
            $username =$user->username;
        }


        //开始事务
        \DB::beginTransaction();
        try {
            //出票
            $u_ticket = AgentTicket::produce($user->id, $ticket->id, $data['maker_id'], 1, 1, $data['score_num']);

            ActivityTicket::incre(['id' => $ticket->id], ['surplus' => -1]);

            $result = AgentActivitySign::apply($user->id, $data['maker_id'], $data['activity_id'], $data['company'], $data['job'],
                $u_ticket->ticket_no, $u_ticket->ticket_id, 0, $data['name'], $username, $non_reversible);

            //发送融云消息
            $return_result = AgentCustomer::instance()->gainCustomerAgentRelationDatas($user->id);
            if ($return_result && $data['agent_id'] != $return_result->agent_id) {
                $activity_result = Activity::where('id', $data['activity_id'])->first();
                $this->_activitySignSuccessSendInfos([
                    'customer_id' => $user->id,
                    'agent_id' => $return_result->agent_id,
                    'activity_name' => $activity_result->subject,
                    'activity_time' => date("Y年m月d日", $activity_result->begin_time),
                    'activity_make' => DB::table('maker')->where('id', $data['maker_id'])->value('subject'),
                ]);
            }

            //加积分
            Agentv010200::add($data['id'], AgentScoreLog::$TYPES_SCORE[2], 2, '活动报名', $result->id);
            \DB::commit();
        } catch (\Exception $e) {
            \DB::rollBack();
            throw(new ExecuteException('报名失败，出现异常' . $e->getMessage()));
        }

        //发短信
        if ($ticket->type == 1) {  //如果是现场票
            //因短信平台原因可能取消动态url短信
            $url = config('app.agent_app_url') . 'activity/detail/' . config('app.agent_version') . '?id=' . $activity->id . '&agent_id='.$data['id'];
            @SendTemplateSMS('activitySiteSign', $user->non_reversible, 'activitySiteSign', [
                'name' => $activity->subject,
                'time' => date('m月d日H点i分', $activity->begin_time),
                'url' => shortUrl($url)
            ], $user->nation_code, 'agent');
        }


        if (is_object($result)) {
            $res = send_transmission(json_encode(['type'=>'new_message', 'style'=>'json',
                'value'=> ['sendTime' => time()]]),
                $user,null, 1);
            return ['data' => $result->id, 'status' => true];
        } else {
            return ['data' => '报名失败', 'status' => false];
        }
    }


    /**
     * 获取活动的报名信息
     */
    public function postEnrollInfos($param)
    {
        //活动信息
        $activity = \DB::table('activity')->where('id', $param['id'])
            ->select('subject as title', 'begin_time', 'keywords',
                'list_img', 'detail_img')
            ->first();

        if (empty($param['id']) || !$activity) {
            return ['data' => '活动id未传递或者不存在该活动', 'status' => false];
        }

        //场地信息
        $makers = \DB::table('activity_maker')
            ->leftJoin('maker', 'activity_maker.maker_id', '=', 'maker.id')
            ->leftJoin('zone', 'maker.zone_id', '=', 'zone.id')
            ->where('activity_maker.activity_id', $param['id'])
            ->where('activity_maker.status', 1)
            ->select('zone.name', 'maker.address', 'maker.tel', 'maker.subject as title', 'maker.id')
            ->get();

        $citys = [];
        foreach ($makers as $k => $v) {
            $v->name = str_replace('市', '', $v->name);
            $citys[] = $v->name;
        }
        $activity->cities = implode(' ', $citys);
        $activity->begin_time = date('Y年m月d日 H:i', $activity->begin_time);
        $activity->keywords ? $activity->keywords = explode(' ', $activity->keywords) : $activity->keywords = [];
//        $activity->list_img = getImage($activity->list_img,'activity', '', 0);
//        $activity->detail_img = getImage($activity->detail_img,'activity', '', 0);

        return ['status' => true, 'message' => ['activity' => $activity, 'makers' => $makers]];
    }


    /**
     * 经纪人活动报名成功页   --数据中心版
     * @param $param
     * @return array
     * @author tangjb
     */
    public function postApplySuccess($param)
    {
        //活动信息
        $sign = AgentActivitySign::with('actity', 'ticket', 'maker')->where('id', $param['id'])->first();

        $sign_users = AgentActivitySign::getSignUsers($sign->activity_id);
        $zone = str_replace('市', '', $sign->maker->zone->name);
        $zone ? $ticket = "现场票 - {$zone}" : $ticket = "现场票";

        //活动报名信息
        $data = [
            'subject' => $sign->actity->subject,
            'begin_time' => $sign->actity->begin_time,
            'name' => $sign->name,
            'tel' => getRealTel($sign->non_reversible, 'agent'),
            'company' => $sign->company,
            'job' => $sign->job,
            'ticket' => $ticket,
            'sign_users' => $sign_users,
            'sign_count' => count($sign_users),
            'activity_id' => $sign->activity_id,
        ];

        return ['message' => $data, 'status' => true];
    }


    /**
     ** @param $param
     * @return array
     * @author tangjb
     */
    public function postSign($param)
    {
        $needs = ['agent_id', 'maker_id', 'activity_id'];
        foreach ($needs as $k => $v) {
            if (empty($param[$v])) {
                return ['message' => "缺少参数{$v}", 'status' => false];
            }
        }

        $agent_sign = AgentActivitySign::with('actity')
            ->where('agent_id', $param['agent_id'])
            ->where('maker_id', $param['maker_id'])
            ->whereIn('status', [0, 1])
            ->where('activity_id', $param['activity_id'])->first();

        $activity = Activity::find($param['activity_id']);


        //已结束
        if ($activity->end_time<time()) {
            return ['status' => false, 'message' => ['message'=>'活动已结束','type'=>'2']];
        }

        //未报名
        if (!$agent_sign) {
            return ['status' => false, 'message' => ['message'=>'没有在该会场报名活动','type'=>'0']];
        }


        if ($agent_sign->status == 1) { //已签到
            return ['status' => false, 'message' => ['message'=>['subject'=>$agent_sign->actity->subject, 'activity_begin_time'=>date('Y-m-d H:i', $agent_sign->actity->begin_time)],'type'=>'1']];

        }


        //开始事务
        \DB::beginTransaction();
        try {
            $agent_sign->status = 1;
            $agent_sign->sign_time = time();
            $agent_sign->save();
            AgentTicket::where('ticket_no', $agent_sign->ticket_no)->update(['is_check' => 1]);
            //加积分
            Agentv010200::add($param['agent_id'], AgentScoreLog::$TYPES_SCORE[3], 3, '活动签到', $agent_sign->id);
            \DB::commit();
        } catch (\Exception $e) {
            \DB::rollBack();
        }

        return ['status' => true, 'message' => ['subject'=>$agent_sign->actity->subject, 'activity_begin_time'=>date('Y-m-d H:i', $agent_sign->actity->begin_time)]];
    }





}