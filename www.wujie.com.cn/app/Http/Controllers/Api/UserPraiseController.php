<?php
/**
 * 用户点赞
 */
namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use DB, Auth;
use App\Models\User\Praise;


class UserPraiseController extends CommonController
{
    /**
     * 点赞
     */
    public function postZan(Request $request){
        if (!$request['uid'] || !$request['id'] || !$request['relation']) {
            return ['status' => FALSE, 'message' => 'uid和对应id、关联类型是必填项'];
        }
        $result=Praise::add($request['uid'], $request['relation'], $request['id']);
        return AjaxCallbackMessage($result, !is_string($result));
    }
}
