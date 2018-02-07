<?php

namespace App\Services\Version\Index;

use App\Services\News;
use App\Services\Version\VersionSelect;
use App\Models\Activity\Entity as Activity;
use App\Models\Video;
use App\Models\Live\Entity as Live;
use App\Models\Brand\Entity as Brand;
use App\Models\Ad;
use \DB;

class _v020700 extends _v020600
{
    /*
     * 首页详情
     */
    public function postPubliclist($param)
    {
        $page = (int)$param['request']->input('page', 1);
        $pageSize = (int)$param['request']->input('page_size', 10);

        //列表顺序,按照create_at倒序排列
        $activity = DB::table('activity')
            ->where('status', 1)
            ->where('end_time' , '>' ,time())
            ->select('id', 'created_at','index_sort', DB::raw("'activity' as type"));

        $live = DB::table('live')
            ->where('end_time' , '>' ,time())
            ->select('id', 'created_at','index_sort', DB::raw("'live' as type"))
            ->where('status', 0);

        $brand = DB::table('brand')
            ->select('id', 'created_at', 'index_sort',DB::raw("'brand' as type"))
            ->where('status', 'enable');

        $video = DB::table('video')
            ->select('id', 'created_at','index_sort', DB::raw("'video' as type"))
            ->where('status', 1);

        $news = DB::table('news')
            ->select('id', 'created_at', 'index_sort',DB::raw("'news' as type"))
            ->where('status', 'show')
            //Todo 仅查出商圈APP的资讯
            ->whereIn('type',['none','brand']);

        // APP2.7首页新增广告
        $ad = DB::table('ad')
            ->select('id','start_time','index_sort',DB::raw("'ad' as type"))
            ->where('status',1)
            ->where('type','index_reserved');

//        $brand_list = DB::table('brand')
//            ->select('id','created_at','index_sort', DB::raw("'brand_list' as type"))
//            ->where('status','enable');


        if($page>1){//从第二页开始取品牌列表数据
            $pageSize = $pageSize-1;
        }

        if($page==1){
            $skip = 0;
        }else{
            $skip = ($page-1) * $pageSize+1;
        }

        $sequence = $activity
            ->union($live)
            ->union($brand)
            ->union($video)
            ->union($news)
            ->union($ad) // TODO 获取接口需要的广告数据
//            ->union($brand_list)
            ->skip($skip)
            ->take($pageSize)
            //->distinct()
            ->orderBy('index_sort', 'desc')
            ->orderBy('created_at', 'desc')
            ->orderBy('id','desc')
            ->get();



        if($page>1){//从第二页开始放品牌列表数据，再每一页的第9个位置放品牌列表
            $brandList = collect(['type'=>'brand_list']);
            array_splice($sequence, 8, 0, $brandList);
        }


        //格式化数据
        $return = array_map($this->formatData(), $sequence);




        return ['message'=>$return ,'status'=>true];

    }

    /*
     * 格式化数据
     */
    private function formatData()
    {
        $func = function ($obj) {

            $arr_obj = (array) $obj;
            switch ($arr_obj['type']) {
                case 'activity':
                    $activity_obj = Activity::find($obj->id);
//                    $obj->activity = $this->getActivityData($activity_obj);
                    $arr_obj['activity'] = $this->getActivityData($activity_obj);
                    break;
                case 'live':
//                    $obj->live = $this->getLiveData($obj);
                    $arr_obj['live'] = $this->getLiveData($obj);
                    break;
                case 'video':
//                    $obj->video = $this->getVideoData($obj);
                    $arr_obj['video'] = $this->getVideoData($obj);
                    break;
                case 'news':
                    $data = $this->getNewsData($obj);
                    unset($data['detail']);//删除多余字段
//                    $obj->news = $data;
                    $arr_obj['news'] = $data;
                    break;
                case 'brand':
//                    $obj->brand = $this->getBrandData($obj);
                    $arr_obj['brand'] = $this->getBrandData($obj);
                    break;
                // 获取品牌列表（全部查出返回即可）
                case 'brand_list':
//                    $obj->brand_list = $this->getBrandlistData();
                    $arr_obj['brand_list'] = $this->getBrandlistData();
                    break;
                case 'ad':
//                    $obj->ad = $this->getAdData($obj);
                    $arr_obj['ad'] = $this->getAdData($obj);
                    break;
                default:
                    break;
            }

            return $arr_obj;

        };

        return $func;
    }

    private function getActivityData($obj)
    {
        return Activity::getPublicData($obj);
    }

    private function getLiveData($obj)
    {
        return Live::getPublicData($obj);
    }

    private function getVideoData($obj)
    {
        return Video::getPublicData($obj);
    }

    private function getNewsData($obj)
    {
        $news = new News();
        return $news->getPublicData($obj);
    }

    private function getBrandData($obj)
    {
        return Brand::getPublicData($obj);
    }
    // TODO 修改方法的名称 Advertisement()，且在模型中增加getPublicData($obj)方法
    private function getAdData($obj)
    {
        return Ad::getPublicData($obj);
    }
    private function getBrandlistData()
    {
        return Brand::getPubliclistData();
    }

    //获取指定数据部分数据源
    public function postData($param) {
        $data = parent::postData($param)['message'];
        //删除无用字段
        unset($data['detail']);
        return ['message' => $data, 'status' => true];
    }

}