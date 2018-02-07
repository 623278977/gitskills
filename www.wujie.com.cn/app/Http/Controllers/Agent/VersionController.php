<?php
/**
 * 经纪人客户端版本控制器
 * Created by PhpStorm.
 * Title：
 * User: yaokai
 * Date: 2017/10/30 0030
 * Time: 23:50
 */
namespace App\Http\Controllers\Agent;

use Illuminate\Http\Request;
use App\Models\Agent\App\Versions as AgentVersions;
use App\Http\Controllers\Controller;

class VersionController extends Controller {
    function postNew(Request $request) {
        $platform = $request->input('platform');//终端平台
        $number = $request->input('number');//当前版本号
        if (!in_array($platform, array_keys(AgentVersions::$_PLATFORMS)) || empty($number)) {
            return AjaxCallbackMessage('参数错误！', false, '');
        }
        $new = AgentVersions::where('platform', '=', $platform)
            ->where('is_release', 'yes')
            ->orderBy('number', 'desc')
            ->first();

        //在此期间 只要有一个是强制更新的就都更新
        $is_force = AgentVersions::where('platform', '=', $platform)
            ->where('is_release', 'yes')
            ->where('is_force', 'yes')
            ->where('number','>', $number)
            ->first();

        if (!$new) {
            return AjaxCallbackMessage('版本获取失败！', false, '');
        }

        return AjaxCallbackMessage([
            'version' => $new->number,
            'comment' => $new->comment,
            'downurl' => $new->package,
            'force' => is_object($is_force),
            'upgrade' => $number && strcasecmp($number, $new->number) < 0
        ], true, '');
    }

}



