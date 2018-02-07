<?php

namespace App\Services\Version\Share;

use App\Models\Distribution\Action;
use App\Models\ScoreLog;
use App\Models\Share\Log;
use App\Models\Video;
use App\Services\Distribution\_v020700 as DistributionService;
use App\Services\Version\VersionSelect;
use DB;
use App\Models\Config;

class _v020700 extends _v020600
{
    /**
     * 分享有奖可收益
     */
    public function postProfit($param)
    {
        $values = array_pluck(Config::where('code','like', 'distribution_profit_%')->get(['code','value']),'value','code');

        $values = [
            'total' => array_get($values, 'distribution_profit_total', 236548552),
            'brands' => array_get($values, 'distribution_profit_brands', 200),
            'live' => array_get($values, 'distribution_profit_live', 150),
            'maker' => array_get($values, 'distribution_profit_maker', 100),
            'payed' => array_get($values, 'distribution_profit_payed', 1230515),
        ];


        return ['message' => $values, 'status' => true];
    }


    /**
     * 分销有奖列表
     */
    public function postShareList($param)
    {
        $shareService = new DistributionService();
        $shareDetail = $shareService->shareList($param['uid'], $param['page'], $param['page_size'], $param['keyword'],true);

        return ['message' => $shareDetail, 'status' => true];
    }


    /**
     * 我的分销
     */
    public function postMyShare($param)
    {
        $shareService = new DistributionService();
        $myshare = $shareService->myShare($param['uid'], $param['page'], $param['page_size']);

        return ['message' => $myshare, 'status' => true];
    }


    /**
     * 分销详情
     */
    public function postShareDetail($param)
    {
        $shareService = new DistributionService();
        $shareDetail = $shareService->shareDetail($param['share_id'], $param['page'], $param['page_size']);
        return ['message' => $shareDetail, 'status' => true];
    }


    public function postSubordinates($param)
    {
        $shareService = new DistributionService();
        $subordinate = $shareService->subordinate($param['uid'], $param['content'], $param['content_id'], 1);
        $entity = $shareService->getEntity($param['content'], $param['content_id']);

        return ['message' => ['entity'=>$entity, 'subordinates'=>$subordinate], 'status' => true];
    }



}