<?php

use Illuminate\Support\Facades\Input;
use App\Services\Chat\Example;
use Illuminate\Support\Facades\Request;
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/5/5
 * Time: 11:15
 */
function defaultKeyIndex($array)
{
    if (is_array($array)) {
        $array = array_map('defaultKeyIndex', array_merge($array));
    }
    return $array;
}

/**
 * ajax请求返回数据格式
 * Enter description here .
 * ..
 *
 * @param string|unknown_type $msg
 * @param bool|unknown_type $code
 * @param string|unknown_type $forwardUrl
 * @param int $https
 * @return string
 */
function AjaxCallbackMessage($msg = '', $code = true, $forwardUrl = '', $https = 1)
{
    $host = \Input::header()['host'][0];
    $host_arr = [
        $host,
        'test.wujie.com.cn',
        'mt.wujie.com.cn'
    ];
    $host_arr = '(' . implode('|', $host_arr) . ')';
    $url = \Input::url();
    $pattern = $needle = $replace = '';
    if (strpos($url, 'https://') !== false) {
        $pattern = '/http:\/\/' . $host_arr . '\//i';
        $needle = 'http://';
        $replace = 'https://';
    } else if (strpos($url, 'http://') !== false) {
        $pattern = '/https:\/\/' . $host_arr . '\//i';
        $needle = 'https://';
        $replace = 'http://';
    }

    if ($https) {
        $array = array(
            "message" => defaultKeyIndex(httpToHttps($msg, $pattern, $needle, $replace)),
            "status" => $code,
            "forwardUrl" => $forwardUrl
        );
    } else {
        $array = array(
            "message" => $msg,
            "status" => $code,
            "forwardUrl" => $forwardUrl
        );
    }


    return json_encode($array);
}


/**
 * 分页结果
 * @User yaokai
 * @param $builder
 * @param Closure|null $callback
 * @return array
 */
function paginate($builder, \Closure $callback = null) {
//    $pageSize   = (int) Input::get('pageSize', $this->pageSize);
//    $lists      = $builder->paginate($pageSize > 0 ? min($pageSize,50) : $this->pageSize);

    $data = [
        'total'         => $builder->total(),
        'per_page'      => $builder->perPage(),
        'current_page'  => $builder->currentPage(),
        'last_page'     => $builder->lastPage(),
    ];

    $items          = $builder->getCollection()->toArray();
    $data['data']   = count($items) && $callback ? $callback($items) : $items;

    return $data;
}



/**
 * 根据结果构建分页结果
 * @User yaokai
 * @param $builder
 * @param Closure|null $callback
 */
function paginator($builder, \Closure $callback = null)
{
    $page_size = (int)Input::get('page_size','10');

    $curPage = \Illuminate\Pagination\Paginator::resolveCurrentPage();
    $itemQuery = clone $builder;

    $items = $itemQuery->forPage($curPage, $page_size)->get();

    $totalResult = $builder->addSelect(\DB::raw('count(1) as count'))->get();//

    $totalItems = $totalResult->first() ? $totalResult->first()->count : '';

    $lists = new \Illuminate\Pagination\LengthAwarePaginator($items->all(), $totalItems, $page_size);

    $result['total']  = $lists->total();

    $items = $lists->getCollection()->toArray();

    $result['data'] = count($items) && $callback ? $callback($items) : $items;

    return $result;

}




/**
 * 获得唯一字符串
 *
 * @return string
 */
function unique_id()
{

    srand((double)microtime() * 1000000);
    return md5(uniqid(rand()));
}

/**
 * @param 二维码内容 value
 * @param 图片存放地址
 * @param 其中logo的地址 logo-path
 */
function img_create($value = '', $filename = '', $logo_path = './images/qrcode/logo.png', $is_alpha = false)
{
    include_once 'phpqrcode.class.php';

    $dis = 'attached/image/qrcode/' . $filename;
    $errorCorrectionLevel = 'H'; //容错级别
    $matrixPointSize = 6; //生成图片大小

    //生成二维码图片
    QRcode::png($value, $dis, $errorCorrectionLevel, $matrixPointSize, 2, false, $is_alpha);

    $path = imagecreatefromstring(file_get_contents($dis));
    if (is_file($logo_path)) {

        $logo = imagecreatefromstring(file_get_contents($logo_path));

        $QR_width = imagesx($path); //二维码图片宽度
        $QR_height = imagesy($path); //二维码图片高度
        $logo_width = imagesx($logo); //logo图片宽度
        $logo_height = imagesy($logo); //logo图片高度
        $logo_qr_width = $QR_width / 4;
        $scale = $logo_width / $logo_qr_width;
        $logo_qr_height = $logo_height / $scale;
        $from_width = ($QR_width - $logo_qr_width) / 2;
        $from_h = ($QR_height - $logo_qr_height) / 2;
        //重新组合图片并调整大小
        imagecopyresampled($path, $logo, $from_width, $from_h, 0, 0, $logo_qr_width, $logo_qr_height, $logo_width, $logo_height);
    }
    //输出图片
    imagepng($path, $dis);
    return $dis;
}

/**
 * 获得图片地址
 * size 表示large thumb 具体宽度根据需要生成
 */
function getImage($filename, $type = 'avatar', $size = 'large', $large_thumb = 1)
{
    if (empty($filename)) {
        //返回默认图片
        switch ($type) {
            case 'avatar':
                return \Illuminate\Support\Facades\URL::asset('/') . ($size == 'large' ? "images/default/avator-xl.png" : "images/default/avator-m.png");
                break;
            case 'activity':
                return \Illuminate\Support\Facades\URL::asset('/') . ($size == 'large' ? "images/default/large-pro.jpg" : "images/default/small-pro.jpg");
                break;
            case 'video':
                return \Illuminate\Support\Facades\URL::asset('/') . ($size == 'large' ? "images/default/large-pro.jpg" : "images/default/small-pro.jpg");
                break;
            case 'news':
                return \Illuminate\Support\Facades\URL::asset('/') . ($size == 'large' ? "images/default/large-pro.jpg" : "images/default/small-pro.jpg");
                break;
            case 'maker':
                return \Illuminate\Support\Facades\URL::asset('/') . ($size == 'large' ? "images/default/large-pro.jpg" : "images/default/small-pro.jpg");
                break;
            case 'live':
                return \Illuminate\Support\Facades\URL::asset('/') . "images/live.png";
                break;
            case 'choujiang':
//                return URL::asset('/').($size=='large' ?"choujiang/images/default.png":"choujiang/images/small_default.png");
                return URL::asset('/') . ($size == 'large' ? "images/default_game.png" : "images/default_game.png");
            default:
                return \Illuminate\Support\Facades\URL::asset('/') . $filename;
                break;
        }
    }

    $pathInfo = pathinfo($filename);

    //如果包含http  就全路径显示
    if (strstr($filename, 'http')) {
        $url = $filename;
    } else {
        if ($size) {
            $url = \Illuminate\Support\Facades\URL::asset('/') . $pathInfo ['dirname'] . ($large_thumb == 1 ? "/_" . $size : "") . "/" . $pathInfo ['basename'];
        } else {
            //原图
            $url = \Illuminate\Support\Facades\URL::asset('/') . $pathInfo ['dirname'] . "/" . $pathInfo ['basename'];
        }
    }

    return $url;
}

/**
 * 检查手机号码是否正确
 *
 * @return boolean
 */
function checkMobile($mobile, $nation_code = '86')
{
    //判断是否是美国号码
//    if(strlen($mobile) == '10'){
//        $nation_code = '1';
//    }
    $nation_code = str_replace('+','',$nation_code);
    if ($nation_code == '86') {
        $num = preg_match("/^[1][3,4,5,7,8][0-9]{9}$/", $mobile, $match);
    } elseif ($nation_code == '1') {
        $num = preg_match("/^[0-9]{10}$/", $mobile, $match);
    } else {
        $num = 0;
        //todo 除了美国之外的情况
    }

    if ($num == 0) {
        return false;
    } else {
        return true;
    }
}

/*
 * 动机：
 * 有些地方需要根据手机号判断国家码，例如agent端邀请投资人、经纪人时的通讯录导入，
 * 目前没有什么好的办法来判断一个号码是美国号，只能根据10位位数来判断，先写一个接口在这里，统一调用
 * 这个接口来判断国家码，等以后真正涉及的国家多了，如果能有更好的方法来判断国家码，那么只用该这里就行了，
 * 达到一改全改的目的
 * */
/**
 * @param $phone    手机号
 * @return $nationCode   国家码
 */
function getNationCode($phone){
    $nationCode = '';
    if(strlen($phone) == 10){
        $nationCode = '1';
    }
    else if(strlen($phone) == 11){
        $nationCode = '86';
    }
    return $nationCode;
}

/**
 * 模糊的检查手机号码是否正确 11位和10位都通过
 *
 * @return boolean
 */
function checkMobileBlur($mobile, $nation_code = '86')
{
    $china_check = preg_match("/^13[0-9]{1}[0-9]{8}$|15[0-9]{1}[0-9]{8}$|17[0-9]{1}[0-9]{8}$|18[0-9]{1}[0-9]{8}$/", $mobile, $match);

    $america_check = preg_match("/[0-9]{10}/", $mobile, $match);

    if (($china_check || $america_check) == 0) {
        return false;
    } else {
        return true;
    }
}


/**
 * +----------------------------------------------------------
 * 产生随机字串，可用来自动生成密码
 * 默认长度6位 字母和数字混合 支持中文
 * +----------------------------------------------------------
 *
 * @param string $len 长度
 * @param string $type 字串类型
 *        0 字母 1 数字 其它 混合
 * @param string $addChars 额外字符
 *        +----------------------------------------------------------
 * @return string +----------------------------------------------------------
 */
function rand_string($len = 6, $type = '', $addChars = '')
{

    $str = '';
    switch ($type) {
        case 0:
            $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz' . $addChars;
            break;
        case 1:
            $chars = str_repeat('0123456789', 3);
            break;
        case 2:
            $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ' . $addChars;
            break;
        case 3:
            $chars = 'abcdefghijklmnopqrstuvwxyz' . $addChars;
            break;
        default:
            // 默认去掉了容易混淆的字符oOLl和数字01，要添加请使用addChars参数
            $chars = 'ABCDEFGHIJKMNPQRSTUVWXYZabcdefghijkmnpqrstuvwxyz23456789' . $addChars;
            break;
    }
    if ($len > 10) { //位数过长重复字符串一定次数
        $chars = $type == 1 ? str_repeat($chars, $len) : str_repeat($chars, 5);
    }
    if ($type != 4) {
        $chars = str_shuffle($chars);
        $str = substr($chars, 0, $len);
    } else {
        // 中文随机字
        for ($i = 0; $i < $len; $i++) {
            $str .= msubstr($chars, floor(mt_rand(0, mb_strlen($chars, 'utf-8') - 1)), 1);
        }
    }
    return $str;
}

function encodeHexStr($dataCoding, $realStr)
{

    if ($dataCoding == 15) {
        return strtoupper(bin2hex(iconv('UTF-8', 'GBK', $realStr)));
    } else if ($dataCoding == 3) {
        return strtoupper(bin2hex(iconv('UTF-8', 'ISO-8859-1', $realStr)));
    } else if ($dataCoding == 8) {
        return strtoupper(bin2hex(iconv('UTF-8', 'UCS-2', $realStr)));
    } else {
        return strtoupper(bin2hex(iconv('UTF-8', 'ASCII', $realStr)));
    }
}

/**
 * 调用发送短信方法(单发)  --数据中心版
 * @User yaokai
 * @param $template 短信模板键名
 * @param $parameters 短信模板变量
 * @param $strMobile 手机号码  [键：手机号 => 值：国家码]
 * @param $type      短信类型
 * @param $nationCode 国家码 默认86
 * @param $app_name app名称，默认无界商圈wjsq  经纪人agent
 * @param $is_md5  手机号是否加盐加密
 * @return int 发送状态，0 为发送失败，1 为发送成功
 */
function SendTemplateSMS($template, $strMobile, $type, $parameters = [], $nationCode = '86', $app_name = 'wjsq', $is_md5 = true)
{
    //国家码如果为空则默认给86 中国
    if (!$nationCode) {
        $nationCode = '86';
    }

    //拼接模板
    $content = trans('sms.' . $template, $parameters);
    if (app()->environment() === 'production' && (strpos($content, '验证码') !== false && strpos($content, '分钟内') !== false)) {//20
        //不是https的就不发短信
        if (!\Illuminate\Support\Facades\Request::secure()) {
            return 0;
        }
        $time = strtotime(date('Ymd'));
        $maxNum = \App\Models\Config::where('code', 'max_sms_num')->value('value');
//        //今日总条数
//        $num = \App\Models\LogSms::where('phone', $strMobile)
//            ->where('created_at', '>=', $time)
//            ->count();
//        //如果总条数大于预设条数则停止发送短信
//        if ($num >= $maxNum) {
//            return 0;
//        }

        //今日登陆短信验证码条数
        $builder = \App\Models\LogSms::where('type', 'sms_loginCode')
            ->where('created_at', '>=', $time);

        if ($is_md5) {
            $builder->where('non_reversible', $strMobile);
        } else {
            $non_reversible = encryptTel($strMobile);
            $builder->where('non_reversible', $non_reversible);
        }

        $login_num = $builder->count();

        //如果sms_loginCode的条数大于预设条数则停止发送短信
        if ($login_num > $maxNum) {
            return 0;
        }

    }

    if ($content === $template) {
        return 0;
    }

    return SendSMS($strMobile, $content, $type, $nationCode, $app_name,$is_md5);
}


