<?php namespace App\Services\Version\Agent\NewAgentDetails;

use App\Models\Agent\NewAgentAreas\NewAgentDetail;
use App\Services\Version\VersionSelect;

class _v010005 extends VersionSelect
{
    /**
     * author zhaoyf
     *
     * 新手专区--详情页
     */
    public function postNewAgentDetails($param)
    {
       $result = $param['request']->input();

       $confirm_result = NewAgentDetail::instance()->gainNewAgentDetailDatas($result);

       return ['message' => $confirm_result, 'status' => true];
    }

    /**
     * author zhaoyf new-agent-zans
     *
     * 新手专区详情点赞
     */
    public function postNewAgentZans($param)
    {
        $result = $param['request']->input();

        $confirm_result = NewAgentDetail::instance()->newAgentZans($result);

        //对结果进行处理
        if (is_string($confirm_result) && $confirm_result == 'confirm_zan') {
            return ['message' => '已经赞过了', 'status' => true];
        }

        return ['message' => '点赞成功', 'status' => true];
    }

    /**
     * author zhaoyf new-agent-comments
     *
     * 新手专区详情评论
     */
    public function postNewAgentComments($param)
    {
        $result = $param['request']->input();

        $comment_result = NewAgentDetail::instance()->gainNewAgentDetailCommentDatas($result);

        return ['message' => $comment_result, 'status' => true];
    }

    /**
     * 新手专区详情分享次数记录
     */
    public function postNewAgentShards($param)
    {
        $result = $param['request']->input();

        //对传递的参数进行处理
        if (empty($param['id']) || !is_numeric($param['id'])) {
            return ['message' => '缺少新手专区详情页ID，且只能为整数', 'status' => false];
        }

        $shard_result = NewAgentDetail::instance()->newAgentShards($result);

        return ['message' => '分享成功', 'status' => true];
    }
}