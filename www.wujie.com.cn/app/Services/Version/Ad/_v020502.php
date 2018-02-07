<?php

namespace App\Services\Version\Ad;

use DB;
use App\Models\Ad;

class _v020502 extends _v020500 {
    /*
     * 作用:根据广告类型获取广告列表
     */
    public function postList($data) {
        $request = $data['request'];
        $type = $request->input('type'); //广告banner类型位置
        if ($type !== 'after_welcome') {
            return parent::postList($data);
        }
        $ad = Ad::where('type', $type)
                ->where('start_time', '<', time())
                ->where('expired_time', '>', time())
                ->where('status', 1)
                ->orderBy(DB::Raw('rand()'), 'desc')
                ->first();

                 $result=[Ad::getBase($ad, $data['version'])];

                $link_url = $result['0']['link_url'];//"http://api.wujie.com.cn/webapp/lottery/detail/_v020600";
                $path = parse_url($link_url)['path'];
                $math = strstr($path, 'lottery');
                if ($math != false) {
                    //匹配成功
                    return ['data'=>[], 'status' => true];
                }
            return ['data' => $ad ? [Ad::getBase($ad, $data['version'])] : [], 'status' => true];
    }

}
