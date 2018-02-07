<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017-12-8
 * Time: 09:30
 */

namespace App\Services\Version\Agent\Comment;
use App\Models\User\Praise;

class _v010100 extends _v010005
{
    public function postAddZan($param)
    {
        $validator_result =\Validator::make($param['request']->input(), [
            'agent_id' => 'required|integer',
            'id'  => 'required|integer',
            'type'=> 'required|in:news,we_chat,activity,live'
        ], [
            'required' => ':attribute为必填项',
        ], [
            'agent_id' => '点赞的用户ID',
            'id'  => '点赞目标ID',
            'type'  => '点赞目标ID',
        ]);

        if ($validator_result->fails()) {
            return ['message' => $validator_result->messages(), 'status' => false];
        }

        //添加点赞 并 对结果进行处理
        $zan_result = Praise::agentAdds($param['agent_id'], $param['type'], $param['id']);
        if (is_string($zan_result)) {
            return ['message' => $zan_result, 'status' => false];
        } else {
            return ['message' => $zan_result, 'status' => true];
        }
    }
}