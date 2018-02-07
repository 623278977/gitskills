<?php


namespace App\Http\Controllers\Agent;
use App\Http\Controllers\Api\CommonController;
use Illuminate\Http\Request;
use App\Models\Video\Entity as VideoModel;


/**
 * 首页——石清源
 */
class VideoController extends CommonController
{


    /*
     * 招商现场列表接口
     *
     * */
    public function postList(Request $request, $version = NULL){
        $versionService = $this->init(__METHOD__, $version, null, 'Agent');
        if($versionService){
            $response = $versionService->bootstrap($request->all());
            return AjaxCallbackMessage($response['message'],$response['status']);
        }
        return AjaxCallbackMessage('api接口不再维护',false);
    }

    /**
     * 招商现场列表--搜索需要
     *
     * @param Request $request
     * @param null $version
     *
     * @return string
     */
    public function postLists(Request $request, $version = NULL)
    {
        $result = json_decode($this->postList($request, $version),true);
        return $result['message'];
    }

    /*
     * 招商现场详情接口
     *
     * */
    public function postDetail(Request $request, $version = NULL){
        $versionService = $this->init(__METHOD__, $version, null, 'Agent');
        if($versionService){
            $response = $versionService->bootstrap($request->all());
            return AjaxCallbackMessage($response['message'],$response['status']);
        }
        return AjaxCallbackMessage('api接口不再维护',false);
    }

    /*
    * 品牌学习视频详情接口
    *
    * */
    public function postStudyVideoDetail(Request $request, $version = NULL){
        $versionService = $this->init(__METHOD__, $version, null, 'Agent');
        if($versionService){
            $response = $versionService->bootstrap($request->all());
            return AjaxCallbackMessage($response['message'],$response['status']);
        }
        return AjaxCallbackMessage('api接口不再维护',false);
    }


    /**
     * 品牌课程视频打卡列表
     * @User yaokai
     * @param Request $request
     * @param null $version
     * @return string
     */
    public function postClock(Request $request, $version = NULL)
    {
        $input = $request->all();
        if (empty($input['video_id'])) {
            return AjaxCallbackMessage('缺少视频id', false);
        }
        $versionService = $this->init(__METHOD__, $version, null, 'Agent');
        if ($versionService) {
            $response = $versionService->bootstrap($request->all());
            return AjaxCallbackMessage($response['message'], $response['status']);
        }
        return AjaxCallbackMessage('api接口不再维护', false);
    }

}