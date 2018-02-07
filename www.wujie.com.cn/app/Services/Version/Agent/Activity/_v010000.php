<?php

namespace App\Services\Version\Agent\Activity;

use App\Services\Version\VersionSelect;
use App\Models\Activity\Entity\AgentActivity as ActivityAgent;


class _v010000 extends VersionSelect
{
    /**
     * 作用:活动列表
     *
     * 返回值:
     */
    public function postList($input = [])
    {
        //获取数据
        $data = ActivityAgent::agentActivityList($input);

        return ['message' => $data, 'status' => true];

    }

    /**
     * 作用:活动详情
     *
     * 返回值:
     */
    public function postDetail($input = [])
    {
        $data = ActivityAgent::agentActivityDetail($input, true);

        return ['message' => $data, 'status' => true];

    }


}