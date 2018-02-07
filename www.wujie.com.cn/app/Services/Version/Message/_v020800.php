<?php namespace App\Services\Version\Message;

use App\Http\Requests\Agent\AgentRequest;
use App\Http\Requests\Agent\CustomerRequest;
use App\Http\Requests\CustomerAgentRequest;
use App\Models\Agent\Agent;
use App\Models\Agent\AgentCustomerLog;
use App\Models\Agent\AgentRongInfo;
use App\Models\Agent\BaseInfoAdd;
use App\Models\Agent\Score\AgentScoreLog;
use App\Models\Brand\BrandStore;
use App\Models\User\Entity as User;
use App\Services\MessageSendService;
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
use App\Models\Brand\Entity as Brand;
use App\Models\Agent\Entity\_v010200 as Agentv010200;

class _v020800 extends VersionSelect
{
    const ACTIVITY_TYPE   = 1;          //活动类型
    const INSPECT_TYPE    = 2;          //考察类型
    const CONTRACT_TYPE   = 3;          //合同类型
    const CONSENT_TYPE    = 1;          //同意的数字标记
    const REJECT_TYPE     = -1;         //拒绝的数字标记
    const CONSENT         = "consent";  //同意
    const REJECT          = "reject";   //拒绝

    protected $dateFormat = 'U';

    public static $instance = null;
    public static function instance()
    {
        if (is_null(self::$instance)) {
           self::$instance = new self;
        }
        return self::$instance;
    }

    /**
     * 星星的评分个数
     */
    public static $str = [
        '1' => 1,
        '2' => 1,
        '3' => 2,
        '4' => 3,
        '5' => 3,
    ];

    /**
     * 查询是否对某经纪人公开手机号
     *
     * @param $param
     * @return array|string
     * @internal param CustomerRequest $request
     */
    public function postIfPublicMobile($param)
    {
        $result = $param['request']->input();

        $query_result = AgentCustomer::where('agent_id', $result['agent_id'])
            ->where('uid', $result['customer_id'])->first();

        //对查询结果进行处理
        if ($query_result->has_tel === 0 ) {
            return ['message'=> ['open' => 0], 'status' => true];
        } elseif ($query_result->has_tel === 1) {
            return ['message' => ['open' => 1], 'status' => true];
        } else {
            return ['message' => '没有查到相关信息', 'status' => false];
        }
    }

    /**
     * 向经纪人公开手机号
     *
     * @param $param
     * @return array|string
     * @internal param CustomerRequest $request
     */
    public function postPublicMobile($param)
    {
        $result = $param['request']->input();

        $public_result = AgentCustomer::where('agent_id', $result['agent_id'])
            ->where('uid', $result['customer_id'])
            ->update(['has_tel' => 1]);

        //对结果进行处理
        if ($public_result) {

            $data = ['content' => '投资人向代理经纪人公开了手机号', 'extra' => 1];
            SendCloudMessage($result['customer_id'], 'agent' . $result['agent_id'],  $data, 'TY:TipMsg', $data, 'custom','one_user');

            //往agent_rong_info表里添加数据
            AgentRongInfo::insert([
                'send_id'      => $result['customer_id'],
                'receive_id'   => 'agent'. $result['agent_id'],
                'info_type'    => Agent::$info_type,
                'content'      => json_encode($data),
                'channel_type' => Agent::$channel_type,
                'msg_time'     => time(),
            ]);

            //公开手机号以后，往日记表里插入数据
            $gain_result = AgentCustomer::where('agent_id', $result['agent_id'])
                ->where('uid', $result['customer_id'])->first();

            //组织数据
            $insert_data = [
                'agent_customer_id' => $gain_result->id,
                'action'            => 2,
                'post_id'           => 0,
                'brand_id'          => $gain_result->brand_id,
                'agent_id'          => $gain_result->agent_id,
                'created_at'        => time(),
                'uid'               => $gain_result->uid,
            ];
            $res = AgentCustomerLog::insertGetId($insert_data);

            //加积分
            Agentv010200::add($result['agent_id'], AgentScoreLog::$TYPES_SCORE[7], 7, '获得投资人手机号', $res);


            return ['message' => '公开成功', 'status' => true];
        } else {
            return ['message' => '遇到意外，或者已经公开过了，请确认', 'status' => false];
        }
    }

