<?php
/****广告banner控制器********/
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\CommonController;
use App\Models\Maker\Entity;
use App\Models\Maker\Member;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use App\Http\Libs\Helper_Huanxin;

class MakerController extends CommonController
{
    /**
     * @param Request $request
     * ovo中心列表
     */
    public function postList(Request $request)
    {
        $where = array();
        $keyword = trim($request->input('keyword'));
        $recommend = $request->input('recommend') ? 1 : 0;
        if ($recommend) {
            $where['recommend'] = 1;
        }
        $makers = Cache::has('maker_list') ? \Cache::get('maker_list') : false;

        if ($makers === false || $keyword || $recommend) {
            if (empty($keyword)) {
                $list = Entity::getRows($where, 0, 0);
            } else {
                $list = Entity::where('subject', 'like', '%' . $keyword . '%')->where($where)->where('status', 1)->get();
            }
            $makers = array();
            if (count($list)) {
                foreach ($list as $k => $v) {
                    $makers[$k] = Entity::getBase($v);
                }
                if (empty($keyword) && !$recommend) {
                    Cache::put('maker_list', $makers, 1);
                }
            }
        }

        return AjaxCallbackMessage($makers, true);
    }

    /**
     * @param Request $request
     * @return string
     * ovo中心详情
     */
    public function postDetail(Request $request)
    {
        $id = $request->input('maker_id');
        $uid = $request->input('uid');
        if (empty($id)) {
            return AjaxCallbackMessage('参数有误', false);
        }
        $maker = Entity::getRow(array('id' => $id));
        if (!isset($maker->id)) {
            return AjaxCallbackMessage('数据有误', false);
        }
        $data = Entity::getBase($maker);
        $user = \App\Models\User\Entity::getRow(array('uid' => $data['uid']));
        $data['nickname'] = isset($user->uid) ? $user->nickname : '';
        $data['avatar'] = isset($user->uid) ? getImage($user->avatar, 'avatar', 'thumb') : getImage('', 'avatar', 'thumb');
        $data['is_member'] = Member::getCount(
            array(
                'uid'      => $uid,
                'maker_id' => $id
            )
        );
        $live_data = Entity::getLiveByMaker($id);
        $data['is_live'] = count($live_data) ? 1 : 0;
        if ($data['is_live']) {
            $data['live'] = $live_data;
        }

        return AjaxCallbackMessage($data, true);
    }

    /**
     * @param Request $request
     * @return string
     * 获取一个ovo的本地群聊
     */
    public function postGroupchatlist(Request $request)
    {
        $id = $request->input('maker_id');
        $uid = (int)$request->input('uid');
        $maker = Entity::find($id);
        if($id == 0){
            $data = [
                //'groupid' => '',
                //'name'    => '',
                //'number'  => '',
                //'follow'  => '',
                //'image'   => '',
            ];
        }else{
            if (empty($maker) || $uid < 1) {
                return AjaxCallbackMessage('参数有误', false);
            }
//        $data = Entity::getGroupChatByMaker($maker->getKey(), $uid);
            $data = [];
            if ($maker->groupid) {//网点自己群
                $result = json_decode(Helper_Huanxin::getGroupDetail([$maker->groupid]), true);
                $number = 2;
                if (isset($result['data'][0]['affiliations_count'])) {
                    $number = $result['data'][0]['affiliations_count'];
                }
                array_unshift(
                    $data,
                    [
                        'groupid' => $maker->groupid,
                        'name'    => $maker->subject,
                        'number'  => $number,
                        'follow'  => -2,
                        'image'   => getImage($maker->logo, 'maker', 'thumb', 0),
                    ]
                );
            }
        }

        return AjaxCallbackMessage($data, true);
    }

    /**
     * @param Request $request
     * @return string
     * 成员列表
     */
    public function postMemberlist(Request $request)
    {
        $maker_id = $request->input('maker_id');
        $page = $request->input('page',1);
        $page_size = $request->input('page_size',15);
        if (empty($maker_id) && $maker_id!=0) {
            return AjaxCallbackMessage('参数有误', false);
        }
        $where['maker_member.maker_id'] = $maker_id;
        $where['user.status'] = 1;
        $industry_id = $request->input('industry_id');
        if (!empty($industry_id)) {
            $where['user_industry.industry_id'] = $industry_id;
        }
        $data = Entity::getMembers($where, $page,$page_size);

        return AjaxCallbackMessage($data, true);
    }

    /**
     * @param Request $request
     * @return string
     * 成员行业列表
     */
    public function postMemberIndustryList(Request $request)
    {
        $maker_id = $request->input('maker_id');
        if (empty($maker_id) && $maker_id!=0) {
            return AjaxCallbackMessage('参数有误', false);
        }
        $lists = \App\Models\Industry::whereIn(
            'id',
            function ($query) use ($maker_id) {
                $query->from('user_industry')
                    ->whereIn(
                        'uid',
                        function ($query) use ($maker_id) {
                            $query->from('user')
                                ->join('maker_member', 'user.uid', '=', 'maker_member.uid')
                                ->where('maker_member.maker_id', '=', $maker_id)
                                ->where('user.status', '1')
                                ->select('user.uid');
                        }
                    )
                    ->DISTINCT()
                    ->select('industry_id');
            }
        )
            ->where('status', '1')
            ->get(['id', 'name'])
            ->toArray();

        return AjaxCallbackMessage($lists, true);
    }

