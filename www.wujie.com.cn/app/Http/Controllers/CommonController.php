<?php

namespace App\Http\Controllers;

use View;
use Auth;
// use Illuminate\Http\Request;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\File;
use App\Http\Controllers\Controller;
use DB ,Input;
class CommonController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    protected $prefix;

    protected $uploadDir;

    protected $imgUrl;

    protected $_apiurl  = "http://mt.wujie.com.cn/api";//测试

    protected $_appurl  = "http://mt.wujie.com.cn/webapp";//测试

    protected $_baseurl = "http://mt.wujie.com.cn";//测试

    protected $_nativeurl = "http://openNativePage";//测试


    function __construct(){
        //debug 开启
//     	\Debugbar::enable();
        //debug 关闭
        //\Debugbar::disable();
        date_default_timezone_set('Asia/Shanghai');

        $this->prefix = config('database.connections.mysql.prefix');

        $this->uploadDir = config('app.uploadDir');

        $this->imgUrl= config('app.imgUrl');

        View::composer('layouts.default', function($view)
        {
            if (Auth::check()) {
                $uid = Auth::user()->uid;
            }
            $uid = isset($uid) ? $uid : Request::input('uid');
            $user = DB::table('user')->where('uid', $uid)->first();
            $view->with('login_uid', $uid)->with('user', $user);
        });
    }




}