/**
 * 发送短信  数据中心版
 * @User yaokai
 * @param $platform  短信平台，tencent 为腾讯云平台，santo为闪通短信平台
 * @param $strMobile 手机号码md5 值
 * @param $content   短信内容
 * @param $type      短信类型
 * @param $nationCode 国家码
 * @param $app_name app名称，默认无界商圈wjsq agent经纪人 c_crm  g_crm Todo 切记勿随意修改值 数据中心有相对应类型
 * @param $is_md5  手机号是否加盐加密
 * return int $str   发送状态，0 为发送失败，1 为发送成功
 */
function SendSMS($strMobile, $content, $type, $nationCode, $app_name, $is_md5)
{
    preg_match_all('/(\+)?(\d{1,10})/', $nationCode, $matches, PREG_SET_ORDER);
    if (isset($matches[0][2])) {
        $nationCode = $matches[0][2];
    } else {
        return false;
    }

    //读取配置
    $sms_platform = \App\Models\Config::cache();

    if ($is_md5){//加密的手机号
        $non_reversible = $strMobile;
        $phone = '';// XXX: 这里如果需要的话需要根据app_name去找相应的伪号码  暂可不做处理  展示的时候关联即可  yaokai 2018.1.11
        $url = config('system.data_center.hosts') . config('system.data_center.send-sms');
    } else {//未加密的手机
        $non_reversible = encryptTel($strMobile);
        $phone = pseudoTel($strMobile);
        $url = config('system.data_center.hosts') . config('system.data_center.direct-sms');
    }

    //验收环境不发短信
    if (app()->environment() === 'beta') {
        $str = 1;
    } elseif ($nationCode == '86' || \App::environment() == 'production') {//如果是线上的环境或者中国的号码

        //数据中心处理
        $datas = [
            'nation_code' => $nationCode,
            'non_reversible' => $strMobile,
            'content' => $content,
            'platform' => $app_name,//来源平台
            'sms_platform' => $sms_platform,//短信平台
            'type' => $type,
            'is_mass' => false,//单发
        ];

        //请求数据中心接口
        $result = json_decode(getHttpDataCenter($url, '', $datas));

        //如果异常则发送失败
        if (!$result || $result->status == false) {
            $str = 0;
        } else {
            $str = 1;
        }
    } else {
        $str = 1;
    }

    if (\Illuminate\Support\Facades\Auth::check()) {
        $uid = \Illuminate\Support\Facades\Auth::id();
    } else {
        $uid = 0;
    }

    $ip = getIP();


    \App\Models\LogSms::create(array(
        'uid' => $uid,
        'ip' => $ip,
        'client_id' => 1,
        'type' => $type,
        'content' => $content,
        'phone' => $phone,
        'status' => $str,
        'platform' => $sms_platform,
        'app_name' => $app_name,
        'nation_code' => $nationCode,
        'non_reversible' => $non_reversible,
    ));

    return $str;

}



/**
 * 调用发送短信方法(手机号加盐群发)  数据中心版
 * @User yaokai
 * @param $template 短信模板键名
 * @param $parameters 短信模板变量
 * @param $strMobile 手机号码  [键：手机号 => 值：国家码]
 * @param $type      短信类型
 * @param $app_name app名称，默认无界商圈wjsq  经纪人agent
 * @return int 发送状态，0 为发送失败，1 为发送成功
 */
function SendsTemplateSMS($template, $strMobile, $type, $parameters = [], $app_name = 'wjsq')
{

    //拼接模板
    $content = trans('sms.' . $template, $parameters);
    if (app()->environment() === 'production' && (strpos($content, '验证码') !== false && strpos($content, '分钟内') !== false)) {//20
        //不是https的就不发短信
        if (!\Illuminate\Support\Facades\Request::secure()) {
            return 0;
        }
    }

    if ($content === $template) {
        return 0;
    }

    return SendsSMS($strMobile, $content, $type, $app_name);
}

/**
 * 发送短信(手机号加盐群发)  数据中心版
 * @User yaokai
 * @param $platform  短信平台，tencent 为腾讯云平台，santo为闪通短信平台
 * @param $strMobile 手机号码
 * @param $content   短信内容
 * @param $type      短信类型
 * @param $nationCode 国家码
 * @param $app_name app名称，默认无界商圈wjsq agent经纪人 c_crm  g_crm Todo 切记勿随意修改值 数据中心有相对应类型
 * return int $str   发送状态，0 为发送失败，1 为发送成功
 */
function SendsSMS($strMobile, $content, $type, $app_name)
{
    //读取配置
    $sms_platform = \App\Models\Config::cache();

    //验收环境不发短信
    if (app()->environment() === 'beta') {
        $str = 1;
    } //如果是线上的环境或者中国的号码
    else{

        //数据中心处理
        $url = config('system.data_center.hosts') . config('system.data_center.send-sms');
        $datas = [
            'non_reversible' => json_encode($strMobile),
            'content' => $content,
            'platform' => $app_name,//来源平台
            'sms_platform' => $sms_platform,//短信平台
            'type' => $type,
            'is_mass' => true,//群发
        ];

        //请求数据中心接口
        $result = json_decode(getHttpDataCenter($url, '', $datas));

        //群发默认都是成功
        $str = 1;

    }


    if (\Illuminate\Support\Facades\Auth::check()) {
        $uid = \Illuminate\Support\Facades\Auth::id();
    } else {
        $uid = 0;
    }

    $ip = getIP();

    //构建数组  insert
    $sms = [];
    $i = 0;
    $time = time();
    foreach ($strMobile as $k => $v) {
        $sms[$i]['uid'] = $uid;
        $sms[$i]['ip'] = $ip;
        $sms[$i]['client_id'] = 1;
        $sms[$i]['type'] = $type;
        $sms[$i]['content'] = $content;
        $sms[$i]['phone'] = 0;
        $sms[$i]['non_reversible'] = $k;
        $sms[$i]['status'] = $str;
        $sms[$i]['platform'] = $sms_platform;
        $sms[$i]['app_name'] = $app_name;
        $sms[$i]['nation_code'] = $v;
        $sms[$i]['created_at'] = $time;
        $sms[$i]['updated_at'] = $time;
        ++$i;
    }

    //写入短信记录
    \App\Models\LogSms::insert($sms);

    return $str;

}

/*
 * 获取IP
 */
