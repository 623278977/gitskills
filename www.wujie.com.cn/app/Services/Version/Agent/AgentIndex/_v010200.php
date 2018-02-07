<?php namespace App\Services\Version\Agent\AgentIndex;

use App\Models\Agent\Agent;
use App\Models\Agent\AgentAdd;
use App\Models\Agent\Score\AgentScoreLog;
use App\Models\Brand\BrandContactor;
use App\Models\RedPacket\RedPacket;

class _v010200 extends _v010100
{


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
        $data = [];
        //继承父类
        $data = parent::postIndex($param, $confirm_version);

        if(!$data['status']){
            return $data;
        }
        $data = $data['message'];
        //添加每日打卡功能
        $punchInfo = AgentScoreLog::everyDayPunch($agent_id);
        if(!$punchInfo['status']){
            return $punchInfo;
        }
        $punchInfo = $punchInfo['message'];
        $data['is_first_login_today'] = $punchInfo['isFirstLogin'];
        if($punchInfo['isFirstLogin']){
            $data['first_login_score'] = $punchInfo['score'];
        }

        //获取经纪人的附加信息
        $agentAddInfo = AgentAdd::where('agent_id',$agent_id)->first();
        /*
         * 是否领取开门大吉红包
         * 判断开门大吉红包有没有
         * */

        $data['is_receive_open_redpacket'] = '1';
        $openRedPacket = RedPacket::showWhere()->where('type',6)->first();
        if(is_object($openRedPacket)){
            $data['is_receive_open_redpacket'] = trim($agentAddInfo['has_receive_open']);
        }

        //检测该经纪人是否是通过400注册进来，如果是则给其分配商务
        $registerInvite = $data['agent_header_info']['register_invite'];
        if(empty($registerInvite) && $agentAddInfo['is_400_register'] == 1){
            $brandContractor = BrandContactor::selectContactor();
            Agent::where('id',$agent_id)->update(['register_invite'=>$brandContractor['non_reversible']]);
        }

        return ['message'=>$data , 'status'=>true];
    }

}