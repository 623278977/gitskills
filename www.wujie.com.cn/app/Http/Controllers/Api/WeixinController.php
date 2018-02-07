<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Libs\OpenWeixin\JsApiConfig;

class WeixinController extends Controller {
    //获取JS-SDK签名配置数据
    public function postJsConfig() {
        //一点来源安全机制处理
        $referer = request()->headers->get('referer');
        if (!$referer || parse_url($referer, PHP_URL_HOST) !== request()->getHttpHost()) {
            return AjaxCallbackMessage('连接来源异常！', false);
        }
        $url = request('url');
        $jsApi = new JsApiConfig('wxa39232c36ae67f81', '01cd59c26db17a3214b35d815c5e24cb');
//        $jsApi = new JsApiConfig('wx1066b88d9a28bbe1','ee85ae79bac57863c90e7faf12ad1ef0');
//        $jsApi = new JsApiConfig('wx8e5ab81536d715e2','d2230efc18fe9172f6cf61250e99c3eb');
        $config = $jsApi->config($url);
        return AjaxCallbackMessage($config, isset($config['signature']));
    }

}
