<?php namespace App\Http\Controllers\Biz;

use \App\Http\Controllers\CommonController;
use Illuminate\Http\Request;


/*** 账户管理控制器 ***/
class AccountController extends CommonController
{
    /**
     * 用户基本信息（user-basic-info）
     */
    public function getUserBasicInfo(Request $request)
    {
        if ( $request->isMethod('post')) {
            $data = $request->input();
        }
    }

    /**
     * 修改用户密码（edit-password）
     */
    public function getEditPassword()
    {

    }

    /**
     * 绑定用户手机号（bind-user-phone）
     */
    public function getBindUserPhone()
    {

    }

    /**
     * 绑定用户邮箱（bind-user-email）
     */
    public function getBindUserEmail()
    {

    }

    /**
     * 用户的安全问题设置（set-issue）
     */
    public function getSetIssue()
    {

    }
}
