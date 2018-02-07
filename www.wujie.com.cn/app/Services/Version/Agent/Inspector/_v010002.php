<?php

namespace App\Services\Version\Agent\Inspector;

use App\Models\Agent\AgentAdd;
use App\Models\Config;
use Validator;
use App\Models\Agent\Agent;
use App\Models\Agent\AgentBrand;
use Hash,DB;
use App\Models\Agent\AgentCategory;
use App\Models\Agent\AgentAchievement;
use App\Models\Agent\AgentLevel;
use App\Models\Identify as duanIdentify;
use App\Models\Zone\Entity as Zone;

class _v010002 extends _v010001
{

    /**
     * 经纪人注册
     * @User yaokai
     * @param $input
     * @return array
     */
    public function postRegister($input)
    {
        $submitFlag = trim($input['submit_flag']);

        //手机号
        $tel = $input['username'];
        //伪号码
        $username = pseudoTel($tel);
        //md5加盐后的注册号码
        $non_reversible = encryptTel($tel);
        //邀请号码
        $inviter_phone = $input['inviter_phone'];
        //md5加盐后的邀请号码
        $inviter_tel = encryptTel($inviter_phone);

        if (empty($submitFlag)) {
            return ['message' => "请填写调用接口标志", 'status' => false];
        }
        if ($submitFlag == 'first') {
            $input['password_confirmation'] = $input['confirm_password'];
            $validator = \Validator::make($input, [
                'code' => 'required',
                'password' => 'required|confirmed',
                'username' => 'required',
                'inviter_phone' => 'required',
            ]);
            if ($validator->fails()) {
                $warnings = $validator->messages();
                $show_warning = $warnings->first();
                return ['message' => $show_warning, 'status' => false];
            }
            $checkPass = checkPassword($input['password']);
            if (!$checkPass['status']) {
                return $checkPass;
            }
            $input['nation_code'] = empty($input['nation_code']) ? '86' : trim($input['nation_code']);
            if (!checkMobile(trim($tel), $input['nation_code'])) {
                return ['message' => "手机号格式不对", 'status' => false];
            }

            //通过md5加盐后的唯一值判断
            $rel = Agent::where("non_reversible", $non_reversible)->first();
            if (is_object($rel)) {
                return ['message' => "该手机号已注册，不能重复注册", 'status' => false];
            }

            //判断是否是400注册
            $is400 = 0;
            $tel400 = Config::getConfigValue('agent_400_register');
            if($inviter_phone == $tel400){
                $is400 = 1;
                $inviter_tel = '';
            }

            if(!$is400){
                //通过md5加盐后的唯一邀请值判断
                $inviterInfo = Agent::where(function ($query) use ($inviter_tel, $inviter_phone) {
                    return $query->where('non_reversible', $inviter_tel)->orWhere('my_invite', $inviter_phone);
                })->where('status', 1)->where('is_verified', 1)->first();
                if (!is_object($inviterInfo)) {
                    return ['message' => "该邀请人不存在", 'status' => false];
                }
                //如果是6位邀请码，就把邀请人手机号码作为邀请码
                if (strlen($input['inviter_phone']) == 6) {
                    $inviter_tel = trim($inviterInfo['non_reversible']);
                }
            }

            $checkResult = duanIdentify::checkIdentify($non_reversible, 'agent_register', $input['code'], $time = 900, 'agent');
            //验收环境任意验证码都能通过
            if (app()->environment() === 'beta') {
                $checkResult = 'success';
            }
            if ($checkResult != 'success') {
                return ['message' => "验证码错误", 'status' => false];
            }
            $inviteNum = Agent::createInviteNum(Agent::class, 'my_invite');

            $nickname = 'a' . time();
            $data = array(
                "username" => $username,
                "non_reversible" => $non_reversible,//md5加盐的唯一值
                "password" => Hash::make($input['password']),
                "register_invite" => $inviter_tel,
                'nickname' => $nickname,
                "my_invite" => $inviteNum,
                'agent_level_id' => 1,
                'nation_code' => trim($input['nation_code']),
            );

            //数据中心处理
            $url = config('system.data_center.hosts') . config('system.data_center.encrypt');
            $param = [
                'nation_code' => $input['nation_code'],
                'tel' => $tel,
                'platform' => 'agent',//来源无界商圈注册
                'en_tel' => $non_reversible,//通过加盐后得到手机号码
            ];

            //请求数据中心接口
            $result = json_decode(getHttpDataCenter($url, '', $param));


            //如果异常则停止
            if (!$result) {
                return ['status' => FALSE, 'message' => '服务器异常！'];
            } elseif ($result->status == false) {
                return ['status' => false, 'message' => $result->message];
            }

            $agentObj = Agent::create($data);
            if (!is_object($agentObj)) {
                return ['message' => "保存数据失败", 'status' => false];
            }
            //给此经纪人创建一条附加信息记录
            $data = [];
            $data['agent_id'] = $agentObj['id'];
            $is400 && $data['is_400_register'] = 1;
            AgentAdd::create($data);
            $rel = $agentObj->id;

            //生成token
            $user_token = GainToken('agent' . $rel, $agentObj['nickname'], '');
            Agent::where('id', $rel)->update(['token' => $user_token]);

            Agent::instance()->sendInfo($input['username'], $rel);

            //
            $data = [];
            $data['agent_id'] = $rel;
            $data['nickname'] = $nickname;
            return ['message' => $data, 'status' => true];
        } else if ($submitFlag == 'end') {
            $agentId = intval($input['agent_id']);
            $isSkip = intval($input['is_skip']);
            if (empty($agentId)) {
                return ['message' => "请传递经纪人id", 'status' => false];
            }
            if (!in_array($isSkip, [0, 1])) {
                return ['message' => "请输入正确的is_skip值", 'status' => false];
            }
            $agentInfo = Agent::where("id", $agentId)->where('status', '<>', -1)->first();
            if (!is_object($agentInfo)) {
                return ['message' => "请输入有效的经纪人id", 'status' => false];
            }
            if (!$isSkip) {
                $authResult = Agent::saveAuthInfo($input);
                if (isset($authResult['error'])) {
                    return ['message' => $authResult['message'], 'status' => false];
                }
            }
            Agent::where('id', $agentId)->increment('login_count', 1, ['last_login' => time()]);

            $data = [];
            $data = self::afterLoginBack('', $agentId);
            //注册成功发送推送
            $agentInfo = Agent::where("id", $agentId)->where('status', '<>', -1)->first();
            try {
                $res = send_transmission(json_encode(['type' => 'new_message', 'style' => 'json',
                    'value' =>
                        [
                            'title' => '从今天起，你就是无界商圈的专业经纪人',
                            'sendTime' => time(),
                        ]
                ]),
                    $agentInfo, null, 1);

            } catch (\Exception $e) {
                return ['message' => $e->getMessage(), 'status' => false];
            }
            return ['message' => $data, 'status' => true];
        }
    }
}