<?php namespace App\Services;

use App\Http\Requests\Agent\AgentRequest;
use App\Http\Requests\Agent\CustomerRequest;
use App\Http\Requests\CustomerAgentRequest;
use App\Models\Agent\Agent;
use App\Models\Agent\AgentCustomerLog;
use App\Models\Agent\BaseInfoAdd;
use App\Models\Brand\BrandStore;
use App\Models\User\Entity as User;
use App\Services\Version\VersionSelect;
use App\Models\Activity\Entity;
use App\Models\Agent\AgentCustomer;
use App\Models\Agent\Invitation;
use App\Models\AgentScore;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Models\Message;
use App\Http\Controllers\Api\CommentController;
use App\Models\Zone;
use App\Models\Zone\Entity as Zones;
use App\Services\Chat\Methods\Message as SendMessage;
use App\Services\Chat\Example;
use App\Models\Comment\Entity as Comment;
use App\Models\Orders\Items as OrdersItems;
use App\Models\Activity\Sign;
use App\Models\Orders\Items;
use \App\Models\Contract\Contract as Contracts;
use DB;

class MessageSendService
{
    const ACTIVITY_TYPE   = 1;          //活动类型
    const INSPECT_TYPE    = 2;          //考察类型
    const CONTRACT_TYPE   = 3;          //合同类型
    const CONSENT_TYPE    = 1;          //同意的数字标记
    const REJECT_TYPE     = -1;         //拒绝的数字标记
    const CONSENT         = "consent";  //同意
    const REJECT          = "reject";   //拒绝
    const URLS            = 'js/agent/generic/web/viewer.html?file=';

    protected $dateFormat = 'U';

    public static $instance = null;

    ##  单例 ##
    public static function instance()
    {
        if (is_null(self::$instance)) {
            self::$instance = new self;
        }

        return self::$instance;
    }


