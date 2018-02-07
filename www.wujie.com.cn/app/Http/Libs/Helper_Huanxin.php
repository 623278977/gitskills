<?php
namespace App\Http\Libs;
use \Cache;

Helper_Huanxin::$apiurl="https://a1.easemob.com/tyrbl/".config('system.huan_xin.name').'/';
class Helper_Huanxin
{

    static $apiurl = '';

    /**
     * 更新token            xxx 整个文件不需要处理
     */
    static function getToken()
    {
//        global $root_dir;
//        $policy = array(
//            'cache_dir' => $root_dir . "/tmp/cache",
//            'life_time' => 2592000
//        );
        $access_token = Cache::get('token-'.config('system.huan_xin.name'));
        if (empty($access_token)) {
            $url = self::$apiurl . "token";
            $params = '{"grant_type":"client_credentials","client_id":"' . config('system.huan_xin.client_id') . '","client_secret":"' . config('system.huan_xin.client_secret') . '"}';
//            $params = '{"grant_type":"client_credentials","client_id":"YXA63M6LYJ8oEeWwPHVJlqewMQ","client_secret":"YXA6viivHQoc3Dj6CHZd5SKfLqpdCnY"}';
            $result = self::callRestfulApi($url, "POST", $params, "");
            $result_json = json_decode($result);
            $access_token = $result_json->{'access_token'};
            Cache::put('token-'.config('system.huan_xin.name'), $access_token, floor($result_json->expires_in / 60) - 10);
        }
        return $access_token;
    }


    /**
     * 发送文本消息给一组用户
     * @param unknown_type $usernames 环信用户id数组
     * @param unknown_type $msg 消息内容
     * @param unknown_type $from 发送方环信id，默认admin为无界投融官方账号id
     * @param array $ext 扩展参数
     * @return mixed
     */
    static function sendMessage($usernames, $msg, $from = "admin", $ext = array("type" => "txt"))
    {

        $url = self::$apiurl . "messages";
        $params = array("target_type" => "users",
            "target" => $usernames,
            "msg" => array("type" => "txt",
                "msg" => $msg),
            "from" => $from,
            "ext" => $ext
        );

        $headers = array('Authorization: Bearer ' . self::getToken());
        $result = self::callRestfulApi($url, "POST", json_encode($params), $headers);
        return $result;
    }

    /**
     * 创建群组
     * @param unknown_type $groupname 群组名称, 此属性为必须的{"name":"xxx","type":"capital","project_id":XXX}
     * @param unknown_type $desc 群组描述, 此属性为必须的;{"type":"capital","project_id":XXX}
     * @param unknown_type $owner 群组的管理员, 此属性为必须的
     * @param unknown_type $members 群组成员,此属性为可选的,但是如果加了此项,数组元素至少一个（注：群主jma1不需要写入到members里面）
     * @param unknown_type $public 是否是公开群, 此属性为必须的,为false时为私有群
     * @param unknown_type $maxusers 群组成员最大数(包括群主), 值为数值类型,默认值200,此属性为可选的
     * @param unknown_type $approval 加入公开群是否需要批准, 默认值是true（加群需要群主批准）, 此属性为可选的,只作用于公开群
     */
    static function createGroup($groupname, $desc, $owner, $members = '', $public = true, $maxusers = 200, $approval = true)
    {
        $url = self::$apiurl . "chatgroups";
        $params = array("groupname" => $groupname,
            "desc" => $desc,
            "public" => $public,
            "maxusers" => $maxusers,
            "approval" => $approval,
            "owner" => $owner
        );
        if ($members) {
            $params['members'] = $members;
        }
        $headers = array('Authorization: Bearer ' . self::getToken());
        $result = self::callRestfulApi($url, "POST", json_encode($params), $headers);
        return $result;
    }

