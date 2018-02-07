<?php

namespace App\Services\Version\Brand;


use App\Models\User\Entity as User;
use App\Models\Brand\BrandInfo;
use App\Models\Brand\Images;
use App\Models\Brand\Entity as Brand;
use App\Models\RedPacket\RedPacket;
use App\Models\RedPacket\RedPacketPerson;


class _v020900 extends _v020800
{

    /**
     * 品牌x详情
     */
    public function postDetail($input)
    {

        $data = parent::postDetail($input);

        //获取品牌的其他附加信息
        $brandAppendInfo = BrandInfo::where('brand_id', $input['id'])->first();
        $data['brand']['join_area'] = trim($brandAppendInfo['join_area']);
        $data['brand']['store_area'] = trim($brandAppendInfo['store_area']);
        $data['brand']['contract_deadline'] = trim($brandAppendInfo['contract_deadline']);
        $data['brand']['initial_investment'] = trim($brandAppendInfo['initial_investment']);
        $data['brand']['single_customer_price'] = trim($brandAppendInfo['single_customer_price']);
        $data['brand']['day_flow'] = trim($brandAppendInfo['day_flow']);
        $data['brand']['month_sales_mount'] = trim($brandAppendInfo['month_sales_mount']);
        $data['brand']['margin_rate'] = trim($brandAppendInfo['margin_rate']);
        $data['brand']['return_period'] = trim($brandAppendInfo['return_period']);

        //品牌支持代理方式

        $data['brand']['agency_way'] = $data['brand']->agentWay();

        if (!empty($input['uid'])) {
            $isHave = BrandInfo::hasRedPacket($input['id']);
            $data['brand']['red_packet'] = $isHave;
        } else {
            $data['brand']['red_packet'] = 0;
        }
        //获取门店实景
        $brandStoreImg = Images::where('type', 'store')
            ->where('brand_id', $input['id'])->orderBy('sort', 'desc')->get();
        $data['store_img'] = [];
        foreach ($brandStoreImg as $oneImg) {
            $arr = [];
            $arr['url'] = getImage($oneImg['src']);
            $arr['id'] = trim($oneImg['id']);
            $arr['introduce'] = trim($oneImg['introduce']);
            $data['store_img'][] = $arr;
        }

        //处理品牌商品图片
        $detailImages = Images::with('brand_images_info')
            ->where('brand_id' , $input['id'])->where('type','detail')->get()->toArray();
        $imageList = [];
        foreach ($detailImages as $oneDetail){
            $arr = [];
            $arr['id'] = trim($oneDetail['id']);
            $arr['src'] = getImage($oneDetail['src']);
            $arr['introduce'] = trim($oneDetail['introduce']);
            $arr['goods_name'] = trim($oneDetail['brand_images_info']['goods_name']);
            $arr['classify'] = trim($oneDetail['brand_images_info']['classify']);
            $imageList[] = $arr;
        }

        //将图片进行分组，共前端使用
        $classifyImage = collect($imageList)->groupBy(function($item){
            return $item['classify'];
        })->toArray();
        $data['brand']['detail_images'] = $imageList;
        $data['brand']['classify_detail_images'] = $classifyImage;
        return $data;
    }


    /*
     * 获取某个品牌的所有可用红包
     * */
    public function postGetBrandRedpacket($input)
    {
        $nowTime = time();
        $brandId = intval($input['brand_id']);
        $uid = intval($input['uid']);
        $userInfo = User::whereIn('status', [1, 2])->find($uid);
        if (!is_object($userInfo)) {
            return ['message' => '该用户无效', 'status' => false];
        }
        $radPacketList = RedPacket::showWhere()->with('brand')
            ->whereIn('post_id',[0 , $brandId])
            ->whereIn('type',[1,2])->where('red_source',0)->get()->toArray();
        //筛除过期红包
        $newRedPacketList = [];
        foreach ($radPacketList as $one){
            if($one['expire_type'] == 0 && $one['expire_at'] <>-1 && $nowTime >= $one['expire_at']){
                continue;
            }
            $newRedPacketList[] = $one;
        }

        if(empty($newRedPacketList)){
            return ['message' => 'no_redpacket', 'status' => false];
        }
        $data['redpacket'] = [];
        $total = 0;
        foreach ($newRedPacketList as $oneRedpacket) {
            $isHave = RedPacketPerson::where('receiver_id', $uid)->where('red_packet_id', $oneRedpacket['id'])->first();
            if (is_object($isHave)) {
                continue;
            }
            $arr = [];
            $arr['id'] = trim($oneRedpacket['id']);
            $arr['amount'] = trim($oneRedpacket['amount']);
            $total += $oneRedpacket['amount'];
            $arr['type'] = trim($oneRedpacket['type']);
            $arr['brand_name'] = '';
            if ($oneRedpacket['type'] == 2) {
                $arr['brand_name'] = trim($oneRedpacket['brand']['name']);
            }
            $arr['status'] = 0;
            $data['redpacket'][] = $arr;
        }
        if(empty($data['redpacket'])){
            return ['message' => 'all_received', 'status' => false];
        }
        $data['total'] = $total;
        return ['message' => $data, 'status' => true];
    }
}