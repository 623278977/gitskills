<?php

/*
 * 微信公众平台处理
 */

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Libs\OpenWeixin\Message;
use App\Http\Libs\OpenWeixin\Signature;
use App\Http\Libs\OpenWeixin\Message\Crypt;

class OpenWeiXinController extends Controller {
    //认证
    public function signature(Request $request) {
        return Signature::valid($request->all());
    }

    //接收入口
    public function index(Request $request) {
        if (empty($GLOBALS["HTTP_RAW_POST_DATA"])) {
            return '';
        }
        $encodingAesKey = 'xAFDs0DZdRJKiIfDFDgnJULbpe4Wd8G4Kf3Rr63JZg0';
        $appId = 'wx4bf1a1880fb4f5ca';
        $UserName = 'gh_412b915151a6';
        $message = new Message($request->get('openid'), $UserName);
        if ($message->isEncrypt()) {
            $message->setEncrypt(new Crypt(Signature::TOKEN, $encodingAesKey, $appId));
        }
        return $message->response($request);
    }

    /*
     * 作用：校验签名是否有效
     * 参数：$input  微信请求校验参数
     * 返回值：string
     */
    public function valid(){
        return \App\Http\Libs\OpenWeixin\Signature::valid(\Request::all());
    }
}