function getIP()
{
    /* 客户端IP */
    if (getenv('HTTP_CLIENT_IP') && strcasecmp(getenv('HTTP_CLIENT_IP'), 'unknown')) {
        $onlineip = getenv('HTTP_CLIENT_IP');
    } elseif (getenv('HTTP_X_FORWARDED_FOR') && strcasecmp(getenv('HTTP_X_FORWARDED_FOR'), 'unknown')) {
        $onlineip = getenv('HTTP_X_FORWARDED_FOR');
    } elseif (getenv('REMOTE_ADDR') && strcasecmp(getenv('REMOTE_ADDR'), 'unknown')) {
        $onlineip = getenv('REMOTE_ADDR');
    } elseif (isset($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR'] && strcasecmp($_SERVER['REMOTE_ADDR'], 'unknown')) {
        $onlineip = $_SERVER['REMOTE_ADDR'];
    } else {
        $onlineip = '127.0.0.1';
    }
    preg_match("/[\d\.]{7,15}/", $onlineip, $onlineipmatches);
    $ip = $onlineipmatches[0] ? $onlineipmatches[0] : 'unknown';
    return $ip;
}

/**
 * 截取utf-8格式的中文字符串
 *
 * @param $sourcestr 是要处理的字符串
 * @param $cutlength 为截取的长度(即字数)
 */
function cut_str($sourcestr, $cutlength, $dot = '...')
{

    $returnstr = '';
    $i = 0;
    $n = 0;
    $str_length = strlen($sourcestr); // 字符串的字节数
    while (($n < $cutlength) and ($i <= $str_length)) {
        $temp_str = substr($sourcestr, $i, 1);
        $ascnum = Ord($temp_str); // 得到字符串中第$i位字符的ascii码
        if ($ascnum >= 224)            // 如果ASCII位高与224，
        {
            $returnstr = $returnstr . substr($sourcestr, $i, 3); // 根据UTF-8编码规范，将3个连续的字符计为单个字符
            $i = $i + 3; // 实际Byte计为3
            $n++; // 字串长度计1
        } elseif ($ascnum >= 192)            // 如果ASCII位高与192，
        {
            $returnstr = $returnstr . substr($sourcestr, $i, 2); // 根据UTF-8编码规范，将2个连续的字符计为单个字符
            $i = $i + 2; // 实际Byte计为2
            $n++; // 字串长度计1
        } elseif ($ascnum >= 65 && $ascnum <= 90)            // 如果是大写字母，
        {
            $returnstr = $returnstr . substr($sourcestr, $i, 1);
            $i = $i + 1; // 实际的Byte数仍计1个
            $n++; // 但考虑整体美观，大写字母计成一个高位字符
        } else            // 其他情况下，包括小写字母和半角标点符号，
        {
            $returnstr = $returnstr . substr($sourcestr, $i, 1);
            $i = $i + 1; // 实际的Byte数计1个
            $n = $n + 0.5; // 小写字母和半角标点等与半个高位字符宽...
        }
    }
    if ($str_length > strlen($returnstr)) {
        $returnstr = $returnstr . $dot; // 超过长度时在尾处加上省略号
    }
    return $returnstr;
}

/**
 * 生成url
 * @param unknown_type $str
 * @param unknown_type $params
 * @return string
 */
function createUrl($str, $params = array(), $platform = "app")
{
    if ($platform == "web") {
        return url($str) . '?' . http_build_query($params);
    } else {
        return config('app.app_url') . $str . '?' . http_build_query($params);
    }
}

/**
 * 距离现在的时间差
 */
function timeDiff($source_time, $format = 'Y-m-d H:i:s')
{
    $diff = time() - $source_time;

    if ($diff <= 0) return '刚刚';//date($format,$source_time);
    $date1 = date('Y-m-d', time());
    $date2 = date('Y-m-d', $source_time);
    $time1 = strtotime($date1);
    $time2 = strtotime($date2);
    if ($date1 === $date2) {
        if ($diff < 60) return $diff . '秒前';
        if ($diff < 3600) return floor($diff / 60) . '分钟前';
        if ($diff < 86400) return '今天 ';//.date('H:i',$source_time);
    } else {
        if (strtotime("+1 day", $time2) == $time1) return '昨天 ';//.date('H:i',$source_time);
        if (strtotime("+2 day", $time2) == $time1) return '前天 ';//.date('H:i',$source_time);
        $day = floor(($time1 - $time2) / 86400);
        if ($day < 30) {
            return $day . '天前';
        } else if ($day < 365) {
            return floor($day / 30) . '个月前';
        } else {
            return floor($day / 365) . '年前';
        }
    }
}

/**
 * 计算两个时间戳间隔
 * @param $startTime
 * @param $endTime
 * @return array
 */
function timediff2($startTime, $nowTime)
{
    $s = $startTime - $nowTime;
    $hour = floor($s / 3600);
    $min = floor(($s - $hour * 3600) / 60);
    $sec = floor($s - $hour * 3600 - $min * 60);
    return array($hour, $min, $sec);
}

/**
 * 对象转数组
 * @param $obj
 * @return mixed
 */
function objToArray($obj)
{
    return (array)json_decode(json_encode($obj), true);
}

/**
 * 对手机号进行*处理
 */
function dealTel($tel)
{
    return substr_replace($tel, '****', 3, 4);
}

//PHP汉字转换拼音的类 用法：
//第二个参数留空则为gb1232编码
//第二个参数随意设置则为utf-8编码
function Pinyin($_String, $_Code = 'gbk')
{
    $_DataKey = "a|ai|an|ang|ao|ba|bai|ban|bang|bao|bei|ben|beng|bi|bian|biao|bie|bin|bing|bo|bu|ca|cai|can|cang|cao|ce|ceng|cha" .
        "|chai|chan|chang|chao|che|chen|cheng|chi|chong|chou|chu|chuai|chuan|chuang|chui|chun|chuo|ci|cong|cou|cu|" .
        "cuan|cui|cun|cuo|da|dai|dan|dang|dao|de|deng|di|dian|diao|die|ding|diu|dong|dou|du|duan|dui|dun|duo|e|en|er" .
        "|fa|fan|fang|fei|fen|feng|fo|fou|fu|ga|gai|gan|gang|gao|ge|gei|gen|geng|gong|gou|gu|gua|guai|guan|guang|gui" .
        "|gun|guo|ha|hai|han|hang|hao|he|hei|hen|heng|hong|hou|hu|hua|huai|huan|huang|hui|hun|huo|ji|jia|jian|jiang" .
        "|jiao|jie|jin|jing|jiong|jiu|ju|juan|jue|jun|ka|kai|kan|kang|kao|ke|ken|keng|kong|kou|ku|kua|kuai|kuan|kuang" .
        "|kui|kun|kuo|la|lai|lan|lang|lao|le|lei|leng|li|lia|lian|liang|liao|lie|lin|ling|liu|long|lou|lu|lv|luan|lue" .
        "|lun|luo|ma|mai|man|mang|mao|me|mei|men|meng|mi|mian|miao|mie|min|ming|miu|mo|mou|mu|na|nai|nan|nang|nao|ne" .
        "|nei|nen|neng|ni|nian|niang|niao|nie|nin|ning|niu|nong|nu|nv|nuan|nue|nuo|o|ou|pa|pai|pan|pang|pao|pei|pen" .
        "|peng|pi|pian|piao|pie|pin|ping|po|pu|qi|qia|qian|qiang|qiao|qie|qin|qing|qiong|qiu|qu|quan|que|qun|ran|rang" .
        "|rao|re|ren|reng|ri|rong|rou|ru|ruan|rui|run|ruo|sa|sai|san|sang|sao|se|sen|seng|sha|shai|shan|shang|shao|" .
        "she|shen|sheng|shi|shou|shu|shua|shuai|shuan|shuang|shui|shun|shuo|si|song|sou|su|suan|sui|sun|suo|ta|tai|" .
        "tan|tang|tao|te|teng|ti|tian|tiao|tie|ting|tong|tou|tu|tuan|tui|tun|tuo|wa|wai|wan|wang|wei|wen|weng|wo|wu" .
        "|xi|xia|xian|xiang|xiao|xie|xin|xing|xiong|xiu|xu|xuan|xue|xun|ya|yan|yang|yao|ye|yi|yin|ying|yo|yong|you" .
        "|yu|yuan|yue|yun|za|zai|zan|zang|zao|ze|zei|zen|zeng|zha|zhai|zhan|zhang|zhao|zhe|zhen|zheng|zhi|zhong|" .
        "zhou|zhu|zhua|zhuai|zhuan|zhuang|zhui|zhun|zhuo|zi|zong|zou|zu|zuan|zui|zun|zuo";
    $_DataValue = "-20319|-20317|-20304|-20295|-20292|-20283|-20265|-20257|-20242|-20230|-20051|-20036|-20032|-20026|-20002|-19990" .
        "|-19986|-19982|-19976|-19805|-19784|-19775|-19774|-19763|-19756|-19751|-19746|-19741|-19739|-19728|-19725" .
        "|-19715|-19540|-19531|-19525|-19515|-19500|-19484|-19479|-19467|-19289|-19288|-19281|-19275|-19270|-19263" .
        "|-19261|-19249|-19243|-19242|-19238|-19235|-19227|-19224|-19218|-19212|-19038|-19023|-19018|-19006|-19003" .
        "|-18996|-18977|-18961|-18952|-18783|-18774|-18773|-18763|-18756|-18741|-18735|-18731|-18722|-18710|-18697" .
        "|-18696|-18526|-18518|-18501|-18490|-18478|-18463|-18448|-18447|-18446|-18239|-18237|-18231|-18220|-18211" .
        "|-18201|-18184|-18183|-18181|-18012|-17997|-17988|-17970|-17964|-17961|-17950|-17947|-17931|-17928|-17922" .
        "|-17759|-17752|-17733|-17730|-17721|-17703|-17701|-17697|-17692|-17683|-17676|-17496|-17487|-17482|-17468" .
        "|-17454|-17433|-17427|-17417|-17202|-17185|-16983|-16970|-16942|-16915|-16733|-16708|-16706|-16689|-16664" .
        "|-16657|-16647|-16474|-16470|-16465|-16459|-16452|-16448|-16433|-16429|-16427|-16423|-16419|-16412|-16407" .
        "|-16403|-16401|-16393|-16220|-16216|-16212|-16205|-16202|-16187|-16180|-16171|-16169|-16158|-16155|-15959" .
        "|-15958|-15944|-15933|-15920|-15915|-15903|-15889|-15878|-15707|-15701|-15681|-15667|-15661|-15659|-15652" .
        "|-15640|-15631|-15625|-15454|-15448|-15436|-15435|-15419|-15416|-15408|-15394|-15385|-15377|-15375|-15369" .
        "|-15363|-15362|-15183|-15180|-15165|-15158|-15153|-15150|-15149|-15144|-15143|-15141|-15140|-15139|-15128" .
        "|-15121|-15119|-15117|-15110|-15109|-14941|-14937|-14933|-14930|-14929|-14928|-14926|-14922|-14921|-14914" .
        "|-14908|-14902|-14894|-14889|-14882|-14873|-14871|-14857|-14678|-14674|-14670|-14668|-14663|-14654|-14645" .
        "|-14630|-14594|-14429|-14407|-14399|-14384|-14379|-14368|-14355|-14353|-14345|-14170|-14159|-14151|-14149" .
        "|-14145|-14140|-14137|-14135|-14125|-14123|-14122|-14112|-14109|-14099|-14097|-14094|-14092|-14090|-14087" .
        "|-14083|-13917|-13914|-13910|-13907|-13906|-13905|-13896|-13894|-13878|-13870|-13859|-13847|-13831|-13658" .
        "|-13611|-13601|-13406|-13404|-13400|-13398|-13395|-13391|-13387|-13383|-13367|-13359|-13356|-13343|-13340" .
        "|-13329|-13326|-13318|-13147|-13138|-13120|-13107|-13096|-13095|-13091|-13076|-13068|-13063|-13060|-12888" .
        "|-12875|-12871|-12860|-12858|-12852|-12849|-12838|-12831|-12829|-12812|-12802|-12607|-12597|-12594|-12585" .
        "|-12556|-12359|-12346|-12320|-12300|-12120|-12099|-12089|-12074|-12067|-12058|-12039|-11867|-11861|-11847" .
        "|-11831|-11798|-11781|-11604|-11589|-11536|-11358|-11340|-11339|-11324|-11303|-11097|-11077|-11067|-11055" .
        "|-11052|-11045|-11041|-11038|-11024|-11020|-11019|-11018|-11014|-10838|-10832|-10815|-10800|-10790|-10780" .
        "|-10764|-10587|-10544|-10533|-10519|-10331|-10329|-10328|-10322|-10315|-10309|-10307|-10296|-10281|-10274" .
        "|-10270|-10262|-10260|-10256|-10254";
    $_TDataKey = explode('|', $_DataKey);
    $_TDataValue = explode('|', $_DataValue);
    $_Data = (PHP_VERSION >= '5.0') ? array_combine($_TDataKey, $_TDataValue) : _Array_Combine($_TDataKey, $_TDataValue);
    arsort($_Data);
    reset($_Data);
    if ($_Code != 'gb2312') $_String = _U2_Utf8_Gb($_String);
    $_Res = '';
    for ($i = 0; $i < strlen($_String); $i++) {
        $_P = ord(substr($_String, $i, 1));
        if ($_P > 160) {
            $_Q = ord(substr($_String, ++$i, 1));
            $_P = $_P * 256 + $_Q - 65536;
        }
        $_Res .= _Pinyin($_P, $_Data);
    }
    return preg_replace("/[^a-z0-9]*/", '', $_Res);
}

function _Pinyin($_Num, $_Data)
{
    if ($_Num > 0 && $_Num < 160) return chr($_Num);
    elseif ($_Num < -20319 || $_Num > -10247) return '';
    else {
        foreach ($_Data as $k => $v) {
            if ($v <= $_Num) break;
        }
        return $k;
    }
}

function _U2_Utf8_Gb($_C)
{
    $_String = '';
    if ($_C < 0x80) $_String .= $_C;
    elseif ($_C < 0x800) {
        $_String .= chr(0xC0 | $_C >> 6);
        $_String .= chr(0x80 | $_C & 0x3F);
    } elseif ($_C < 0x10000) {
        $_String .= chr(0xE0 | $_C >> 12);
        $_String .= chr(0x80 | $_C >> 6 & 0x3F);
        $_String .= chr(0x80 | $_C & 0x3F);
    } elseif ($_C < 0x200000) {
        $_String .= chr(0xF0 | $_C >> 18);
        $_String .= chr(0x80 | $_C >> 12 & 0x3F);
        $_String .= chr(0x80 | $_C >> 6 & 0x3F);
        $_String .= chr(0x80 | $_C & 0x3F);
    }
    return @iconv('UTF-8', 'GB2312//TRANSLIT//IGNORE', $_String);
}

function _Array_Combine($_Arr1, $_Arr2)
{
    for ($i = 0; $i < count($_Arr1); $i++) $_Res[$_Arr1[$i]] = $_Arr2[$i];
    return $_Res;
}

//php获取中文字符拼音首字母  经纪人端已弃用
function getFirstCharter($str)
{
    if (empty($str)) {
        return '';
    }
    $fchar = ord($str{0});
    if ($fchar >= ord('A') && $fchar <= ord('z')) return strtoupper($str{0});

    if (strlen($str) == 1) {
        if ($fchar > ord('z') || $fchar < ord('A')) {
            return null;
        }
    }

    $str = mb_detect_encoding($str, ['UTF-8', 'gbk']);

    if (!in_array($str, ['UTF-8', 'gbk'])) {
        return null;
    }

    $s1 = @iconv('UTF-8', 'gbk//TRANSLIT//IGNORE', $str);
    $s2 = @iconv('gbk', 'UTF-8//TRANSLIT//IGNORE', $s1);
    $s = $s2 == $str ? $s1 : $str;
    $asc = ord($s{0}) * 256 + ord($s{1}) - 65536;
    if ($asc >= -20319 && $asc <= -20284) return 'A';
    if ($asc >= -20283 && $asc <= -19776) return 'B';
    if ($asc >= -19775 && $asc <= -19219) return 'C';
    if ($asc >= -19218 && $asc <= -18711) return 'D';
    if ($asc >= -18710 && $asc <= -18527) return 'E';
    if ($asc >= -18526 && $asc <= -18240) return 'F';
    if ($asc >= -18239 && $asc <= -17923) return 'G';
    if ($asc >= -17922 && $asc <= -17418) return 'H';
    if ($asc >= -17417 && $asc <= -16475) return 'J';
    if ($asc >= -16474 && $asc <= -16213) return 'K';
    if ($asc >= -16212 && $asc <= -15641) return 'L';
    if ($asc >= -15640 && $asc <= -15166) return 'M';
    if ($asc >= -15165 && $asc <= -14923) return 'N';
    if ($asc >= -14922 && $asc <= -14915) return 'O';
    if ($asc >= -14914 && $asc <= -14631) return 'P';
    if ($asc >= -14630 && $asc <= -14150) return 'Q';
    if ($asc >= -14149 && $asc <= -14091) return 'R';
    if ($asc >= -14090 && $asc <= -13319) return 'S';
    if ($asc >= -13318 && $asc <= -12839) return 'T';
    if ($asc >= -12838 && $asc <= -12557) return 'W';
    if ($asc >= -12556 && $asc <= -11848) return 'X';
    if ($asc >= -11847 && $asc <= -11056) return 'Y';
    if ($asc >= -11055 && $asc <= -10247) return 'Z';
    return null;
}

/**
 * @param $str
 * @return mixed
 * url上面去除域名
 */
function removeDomainStr($str)
{
    return str_replace(config('app.base_url'), "", $str);
}

/**
 * @param $totalPage 总页数
 * @param $currentPage
 * @param $total
 * @param $url
 * @return string
 */
function loadPage($totalPage, $currentPage, $total)
{
    $url = preg_replace('/[?|&]page=(\d+)/i', '', $_SERVER['REQUEST_URI']);
    $nextPage = $currentPage + 1;
    $str = '';
    if ($currentPage != 1) {
        $lastPage = $currentPage - 1;
        $str .= '<a href="' . url($url) . '"><<首页</a>';
        if (strpos($url, '?') !== false) {
            $str .= '<a href="' . url($url . '&page=' . $lastPage) . '"><上一页</a>';
        } else {
            $str .= '<a href="' . url($url . '?page=' . $lastPage) . '"><上一页</a>';
        }
    }
    $str .= $currentPage . '-' . $totalPage . '页，共' . $total . '条';
    if ($currentPage != $totalPage) {
        if (strpos($url, '?') !== false) {
            $str .= '<a href="' . url($url . '&page=' . $nextPage) . '">下一页></a>';
            $str .= '<a href="' . url($url . '&page=' . $totalPage) . '">尾页>></a>';
        } else {
            $str .= '<a href="' . url($url . '?page=' . $nextPage) . '">下一页></a>';
            $str .= '<a href="' . url($url . '?page=' . $totalPage) . '">尾页>></a>';
        }
    }
    return $str;
}

/**
 * 将一个平面的二维数组按照指定的字段转换为树状结构
 *
 * 用法：
 *
 * @code php
 * $rows = array(
 * array('id' => 1, 'value' => '1-1', 'parent' => 0),
 * array('id' => 2, 'value' => '2-1', 'parent' => 0),
 * array('id' => 3, 'value' => '3-1', 'parent' => 0),
 *
 * array('id' => 7, 'value' => '2-1-1', 'parent' => 2),
 * array('id' => 8, 'value' => '2-1-2', 'parent' => 2),
 * array('id' => 9, 'value' => '3-1-1', 'parent' => 3),
 * array('id' => 10, 'value' => '3-1-1-1', 'parent' => 9),
 * );
 *
 * $tree = Helper_Array::tree($rows, 'id', 'parent', 'nodes');
 *
 * dump($tree);
 * // 输出结果为：
 * // array(
 * // array('id' => 1, ..., 'nodes' => array()),
 * // array('id' => 2, ..., 'nodes' => array(
 * //        array(..., 'parent' => 2, 'nodes' => array()),
 * //        array(..., 'parent' => 2, 'nodes' => array()),
 * // ),
 * // array('id' => 3, ..., 'nodes' => array(
 * //        array('id' => 9, ..., 'parent' => 3, 'nodes' => array(
 * // array(..., , 'parent' => 9, 'nodes' => array(),
 * //        ),
 * // ),
 * // )
 * @endcode
 *
 * 如果要获得任意节点为根的子树，可以使用 $refs 参数：
 * @code php
 * $refs = null;
 * $tree = Helper_Array::tree($rows, 'id', 'parent', 'nodes', $refs);
 *
 * // 输出 id 为 3 的节点及其所有子节点
 * $id = 3;
 * dump($refs[$id]);
 * @endcode
 *
 * @param array $arr
 *        数据源
 * @param string $key_node_id
 *        节点ID字段名
 * @param string $key_parent_id
 *        节点父ID字段名
 * @param string $key_childrens
 *        保存子节点的字段名
 * @param boolean $refs
 *        是否在返回结果中包含节点引用
 *
 *        return array 树形结构的数组
 */
function toTree($arr, $key_node_id, $key_parent_id = 'parent_id', $key_childrens = 'children', $treeIndex = false, & $refs = null)
{

    $refs = array();
    foreach ($arr as $offset => $row) {
        $arr[$offset][$key_childrens] = array();
        $refs[$row[$key_node_id]] = &$arr[$offset];
    }

    $tree = array();
    foreach ($arr as $offset => $row) {
        $parent_id = $row[$key_parent_id];
        if ($parent_id) {
            if (!isset($refs[$parent_id])) {
                if ($treeIndex) {
                    $tree[$offset] = &$arr[$offset];
                } else {
                    $tree[] = &$arr[$offset];
                }
                continue;
            }
            $parent = &$refs[$parent_id];
            if ($treeIndex) {
                $parent[$key_childrens][$offset] = &$arr[$offset];
            } else {
                $parent[$key_childrens][] = &$arr[$offset];
            }
        } else {
            if ($treeIndex) {
                $tree[$offset] = &$arr[$offset];
            } else {
                $tree[] = &$arr[$offset];
            }
        }
    }

    return $tree;
}

/**
 * 递归法寻找家谱树
 */
function familyTree($arr, $upid)
{
    $trees = [];

    foreach ($arr as $k => $v) {
        if (is_object($v)) {
            $id = $v->id;
            $vupid = $v->upid;
        } elseif (is_array($v)) {
            $id = $v['id'];
            $vupid = $v['upid'];
        } else {
            return false;
        }

        if ($id == $upid) {
            $trees[] = $v;
            $trees = array_merge($trees, familyTree($arr, $vupid));
        }
    }

    return $trees;
}

/*
 * 格式化金钱(不管是小数还是整数)
 */
function doFormatMoney($money)
{
    $relMoney = '';
    $moneyArr = explode('.' , $money);
    if(count($moneyArr) == 1){
        $relMoney = formatIntMoney($moneyArr[0]);
    }
    else if(count($moneyArr) == 2){
        $intPart = formatIntMoney($moneyArr[0]);
        $decimalPart = formatIntMoney(strrev($moneyArr[1]));
        $relMoney = $intPart .'.'. strrev($decimalPart);
    }
    else{
        $relMoney = false;
    }
    return $relMoney;
}

//对整数进行金钱格式化
function formatIntMoney($intMoney){
    $tmpMoney = strrev($intMoney);
    $formatMoney = '';
    for ($i = 3; $i < strlen($intMoney); $i += 3) {
        $formatMoney .= substr($tmpMoney, 0, 3) . ',';
        $tmpMoney = substr($tmpMoney, 3);
    }
    $formatMoney .= $tmpMoney;
    return strrev($formatMoney);
}


/**
 * 根据账号创建时间-当前时间 生成周期
 * $begin,$end 均为unix时间戳
 * $month int  为周期长 12的因子
 */
function getPeroid($begin, $end, $month = 6)
{
    $end = isset($end) ? $end : time();
    $begin_year = date('Y', $begin);
    $end_year = date('Y', $end);
    $end_month = date('m', $end);
    $current_year = $begin_year;
    $peroid = array();
    for ($current_year; $current_year < $end_year; $current_year++) {
        for ($i = 0; $i < 12; $i += $month) {

            $peroid[] = [$current_year . (doFormatMonth($i + 1)), $current_year . (doFormatMonth($i + $month))];
        }
    }
    for ($i = 0; $i < $end_month; $i += $month) {
        $peroid[] = [$current_year . (doFormatMonth($i + 1)), $current_year . (doFormatMonth($i + $month))];
    }
    foreach ($peroid as $k => $v) {
        $peroid[$k] = implode($v, '--');
    }
    return $peroid;
}

/**
 * 格式化月份
 */
function doFormatMonth($month)
{
    return $month < 10 ? '0' . $month : $month;
}

/**
 * 文件大小格式
 */
function human_filesize($bytes, $decimal = 2)
{
    $suffix = ['B', 'KB', 'MB', 'G', 'TB'];
    $factor = floor((strlen($bytes) - 1) / 3);
    return sprintf("%.{$decimal}f", $bytes / pow(1024, $factor)) . $suffix[$factor];
}

/**
 * 对数组排序
 *
 * @param $para 排序前的数组
 * return 排序后的数组
 */
function argSort($para)
{
    ksort($para);
    reset($para);

    return $para;
}


/**
 * RSA签名
 *
 * @param $data 待签名数据
 * @param $private_key_path 商户私钥文件路径
 * return 签名结果
 */
function rsaSign($data, $private_key_path)
{
    $priKey = file_get_contents($private_key_path);
    $res = openssl_get_privatekey($priKey);
    openssl_sign($data, $sign, $res);
    openssl_free_key($res);
    //base64编码
    $sign = base64_encode($sign);

    return $sign;
}


/**
 * 除去数组中的空值和签名参数
 *
 * @param $para 签名参数组
 * return 去掉空值与签名参数后的新签名参数组
 */
function paraFilter($para)
{
    $para_filter = array();
    while (list ($key, $val) = each($para)) {
        if ($key == "sign" || $key == "sign_type" || $val == "") {
            continue;
        } else {
            $para_filter[$key] = $para[$key];
        }
    }

    return $para_filter;
}

/* 时间格式化 */
function toDate($time, $format = 'Y-m-d H:i:s')
{
    if (empty($time)) {
        return '';
    }
    $format = str_replace('#', ':', $format);
    return date(($format), $time);
}

function createLinkstring($para, $quotes = 1)
{
    $arg = "";
    while (list ($key, $val) = each($para)) {
        if ($quotes == 1) {
            $arg .= $key . "=" . '"' . $val . '"' . "&";
        } elseif ($quotes == 2) {
            $arg .= $key . "=" . urlencode($val) . "&";
        } else {
            $arg .= $key . "=" . $val . "&";
        }
    }
    //去掉最后一个&字符
    $arg = substr($arg, 0, count($arg) - 2);

    //如果存在转义字符，那么去掉转义
    if (get_magic_quotes_gpc()) {
        $arg = stripslashes($arg);
    }

    return $arg;
}


/**
 * 远程获取数据，POST模式
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
function getHttpResponsePOST($url, $cacert_url, $para, $input_charset = '')
{
    if (trim($input_charset) != '') {
        $url = $url . "_input_charset=" . $input_charset;
    }
    $curl = curl_init($url);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, true);//SSL证书认证
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2);//严格认证
    curl_setopt($curl, CURLOPT_CAINFO, $cacert_url);//证书地址
    curl_setopt($curl, CURLOPT_HEADER, 0); // 过滤HTTP头
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);// 显示输出结果
    curl_setopt($curl, CURLOPT_POST, true); // post传输数据
    curl_setopt($curl, CURLOPT_POSTFIELDS, $para);// post传输数据
    $responseText = curl_exec($curl);
//    print_r($responseText);
//    exit;
//    print_r(curl_error($curl));exit;//如果执行curl过程中出现异常，可打开此开关，以便查看异常内容
    curl_close($curl);
    return $responseText;
}

/**
 * 文件下载
 */
function download($name, $dir)
{
//    $file_name = "xxx.rar";     //下载文件名
//    $file_dir = "./up/";        //下载文件存放目录
    $file_name = $name;     //下载文件名
    $file_dir = $dir;        //下载文件存放目录
//检查文件是否存在
    if (!file_exists($file_dir . $file_name)) {
        echo "文件找不到";
        exit ();
    } else {
        //打开文件
        $file = fopen($file_dir . $file_name, "r");
        //输入文件标签
        Header("Content-type: application/octet-stream");
        Header("Accept-Ranges: bytes");
        Header("Accept-Length: " . filesize($file_dir . $file_name));
        Header("Content-Disposition: attachment; filename=" . $file_name);
        //输出文件内容
        //读取文件内容并直接输出到浏览器
        echo fread($file, filesize($file_dir . $file_name));
        fclose($file);
        exit ();
    }
}

/**
 * 生成系统消息
 * @param $uid
 * @param $title
 * @param $content
 * @param $ext
 * @param $end
 * @param $type
 */
function createMessage($uid, $title, $content, $ext, $end, $type, $delay = 0, $post_id = 0)
{
    $obj = new \App\Http\Controllers\Api\MessageController();
    $obj->createMessage($uid, $title, $content, $ext, $end, $type, $delay, $post_id);
}

/**
 * 个推消息：发送点击打开应用模板消息
 * @param $title     通知栏标题  string
 * @param $text      通知栏内容  string
 * @param $content   透传内容，供APP接收处理 string
 * @param $duration  消息展示时间区间，开始时间与结束时间 array(Y-m-d H:i:s,Y-m-d H:i:s)
 * @param $users     要发的用户或用户列表，为空时发送全部用户
 * 返回值：array|string|false
 */
function pushInfo($title, $text, $content, $duration, $users = null)
{
    $getuiInfo = new \App\Services\GeTui\Template();
    $getuiInfo->sendNotification($title, $text, $content, $duration, $users);
}


//二维数组去掉重复值
function mult_unique($array)
{
    $return = array();
    foreach ($array as $key => $v) {
        if (!in_array($v, $return)) {
            $return[] = $v;
        }
    }
    return $return;
}


/**
 * 转化成一位小数万单位
 * @param unknown_type $number
 * @return unknown|string
 */
function format_number($number)
{
    if ($number < 9999) {
        return $number;
    } elseif ($number < 100000000 && $number >= 10000) {
        $w = number_format($number / 10000, 2);
        return $w . '万';
    } else {
        $w = number_format($number / 100000000, 2);
        return $w . '亿';
    }
}

/**
 * 以队列形式发送短信
 * @param $strMobile
 * @param $content
 * @param $type
 * @param string $sendType
 * @param int $delay
 */
function sendSMSbyJob($strMobile, $content, $type, $sendType = '', $delay = 0)
{
    $obj = new \App\Http\Controllers\Api\SmsController();
    $obj->sendSMS($strMobile, $content, $type, $sendType, $delay);
}

// 把数组中的null全部转化成空字符串
function converNullToString(&$o)
{
    if (!is_array($o) && is_null($o)) return '';
    foreach ($o as $k => $v) {
        $o[$k] = is_array($v) ? converNullToString($v) : (is_null($v) ? '' : $v);
    }

    return $o;
}


/**
 * 截取字符串，其余用...表示
 */
function substrwithdot($str, $n)
{
    $count = mb_strlen($str, 'utf-8');
    $str = mb_substr($str, 0, $n, 'utf-8');

    if ($count > $n) {
        $str = $str . str_repeat('.', 3);
    }
    return $str;
}

/**
 * 检验一个数是不是自然数 包含0
 */
function isInt($num)
{
    if (preg_match("/^[1-9]\d|0*$/", $num) != 1)//当不为整数时
    {
        return false;
    } else {
        return true;
    }
}

function week($time, $name = '周')
{
    $weekarray = array("日", "一", "二", "三", "四", "五", "六");
    return $name . $weekarray[date("w", $time)];
}

/**
 * 根据数组中排序
 * @param $direction 'SORT_DESC' 'SORT_ASC'
 * @param $field '要排序的字段'
 * @param array $arr
 * @return array
 */
function arraySort($field, array $arr = [], $direction = 'SORT_DESC')
{
    if (empty($arr)) return [];
    $sort = [
        'direction' => $direction,
        'field' => $field
    ];
    $arrSort = [];
    foreach ($arr AS $uniqid => $row) {
        foreach ($row AS $key => $value) {
            $arrSort[$key][$uniqid] = $value;
        }
    }
    if ($sort['direction']) {
        array_multisort($arrSort[$sort['field']], constant($sort['direction']), $arr);
    }
    return $arr;
}

/**
 * 调用发送通知方法
 * @user yaokai
 * 作用：发送点击打开应用模板消息
 * 参数：$title     通知栏标题  string
 *      $text      通知栏内容  string
 *      $content   json串透传内容，供APP接收处理 string
 *      $users     要发的用户或用户列表，为空时发送全部用户 Illuminate\Database\Eloquent\Model|Illuminate\Database\Eloquent\Collection|null
 *      $duration  消息展示时间区间，开始时间与结束时间 array(Y-m-d H:i:s,Y-m-d H:i:s)
 * 返回值：array|string|false
 */
function SendTemplateNotifi($title,$titleparames,$text,$parameters, $content, $users, $duration = null, $is_agent = false)
{
    //拼接模板
    $title = trans('notification.' . $title, $titleparames);
    $text = trans('notification.' . $text, $parameters);

    return send_notification($title, $text, $content, $users, $duration, $is_agent);
}

/*
 * 作用：发送点击打开应用模板消息
 * 参数：$title     通知栏标题  string
 *      $text      通知栏内容  string
 *      $content   json串透传内容，供APP接收处理 string
 *      $users     要发的用户或用户列表，为空时发送全部用户 Illuminate\Database\Eloquent\Model|Illuminate\Database\Eloquent\Collection|null
 *      $duration  消息展示时间区间，开始时间与结束时间 array(Y-m-d H:i:s,Y-m-d H:i:s)
 * 返回值：array|string|false
 */
function send_notification($title, $text, $content, $users, $duration = null, $is_agent=false)
{
    return \App\Services\GeTui\GeTui::sendNotification($title, $text, $content, $duration, $users, $is_agent);
}

/**
 * 调用发送透传方法
 * @user yaokai
 * @param $template 短信模板键名
 * @param $parameters 短信模板变量
 * @param $users    要发的用户或用户列表，为空时发送全部用户 Illuminate\Database\Eloquent\Model|Illuminate\Database\Eloquent\Collection|null
 * @param $duration 消息展示时间区间，开始时间与结束时间 array(Y-m-d H:i:s,Y-m-d H:i:s)
 * @param $is_agent app名称，默认无界商圈wjsq
 */
function SendTemplateTrans($template,$parameters, $users, $duration = null, $is_agent=false)
{
    //拼接模板
    $content = trans('transmisson.' . $template, $parameters);


    return send_transmission($content, $users, $duration, $is_agent);
}


/*
 * 作用：发送透传消息
 * 参数：$content   json串透传内容，供APP接收处理 string
 *      $users     要发的用户或用户列表，为空时发送全部用户 Illuminate\Database\Eloquent\Model|Illuminate\Database\Eloquent\Collection|null
 *      $duration  消息展示时间区间，开始时间与结束时间 array(Y-m-d H:i:s,Y-m-d H:i:s)
 * 返回值：array|string|false
 */
function send_transmission($content, $users, $duration = null, $is_agent=false)
{
    return \App\Services\GeTui\GeTui::sendTransmission($content, $duration, $users, $is_agent);
}


/*
 * 作用：发送透传消息和通知
 * 参数：$content   json串透传内容，供APP接收处理 string
 *      $users     要发的用户或用户列表，为空时发送全部用户 Illuminate\Database\Eloquent\Model|Illuminate\Database\Eloquent\Collection|null
 *      $duration  消息展示时间区间，开始时间与结束时间 array(Y-m-d H:i:s,Y-m-d H:i:s)
 * 返回值：array|string|false
 */
function send_trans_and_notice($content, $users, $duration = null, $is_agent=false)
{
    return \App\Services\GeTui\GeTui::sendTransAndNotice($content, $duration, $users, $is_agent);
}



/*
 * 作用：舍弃小数点后的0
 * 参数：
 *
 *
 * 返回值：array|string|false
 */
function abandonZero($num)
{
    if (ceil($num) == floor($num)) {
        $num = (int)$num;
        return (string)$num;
    } else {
        return $num;
    }
}

/*
 * 格式化金钱
 */
function formatMoney($num)
{
    $num_arr = explode('.', $num);
    if ($num_arr[1] == '00') {
        return $num_arr[0];
    }

    $dotnum = $num_arr[1];
    if (strpos($dotnum, '0') == 1) {
        $temp = str_replace('0', '', $dotnum);
        return $num_arr[0] . '.' . $temp;
    } else {
        return $num;
    }
}

/*
 * 作用：用*号替代姓名除第一个字之外的字符
 * 参数：
 *
 *
 * 返回值：string
 */
function starReplace($name, $num = 0)
{
    //去除空格
    $name = trim($name);

    if ($num && mb_strlen($name, 'UTF-8') > $num) {
        return mb_substr($name, 0, 4) . '*';
    }

    if ($num && mb_strlen($name, 'UTF-8') <= $num) {
        return $name;
    }

    $doubleSurname = [
        '欧阳', '太史', '端木', '上官', '司马', '东方', '独孤', '南宫',
        '万俟', '闻人', '夏侯', '诸葛', '尉迟', '公羊', '赫连', '澹台', '皇甫', '宗政', '濮阳',
        '公冶', '太叔', '申屠', '公孙', '慕容', '仲孙', '钟离', '长孙', '宇文', '司徒', '鲜于',
        '司空', '闾丘', '子车', '亓官', '司寇', '巫马', '公西', '颛孙', '壤驷', '公良', '漆雕', '乐正',
        '宰父', '谷梁', '拓跋', '夹谷', '轩辕', '令狐', '段干', '百里', '呼延', '东郭', '南门', '羊舌',
        '微生', '公户', '公玉', '公仪', '梁丘', '公仲', '公上', '公门', '公山', '公坚', '左丘', '公伯',
        '西门', '公祖', '第五', '公乘', '贯丘', '公皙', '南荣', '东里', '东宫', '仲长', '子书', '子桑',
        '即墨', '达奚', '褚师', '吴铭'
    ];

    $surname = mb_substr($name, 0, 2);
    if (in_array($surname, $doubleSurname)) {
        $name = mb_substr($name, 0, 2) . str_repeat('*', (mb_strlen($name, 'UTF-8') - 2));
    } else {
        $name = mb_substr($name, 0, 1) . str_repeat('*', (mb_strlen($name, 'UTF-8') - 1));
    }


    return $name;
}

// 把数据中的null全部转化成空字符串
function nullToString(&$o)
{
    if ($o === 0) return $o;
    if ($o === '0') return $o;
    if (is_array($o) && count($o) == 0) return $o;
    if (is_object($o) && count($o) == 0) return $o;

    if (!is_array($o) && !is_object($o) && is_null($o)) return '';

    foreach ($o as $k => $v) {
        if (is_array($o)) {
            $o[$k] = (is_array($v) || is_object($v)) ? nullToString($v) : (is_null($v) ? '' : $v);
        } elseif (is_object($o)) {
            $o->$k = (is_array($v) || is_object($v)) ? nullToString($v) : (is_null($v) ? '' : $v);
        }
    }

    return $o;
}


/*
 * 作用：从以DB写法获得的结果集中取得某一键值
 * 参数：
 *
 *
 * 返回值：string
 */
function getValueFromDb($collection, $key)
{
    if (!count($collection)) {
        return [];
    }
    $res = [];
    foreach ($collection as $k => $v) {
        $res[] = $v->$key;
    }

    return $res;
}

/*
 * 二维数组去重
 */
function unique_arr($array2D, $stkeep = false, $ndformat = true)
{
    // 判断是否保留一级数组键 (一级数组键可以为非数字)
    if ($stkeep) $stArr = array_keys($array2D);

    // 判断是否保留二级数组键 (所有二级数组键必须相同)
    if ($ndformat) $ndArr = array_keys(end($array2D));

    //降维,也可以用implode,将一维数组转换为用逗号连接的字符串
    foreach ($array2D as $v) {
        $v = join(",", $v);
        $temp[] = $v;
    }

    //去掉重复的字符串,也就是重复的一维数组
    $temp = array_unique($temp);

    //再将拆开的数组重新组装
    foreach ($temp as $k => $v) {
        if ($stkeep) $k = $stArr[$k];
        if ($ndformat) {
            $tempArr = explode(",", $v);
            foreach ($tempArr as $ndkey => $ndval) $output[$k][$ndArr[$ndkey]] = $ndval;
        } else $output[$k] = explode(",", $v);
    }

    return $output;
}

/*
 * 递归将结果中包含http的域名变更为https
 */
function httpToHttps($data, $pattern = '', $needle = '', $replace = '')
{
    if (empty($data)) {
        return $data;
    }

    //字符串
    if (is_string($data)) {
        if (preg_match($pattern, $data)) {
            return str_replace($needle, $replace, $data);
        }
    }

    //数组
    if (is_array($data)) {
        foreach ($data as &$item) {
            $item = httpToHttps($item, $pattern, $needle, $replace);
        }
    }

    //对象
    if (is_object($data)) {
        $data = objToArray($data);
        foreach ($data as &$value) {
            $value = httpToHttps($value, $pattern, $needle, $replace);
        }
    }

    return $data;
}

/*
 * 调用自己服务器上的接口生成短链接
 */
function shortUrl($longUrl)
{
    //$url = "https://api.weibo.com/2/short_url/shorten.json?source=2400945585&url_long=" . urlencode($longUrl);

    $url = "http://w.wjsq.org/api/shorturl/short?url=" . urlencode($longUrl);
    //$post_date = $longUrl;

    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
    // 设置获取的信息以文件流的形式返回，而不是直接输出
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    $data = curl_exec($curl);
    curl_close($curl);
    $result = json_decode($data, true);
    /*
        if (isset($result['urls'][0]['url_short'])) {
            return $result['urls'][0]['url_short'];
        } else {
            return false;
        }
	*/

    if (isset($result['message']) && ($result['status'] === TRUE)) {
        return $result['message'];
    } else {
        return false;
    }
}

/*
 * 获取扩展名
 */
function get_extension($file)
{
    return pathinfo($file, PATHINFO_EXTENSION);
}


/*
 * 数组按key分组
 */
function array_group_by_key($arr, $key)
{
    $grouped = [];
    foreach ($arr as $value) {
        $grouped[$value[$key]][] = $value;
    }

    if (func_num_args() > 2) {
        $args = func_get_args();
        foreach ($grouped as $key => $value) {
            $parms = array_merge([$value], array_slice($args, 2, func_num_args()));
            $grouped[$key] = array_group_by_key($parms);
        }
    }
    return $grouped;
}


/*
 * 获取分享码
 */
function makeShareMark($id, $type, $uid)
{
    $str = $type . '&' . $id . '&' . $uid;
    $str = md5($_SERVER['HTTP_HOST']) . '&share_code=' . $str;
    $str = \Crypt::encrypt($str);

    return $str;
}


/*
 * 将秒数转换成时分秒
 */
function changeTimeType($seconds)
{
    if ($seconds > 3600) {
        $minutes = intval($seconds / 60);
        $seconds = intval($seconds % 60);
        if ($seconds < 10) {
            $seconds = '0' . $seconds;
        }
        $time = $minutes . ':' . $seconds;
    } else {
        $time = gmstrftime('%M:%S', $seconds);
    }
    return $time;
}

/*
 * 二维数组按字段排序
 */
function muliterArraySortByfield($targetArr, $sortKey, $sortType = SORT_DESC)
{

    //空数组返回
    if (empty($targetArr)) {
        return $targetArr;
    }

    foreach ($targetArr as $key => $row) {
        if (!is_array($row)) {
            return false;
        }

        if (!array_key_exists($sortKey, $row)) {
            return false;
        }

        $order[$key] = $row[$sortKey];
    }

    array_multisort($order, $sortType == 'desc' ? SORT_DESC : SORT_ASC, $targetArr);

    return $targetArr;
}

/**
 * 从富文本中提取出文本
 * $num = -1 时 ，不进行字符串截取
 */
function extractText($str, $num = 30)
{
    $str = (trim(strip_tags($str)));
    $str = preg_replace('/\s{1,}/', '', $str);
    $str = preg_replace('/(&nbsp;){1,}/', '', $str);
    if($num != -1){
        $str = cut_str($str, $num);
    }
    return $str;
}


function transformEmoji($content)
{
    $content = mb_convert_encoding($content, 'utf-16');
    $bin = bin2hex($content);
    $arr = str_split($bin, 4);
    $l = count($arr);
    $str = '';
    for ($n = 0; $n < $l; $n++) {
        if (isset($arr[$n + 1]) && ('0x' . $arr[$n] >= 0xd800 && '0x' . $arr[$n] <= 0xdbff && '0x' . $arr[$n + 1] >= 0xdc00 && '0x' . $arr[$n + 1] <= 0xdfff)) {
            $H = '0x' . $arr[$n];
            $L = '0x' . $arr[$n + 1];
            $code = ($H - 0xD800) * 0x400 + 0x10000 + $L - 0xDC00;
            $str .= '&#' . $code . ';';
            $n++;
        } else {
            $str .= mb_convert_encoding(hex2bin($arr[$n]), 'utf-8', 'utf-16');
        }
    }

    return $str;
}

/**
 * 根据请求类型，获取不同的用户值    --数据中心  不处理  没有影响
 *
 * @param $userid       用户ID
 * @param $username     用户名称
 * @param $imgs         图片
 * @param $request_type 请求类型
 *
 * @return int|status|string|token 200 ...
 *
 */
function GainToken($userid, $username, $imgs, $request_type = 'user_token')
{
    $images = $imgs ?: getImage('');

    //通过类型调用不同的方法
    if ($request_type == 'user_token') {
        $result = Example::instances()->instance()->User()->getToken($userid, $username, $images);
    } elseif ($request_type == 'user_refresh') {
        $result = Example::instances()->instance()->User()->refresh($userid, $username, $images);
    } elseif ($request_type == 'user_checkOnline') {
        $result = Example::instances()->instance()->User()->checkOnline($userid);
    }

    //返回结果处理
    if ( !is_array($result) ) {
        $results = json_decode($result, true);
        if ($results['code'] == 200) {
            if (isset($results['token']) && !empty($results['token'])) {
                return $results['token'];
            } else {
                return $results['code'];
            }
        } else {
            echo AjaxCallbackMessage($results['errorMessage'], false); exit;
        }
    } else {
        echo AjaxCallbackMessage($result['message'], false); exit;
    }
}

/**
 *  author zhaoyf
 * 发送消息
 *
 * @param $fromUserId           发送者用户ID
 * @param $toUserId             接受者用户ID
 * @param $content              消息内容
 * @param string $objectName    消息类型：默认RC:TxtMsg
 * @param string $pushContent   自定义消息内容（当消息类型为自定义，例：TY:TipMsg时，这个参数需要）
 * @param bool $tags --bool |   标记：用于区分是单发还是双向发送，默认false为双向
 *
 * @param string $content_type
 * @param string $transform
 * @return array
 */
function SendCloudMessage($fromUserId, $toUserId, $content, $objectName = 'RC:TxtMsg', $pushContent = '', $tags = false, $transform = null, $content_type = "content")
{
    if ('agent' === substr($fromUserId, 0 , 5)) {
        $form_user_id = str_replace('agent', "", $fromUserId);
    } else {
        $form_user_id = $fromUserId;
    }
    if ('agent' === substr($toUserId, 0 , 5)) {
        $to_user_id   = str_replace('agent', "", $toUserId);
    } else {
        $to_user_id   = $toUserId;
    }

    //根据不同值来进行不同的操作
    switch ($transform) {
        case 'one_agent' :
            $agent_data  = \App\Models\Agent\Agent::find($form_user_id);
            $one_data  = [
                'id'    => 'agent'.$agent_data->id,
                'name'  =>  $agent_data->nickname,
                'icon'  => getImage($agent_data->avatar,'avatar', ''), ];
            break;

        case 'one_user':
            $user_data  = \App\Models\User\Entity::find($form_user_id);
            $one_data  = [
                'id'    => $user_data->uid,
                'name'  => trim($user_data->nickname),
                'icon'  => getImage($user_data->avatar, 'avatar', ''),
            ];
            break;

        case 'bothway':
            $user_data  = \App\Models\User\Entity::find($form_user_id);
            $agent_data = \App\Models\Agent\Agent::find($to_user_id);

            $_user  = [ //用户头像相关数据
                'id'    => $user_data->uid,
                'name'  => trim($user_data->nickname),
                'icon'  => getImage($user_data->avatar, 'avatar', ''),
            ];
            $_agent = [ //经纪人头像相关数据
                'id'    => 'agent'.$agent_data->id,
                'name'  => $agent_data->nickname,
                'icon'  => getImage($agent_data->avatar, 'avatar', ''),
            ];
            break;

        default :
            break;
    }

    //不需要发送者头像信息，信息自定义发送
    if ($tags === "custom") {
        //用户头像自动添加
        if(!empty($one_data)){
            $content = array_merge($content , ['user'=>$one_data]);
        }
        $_result  = Example::instances()->instance()->Message()->publishPrivate($fromUserId, $toUserId, $objectName, json_encode($content), $pushContent);
        if (json_decode($_result, true)['code'] == 200) {
            return ['status' => true];
        } else {
            ['status' => false];
        }
    //需要发送者头像信息，信息单向发送
    } elseif ($tags) {
        $_result  = Example::instances()->instance()->Message()->publishPrivate($fromUserId, $toUserId, $objectName, json_encode([$content_type => $content, 'user' => $one_data]), $pushContent);
        if (json_decode($_result, true)['code'] == 200) {
            return ['status' => true];
        } else {
            ['status' => false];
        }

    //需要发送者头像信息，信息双向发送
    } else {
        $_result = Example::instances()->instance()->Message()->publishPrivate($fromUserId, $toUserId, $objectName, json_encode([$content_type => $content, 'user' => $_user]), $pushContent);
        if (json_decode($_result, true)['code'] == 200) {
           $results = Example::instances()->instance()->Message()->publishPrivate($toUserId, $fromUserId, $objectName, json_encode([$content_type => $content, 'user' => $_agent]), $pushContent);
            if(json_decode($results, true)['code'] == 200) {
                return ['status' => true];
            } else {
                return ['status' => false];
            }
        } else {
            return ['status' => false];
        }
    }

}


/**
 * 根据用户身份证号 获取对应的星座
 * @param string $idcard 身份证号码
 *
 * return constellation 对应星座
 */
function getStarsign($idcard, $tags = "idcard")
{
    if (empty($idcard)) return null;

    if ($tags == "idcard") {
        $b = substr($idcard, 10, 4);
        $m = (int)substr($b, 0, 2);
        $d = (int)substr($b, 2);
    } elseif ($tags == "birth_time") {
        $b = substr($idcard, 5, 5);
        $m = (int)substr($b, 0, 2);
        $d = (int)substr($b, 3);
    }

    if (($m == 1 && $d <= 21) || ($m == 2 && $d <= 19)) {
        $val = "水瓶座";
    } elseif (($m == 2 && $d > 20) || ($m == 3 && $d <= 20)) {
        $val = "双鱼座";
    } elseif (($m == 3 && $d > 20) || ($m == 4 && $d <= 20)) {
        $val = "白羊座";
    } elseif (($m == 4 && $d > 20) || ($m == 5 && $d <= 21)) {
        $val = "金牛座";
    } elseif (($m == 5 && $d > 21) || ($m == 6 && $d <= 21)) {
        $val = "双子座";
    } elseif (($m == 6 && $d > 21) || ($m == 7 && $d <= 22)) {
        $val = "巨蟹座";
    } elseif (($m == 7 && $d > 22) || ($m == 8 && $d <= 23)) {
        $val = "狮子座";
    } elseif (($m == 8 && $d > 23) || ($m == 9 && $d <= 23)) {
        $val = "处女座";
    } elseif (($m == 9 && $d > 23) || ($m == 10 && $d <= 23)) {
        $val = "天秤座";
    } elseif (($m == 10 && $d > 23) || ($m == 11 && $d <= 22)) {
        $val = "天蝎座";
    } elseif (($m == 11 && $d > 22) || ($m == 12 && $d <= 21)) {
        $val = "射手座";
    } elseif (($m == 12 && $d > 21) || ($m == 1 && $d <= 20)) {
        $val = "魔羯座";
    } else {
        $val = '';
    }
    return $val;
}

/**
 *  根据身份证号码获取出身地址
 * @param string $idcard 身份证号码
 *
 * @return string $address
 */
function getAddress($idcard, $type = 1)
{
    if (empty($idcard)) return null;

    switch ($type) {
        case 1:
            $key_06 = substr($idcard, 0, 6);
            if (!empty($key_06)) {
                $where['region_id'] = $key_06;
            } else {
                # 没有具体的地址截取前两位数(只获取省份)
                $key_02 = substr($idcard, 0, 2);
                if (!empty($key_02)) {
                    $where['region_id'] = $key_02;
                } else {
                    return null;
                }
            }
            break;
        case 2:
            # 截取前两位数(只获取省份)
            $key = substr($idcard, 0, 2);
            if (!empty($key)) {
                $where['region_id'] = $key;
            }
            break;
        default:
            return null;
            break;
    }
    $result = DB::table('region')->where($where)->first();
    return $result ? $result->region : "" ;
}

/**
 * 根据身份证 或 出生日期 获取属于某个年代（比如：90后，00后等）
 *
 * @param $time_type    根据tags来传递不同的值，身份证 或 出生日期
 * @param string $tags  idcard：身份证（默认）； birth_time：出生日期
 * @return string
 */
function getTime($time_type, $tags = "idcard")
{

    if (empty($time_type)) return null;
    $time = '';
    if ($tags == "idcard") {
        $times = substr($time_type, 6, 4);
    } elseif ($tags == "birth_time") {
        $times = substr($time_type, 0, 4);
    }

    if ($times <= 0) {
        return '';
    }
    if ($times < "1980") {
        $time = "70后";
    } elseif ($times < "1990") {
        $time = "80后";
    } elseif ($times < "1995") {
        $time = "90后";
    } elseif ($times < "2000") {
        $time = "95后";
    } elseif ($times < "2010") {
        $time = "00后";
    } elseif ($times < "2020") {
        $time = "10后";
    } else {
        $time = '未知年代';
    }

    return $time;
}

/*
 * 获取当前季度格式化字段
 *
 * */
function getQuarterFormat()
{
    $season = ceil((date('n')) / 3);
    $str = '';
    switch ($season) {
        case '1':
            $str = date('Y') . '年1月-3月';
            break;
        case '2':
            $str = date('Y') . '年4月-6月';
            break;
        case '3':
            $str = date('Y') . '年7月-9月';
            break;
        case '4':
            $str = date('Y') . '年10月-12月';
            break;
    }
    return $str;
}

/**
 * 获取汉字的首拼音
 *
 * @return null|string
 */
function getfirstchar($str)
{
    try{
        if (empty($str)) {
            return "";
        }
        $fchar = ord($str{0});
        if ($fchar >= ord("A") and $fchar <= ord("z")) return strtoupper($str{0});
//    $s1 = iconv("UTF-8", "gb2312", $str);
        $s1 = iconv("UTF-8", "gb2312//TRANSLIT//IGNORE", $str);
        $s2 = iconv("gb2312", "UTF-8//TRANSLIT//IGNORE", $s1);
        if ($s2 == $str) {
            $s = $s1;
        } else {
            $s = $str;
        }
        $asc = ord($s{0}) * 256 + ord($s{1}) - 65536;
        if ($asc >= -20319 and $asc <= -20284) return "A";
        if ($asc >= -20283 and $asc <= -19776) return "B";
        if ($asc >= -19775 and $asc <= -19219) return "C";
        if ($asc >= -19218 and $asc <= -18711) return "D";
        if ($asc >= -18710 and $asc <= -18527) return "E";
        if ($asc >= -18526 and $asc <= -18240) return "F";
        if ($asc >= -18239 and $asc <= -17923) return "G";
        if ($asc >= -17922 and $asc <= -17418) return "H";
        if ($asc >= -17922 and $asc <= -17418) return "I";
        if ($asc >= -17417 and $asc <= -16475) return "J";
        if ($asc >= -16474 and $asc <= -16213) return "K";
        if ($asc >= -16212 and $asc <= -15641) return "L";
        if ($asc >= -15640 and $asc <= -15166) return "M";
        if ($asc >= -15165 and $asc <= -14923) return "N";
        if ($asc >= -14922 and $asc <= -14915) return "O";
        if ($asc >= -14914 and $asc <= -14631) return "P";
        if ($asc >= -14630 and $asc <= -14150) return "Q";
        if ($asc >= -14149 and $asc <= -14091) return "R";
        if ($asc >= -14090 and $asc <= -13319) return "S";
        if ($asc >= -13318 and $asc <= -12839) return "T";
        if ($asc >= -12838 and $asc <= -12557) return "W";
        if ($asc >= -12556 and $asc <= -11848) return "X";
        if ($asc >= -11847 and $asc <= -11056) return "Y";
        if ($asc >= -11055 and $asc <= -10247) return "Z";
        return '';
    }catch (\Exception $e){
        return "";
    }
}

/*
 *
 * 身份证识别
 * param:$img  身份证二进制数据的base64编码
 * param:$type  1代表正面 0代表反面
 * */
function getIdCardIdentityInfo($imgUrl, $type)
{
    $ss = [];
    $ss[] = $imgUrl;
    $ss[] = $type;
    file_put_contents(storage_path('sfz') , json_encode($ss),FILE_APPEND);

    $image_data = fread(fopen($imgUrl, 'r'), filesize($imgUrl));
    $base64Img = base64_encode($image_data);
    $imgSide = "back";
    if ($type) {
        $imgSide = "face";
    }
    $host = "https://dm-51.data.aliyun.com";
    $path = "/rest/160601/ocr/ocr_idcard.json";
    $method = "POST";
    $appcode = "b10e87b3557943c5bba212ccc11a22f6";
    $headers = array();
    array_push($headers, "Authorization:APPCODE " . $appcode);
    //根据API的要求，定义相对应的Content-Type
    array_push($headers, "Content-Type" . ":" . "application/json; charset=UTF-8");
    $querys = "";
    $bodys = "{
         \"inputs\": [
                {
                    \"image\": {
                        \"dataType\": 50,
                        \"dataValue\": \"{$base64Img}\"
                    },
                    \"configure\": {
                        \"dataType\": 50,
                        \"dataValue\": \"{\\\"side\\\":\\\"{$imgSide}\\\"}\"
                    }
                }
            ]
        }";
    $url = $host . $path;

    $curl = curl_init();
    curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $method);
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($curl, CURLOPT_FAILONERROR, false);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_HEADER, false);
    if (1 == strpos("$" . $host, "https://")) {
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
    }
    curl_setopt($curl, CURLOPT_POSTFIELDS, $bodys);
    $data = curl_exec($curl);
    file_put_contents(storage_path('sfz') , json_encode($data),FILE_APPEND);
    if (curl_getinfo($curl, CURLINFO_HTTP_CODE) != '200') {
        return false;
    }
    $outputs = json_decode($data, 1);
    if(empty($outputs)){
        return false;
    }
    $idcardInfo = json_decode($outputs['outputs'][0]['outputValue']['dataValue'], 1);
    if ($idcardInfo['success'] === true) {
        return $idcardInfo;
    }
    return false;
}

