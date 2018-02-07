<?php namespace App\Services\Version\Agent\LuckyBag;

use App\Models\Agent\RedPacketAgent;
use App\Models\Validate;
use App\Services\Version\VersionSelect;

class _v010300 extends VersionSelect
{
    /**
     * author zhaoyf
     *
     * 获取经纪人拥有的福袋红包数据信息
     *
     * @param $param     经纪人ID arrays
     *
     * @return arrays | nulls
     */
    public function postAgentLuckyBagRedLists($param)
    {
        //获取传递的参数值
        $agent_id  = $param['agent_id'];
        $status    = $param['status'];
        $page      = isset($param['page']) ?      $param['page']      : 1;
        $page_size = isset($param['page_size']) ? $param['page_size'] : 10;

        //对参数进行过滤处理
        $validate_result         = Validate::validateIsNumericOrIsSetOrNoEmpty($agent_id);
        $status_validate_result1 = Validate::validateIsNumericOrIsSetOrNoEmpty($status);
        $status_validate_result2 = Validate::validateAssignValue($status, [0, 1, 2]);

        //对验证结果进行处理
        if (!$validate_result) {
            return ['message' => '经纪人ID不能为空，且只能是数字值', 'status' => false];
        }

        //对验证结果进行处理
        if (!$status_validate_result1 || !$status_validate_result2) {
            return ['message' => '查看的状态值不能为空，且只能介于 0 or 1 or 2之间', 'status' => false];
        }

        //获取结果
        $gain_result = RedPacketAgent::instance()->gainAgentLuckyBagReds($agent_id, 1, null, $page, $page_size, $status);

        //返回结果
        return ['message' => $gain_result, 'status' => true];
    }

    /**
     * author zhaoyf
     *
     * 经纪人进行红包抽奖（福袋红包）
     *
     * @param $agent_id     经纪人ID arrays
     *
     * @return arrays
     */
    public function postAgentRedExtracts($param)
    {
        //获取传递的参数值
        $agent_id = $param['agent_id'];

        //对参数进行过滤处理
        $validate_result = Validate::validateIsNumericOrIsSetOrNoEmpty($agent_id);

        //对验证结果进行处理
        if (!$validate_result) {
            return ['message' => '经纪人ID不能为空，且只能是数字值', 'status' => false];
        }

        //获取结果
        $gain_result = RedPacketAgent::instance()->redGainPros($agent_id);

        return ['message' => $gain_result, 'status' => true];
    }

    /**
     * author zhaoyf
     *
     * 查看红包详情
     *
     * @param $param    集合参数 arrays
     *
     * @return arrays
     */
    public function postLookRedDetails($param)
    {
        //获取查看的类型（agent | customer）和获取查看详情的红包ID
        $type   = $param['type'];
        $agent_get_red_id = $param['agent_get_red_id'];

        //对传递的红包ID值进行过滤处理
        $red_id_validate_result = Validate::validateIsNumericOrIsSetOrNoEmpty($agent_get_red_id);

        //对验证结果进行处理
        if (!$red_id_validate_result) {
            return ['message' => '查看的详情红包ID不能为空，且只能是数字值', 'status' => false];
        }

        if (trim($type) == 'agent') {
            $agent_id = $param['agent_id'];

            //对参数进行过滤处理
            $agent_id_validate_result = Validate::validateIsNumericOrIsSetOrNoEmpty($agent_id);

            //对验证结果进行处理
            if (!$agent_id_validate_result) {
                return ['message' => '经纪人ID不能为空，且只能是数字值', 'status' => false];
            }

            //获取结果
            $gain_result = RedPacketAgent::instance()->luckyBagRedDetails($agent_id, null, $agent_get_red_id, $type);

            //返回结果
            return ['message' => $gain_result, 'status' => true];

        } elseif (trim($type) == 'customer') {
            $agent_id = $param['agent_id'];
            $uid      = $param['uid'];

            //对参数进行过滤处理
            $agent_id_validate_result = Validate::validateIsNumericOrIsSetOrNoEmpty($agent_id);
            $uid_validate_result      = Validate::validateIsNumericOrIsSetOrNoEmpty($uid);

            //对验证结果进行处理
            if (!$agent_id_validate_result || !$uid_validate_result) {
                return ['message' => 'type = customer时，经纪人ID或用户ID都不能为空，且只能是数字值', 'status' => false];
            }

            //获取结果
            $gain_result = RedPacketAgent::instance()->luckyBagRedDetails($agent_id, $uid, $agent_get_red_id, $type);

            //返回结果
            return ['message' => $gain_result, 'status' => true];
        } else {
            return ['message' => '传递的类型错误，只能为agent | customer', 'status' => false];
        }
    }

    /**
     * author zhaoyf
     *
     * 获取某个投资人的红包数据列表信息
     *
     * @param $param    集合参数 arrays
     *
     * @return arrays
     */
    public function postGainOneCustomerRedDatas($param)
    {
        //获取参数值
        $agent_id  = $param['agent_id'];
        $uid       = $param['uid'];
        $page      = isset($param['page']) ?      $param['page']      : 1;
        $page_size = isset($param['page_size']) ? $param['page_size'] : 50;

        //对参数进行过滤处理
        $agent_id_validate_result = Validate::validateIsNumericOrIsSetOrNoEmpty($agent_id);
        $uid_validate_result      = Validate::validateIsNumericOrIsSetOrNoEmpty($uid);

        //对验证结果进行处理
        if (!$agent_id_validate_result || !$uid_validate_result) {
            return ['message' => '经纪人ID或用户ID不能为空，且只能是数字值', 'status' => false];
        }

        //获取结果
        $gain_result = RedPacketAgent::instance()->gainOneCustomerRedDatas($agent_id, $uid, $page, $page_size);

        //返回结果
        return ['message' => $gain_result, 'status' => true];
    }

    /**
     * author zhaoyf
     *
     * 处理： 经纪人发送指定红包给指定投资人--设置发送红包后，红包的有效时间（五个小时）
     *
     * @param $agent_get_red_id 经纪人获取的红包对应表的ID
     * @param $red_id           经纪人发送给投资人的红包ID
     * @param $uid              投资人ID
     * @param $agent_id         经纪人ID
     *
     * @return bool
     */
    public function postSetSendRedLaterOfValidTime($param)
    {
        //获取参数值
        $agent_get_red_id = $param['agent_get_red_id'];
        $red_id           = $param['red_id'];
        $uid              = $param['uid'];
        $agent_id         = $param['agent_id'];

        //验证处理结果
        $validate_result = Validate::validateGroupValue([$agent_get_red_id, $red_id, $uid, $agent_id]);

        //结果判断处理
        if (!$validate_result) {
            return ['message' => '经纪人红包获取对应ID | 红包ID | 用户ID | 经纪人ID 都不能为空,且都只能为数字值', 'status' => false];
        }

        //进行数据添加，并且返回结果
        $gain_result = RedPacketAgent::instance()->setSendRedLaterOfValidTime($agent_get_red_id, $red_id, $uid, $agent_id);

        //返回结果处理
        if ($gain_result) {
            return ['message' => 'ok', 'status' => true];
        }
    }
}