    /**
     * author zhaoyf
     *
     * 初始化显示经纪人和品牌信息数据
     *
     * @param $param
     * @return array
     */
    public function postAgentInfoInitializeShow($param)
    {
        $result = $param['request']->input();

        if (empty($result['agent_id']) || !intval($result['agent_id'])) {
            return ['message' => '经纪人ID不能为空且只能是整形', 'status' => false];
        }
        if (empty($result['brand_id']) || !intval($result['brand_id'])) {
            return ['message' => '品牌ID不能为空且只能是整形', 'status' => false];
        }

        $confirm_data = AgentScore::instance()->AgentInfoInitializeShow($result);

        return ['message' => $confirm_data['message'], 'status' => $confirm_data['status']];
    }

    /**
     * 对品牌和经纪人进行评价
     *
     * return bool
     * @param $param
     * @return array|string
     * @internal param CustomerAgentRequest $request
     */
    public function postAddComment($param)
    {
        $result = $param['request']->input();

        //对用户ID进行处理
        if (!isset($result['customer_id']) || !is_numeric($result['customer_id']) ) {
            return ['message' => '客户ID不能为空，且只能为整数', 'status' => false];
        }

        //当用户没有上传图片的时候，默认为空
        if (empty($result['images'])) {
            $result['images'] = [];
        }

        //处理提交的评论内容
        $content = mb_convert_encoding($result['content'], 'utf-16');
        $bin     = bin2hex($content);
        $arr     = str_split($bin, 4);
        $l       = count($arr);
        $str     = '';

        for ($n = 0; $n < $l; ++$n) {
            if (isset($arr[$n + 1]) && ('0x' . $arr[$n] >= 0xd800 && '0x' . $arr[$n] <= 0xdbff && '0x' . $arr[$n + 1] >= 0xdc00 && '0x' . $arr[$n + 1] <= 0xdfff)) {
                $H    = '0x' . $arr[$n];
                $L    = '0x' . $arr[$n + 1];
                $code = ($H - 0xD800) * 0x400 + 0x10000 + $L - 0xDC00;
                $str .= '&#' . $code . ';';
                $n++;
            } else {
                $str .= mb_convert_encoding(hex2bin($arr[$n]),'utf-8','utf-16');
            }
        }

        $content = $str;
        $type    = 'Brand';

        //当用户为匿名形式评价时，随机生成一个名称
        if ($result['is_anonymous'] == 1) {
            $nicknames = DB::table('comment')->where('type', $type)
                ->where('post_id',  $result['brand_id'])
                ->where('uid', 0)->lists('nickname');
            $arr = array();
            foreach ($nicknames as $k => $v) {
                $arr[] = substr($v, -4);
            }
            $num      = $this->makeNum($arr);
            $nickname = '匿名' . $num;
        } else {
            $user     = DB::table('user')->where('uid', $result['customer_id'])->first();
            $nickname = $user->nickname;
        }

        //对用户对品牌星星评价的处理
        $manage_grade = self::$str[$result['level']];

        //对品牌进行评价
        //$count    = Comment::commentsCount($type, $result['brand_id']);
        $brand_comment_result = Comment::add($result['brand_id'], $result['customer_id'], $type, $content, 0, $nickname, [], $result['images'], 'normal', 'adopt', $manage_grade, $result['level']);

        //查询出合同ID
        $con_id = Contracts::where('agent_id', $result['agent_id'])
            ->where('uid', $result['customer_id'])
            ->where('brand_id', $result['brand_id'])
            ->whereIn('status', [2, 4, 5])
            ->value('id');

        //对查询的合同ID结果进行处理
        if (is_null($con_id)) {
            return ['message' => '根据传递的经纪人ID和用户ID以及品牌ID，没有查询出对应的合同ID； 缺少合同ID', 'status' => false];
        }

        //添加对经纪人的评价
        $data = [
            'agent_id'      => $result['agent_id'],
            'customer_id'   => $result['customer_id'],
            'service_score' => empty($result['service_score']) ? 0 : $result['service_score'],
            'ability_score' => empty($result['ability_score']) ? 0 : $result['ability_score'],
            'timely_score'  => empty($result['timely_score'])  ? 0 : $result['timely_score'],
            'brand_id'      => $result['brand_id'],
            'contract_id'   => $con_id,
            'created_at'    => time(),
        ];

        $agent_comment_result = AgentScore::insertGetId($data);

        //对结果进行处理
        if ( $agent_comment_result) {
            return ['message' => $agent_comment_result, 'status' => true];
        } else {
            return ['message' => '评价失败', 'status' => false];
        }
    }

