<?php
/**
 * Created by PhpStorm.
 * Title：
 * User: yaokai
 * Date: 2017/11/7 0007
 * Time: 16:29
 */

namespace App\Services\Version\Agent\Consult;

use App\Services\Version\VersionSelect;
use DB, Input;
use App\Models\SendOrderQueue\V020800 as SendOrderQueue;

class _v010003 extends VersionSelect
{

    /**
     * 咨询任务列表
     * @User yaokai
     * @param Request $request
     * @param null $version
     */
    public function postLists($input = [])
    {
        //排序
        $orderby = $input['order_by'];
        //分类
        $categorys1_id = $input['categorys1_id'];
        //分页
        $page_size = $input['page_size']?:'10';

        //找出所有已派单信息
        $orders = SendOrderQueue::getOrders($input['agent_id'],$orderby,$categorys1_id,$page_size);

        return ['message' => $orders, 'status' => true];

    }


    /**
     * 咨询任务详情
     * @User yaokai
     * @param Request $request
     * @param null $version
     */
    public function postDetail($input = [])
    {
        //找出派单信息
        $orders = SendOrderQueue::getOrder($input['agent_id'],$input['id']);

        return ['message' => $orders, 'status' => true];

    }


    /**
     * 拒绝咨询任务
     * @User yaokai
     * @param Request $request
     * @param null $version
     */
    public function postRefuseAccept($input = [])
    {
        DB::beginTransaction();

        //找出派单信息修改为拒绝
        $orders = SendOrderQueue::where('agent_id',$input['agent_id'])
                ->where('send_investor_id',$input['id'])
                ->update(['status' => '-4']);

        if (!$orders){
            return ['message' => '任务不存在', 'status' => true];
        }else{
            DB::commit();
            return ['message' => '拒绝成功', 'status' => true];
        }

    }



}




























