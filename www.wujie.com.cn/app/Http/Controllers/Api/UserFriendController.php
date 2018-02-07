<?php
/**
 * 对接申请池控制器   --整个控制器弃用 环信  yaokai
 */
namespace App\Http\Controllers\Api;
use App\Http\Libs\Helper_Huanxin;
use App\Models\User\Entity;
use App\Models\User\Friend;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Api\CommonController;
use DB, Auth;
use Illuminate\Support\Facades\Cache;


class UserFriendController extends CommonController
{
    /**
     * @param Request $request
     * 设置备注
     */
    public function postSetremark(Request $request){
        $uid=intval($request->input('uid'));
        $other_uid=intval($request->input('other_uid'));
        if(empty($uid)||empty($other_uid))
            return AjaxCallbackMessage('参数有误',false);
        $remark=$request->input('remark');
        $friend_tel=$request->input('friend_tel');
        $friend_intro=$request->input('friend_intro');
        if(Friend::getCount(array('uid'=>$uid,'other_uid'=>$other_uid))){
            Friend::where(array('uid'=>$uid,'other_uid'=>$other_uid))->update(array(
                    'remark'=>$remark,
                    'friend_tel'=>$friend_tel,
                    'friend_intro'=>$friend_intro
                ));
        }else{
            Friend::create(compact('uid','other_uid','remark','friend_tel','friend_intro'));
        }
        return AjaxCallbackMessage('设置成功',true);
    }

    /**
     * @param Request $request
     * 获取一个人好友列表
     */
    public function postGetlist(Request $request){
        $uid=$request->input('uid');
        if(empty($uid))
            return array();
        $page=$request->input('page')?:0;
        $list=Helper_Huanxin::getFriendList($uid,'array');
        if(!is_array($list)){
            $list=[];
        }
        $count=count($list);
        $data=array();
        $array=array_slice($list,$page*10,10);
        if(count($array)){
            foreach($array as $k=>$v){
                $user=Entity::getRow(array('uid'=>$v));
                $data[$k]=Entity::getUser($user);
                $data[$k]['job']=isset($user->business_card->job)?$user->business_card->job:'';
                $data[$k]['institution']=isset($user->business_card->institution)?$user->business_card->institution:'';
            }
        }
        return AjaxCallbackMessage(array(
            'userlist'=>$data,
            'count'=>$count
        ),true);
    }

    /*
     * 加好友发送消息
     */
    public function postSendmessage(Request $request){
        //接收方
        $to = $request->input('to');
        $toUser = Entity::where('uid',$to)->first();
        if(!$toUser){
            return AjaxCallbackMessage('接收方不存在',FALSE);
        }
        //发送方
        $from = $request->input('from');
        $fromUser = Entity::where('uid',$from)->first();
        if(!$fromUser){
            return AjaxCallbackMessage('发送方不存在',FALSE);
        }
        $nickname = $fromUser->nickname?:$fromUser->username;
        $method = $request->input('type','push');
        if($method == 'push' || $method == 'all'){
            //发推送
            send_notification("您有一条好友提示",
                "($nickname)诚心添加你为好友，应该是被你的气质所吸引，所以你要通过吗？",
                json_encode(['type'=>'add_friend','style'=>'id','value'=>$fromUser->uid]),//透传消息
                $toUser);
        }
        if($method == 'sms' || $method == 'all'){
            //发短信
            @SendTemplateSMS('addbuddy',$toUser->username,'addbuddy',['nickname'=>$nickname]);
        }
        if($method == 'message' || $method == 'all'){
            //站内信
            createMessage(
                $toUser->uid,
                $title = "您有一条好友提示",
                $content = "（{$nickname}）想添加你为好友，确认通过即可聊天。",
                $ext = '',
                //$end = '<p>如有疑问，请致电服务热线<span>400-011-0061</span></p>',
                $end = '',
                $type = 1,
                $delay = 0
            );
        }
        return AjaxCallbackMessage('操作成功',true);
    }
}
