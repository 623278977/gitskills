<?php namespace App\Services\Version\Agent\Comment;

use App\Models\Agent\Comments;
use App\Models\Agent\Score\AgentScoreLog;
use App\Services\Version\VersionSelect;
use App\Models\User\Praise;
use App\Models\Agent\Entity\_v010200 as Agentv010200;

class _v010001 extends VersionSelect
{
    const CONFIRM_ZAN = 1;  //已经赞过的数字标记
    const NOT_ZAN     = 2;  //没有赞过的数字标记

    /**
     * 资讯评论 zhaoyf
     *
     * @param    $param  集合参数

     * @internal param CommentRequest $request
     *
     * @return array|string
     */
    public function postNewsAddComment($param)
    {
        $result = $param['request']->input();
        
        $result = Comments::instance()->addNesComments($result);

        //对评论结果进行处理
        if ($result) {
            //给积分
            Agentv010200::add($param['uid'], AgentScoreLog::$TYPES_SCORE[23], 23, '对资讯留言', $param['post_id']);

            return ['message' => '评论成功', 'status' => true];
        } else {
            return ['message' => '评论失败', 'status' => false];
        }
    }

    /**
     * author zhaoyf
     *
     * 对资讯点赞
     *
     * @param $param
     * @return array
     */
    public function postNewsAddZan($param)
    {
        $validator_result =\Validator::make($param['request']->input(), [
                'uid' => 'required|integer',
                'id'  => 'required|integer',
            ], [
                'required' => ':attribute为必填项',
            ], [
                'uid' => '点赞的用户ID',
                'id'  => '点赞资讯ID',
            ]);

        if ($validator_result->fails()) {
            return ['message' => $validator_result->messages(), 'status' => false];
        }

//        //给积分
//        Agentv010200::add($param['uid'], AgentScoreLog::$TYPES_SCORE[20], 20, '点赞', $param['id']);

        //添加点赞 并 对结果进行处理
        $zan_result = Praise::AgentAdds($param['uid'], 'news', $param['id']);
        if (is_string($zan_result)) {
            return ['message' => $zan_result, 'status' => false];
        } else {
            return ['message' => $zan_result, 'status' => true];
        }
    }

    /**
     *  author zhaoyf
     *
     * 获取指定资讯所有的评论数据列表
     *
     * @param $param    集合数据参数
     *
     * @return array|string
     */
    public function postAssignNewsAllCommentList($param)
    {
        $result = $param['request']->input();

        return Comments::instance()->GainAssignNewsCommentList($result);
    }

    /**
     * author zhaoyf
     *
     * 对指定用户的评论进行点赞
     *
     * @param $param
     * @return array|string
     * @internal param Request $request
     * @internal param null $version
     */
    public function postAssignUserCommentAddZan($param)
    {
        $result = $param['request']->input();

        //对传递的数据值进行验证
        $validator_result = \Validator::make($param['request']->input(), [
            'uid' => 'required|integer',
            'id'  => 'required|integer',
        ],[
            'required' => ':attribute为必填项',
        ], [
            'uid' => '当前登录用户ID',
            'id'  => '评论ID',
        ]);

        //对验证结果进行处理
        if ($validator_result->fails()) {
            return ['message' => $validator_result->messages(), 'status' => false];
        }

        //进行评论点赞处理
        $zan_result = Comments::instance()->AssignUserCommentZan($result);

        if ($zan_result) {
            return ['message' => '操作成功', 'status' => true];
        } elseif ($zan_result == self::CONFIRM_ZAN) {
            return ['message' => '你已经点过赞了', 'status' => false];
        } elseif ($zan_result == self::NOT_ZAN) {
            return ['message' => '无法取消赞，你没有点过赞', 'status' => false];
        } else {
            return ['message' => '操作失败', 'status' => false];
        }
    }
}