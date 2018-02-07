<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Config extends Model
{
    //

    protected $table = 'config';


    public $timestamps = false;


    /**
	 * 返回缓存或者更新缓存
	 *
	 * @return array
	 */

	static function cache($cache = 0)
	{
		$config =  \Cache::has('config_sms_platform') ? \Cache::get('config_sms_platform') : false;
		if ($config === false || $cache)
		{
			$config_sms_platform = \App\Models\Config::where('code','sms_platform')->value('value');
			\Cache::put('config_sms_platform', $config_sms_platform,10);
		}

		if (empty($config_sms_platform) ||!in_array($config_sms_platform, ['santo', 'tencent'])){
			$config_sms_platform = 'tencent';
		}
		return $config_sms_platform;
	}

    /**
     * 功能描述：根据配置标识，取其值
     *
     * 参数说明：
     * @param $flag     配置标识
     *
     * 返回值：
     * @return mixed
     *
     * 实例：
     * 结果：
     *
     * 作者： shiqy
     * 创作时间：@date 2018/1/31 0031 上午 11:46
     */
	public static function getConfigValue($flag){
	    $data = self::where('code',$flag)->value('value');
	    return $data;
    }

}