    /**
     * 查询客户对经纪人和品牌的评价
     *
     * @internal param $agent_id     经纪人ID
     * @internal param $customer_id  客户ID
     * @paeam    $brand_id           品牌ID
     *
     * @param $param
     * @return data_list|array
     */
    public function postComment($param)
    {
        $result = $param['request']->input();

        if (empty($result['brand_id']) || !intval($result['brand_id'])) {
            return ['message' => '品牌ID不能为空且只能是整形', 'status' => false];
        }

        $confirm_data = AgentScore::instance()->AgentBrandScore($result);

        return ['message' => $confirm_data['message'], 'status' => $confirm_data['status']];

    }

    /**
     * 客户--多动作操作（活动邀请、考察邀请，合同）zhaoyf
     *
     * @param $param
     *
     * @return array|bool
     */
    public function postMultipleAction($param)
    {
        $result = $param['request']->input();

        return $this->_multipleAction($result);
    }

    /**
     * 内部类型操作：考察邀请，活动邀请，合同（集合动作：接受 | 拒绝）
     * @param $param
     * @return array
     */
    private  function _multipleAction($param)
    {
        if ($param['action_type'] == self::ACTIVITY_TYPE) {

            //根据邀请函ID获取信息
            $get_activity_invite = $this->_getInvitationInfo($param);
            if (!$get_activity_invite) {
                return ['message' => '该活动邀请不存在', 'status' => false];
            }

            //获取邀请活动下的品牌ID
           /* $activity_brand_id = Entity::with('brand')
                ->where('id', $get_activity_invite->post_id)
                ->first();*/

            $activity_brand_id = DB::table('activity_brand')
                ->where('activity_brand.activity_id', $get_activity_invite->post_id)
                ->value('brand_id');

            //对动作结果进行处理
            if ($param['action'] === self::CONSENT) {
                $this->_sendNote($get_activity_invite); //发送接受短信提示
                $add_result = $this->addAgentCustomerLog($get_activity_invite, $activity_brand_id, 5);
                if ($add_result['status']) {
                   return $this->_agentPushInfo($this->_getInvitationInfo($param), 'activity', self::CONSENT);
                } else {
                    return $add_result;
                }
            } elseif ($param['action'] === self::REJECT) {
                $add_result = $this->addAgentCustomerLog($get_activity_invite, $activity_brand_id, 4, $param['remark']);
                if ($add_result['status']) {
                   return $this->_agentPushInfo($this->_getInvitationInfo($param), 'activity', self::REJECT);
                } else {
                    return $add_result;
                }
            } else {
                return ['message' => '动作操作不存在', 'status' => false];
            }

        } elseif ($param['action_type'] == self::INSPECT_TYPE) {

            //根据邀请函ID获取信息
            $get_inspect_invite = $this->_getInvitationInfo($param);
            if (!$get_inspect_invite)   {
                return ['message' => '该考察邀请不存在', 'status' => false];
            }
            $inspect_brand_id = BrandStore::where('id', $get_inspect_invite->post_id)->value('brand_id');

            //对动作结果进行处理
            if ($param['action'] === self::CONSENT) {
//                if ($_result['status']) {
                   return $this->_agentPushInfo($this->_getInvitationInfo($param), 'inspect', self::CONSENT);
//                } else {
//                    return $_result;
//                }
            } elseif ($param['action'] === self::REJECT) {
                $_result = $this->addAgentCustomerLog($get_inspect_invite, $inspect_brand_id, 7, $param['remark']);
                if ($_result['status']) {
                   return $this->_agentPushInfo($this->_getInvitationInfo($param), 'inspect', self::REJECT);
                } else {
                    return $_result;
                }
            } else {
                return ['message' => '动作操作不存在', 'status' => false];
            }

        } elseif ($param['action_type'] == self::CONTRACT_TYPE) {

                //根据合同ID获取信息
                $contract_info = Contracts::find($param['action_id']);

                if (!$contract_info) {
                    return ['message' => '该合同不存在', 'status' => false];
                }

            //对动作结果进行处理
            if ($param['action'] === self::CONSENT) {
//                if ($_result) {
                   return $this->_agentPushInfo(Contracts::find($param['action_id']), 'contract', self::CONSENT);
//                } else {
//                    return $_result;
//                }
            } elseif ($param['action'] === self::REJECT) {
                $_result = $this->addAgentCustomerLog($contract_info, $contract_info['brand_id'], 10, $param['remark'], 'contract');
                if ($_result['status']) {
                   return $this->_agentPushInfo(Contracts::find($param['action_id']), 'contract', self::REJECT);
                } else {
                    return $_result;
                }
            } else {
                return ['message' => '动作操作不存在', 'status' => false];
            }
        } else {
            return ['message' => '动作类型不存在', 'status' => false];
        }
    }

