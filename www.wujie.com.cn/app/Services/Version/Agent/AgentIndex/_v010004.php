<?php namespace App\Services\Version\Agent\AgentIndex;

use App\Services\News;
use App\Models\Agent\Agent;

class _v010004 extends _v010003
{
    const DOWN    = 2;      //登录经纪人的下线标记数
    const VERSION = '04';   //版本号

    /**
     * 经纪人首页显示
     * @param $param    参数结合
     * @param version   版本号
     *
     * @return array|string
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

        //继承父类，返回结果
        return parent::postIndex($param, $confirm_version);
    }

}