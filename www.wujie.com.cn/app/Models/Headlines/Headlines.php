<?php
/**广点通模型
 * Created by PhpStorm.
 * User: yaokai
 * Date: 2017/5/2
 * Time: 11:10
 */
namespace App\Models\Headlines;

use App\Models\Gdt\Clientdata;
use Illuminate\Database\Eloquent\Model;
use \DB, Closure, Input;
use Validator;

class Headlines extends Model
{

    protected $dateFormat = 'U';

    protected $table = 'headlines';
    //黑名单
    protected $guarded = ['id'];

    public static $_RULES = [//验证规则

    ];
    public static $_MESSAGES = [//验证字段说明

    ];


    /**
     *
     */
    static function handle($idfa)
    {
        $key = 'f8d7be32-8cb9-4d47-97a7-965cb9936caf';//加密密钥
        $time = (time()*1000-24*3600*7*1000);//一周内的数据

        //是否有这个手机
//        $a = Clientdata::where('platform','iOS')->where('imei',$idfa)->value('id');

        //已上报的不用再上报
        $status = self::select('status')->where('idfa',$idfa)->get()->toArray();
        $status = array_unique(array_flatten($status));
        if(!in_array('1',$status)){
            //最大id
            $id = self::select(DB::raw('MAX(id) as id'))
                ->where('idfa',$idfa)
                ->value('id');
            $callback_url = self::where('id',$id)
                ->where('status','0')
                ->value('callback_url');
            //如果存在，则进行签名回调
            if($callback_url){
                $sign = md5($callback_url.$key);
                $url = $callback_url.'&sign='.$sign;//拼接回调地址  签名放最后面
            }else{
                return;
            }
            //修改上报状态
            self::where('id',$id)->update(['status'=>'1']);
//        $url= 'http://appfly.com/send/idfa/?idfa=0E210CC5-44EA-490C-B95D-67E45AE93D43&aid=1234567';
//        $convert_secret_key = 'ffbba6e3-9edc-456d-8de2-bdffdbee22d7';
//        $sign = md5($url.$convert_secret_key);
            return self::sendurl($url);
        }
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