    /**
     * 接受活动邀请函以后，向经纪人发送短信提示  --数据中心版
     *
     * @param $param
     * @return int|mixed
     */
    private function _sendNote($param)
    {
        $result_data = Invitation::with('belongsToAgent', 'hasOneUsers', 'hasOneActivity')
            ->where('agent_id', $param['agent_id'])
            ->where('uid',  $param['uid'])
            ->where('expiration_time', '>', time())
            ->where('type', self::ACTIVITY_TYPE)
            ->where('status', self::CONSENT_TYPE)
            ->first();

        //对手机号进行处理，默认只发送中国号
        if ($result_data->hasOneUsers->non_reversible) {

            //组织参数
            $data = [
                'customer_name'  => !empty($result_data->hasOneUsers->realname) ? $result_data->hasOneUsers->realname : $result_data->hasOneUsers->nickname,
                'customer_tel'   => $result_data->hasOneUsers->username,
                'activity_name'  => $result_data->hasOneActivity->subject,
                'activity_times' => date("Y年m月d日 H:i", $result_data->hasOneActivity->begin_time),
                'urls'           => substr(shortUrl('http://'. env("APP_HOST") . "/webapp/agent/newsactask/detail?invite_id=" . $result_data->id), 7),
            ];

            //发送短信
           return @SendTemplateSMS('invite_activity_success_info', $result_data->belongsToAgent->non_reversible, 'invite_activity_success_info', $data);
        }
    }

    /**
     * 根据对应的邀请函ID，从邀请函表获取数据
     *
     * @param $param 参数集合
     * @param string $type 获取数据数：一个或多个
     *
     * @return bool | result_object
     */
    private function _getInvitationInfo($param, $type = "one")
    {
        if ($type == "one") {
            $get_invite = Invitation::where('id', intval($param['action_id']))->first();
        } elseif ($type == "multiple") {
            $get_invite = Invitation::where('id', intval($param['action_id']))->get();
        }

        //对结果进行处理
        if ($get_invite) {
            return $get_invite;
        } else {
            return false;
        }
    }

