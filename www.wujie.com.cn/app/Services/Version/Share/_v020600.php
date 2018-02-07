<?php

namespace App\Services\Version\Share;

use App\Models\Distribution\Action;
use App\Models\ScoreLog;
use App\Models\Share\Log;
use App\Models\Video;
use App\Services\ShareService;
use App\Services\Version\VersionSelect;
use DB;

class _v020600 extends _v020500
{
    /**
     * 分销有奖
     */
    public function postShareList($param)
    {
        $shareService = new ShareService();
        $shareDetail = $shareService->shareList($param['uid'], $param['page'], $param['page_size'], $param['keyword'],true);

        return ['message' => $shareDetail, 'status' => true];
    }

}