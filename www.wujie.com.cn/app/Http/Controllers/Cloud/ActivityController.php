<?php

namespace App\Http\Controllers\Cloud;

use DB;
use App\Models\ScoreLog;
use Illuminate\Http\Request;
use App\Models\Activity\Sign;
use App\Models\Activity\Ticket;
use App\Models\User\Entity as User;
use App\Models\User\Ticket as UserTicket;
use App\Models\Activity\Entity as Activity;

class ActivityController extends Controller {
    /**
     * 活动信息
     */
    public function postLists(Request $request) {
        //发布的活动
        $activity = Activity::where('status', 1);
        $cross_domain = $request->get('cross_domain');
        if (in_array($cross_domain, ['yes', 'no'], true)) {
            $activity->where('cross_domain', '=', $cross_domain == 'yes' ? '1' : '0');
        }
        $industry_id = $request->get('industry_id');
        if ($industry_id > 0) {
            $activity->whereIn('id', function($query)use($industry_id) {
                $query->from('activity_industry')->where('industry_id', '=', (int) $industry_id)->select('aid');
            });
        }
        $publisher_uid = $request->get('publisher_uid');
        if ($publisher_uid > 0) {
            $activity->where('publisher_uid', '=', (int) $publisher_uid);
        }
        $status = (int) $request->get('status');
        switch ($status) {
            case 1://未开始
                $activity->where('begin_time', '>', time())->orderBy('begin_time', 'asc');
                $is_end = 0;
                break;
            case -1://已结束
                $activity->where('end_time', '<', time())->orderBy('end_time', 'desc');
                $is_end = 1;
                break;
            default://进行中（默认）
                $activity->where('begin_time', '<', time())->where('end_time', '>', time())->orderBy('id', 'desc');
                $is_end = 0;
                break;
        }
        $data = array();
        $pageSize = min($request->pageSize > 1 ? (int) $request->pageSize : 100, 500);
        $lists = $activity->paginate($pageSize);
        $ids = array_pluck($lists, 'id');
        if (count($ids)) {
            //现场票价
            $ticket_prices = Ticket::whereIn('activity_id', $ids)
                    ->where('type', '=', '1')
                    ->where('status', '=', '1')
                    ->get(['price', 'activity_id'])
                    ->lists('price', 'activity_id');
            //人数
            $ticket_nums = Sign::whereIn('activity_id', $ids)
                    ->where('status', '>=', '0')
                    ->groupBy('activity_id')
                    ->get([DB::raw('count(1) as num,activity_id')])
                    ->lists('num', 'activity_id');
            foreach ($lists as $val) {
                $data[] = [
                    'id' => $val->id,
                    'subject' => $val->subject,
                    'begin_time' => $val->begin_time,
                    'time' => date("Y-m-d H:i", $val->begin_time),
                    'price' => $ticket_prices->has($val->id) ? $ticket_prices[$val->id] : '-',
                    'count' => $ticket_nums->has($val->id) ? $ticket_nums[$val->id] : '0',
                    'is_end' => $is_end,
                ];
            }
        }
        return AjaxCallbackMessage($data, true);
    }

    /*
     * 行业列表
     */
    public function postIndustrys() {
        return AjaxCallbackMessage(\App\Models\Industry::where('status', '1')->orderBy('sort', 'desc')->get(['id', 'name'])->toArray(), true);
    }

    /*
     * 活动场地列表
     */
    public function postMakerLists(Request $request) {
        $activity_id = (int) $request->input('activity_id');
        if ($activity_id < 1) {
            return AjaxCallbackMessage('活动参数异常！', false);
        }
        $activity = Activity::where('id', $activity_id)->first();
        if (!$activity || $activity->status != 1) {
            return AjaxCallbackMessage('活动不存在', false, '');
        }
        $lists = $activity->makers()
                ->where('activity_maker.status', '1')
                ->get(['maker.subject', 'maker.id'])
                ->toArray();
        return AjaxCallbackMessage($lists, true);
    }

    /*
     * 发布人列表
     */
    public function postPublishers() {
        return AjaxCallbackMessage(\App\Models\Activity\Publisher::where('status', '1')->get(['id', 'nickname'])->toArray(), true);
    }

