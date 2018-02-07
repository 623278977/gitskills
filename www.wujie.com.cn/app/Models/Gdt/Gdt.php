<?php
/**广点通模型
 * Created by PhpStorm.
 * User: yaokai
 * Date: 2017/5/2
 * Time: 11:10
 */
namespace App\Models\Gdt;

use Illuminate\Database\Eloquent\Model;
use \DB, Closure, Input;
use Validator;

class Gdt extends Model
{

    protected $dateFormat = 'U';

    protected $table = 'gdt';
    //黑名单
    protected $guarded = ['id'];

    public static $_RULES = [//验证规则
        'muid' => 'required|between:1,40',     // varchar(40) NOT NULL COMMENT '设备id',
        'click_time' => 'required',                  // varchar(10) NOT NULL COMMENT '点击发生的时间',
        'click_id' => 'required|unique:gdt',  // varchar(100) NOT NULL COMMENT '广点通后台生成的点击id,唯一',
        'appid' => 'required',                  // int(10) NOT NULL COMMENT '应用ID',
        'advertiser_id' => 'required',                  // int(10) NOT NULL COMMENT '广点通账户id',
        'app_type' => 'required',                  // varchar(10) NOT NULL COMMENT 'app类型',
    ];
    public static $_MESSAGES = [//验证字段说明
        'muid' => '设备id',              // varchar(40) NOT NULL COMMENT '设备id',
        'click_time' => '点击发生的时间',        // varchar(10) NOT NULL COMMENT '点击发生的时间',
        'click_id' => '广点通后台生成的点击id',// varchar(100) NOT NULL COMMENT '广点通后台生成的点击id,唯一',
        'appid' => '应用ID',              // int(10) NOT NULL COMMENT '应用ID',
        'advertiser_id' => '广点通账户id',         // int(10) NOT NULL COMMENT '广点通账户id',
        'app_type' => 'app类型',             // varchar(10) NOT NULL COMMENT 'app类型',
    ];


    public function gdtStatus()
    {
        return $this->hasMany('App\Models\Gdt\Status', 'gdt_id', 'id');
    }

    static function gdt(array $data)
    {

        $validator = Validator::make($data, Gdt::$_RULES);
        $errors = $validator->errors()->all();
        if (empty($errors)) {
            $result = self::create($data);
            $ret = 0;
        } else {
            $ret = 1;
        }
        if ($ret != 0) {
            $msg = $errors;
        } else {
            $msg = 'success';
        }
        return json_encode(['ret' => $ret, 'msg' => $msg]);
    }

    /**
     * 广告主： 3972252
     * 统计方案： API方案一
     * 秘钥A（encrypt_key）： BAAAAAAAAAAAPJyc
     * 秘钥B（sign_key）： 74a7c6feaa377ce6
     */
    static function handle()
    {

        //昨天凌晨时间0:00:00
        $startdate = date('Y-m-d H:i:s', mktime(0, 0, 0, date('m'), date('d') - 1, date('Y')));
        //昨天结束时间23:59:59
        $enddate = date('Y-m-d H:i:s', mktime(0, 0, 0, date('m'), date('d'), date('Y')) - 1);

        //获取每天的激活用户
        $activate = Clientdata::select(DB::raw('MIN(id) as id,platform,imei,date'))
            ->where('date', '>=', $startdate)
            ->where('date', '<', $enddate)
            ->groupBy('imei')
            ->get()
            ->toArray();

        //获取每天的注册用户
        $register = Event::with('eventdata')
            ->select('event_defination.event_id')
            ->where('event_identifier', 'register')
            ->get()
            ->toArray();
//        dd($register);
        $zhuce = [];
        foreach ($register as $rgst) {
            foreach ($rgst['eventdata'] as $k => $event) {
                $zhuce[$k]['imei'] = $event['imei'];
                $zhuce[$k]['platform'] = $event['platform'];
                $zhuce[$k]['clientdate'] = $event['clientdate'];
            }
        }

        //获取广点通点击数据
        $result = Gdt::with('gdtStatus')
            ->whereNotIn('gdt.id', function ($query) {
                $query->from('gdt_status')
                    ->where('type', 'register')
                    ->where('status', 'reported')
                    ->select('gdt_id');
            })
            ->get()
            ->toArray();
        $activate = [
            0 => [
                "id" => 10258,
                "platform" => "iOS",
                "imei" => "1bba8b40119f80e68f2c626bbee0eedf469f8d8f",
                "date" => "2017-05-03 09:09:37",
            ]];

        //循环上传激活数据
        foreach ($activate as $ate) {
            $conv_type = 'activate';
            //iOS
            if (!empty($ate['platform']) && strtolower($ate['platform']) === 'ios') {
                foreach ($result as $rst) {
                    dd(md5(strtoupper($ate['imei'])));
                    $imei = md5(strtoupper($ate['imei']));
                    if ($rst['muid'] === $imei) {
                        $time = strtotime($ate['date']);
                        $url = self::setting($rst, $time, $conv_type);
                    }
                }
                //android
            } elseif (!empty($ate['platform']) && strtolower($ate['platform']) === 'android') {
                foreach ($result as $rst) {
                    $imei = md5(strtolower($ate['imei']));
                    if ($rst['muid'] === $imei) {
                        $time = strtotime($ate['date']);
                        $url = self::setting($rst, $time, $conv_type);
                    }
                }
            }
        }

        //循环上传注册数据
        foreach ($zhuce as $value) {
            $conv_type = 'register';
            //iOS
            if (!empty($value['platform']) && strtolower($value['platform']) === 'ios') {
                foreach ($result as $rst) {
                    dd(md5(strtoupper($value['imei'])));
                    $imei = md5(strtoupper($value['imei']));
                    if ($rst['muid'] === $imei) {
                        $time = strtotime($value['clientdate']);
                        $url = self::setting($rst, $time, $conv_type);
                    }
                }
                //android
            } elseif (!empty($value['platform']) && strtolower($value['platform']) === 'android') {
                foreach ($result as $rst) {
                    $imei = md5(strtolower($value['imei']));
                    if ($rst['muid'] === $imei) {
                        $time = strtotime($value['clientdate']);
                        $url = self::setting($rst, $time, $conv_type);
                    }
                }
            }
        }


//        return $http;
    }