    /**
     * 推送经纪人ID通知和消息信息
     *
     * @param $param     指定数据信息
     * @param $tags     标记，考察邀请，活动邀请，合同
     * @param $type     表示拒绝还是接受
     *
     * @return array
     */
    public function pushInfo($param, $tags, $type)
    {
        $user  = User::find((int)$param->uid);
        $agent = Agent::find((int)$param->agent_id);

        if ($tags == "activity") {
            if ($type == "consent") {

                //根据邀请函里的投资人ID和经纪人ID获取一条status=1的数据
                //如果结果为真，进行推送，否则不发送消息
                $gain_result = Invitation::where('uid', $param->uid)
                    ->where('agent_id', $param->agent_id)
                    ->where('status', self::CONSENT_TYPE)
                    ->first();

                // todo：这里的判断是处理重复操作的（处理重复的接受和拒绝）
//                if ($param->status == self::CONSENT_TYPE) {
//                    return ['message' => trans('tui.activity_consent_notice'), 'status' => false];
//                } elseif ($param->status == self::REJECT_TYPE) {
//                    return ['message' => trans('tui.activity_reject_notice'),  'status' => false];
//                }
                if ($gain_result) {

                    //c端推送提示消息
                    $content = [
                        'type' => 'new_remind',
                        'style' => 'json',
                        'value' => [
                            'title' => trans('tui.activity_tui_consent_notice', ['name' => $user->nickname]),
                            'sendTime' => time(),
                        ]
                    ];

                    //融云推送消息
                    $activity = Sign::with('hasOneActity', 'belongsToMaker')
                        ->where('activity_id', $param->post_id)->where('uid', $param->uid)->first();

                    $times = date('Y年m月d日', $activity['hasOneActity']['begin_time']);  //活动时间
                    $url = shortUrl('https://' . env('APP_HOST') . '/webapp/agent/newsactask/detail?invite_id=' . $param->id);
                    $urls = shortUrl('https://' . env('APP_HOST') . '/webapp/actinvitation/detail/_v020800?invite_id=' . $param->id);

                    $agentContent = [
                        'agent_name' => $agent->is_public_realname ?  $agent->realname : $agent->nickname,
                        'activity_name' => $activity['hasOneActity']['subject'],
                        'time' => $times,
                        'place_zone' => $activity['belongsToMaker']['subject'],
                        'url' => $url
                    ];
                    $investorContent = [
                        'agent_name' => $agent->is_public_realname ?  $agent->realname : $agent->nickname,
                        'activity_name' => $activity['hasOneActity']['subject'],
                        'time' => $times,
                        'place_zone' => $activity['belongsToMaker']['subject'],
                        'url' => $urls
                    ];

                    $gather_data = [
                        'content' => [
                            'investorContent' => trans('tui.activity_rong_consent_notice', $investorContent),
                            'agentContent' => trans('tui.activity_rong_consent_notice', $agentContent),
                        ],
                        'investorContent' => trans('tui.activity_rong_consent_notice', $investorContent),
                        'agentContent' => trans('tui.activity_rong_consent_notice', $agentContent),
                    ];

                    $activity_send_result = SendCloudMessage($user->uid, 'agent' . $agent->id, $gather_data, 'TY:DiffMsg', $gather_data, 'custom');  //发送融云消息
                    if ($activity_send_result['status']) {
                        $this->_alterInviteStatus($param, 'consent', 'invite');
                    }
                }
            } else {
                if ($param->status == self::REJECT_TYPE) {
                    return ['message' => trans('tui.activity_reject_notice', ['name' => $user->nickname]), 'status' => false];
                }

                //c端推送提示消息
                $content = [
                    'type'  => 'new_remind',
                    'style' => 'json',
                    'value' => [
                        'title'    => trans('tui.activity_tui_reject_notice', ['name' => $user->nickname]),
                        'sendTime' => time(),
                    ]
                ];

                //融云推送消息
                $url   = shortUrl('https://' . env('APP_HOST') . '/webapp/agent/newsactask/detail?invite_id='. $param->id);
                $urls  = shortUrl('https://' . env('APP_HOST') . '/webapp/actinvitation/detail/_v020800?invite_id='. $param->id);
                $data  = trans('tui.activity_rong_reject_notice', ['agent_name' => $agent->is_public_realname ? $agent->realname : $agent->nickname, 'statement' => $param->remark, 'url' => $url]);
                $datas = trans('tui.activity_rong_reject_notice', ['agent_name' => $agent->is_public_realname ? $agent->realname : $agent->nickname, 'statement' => $param->remark, 'url' => $urls]);
                $gather_dataa = [
                    'content' => [
                        'investorContent'  => $datas,
                        'agentContent'     => $data
                    ],
                    'investorContent' => $datas,
                    'agentContent'    => $data
                ];

                $result_status = SendCloudMessage($user->uid, 'agent' . $agent->id, $gather_dataa, 'TY:DiffMsg', $gather_dataa, 'custom');  //发送融云消息
                if ($result_status['status']) {
                    $this->_alterInviteStatus($param,'reject', 'invite');
                }
            }
        } elseif ($tags == "inspect") {
            if ($type == "consent") {
                if ($param->status == self::CONSENT_TYPE) {
                    return ['message' => trans('tui.inspect_consent_notice'), 'status' => false];
                } elseif ($param->status == self::REJECT_TYPE) {
                    return ['message' => trans('tui.inspect_reject_notice'),  'status' => false];
                }
                $result_status['status'] = true;

            } else {
                if ($param->status == self::REJECT_TYPE) {
                    return ['message' => trans('tui.inspect_reject_notice'), 'status' => false];
                }

                //c端推送提示消息
                $content = [
                    'type'  => 'new_remind',
                    'style' => 'json',
                    'value' => [
                        'title'    => trans('tui.inspect_tui_reject_notice', ['name' => $user->nickname]),
                        'sendTime' => time(),
                    ]
                ];

                //融云推送消息
                $url   = shortUrl('https://'. env('APP_HOST') . '/webapp/agent/newsinvestask/detail?inspect_id='. $param->id);
                $urls  = shortUrl('https://' . env('APP_HOST') . '/webapp/investinvitation/detail/_v020800?inspect_id=' . $param->id);
                $brand_name = BrandStore::with('hasOneBrand')->where('id', $param['post_id'])->first();

                $agentContent = [
                    'agent_name' => $agent->is_public_realname ?  $agent->realname : $agent->nickname,
                    'brand_name' => $brand_name['hasOneBrand']['name'],
                    'money'      => number_format($param['default_money']),
                    'statement'  => $param->remark,
                    'url'        => $url,
                ];
                $investorContent = [
                    'agent_name' => $agent->is_public_realname ?  $agent->realname : $agent->nickname,
                    'brand_name' => $brand_name['hasOneBrand']['name'],
                    'money'      => number_format($param['default_money']),
                    'statement'  => $param->remark,
                    'url'        => $urls,
                ];

                $gather_data = [
                    'content' => [
                        'investorContent' => trans('tui.inspect_rong_reject_notice', $investorContent),
                        'agentContent'    => trans('tui.inspect_rong_reject_notice', $agentContent)
                    ],
                    'investorContent' => trans('tui.inspect_rong_reject_notice', $investorContent),
                    'agentContent'    => trans('tui.inspect_rong_reject_notice', $agentContent)
                ];

                $result_status = SendCloudMessage($user->uid, 'agent' . $agent->id, $gather_data, 'TY:DiffMsg', $gather_data, 'custom');  //发送融云消息
                if ($result_status['status']) {
                    $this->_alterInviteStatus($param,'reject', 'invite');
                }
            }
        } elseif ($tags == "contract") {
            $contract = Contracts::with(
                ['user' => function ($query) {
                    $query->select('uid', 'avatar', 'realname', 'nickname');
                }, 'brand' => function ($query) {
                    $query->select('id', 'name');
                }, 'agent' => function ($query) {
                    $query->select('id', 'realname');
                }]
            )->where('id', $param['id'])->first();

            if ($type == "consent") {
                if ($contract['status'] == self::CONSENT_TYPE || $contract['status'] == 2) {
                    return ['message' => trans('tui.contract_consent_notice'), 'status' => false];
                } elseif ($contract['status'] == self::REJECT_TYPE) {
                    return ['message' => trans('tui.contract_reject_notice'), 'status' => false];
                }
                $result_status['status'] = true;

            } else {
                if ($contract['status'] == self::REJECT_TYPE) {
                    return ['message' => trans('tui.contract_reject_notice'), 'status' => false];
                }

                //c端推送提示消息
                $content = [
                    'type'  => 'new_remind',
                    'style' => 'json',
                    'value' => [
                        'title'    => trans('tui.contract_tui_reject_notice', ['name' => $user->nickname]),
                        'sendTime' => time(),
                    ]
                ];

                $agentContent = [
                    'agent_name'  => $agent->is_public_realname ?  $agent->realname : $agent->nickname,
                    'contract_no' => $contract['contract_no'],
                    'brand_name'  => $contract['brand']['name'],
                    'money'       => number_format($contract['amount']),
                    'statement'   => $contract['remark'],
                    'urls'        => shortUrl('https://'.env('APP_HOST') .'/'.self::URLS . $contract['address'])
                ];

                $investorContent = [
                    'agent_name'  => $agent->is_public_realname ?  $agent->realname : $agent->nickname,
                    'contract_no' => $contract['contract_no'],
                    'brand_name'  => $contract['brand']['name'],
                    'money'       => number_format($contract['amount']),
                    'statement'   => $contract['remark'],
                    'urls'        => shortUrl('https://'.env('APP_HOST'). '/' .'/'.self::URLS . $contract['address'])
                ];

                $gather_data = [
                    'content' => [
                        'investorContent' => trans('tui.contract_rong_reject_notice', $investorContent),
                        'agentContent'    => trans('tui.contract_rong_reject_notice', $agentContent)
                    ],
                    'investorContent' => trans('tui.contract_rong_reject_notice', $investorContent),
                    'agentContent'    => trans('tui.contract_rong_reject_notice', $agentContent)
                ];

                $result_status  = SendCloudMessage($user->uid, 'agent' . $agent->id, $gather_data, 'TY:DiffMsg', $gather_data, 'custom');  //发送融云消息
                if ($result_status['status']) {
                    $this->_alterInviteStatus($contract, 'reject', 'contract');
                }
            }
        }

        //发送透传消息
        if (count($content)) {
            $tui_result = send_transmission(json_encode($content), $agent, null, true);
        }

        //对综合结果进行处理
        if ( $tui_result && $result_status['status']) {
            return ['message' => '发送成功', 'status' => true];
        } elseif ($tui_result) {
            return ['message' => '推送消息和日记记录成功，融云发送消息失败', 'status' => true];
        } elseif ($result_status) {
            return ['message' => '融云发送消息和日记记录成功，推送消息失败', 'status' => true];
        } else {
            return ['message' => '日记记录成功，融云和推送消息失败', 'status' => true];
        }
    }

    /**
     *  author zhaoyf
     *
     * 改变邀请函的状态
     *
     * @param $param
     * @param $tags
     * @param $type
     */
    public function _alterInviteStatus($param, $tags, $type)
    {
        $uid           = $param['uid'];
        $agent_id      = $param['agent_id'];
        $confirm_id    = $param['id'];

        if ($type === "invite") {
            if ($tags === "consent") {
                $status = 1;
            } else {
                $status = -1;
            }

            Invitation::where('agent_id', $agent_id)
                ->where('uid', $uid)
                ->where('id',  $confirm_id)
                ->update(['status' => $status]);

        } elseif($type === "contract") {
            if ($tags === "consent") {
                $status = 1;
            } else {
                $status = -1;
            }

            Contracts::where('agent_id', $agent_id)
                ->where('uid', $uid)
                ->where('id',  $confirm_id)
                ->update(['status' => $status, 'confirm_time' => time()]);
        }

    }
}