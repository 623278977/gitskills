<?php namespace App\Services\Version\Agent\Message;

use App\Models\Agent\Message\AgentCustomerMessage;

class _v010002 extends _v010000
{
    /**
     * 消息 -- 推荐投资人列表 zhaoyf
     *
     * @param $param
     *
     * @return array|string
     */
    public function postRecommendCustomer($param)
    {
        $result = $param['request']->input();
        $confirm_result = AgentCustomerMessage::instance()->recommendCustomers($result);

       //对返回结果进行处理
       if (is_null($confirm_result)) {
           return ['message' => '没有推荐投资人信息', 'status' => false];
       } else {
           return ['message' => $confirm_result, 'status' => true];
       }
    }

    /**
     * author zhaoyf
     *
     * 改变推荐投资人按钮状态
     * @param $param
     *
     * @return array
     */
    public function postChangeCustomerButtonStatus($param)
    {
        $result = $param['request']->input();

        //对传递的参数进行处理
        if (empty($result['agent_id']) || !is_numeric($result['agent_id'])) {
            return ['message' => '经纪人ID不能为空，且只能为整数', 'status' => false];
        }
        if (empty($result['customer_id']) || !is_numeric($result['customer_id'])) {
            return ['message' => '投资人ID不能为空，且只能为整数', 'status' => false];
        }
        if (empty($result['status']) || !is_numeric($result['status'])) {
            return ['message' => '状态ID不能为空，且只能为整数', 'status' => false];
        }

        $update_result = AgentCustomerMessage::instance()->changeCustomerButtonStatus($result);

       //对结果进行处理
        if ($update_result) {
            return ['message' => '更新成功', 'status' => $update_result];
        } else {
            return ['message' => '更新失败', 'status' => $update_result];
        }
    }
}