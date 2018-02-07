<?php
/****Video控制器********/
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\CommonController;
use App\Http\Requests\Video\DetailRequest;
use App\Http\utils\randomViewUtil;
use App\Models\Ad;
use App\Models\User\Favorite;
use App\Models\Video;
use App\Models\VideoType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use App\Models\Order\Entity as Order;
use App\Models\User\Ticket;

class VideoController extends CommonController
{
    /*
     *视频类型
     */
    public function postTypelist()
    {
        return AjaxCallbackMessage(VideoType::cache(1), true);
    }

    /**
     * @param Request $request
     * @return string
     * 点播列表
     */
    public function postList(Request $request , $version = null)
    {
        if(in_array($version , ['_v020400'])){
            $version = null;
        }

        if (Auth::check()) {
            $uid = Auth::id();
        } else {
            $uid = $request->input('uid');
        }
        $where = $params = array();
        $page = $request->input('page') ?: 0;
        //针对ios视频列表线上数据返回不正常修改
        $page_size = $request->input('page_size' ,10);
        if ($version >= '_v020500' && $version <= '_v020602' ){
            $page_size = 99;
        }
        if($page > 0 && $version){
            $page = $page-1;
        }
        $vip_id = $request->input('vip_id') ?: 0;
        $type = $request->input('type_id') ?: 0;
        $order = $request->input('order') ?: 'zhineng';//智能 zhineng 最新上传created_at  人气最高view  收藏最多favor_count
        $keywords = $request->get('keywords', '');
        $keyword = $request->get('keyword', '');
        $keyword = empty($keywords) ? $keyword :$keywords;
        //020500版本增加 热门关键字搜索
        $hotwords = $request->input('hotwords' , '');
        //品牌
        $brand_id = $request->input('brand_id' ,'');
        if ($order)
            $params['order'] = $order;
        $selection = $request->input('selection') ?: '';//本地商圈maker_id   小编推荐is_remmend  热门视频is_hot
        if ($selection)
            $params['selection'] = $selection;
        if ($type)
            $where['video.type'] = $type;
        if($keyword)
            $params['keyword'] = $keyword;
        if($hotwords)
            $params['hotwords'] = $hotwords;
        if($brand_id)
            $params['brand_id'] = $brand_id;
        $data = array();
        $list = Cache::has('video_list') ? Cache::get('video_list') : false;
        if ($list === false || $page != 0 || $type || $order || $selection ) {
            $list = array();
            $videos = Video::getRows($where,$vip_id, $page, $page_size, $params);
            if (count($videos)) {
                foreach ($videos as $k => $v) {
                    $data[$k] = Video::getBase($v);
                    $data[$k]['dataCount'] = $videos->dataCount;
                }
                $list = $data;
                Cache::put('video_list', $data, 60);
            }
        }

        if (count($list)) {
            foreach ($list as $k => $v) {
                $list[$k]['small_image'] = isset($videos[$k]->videoType->small_image) ?
                    getImage($videos[$k]->videoType->small_image, '', '', 0) : "";
                $list[$k]['favorite_count'] = $videos[$k]->favor_count;//favorite->count();
                $list[$k]['is_favorite'] = $videos[$k]->favorite($uid)->count();
                strtotime($videos[$k]->created_at->toDateTimeString())>(time()-24*3600*5) ?$list[$k]['is_new']=1:$list[$k]['is_new']=0;
            }
        }

        $is_return=$request->input('is_return')?:0;

        //v020500版本对接口做相关处理
        $versionService = $this->init(__METHOD__, $version);
        if($versionService){
            $response = $versionService->bootstrap(['list' => $list] , ['request' => $request]);

            return ($is_return==1) ? $response['message']: AjaxCallbackMessage($response['message'], $response['status']);
        }
        return ($is_return==1) ? $list: AjaxCallbackMessage($list, true);
    }

    /**
     * 获取点播详情页
     */
    public function postDetail(DetailRequest $request, $version = null)
    {
        $data = $request->input();

        //视频点击数加1
        Video::where('id', $data['id'])->increment('view');
        //伪浏览量
        $sham_view = Video::where('id', $data['id'])->value('sham_view');
        $increment = randomViewUtil::getRandViewCount($sham_view);//增量
        Video::where('id', $data['id'])->increment('sham_view',$increment);

        //初始化
        $versionService = $this->init(__METHOD__, $version);

        if($versionService){
            $response = $versionService->bootstrap($data, ['uid'=>$this->uid]);
            return AjaxCallbackMessage($response['message'], $response['status']);
        }
        $list = Video::detail($data['id'], $this->uid);
        $rec = Video::recommend($data['id'], $this->uid);
        $detail['self'] = $list;
        $detail['rec'] = $rec;
        $detail['page_url'] = createUrl('video/detail',array('id'=>$data['id'],'uid'=>$this->uid,'pagetag'=>config('app.video_detail')));

        return AjaxCallbackMessage($detail, true);
    }
    /**
     * 课程列表
     */
    public function postCurriculum(Request $request , $version = null){
        //v020500版本对接口做相关处理
        $versionService = $this->init(__METHOD__, $version);
        if($versionService){
            $response = $versionService->bootstrap(['request' => $request]);
            return AjaxCallbackMessage($response['message'], $response['status']);
        }
        return AjaxCallbackMessage('接口不存在', FALSE);
    }


    /**
     * 视频购买信息
     */
    public function postBuyInfo(Request $request , $version = null){
        $data = $request->input();
        if (empty($data['id'])) {
            return AjaxCallbackMessage('录播id不能为空', false);
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