    /**
     * 修改群信息
     * @param unknown_type $group_id 群id
     * @param array $params 要修改的群属性;  "{"需用"%7B"代替
     *                        array(
     * "groupname"=>"testrestgrp12", //群组名称
     * "description"=>"update groupinfo", //群组描述
     * "maxusers"=>300, //群组成员最大数(包括群主), 值为数值类型
     * )
     * @return mixed
     */
    static function editGroup($group_id, $params)
    {
        if (!isset($group_id) || $group_id == "") return '{"error":"need a group_id"}';
        if (!isset($params) || !is_array($params) || !count($params)) return '{"error":"need edit params"}';
        $url = self::$apiurl . "chatgroups/" . $group_id;
        $headers = array('Authorization: Bearer ' . self::getToken());
        $result = self::callRestfulApi($url, "PUT", json_encode($params), $headers);
        return $result;
    }

    /**
     * 删除群组
     * @param unknown_type $groupid 群id
     * @return mixed
     */
    static function deleteGroup($groupid)
    {
        $url = self::$apiurl . "chatgroups/" . $groupid;
        $headers = array('Authorization: Bearer ' . self::getToken());
        $result = self::callRestfulApi($url, "DELETE", "", $headers);
        return $result;
    }

    /**
     * 加入群聊
     * @param unknown_type $groupid 群id
     * @param unknown_type $username 要加入用户环信id
     * @return mixed
     */
    static function addMember($groupid, $username)
    {
        $url = self::$apiurl . "chatgroups/" . $groupid . "/users/" . $username;
        $headers = array('Authorization: Bearer ' . self::getToken());
        $result = self::callRestfulApi($url, "POST", "{}", $headers);
        return $result;
    }

    /**
     * 批量加入群聊
     * @param unknown_type $groupid 群id
     * @param unknown_type $username 要加入用户环信id
     * @return mixed
     */
    static function addMembers($groupid, $params)
    {
        if (!isset($params) || !is_array($params) || !count($params)) return '{"error":"need edit params"}';
        $url = self::$apiurl . "chatgroups/" . $groupid . "/users";
        $headers = array('Authorization: Bearer ' . self::getToken());
        $result = self::callRestfulApi($url, "POST", json_encode(array('usernames' => $params)), $headers);
        return $result;
    }

    /**
     * 群组减人
     * @param unknown_type $groupid 群id
     * @param unknown_type $username 要加入用户环信id
     * @return mixed
     */
    static function deleteMember($groupid, $username)
    {
        $url = self::$apiurl . "chatgroups/" . $groupid . "/users/" . $username;
        $headers = array('Authorization: Bearer ' . self::getToken());
        $result = self::callRestfulApi($url, "DELETE", "{}", $headers);
        return $result;
    }

    /**
     * 群组批量减人
     * @param $groupid
     * @param $params
     * @return mixed|string
     */
    static function deleteMembers($groupid, $params)
    {
        $url = self::$apiurl . "chatgroups/" . $groupid . "/users/" . $params;
        $headers = array('Authorization: Bearer ' . self::getToken());
        $result = self::callRestfulApi($url, "DELETE", "{}", $headers);
        return $result;
    }

    /**
     * 获得好友列表
     * @param unknown_type $username 用户环信id
     * @param unknown_type $return_type 返回类型， json，array
     * @return unknown
     */
    static function getFriendList($username, $return_type)
    {
        if (!isset($username) || $username == "") return "";
        $url = self::$apiurl . "users/" . $username . "/contacts/users";
        $headers = array('Authorization: Bearer ' . self::getToken());
        $result = self::callRestfulApi($url, "GET", "", $headers);
        $result2 = json_decode($result);
        if(!$result2){
            return '';
        }
        switch ($return_type) {
            case("json") :
                return $result2->{'duration'} == 0 ? "" : json_encode($result2->{'data'});
            case("array") :
                return $result2->{'duration'} == 0 ? "" : isset($result2->{'data'}) ? $result2->{'data'} : "";
        }

    }

