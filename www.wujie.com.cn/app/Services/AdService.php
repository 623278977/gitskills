<?php

namespace App\Services;

use App\Models\Brand\Entity as BrandModel;
use App\Models\Brand\Images;
use App\Models\Brand\Quiz;
use App\Models\News\Entity as News;
use App\Models\User\Favorite;
use App\Models\Ad;
class AdService
{
    /**
     * 作用:根据广告类型分页获取广告列表
     * 参数:$type 类型  $page 分页  $pageSize 分页size
     *
     * 返回值:
     */
    public function lists($type, $page = 1, $pageSize = 5 ,$version = null)
    {
        $list = Ad::where('type', $type)
            ->where('start_time', '<', time())
            ->where('expired_time', '>', time())
            ->where('status', 1)
            ->orderBy('sort','desc')
            ->orderBy('created_at','desc')
            ->skip(($page-1)*$pageSize)
            ->take($pageSize)
            ->paginate($pageSize);
        $data = [];
        foreach($list as $k=>$v){
            $data[$k]=$version ? Ad::getBase($v , $version) : Ad::getBase($v);
        }

        return $data;
    }



}