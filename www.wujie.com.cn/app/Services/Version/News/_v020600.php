<?php

namespace App\Services\Version\News;

use App\Models\User\Praise;
use App\Models\Brand\Entity as Brand;

class _v020600 extends _v020500 {
    /*
     * 资讯详情
     */
    public function postDetail($param = []) {
        $result = parent::postDetail($param);
        //猜你喜欢品牌
        if (isset($result['data']->brand)) {
            $brand = Brand::find($result['data']->relation_id);
            $result['data']->brand = $this->brand($brand);
            if (!empty($param['guess'])) {//获取猜测品牌数据
                $limit = 3;
                $brands = Brand::with('categorys1')
                        ->where('categorys1_id', $brand->categorys1_id)
                        ->where('id', '!=', $brand->id)
                        ->orderBy('click_num', 'desc')
                        ->limit($limit)
                        ->get();
                if ($brands->count() < $limit) {//不足，找其它的补
                    $brands = $brands->merge(Brand::with('categorys1')
                                    ->whereNotIn('id', array_merge([$result['data']->relation_id], array_pluck($brands, 'id')))
                                    ->orderBy('click_num', 'desc')
                                    ->limit($limit - $brands->count())
                                    ->get());
                }
                $result['data']->guess_brands = array_map([$this, 'brand'], $brands->all());
            }
        }
        $result['data']['is_zan'] = $param['uid'] ? Praise::where('uid', '=', $param['uid'])
                        ->where('relation', 'news')
                        ->where('relation_id', '=', $param['id'])
                        ->where('status', '<>', 'cancel')
                        ->count() : 0;
        return $result;
    }

    //获取品牌内容
    public function brand(Brand $brand) {
        $data = [];
        $data['id'] = $brand->id;
        $data['name'] = $brand->name;
        $data['category_name'] = $brand->categorys1->name;
        $data['investment_min'] = formatMoney($brand->investment_min);
        $data['investment_max'] = formatMoney($brand->investment_max);
        $data['logo'] = getImage($brand->logo, '', '');
        $data['created'] = (string) $brand->created_at;
        if ($brand->keywords) {
            $data['keywords'] = strpos($brand->keywords, ' ') !== FALSE ? explode(' ', $brand->keywords) : [$brand->keywords];
        } else {
            $data['keywords'] = [];
        }
        $data['issuer'] = $brand->issuer;
        $data['details'] = $brand->detail = trim(preg_replace('#<\w+(\s+.*?)?>|</\w+>|&nbsp;#', '', $brand->details));
        //v020700使用
        $data['brand_summary'] = $brand->brand_summary;
        if (empty($data['brand_summary']) || is_null($data['brand_summary']) || !isset($data['brand_summary']) ) {
            $data['brand_summary'] = mb_substr(preg_replace('/\s+/i','',str_replace('&nbsp;','',strip_tags($data['details']))), 0, 50);
        }
        return $data;
    }

}
