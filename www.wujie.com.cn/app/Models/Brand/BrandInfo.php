<?php

namespace App\Models\Brand;

use App\Models\RedPacket\RedPacket;
use App\Models\RedPacket\RedPacketPerson;
use Illuminate\Database\Eloquent\Model;

class BrandInfo extends Model
{
    public $timestamps = false;

    protected $table = 'brand_info';

    protected $guarded = [];


    public static function hasRedPacket($brandId)
    {
        $isHave = 0;
        $nowTime = time();
        $radPacketList = RedPacket::showWhere()
            ->whereIn('post_id',[0 , $brandId])
            ->whereIn('type',[1,2])->get()->toArray();
        //筛除过期红包
        $newRedPacketList = [];
        foreach ($radPacketList as $one){
            if($one['expire_type'] == 0 && $one['expire_at'] <>-1 && $nowTime >= $one['expire_at']){
                continue;
            }
            $newRedPacketList[] = $one;
        }
        if(!empty($newRedPacketList)){
            $isHave = 1;
        }
        return $isHave;
    }
}
