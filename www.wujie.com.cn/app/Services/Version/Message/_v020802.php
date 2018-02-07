<?php namespace App\Services\Version\Message;

use App\Models\Agent\Agent;
use App\Models\Agent\AgentCustomer;
use Illuminate\Support\Str;

class _v020802 extends _v020801
{
    const PAI_RELATION_TYPE = 5;    //派单关系
    const ACCOUNT_TYPE      = 3;    //内部经纪人

    /**
     * 根据指定条件获取品牌信息--发送融云消息
     * @param $param
     * @return array
     */
    public function postSendRongBrandInfo($param)
    {
        $result = $param['request']->input();

        //根据经纪人的ID获取其对应的身份类型
        $type_result = Agent::where('id', $result['agent_id'])->first();
        if ($type_result) {
            if ($type_result->account_type == self::ACCOUNT_TYPE) {
                $gain_result = AgentCustomer::instance()->gainAssignConditionData($result);
            } else {
                //对传递的参数进行处理
                if (empty(trim($result['brand_id'])) || !is_numeric(trim($result['brand_id']))) {
                    return ['message' => '缺少品牌ID，且只能为整数', 'status' => false];
                }
                $gain_result = AgentCustomer::instance()->gainAssignConditionData($result);
            }
        } else {
            return ['message' => '该经纪人不存在', 'status' => false];
        }

        //如果是内部经纪人,不发消息
        if (is_string($gain_result) && $gain_result == 'inner_agent') {
            return ['message' => '内部经纪人', 'status' => true];
        }

        if (!is_null($gain_result)) {
            $data = [
                'title'    => $gain_result['message']->name,
                'digest'   => !empty($gain_result['message']->brand_summary) ?  $gain_result['message']->brand_summary  : Str::limit(trim(preg_replace('#<\w+(\s+.*?)?>|</\w+>|&nbsp;#','', $gain_result['message']->details)), 50),
                'imageURL' => getImage($gain_result['message']->logo),
                'url'      => 'https://'. env('APP_HOST') . '/webapp/agent/brand/detail?agent_id='. $gain_result['agent_id'] .'&id=' . $gain_result['message']->id,
                'type'     => '0',
            ];

            //发送融云消息
            $send_result = SendCloudMessage($result['customer_id'], 'agent' . $gain_result['agent_id'], $data, 'TY:RichMsg', '', 'custom','one_user');

            //再次发送融云消息
            $datas = ['content' => 'Hi，我对这个品牌有咨询意向~',];
            $send_notice_result = SendCloudMessage($result['customer_id'], 'agent' . $gain_result['agent_id'], $datas, 'RC:TxtMsg', '', 'custom','one_user');

            //经纪人发送融云消息---形成的派单关系
            if ($gain_result['relation_result']->source == self::PAI_RELATION_TYPE) {
                $_datas = ['content' => trans('tui.agent_pai_notice_infos', ['brand_name' => $gain_result['message']->name])];
                $send_notice_result = SendCloudMessage('agent' . $gain_result['agent_id'], $result['customer_id'], $_datas, 'RC:TxtMsg', '', 'custom','one_agent');
            } else {
                //经纪人发送融云消息--- 形成的邀请关系
                $_datas = ['content' => trans('tui.agent_notice_infos', ['brand_name' => $gain_result['message']->name])];
                $send_notice_result = SendCloudMessage('agent' . $gain_result['agent_id'], $result['customer_id'], $_datas, 'RC:TxtMsg', '', 'custom','one_agent');
            }

            //对发送结果进行处理
            if ($send_result && $send_notice_result && $send_notice_result) {
                return ['message' => '发送成功', 'status' => true];
            } else {
                return ['message' => '发送失败', 'status' => false];
            }
        } else {
            return ['message' => '没有相关信息', 'status' => true];
        }
    }
}