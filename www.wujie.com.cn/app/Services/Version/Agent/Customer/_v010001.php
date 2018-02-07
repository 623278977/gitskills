<?php namespace App\Services\Version\Agent\Customer;

use App\Models\Agent\AgentCustomer;

class _v010001 extends _v010000
{
    /**
     * author zhaoyf
     *
     * 添加通过分享过来的客户与经纪人的数据信息
     * @param $param
     * @return array
     */
    public function postAddShareCustomerInfo($param)
    {
        $result = $param['request']->input();

        //执行添加数据操作 并 返回处理后的结果
        return AgentCustomer::instance()->addShareCustomerInfos($result);
    }
}