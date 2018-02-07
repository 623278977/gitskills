<?php

namespace App\Services\Version\News;

use App\Models\Activity\Live;
use App\Models\User\Praise;
use App\Services\Version\VersionSelect;
use App\Models\Activity\Entity as Activity;
use App\Models\Order\Entity as Order;
use App\Models\Activity\Sign;
use App\Models\ScoreLog;
use App\Models\Activity\Ticket;
use App\Models\Maker\Entity as Maker;
use App\Models\User\Entity as User;
use App\Models\Brand\Entity as Brand;
use App\Models\Vip\Entity as Vip;
use App\Models\Vip\User as Vip_User;
use App\Models\Vip\Term as ViP_term;
use App\Models\User\Ticket as User_Ticket;
use DB;
use App\Services\News;

class _v020500 extends VersionSelect
{


    /*
     * 资讯详情
     */
    public function postDetail($param = [])
    {
        $newsService = new News();
        $list = $newsService->detail($param['id']);
        //分享标识码
        $list->share_mark = makeShareMark($param['id'], 'news', $param['uid']);
//        $list->code = md5(uniqid().rand(1111,9999));
        //该用户对该目标点击缓存加1
        if($param['uid']){
            $origin_cache = \Cache::get('news' . $param['id'] . 'view' . $param['uid'], 0);
            \Cache::forever('news' . $param['id'] . 'view' . $param['uid'], $origin_cache+1);
        }
        return ['data' => $list, 'status' => true];
    }


    








}