    /**
     * 签到的用户列表   --数据中心没有影响  暂不处理
     */
    public function postSignUsers(Request $request) {
        $signin = (int) $request->input('signin');
        $maker_id = (int) $request->input('maker_id');
        $activity_id = (int) $request->input('activity_id');
        $pageSize = $request->pageSize > 1 ? (int) $request->pageSize : 100;
        if ($activity_id < 1) {
            return AjaxCallbackMessage('活动参数异常！', false);
        }
        $sign = Sign::where('activity_id', $activity_id);
        //0未签到，1已签到，2临时访客，3所有
        switch ($signin) {
            case 0:
                $sign->where('status', '=', '0');
                break;
            case 1:
                $sign->where('status', '=', '1')->where('uid', '>', '0');
                break;
            case 2:
                $sign->where('status', '=', '1')->where('uid', '=', '0');
                break;
            default:
                $sign->where('status', '>=', '0');
                break;
        }
        if ($maker_id > 0) {
            $sign->where('maker_id', '=', $maker_id);
        }
        $data = array();
        foreach ($sign->paginate($pageSize) as $key => $val) {
            $data[$key]['name'] = (string) $val->name;
            $data[$key]['tel'] = $val->tel? : ($val->uid && $val->user ? $val->user->username : '');
            $data[$key]['signname'] = $val->status == 1 ? '已签到' : '未签到';
            if ($data[$key]['name']) {
                continue;
            }
            if ($val->uid && $val->user) {
                $data[$key]['name'] = $val->user->realname;
            }
        }
        return AjaxCallbackMessage($data, true);
    }

    /**
     * 新增临时访客
     */
    public function postApply(Request $request) {
        $applyData = $request->only('name', 'tel', 'activity_id', 'maker_id');
        if (!$request->has('name')) {
            return AjaxCallbackMessage('姓名不能为空', false, '');
        }
        $applyData['company'] = $request->has('company') ? $request->get('company') : '';
        $applyData['job'] = $request->has('job') ? $request->get('job') : '';
        $activity_id = (int) $request->input('activity_id');
        $tel = trim($request->input('tel'));
        if (empty($tel)) {
            return AjaxCallbackMessage('手机号不能为空', false, '');
        }
        if (!preg_match('/^((\d{10,11})|(1[34578]\d{9}))$/', $tel)) {
            return AjaxCallbackMessage('手机号格式不对', false, '');
        }
        if ($activity_id < 1) {
            return AjaxCallbackMessage('activity_id不能为空', false, '');
        }
        if ($applyData['maker_id'] < 1) {
            return AjaxCallbackMessage('场地必填', false, '');
        }
        $user = User::where('username', $tel)->first();
        $applyData['uid'] = 0;
        $uid = 0;
        if (isset($user->uid)) {
            $uid = $user->uid;
        }
        $activity = Activity::where('id', $activity_id)->first();
        //找出这个活动相同标签的活动
        $tag_id = $activity->tag_id;
        $activitys = Activity::where('tag_id',$tag_id)->get()->toArray();
        $activitys = array_pluck($activitys,'id');//这个标签所有的关联活动id
        if (!$activity) {
            return AjaxCallbackMessage('活动不存在', false, '');
        }
        $subject = $activity->subject;
        $activityApply = Sign::whereIn('activity_id', $activitys)
                ->where('uid', $uid)
                ->where('status', 1);
        if (!$uid) {
            $activityApply->where('tel', $tel);
        }
        if ($activityApply->count()) {
            return AjaxCallbackMessage('您已签到此活动，请不要重复操作', false, '');
        }
        $applyData['sign_time'] = time();
        $applyData['status'] = 1;
        $apply = Sign::create($applyData);
        if ($apply) {
            self::sendNotice($uid, $tel, $subject);
            return AjaxCallbackMessage("成功", true, '');
        }
        return AjaxCallbackMessage("失败", false, '');
    }