    /**
     * 经纪人用户日记表添加记录
     * @param    $data          需要添加的数据
     * @param    $brand_id      品牌ID
     * @param    $action        拒绝 | 接受
     * @param    $describe      拒绝时需要的备注信息
     * @param    string $type   区分邀请函和合同 invite:邀请函；contract:合同
     * @internal $result        数据个数获取
     *
     * @return   array
     *
     */
    public  function addAgentCustomerLog($data, $brand_id, $action, $describe = null, $type = "invite")
    {
        if (is_null($brand_id) || !isset($brand_id)) {
            return ['message' => '所关联的活动或考察或合同对应的品牌ID不存在', 'status' => false];
        }

        //根据邀请函里的用户ID和经纪人ID关联agent_customer表获取ID，然后添加到agent_customer_log表里
        $agent_customer_id = AgentCustomer::where('uid',$data->uid)->where('agent_id', $data->agent_id)->value('id');

        $datas = [  //组合数据
            'agent_customer_id' => $agent_customer_id ? $agent_customer_id : 0,
            'agent_id'          => $data->agent_id,
            'action'            => $action,
            'brand_id'          => $brand_id,
            'created_at'        => time(),
            'uid'               => $data->uid,
        ];

        //除了用户对于邀请函或合同的接受和拒绝外，其他情况post_id = 0
        if (in_array($action, [-1, 0, 1, 2, 12])) {
            $datas['post_id']  = 0;
        } else {
            $datas['post_id']  = $data->id;
        }

        if ($type == "invite") {            //邀请函类型
            if (!is_null($describe)) {
                $datas['remark'] = $describe;
                $update_result = Invitation::where('id', intval($data->id))
                    ->update(['remark' => htmlspecialchars($describe)]);
            } else {
                $update_result = 1;
            }

        } elseif ($type == "contract") {    //合同类型
            if (!is_null($describe)) {
                $datas['remark'] = $describe;
                $update_result   = Contracts::where('id', $data->id)
                 ->update(['remark' => $describe]);
            } else {
                $update_result = 1;
            }
        } elseif ($type == "other") {
            if (!is_null($describe)) {
                $datas['remark'] = $describe;
            }
        }

        //添加日记记录，同时处理结果
        $add_result = AgentCustomerLog::insert($datas);

        if ($type == "other") {
            if ($add_result) {
                return ['message' => '操作成功', 'status' => true];
            } else {
                return ['message' => '操作失败', 'status' => false];
            }
        } else {
            if ($update_result && $add_result) {
                return ['message' => '操作成功', 'status' => true];
            } else {
                return ['message' => '操作失败', 'status' => false];
            }
        }
    }

    /**
     * 推送经纪人通知和消息信息
     *
     * @param $param    指定数据信息
     * @param $tags     标记，考察邀请，活动邀请，合同
     * @param $type     表示拒绝还是接受
     *
     * @return array
     */
    private function _agentPushInfo($param, $tags, $type)
    {
        if (!$param) return ['message' => '邀请函不存在', 'status' => false];

        return MessageSendService::instance()->pushInfo($param, $tags, $type);
    }

