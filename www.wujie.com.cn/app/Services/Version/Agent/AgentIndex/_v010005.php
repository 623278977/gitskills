<?php namespace App\Services\Version\Agent\AgentIndex;

use App\Models\Agent\Agent;
use App\Models\Agent\NewAgentAreas\NewAgentArea;
use App\Models\Agent\Entity\_v010200 as Agentv010200;
use App\Models\Agent\Score\AgentScoreLog;

class _v010005 extends _v010004
{
    const VERSION     = '05';   //05 版本号
    const ENABLE_TYPE = 1;      //启用标记
    const LOST_TYPE   = 0;      //禁用标记

    /**
     * 经纪人首页显示
     *
     * @param    $param
     * @return   array|string
     * @internal param Request $request
     * @internal param null $version
     */
    public function postIndex($param, $version = null)
    {
        //判断经纪人ID是否存在
        $agent_id = $param['request']->input('agent_id');

        if (empty($agent_id) || !is_numeric($agent_id)) {
            return ['message' => '缺少经纪人ID且只能为整数', 'status' => false];
        }

        //对传递的版本号进行处理
        if ($version) {
            $confirm_version = $version;
        } else {
            $confirm_version = self::VERSION;
        }

        //继承父类
        $result = parent::postIndex($param, $confirm_version);

        //05版本的代码扩展 -- 首页新手专区
        $new_agent_data = NewAgentArea::instance()->gainIndexNewAgentImgLists();

        //组合数据
        foreach ($result['message'] as $key => $vls) {
            $result['message']['new_agent_area'] = $new_agent_data;
        }

        //判断经纪人是否是第一次进入首页
         $agent_result     = Agent::where('id', $agent_id);
         $agent_first_data = $agent_result->first();

        //如果is_first_enter = 0: 表示不是； 等于1: 表示是。
         if ($agent_first_data->is_fist_page_shown < self::ENABLE_TYPE) {
             $result['message']['is_first_enter'] = self::ENABLE_TYPE;
             $agent_result->update(['is_fist_page_shown' => self::ENABLE_TYPE]);
         } else {
             $result['message']['is_first_enter'] = self::LOST_TYPE;
         }

        return $result;
    }
}