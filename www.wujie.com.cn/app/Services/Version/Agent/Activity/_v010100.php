<?php namespace App\Services\Version\Agent\Activity;

use App\Models\Agent\AgentCurrencyLog;
use App\Models\Zone\Entity as Zone;
use Illuminate\Support\Facades\DB;

class _v010100 extends _v010005
{
    //对后台假数据类型进行格式转换
    private $_type_replace = [
        1   => 17,  //点赞
        2   => 14,  //活动
        3   => 15,  //考察
        4   => 6    //加盟品牌
    ];

    /**
     * author zhaoyf
     *
     * 圣诞活动，活动详情
     *
     * @param $param    array
     */
    public function postChristmasDetail($param)
    {
        $my_array     = array();
        $gather_array = array();

        //对传递的ID值进行处理
        if (!isset($param['agent_id']) || empty($param['agent_id'])) {
            return ['message' => '缺少经纪人ID', 'status' => false];
        }
        if (!is_numeric($param['agent_id'])) {
            return ['message' => '经纪人ID只能为整数', 'status' => false];
        }

        //获取经纪人圣诞活动期间邀请情况的数据信息
        //当前经纪人圣诞的邀请情况
        $gain_result = AgentCurrencyLog::getInstance()->gainAgentToDatas($param['agent_id']);

        //对结果进行处理
        if (is_null($gain_result)) {
            return ['message' => '没有任何数据信息', 'status' => false];
        }

        //获取点赞结果
        $status_result = DB::table('agent_screen_capture')
                         ->where('agent_id', $param['agent_id'])
                         ->orderBy('created_at', 'desc')->first();

        //获取英雄榜数据（真/假同时获取）
        $hero_result = DB::table('agent_christmas_heros')
                       ->where('status', 1)
                       ->orderBy('created_at', 'desc')
                       ->get();

        //对当前经纪人数据处理
        $tags = false;
        if (!is_null($gain_result)) {
            foreach ($gain_result as $key => $vls) {

                //用户标记佣金表里是否已经存在点赞成功获取的5元奖励了
                if ($vls->type == 17) $tags = true;

                //组合结果数据信息
                $my_array[] = [
                    'type'   => $vls->type,
                    'status' => 1
                ];
            }

            //如果佣金表里没有点赞记录，表示点赞获取5元奖励暂时没有
            //这时需要根据当前经纪人ID去经纪人集赞截屏表查询，是否存在
            //当存在时，返回对应的审核状态，当没有时，状态为 -1，表示没有
            if (!$tags) {
                $my_array[] = [
                    'type'   => 17,
                    'status' => $status_result ? $status_result->status : -1
                ];
            }
        }

        //对所有经纪人数据处理
       /* if (!is_null($gain_result['gather'])) {
            foreach ($gain_result['gather'] as $key => $vls) {

                //组合结果数据信息
                $gather_array[] = [
                    'name'       => $vls->agents->nickname,
                    'avatar'     => getImage($vls->agents->avatar),
                    'zone'       => abandonProvince(Zone::pidNames([$vls->agents->zone_id])),
                    'type'       => $vls->type,
                    'created_at' => strtotime($vls->created_at),
                ];
            }
        }*/
        if ($hero_result) {
            foreach ($hero_result as $key => $value) {
                $gather_array[] = [
                    'name'       => $value->nickname,
                    'avatar'     => getImage($value->avatar),
                    'zone'       => $value->zone,
                    'type'       => $this->_type_replace[$value->type],
                    'created_at' => $value->created_at,
                ];
            }
        }

        //返回组合后的结果
        return [
            'message' => [
                'my_infos'      => $my_array,
                'gather_infos'  => $gather_array
            ],
            'status'  => true,
        ];
    }
}