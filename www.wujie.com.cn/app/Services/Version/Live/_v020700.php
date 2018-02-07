<?php

namespace App\Services\Version\Live;


use App\Models\Activity\Ticket;
use App\Models\Distribution\Action;
use App\Models\Live\Entity\V020700 as LiveV020700;
use App\Models\Activity\Ticket as ActivityTicket;
use App\Models\User\Entity as User;
use App\Models\Brand\Entity as Brand;
use App\Models\User\Praise;
use App\Models\Distribution\Entity as Distribution;
class _v020700 extends _v020500
{
    /**
     * 直播列表
     */
    public function postList($data)
    {
        $result = $data['result'];
        //新增返回积分形式价格
        foreach ($result as $k => &$v) {
            $v['score_price'] = Ticket::getScorePrice($v['activity_id'])?:0;
        }
        return ['message' => $result, 'status' => true];
    }

    /**
     * 直播详情
     */
    public function postDetail($param = [])
    {
        $data = parent::postDetail($param)['message'];

        $activity_id = $data['live']['activity_id'];
        $uid = $param['uid'];//用户id
        //品牌是否已收藏
        foreach ($data['live']['brands'] as $k=>$v) {
            $brand_id = $v['id'];
            $is_collect = Brand\V020700::getCollect($uid,$brand_id);
            $data['live']['brands'][$k]['is_collect'] = $is_collect;
        }

        //积分形式的直播票价格
        if ($activity_id == 0){
            $data['score_price'] = 0;
        }else{
            $data['score_price'] = ActivityTicket::getScorePrice($activity_id);
        }
        $distribution_id = $data['live']['distribution_id'];
        //返回分享规则
        $data['distribution'] = Action\V020700::getDescribe($distribution_id,'live',$param['id']);
        $data['is_distribution'] = Distribution::IsDeadline($distribution_id,$data['live']['distribution_deadline']);//分销是否失效
        //直播点赞
        $data['count_zan']      = Praise::ZanCount($param['id'],'live');
        //todo 增加分享文案字段 zhaoyf
        $data['share_summary']  = $data['live']['share_summary'] ?: '';
        $data['is_zan'] = $param['uid'] ? Praise::where('uid', '=', $param['uid'])
            ->where('relation', 'live')
            ->where('relation_id', '=', $param['id'])
            ->where('status', '<>', 'cancel')
            ->count() : 0;

        return ['message'=>$data, 'status'=>true];
    }

    /**
     * 获取直播购买信息
     */
    public function postBuyInfo($param)
    {
        if($param['type']=='activity'){
            $live = LiveV020700::where('activity_id', $param['id'])->select('id', 'begin_time', 'description', 'list_img', 'subject', 'summary', 'activity_id')->first();
        }else{
            //直播基本信息
            $live = LiveV020700::where('id', $param['id'])->select('id', 'begin_time', 'description', 'list_img', 'subject', 'summary', 'activity_id')->first();
        }

        //价格信息
        $ticket = ActivityTicket::where('activity_id', $live->activity_id)->where('type', 2)->first();
        $live->score_price = $ticket->score_price;
        $live->ticket_id = $ticket->id;
        $live->list_img = getImage($live->list_img, 'live', '');
        $live->begin_time = date('m月d日 H:i', $live->begin_time);

        $live->summary= ($live->summary ?$live->summary:cut_str(trim(strip_tags($live->description)), 30));
        //用户积分
        $user = User::where('uid', $param['uid'])->select('score', 'username', 'nickname', 'realname')->first();
        !$user->realname && $user->realname=$user->nickname;
        unset($live->description, $user->nickname);

        return ['data'=>['user'=>$user, 'live'=>$live], 'status'=>true];
    }






}