<?php
namespace App\Http\Controllers\Api;

use App\Http\Requests\Share\CollectRequest;
use DB, Input;
use Illuminate\Http\Request;
use App\Models\Config;

class ShareController extends CommonController
{
    /**
     * 分享积分入库
     */
    public function postCollectScore(CollectRequest $request, $version = null)
    {
        $versionService = $this->init(__METHOD__, $version);
        $begin_time = $request->cookie('begin_time', 0);

        if($begin_time){
            $watch_long = (time()-$begin_time)/60;
        }else{
            $watch_long = 0;
        }



        if ($versionService) {
            $response = $versionService->bootstrap($request->all(),['watch_long'=>$watch_long]);

            return AjaxCallbackMessage($response['message'], $response['status']);
        }

        return AjaxCallbackMessage('api接口不再维护', false);
    }


    /**
     * 分享记录入库
     */
    public function postShare(Request $request, $version = null)
    {
        $versionService = $this->init(__METHOD__, $version);
        $data = $request->all();

        if(empty($data['code'])){
            $data['code'] = md5(uniqid() . rand(1111, 9999));
        }

        if ($versionService) {
            $response = $versionService->bootstrap($data);

            return AjaxCallbackMessage($response['message'], $response['status']);
        }

        return AjaxCallbackMessage('api接口不再维护', false);
    }


    /**
     * 我的分享记录
     */
    public function postMyShare(Request $request, $version = null)
    {
        $versionService = $this->init(__METHOD__, $version);
        $page = $request->get('page', 1);
        $page_size = $request->get('page_size', 10);

        if ($versionService) {
            $response = $versionService->bootstrap($request->all(),['page'=>$page, 'page_size'=>$page_size]);

            return AjaxCallbackMessage($response['message'], $response['status']);
        }

        return AjaxCallbackMessage('api接口不再维护', false);
    }



    /**
     * 分销详情
     */
    public function postShareDetail(Request $request, $version = null)
    {
        $page = $request->get('page', 1);
        $page_size = $request->get('page_size', 10);
        $versionService = $this->init(__METHOD__, $version);

        if ($versionService) {
            $response = $versionService->bootstrap($request->all(), ['page'=>$page, 'page_size'=>$page_size]);
            return AjaxCallbackMessage($response['message'], $response['status']);
        }

        return AjaxCallbackMessage('api接口不再维护', false);
    }



    /**
     * 分销有奖
     */
    public function postShareList(Request $request, $version = null)
    {
        if(!$request->input('uid')){
            return AjaxCallbackMessage('当前登录用户uid是必传参数', false);
        }
        $page = $request->get('page', 1);
        $page_size = $request->get('page_size', 15);
        $keyword = $request->get('keyword', '');
        $versionService = $this->init(__METHOD__, $version);
        if ($versionService) {
            $response = $versionService->bootstrap($request->all(), ['page'=>$page, 'page_size'=>$page_size, 'keyword'=>$keyword]);
            return AjaxCallbackMessage($response['message'], $response['status']);
        }
        return AjaxCallbackMessage('api接口不再维护', false);
    }

    /**
     * 生成分享短链接
     * @param Request $request
     * @return string
     */
    public function postShortUrl(Request $request){
        $url=$request->get('url');
//        if (strpos($url, 'https://' . $request->getHttpHost() . '/') !== 0
//            && strpos($url, 'http://' . $request->getHttpHost() . '/') !== 0) {
//            return AjaxCallbackMessage('链接地址不合法', false);
//        }
        if ($shorturl = shortUrl($url)) {
            return AjaxCallbackMessage($shorturl, true);
        }
        return AjaxCallbackMessage('生成失败', false);
    }
    /**
     * 生成分享地址
     */
    public function postShareUrl(Request $request){
        $service=new \App\Services\ShareService();
        if(!$request->has('id','type','uid')){
            return AjaxCallbackMessage('参数丢失', false);
        }
        $url= $service->detailShareUrl($request->get('id'), $request->get('type'), $request->get('uid'));
        if($url){
            return AjaxCallbackMessage($url, true);
        }
        return AjaxCallbackMessage('生成失败', false);
    }



    //分享有奖可收益
    public function postProfit(Request $request, $version = null)
    {
        //老版本走这个代码
        if(in_array($version, ['_v020600', '_v020602'])){
            $values = array_pluck(Config::where('name','like', 'distribution_profit_%')->get(['code','value']),'value','code');
            return AjaxCallbackMessage([
                'total' => array_get($values, 'distribution_profit_total', 236548552),
                'brands' => array_get($values, 'distribution_profit_brands', 200),
                'live' => array_get($values, 'distribution_profit_live', 150),
                'maker' => array_get($values, 'distribution_profit_maker', 100)
            ], true);
        }

        $versionService = $this->init(__METHOD__, $version);

        if ($versionService) {
            $response = $versionService->bootstrap($request->all());

            return AjaxCallbackMessage($response['message'], $response['status']);
        }

        return AjaxCallbackMessage('api接口不再维护', false);
    }


    /**
     * 获取下属
     */
    public function postSubordinates(Request $request, $version = null)
    {
        if(!$request->has('content','content_id','uid')){
            return AjaxCallbackMessage('参数丢失', false);
        }
        $versionService = $this->init(__METHOD__, $version);
        if ($versionService) {
            $response = $versionService->bootstrap($request->all());
            return AjaxCallbackMessage($response['message'], $response['status']);
        }
        return AjaxCallbackMessage('api接口不再维护', false);
    }

}