/**
 * 银行卡识别
 * $imgUrl           证件图片
 * $credentialsTYPE  识别类型：bank(银行卡)，身份证....（默认是：bank）
 * @param $imgUrl
 * @return null|string
 */
function getBankCardInfo($imgUrl)
{
    $result = \App\Services\Bank::instance()->bankBodys($imgUrl);
    if (is_null($result)) {
        return null;
    } elseif (is_string($result) && $result === "data_null") {
        return "data_null";
    }

    return $result;
}


/**
 * 替换中间的数字成*号
 */
function digitalStarReplace($digital, $before_num = 4, $after_num = 4)
{
    $call_back = function ($matches) {
        return $matches[1] . str_repeat('*', strlen($matches[2])) . $matches[3];
    };

    $res = preg_replace_callback(
        "/(\d{{$before_num}})(\d+)(\d{{$after_num}})/i",
        $call_back,
        $digital);

    return $res;

}

/*
 * 身份证号码正则验证
 *$idCard:  身份证号
 * return : 正确返回    1
 *          错误返回    0
 * */
function idCardExpVerify($idCard){
    $idCard = trim($idCard);
    $rel = preg_match("/^([1-9]\d{5}(18|19|([23]\d))\d{2}((0[1-9])|(10|11|12))(([0-2][1-9])|10|20|30|31)\d{3}[0-9Xx])|([1-9]\d{5}\d{2}((0[1-9])|(10|11|12))(([0-2][1-9])|10|20|30|31)\d{2})$/", $idCard);
    return $rel;
}

