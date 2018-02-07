<?php

/*
 * 微信签名处理,接口配置信息
 */

namespace App\Http\Libs\OpenWeixin;

class Signature {

    const TOKEN = 'nLCI873kn09DLWN0NKkj9'; //自定义公众平台专用token串

    /*
     * 作用：校验签名
     * 参数：$signature  微信加密签名串
     *      $timestamp   微信请求时间戳
     *      $nonce       微信请求随机数
     * 返回值：boolean
     */
    public static function check($signature, $timestamp, $nonce) {
        return self::make($timestamp, $nonce) === $signature;
    }

    /*
     * 作用：校验签名是否有效
     * 参数：$input  微信请求校验参数
     * 返回值：string
     */
    public static function valid(array $input) {
        if (empty($input['signature']) || empty($input['timestamp']) || empty($input['nonce']) || empty($input['echostr']) ||
                !self::check($input['signature'], $input['timestamp'], $input['nonce'])) {
            return '';
        }
        return $input['echostr'];
    }

    /*
     * 作用：生成签名
     * 参数：$input  微信请求校验参数
     * 返回值：string
     */
    public static function make($timestamp, $nonce) {
        $tmpArr = array(self::TOKEN, $timestamp, $nonce);
        sort($tmpArr, SORT_STRING);
        return WeiXin::sha1($tmpArr, '');
    }

}
