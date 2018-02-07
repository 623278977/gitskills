<?php
/**
 * Created by PhpStorm.
 * Title：透传转通知
 * User: yaokai
 * Date: 2017/11/15 0015
 * Time: 02:44
 */

namespace App\Services\GeTui;

use App\Models\Agent\Agent;

class TransmissonType extends GeTui
{


    /**
     * 透传转通知中的变量处理
     * @User yaokai
     * @param $item
     * @return array
     */
    public static function templateType($item)
    {
        $res = [];
        $value = $item->value;
        $new_item = [];
        $new_item['type'] = $item->type;
        $new_item['style'] = $item->style;

        switch ($item->type){
            case 'send_order'://派单离线通知处理
                $res['name'] = $value->realname?:'';
                $res['zone_name'] = $value->zone_name?'('.$value->zone_name.')':'';
                $res['brand_name'] = $value->brand_name?:'';
                $res['slogan'] = $value->slogan?:'';
                $new_item['value'] = (string)$value->id;//iOS 需要字符串
                break;
            case 'accept_order'://经纪人接单通知处理
                $agent_id = $value->agent_id;
                $agent = Agent::where('id',$agent_id)->first();
                $res['name'] = $agent->is_public_realname?$agent->realname:$agent->nickname;
                break;
            default:
                break;

        }
        return compact('res', 'new_item');
    }





}