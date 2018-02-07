<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Type extends Model
{
    //

    protected $table = 'type';


    public $timestamps = false;


    /**
	 * 返回缓存或者更新缓存
	 *
	 * @return array
	 */

	static function cache($cache = 0)
	{
		$types =  Cache::has('type') ? \Cache::get('type') : false;

		if ($types === false || $cache)
		{
			$data = array();
			$types = self::whereRaw('upid = 0 and status = 1')->orderBy('sort')->get();

			foreach ($types as $type)
			{
				$data[$type->code] = array();
				$children = Type::where('upid',$type->id)->orderBy('sort','desc')->get();
				foreach ($children as $child)
				{
					if ($child->status < 1) {
						continue;
					}
					if ($child->code) {
						$data[$type->code][$child->code] = $child['name'];
					}
					else {
						$data[$type->code][] = $child['name'];
					}
				}
			}
			$types = $data;
			$result = \Cache::put('type', $data,1440);
		}
		return $types;
	}

}
