<?php
/****测试控制器********/
namespace App\Http\Controllers\Api;


use App\Models\Config;
use Illuminate\Http\Request;
use Queue;
use DB;
use App\Models\Video;
use App\Models\Brand\Entity as Brand;
use Route;
use Input;
use ArrayIterator;
use App\Models\Share\Log;

class ConfigController extends CommonController
{

    public function postConfigs(Request $request)
    {
        $data = $request->input();
        $config = Config::where('code', $data['code'])->first();

        return AjaxCallbackMessage($config, true);
    }

}