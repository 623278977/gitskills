<?php

/*
 * 微信公众平台接口基类
 */

namespace App\Http\Libs\OpenWeixin;

use Illuminate\Support\Facades\Cache;

abstract class WeiXin {

    protected $appid; //唯一凭证
    protected $secret; //唯一凭证密钥

    const HOST = 'https://api.weixin.qq.com'; //接口基本域名
    const CACHE_KEY_PREFIX = 'weixin_api_'; //缓存键名前缀

    /*
     * 作用：初始化处理
     * 参数：$appid  唯一凭证
     *      $secret  唯一凭证密钥
     * 返回值：无
     */
    public function __construct($appid, $secret) {
        $this->appid = $appid;
        $this->secret = $secret;
    }

    /*
     * 作用：获取公众号的access_token凭证
     * 参数：$reload  是否强制重新获取
     * 返回值：string|false|array
     */
    public function accessToken($reload = false) {
        $key = 'access_token';
        if (!$reload) {
            $access_token = $this->getCache($key);
            if ($access_token) {
                return $access_token;
            }
        }
        $result = $this->curl('cgi-bin/token', [
            'grant_type' => 'client_credential',
            'appid' => $this->appid,
            'secret' => $this->secret,
        ]);
        if ($result === false) {//获取失败
            return $result;
        }
        if (isset($result['errcode'])) {//获取出错,返回整个错误说明 如：{"errcode":40013,"errmsg":"invalid appid"}
            return $result;
        }
        $this->setCache($key, $result['access_token'], $result['expires_in']); //缓存数据
        return $result['access_token'];
    }

    /*
     * 作用：发送请求
     * 参数：$uri    请求的URL路径地址，不含域名
     *      $query   请求参数数据
     *      $isPost  是否为POST方式请求
     * 返回值：array|false
     */
    public function curl($uri, array $query, $isPost = false) {
        $ch = curl_init();
        $queryStr = http_build_query($query);
        if ($isPost) {
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $queryStr);
        } else {
            $uri .= '?' . $queryStr;
        }
        curl_setopt($ch, CURLOPT_URL, self::HOST . '/' . ltrim($uri, '/'));
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $return = curl_exec($ch);
        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        if ($code != '200') {
            return false;
        }
        try {
            return json_decode($return, true);
        } catch (Exception $e) {
            return false;
        }
    }

    /*
     * 作用：写入缓存数据
     * 参数：$key  基础键名
     *      $val   缓存值
     *      $expires  缓存时间，以标准有效期再减20分钟
     * 返回值：无
     */
    public function setCache($key, $val, $expires) {
        Cache::put($this->key($key), $val, round(($expires - 1200) / 60));
    }

    /*
     * 作用：获取缓存数据
     * 参数：$key  获取键名
     * 返回值：mixed
     */
    public function getCache($key) {
        return Cache::get($this->key($key));
    }

    /*
     * 作用：生成缓存键名
     * 参数：$key  基本键名
     * 返回值：string
     */
    protected function key($key) {
        return self::CACHE_KEY_PREFIX . md5($this->appid . $this->secret) . '_' . $key;
    }

    /*
     * 作用：生成加密串
     * 参数：$signs  需要生成的数组
     *      $glue 分隔符
     * 返回值：string
     */
    public static function sha1($signs, $glue = '&') {
        return sha1(implode($glue, $signs));
    }

}