/*
 * 验证密码
 * */
function checkPassword($password){
    if(empty($password)){
        return ['message'=>'密码不能为空','status'=>false];
    }
    $num = preg_match("/^\w{6,16}$/", $password);
    if(empty($num)){
        return ['message'=>'密码必须6—16位字母，数组，下划线组成','status'=>false];
    }
    return ['message'=>'ok','status'=>true];
}


/**
 * 根据经纬度计算两地的
 *
 * @param $lat1 地点1的纬度
 * @param $lon1 地点1的经度
 * @param $lat2 地点2的纬度
 * @param $lon2 地点2的经度
 * @param $radius 地球的半径
 * @return float
 * @author tangjb
 */
function distance($lat1, $lon1, $lat2,$lon2,$radius = 6378.137)
{
    $rad = floatval(M_PI / 180.0);

    $lat1 = floatval($lat1) * $rad;
    $lon1 = floatval($lon1) * $rad;
    $lat2 = floatval($lat2) * $rad;
    $lon2 = floatval($lon2) * $rad;

    $theta = $lon2 - $lon1;

    $dist = acos(sin($lat1) * sin($lat2) +
        cos($lat1) * cos($lat2) * cos($theta)
    );

    if ($dist < 0 ) {
        $dist += M_PI;
    }

    return $dist = $dist * $radius;
}