    /**
     *  author zhaoyf   --数据中心版
     *
     * 邀请函动作（拒绝 | 接受；活动直接为拒绝）
     * @param $param
     *
     * @return data_list|array
     */
    public function postInviteAction($param)
    {
        $result = $param['request']->input();

       //等于活动时，获取活动详情数据（action只能为：reject）
       if ($result['type'] == self::ACTIVITY_TYPE) {
           if ($result['action_tags'] !== self::REJECT) {
               return ['message' => '缺少对应的活动动作行为，且只能为：reject', 'status' => false];
           }

           //根据邀请函ID获取到对应的数据信息
           $activity = Invitation::with( 'hasOneUsers', 'belongsToAgent', 'hasOneActivity')
               ->where('id', $result['invite_id'])->first();

           //获取经纪人和客户的关系
           $relative = $this->_agentCustomerRelative($activity['hasOneUsers']['uid'], $activity['belongsToAgent']['id'], $activity['hasOneUsers']['register_invite'], $activity['belongsToAgent']['non_reversible']);

           //活动对应的地区信息
           $site = DB::table('activity_maker')
               ->leftJoin('maker', 'activity_maker.maker_id', '=', 'maker.id')
               ->leftJoin('zone', 'maker.zone_id', '=', 'zone.id')
               ->where('activity_maker.activity_id', $activity->hasOneActivity->id)
               ->where('activity_maker.status', 1)
               ->select(DB::raw('GROUP_CONCAT(lab_zone.name) as site'))
               ->first();

           //获取经纪人数据信息
           $agent = [
               'is_public_realname' => $activity['belongsToAgent']['is_public_realname'],
               'realname'    => $activity['belongsToAgent']['realname'],
               'nickname'    => $activity['belongsToAgent']['nickname'],
               'avatar'      => getImage($activity['belongsToAgent']['avatar'], 'avatar', ''),
               'level'       => $activity['belongsToAgent']['agent_level_id'],
               'level_title' => Agent::$Agentlevel[$activity['belongsToAgent']['agent_level_id']],
               'relative'    => $relative,
           ];

           //获取活动数据信息
           $activity = [
               'subject'     => $activity->hasOneActivity->subject,
               'detail_img'  => $activity->hasOneActivity->detail_img,
               'begin_time'  => $activity->hasOneActivity->begin_time,
               'address'     => $site->site,
               'keywords'    => $activity->hasOneActivity->keywords ?  explode(' ', $activity->hasOneActivity->keywords) : [],
           ];

           //返回组合后的具体信息
           return ['message' => ['activity' =>$activity, 'agent' => $agent], 'status' => true];

       //等于考察邀请时，获取考察详情数据信息
       } elseif ($result['type'] == self::INSPECT_TYPE) {

           //根据邀请函ID获取到对应的数据信息
           $inspect = Invitation::with(['hasOneUsers' => function($query) {
               $query->select('uid', 'register_invite');
           }, 'belongsToAgent' => function($query) {
                $query->select('id', 'is_public_realname', 'nickname', 'realname', 'avatar', 'agent_level_id','username','non_reversible');
           }, 'hasOneStore' => function($query) {
                $query->select('id', 'brand_id', 'name', 'address', 'zone_id');
           }, 'hasOneStore.hasOneBrand' => function($query) {
                $query->select('id', 'name', 'logo', 'slogan', 'categorys1_id', 'investment_min', 'investment_max', 'keywords');
           }, 'hasOneStore.hasOneBrand.categorys1' => function($query) {
                $query->select('id', 'name', 'pid');
           }])
           ->where('id',   $result['invite_id'])
           ->where('type', self::INSPECT_TYPE)
           ->first();

           //对查询结果进行处理
           if (is_null($inspect)) {
               return ['message' => '没有查询到任何信息', 'status' => false];
           }

           //判断品牌是否已经下架
           if ($inspect['hasOneStore']['hasOneBrand']) {
               $brand_result = Brand::where('id', $inspect['hasOneStore']['hasOneBrand']['id'])->first();
               if ($brand_result->status == 'disable' || $brand_result->agent_status == '0') {
                    return ['message' => ['status' => -1, 'message' => '此产品已经下架'], 'status' => false];
               }
           }

           //获取经纪人和客户的关系
           $relative = $this->_agentCustomerRelative($inspect['hasOneUsers']['uid'], $inspect['belongsToAgent']['id'], $inspect['hasOneUsers']['register_invite'], $inspect['belongsToAgent']['non_reversible']);

           //获取经纪人数据信息
           $agent = [
               'is_public_realname' => $inspect['belongsToAgent']['is_public_realname'],
               'realname'    => $inspect['belongsToAgent']['realname'],
               'nickname'    => $inspect['belongsToAgent']['nickname'],
               'avatar'      => getImage($inspect['belongsToAgent']['avatar'], 'avatar', ''),
               'level'       => $inspect['belongsToAgent']['agent_level_id'],
               'level_title' => Agent::$Agentlevel[$inspect['belongsToAgent']['agent_level_id']],
               'relative'    => $relative,
           ];

           //获取品牌信息
           $inspect_data = [
                'brand_name'            =>  $inspect['hasOneStore']['hasOneBrand']['name'],
                'brand_logo'            =>  getImage($inspect['hasOneStore']['hasOneBrand']['logo']),
                'brand_slogan'          =>  $inspect['hasOneStore']['hasOneBrand']['slogan'],
                'inspect_industry_cate' =>  $inspect['hasOneStore']['hasOneBrand']['categorys1']['name'],
                'start_money'           =>  $inspect['hasOneStore']['hasOneBrand']['investment_min']>=100 ? $inspect['hasOneStore']['hasOneBrand']['investment_min'] : abandonZero($inspect['hasOneStore']['hasOneBrand']['investment_min']) .' - '.abandonZero($inspect['hasOneStore']['hasOneBrand']['investment_max']),
                'inspect_store'         =>  $inspect['hasOneStore']['name'],
                'inspect_header_region' =>  Zones::pidNames([$inspect['hasOneStore']['zone_id']]),
                'inspect_detail_site'   =>  $inspect['hasOneStore']['address'],
                'inspect_time'          =>  $inspect->inspect_time,
                'keywords'              =>  $inspect['hasOneStore']['hasOneBrand']['keywords'] ? explode(' ', $inspect['hasOneStore']['hasOneBrand']['keywords']) : [],
           ];

           if ($result['action_tags'] === self::CONSENT) {
                $inspect_data['default_money'] = number_format($inspect->default_money);
            }

            //返回组合后的数据信息
            return ['message' => ['agent' => $agent, 'inspect' => $inspect_data], 'status' => true];
       }

    }

