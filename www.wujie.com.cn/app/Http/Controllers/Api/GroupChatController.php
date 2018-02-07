<?php
/****群聊控制器********/
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\CommonController;
use App\Http\Libs\Helper_Huanxin;
use App\Models\GroupChat;
use Illuminate\Http\Request;
use App\Models\User\Entity as User;
use DB;

class GroupChatController extends CommonController
{
    /*
     * 删除热门群聊
     */
    public function postDelete(Request $request)
    {
        $groupid = $request->input('groupid');
        if (empty($groupid))
            return AjaxCallbackMessage('参数有误', false);
        GroupChat::where('groupid', $groupid)->delete();
        return AjaxCallbackMessage('删除成功', true);
    }

    /**
     * 创建群聊
     * @param Request $request
     * @return string
     */
    public function postCreate(Request $request)
    {
        $param = $request->all();
        $groupname = $request->input('groupname', '');
        $avatar = $request->input('avatar', '');
        if (empty($groupname)) return AjaxCallbackMessage('群组名称不能为空', false);
        $desc = $request->input('desc', '');
        if (empty($desc)) return AjaxCallbackMessage('群组描述不能为空', false);
        $owner = $request->input('owner', '');
        if (empty($owner)) return AjaxCallbackMessage('群组管理员不能为空', false);
        $activity_id = $request->input('activity_id', 0);
        $members = $request->input('members', '');
        //$members = ['183699'];
        $public = $request->input('public', true);
        $approval = $request->input('approval', true);
        if ($public == '1' || $approval == '1') $approval = $public = true;
        if ($public == '0' || $approval == '0') $approval = $public = false;
        $maxusers = $request->input('maxusers', 200);
        $return = Helper_Huanxin::createGroup($groupname, $desc, $owner, $members, $public, $maxusers, $approval);
        $return = json_decode($return, true);
        if (isset($return['data']['groupid']) && !empty($return['data']['groupid'])) {
            $data = [
                'groupid' => $return['data']['groupid'],
                'uid' => $owner,
                'groupname' => $groupname,
                'description' => $desc,
                'activity_id' => $activity_id,
                'avatar' => $avatar,
                'number' => !empty($members) ? count($members) + 1 : 0,
                'name' => $this->serialMember($members, $owner),
                'created_at' => time(),
                'updated_at' => time(),
            ];
            $res = GroupChat::create($data);
            if ($res) {
                $return = $this->getReturnData($res->id);
                return AjaxCallbackMessage($return, true);
            }
        }
        return AjaxCallbackMessage($return, false);
    }

    /**
     * 序列化群组成员
     * @param $members
     * @param $owner
     * @return array
     */
    private function serialMember($members, $owner)
    {
        if (is_array($members)) {
            array_unshift($members, $owner);
        }elseif(empty($members)){
            $members[] = $owner;
        }
        return serialize($members);
    }

    /**
     * 群组创建成功返回数据
     * @param $id
     * @return array
     */
    private function getReturnData($id)
    {
        $obj = GroupChat::find($id);
        $return = [];
        $members = GroupChat::getMemberInfo(unserialize($obj->name));
        $return['groupid'] = $obj->groupid;
        $return['groupname'] = $obj->groupname;
        $return['description'] = $obj->description;
        $return['avatar'] = $obj->avatar;
        $return['owner'] = $obj->uid;
        $return['memberinfo'] = $members;
        $return['number'] = $obj->number;
        $return['type'] = $obj->type;
        $return['activity_id'] = $obj->activity_id;
        $return['opportunity_id'] = $obj->opportunity_id;
        return $return;
    }

    /**
     * 编辑群聊信息
     * @param Request $request
     * @return string
     */
    public function postEdit(Request $request)
    {
        $group_id = $request->input('groupid', '');
        if (empty($group_id)) return AjaxCallbackMessage('群组id不能为空', false);
        $data = [];
        if($request->has('groupname')){
            $data['groupname'] = $request->input('groupname');
        }
        if($request->has('description')){
            $data['description'] = $request->input('description');
        }
        $return = Helper_Huanxin::editGroup($group_id, $data);
        $return = json_decode($return, true);
        if (isset($return['data'])) {
            $res = GroupChat::where('groupid', $group_id)->update($data);
            if ($res !== false) return AjaxCallbackMessage('编辑成功', true);
        }
        return AjaxCallbackMessage($return, false);
    }

