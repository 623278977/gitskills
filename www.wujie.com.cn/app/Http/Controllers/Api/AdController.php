<?php
/****广告banner控制器********/
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\CommonController;
use App\Models\Ad;
use Illuminate\Http\Request;

class AdController extends CommonController
{
    /*
     * 根据不同type取ad数据
     * type 为数组
     */
    public function postList(Request $request, $version=null)
    {
        $data = $request->input();
        $type = $request->input('type') ?: 'app_index_banner';//广告banner类型位置
        $page = $request->input('page', 1);
        $page_size = $request->input('page_size', 5);

        if ($version) {
            $versionService = $this->init(__METHOD__, $version);
            $result = $versionService->bootstrap($data, ['version' => $version ,'request' => $request]);
            return AjaxCallbackMessage($result['data'],$result['status']);
        }
        return AjaxCallbackMessage(Ad::getAds($type), true);
    }

}