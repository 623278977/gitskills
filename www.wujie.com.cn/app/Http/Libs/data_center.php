<?php
/**
 * Created by PhpStorm.
 * Title：公共2
 * User: yaokai
 * Date: 2017/11/29 0029
 * Time: 14:52
 */

/**
 * 生成伪号码
 * @User yaokai
 * @param $tel 15555555555
 * @return string   155****5555
 */
function pseudoTel($tel)
{
    //手机号码简单处理
    $format_tel = substr_replace($tel, '****', 3, 4);
    return $format_tel;
}

/**
 * md5加密手机号  生成唯一标识
 * @User yaokai
 * @param $tel
 * @return string 加盐后的md5手机号  FIXME 这里的盐切记不可随意切换 否则后果自负
 */
function encryptTel($tel)
{
    //md5加密
    $non_reversible = md5('TYrbl' . $tel . '171209');

    return $non_reversible;
}

/**
 * 远程获取数据中心数据，POST模式
 * 注意：
 * 1.使用Crul需要修改服务器中php.ini文件的设置，找到php_curl.dll去掉前面的";"就行了
 * 2.文件夹中cacert.pem是SSL证书请保证其路径有效，目前默认路径是：getcwd().'\\cacert.pem'
 *
 * @param $url 指定URL完整路径地址
 * @param $cacert_url 指定当前工作目录绝对路径
 * @param $para 请求的数据
 * @param $input_charset 编码格式。默认值：空值
 * return 远程输出的数据
 */
function getHttpDataCenter($url, $cacert_url, $para, $input_charset = '')
{
    if (trim($input_charset) != '') {
        $url = $url . "_input_charset=" . $input_charset;
    }

    $cacert_url = $cacert_url ?: '/usr/local/nginx/conf/vhost/https_cert/wjsq/star_wujie_com_cn.pem';

    $curl = curl_init($url);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, true);//SSL证书认证
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2);//严格认证
    curl_setopt($curl, CURLOPT_CAINFO, $cacert_url);//证书地址
    curl_setopt($curl, CURLOPT_HEADER, 0); // 过滤HTTP头
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);// 显示输出结果
    curl_setopt($curl, CURLOPT_POST, true); // post传输数据
    curl_setopt($curl, CURLOPT_POSTFIELDS, $para);// post传输数据
    $responseText = curl_exec($curl);
//    print_r(curl_error($curl));exit;//如果执行curl过程中出现异常，可打开此开关，以便查看异常内容
    curl_close($curl);

    return $responseText;
}


/**
 * 沉淀号码
 * @User tangjb
 * @param $tel
 * @return string
 */
function depositTel($tel, $en_tel, $platform = 'wjsq', $nationCode = 86)
{
    //数据中心处理
    $url = config('system.data_center.hosts') . config('system.data_center.encrypt');
    $data = [
        'nation_code' => $nationCode,
        'tel' => $tel,
        'platform' => $platform,
        'en_tel' => $en_tel,//通过加盐后得到手机号码
    ];

    $result = json_decode(getHttpDataCenter($url, '', $data));

    if (!$result || $result->status == false) {
        throw new Exception('服务器累了！');
    } else{
        return true;
    }
}



/**
 * 解密号码
 * @User tangjb
 * @param $tel
 * @return string
 */
function getRealTel($en_tel, $platform)
{
    $url = config('system.data_center.hosts') . config('system.data_center.decrypt');
    $datas = ['en_tel'=> $en_tel , 'platform'=>$platform];

    $result = json_decode(getHttpDataCenter($url, '', $datas));
    if( empty($result) || $result->status== false){
        return '';
    }
    return $result->message;
}





