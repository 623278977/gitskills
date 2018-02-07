<?php namespace App\Services\Version\Agent\AgentIndex;

use App\Models\Video\Entity as Video;
use App\Models\Live\Entity as LiveModel;
use App\Models\Activity\Entity\AgentActivity as ActivityAgent;

class _v010100 extends _v010005
{
    const VERSION  = '0101';    //0101 版本号
    const TYPE_ONE = 1;         //数字一类型
    const TYPE_TWO = 2;         //数字二类型

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

        //首页显示的热门视频
        $video_result = Video::gainIndexShowVideos();

        //组合数据
        foreach ($result['message'] as $key => $vls) {
            $result['message']['hot_video'] = $video_result;
        }

        //返回组合后的数据
        return $result;
    }

    /**
     * author zhayf
     *
     * 整合活动和直播显示（获取组合数据，根据类型区分）
     *
     * @param param [
     *  'page'      =>  初始化分页数 int
     *  'page_size' =>  每页显示页数 int
     *  'type'      =>  区分类型（1：活动，2：直播）int
     * ]
     *
     * return array
     */
    public function postGainCombinationDatas($param)
    {
        $result = $param['request']->input();

        //对传递的参数进行处理
        if (empty($result['type']) || !is_numeric($result['type'])) {
            return ['message' => '缺少类型值（1 或 2），且只能为整数', 'status' => false];
        }
        if (!in_array($result['type'], [self::TYPE_ONE, self::TYPE_TWO]) ) {
            return ['message' => '传递类型错误，只能为 1 或 2', 'status' => false];
        }

        //根据类型获取数据值
        if ($result['type'] == self::TYPE_ONE) {
           $confirm_result = ActivityAgent::agentActivityList();
        } elseif ($result['type'] == self::TYPE_TWO) {
           $confirm_result = LiveModel::getLiveList($result['page'] ?: self::TYPE_ONE, $result['page_size'] ?: 10);
        }

        //返回数据
        return ['message' => $confirm_result, 'status' => true];
    }
}