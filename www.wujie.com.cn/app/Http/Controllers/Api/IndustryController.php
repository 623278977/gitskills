<?php
/****行业控制器********/
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\CommonController;
use App\Models\Industry;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class IndustryController extends CommonController {
	/*
	 *获取行业数据
	 */
	public function postList(){
		return AjaxCallbackMessage(Industry::cache(),true);
	}

}