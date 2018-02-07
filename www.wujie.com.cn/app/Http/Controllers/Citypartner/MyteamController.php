<?php

namespace App\Http\Controllers\Citypartner;

use App\Models\Zone;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Models\CityPartner\Entity as CityPartner;
use App\Http\Controllers\Citypartner\CommonController as BaseController;
use DB;

class MyteamController extends BaseController
{
    /**
     * 我的团队
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function anyIndex(Request $request)
    {
        $mid = $request->input('uid', function () {
            return $this->mid;
        });
        $userinfo = $this->userinfo;
        $teaminfo = CityPartner::myTeam($mid);
        if ($teaminfo) {
            foreach ($teaminfo as $item) {
                $item->zone_id = Zone::getZone($item->zone_id);
                $item->zone_id = str_replace('市','',$item->zone_id);
                if($item->amount > 9999){
                    $item->amount2 = explode('.',bcdiv($item->amount,10000,2));
                }
                $item->network_id = DB::table('maker')->where('partner_uid',$item->uid)->where('status',1)->select('id')->first();
            }
        }
        return view('citypartner.myteam.myteam', compact('teaminfo', 'userinfo'));
    }

    /**
     * 团队详情
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function anyDetail(Request $request)
    {
        $uid = $request->input('uid', '');
        $no = $request->input('no');
        $detailInfo = CityPartner::detailinfo($uid);
        $detailInfo[1]->zone_id = Zone::getZone($detailInfo[1]->zone_id);
        if ($detailInfo) {
            $totalamount = 0;
            foreach ($detailInfo[0] as $item) {
                $item->arrival_at = date('Y.m.d H:i', $item->arrival_at);
                $item->arrival_at = explode(' ', $item->arrival_at);
                $totalamount += $item->amount;
            }
        }
        $perPage = $detailInfo[0]->perPage();
        $total = $detailInfo[0]->total();
        $lastPage = $detailInfo[0]->lastPage();
        $nextPageUrl = $detailInfo[0]->nextPageUrl();
        $param = '&uid='.$detailInfo[1]->uid;
        if($nextPageUrl){
            $nextPageUrl = $nextPageUrl . $param;
        }
        $previosuPage = $detailInfo[0]->previousPageUrl();
        if($previosuPage){
            $previosuPage = $previosuPage . $param;
        }
        $currentPage = $detailInfo[0]->currentPage();
        $startPage = ($currentPage - 1) * $perPage + 1;
        $startPage = $startPage > $total ? $total : $startPage;
        $endPage = $currentPage*$perPage;
        $totalPage = ceil($total/$perPage);
        return view('citypartner.myteam.myteam_detail', compact('detailInfo', 'perPage', 'total', 'lastPage', 'nextPageUrl', 'currentPage','startPage','endPage','totalamount','no','previosuPage','totalPage'));
    }
}
