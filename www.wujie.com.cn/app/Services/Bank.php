<?php namespace App\Services;

class Bank
{
    const HOSTS      = "http://124.api.apistore.cn";        //"http://jisuyhksb.market.alicloudapi.com";
    const PATHS      = "/bankcard";                         //"/bankcardcognition/recognize";
    const APPCODES   = "b10e87b3557943c5bba212ccc11a22f6";  //"bb8d7a90f14e4d289b501f415bd51c3b";

    public static $instance = null;

    public static function instance()
    {
        if (is_null(self::$instance)) {
            self::$instance = new self;
        }
        return self::$instance;
    }

    /**
     * 返回银行卡主体基本信息
     *
     * param $img_url   银行卡图片
     * dataType 50      表示image的数据类型为字符串
     * dataValue        图片以base64编码的string
     * @param $img_url
     * @param string $credentialsTYPE
     * @param string $method
     * @return null|string
     */
    public function bankBodys($img_url, $credentialsTYPE = "bank", $method = "POST")
    {
        $headers = array();
        array_push($headers, "Authorization:APPCODE " . self::APPCODES);
        array_push($headers, "Content-Type".":"."application/x-www-form-urlencoded; charset=UTF-8");

        $bodys  = "bas64String=$img_url";
        $url    = self::HOSTS . self::PATHS;

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $method);
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_FAILONERROR, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HEADER, true);

        if (1 == strpos("$".self::HOSTS, "https://")) {
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        }

        curl_setopt($curl, CURLOPT_POSTFIELDS, $bodys);
        $data = curl_exec($curl);


        if (curl_getinfo($curl, CURLINFO_HTTP_CODE) == '200') {
            $_array = explode("\r\n", $data);
            $result = json_decode($_array[17], true);
            if ($result['reason'] == "识别成功") {
                if (!empty($result['result']) || isset($result['result'])) {
                    return $result['result'];
                } else { return "data_null"; }
            } else { return null; }
        } else {
            return null; }
    }
}
