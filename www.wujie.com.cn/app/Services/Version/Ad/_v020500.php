<?php

namespace App\Services\Version\Ad;

use App\Services\AdService;
use App\Services\Version\VersionSelect;
use Validator;
use \DB;

class _v020500 extends _v020400
{

    /*
     * 作用:根据广告类型获取广告列表
     */
    public function postList($data)
    {
        $request = $data['request'];
        $type = $request->input('type') ?: 'app_index_banner';//广告banner类型位置
        $page = $request->input('page', 1);
        $page_size = $request->input('page_size', 5);

        if(!in_array($type,['app_index_banner','find_brand','news','after_welcome','index_reserved','special'])){
            return ['data' => '非法的类型' ,'status' => false];
        }

        $adService = new AdService();
        $list = $adService->lists($type, $page, $page_size , $data['version']);

        return ['data' => $list, 'status' => true];
    }


}