/**
 * 根据生日获取星座
 *
 * @param $m
 * @param $d
 * @return string
 * @author tangjb
 */
function getStarsignByMonth($m, $d)
{
    $m = intval($m);
    $d = intval($d);
    if($m==0 || $d==0){
        return false;
    }

    if (($m == 1 && $d >= 21) || ($m == 2 && $d <= 19)) {
        $val = "水瓶座";
    } elseif (($m == 2 && $d > 20) || ($m == 3 && $d <= 20)) {
        $val = "双鱼座";
    } elseif (($m == 3 && $d > 20) || ($m == 4 && $d <= 20)) {
        $val = "白羊座";
    } elseif (($m == 4 && $d > 20) || ($m == 5 && $d <= 21)) {
        $val = "金牛座";
    } elseif (($m == 5 && $d > 21) || ($m == 6 && $d <= 21)) {
        $val = "双子座";
    } elseif (($m == 6 && $d > 21) || ($m == 7 && $d <= 22)) {
        $val = "巨蟹座";
    } elseif (($m == 7 && $d > 22) || ($m == 8 && $d <= 23)) {
        $val = "狮子座";
    } elseif (($m == 8 && $d > 23) || ($m == 9 && $d <= 23)) {
        $val = "处女座";
    } elseif (($m == 9 && $d > 23) || ($m == 10 && $d <= 23)) {
        $val = "天秤座";
    } elseif (($m == 10 && $d > 23) || ($m == 11 && $d <= 22)) {
        $val = "天蝎座";
    } elseif (($m == 11 && $d > 22) || ($m == 12 && $d <= 21)) {
        $val = "射手座";
    } elseif (($m == 12 && $d > 21) || ($m == 1 && $d <= 20)) {
        $val = "魔羯座";
    }else{
        $val = '';
    }
    return $val;
}


