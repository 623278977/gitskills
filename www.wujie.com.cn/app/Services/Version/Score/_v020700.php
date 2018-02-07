<?php

namespace App\Services\Version\Score;


use App\Services\Version\VersionSelect;
use App\Models\Score\Goods\V020700 as GoodsV020700;

class _v020700 extends VersionSelect
{

    /*
    * 积分商品
    */
    public function postGoods($data)
    {
       $goods =  GoodsV020700::getGoods();

        $goods->transform(function ($item, $key) {
            $item->price = abandonZero($item->price);
            $item->product_id = $item->id;
            unset($item->id);
            return $item;
        });

        return ['message'=>['goods'=>$goods], 'status'=>true];
    }



}