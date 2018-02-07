<?php
/**群聊***/
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use App\Models\User\Entity as User;
use App\Models\GroupMember;
use DB;

class GroupChat extends Model
{
    //

    protected $table = 'group_chat';
    protected $fillable = [
        'groupid',
        'activity_id',
        'uid',
        'groupname',
        'description',
        'name',
        'number',
        'created_at',
        'updated_at',
        'avatar',
    ];

    public $timestamps = false;

    static function getRows($where)
    {
        return self::where($where)->get();
    }

    static function getBase($groupChat)
    {
        if (!isset($groupChat->id)) return array();
        $data = array();
        $data['groupid'] = $groupChat->groupid;
        $data['name'] = $groupChat->groupname;
        $data['number'] = $groupChat->number;
        $data['sendtime'] = timeDiff($groupChat->sendtime);
        $data['image'] = getImage($groupChat->image, 'groupChat', 'large', 0);
        return $data;
    }

    /**
     * 群聊信息
     * @param $groupids
     * @return mixed
     */
    static function groupInfo($groupids)
    {
        if (!is_array($groupids)) $groupids = explode(',', $groupids);
        $data = DB::table('group_chat as gc')
            ->whereIn('groupid', $groupids)
            ->select('gc.groupid', 'gc.sendtime', 'gc.uid', 'gc.number', 'gc.avatar',
                'gc.groupname', 'gc.created_at', 'gc.updated_at', 'gc.description', 'gc.name',
                'gc.uid as owner', 'gc.type', 'gc.activity_id', 'gc.opportunity_id')
            ->get();
        foreach ($data as $item) {
            $item->name = unserialize($item->name);
            if ($item->name[0] == 'admin' || $item->name[0] == '-1')
                $item->number -= 1;
            //array_shift($item->name);
            $members = self::getMemberInfo($item->name);
            $item->members = $members;
            unset($item->name);
        }
        return $data;
    }

    /**
     * 群员信息
     * @param $members
     * @return array
     */
    static function getMemberInfo($members)
    {
        $return = [];
        $ids = is_array($members) ? $members : explode(',', $members);
        foreach ($ids as $id) {
            $userinfo = User::where('uid', $id)
                ->select('uid', 'username', 'avatar', 'nickname')
                ->first();
            if ($userinfo) {
                $userinfo->avatar = getImage($userinfo->avatar) ?: '';
                $userinfo = $userinfo->toArray();
            }
            if (!is_null($userinfo)) {
                $return[] = $userinfo;
            }
        }
        return $return;
    }

    /**
     * 新增/删除群组成员,返回更新后的群组信息
     * @param $group_id
     * @param $member_uid
     * @return mixed
     */
    static function addOrDeleteMember($group_id, $member_uid, $act)
    {
        $data = self::getGroupInfo($group_id);
        $data->name = unserialize($data->name);
        if ($act == 'add') {
            $arr = $data->name;
            array_push($arr, $member_uid);
            $data->name = array_unique(array_flatten($arr));
        } else {
            $data->name = array_diff($data->name, $member_uid);
        }
        //更新数据
        $res = self::where('groupid', $group_id)->update(['name' => serialize($data->name), 'number' => count($data->name)]);
        //更新group_member表数据
        self::addMemberToDB($group_id, $data->name, $member_uid, $act);
        if ($res !== false) {
            $ids = [];
            $ids[] = $group_id;
            return self::groupInfo($ids);
        }
    }

    /**
     * 更新group_member表数据
     * @param $group_id
     * @param $members
     * @param $member_uid
     * @param $act
     */
    static function addMemberToDB($group_id, $members, $member_uid, $act)
    {
        if (empty($members) || empty($member_uid)) return;
        if ($act == 'add') {
            foreach ($members as $member) {
                if (DB::table('group_member')->where('uid', $member)->where('groupid', $group_id)->first()) continue;
                DB::table('group_member')->insert(
                    ['groupid' => $group_id, 'uid' => $member, 'created_at' => time(), 'updated_at' => time()]
                );
            }
        }else{
            foreach($member_uid as $member){
                DB::table('group_member')->where('groupid',$group_id)->where('uid',$member)->delete();
            }
        }
    }

    /**
     * 群组信息
     * @param $groupid
     * @return mixed
     */
    static function getGroupInfo($groupid)
    {
        if (is_array($groupid) || strpos($groupid, ',')) {
            return self::groupInfo($groupid);
        }
        return self::where('groupid', $groupid)->first();
    }

    /**
     * 更新群组头像
     * @param $groupid
     * @param $avatar
     * @return mixed
     */
    static function changeAvatar($groupid, $avatar)
    {
        return self::where('groupid', $groupid)
            ->update(['avatar' => $avatar]);
    }
}
