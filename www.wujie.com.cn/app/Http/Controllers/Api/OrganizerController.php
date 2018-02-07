<?php
/**
 * 我关注的主办方
 */
namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Models\Activity\OrganizerFollow;
use App\Models\Activity\Organizer;
use App\Http\Controllers\Api\CommonController;
use App\Models\Zone;
use DB, View;

class OrganizerController extends CommonController
{
    /**
     * 我关注的主办方
     * @param Request $request
     * @return string
     */
    public function postMyfollow(Request $request)
    {
        $uid = isset($uid) ? $uid : $request->input('uid');
        $page = $request->input('page') ? $request->input('page') : 1;
        $pageSize = $request->input('page_size');
        $pageSize = $pageSize ? $pageSize : 10;
        $data = DB::table('activity_organizer_follow as aof')
            ->join('activity_publisher as ap', 'aof.organizer_id', '=', 'ap.id')
            //->join('user as u', 'ap.uid', '=', 'u.uid')
            ->where('aof.uid', $uid)
            ->where('aof.status', 1)
            ->orderBy('aof.created_at', 'desc')
            ->skip(($page-1) * $pageSize)
            ->take($pageSize)
            ->select('aof.organizer_id', 'ap.nickname', 'ap.description', 'ap.likes', 'aof.created_at', 'ap.avatar')
            ->get();
        foreach ($data as $item) {
            $item->created_at = date('Y-m-d H:i:s', $item->created_at);
            $item->avatar = getImage($item->avatar);
        }
        return AjaxCallbackMessage($data, true);
    }

    /**
     * 主办方信息
     * @param Request $request
     * @return string
     */
    public function postInfo(Request $request)
    {
        $organizer_id = $request->input('organizer_id');
        $uid = $request->input('uid','');
        $is_follows = DB::table('activity_organizer_follow')
            ->where('uid',$uid)
            ->where('organizer_id',$organizer_id)
            ->first();
        //如果没有关注,返回'';
        $uid = count($is_follows) ? $uid : '';
        $organizerInfo = DB::table('activity_publisher as ap')
            ->leftjoin('activity_organizer_follow as aof','ap.id','=','aof.organizer_id')
            ->where('ap.id', $organizer_id)
            ->where(function ($query) use ($uid) {
                if(!empty($uid)){
                    $query->where('aof.uid', $uid);
                }
            })
            ->select('ap.id', 'ap.description', 'ap.avatar','aof.status','ap.likes','ap.nickname')
            ->first();
        if (count($organizerInfo)) $organizerInfo->avatar = getImage($organizerInfo->avatar, 'avatar');
        if(empty($uid) && count($organizerInfo)) $organizerInfo->status = 0;
        //活动列表
        //$activityList = self::postActivityList($request);
        return AjaxCallbackMessage($organizerInfo, true);
    }

    /**
     *主办方详情
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function postDesc(Request $request)
    {
        $organizer_id = $request->input('organizer_id');
        $desc = DB::table('activity_publisher')
            ->where('id', $organizer_id)
            ->where('status', 1)
            ->select('id', 'description', 'avatar')
            ->first();
        if (count($desc)) $desc->avatar = getImage($desc->avatar, 'avatar');
        return AjaxCallbackMessage($desc, true);
    }

    /**
     * 获取主办方的活动
     * @param Request $request
     * @return string
     */
    public function postActivitylist(Request $request)
    {
        $obj = new ActivityController();
        $data = $obj->postOrganizerlist($request->all());
        self::formatData($data);
        //获取活动地点
        self::activityMaker($data);
        return AjaxCallbackMessage($data, true);
    }

    /**
     * 格式化数据
     * @param $data
     */
    private function formatData($data){
        foreach ($data as $item) {
            $item->list_img = getImage($item->list_img);
            if ($item->end_time < time()) {
                $item->is_over = '已结束';
            } else {
                $item->is_over = null;
            }
            $item->begin_time = date('m/d', $item->begin_time);
            $item->end_time = date('m/d', $item->end_time);
            if($item->price == $item->max_price && $item->price == 0){
                $item->price = '-1';
            }
            if(DB::table('vip')->where('id',$item->vip_id)->first()){
                $item->is_vip = 1;
            }else{
                $item->is_vip = 0;
            }
            unset($item->max_price);
            unset($item->vip_id);
        }
    }

    /**
     * 获取活动地点
     * @param $data
     */
    private function activityMaker($data)
    {
        foreach ($data as $item) {
            $zones = DB::table('activity_maker as am')
                ->leftjoin('maker as m', 'am.maker_id', '=', 'm.id')
                ->leftjoin('zone as z', 'm.zone_id', '=', 'z.id')
                ->where('am.activity_id', $item->id)
                ->select('z.id')
                ->get();
            foreach ($zones as $zone) {
                $zone->id = Zone::getZone($zone->id);
            }
            $zones = array_flatten(objToArray($zones));
            $item->city = implode('@', array_unique(array_flatten(str_replace('市','',$zones))));
        }
    }

    /**
     * 关注活动主办方
     * @param Request $request
     * @return string
     */
    public function postFollow(Request $request)
    {
        $organizer_id = $request->input('organizer_id');
        $uid = isset($uid) ? $uid : $request->input('uid');
        $organizer = Organizer::where('id', $organizer_id)->first();
        if (!isset($organizer->id)) return AjaxCallbackMessage('操作异常', false);
        $like = OrganizerFollow::where('organizer_id', $organizer_id)
            ->where('uid', $uid)
            ->first();
        if (count($like)) {
            //关注状态变更
            $like->status = $like->status == 1 ? 0 : 1;
            $organizer_status = $like->status;
            $like->save();
            //关注likes+1;取消关注likes-1
            $organizer_status ? $organizer->likes += 1 : $organizer->likes -= 1;
            $organizer->save();
            return AjaxCallbackMessage($organizer->likes, true);
        } else {
            //第一次关注
            $param = [
                'organizer_id' => $organizer_id,
                'uid' => $uid,
                'status' => 1,
                'created_at' => time(),
                'updated_at' => time(),
            ];
            OrganizerFollow::create($param);
            $organizer->likes += 1;
            $organizer->save();
            return AjaxCallbackMessage($organizer->likes, true, 1);
        }
    }
}
