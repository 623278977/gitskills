<?php

namespace App\Http\Controllers\Cloud;

use Illuminate\Http\Request;

class AuthController extends Controller {
    /**
     * 登陆
     * @param Request $request
     */
    public function anyLogin(Request $request) {
        if ($request['password'] === '123456') {
            return AjaxCallbackMessage('登录成功', true);
        } else {
            return AjaxCallbackMessage('密码错误', false);
        }
    }

}