/**
 * 把省和市去掉
 */
function abandonProvince($name)
{
    $call_back = function ($matches) {
        return $matches[1];
    };


    $res = preg_replace_callback(
        "/([^市省]?)(市|省)/i",
        $call_back,
        $name);

    return $res;
}


/**
 * 时间倒计时
 *
 * @param $expiration_time
 *
 * @return string
 */
function countDown($expiration_time)
{
    $presentTime  = new DateTime();
    $endTime      = new DateTime(date("Y-m-d H:i:s", $expiration_time));
    $surplusTime  = $endTime->getTimestamp() - $presentTime->getTimestamp();

    $days   = floor($surplusTime / 86400);              //获取天数
    $hours  = floor($surplusTime % 86400 / 3600);       //获取小时数
    $minute = floor($surplusTime % 86400 % 3600 / 60);  //获取分钟

    if ($days <= 0 && $hours <= 0 && $minute <= 0  ) return '已过期';

    //当分钟小于10时，前面加零
    $minute = $minute < 10 ?  "0" . $minute : $minute;

   return "还剩{$days}天{$hours}小时{$minute}分";
}

/**
 * 通过发送时间和打开时间计算红包的打开的时间差
 */
function openTime($send_time, $open_time)
{
    $surplusTime  = $open_time - $send_time;

    if ($surplusTime <= 0)     return '刚刚将红包打开';
    if ($surplusTime < 60)     return '一分钟前将红包点开';
    if ($surplusTime <= 3600)  return floor($surplusTime / 60) .'分钟将红包点开';
    if ($surplusTime <= 86400) return floor($surplusTime / 3600) . '小时将红包点开';

    return floor($surplusTime / 86400) . '天将红包打开'; //获取天数
}

/**
 * 对银行卡号 进行格式化，并返回银行名称
 * @param  $bank  银行卡号
 * @start_num  开头留几位 默认4位
 * @$num       *去几位 默认10位
 *
 * @return   格式为  6600****************90291（工商银行）
 */
function bankFormat($bank,$start_num = 4, $num = 10){

    $ast = '**********'; //默认*10个
    $ask = '*';
    for ($i=1;$i<$num;$i++){
        $ask .= '*';
    }

    if ($num == 10){
        $asterisk = $ast;
    }else{
        $asterisk = $ask;
    }

    $format = substr_replace($bank,$asterisk,$start_num,$num);

    return $format;
}

/*
 * 对银行卡号、微信号、支付宝账号，进行加密处理，然后将账号类型附带后面进行返回
 * @return   格式为    6600****************90291（工商银行）
 *                     150*****554（微信）
 *                     85*********com（支付宝）
 *
 * 微信，支付宝两种类型以后跟进需求再写
 * */

function accountEncrypt($account,$type){
    $result = '-';
    if(!empty($account)){
        if($type == 'unionpay'){
            $result = bankFormat($account);
        }
        $result = $account;
    }
    return $result;
}

/*
 *
 * 对身份证号进行加密
 *
 * */
/**
 * @param $idCard  要加密的字符串
 * @param int $startNum     开头保留几位
 * @param int $endNum       末尾保留几位
 * @param int $middle       中间用$middle个*来代替
 * @return mixed|string     返回加密的字符串
 */

function idCardEncrypt($idCard , $startNum = 2 , $endNum = 2 ,$middle = 0){
    $encry = "";
    if(!empty($idCard)){
        $len = strlen($idCard);
        if($len <= $startNum + $endNum){
            for($i = 0 ;$i < $len; $i++){
                $encry .= '*';
            }
            return $encry;
        }
        if(empty($middle)){
            $middle = $len -  $startNum - $endNum;
        }
        for($i = 0 ;$i < $middle; $i++){
            $encry .= '*';
        }
        $idCard = substr_replace($idCard,$encry,$startNum,$len - $startNum - $endNum);
    }
    return $idCard;
}






/**
 *每隔三位加一个逗号
 *
 * @author tangjb
 */
function numFormatWithComma($num){
    if(!is_numeric($num)){
        return false;
    }

    $num = abandonZero($num);
    //判断是否为小数
    if(preg_match('/^[0-9]+(\.)([0-9])+$/', $num)){
        $num_arr = explode('.',$num);//把整数和小数分开
    }else{
        $num_arr[0] = $num;
    }


    if(isset($num_arr[1])){
        $rl = $num_arr[1];//小数部分的值
    }

    $j = strlen($num_arr[0]) % 3;//整数有多少位
    $sl = substr($num_arr[0], 0, $j);//前面不满三位的数取出来
    $sr = substr($num_arr[0], $j);//后面的满三位的数取出来
    $i = 0;
    $rvalue ='';

    while($i <= strlen($sr)){
        $rvalue = $rvalue.','.substr($sr, $i, 3);//三位三位取出再合并，按逗号隔开
        $i = $i + 3;
    }
    $rvalue = $sl.$rvalue;

    $rvalue = substr($rvalue,0,strlen($rvalue)-1);//去掉最后一个逗号
    $rvalue = explode(',',$rvalue);//分解成数组
    if($rvalue[0]==0 && count($rvalue)>1){
        array_shift($rvalue);//如果第一个元素为0，删除第一个元素
    }
    $rv = $rvalue[0];//前面不满三位的数
    for($i = 1; $i < count($rvalue); $i++){
        $rv = $rv.','.$rvalue[$i];
    }
    if(!empty($rl)){
        $rvalue = $rv.'.'.$rl;//小数不为空，整数和小数合并
    }else{
        $rvalue = $rv;//小数为空，只有整数
    }

    return $rvalue;
}

