<?php
namespace App\Http\Controllers\Api;


use App\Http\Requests\News\DetailRequest;
use App\Http\utils\randomViewUtil;
use Illuminate\Http\Request;
use App\Services\News;
use App\Models\User\Praise;
use App\Models\News\Entity;

class NewsController extends CommonController
{
    /**
     * 获取资讯列表
     */
    public function postList(Request $request, News $news=null,$version)
    {
        if(in_array($version , ['_v020400','_v020500','_v020600','_v020602'])){
            $version = null;
        }
        $page = $request->get('page', 1);
        $page_size = $request->get('page_size', 15);
        $hotwords = $request->get('hotwords','');
        $result = $news->lists($page, $page_size ,$hotwords);

        foreach($result as $item){
            if($item->keywords){
                $item->keywords = strpos($item->keywords,' ')!==FALSE ? explode(' ',$item->keywords) : [$item->keywords];
            }else{
                $item->keywords = [];
            }
            $item->dataCount = $result->dataCount?:0;
            if($version){
                $item->created_at_format = explode(' ',$item->created_at_format)[0];
            }
            $item->detail = preg_replace('/\s+/i','',str_replace('&nbsp;','',strip_tags($item->detail)));
        }
        $is_return=$request->input('is_return')?:0;

        //v020700版本对接口做相关处理
        $versionService = $this->init(__METHOD__, $version);
        if($versionService){
            $response = $versionService->bootstrap(['result' => $result]);
            return ($is_return==1) ? $response['message']: AjaxCallbackMessage($response['message'], $response['status']);
        }
        return $request->input('is_return') ==1 ? $result : AjaxCallbackMessage($result, true);
    }


    /**
     * 获取资讯详情
     */
    public function postDetail(DetailRequest $request, News $news=null, $version = null)
    {
        $data = $request->input();
        $uid = $request->input('uid', 0);

        //资讯浏览量自增1
        Entity::where('id', $data['id'])->increment('view');
        //伪浏览量
        $sham_view = Entity::where('id', $data['id'])->value('sham_view') ?: 1;
        $increment = randomViewUtil::getRandViewCount($sham_view);//增量
        Entity::where('id', $data['id'])->increment('sham_view',$increment);

        //初始化
        $versionService = $this->init(__METHOD__, $version);
        if($versionService){
            $response = $versionService->bootstrap($data, ['uid'=>$uid]);
            return AjaxCallbackMessage($response['data'], $response['status']);
        }
        $id = $request->get('id', 1);
        $result = $news->detail($id);

        return AjaxCallbackMessage($result, true);
    }
    /**
     * 资讯点赞
     */
    public function postZan(Request $request){
        if (!$request['uid'] || !$request['id']) {
            return ['status' => FALSE, 'message' => 'uid和资讯id是必填项'];
        }
        $result=Praise::add($request['uid'], 'news', $request['id']);
        return AjaxCallbackMessage($result, !is_string($result));
    }



    /**
     * 咨询购买信息
     */
    public function postBuyInfo(Request $request , $version = null){
        $data = $request->input();
        if (empty($data['id'])) {
            return AjaxCallbackMessage('资讯id不能为空', false);
        }
        if (empty($data['uid'])) {
            return AjaxCallbackMessage('用户uid不能为空', false);
        }

        $versionService = $this->init(__METHOD__, $version);
        if ($versionService) {
            $response = $versionService->bootstrap($request->all());

            return AjaxCallbackMessage($response['data'], $response['status']);
        }

        return AjaxCallbackMessage('api接口不再维护', false);
    }
}