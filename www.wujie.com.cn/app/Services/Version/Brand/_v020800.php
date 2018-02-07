<?php

namespace App\Services\Version\Brand;

use App\Models\Comment\Entity as Comment;
use App\Models\Agent\AgentBrand;
use App\Models\Brand\Entity\V020800 as Brand;
use App\Models\SendInvestor\V020800 as SendInvestor;
use App\Models\SendOrderQueue\V020800 as SendOrderQueue;
use App\Models\Agent\AgentCustomer;
use App\Models\User\Entity as User;
use App\Models\Agent\TraitBaseInfo\RongCloud;


class _v020800 extends _v020700
{
    /*
     *品牌评论
     * */
    public function postComments($input)
    {
        $brandId = intval($input['request']->input('brand_id'));
        $uid = intval($input['request']->input('uid'));
        if (empty($brandId)) {
            return ['message' => '请输入品牌id', 'status' => false];
        }
        if (empty($uid)) {
            return ['message' => '请输入用户id', 'status' => false];
        }
        $page = intval($input['request']->input('page', 1));
        $pageSize = intval($input['request']->input('page_size', 10));
        $data = Comment::getCommentList($brandId,$uid, $page, $pageSize);
        if (isset($data['error'])) {
            return ['message' => $data['message'], 'status' => false];
        }
        return ['message' => $data, 'status' => true];
    }

    /**
     * 品牌x详情
     */
    public function postDetail($data)
    {
        $nowTime = time();
        $brandId = intval($data['id']);
        $uid = intval($data['uid']);

        // todo 增加对重复发送融云消息的处理 zhaoyf 2017-12-14 16:45
        $is_send_cloud_info = isset($data['is_send_cloud_info']) ?  $data['is_send_cloud_info'] : '';
        ################ end ##################

        $data = parent::postDetail($data);

        $commentArr = Comment::getCommentList($data['brand']['id'], $uid,1, 1);
        $data['comment'] = null;
        if (!empty($commentArr)) {
            $data['comment']['avatar'] = getImage($commentArr[0]['avatar'], 'avatar', '');
            $data['comment']['nickname'] = trim($commentArr[0]['nickname']);
            $data['comment']['content'] = trim($commentArr[0]['content']);
        }
        $data['brand']['comments'] = Comment::getBrandCommentCount($data['brand']['id']);
        $data['brand']['agents'] = AgentBrand::getAgentBrandOnLineCount($data['brand']['id']);
        if(!empty($uid)){
            $protectAgentCustomer = AgentCustomer::where(function ($query)use($uid,$nowTime){
                $query->where('uid',$uid);
                $query->where('protect_time','>',$nowTime);
            })->first();
            if(is_object($protectAgentCustomer)){
                $data['protect_time_agent'] = trim($protectAgentCustomer['agent_id']);
            }
            //获取该投资人的邀请人
            $data['inviter'] = 0;
            $inviter = User::getMyInviterInfo($uid);
            if(!empty($inviter['role']) && $inviter['role'] == 2 ){
                $agentBrands = AgentBrand::getAgentBrandId($inviter['id']);
                if(in_array($brandId,$agentBrands)){
                    $data['inviter'] = $inviter;
                }
            }

            // todo 由于点击详情和点击【更多详情】调用的都是这个品牌详情接口
            // todo 导致融云消息重复发送了， 现在增加类型区分：is_send_cloud_info
            // todo is_send_cloud_info = 1 时，发送融云消息
            // todo is_send_cloud_info = 0 时，就不发送消息了
            // todo changePerson zhaoyf 2017-12-14 16:20
            if (!is_null($is_send_cloud_info) && $is_send_cloud_info == 1) {

                //给这个投资人的邀请经纪人发送融云消息
                $query_results = AgentCustomer::where('uid', $uid)
                    ->whereIn('source', [1, 2, 3, 4, 6, 7])
                    ->where('level',  '<>', -1)
                    ->where('status', '<>', -1)
                    ->select('uid', 'agent_id')
                    ->first();

                #处理对应的结果值
                //1、如果$data['brand'] 为真
                //2、且agent_status = 1 然后继续执行
                if ( $query_results && is_object($data['brand']) && $data['brand']->agent_status == 1 ) {

                    //获取品牌ID值
                    $brand_id = intval($data['brand']->id);

                    //查询该品牌是否已经被当前经纪人代理过了
                    $agent_brand_result = AgentBrand::where([
                        'agent_id' => $query_results->agent_id,
                        'brand_id' => $brand_id,
                        'status' => 4,
                    ])->first();

                    if ($agent_brand_result) {
                        $type = 'confirm_brand_notice';
                    } else {
                        $type = 'no_brand_notice';
                    }

                    //发送融云消息
                    $msg         = trans('tui.browse_brand_notice', ['brand_name' => trim($data['brand']->name)]);
                    $send_result = SendCloudMessage($uid, 'agent' . $query_results->agent_id, $msg, 'RC:TxtMsg', '', true, 'one_user');

                    //发送品牌卡片
                    $msg = [
                        'title'     => trim($data['brand']->name),
                        'digest'    => empty($data['brand']->summary) ? extractText($data['brand']->details, 30) : extractText($data['brand']->summary, 30),
                        'imageURL'  => getImage($data['brand']->logo, '', ''),
                        'url'       => 'https://' . env('APP_HOST') . '/webapp/agent/brand/detail?agent_id=' . $query_results->agent_id . '&id=' . $brand_id,
                        'type'      => '0',
                    ];
                    $send_result    = SendCloudMessage($uid, 'agent' . $query_results->agent_id, $msg, 'TY:RichMsg', '', 'custom', 'one_user');

                    //发送提示
                    $msg         = trans("tui.{$type}");
                    $send_result = SendCloudMessage($uid, 'agent' . $query_results->agent_id, $msg, 'TY:TipMsg', '', true, 'one_user');
                }
            }
        }

        return $data;
    }


    /**
     * 在线客服咨询
     *
     * @param Request $request
     * @param null $version
     * @return string
     * @author tangjb
     */
    public function postAdvisory($data)
    {
        $res = Brand::getInstance()->setSendQueue($data['brand_id'], $data['uid']);

        return ['message' => ['send_investor_id'=>$res], 'status' => true];
    }



    /**
     * 取消订单
     *
     * @param Request $request
     * @param null $version
     * @return string
     * @author tangjb
     */
    public function postCancelAdvisory($param)
    {
        SendInvestor::where('id', $param['send_investor_id'])->update(['status'=>-1]);
        SendOrderQueue::where('send_investor_id', $param['send_investor_id'])
            //->where('status', 0) //todo 2.8.3没有了取消派单  没有强制更新 导致经纪人APP1.0.3列表异常 2017.11.22
            ->update(['status'=>-3]);
        return ['message' => '操作成功', 'status' => true];
    }



}