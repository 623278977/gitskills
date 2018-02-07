<?php namespace App\Services\Version\Message;

use App\Models\Agent\AgentCustomer;
use Illuminate\Support\Str;

class _v020801 extends _v020800
{
    /**
     * 根据指定条件获取品牌信息
     * @param $param
     */
    public function postSendRongBrandInfo($param)
    {
        $result = $param['request']->input();

        $gain_result = AgentCustomer::instance()->gainAssignConditionData($result);
        if (!is_null($gain_result)) {
            $data = [
                'content' => [
                    'title'    => $gain_result['message']->name,
                    'digest'   => !empty($gain_result['message']->brand_summary) ?  $gain_result['message']->brand_summary  : Str::limit(trim(preg_replace('#<\w+(\s+.*?)?>|</\w+>|&nbsp;#','', $gain_result['message']->details)), 50),
                    'imageURL' => getImage($gain_result['message']->logo),
                    'url'      => 'https://'. env('APP_HOST') . '/webapp/agent/brand/detail?agent_id='. $gain_result['agent_id'] .'&id=' . $gain_result['message']->id,
                    'type'     => '0',
                ]
            ];

            //发送融云消息
            SendCloudMessage($result['customer_id'], 'agent' . $gain_result['agent_id'], $data, 'TY:RichMsg', '', 'custom','one_user');
            //再次发送融云消息
            $datas = ['content' => 'Hi，我对这个品牌有咨询意向~',];
            SendCloudMessage($result['customer_id'], 'agent' . $gain_result['agent_id'], $datas, 'RC:TxtMsg', '', 'custom','one_user');
        }
    }
}