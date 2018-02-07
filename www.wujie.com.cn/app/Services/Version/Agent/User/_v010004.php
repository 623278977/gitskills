<?php namespace App\Services\Version\Agent\User;

use App\Services\Version\VersionSelect;
use DB;
use Validator;
use App\Models\Agent\Agent;


class _v010004 extends _v010003
{

    /**
     * 被邀请的情况下注册成经纪人
     * @User yaokai
     * @param $input
     * @return array
     */
    public function postAgentRegister($input)
    {
        return parent::postAgentRegister($input);
    }

    /*
     * 被邀请的情况下注册成投资人
     * shiqy
     * 添加：给经纪人发送通知
     * */
    public function postCustomerRegister($input)
    {
        return parent::postCustomerRegister($input);
    }








}