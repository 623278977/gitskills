<?php

namespace App\Services\Version\Live;

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
use App\Services\ActivityService;
use App\Models\Live\Entity as Live;

class _v020500 extends _v020400
{


    /*
     * 直播详情页
     */
    public function postDetail($param = [])
    {
        $lists = Live::detail_v25($param['id'], $param['uid']);
        if (isset($param['platform'])) {
            \App\Models\Log\Live::add($param['platform'], $param['id'], $param['uid']);
        }
        $rec = Live::recommend($param['id']);
        $lists['rec'] = $rec;
        $lists['page_url'] = createUrl('live/detail', array('id' => $param['id'], 'uid' => $param['uid'],
                                                            'pagetag' => config('app.live_detail')));
        //分享标识码
        $lists['share_mark'] = makeShareMark($param['id'], 'live', $param['uid']);
//        $lists['code'] = md5(uniqid().rand(1111,9999));
        //该用户对该目标点击缓存加1
        if($param['uid']){
            $origin_cache = \Cache::get('live' . $param['id'] . 'view' . $param['uid'], 0);
            \Cache::forever('live' . $param['id'] . 'view' . $param['uid'], $origin_cache+1);
        }
        return ['message'=>$lists, 'status'=>true];
    }

    /*
     * 获取直播下品牌商品信息
     * */
    public function postGoodsdetail($param = [])
    {
        $data = Live::Goodsdetail($param['live_id']);
        return ['data' => $data, 'status' => true];

    }











}