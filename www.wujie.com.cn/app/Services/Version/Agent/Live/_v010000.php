<?php

namespace App\Services\Version\Agent\Live;

use App\Services\Version\VersionSelect;
use App\Models\Live\Entity as LiveModel;
class _v010000 extends VersionSelect
{
    /*
    * 直播大厅接口
    *
    * */
    public function postList($input){
        $page = empty($input['page']) ? 1 : intval($input['page']);
        $pageCount = empty($input['page_size']) ? 10 : intval($input['page_size']);
        $data=LiveModel::getLiveList($page,$pageCount);
        return ['message' => $data, 'status' => true];
    }

    /*
     *
     * 直播详情接口
     *
     * */
    public function postDetail($input){
        $agentId=intval($input['agent_id']);
        $liveId=intval($input['id']);
        if(empty($liveId)){
            return ['message' => "请传递直播id", 'status' => false];
        }
        $data=LiveModel::getLiveDetail($liveId);
        if(isset($data['error'])){
            return ['message' => $data["message"], 'status' => false];
        }
        return ['message' => $data, 'status' => true];
    }



}