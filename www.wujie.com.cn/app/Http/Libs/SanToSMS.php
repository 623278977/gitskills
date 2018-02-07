<?php

/*
 * 闪通国际短信通道平台
 */

class SanToSMS {

    //测试API账号：test1 密码：santo20160913
    //验证码账号： vcpvpv 密码：FwFqnVhS
    //通知类账号： 8du68c 密码：C9YJQSQg
    //营销类账号： vw8bkh 密码：Ecbmyo9b
    private static $accounts = [
        'validate' => ['vcpvpv', 'FwFqnVhS'],
        'notify' => ['8du68c', 'C9YJQSQg'],
//        'sale' => ['vw8bkh', 'Ecbmyo9b'],
        'sale' => ['wsjuph', 'Xrzb4DrB'],
    ];
    //请求域名
    private $url = 'http://api2.santo.cc/submit';
    //错误代码
    private static $error = [
        '0101' => '无效的command参数',
        '0100' => '请求参数错误',
        '0104' => '账号信息错误',
        '0106' => '账号密码错误',
        '0110' => '目标号码格式错误或群发号码数量超过100个',
        '0600' => '未知错误',
    ];
    //状态报告
    private static $status = [
        '401' => '无下行权限（余额不足）',
        '402' => '内容含非法关键字',
        '403' => '内容未含白名单关键字',
        '404' => '其他错误导致信息未能下发（可咨询客服）',
        '501' => '未知错误（可咨询客服）',
    ];

    //发送短信
    public function send($da, &$contents) {
        $ch = curl_init($this->url . '?' . $this->query($this->parse($da), $contents));
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $return = curl_exec($ch);
        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        if (isset(self::$status[$code])) {//返回错误
            return self::$status[$code];
        }
        parse_str($return, $arr);
        if (!isset($arr['mterrcode'])) {//发送异常
            return false;
        }
        if (isset(self::$error[$arr['mterrcode']])) {//发送错误
            return self::$error[$arr['mterrcode']];
        }
        if ($arr['mterrcode'] == '000') {//发送成功
            return true;
        }
    }

    //连接参数
    private function query($da, &$contents) {
        if (strpos($contents, '验证码') !== false && strpos($contents, '分钟内') !== false) {
            list($cpid, $cppwd) = self::$accounts['validate'];
        } elseif(strpos($contents, 'http://') === false){
            list($cpid, $cppwd) = self::$accounts['notify'];
        }else {
            if (strpos($contents, '退订回T') === false) {
                $contents = str_replace('【无界商圈】', '', $contents) . '退订回T';
            }
            list($cpid, $cppwd) = self::$accounts['sale'];
        }
        if (strpos($da, ',') > 1) {
            $command = 'MULTI_MT_REQUEST';
        } else {
            $command = 'MT_REQUEST';
        }
        $query = [
            'command' => $command,
            'cpid' => $cpid,
            'cppwd' => $cppwd,
            'da' => $da, //目标号码
//            'sa' => 'tyrbl', //自定义发送者号码
            'dc' => 15, //消息内容编码，默认15,GBK编码15: GBK  8: Unicode  0: ISO8859-1
            'sm' => encodeHexStr(15, preg_replace('/【[\x{4e00}-\x{9fa5}]+】/u', '', $contents)), //消息内容，经编码后的字符串
        ];
        return http_build_query($query);
    }

    //处理手机号
    private function parse($mobiles) {
        if (!is_array($mobiles)) {
            $mobiles = explode(',', $mobiles);
        }
        return implode(',', array_map(function($item) {
                    return trim($item);
                }, $mobiles));
    }

}
