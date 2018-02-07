<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class CacheTool extends Model
{
	static function clearCache($name){
		Cache::forget($name);
	}
}
