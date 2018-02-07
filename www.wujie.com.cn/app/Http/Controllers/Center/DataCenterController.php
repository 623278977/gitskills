<?php

/**
 * Created by PhpStorm.
 * Title：数据中心
 * User: yaokai
 * Date: 2018/1/4 0004
 * Time: 17:53
 */

namespace App\Http\Controllers\Center;


use App\Models\Agent\Agent;
use App\Models\User\Entity as User;
use App\Models\Validate;
use Illuminate\Http\Request;
use App\Http\Controllers\Api\CommonController;

class DataCenterController extends CommonController
{
    //列表
    public function postDecrypt(Request $request)
    {

        if (!$request->has('en_tel', 'platform')) {

            return AjaxCallbackMessage('缺少参数', false);

        }
        if (!in_array($request->get('platform'), ['wjsq', 'agent', 'c_crm', 'g_crm'], true)) {

            return AjaxCallbackMessage('来源平台不合法', false);

        }

        //数据中心处理
        $url = config('system.data_center.hosts') . config('system.data_center.decrypt');

        //用户加密后的手机号
        $non_reversible = $request->get('en_tel');
        $platform = $request->get('platform');

        $data = [
            'platform' => $platform,//来源C端呼叫系统
            'en_tel' => $non_reversible,//通过加盐后得到手机号码
        ];

        //请求数据中心接口
        $result = json_decode(getHttpDataCenter($url, '', $data));
        //如果异常则停止
        if (!$result) {
            return AjaxCallbackMessage('服务器异常!', false);
        } elseif ($result->status == false) {
            return AjaxCallbackMessage($result->message, false);
        }

        return AjaxCallbackMessage($result->message, true);


    }

    /**
     * author zhaoyf
     *
     * 解析手机号
     *
     * @param   type    类型：区分是投资人还是经纪人 analyses-phone
     * @param   use_id  用户ID
     *
     * @return string （phone）
     */
    public function postAnalysesPhone(Request $request)
    {
        //获取参数值
        $type   = $request->input('type');
        $use_id = $request->input('use_id');

        //对参数进行处理
        $type_validate_result   = Validate::validateAssignValue($type, [1, 2]);
        $use_id_validate_result = Validate::validateIsNumericOrIsSetOrNoEmpty($use_id);

        //对验证结果进行处理
        if (!$type_validate_result) {
            return AjaxCallbackMessage('传递的类型有误，只能为 1 | 2', false);
        }
        if (!$use_id_validate_result) {
            return AjaxCallbackMessage('用户ID不能为空，且只能整数值', false);
        }

        // 通过类型区分： 是需要解析经纪人还是投资人端的手机号
        switch ($type) {
            case 1: $non = Agent::where('id', $use_id)->first()->non_reversible;
                break;
            case 2: $non = User::where('uid', $use_id)->first()->non_reversible;
                break;
            default :
                 $non = null;
                 break;
        }

        //进行手机号的解析
        if (isset($non) && !is_null($non)) {
            $gain_phone = Agent::getRealPhone($non, $type == 1 ? 'agent' : 'wjsq');
        }

        //返回解析后的结果
        return AjaxCallbackMessage($gain_phone, true);
    }
}