/**
 * 当没有头像时，默认使用这个
 *
 * @return string
 */
function default_img()
{
    return "http://test.wujie.com.cn/images/default/avator-m.png";
}

/**
 * 去掉数字的千分符
 *
 * @author tangjb
 */
function abondonComma($num)
{
    $num = str_replace(',', '', $num);

    return $num;
}



//function handleBankCardNumber($card,$bankList)
//{
//    $card_8 = substr($card, 0, 8);
//    if (isset($bankList[$card_8])) {
//        echo $bankList[$card_8];
//        return;
//    }
//    $card_6 = substr($card, 0, 6);
//    if (isset($bankList[$card_6])) {
//        echo $bankList[$card_6];
//        return;
//    }
//    $card_5 = substr($card, 0, 5);
//    if (isset($bankList[$card_5])) {
//        echo $bankList[$card_5];
//        return;
//    }
//    $card_4 = substr($card, 0, 4);
//    if (isset($bankList[$card_4])) {
//        echo $bankList[$card_4];
//        return;
//    }
//    echo '该卡号信息暂未录入';
//}

/*
 * 生成用户昵称
 *
 * 投资人昵称：wjsq+5为字母加数字组合  例如：wjsqXXXXX
 *
 * */
function getRandomString($len, $chars=null,$prefix = 'wjsq')
{
    if (is_null($chars)){
        $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
    }
    mt_srand(10000000*(double)microtime());
    for ($i = 0, $str = $prefix, $lc = strlen($chars)-1; $i < $len; $i++){
        $str .= $chars[mt_rand(0, $lc)];
    }
    return $str;
}

/*
 * 对数组中每个元素执行trim方法
 * */
function arrayTrim($arr){
    foreach($arr as &$item){
        if(is_array($item) || is_object($item)){
            $item = arrayTrim($item);
            continue;
        }
        $item = trim($item);
    }
    return $arr;
}


function getClient()
{
    $agent = Request::header('USER_AGENT');

    if (strpos($agent, 'MicroMessenger') !== false) {//微信内置
        $platform = 'weixin';
    } elseif (strpos($agent, 'android') !== false || strpos($agent, 'okhttp') !== false) {//安卓
        $platform = 'android';
    } elseif (strpos($agent, 'iPhone') !== false) {//iPhone
        $platform = 'iPhone';
    } elseif (strpos($agent, 'iPod') !== false) {//iPod
        $platform = 'iPod';
    } elseif (preg_match('/mozilla|m3gate|winwap|openwave|Windows NT|Windows 3.1|95|Blackcomb|98|ME|XWindow|ubuntu|Longhorn|AIX|Linux|AmigaOS|BEOS|HP-UX|OpenBSD|FreeBSD|NetBSD|OS\/2|OSF1|SUN/i', $agent)) {
        $platform = 'pc';
    }else{
        return '';
    }


    return $platform;
}

/*
 * 判断请求来源
 * 如果是经纪人端 则返回agent
 *      c端          则返回wjsq
 *      前端          则返回web
 *      其他          返回  other
 *
 * */
//function getHttpSource(){
//    $url = $_SERVER['REQUEST_URI'];
//    preg_match("/^\/([a-z]+)\/([^\/]+)/",$url,$matches);
//    $str = trim($matches[1]);
//    $source = 'other';
//    if($str == 'api'){
//        $source = 'wjsq';
//    }
//    else if($str == 'agent'){
//        $source = $str;
//    }
//    else if($str == 'webapp' && $str == 'agent'){
//        $source = 'web';
//    }
//    return $source;
//}


function num2char($num,$mode=true){
    $char = array('零','一','二','三','四','五','六','七','八','九');
    //$char = array('零','壹','贰','叁','肆','伍','陆','柒','捌','玖);
    $dw = array('','十','百','千','','万','亿','兆');
    //$dw = array('','拾','佰','仟','','萬','億','兆');
    $dec = '点';  //$dec = '點';
    $retval = '';
    if($mode){
        preg_match_all('/^0*(\d*)\.?(\d*)/',$num, $ar);
    }else{
        preg_match_all('/(\d*)\.?(\d*)/',$num, $ar);
    }
    if($ar[2][0] != ''){
        $retval = $dec . ch_num($ar[2][0],false); //如果有小数，先递归处理小数
    }
    if($ar[1][0] != ''){
        $str = strrev($ar[1][0]);
        for($i=0;$i<strlen($str);$i++) {
            $out[$i] = $char[$str[$i]];
            if($mode){
                $out[$i] .= $str[$i] != '0'? $dw[$i%4] : '';
                if($str[$i]+$str[$i-1] == 0){
                    $out[$i] = '';
                }
                if($i%4 == 0){
                    $out[$i] .= $dw[4+floor($i/4)];
                }
            }
        }
        $retval = join('',array_reverse($out)) . $retval;
    }
    return $retval;
}

/*
 * 二维不定长数据，按照指定规则，多字段排序
 *
 * */
/**
 * @param $array 不定长二维数组，可以处理从两张表中取出的杂糅数组
 * @param $rule     类似[
 *                          'age'=>'desc',
 *                          'created_at'=>'desc',
 *                          'sort'=>'asc',
 *                      ]
 *                  越靠前的字段，越优先，在前面字段相同的情况下，才排后面的字段
 * @return $array   排序后的数组
 *
 * 局限：  排序字段 必须在$array  元素中的第一维度  ，例如  sort在  array[$i]['sort']中不适用，只能在  array['sort']才行
 *          但可以将该字段调整到第一维在调用方法。
 */

function multiFieldSort($array , $rule){
    if(empty($rule) || empty($array)){
        return $array;
    }

    //获取当前排序字段和排序规则
    $currRule = reset($rule);
    $currRuleField = key($rule);
    array_shift($rule);
    if($currRule == 'asc'){
        $ruleStr = '';
    }
    else{
        $ruleStr= studly_case($currRule);
    }
    $sortMethodName = 'sortBy'.$ruleStr;
    $array = collect($array)->$sortMethodName($currRuleField)->values()->toArray();

    //寻找按照上述排序规则排序后，相同的排序值 元素，准备按照下一排序规则排序。
    $startIndex = 0;
    $sameBox = [];
    for ( $i = 0; $i < count($array) ; $i++){
        if(empty($sameBox) || end($sameBox)[$currRuleField] == $array[$i][$currRuleField]){
            $sameBox[] = $array[$i];
        }
        else{
            if(count($sameBox) > 1){
                $afterSortArr = multiFieldSort($sameBox,$rule);
                array_splice($array,$startIndex,count($afterSortArr),$afterSortArr);
            }
            $sameBox = [];
            $sameBox[] = $array[$i];
            $startIndex = $i;
        }
    }
    //对于数组末尾中排序值相同的单独处理
    if(count($sameBox) > 1){
        $afterSortArr = multiFieldSort($sameBox,$rule);
        array_splice($array,$startIndex,count($afterSortArr),$afterSortArr);
    }
    return $array;
}

/**
 *  author zhaoyf
 *
 * 多维数组排序
 *
 * @param   $array      需要排序的数组
 * @param   $sort_key   指定某个数组key排序
 * @param   $sort       排序方式（desc, asc）默认倒序
 *
 */
function multiArraySort($array, $sort_key, $sort = 'desc')
{
    $keys = array();
    foreach ($array as $key => $vls) {
        $keys[$key]  = $vls[$sort_key];
    }

    //根据排序方式进行排序操作处理
    if ($sort == 'desc') {
        array_multisort($keys, SORT_DESC, $array);
    }

    //返回处理后的数组结果
    return $array;
}

/*
 * 对音频时长进行格式化 例如86:12   00:12
 * */

function formatAudioLen($audioLen){
    $audioLen = intval($audioLen);
    $data[] = intval($audioLen / 60);
    $data[] = $audioLen % 60;
    foreach ($data as &$one){
        if($one == 0){
            $one = '00';
        }
        else if($one < 10){
            $one = '0'.$one;
        }
    }
    return implode(':' , $data);
}

/**
 * 获取真实手机号
 * @param $non_reversible  加密后的手机号
 * @param $source  来源平台：wjsq无界商圈，agent无界商圈经纪人，c_crm,g_crm
 * @return string
 */
function getRealPhone($non_reversible , $source){
    $url = config('system.data_center.hosts') . config('system.data_center.decrypt');
    if(!in_array($source,['wjsq','agent','c_crm','g_crm'])){
        return '平台类型不正确';
    }
    $datas = ['en_tel'=> $non_reversible , 'platform'=>$source];
    //请求数据中心接口
    $result = json_decode(getHttpDataCenter($url, '', $datas));
    if( empty($result) || !$result->status){
        return '';
    }
    return trim($result->message);
}

//将姓名中的姓变为*号
function encryptName($name){
    if (empty($name)){
        return "";
    }
    $firstName = mb_substr($name, 0, 1);
    $lastName = mb_substr($name, 1);
    return "*".$lastName;
}

/*
 * 功能:根据概率选择某一项目
 * $array 前期数据
 *  $array = [
 *              [ itemId=>    ,  prob=>  ,（其他字段可选，有itemId可以找到其他信息）]
 *              [ itemId=>    ,  prob=>  ]
 *              [ itemId=>    ,  prob=>  ]
 *              [ itemId=>    ,  prob=>  ]
 *          ]
 * itemId 为项目id ， prob 该项目发生的概率
 * @return int|mixed  失败：返回0  成功：返回array对应元素
 */
function selectByProbability($array){
    $data = $array;
    //将概率转为整数
    $allProb = array_pluck($data , 'prob');
    //获取将小数放大成整数的最小倍数,将所有的概率转化为整数
    $minMultiple = getMinPower($allProb);
    $probabilityTotal = 0 ;//概率总值
    foreach ($data as &$item){
        $item['prob'] = floatval($item['prob'] * $minMultiple);
        $probabilityTotal += $item['prob'];
    }
    //将概率转化为区间
    $awardLine = [];
    $dot = 0;
    foreach ($data as $k => $v) {
        $awardLine[$k]['itemId'] = $v['itemId'];
        $awardLine[$k]['start'] = $dot;
        $dot = $dot + $v['prob'];
        $awardLine[$k]['end'] = $dot;
    }
    //生成随机数，投入区间池
    $rand_num = mt_rand(1, $probabilityTotal);
    $hit = 0;
    foreach ($awardLine as $v) {
        if ($rand_num > $v['start'] && $rand_num <= $v['end']) {//命中
            foreach ($array as $one){
                if($one['itemId'] == $v['itemId']){
                    $hit = $one;
                    break 2;
                }
            }
        }
    }
    //返回选中项目id
    return $hit;
}

//将一组小数同倍放大成整数 所需要乘的  最小10的倍数
function getMinPower($array){
    $minMultiple = 0;
    foreach ($array as $one){
        $exArr = explode('.' , $one);
        if(count($exArr) > 1){
            $len = strlen($exArr[1]);
            $len > $minMultiple && $minMultiple = $len;
        }
    }
    return pow(10 , $minMultiple);
}


//返回两个数之间的随机数，包括小数和整数
function twoNumRand($num1 , $num2){
    $num1 = floatval($num1);
    $num2 = floatval($num2);
    if($num1 == $num2){
        return $num1;
    }
    $multiple = getMinPower([$num1 , $num2]);
    $randNum = mt_rand(floatval($num1 * $multiple) , floatval($num2 * $multiple));
    return floatval($randNum / $multiple);
}


/**
 * 功能描述：    获取类单例实例
 *
 * 参数说明：
 * @param $modelClass   为模型的命名空间路径
 *
 * 返回值：
 * @return mixed        该模型的一个实例
 *
 * 实例：      modelFactory(Agent::class)
 * 结果：
 *
 * 作者： shiqy
 * 创作时间：@date 2018/1/31 0031 下午 2:37
 */
function modelFactory($modelClass , $param = []){
    if(!array_key_exists($modelClass , App\Models\Factory::$modelStore)){
        App\Models\Factory::$modelStore[$modelClass] = new $modelClass($param);
    }
    return App\Models\Factory::$modelStore[$modelClass];
}





