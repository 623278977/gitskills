<?php

namespace App\Services\Version\Live;


use App\Services\Live;
use App\Services\Version\VersionSelect;
use Validator;
use \DB;

class _v020400 extends VersionSelect
{
    public $liveService;

    public function __construct($controllerName, $controllerMethod, Live $liveService = null)
    {
        parent::__construct($controllerName, $controllerMethod);
        $this->liveService = $liveService;
    }


    /**
     * 作用:获取某场直播的在线人数及登录用户头像
     * 参数:$data
     *
     * 返回值:
     */
    public function postViewers($data)
    {
        $liveService = new Live();
        $list = $liveService->getOnlineUsers($data['live_id'], $data['log_id'], $data['with_anonymous'], $data['fetch_size']);

        return ['data' => $list, 'status' => true];
    }


    public function postWallInfo($data)
    {
        $liveService = new Live();

        $list = $liveService->getWallInfo($data['live_id']);

        if (!$list) {
            return ['data' => '出现错误了', 'status' => false];
        } else {
            return ['data' => $list, 'status' => true];
        }
    }
}