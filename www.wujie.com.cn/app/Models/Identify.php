<?php
/**
 * 短信验证码
 * @author Administrator
 *
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Identify extends Model
{
    protected $table = 'identify';

    protected $fillable = array('uid', 'code', 'mobile', 'type', 'app_name','nation_code','non_reversible');

    protected $dateFormat = 'U';

    protected function getDateFormat()
    {
        return date(time());
    }

    /**
     * 验证 验证码
     * @User yaokai
     * @param $phone md5加盐后的手机号
     * @param $type 验证码类型
     * @param $code 验证码
     * @param int $time 多久失效(默认半个小时)
     * @param string $app_name app名称
     * @return string
     */
    static function checkIdentify($phone, $type, $code, $time = 900, $app_name = 'wjsq')
    {
        //验收环境任意验证码都能通过
        if (app()->environment() === 'beta') {
            return 'success';
        }

        $identify = self::getRow(array(
            'code' => $code,
            'non_reversible' => $phone,
            'app_name' => $app_name,
            'type' => $type
        ));
        if ($identify) {
            if ($identify->status == 1 || $identify->status == 2) {
                return '验证码无效！';
            }
            if ((time() - strtotime($identify->created_at)) > $time) {//过期
                return '验证码过期!';
            } else {//正确
                $identify->status = 1;
                $identify->save();
                $identifys = self::where(array(
                    'non_reversible' => $phone,
                    'type' => $type,
                    'app_name' => $app_name,
                    'status' => 0,
                ))
                    ->where('created_at', '>=', time() - $time)
                    ->get();
                foreach ($identifys as $item) {
                    $item->status = 2;
                    $item->save();
                }
                return 'success';
            }
        } else {//错误
            return '验证码错误!';
        }
    }

    static function getRow($where)
    {
        return self::where($where)->orderBy('created_at', 'desc')->first();
    }
}
