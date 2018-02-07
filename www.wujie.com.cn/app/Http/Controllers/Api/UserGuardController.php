<?php
/**
 * 对接申请池控制器
 */
namespace App\Http\Controllers\Api;

use App\Models\User\Entity;
use App\Models\User\Guard;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Api\CommonController;
use DB, Auth;


class UserGuardController extends CommonController
{
    /**
     * @param Request $request
     * @return string
     * 设置对方是否可以看我的好友
     */
    public function postStore(Request $request){
        $uid=intval($request->input('uid'));
        $other_uid=intval($request->input('other_uid'));
        $type=$request->input('type'); //1 设置对方不能看我的好友  -1设置回默认（可以看)
        if(empty($uid)||empty($other_uid)||empty($type))
            return AjaxCallbackMessage('参数有误',false);
        $count=Guard::getCount(array('uid'=>$uid,'other_uid'=>$other_uid));
        if($type==1){
            if($count)
                return AjaxCallbackMessage('设置成功',true);
            Guard::create(compact('uid','other_uid'));
            return AjaxCallbackMessage('设置成功',true);
        }elseif($type==-1){
            if(!$count)
                return AjaxCallbackMessage('设置成功',true);
            Guard::where(array('uid'=>$uid,'other_uid'=>$other_uid))->delete();
            return AjaxCallbackMessage('设置成功',true);
        }
        return AjaxCallbackMessage('参数有误',false);
    }
    /**
     * 判断一个是否有资格看我的好友
     */
    public function postCheckpower(Request $request){
        $uid=intval($request->input('uid'));
        $other_uid=intval($request->input('other_uid'));
        if(empty($uid)||empty($other_uid))
            return AjaxCallbackMessage('参数有误',false);
        $count=Guard::getCount(array('uid'=>$uid,'other_uid'=>$other_uid));
        if($count){
            return AjaxCallbackMessage(1,true);
        }
        return AjaxCallbackMessage(0,true);
    }
}