    /**
     * 是否好友
     * @param unknown_type $username 用户环信id
     * @param unknown_type $friend_username 用户好友环信id
     * @return string
     */
    static function isFriend($username, $friend_username)
    {
        if (!isset($username) || $username == "") return "notlogin";
        if ($username == $friend_username) return "self";
        $friend_list = self::getFriendList($username, "array");
        if ($friend_list && in_array($friend_username, $friend_list)) {
            return "friend";
        } else {
            return "stranger";
        }
    }

    /**
     * 批量判断是否好友
     * @param unknown_type $username 用户环信id
     * @param array $friend_usernames 用户好友环信id，
     * @return string
     */
    static function isFriends($username, $friend_usernames)
    {
        if (!is_array($friend_usernames)) return "error type";
        if (!isset($username) || $username == "") return "nologin";
        $result = array();
        $friend_list = self::getFriendList($username, "array");
        foreach ($friend_usernames as $k => $friend_username) {
            $result[$k]['usernames'] = $friend_username;
            if ($username == $friend_username) {
                $result[$k]['relation'] = "slef";
            } else if ($friend_list && in_array($friend_username, $friend_list)) {
                $result[$k]['relation'] = "friend";
            } else {
                $result[$k]['relation'] = "stranger";
            }
        }
        return json_encode($result);
    }

    /**
     * 发送好友请求
     * @param unknown_type $username 用户环信id
     * @param unknown_type $friend_username 用户好友环信id
     * @return mixed
     */
    static function requestFriend($username, $friend_username)
    {
        $url = self::$apiurl . "users/" . $username . "/contacts/users/" . $friend_username;
        $headers = array('Authorization: Bearer ' . self::getToken());
        $result = self::callRestfulApi($url, "POST", "{}", $headers);
        return $result;
    }

    /**
     * 注册环信用户
     * @param unknown_type $users array("username"=>username,"password"=>username) 密码为用户名，批量注册则传数组
     * @return mixed
     */
    static function register($users)
    {
        $url = self::$apiurl . "users";
        $headers = array('Authorization: Bearer ' . self::getToken());
        $result = self::callRestfulApi($url, "POST", json_encode($users), $headers);
        return $result;
    }

    /**
     * 调用restful接口
     * @param unknown_type $URL 接口地址
     * @param unknown_type $type GET、POST、PUT、DELETE
     * @param unknown_type $params 参数，JSON字符串格式
     * @param unknown_type $headers header参数
     * @param unknown_type $user_password 用户名和密码[username]:[password]
     * @return mixed
     */
    static function callRestfulApi($url, $type, $params, $headers, $user_password = "")
    {
        $ch = curl_init();
        $timeout = 5;
        curl_setopt($ch, CURLOPT_URL, $url);
        if ($headers != "") {
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        } else {
            curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-type: text/json'));
        }
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);

//         CURLOPT_USERPWD 传递一个连接中需要的用户名和密码，格式为："[username]:[password]"。
        if ($user_password != "")
            curl_setopt($ch, CURLOPT_USERPWD, $user_password);

        switch ($type) {
            case "GET" :
                curl_setopt($ch, CURLOPT_HTTPGET, true);
                break;
            case "POST":
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
                break;
            case "PUT" :
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
                curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
                break;
            case "DELETE":
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
                curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
                break;
        }
        $file_contents = curl_exec($ch);//获得返回值
        curl_close($ch);
        return $file_contents;
    }

    /**
     * 获取一个或多个群组的详情
     * @param $group_ids
     * @return mixed
     */
    static function getGroupDetail($group_ids)
    {
        $g_ids = implode(',', $group_ids);
        $url = self::$apiurl . 'chatgroups/' . $g_ids;
        $headers = array('Authorization: Bearer ' . self::getToken());
        $result = self::callRestfulApi($url, "GET", "{}", $headers);
        return $result;
    }


}
