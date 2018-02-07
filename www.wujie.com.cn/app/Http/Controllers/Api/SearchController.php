<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/2/29
 * Time: 10:15
 */
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\CommonController;
use Illuminate\Http\Request;
use App\Models\User;

class SearchController extends CommonController
{
    public function postList(Request $request, $version = null)
    {
        if(in_array($version , ['_v020400'])){
            $version = null;
        }

        $data = $request->input();

        //初始化
        $versionService = $this->init(__METHOD__, $version);

        if ($versionService) {
            $response = $versionService->bootstrap($data, ['request' => $request ,'version' => $version]);
            return AjaxCallbackMessage($response['message'], $response['status']);
        }

        //兼容参数传的不一致
        $keywords = $request->input('keywords');
        $keyword = $request->input('keyword');
        if (empty($keyword) && empty($keywords))
            return AjaxCallbackMessage('关键词不能为空', false);

        return AjaxCallbackMessage(array(
            'video' => call_user_func_array(array(new VideoController(), 'postList'), array($request)),
            'live' => call_user_func_array(array(new LiveController(), 'postList'), array($request,null, 1)),
            'activity' => call_user_func_array(array(new ActivityController(), 'postList'), array($request)),
            'subscribe' => call_user_func_array(array(new LiveController(), 'postUserSubscribe'), array($request)),
            'opportunity' => call_user_func_array(array(new OpportunityController(), 'postList'), array($request)),
            'brand' => call_user_func_array(array(new BrandController(), 'postLists'), array(NULL, $request->input('is_return', 1), 0))
        ), true);

    }

    /*
     * 热门关键词,供app选择展示用
     */
    public function postHotwords(Request $request , $version = null)
    {
        $data = $request->input();

        //初始化
        $versionService = $this->init(__METHOD__, $version);

        if ($versionService) {
            $response = $versionService->bootstrap($data ,['request' => $request]);
            return AjaxCallbackMessage($response['message'], $response['status']);
        }

        return AjaxCallbackMessage('该api接口不再维护', false);
    }
}