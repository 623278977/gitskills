<?php

namespace App\Services\Version\Index;

use App\Models\Brand\Entity as Brand;
use App\Models\Live\Entity as Live;

class _v020600 extends _v020500 {
    //获取指定数据部分数据源
    public function postData($param) {
        if (!$param['type'] || empty($param['id'])) {
            return ['message' => '参数丢失', 'status' => false];
        }
        switch ($param['type']) {
            case 'brand':
                $data = Brand::find($param['id']);
                break;
            default:
                return ['message' => '类型不支持', 'status' => false];
        }
        if (empty($data)) {
            return ['message' => '数据不存在', 'status' => false];
        }
        return ['message' => $this->{$param['type']}($data), 'status' => true];
    }

    //获取品牌内容
    public function brand(Brand $brand) {
        $data = [];
        $data['id'] = $brand->id;
        $data['name'] = $brand->name;
        $data['category_name'] = $brand->categorys1->name;
        $data['investment_min'] = formatMoney($brand->investment_min);
        $data['investment_max'] = formatMoney($brand->investment_max);
        $data['logo'] = getImage($brand->logo);
        $data['created'] = (string) $brand->created_at;
        if ($brand->keywords) {
            $data['keywords'] = strpos($brand->keywords, ' ') !== FALSE ? explode(' ', $brand->keywords) : [$brand->keywords];
        } else {
            $data['keywords'] = [];
        }
        $data['issuer'] = $brand->issuer;
        $data['slogan'] = $brand->slogan;
        $data['investment_arrange'] = $brand->investment_min . '万-' . $brand->investment_max . '万';
        $data['is_recommend'] = $brand->is_recommend;
        $data['detail'] = trim(preg_replace('#<\w+(\s+.*?)?>|</\w+>|&nbsp;#', '', $brand->details));
        //v020700使用
        $data['brand_summary'] = $brand->brand_summary;
        if (empty($data['brand_summary'])) {
            $data['brand_summary'] = mb_substr(preg_replace('/\s+/i','',str_replace('&nbsp;','',strip_tags($brand->details))),0,50);
        }
        return $data;
    }

    /*
    * 查询是否具有正在进行的直播
    */
    public function postIfLiving($param)
    {
        $count = Live::where('begin_time', '<', time())
            ->where('end_time', '>', time())->where('status', 0)->count();

        if($count){
            return ['message'=>1 ,'status'=>true];
        }else{
            return ['message'=>0 ,'status'=>false];
        }
    }


}