    /**
     * 获取一个或多个群组的详情
     * @param Request $request
     * @return string
     */
    public function postGetgroupinfo(Request $request)
    {
        $groupids = $request->input('groupids', '');
        if (empty($groupids)) return AjaxCallbackMessage('群组id不能为空', false);
        //$groupids = ['210092020973175208'];
        $data = GroupChat::groupInfo($groupids);
        if ($data) {
            return AjaxCallbackMessage($data, true);
        }
        return AjaxCallbackMessage('操作失败', false);
    }

    /**
     * 删除群组
     * @param Request $request
     * @return string
     */
    public function postDeletegroup(Request $request)
    {
        $groupid = $request->input('groupid');
        $res = Helper_Huanxin::deleteGroup($groupid);
        $res = json_decode($res, true);
        if ($res['data']['success'] == true) {
            $res = GroupChat::where('groupid', $groupid)->delete();
            if ($res !== false) return AjaxCallbackMessage('删除成功', true);
        }
        return AjaxCallbackMessage($res, false);
    }

    /**
     * 添加/移除群组成员
     * @param Request $request
     * @return string
     */
    public function postAddordelmember(Request $request)
    {
        $group_id = $request->input('groupid', '');
        if (empty($group_id))
            return AjaxCallbackMessage('群组id不能为空', false);
        $member_uid = $request->input('member_uid', '');
        if (empty($member_uid) || !is_array($member_uid))
            return AjaxCallbackMessage('群组成员uid为不为空的数组', false);
        //$member_uid = ['148'];
        $act = $request->input('act', 'add');
        $res = $this->addOrDelMember($group_id, $member_uid, $act);
        $res = json_decode($res, true);
        if ($act == 'add'){
            if (isset($res['data']) && ($res['data']['action'] == 'add_member')) {
                //添加单个群组成员
                if(isset($res['data']['user'])){
                    $member_uid = [$res['data']['user']];
                    $return = GroupChat::addOrDeleteMember($group_id, $member_uid, $act);
                    if ($return) return AjaxCallbackMessage($return, true);
                }
                //批量添加群组成员
                if(isset($res['data']['newmembers'])){
                    $member_uid = $res['data']['newmembers'];
                    $return = GroupChat::addOrDeleteMember($group_id, $member_uid, $act);
                    if ($return) return AjaxCallbackMessage($return, true);
                }
            }
        }elseif($act == 'delete'){
            if (isset($res['data']) && (isset($res['data']['action']) && $res['data']['action'] == 'remove_member' || isset($res['data'][0]['action']) && $res['data'][0]['action'] == 'remove_member')) {
                //删除单个群组成员
                if (isset($res['data']['user'])){
                    $member_uid = [$res['data']['user']];
                    $return = GroupChat::addOrDeleteMember($group_id, $member_uid, $act);
                    if ($return) return AjaxCallbackMessage($return, true);
                }
                //批量删除群组成员
                if (isset($res['data'][0]['user'])){
                    foreach($res['data'] as $item){
                        if($item['result'] == true){
                            $member_uid[] = $item['user'];
                        }
                    }
                    $return = GroupChat::addOrDeleteMember($group_id, $member_uid, $act);
                    if ($return) return AjaxCallbackMessage($return, true);
                }
            }
        }
        return AjaxCallbackMessage($res, false);
    }

    /**
     * 分配添加/移除群组成员操作
     * @param $group_id
     * @param $member_uid
     * @param $act
     * @return mixed|void
     */
    private function addOrDelMember($group_id, $member_uid, $act)
    {
        if ($act == 'add') {
            if (count($member_uid) > 1) {
                $res = Helper_Huanxin::addMembers($group_id, $member_uid);
            } else {
                $res = Helper_Huanxin::addMember($group_id, $member_uid[0]);
            }
        } elseif ($act == 'delete') {
            if (count($member_uid) > 1) {
                $member_uid = implode(',', $member_uid);
                $res = Helper_Huanxin::deleteMembers($group_id, $member_uid);
            } else {
                $res = Helper_Huanxin::deleteMember($group_id, $member_uid[0]);
            }
        }
        return $res;
    }

    /**
     * 修改群头像
     * @param Request $request
     * @return string
     */
    public function postChangeavatar(Request $request){
        $group_id = $request->input('groupid', '');
        $avatar = $request->input('avatar','');
        $res = GroupChat::changeAvatar($group_id,$avatar);
        if($res!==false) return AjaxCallbackMessage('操作成功', true);
        return AjaxCallbackMessage('操作失败', false);
    }

}