<?php
/****专版控制器********/
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\CommonController;
use App\Http\Requests\Vip\DetailRequest;
use App\Http\Requests\Vip\RecommendRequest;
use App\Http\Requests\Vip\RecordRequest;
use App\Models\Ad;
use App\Models\User\Favorite;
use App\Models\Video\Entity as Video;
use App\Models\VideoType;
use Illuminate\Http\Request;
//use Illuminate\Support\Facades\Request;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use App\Models\Order\Entity as Order;
use App\Models\User\Ticket;
use App\Models\Vip\Entity as Vip;
use App\Models\Live\Entity as Live;
use App\Models\Activity\Entity as Activity;
use App\Http\Requests\VipRecordRequest;
class VipController extends CommonController
{
    private $vip_id;

    public function __construct(Request $request)
    {
        Parent::__construct($request);
        $vip_id = $request->input('vip_id');
        if ($vip_id && !isInt($vip_id)) {
            $this->vip_id = -1;
        } elseif ($vip_id && isInt($vip_id)) {
            $this->vip_id = $request->input('vip_id');
        }
    }

    /*
    * 作用:获取可用专版列表
    * 参数:无
    *
    * 返回值:json
    */
    public function postList(Request $request)
    {
        $uid = $request->get('uid', 0);
        $responseData = Vip::getAllLists($uid);
        $responseData = $responseData->toArray();
        shuffle($responseData);
        return AjaxCallbackMessage($responseData, true);
    }

    /**
     * 获取专版详情
     */
    public function postDetail(DetailRequest $request)
    {
        $attach = $request->input('attach', 0);
        $agreement = $request->input('agreement', 0);
        $package = $request->input('package', 0);
        $result = Vip::detail($this->vip_id, $this->uid, $attach, $agreement, $package);

        return AjaxCallbackMessage($result, true);
    }

    /**
     * 获取专版推荐的资源
     */
    public function postRecommend(Request $request)
    {
        if ($this->vip_id == -1) {
            return AjaxCallbackMessage('vip_id一定要为正整数', false);
        };
        $resource = $request->input('resource');
        $position_id = $request->input('position_id', 0);
//        $resource = array('activity' => 2, 'video' => 2, 'live' => 3, 'vip' => 4);
        $result = Vip::recommend($this->vip_id, $resource, $this->uid,$position_id);

        return AjaxCallbackMessage($result, true);
    }
    
    /*
    * 作用:专版下的搜索
    * 参数:
    * 
    * 返回值:
    */
    public function postSearch(Request $request)
    {
        $vip_id = $request->get('vip_id', '');
        $keywords = $request->get('keywords', '');
        $keyword  = $request->get('keyword', '');
        $keywords = $keywords == '' ? $keyword : $keywords;
        $category = $request->get('category','');
        $page = $request->get('page',1);
        $pageSize = $request->get('pageSize',15);
        if ($vip_id == '') {
            return AjaxCallbackMessage('专版ID为空', false);
        }
        $responseData = [];
        //专版名称
        $vip = \DB::table('vip')->where('id',$vip_id)->first();
        $responseData['vip_name'] = $vip->name;
        if($category == 1){
            $responseData ['vip_activity'] = Activity::getVipActivity($vip_id, $keywords,$pageSize);
        }elseif($category == 2){
            $responseData ['vip_live'] = Live::getVipLive($vip_id, $keywords,$pageSize);
        }elseif($category == 3){
            $responseData ['vip_video'] = Video::getVipVideo($vip_id, $keywords,$pageSize);
        }else{
            $responseData ['vip_activity'] = Activity::getVipActivity($vip_id, $keywords,$pageSize);
            $responseData ['vip_video'] = Video::getVipVideo($vip_id, $keywords,$pageSize);
            $responseData ['vip_live'] = Live::getVipLive($vip_id, $keywords,$pageSize);
        }

        return AjaxCallbackMessage($responseData, true);
    }

    /**
     *获取某用户的专版购买记录
     */
    public function postRecord(RecordRequest $request)
    {
        $records = Vip::records($this->uid, $this->vip_id);

        return AjaxCallbackMessage($records, true);
    }

}