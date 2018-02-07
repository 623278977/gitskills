<?php
/****登陆注册控制器********/
namespace App\Http\Controllers\Biz;

use App\Models\Ad;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class LoginController extends Controller
{
    /*
     * 获取登陆页面
     */
    public function login(Request $request)
    {
        return view('biz.login.login');
    }


    /*
    * 执行登陆动作
    */
    public function doLogin(Request $request)
    {
        return view('biz.login.index');
    }

    /*
    * 获取注册页面
    */
    public function register(Request $request)
    {
        return view('biz.login.register');
    }


    /*
    * 执行注册动作
    */
    public function doRegister(Request $request)
    {
        return view('biz.login.index');
    }


}