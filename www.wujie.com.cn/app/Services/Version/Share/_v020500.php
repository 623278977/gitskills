<?php

namespace App\Services\Version\Share;

use App\Models\Distribution\Action;
use App\Models\ScoreLog;
use App\Models\Share\Log;
use App\Models\Video;
use App\Services\ShareService;
use App\Services\Version\VersionSelect;
use DB;

class _v020500 extends VersionSelect
{
    /*
    * 分享奖励入库
    */
    public function postCollectScore($param)
    {
        if(empty($param['relation_id'])){
            return ['message' => '参数异常，没有relation_id', 'status' => false];
        }

        if(empty($param['share_mark']) || strlen($param['share_mark'])<30){
            return ['message' => '参数异常', 'status' => false];
        }
        $share_remark = \Crypt::decrypt($param['share_mark']);
        $md5 = substr($share_remark, 0, 32);
        if ($md5 != md5($_SERVER['HTTP_HOST'])) {
            return ['message' => '分享码有误', 'status' => true];
        }
        $share_remark = explode('&', substr($share_remark, 44));


        //判断此次分销动作是否需要给予奖励
        $effective = Action::isEffective($share_remark[0], $share_remark[1], $param['type'], $share_remark[2], $param['watch_long']);
        if (!$effective) {
            return ['message' => '无分销或分销超限', 'status' => false];
        }

        $transfer = [
            'share'  => '分享',
            'relay'  => '转发',
            'watch'  => '观看',
            'enroll' => '报名',
            'sign'   => '签到',
            'view'   => '查看',
            'intent' => '意向留言'
        ];

        $entity = ['activity' => '活动', 'brand' => '品牌', 'live' => '直播', 'video' => '视频', 'news' => '资讯'];
        $effective->action=='share' ?$genus_type='share':$genus_type='share_'.$effective->action;
        //奖励入库
        if (Action::obtainReward(
            $share_remark[2],
            $effective,
            $param['relation_id'],
            $transfer[$param['type']] . $entity[$share_remark[0]] . '分销获得奖励',
            $param['uid'],
            $share_remark[0],
            $share_remark[1],
            0,
            $genus_type
        )
        ) {
            return ['message' => '操作成功', 'status' => true];
        }

        return ['message' => '操作失败', 'status' => false];
    }

    /**
     * 分享记录入库
     */
    public function postShare($param)
    {
        if (isset($param['share_mark'])) {
            $share_remark = \Crypt::decrypt($param['share_mark']);
            $md5 = substr($share_remark, 0, 32);
            if ($md5 != md5($_SERVER['HTTP_HOST'])/* || $share_remark[2] != $param['uid']*/) {
                return ['message' => '分享码有误', 'status' => true];
            }
            $share_remark = explode('&', substr($share_remark, 44));
            $param['source_uid'] = $share_remark[2];
        } else {
            $param['source_uid'] = 0;
        }
        $shareService = new ShareService();
        $create = $shareService->createShare($param['uid'], $param['content_id'], $param['content'],
            $param['source'], $param['code'], $param['source_uid']);

        if ($create) {
            return ['message' => '操作成功', 'status' => true];
        } else {
            return ['message' => '操作失败', 'status' => false];
        }
    }

    /**
     * 我的分销
     */
    public function postMyShare($param)
    {
        $shareService = new ShareService();
        $myshare = $shareService->myShare($param['uid'], $param['page'], $param['page_size']);

        return ['message' => $myshare, 'status' => true];
    }

    /**
     * 分销详情  --疑似弃用  数据中心暂不处理
     * @User
     * @param $param
     * @return array
     */
    public function postShareDetail($param)
    {
        $shareService = new ShareService();
        $shareDetail = $shareService->shareDetail($param['share_id'], $param['page'], $param['page_size']);

        return ['message' => $shareDetail, 'status' => true];
    }

    /**
     * 分销有奖
     */
    public function postShareList($param)
    {
        $shareService = new ShareService();
        $shareDetail = $shareService->shareList($param['uid'], $param['page'], $param['page_size'], $param['keyword']);

        return ['message' => $shareDetail, 'status' => true];
    }

}