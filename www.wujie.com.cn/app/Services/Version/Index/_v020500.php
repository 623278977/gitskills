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

class _v020500 extends VersionSelect
{
    /*
     * 点播详情
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
            ->where('status', 'show');


        $sequence = $activity
            ->union($live)
            ->union($brand)
            ->union($video)
            ->union($news)
            ->skip(($page-1) * $pageSize)
            ->take($pageSize)
            //->distinct()
            ->orderBy('index_sort', 'desc')
            ->orderBy('created_at', 'desc')
            ->orderBy('id','desc')
            ->get();
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

            switch ($obj->type) {
                case 'activity':
                    $activity_obj = Activity::find($obj->id);
                    $obj->activity = $this->getActivityData($activity_obj);
                    break;
                case 'live':
                    $obj->live = $this->getLiveData($obj);
                    break;
                case 'video':
                    $obj->video = $this->getVideoData($obj);
                    break;
                case 'news':
                    $obj->news = $this->getNewsData($obj);
                    break;
                case 'brand':
                    $obj->brand = $this->getBrandData($obj);
                    break;
                default:
                    break;
            }

            return $obj;


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



    /*
    * 生成code
    */
    public function postCode($param)
    {
        $return = md5(uniqid().rand(1111,9999));
        return ['message'=>$return ,'status'=>true];
    }

}