    //配置参数
    static function setting($result, $time, $conv_type)
    {
        static $array = [
            'advertiser_id' => '3972252',//广告主id
            'encrypt_key' => 'BAAAAAAAAAAAPJyc',//秘钥A
            'sign_key' => '74a7c6feaa377ce6',//秘钥B
        ];
        //获取参数
        $gdt_id = $result['id'];
        $muid = $result['muid'];                //设备id
        $appid = $result['appid'];              //Android/ios应用id
        $click_id = $result['click_id'];        //广点通点击跟踪 id
        $conv_time = $time;                     //转化发生时间(必填)
        $conv_time = '1493797855';                     //转化发生时间(必填)
        if ($conv_type === 'activate') {
            $conv_type = 'MOBILEAPP_ACTIVITE';//转化行为标记参数MOBILEAPP_ACTIVITE(激活)
        } else {
            $conv_type = 'MOBILEAPP_REGISTER';//MOBILEAPP_REGISTER(注册)
        }
        $app_type = $result['app_type'];        //app类型
        $advertiser_id = $array['advertiser_id'];//账户id
        $sign_key = $array['sign_key'];         //广点通用户签名密钥
        $encrypt_key = $array['encrypt_key'];   //广点通账户加密密钥

        //组合参数
        $query_string = 'muid=' . $muid . '&conv_time=' . $conv_time . '&click_id=' . $click_id;
        //参数签名
        $page = 'http://t.gdt.qq.com/conv/app/' . $appid . '/conv?' . $query_string;
        //参数签名urlencode
        $encode_page = urlencode($page);
        //组装字符串property
        $property = $sign_key . '&GET&' . $encode_page;
        //加密生成signature
        $signature = md5($property);
        //参数加密
        $base_data = $query_string . '&sign=' . urlencode($signature);
        //简单异或+base64
        $data = base64_encode(self::simple_xor($base_data, $encrypt_key));
        //组装请求
        $attachment = 'conv_type=' . $conv_type . '&app_type=' . strtoupper($app_type) . '&advertiser_id=' . $advertiser_id;
        //最终请求
        $http = 'http://t.gdt.qq.com/conv/app/' . $appid . '/conv?v=' . $data . '&=' . $attachment;
        dd($http);
        //上报转化
        $status = self::sendurl($http);
        if ($status['ret'] === 0) {
            Status::create(['gdt_id' => $gdt_id, 'type' => 'activate', 'status' => 'reported']);
        }
        dd($status);
        return $status;
    }

    //异或加密
    static function simple_xor($info, $key)
    {
        $result = '';
        if (empty($info) || empty($key)) {
            return $result;
        }
        $k = 0;
        $keylen = strlen($key);
        for ($i = 0; $i < strlen($info); ++$i) {

            $result .= $info[$i] ^ $key[$k];
            $k = ++$k % $keylen;


        }
        return $result;
    }

    //接口二，访问
    static function sendurl($url)
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $data = curl_exec($curl);
        curl_close($curl);
        return json_decode($data, true);
    }
}