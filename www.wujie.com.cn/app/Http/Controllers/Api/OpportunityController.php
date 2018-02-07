<?php
/**
 * 商机控制器
 */
namespace App\Http\Controllers\Api;

use App\Events\Event;
use App\Events\WJSQView;
use App\Models\GroupChat;
use App\Models\User\Entity;
use App\Models\User\Favorite;
use App\Models\Zone;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Models\User\Apply;
use App\Http\Controllers\Api\CommonController;
use App\Models\Opportunity;
use DB, Validator;
use Illuminate\Support\Facades\Auth;
use App\Models\Activity\Entity as Activity;
use App\Models\ScoreLog;

class OpportunityController extends CommonController
{
    /**
     * 发布商机
     * @param Request $request
     * @return string
     */
    public function postPublic(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'content' => 'required',
            'phone' => 'required',
            'name' => 'required',
        ]);
        if ($validator->fails()) return AjaxCallbackMessage('请填写完整信息', false);
        $uid = isset($uid) ? $uid : $request->input('uid');
        $content = $request->input('content');
        $phone = $request->input('phone');
        $name = $request->input('name');
        $created_at = $updated_at = time();
        $is_submit = 1;
        $param = compact("uid", "content", "phone", "name", "created_at", "updated_at", "is_submit");
        $opportunity = Opportunity::create($param);
        if (count($opportunity)) {
            //发布商机成功,生成系统消息
            createMessage(
                $uid,
                $title = '你的商机已成功提交到无界商圈后台',
                $content = '感谢你提供商机信息，我们将尽快确认并与你取得联系。无界商圈感谢你让商圈更加丰富多元！',
                $ext = '',
                $end = '<p>如有疑问，请致电服务热线<span>400-011-0061</span></p>',
                $type = 1,
                $delay = 0
            );
            //首次发布商机,获得20无界币
            self::addFirstScore($uid, $opportunity->id);
            return AjaxCallbackMessage('发布成功', true);
        } else {
            return AjaxCallbackMessage('发布失败', false);
        }
    }


    /**
     * 如果是首次发布商机成功，就新增20个无界币
     * @param $uid
     */
    private function addFirstScore($uid, $opportunity_id)
    {
        $first_op = Opportunity::first($uid);

    }

    /**
     * @param Request $request
     * @return string
     * 发现商机
     */
    public function postList(Request $request)
    {
        $uid = $request->input('uid') ?: 0;
        $where = $data = $params = array();
        $where['opportunity.is_submit'] = 0;
        $page = $request->input('page') ?: 0;
        $pageSize = $request->input('pageSize') ?: 10;
        $keywords = $request->get('keywords', '');
        $keyword = $request->get('keyword', '');
        $keyword = empty($keywords) ? $keyword :$keywords;
        if ($keyword)
            $params['keyword'] = $keyword;
        $type = $request->input('type');//主体 goverment 政府 park园区
        if ($type)
            $where['opportunity.type'] = $type;
        $zone_id = $request->input('zone_id');
        $zoneIds = Zone::getZoneIds($zone_id);
        if (count($zoneIds))
            $params['zone_id'] = $zoneIds;
        $industry_id = $request->input('industry_id');
        if (count($industry_id))
            $params['industry_id'] = $industry_id;
        $quyu = $request->input('quyu');
        if ($quyu)
            $params['quyu'] = $quyu;
        $opportunitys = Opportunity::getRows($where, $page, $pageSize, $params);
        if (count($opportunitys)) {
            foreach ($opportunitys as $k => $v) {
                $data[$k] = Opportunity::getBase($v);
                $data[$k]['favorite_count'] = Favorite::getCount(array(
                    'status' => 1,
                    'model' => 'opportunity',
                    'post_id' => $v->id
                ));
            }
        }
        $is_return = $request->input('is_return') ?: 0;
        return ($is_return == 1) ? $data : AjaxCallbackMessage($data, true);
    }

    /**
     * @param Request $request
     * @return string
     * 商机详情  --疑似弃用  不处理
     */
    public function postDetail(Request $request)
    {
        $id = $request->input('id');
        $uid = $request->input('uid', 0);
        if (empty($id))
            return AjaxCallbackMessage('参数有误', false);
        $opportunity = Opportunity::getRow(array('id' => $id));
        if (!isset($opportunity->id))
            return AjaxCallbackMessage('参数有误', false);
        $data = Opportunity::getBase($opportunity);
        $data['dapartment'] = $opportunity->dapartment;
        $data['intro'] = $opportunity->park_info;
        $data['policy'] = $opportunity->policy;
        $data['name'] = $opportunity->name;
        $data['condition'] = $opportunity->condition;
        $data['user'] = Entity::getBase($opportunity->user);
        $groupChat = GroupChat::where('opportunity_id', $opportunity->id)->first();
        $data['groupid'] = isset($groupChat->id) ? $groupChat->groupid : '';
        $data['is_apply'] = (bool)Apply::where(['uid' => $uid, 'opportunity_id' => $id])->where('created_at', '>', time() - 86400)->count();
        $data['favorite_count'] = Favorite::getCount(array(
            'status' => 1,
            'model' => 'opportunity',
            'post_id' => $opportunity->id
        ));
        $data['is_favorite'] = Favorite::getCount(array(
            'uid' => (Auth::check()) ? Auth::id() : $uid,
            'status' => 1,
            'model' => 'opportunity',
            'post_id' => $opportunity->id
        ));

        \Illuminate\Support\Facades\Event::fire(new WJSQView('opportunity', $opportunity));
        return AjaxCallbackMessage($data, true);
    }

    /**
     * @param Request $request
     * 商机相关的活动
     */
    public function postActivitys(Request $request)
    {
        $id = $request->input('id');
        if (empty($id))
            return AjaxCallbackMessage('参数有误', false);
        $opportunity = Opportunity::find($id, ['zone_id', 'id']);
        if (!isset($opportunity->id))
            return AjaxCallbackMessage('参数有误', false);
        $data = array();
//        $activitys = Activity::whereIn('id', function ($query) use ($opportunity) {
//            $query->from('activity_maker')
//                ->whereIn('maker_id', function ($query) use ($opportunity) {
//                    $query->from('maker')
//                        ->where(['zone_id' => $opportunity->zone_id, 'status' => 1])
//                        ->select('id');
//                })->select('activity_id');
//        })
//            ->where('end_time', '>', time())
//            ->orderBy('view', 'desc')
//            ->limit(3)
//            ->get();
//
//        if ($activitys->count() < 3) {//不足取其它推荐的
//            $query = Activity::where('is_recommend', '=', 1)
//                ->where('end_time', '>', time())
//                ->orderBy('view', 'desc')
//                ->limit(3 - $activitys->count());
//            if ($activitys->count()) {
//                $query->whereNotIn('id', $activitys->modelKeys());
//            }
//            $activitys = $activitys->merge($query->get());
//        }
//        foreach ($activitys as $k => $v) {
//            $data[$k] = Activity::getBase($v);
//            $data[$k]['begin_time'] = $v->begin_time;
//            $data[$k]['end_time'] = $v->end_time;
//            $data[$k]['zone'] = Activity::getZone($v);
//            $data[$k]['price'] = Activity::getMinTicket($v);
//        }
        $query = DB::table('activity')
            ->leftJoin('activity_maker', 'activity.id', '=', 'activity_maker.activity_id')
            ->leftJoin('maker', 'maker.id', '=', 'activity_maker.maker_id')
            ->leftJoin('activity_ticket', 'activity_ticket.activity_id', '=', 'activity.id')
            ->where('maker.status', 1)
            ->where('activity_maker.status', 1)
            ->where('activity.end_time', '>', time())
            ->where('activity_ticket.surplus', '>', 0)
            ->where('activity_ticket.type', 1)
//            ->orderBy('activity.view', 'desc')
            ->orderBy(\DB::raw('RAND()'))
            ->groupBy('activity.id')
            ->select('maker.id as now_maker_id', 'activity.id', 'activity.list_img','activity.description',
                'activity.subject', 'activity.begin_time', 'activity.end_time','activity.view','activity.vip_id',
                'activity.is_recommend'
            );
        $provice_query = clone $query;
        $other_query = clone $query;
        $city_query = $query->where('maker.zone_id', $opportunity->zone_id)->limit(3);
        //不足取同省城的
        $provice_activitys = array();
        if (count($city_query->get()) < 3) {//不足取其它推荐的
            $provice_activity = $provice_query
                ->whereIn('maker.zone_id', Zone::brothers($opportunity->zone_id)->modelKeys())
                ->where('maker.zone_id', '<>', $opportunity->zone_id)
                ->whereNotIn('activity.id', $city_query->lists('id'))
                ->where('activity.is_recommend', 1)
                ->limit(3 - count($city_query->get()));
            $provice_activitys = $provice_activity->get();
        }
        isset($provice_activity) ? $provice_count =count($provice_activity->get()):$provice_count=0;
        isset($provice_activity) ? $provice_ids =$provice_activity->lists('id'):$provice_ids=[];
        //再不足就随机取
        $other_activitys = array();
        if ((count($query->get())+$provice_count) < 3) {
            $other_activitys = $other_query
                ->where('activity.is_recommend', 1)
                ->whereNotIn('activity.id', $city_query->lists('id'))
                ->whereNotIn('activity.id', $provice_ids)
                ->limit(3-(count($query->get())+$provice_count))
                ->get();
        }
        $activitys = array_merge($city_query->get(),$provice_activitys ,$other_activitys );
        foreach ($activitys as $k => $v) {
            $data[$k] = Activity::getBase($v,1);
            $data[$k]['now_maker_id'] = $v->now_maker_id;
            $data[$k]['begin_time'] = $v->begin_time;
            $data[$k]['end_time'] = $v->end_time;
            $data[$k]['zone'] = Activity::getZone($v,1);
            $data[$k]['price'] = Activity::getMinTicket($v,'range',1);
            $data[$k]['subject'] = $v->subject. '-' . Activity::getZoneName($v->now_maker_id) . '站';
        }
        return AjaxCallbackMessage($data, true);
    }
}