    /**
     * 活动签到    -- 数据中心版
     * @User yaokai
     * @param Request $request
     * @return string
     */
    public function postSignin(Request $request)
    {
        $params = $request->only('tel', 'activity_id', 'ticket_no', 'maker_id');

        //用户加密后的手机号
        $non_reversible = encryptTel($params['tel']);

        if (empty($params['activity_id'])) {
            return AjaxCallbackMessage('活动id 不能为空', false, '');
        }
        if ($params['maker_id'] < 1) {
            return AjaxCallbackMessage('场地必填', false, '');
        }
        $activity = Activity::where('id', $params['activity_id'])->first();
        //找出这个活动相同标签的活动
        $tag_id = $activity->tag_id;
        $activitys = Activity::where('tag_id', $tag_id)->get()->toArray();
        $activitys = array_pluck($activitys, 'id');//这个标签所有的关联活动id
        if (!$activity) {
            return AjaxCallbackMessage('活动不存在', false, '');
        }
        $subject = $activity->subject;//短信内容
        $apply = Sign::whereIn('activity_id', $activitys)
            ->where('status', '>', -1)
            ->orderBy('status', 'asc');
        if (!$params['ticket_no']) {
            if (!preg_match('/^((\d{10,11})|(1[34578]\d{9}))$/', $params['tel'])) {
                return AjaxCallbackMessage('手机号格式不对', false, '');
            }
            $apply->where('non_reversible', $non_reversible);
        } else {
            $ticket = UserTicket::where('ticket_no', $params['ticket_no'])->first();
            $uid = $ticket->uid;
            if (!$ticket || $ticket->status != 1) {
                return AjaxCallbackMessage('门票不存在', false, '');
            }
            //考虑多个关联活动的门票不一样则不筛选门票
            $apply->where('uid', $uid);
        }
        $apply_maker = clone $apply;
        $apply_makers = $apply_maker->where('maker_id', $params['maker_id'])->get();
        $array = $apply->get()->toArray();
        $apply = $array ? $apply->get() : $apply_makers;
        if ($array) {
            foreach ($apply as $apply) {
                if ($apply->status == 1) {
                    return AjaxCallbackMessage('您已签到此活动，请不要重复操作', false, '');
                }
                //修改签到数据
                $apply->update(array('status' => 1, 'sign_time' => time(), 'maker_id' => $params['maker_id']));
                UserTicket::where('uid', $apply->uid)
                    ->whereIn('activity_id', $activitys)
                    ->where('type', 1)
                    ->where('is_check', 0)
                    ->update(['is_check' => 1]);
            }
        } else {
            $user = User::where('non_reversible', $non_reversible)->first();
            $data = ['is_apply' => 0];
            if (!$user) {//木有签到数据，并且木有注册的用户
                $data['name'] = '';
                $data['tel'] = '';
            } else {//木有签到数据，的注册用户
                $data['name'] = $user->realname;
                $data['tel'] = $user->non_reversible;
            }
            return AjaxCallbackMessage($data, false, '');
        }
        $data = ['is_apply' => 1];
        if (isset($ticket) && $ticket->user) {
            $data['name'] = $ticket->user->realname;
            $data['tel'] = $ticket->user->non_reversible;
            $uid = $ticket->user->getKey();
        } else {
            $data['name'] = $apply->name;
            $data['tel'] = $apply->non_reversible;
            $uid = 0;
        }

        self::sendNotice($uid, $data['tel'], $subject);//发短信开发时暂时屏蔽

        return AjaxCallbackMessage($data, true, '');
    }

    /**
     * 签到 发短信以及环信通知  --  数据中心版
     * @param unknown_type $uid
     * @param unknown_type $tel  md5加盐的手机号
     * @param unknown_type $subject
     */
    private static function sendNotice($uid, $tel, $subject)
    {
        if ($tel) {
            if ($uid) {
                $user = \App\Models\User\Entity::where('uid', $uid)->first();
                @SendTemplateSMS('yearQianDao', $tel, 'qiandao', ['subject' => $subject], $user->nation_code);
            } else {
                $user = \App\Models\User\Entity::where('username', $tel)->first();
                if (!isset($user->uid)) {
                    @SendTemplateSMS('yearQianDao', $tel, 'qiandao', ['subject' => $subject], $user->nation_code);
                }
            }
        }
    }

}
