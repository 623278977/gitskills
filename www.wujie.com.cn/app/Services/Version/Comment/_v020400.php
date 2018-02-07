<?php

namespace App\Services\Version\Comment;


use App\Services\CommentService;
use App\Services\Version\VersionSelect;
use Validator;
use \DB;
class _v020400 extends VersionSelect
{
    public $liveService;

    public function __construct($controllerName, $controllerMethod, Live $liveService =null)
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
    public function postSingleComment($data)
    {
        $service = new CommentService();
        $list = $service->getSingleComment($data['id']);

        return ['data' => $list, 'status' => true];
    }
}