    /**
     * 获取经纪人和客户的关系
     *
     * 经纪人关系
     *  1：派单关系；
     *  2：邀请关系；
     *  3：邀请、派单关系；
     *  4：派单、邀请关系；
     *  5：推荐关系；
     *  0：没有关系
     *
     * @param $uid
     * @param $agent_id
     *
     * @param $register
     * @param $username
     *
     * @return int
     */
    protected function _agentCustomerRelative($uid, $agent_id, $register, $username)
    {
        //获取经纪人和客户的关系
        $agent_relation = AgentCustomer::where('agent_id', $agent_id)
            ->where('uid', $uid)
            ->where('level' , '<>', -1)
            ->where('status', '<>', -1)
            ->first()->source;

        if ($register == $username) {
            $relative = 2;
        } elseif ($agent_relation) {
            switch ($agent_relation) {
                case 1:
                case 2:
                case 3:
                case 4:
                    $relative = 2;  //邀请关系
                    break;
                case 5:
                    $relative = 1;  //派单关系
                    break;
                case 6:
                    $relative = 3;  //邀请派单关系
                    break;
                case 7:
                    $relative = 4;  //派单邀请关系
                    break;
                case 8:
                    $relative = 5;  //推荐关系
                    break;
                default:
                    $relative = 0;  //没有关系
                    break;
            }
        } else {
            $relative = 0;
        }

        return $relative;
    }

    /**
     * 生成4个不重复的数字
     */
    private function makeNum($arr)
    {
        $num = rand(0, 9) . rand(0, 9) . rand(0, 9) . rand(0, 9);
        if (in_array($num, $arr)) {
            $num = self::makeNum($arr);
        }

        return $num;
    }
}