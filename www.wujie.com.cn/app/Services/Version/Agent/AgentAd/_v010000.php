<?php
namespace App\Services\Version\Agent\AgentAd;
use App\Services\Version\VersionSelect;
use App\Models\Agent\AgentAd;

class _v010000 extends VersionSelect{

    public function postList($data, $page = 1, $pageSize = 5 )
    {
        $request = $data['request'];
        $type = trim($request->input('type')); //广告banner类型位置

        $type = array_search($type,AgentAd::$_TYPE);

        if(!$type){
            return ['data' => '非法的类型' ,'status' => false];
        }
        $ad = AgentAd::where('type', $type)
            ->where('start_time', '<', date('Y-m-d H:i:s'))
            ->where('expired_time', '>', date('Y-m-d H:i:s'))
            ->where('status', 1)
            ->orderBy('sort','desc')
            ->orderBy('created_at','desc')
            ->skip(($page-1)*$pageSize)
            ->take($pageSize)
            ->paginate($pageSize);
        $lists = [];
        foreach($ad as $k=>$v){
            $lists[$k]=AgentAd::getBase($v);
        }
        return ['data' =>  $lists , 'status' => true];
    }
}