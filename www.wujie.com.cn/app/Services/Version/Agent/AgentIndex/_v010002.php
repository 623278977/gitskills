<?php namespace App\Services\Version\Agent\AgentIndex;

use App\Services\Version\Agent\AgentIndex\_v010000;
use App\Models\Agent\Agent;

class _v010002 extends _v010000
{
    const VERSION = '02';   //版本号

    /**
     * 经纪人首页显示 -- 010002 版本
     * @param    $param
     * @return   array|string
     * @internal param Request $request
     * @internal param null $version
     */
    public function postIndex($param, $version = null)
    {
        //对传递的版本号进行处理
        if ($version) {
            $confirm_version = $version;
        } else {
            $confirm_version = self::VERSION;
        }

      return  parent::postIndex($param, $confirm_version);
    }
}