    /**
     * 申请入驻 ovo   -- 数据中心弃用  不处理  yaokai
     * @param Request $request
     * @return string
     */
    public function postMakermember(Request $request)
    {
        $uid = $request->input('uid');
        $maker_id = $request->input('maker_id');
        if (!\App\Models\User\Entity::checkAuth($uid)) {
            return AjaxCallbackMessage('账号异常', false);
        }
        if (empty($maker_id) && $maker_id!=0) {
            return AjaxCallbackMessage('参数有误', false);
        }
        if($maker_id == 0){
            $maker = (object)config('system.virtual_ovo');
        }else{
            $maker = Entity::find($maker_id);
        }
        if (empty($maker)) {
            return AjaxCallbackMessage('网点不存在！', false);
        }

        if (Member::getCount(compact('uid', 'maker_id'))) {
            return AjaxCallbackMessage('入驻成功', true);
        }
        Member::where('uid', $uid)->delete();
        Member::create(compact('uid', 'maker_id'));
        $user = \App\Models\User\Entity::getRow(array('uid' => $uid));
        //退出群
        $old_maker = Entity::find($user->maker_id, ['groupid', 'subject']);
        $user->update(compact('maker_id'));//关联OVO存在两个地方，程序未能做到统一
        if ($old_maker && $old_maker->groupid) {
            try {
                $group = new GroupChatController();
                //退出原来的
                $request->merge(['act' => 'delete', 'groupid' => $old_maker->groupid, 'member_uid' => [$uid]]);
                $group->postAddordelmember($request);
                //加入新的
                $request->merge(['act' => 'add', 'groupid' => $maker->groupid, 'member_uid' => [$uid]]);
                $group->postAddordelmember($request);
            } catch (\Exception $e) {
            }
        }
        $message_maker_id = $maker_id == 0 ? 0 :$maker->getKey();
        createMessage(
            $user->uid,
            '你已成功入驻' . $maker->subject,
            '感谢你选择<a style="color:#1e8cd4" href="wjsq://ovo?makerid=' . $message_maker_id . '">' . $maker->subject . '</a>，运营中心将努力为你提供本地活动、直播、商机对接等服务。',
            '',
            '',
            1
        );
//        $ovoruzhi_content = trans('sms.ovoruzhi', ['maker' => $maker->subject]);
//        @SendSMS($user->username, $ovoruzhi_content, 'ovoruzhi', 3);
        @SendTemplateSMS('ovoruzhi',$user->username,'ovoruzhi',['maker' => $maker->subject],$user->nation_code);
        //入驻成功发送城市合伙人消息
        \App\Models\Partner\Message::newMemberJoinYou($user, $maker_id);

        return AjaxCallbackMessage('入驻成功', true);
    }

    /*
     * 以地区id找到ovo详情
     */
    public function postSwitchmaker(Request $request){
        $uid = $request->input('uid');
        if(!$uid){
            return AjaxCallbackMessage('uid不能为空', FALSE);
        }
        $zone_id = $request->input('zone_id','');
        if(!$zone_id && !$request->has('maker_id')){
            return AjaxCallbackMessage('地区不能为空', FALSE);
        }
        if($request->has('maker_id')){
            $id = $request->input('maker_id');
            if($id == 0){
                $maker = (object)config('system.virtual_ovo');
            }else{
                $maker = Entity::getRow(array('id' => $id));
            }
            if (!isset($maker->id)) {
                return AjaxCallbackMessage('数据有误', false);
            }
        }else{
            $maker = \App\Models\Maker\Entity::findNearByMaker($zone_id,$uid);
        }
        $data = Entity::getBase($maker);
        $user = \App\Models\User\Entity::getRow(array('uid' => $data['uid']));
        $data['nickname'] = isset($user->uid) ? $user->nickname : '';
        if($maker->id == 0){
            $data['nickname'] = $maker->nickname;
            $data['uid'] = $maker->uid;
        }
        $data['avatar'] = isset($user->uid) ? getImage($user->avatar, 'avatar', 'thumb') : getImage('', 'avatar', 'thumb');
        $data['is_member'] = Member::getCount(
            array(
                'uid'      => $uid,
                'maker_id' => $maker->id
            )
        );
        $live_data = Entity::getLiveByMaker($maker->id);
        $data['is_live'] = count($live_data) ? 1 : 0;
        if ($data['is_live']) {
            $data['live'] = $live_data;
        }
        $data['is_virtual_ovo'] = 0;
        if ($maker->id == 0){
            $data['is_virtual_ovo'] = 1;
        }
        return AjaxCallbackMessage($data, true);

    }
}