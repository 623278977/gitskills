<?php

/*
 * 用户编辑
 */

namespace App\Services\Version\User;

use App\Models\Video;
use App\Models\Activity\Ticket;
use App\Models\Live\Entity as Live;
use App\Models\User\Entity as User;

class _v020502 extends _v020500 {
    /*
     * 我的门票列表
     */
    public function postUserticketlist($param) {
        if (empty($param['_uid'])) {
            return ['message' => 'uid必填', 'status' => false];
        }

        if (!User::find($param['_uid'])) {
            return ['message' => '非法的uid', 'status' => false];
        }

        $request = $param['request'];
        ;
        $type = $request->input('type') ? : 'my'; //我的my  未完成notover
        $page = (int) $request->input('page') ? : 1;
        $pageSize = (int) $request->input('pageSize') ? : 15;

        $where = array();
        $where['ut.uid'] = $param['_uid'];

        $return = [];

        $tickets = \App\Models\User\Ticket::getTicketsList($where, $type, $page, $pageSize, ['has_starting' => 1]);

        if ($tickets) {
            $_NOW = time();
            foreach ($tickets as $k => $item) {
                $return[$k]['group'] = $item['group'];
                $return[$k]['order_no'] = $item['order_no'];
                $return[$k]['subject'] = $item['subject'];
                $return[$k]['begin_time'] = date('Y年m月d日 H:i', $item['begin_time_raw']);
                $return[$k]['surplus_time'] = $item['begin_time_raw'] > $_NOW ? $item['begin_time_raw'] - $_NOW : -1;
                $return[$k]['ticket_type'] = $item['type'];
                $return[$k]['price'] = $item['price'] == '0.00' ? '免费' : $item['price'];
                $return[$k]['score_price'] = $item['score_price'] == '0' ? '免费' : $item['score_price'];
                $return[$k]['online_money'] = $item['online_money'];
                $ticket = Ticket::find($item['aid']);
                $ticket_name = $ticket ? $ticket->name : '';
                $return[$k]['ticket_name'] = $ticket_name;
                $return[$k]['ticket_status'] = $this->getTicketStatus($item, $param['_uid'], $type);
                $return[$k]['activity_id'] = $item['activity_id'];
                $return[$k]['is_sign'] = $item['is_sign'];
                $return[$k]['ticket_url'] = $item['ticket_url'];
                $return[$k]['is_over'] = $item['is_over'];
                $return[$k]['order_lefttime'] = $item['order_lefttime'];
                $return[$k]['maker_id'] = $item['maker_id'];
                $return[$k]['maker_subject'] = $item['maker_subject'];
                $return[$k]['address'] = $item['address'];
                $return[$k]['tel'] = $item['tel'];
                $return[$k]['city'] = [$item['city']];
                if($item['upid'] && $zone=\App\Models\Zone::find($item['upid'], ['name'])){
                    array_unshift($return[$k]['city'], $zone->name);
                }
                $live = Live::where('activity_id', $item['activity_id'])->first();
                $return[$k]['live_id'] = $live ? $live->id : 0;
                $video = Video::where('activity_id', $item['activity_id'])->where('live_id', $return[$k]['live_id'])->first();
                $return[$k]['video_id'] = $video ? $video->id : 0;
                $return[$k]['ticket_id'] = $item['id'];
                $return[$k]['is_check'] = $item['is_check'];
                $return[$k]['pay_way'] = $item['pay_way'] == 'ali' ? '支付宝支付' : '微信支付';
                if($item['pay_way'] == 'ali'){
                    $return[$k]['pay_way'] = '支付宝支付';
                }elseif($item['pay_way'] == 'weixin'){
                    $return[$k]['pay_way'] = '微信支付';
                }else{
                    $return[$k]['pay_way'] = '积分支付';
                }

                $return[$k]['order_id'] = $item['order_id'];
                $return[$k]['activity_ticket_id'] = $item['aid'];
            }
        }

        return ['message' => $return, 'status' => true];
    }

}
