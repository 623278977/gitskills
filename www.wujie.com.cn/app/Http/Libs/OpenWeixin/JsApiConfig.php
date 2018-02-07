<?php

/*
 * 获取JS api 的ticket，JS-SDK专用
 */

namespace App\Http\Libs\OpenWeixin;

class JsApiConfig extends WeiXin {
    /*
     * 作用：获取公众号的access_token凭证
     * 参数：$reload  是否强制重新获取
     * 返回值：string|false|array
     */
    public function token($reload = false) {
        $key = 'js_access_token';
        if (!$reload) {
            $access_token = $this->getCache($key);
            if ($access_token) {
                return $access_token;
            }
        }
        $access_token = $this->accessToken();
        if (!is_string($access_token)) {//获取失败
            return $access_token;
        }
        $result = $this->curl('cgi-bin/ticket/getticket', [
            'access_token' => $access_token,
            'type' => 'jsapi',
        ]);
        if ($result === false) {//获取失败
            return $result;
        }
        if (!isset($result['errcode']) || $result['errcode']) {//获取出错,返回整个错误说明 如：{"errcode":40013,"errmsg":"invalid appid"}
            return $result;
        }
        $this->setCache($key, $result['ticket'], $result['expires_in']); //缓存数据
        return $result['ticket'];
    }

    /*
     * 作用：生成签名串
     * 参数：$noncestr  随机干扰串
     *      $timestamp  当前时间戳
     *      $url        SDK所在页面URL全地址
     * 返回值：string
     */
    public function signature($noncestr, $timestamp, $url) {
        $jsapi_ticket = $this->token();
        if (!is_string($jsapi_ticket)) {//获取签名失败
            return $jsapi_ticket;
        }
        if (strpos($url, '#') > 0) {
            $url = preg_replace('/#.*$/is', '', $url);
        }
        $sign = compact('noncestr', 'timestamp', 'url') + ['jsapi_ticket' => $jsapi_ticket];
        ksort($sign);
        $signs = [];
        foreach ($sign as $key => $val) {
            $signs[] = "$key=$val";
        }
        return WeiXin::sha1($signs);
    }

    /*
     * 作用：生成签名所需要数据
     * 参数：$url  要生成的URL地址
     * 返回值：array
     */
    public function config($url = null) {
        $timestamp = time();
        $nonce = md5(uniqid(microtime(true)));
        if (is_null($url)) {
            $url = request()->url() . (isset($_SERVER['QUERY_STRING']) && strlen($_SERVER['QUERY_STRING']) ? '?' . $_SERVER['QUERY_STRING'] : '');
        }
        $signature = $this->signature($nonce, $timestamp, $url);
        return is_string($signature) ? [//请求成功
            'appId' => $this->appid, // 必填，公众号的唯一标识
            'timestamp' => $timestamp, // 必填，生成签名的时间戳
            'nonceStr' => $nonce, // 必填，生成签名的随机串
            'signature' => $signature, // 必填，签名，见附录1
                ] : $signature; //签名失败
    }

}
