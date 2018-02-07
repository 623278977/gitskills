<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    //

    protected $table = 'setting';


    public $timestamps = false;


    /**
	 * 返回缓存或者更新缓存
	 *
	 * @return array
	 */

	static function cache($cache = 0)
	{
		$settings =  \Cache::has('setting') ? \Cache::get('setting') : false;
		
		if ($settings === false || $cache)
		{
			$data = array();
			$settings = self::all();
			foreach ($settings as $setting) {
				$data[$setting->variable]['variable'] = $setting->variable;
				$data[$setting->variable]['name'] = $setting->name;
				$data[$setting->variable]['value'] = $setting->value;
			}
			$settings = $data;
			\Cache::put('setting', $data,86400);
		}
		return $settings;
	}

}
