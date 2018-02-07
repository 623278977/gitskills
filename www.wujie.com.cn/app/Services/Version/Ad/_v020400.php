<?php

namespace App\Services\Version\Ad;


use App\Services\AdService;
use App\Services\Version\VersionSelect;
use Validator;
use \DB;
class _v020400 extends VersionSelect
{
    public $adService;

    public function __construct($controllerName, $controllerMethod, AdService $adService =null)
    {
        parent::__construct($controllerName, $controllerMethod);
        $this->adService = $adService;
    }

    /**
     * 作用:根据广告类型获取广告列表
     * 参数:$data
     *
     * 返回值:
     */
    public function postList($data)
    {
        $adService = new AdService();
        $list = $adService->lists($data['type'],  $data['page'], $data['page_size']);

        return ['data' => $list, 'status' => true];
    }


}