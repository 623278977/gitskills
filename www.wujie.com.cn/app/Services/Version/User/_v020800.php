<?php namespace App\Services\Version\User;

use App\Http\Controllers\Api\CommonController;
use App\Models\Activity\Sign as ActivitySign;
use App\Models\Agent\AgentBrand;
use App\Models\Agent\AgentCurrencyLog;
use App\Models\Agent\AgentCustomer;
use App\Models\Agent\AgentCustomerLog;
use App\Models\Agent\Invitation;
use App\Models\Categorys;
use App\Models\Contract\Contract;
use App\Models\Fund;
use App\Models\User\Entity as User;
use App\Models\Agent\Agent;
use App\Models\User\Industry;
use App\Models\Industry as Industrs;
use App\Models\User\Entity;
use App\Models\CacheTool;
use App\Models\ScoreLog;
use App\Models\Brand\Entity as Brand;
use App\Models\Brand\Entity\V020800 as BrandV020800;
use App\Models\User\UserFondCate;
use App\Models\Zone\Entity as Zone;
use App\Models\AgentScore;
use DB;
use App\Models\Orders\Entity as Orders;
use App\Models\User\Free;
use App\Models\SendInvestor\V020800 as SendInvestor;
use App\Models\Message;


class _v020800 extends _v020700
{

    const FUND_VALID = 1;
    const FUND_INVALID = 0;
    const CONFIRM_AGENCY = 4;

    //经纪人和客户的关系数字标记
    const RELATION_ID_1 = 1;
    const RELATION_ID_2 = 2;
    const RELATION_ID_3 = 3;
    const RELATION_ID_4 = 4;
    const RELATION_ID_5 = 5;
    const RELATION_ID_6 = 6;
    const RELATION_ID_7 = 7;
    const RELATION_ID_8 = 8;
    const RELATION_ID_9 = 9;
    const LOST_ID_TYPE  = -1;

    /**
     * 投资人详情    --数据中心版
     *
     * @param  $param investor_id 投资人ID
     *
     * @return array|string
     */
    public function postDetail($param)
    {
        if (empty($param['investor_id']) || !isset($param['investor_id'])) {
            return ['message' => '缺少投资人ID：investor_id', 'status' => false];
        }

        //获取投资人基本信息
        $results = User::with('zone', 'industrys.industry')
            ->where('uid', $param['investor_id'])
            ->first();

        //对查询投资人的结果进行处理
        if(is_null($results)) {
            return ['message' => '没有查询到投资人的相关信息', 'status' => false];
        }

        //获取城市
        $zone      = new \App\Models\Zone\Entity();
        $zone_name = $zone->pidNames([$results['zone']['id']]);

        //获取经纪人姓名
        $agent_name = Agent::where('non_reversible', $results['register_invite'])->first();
        if (is_object($agent_name)) {
            if ($agent_name->is_public_realname) {
                $agent_names = $agent_name->realname ?: $agent_name->nickname;
            } else {
                $agent_names = $agent_name->nickname;
            }
        } else {
            $agent_names = "";
        }

        //获取用户感兴趣的分类
        $industry_name = [];
        foreach ( $results['industrys'] as $keys => $vls) {
            $industry_name[$keys] = $vls['industry']['name'];
        }

        //对分类进行字符串处理
        $industry_names = $industry_name ?  implode(' ', $industry_name) : '';

        //组合数据
        $data = [
            'customer_name' => $results['realname'] ?: $results['nickname'],
            'avatar'        => getImage($results['avatar'], 'avatar', ''),
            'sign'          => $results['sign'],
            'gender'        => $results['gender'] == -1 ? '未知' : ($results['gender'] == 0 ? '女' : '男'),
            'last_login'    => $results['last_login'],
            'city'          => $zone_name,
            'diploma'       => $results['diploma'],
            'position'      => $results['profession'],
            'earning'       => $results['earning'],
            'interest_industries' => $industry_names,
            'invest_intention'    => $results['invest_intention'],
            'invest_quota'        => $results['investment_min'] . '-' . $results['investment_max'],
            'created_at'          => $results['created_at'],
            'invite'              => $agent_names,
            'last_login'          => date('d', $results['last_login']),
            'keywords'            => [
                getTime($results['birth'], 'birth_time'),
                getStarsign($results['birth'], 'birth_time'),
                str_replace('市', '人', $results['zone']['name']),
                $industry_name,
                User::$IFIntention[$results['invest_intention'] ?: 0],
                $results['investment_min'] . '-' . $results['investment_max'],
            ],
        ];

        return ['message' => $data, 'status' => true];
    }

    /**
     * 投资人个人信息修改
     *
     * @param $param
     *
     * @return array|bool
     */
    public function postUpdate($param)
    {
        $results = $param['request']->input();

        if (!isset($results['uid']) || !is_numeric($results['uid'])) {
            return ['message' => '缺少用户ID：uid，且只能为整数', 'status' => false];
        }
        if (!Entity::checkAuth($results['uid'])) {
            return ['message' => '账号有误', 'status' => false];
        }

        if (isset($results['nickname']) || !empty($results['nickname'])) {
            $content = mb_convert_encoding((string)$results['nickname'], 'utf-16');
            $bin     = bin2hex($content);
            $arr     = str_split($bin, 4);
            $l       = count($arr);
            $str     = '';

            for ($n = 0; $n < $l; $n++) {
                if (isset($arr[$n + 1]) && ('0x' . $arr[$n] >= 0xd800 && '0x' . $arr[$n] <= 0xdbff && '0x' . $arr[$n + 1] >= 0xdc00 && '0x' . $arr[$n + 1] <= 0xdfff)) {
                    $n++;
                } else {
                    $str .= $arr[$n];
                }
            }
            $nickname = mb_convert_encoding(hex2bin($str), 'utf-8', 'utf-16');

            $param['request']->merge(compact('nickname'));
            if (Entity::where('uid', '!=', $param['uid'])
                ->where("nickname", '=', $nickname)->count()
            ) {

                return ['message' => '昵称已存在', 'status' => false];
            }
        }
        $list = array('activity_remind', 'avatar', 'nickname', 'realname', 'gender', 'zone_id', 'sign', 'tel', 'birth', 'diploma', 'earning', 'profession', 'investment_min', 'investment_max', 'invest_intention', 'other_demand');
        $comm = new CommonController();
        $comm->editList($param['request'], new Entity(), 'uid', $results['uid'], $list);

        if ($results['nickname']) {
            $param['request']->merge(['nickname' => $results['nickname']]);
        }

        $avatar = $param['request']->input('avatar');
        if (strpos($avatar, config('app.base_url')) !== false) {
            Entity::where('uid', $results['uid'])->update(array('avatar' => removeDomainStr($avatar)));
        }
        $industry = $param['request']->input('industry');

        if (count($industry)) {
            $user = Entity::getRow(array('uid' => $results['uid']));
            Industry::userFondCates($user->uid, $industry);
        }

        //更新融云用户信息
        $userInfo = User::find($results['uid']);
        GainToken($userInfo['uid'], trim($userInfo['nickname']), trim($userInfo['avatar']), 'user_refresh');
        return ['message' => '修改成功', 'status' => true];
    }

    /**
     *  author zhaoyf
     *
     * c端额外补充字符数据
     *
     * @param $param
     * @return array|string
     * @internal param Categorys|null $cates
     * @internal param Request $request
     * @internal param null $version
     */
    public function postAddNewInfo($param)
    {
        $result = $param['request']->input();

        //判断用户ID是否传递和有效
        if (empty($result['uid']) || !isset($result['uid'])) {
            return ['message' => '缺少用户ID，且只能为整形', 'status' => false];
        }

        //判断用户是否存在
        $user_result = User::find($result['uid']);
        if (!$user_result) {
            return ['message' => '该用户不存在', 'status' => false];
        }

        //根据用户信息获取品牌分类
        $gain_result = UserFondCate::where('uid', $result['uid'])
            ->select(DB::raw('GROUP_CONCAT(cate_id) as cate_id'))->first();

        //判断用户是否有感兴趣的品牌分类
        if (!empty($gain_result->cate_id) || !is_null($gain_result->cate_id)) {
            $confirm_result = $this->_gainCateData($gain_result);
        } else {
            $gain_result = null;
            $confirm_result = $this->_gainCateData();
        }

        if ($gain_result == null || ($user_result->investment_min == '0' &&
                $user_result->investment_max == '0') ||
            $user_result->invest_intention == '0'
        ) {
            $user_data['investment_min'] = $user_result->investment_min;
            $user_data['investment_max'] = $user_result->investment_max;
            $user_data['invest_intention'] = $user_result->invest_intention;
            $user_data['industry'] = empty($confirm_result) ? '' : $confirm_result;
        } else {
            $user_data = null;
        }

        return ['message' => $user_data, 'status' => true];
    }

    /**
     * author zhaoyf
     *
     * 获取分类信息
     *
     * @param null $gain_result
     * @return array
     */
    private function _gainCateData($gain_result = null)
    {
        //获取分类
        $gain_results = DB::table('categorys')
            ->where('pid', 0)
            ->select('id', 'pid', 'name', 'logo')
            ->get();

        foreach ($gain_results as $kes => $vlss) {
            $cate_data[$kes] = (array)$vlss;
        }

        //处理结果，并且返回
        if (is_null($gain_result)) {
            return ['exists_cate_id' => [], 'cates' => $cate_data,];
        } else {
            return ['exists_cate_id' => explode(',', $gain_result['cate_id']), 'cates' => $cate_data,];
        }
    }

    /**
     * 投资人（用户）额外信息 zhaoyf  --数据中心版
     *
     * @param $param
     * @return array
     */
    public function postUserinfoext($param)
    {
        if (!isset($param['uid']) || empty($param['uid']) || !is_numeric($param['uid'])) {
            return ['message' => '缺少用户ID：uid，且只能是整形', 'status' => false];
        }

        $uid = $param['uid'];

        //todo 增加对用户状态的判断条件 zhaoyf 2017-12-26 11:25
        if (!($user = User::where(['uid' => $uid, 'status' => 1])->first())) {
            return ['message' => '非法的uid', 'status' => false];
        }

        $return['currency'] = $user->currency;    //无界币
        $return['score'] = $user->score;          //无界币

        //分享次数
        $share_currency_count = \DB::table('currency_log')
            ->where('uid', $uid)
            ->whereIn('action', ['share_distribution', 'relay_distribution'])
            ->count();

        $share_score_count = \DB::table('score_log')
            ->where('uid', $uid)
            ->whereIn('type', ['share_distribution', 'relay_distribution'])
            ->count();

        $return['share_count'] = $share_currency_count + $share_score_count;

        //阅读量
        $currency_reward_count = \DB::table('currency_log')
            ->where('uid', $uid)
            ->where('action', 'view_distribution')
            ->count();

        $score_reward_count = \DB::table('score_log')
            ->where('uid', $uid)
            ->where('type', 'view_distribution')
            ->count();

        $return['read_count'] = $currency_reward_count + $score_reward_count;

        //意向客户
        $intend_brand_ids = \DB::table('distribution_log as a')
            ->where('a.uid', $uid)
            ->where('a.relation_type', 'brand')
            ->where('a.genus_type', 'intent')
            ->select('a.id')
            ->get();

        $return['intend_count'] = count($intend_brand_ids);

        //累计佣金
        $currency = \DB::table('currency_log')
            ->where('uid', $uid)
            ->where('operation', 1)
            ->whereIn(
                'action',
                [
                    'share_distribution',
                    'relay_distribution',
                    'relay_distribution',
                    'watch_distribution',
                    'enroll_distribution',
                    'sign_distribution',
                    'view_distribution',
                    'intent_distribution'
                ]
            )
            ->sum('num');

        $return['currency_total'] = $currency;

        //是否已填写邀请码 1 :是 0 :否
        $return['is_done_invitecode'] = $user->register_invite ? 1 : 0;
        $unReadCount = Message::unReadcounts($uid);
        $return['unread_messages'] = $unReadCount;

        //有多少人填写了我的邀请码
        $return['invite_count'] = User::where('register_invite', $user->my_invite)->orWhere('register_invite', $user->non_reversible)->count();

        //今天是否签到
        $return['is_sign'] = ScoreLog::typeCount($user->uid, 'user_sign', date('Y-m-d')) ||
        ScoreLog::typeCount($user->uid, 'user_sign_first', date('Y-m-d')) ? 1 : 0;
        if ($user->serial_sign > 0 && !ScoreLog::typeCount($user->uid, 'user_sign', date('Y-m-d', time() - 86400))
            && !ScoreLog::typeCount($user->uid, 'user_sign_first', date('Y-m-d', time() - 86400))
        ) {//未连续
            $user->update(['serial_sign' => 0]);
        }

        //连续签到几次
        $return['serial_sign'] = $user->serial_sign;

        //本次签到赠送
        if ($user->serial_sign >= 30) {
            $return['sign_score'] = 100;
        } else {
            $return['sign_score'] = ScoreLog::typeCount($user->uid, 'user_sign') == 0 ? 10 : min($user->serial_sign * 5 + 5, 150);
        }

        //已经提取佣金
        $return['extracted'] = \App\Models\User\Withdraw::where('uid', $uid)->where('status', '!=', 'fail')
            ->sum(\DB::raw('if(status="pending",money,actual)'));

        //累计邀请好友报名
        $return['invite_sign_count'] = ActivitySign::inviteSign($uid);

        //是否有新的经济人 todo 优化代码：减少对User表的一次查询 zhaoyf 2017-12-26 11:20
        $is_new_agent = Agent::where('non_reversible', $user->register_invite)->count();
        $return['new_agent'] = $is_new_agent ? 1 : 0;

        //是否有处于待确定的活动邀请和考察邀请 new_activity_invite、new_inspect_invite
        $new_activity_invite = Invitation::where('uid', $uid)
            ->where('type', 1)
            ->where('status', 0)
            ->count();
        $return['new_activity_invite'] = $new_activity_invite ? 1 : 0;

        //待确定的考察邀请
        $inspect_invite = Invitation::where('uid', $uid)
            ->where('type', 2)
            ->where('status', 0)
            ->count();
        $return['new_inspect_invite'] = $inspect_invite ? 1 : 0;

        //待确定的签订合同 new_contract
        $new_contract = Contract::where('uid', $uid)
            ->where('status', 0)
            ->count();
        $return['new_contract'] = $new_contract ? 1 : 0;

        //获取用户的最小和最大投资额度和用户的意向度
        if (is_object($user)) {
            $return['investment_min']   = number_format($user->investment_min);
            $return['investment_max']   = number_format($user->investment_max);
            $return['invest_intention'] = $user->invest_intention;
            $return['other_demand']     = preg_replace('/\s*/i', '', strip_tags($user->other_demand));
        }

        //获取用户喜欢的分类
        $cate_id = DB::table('user_fond_cate')
            ->where('uid', $uid)
            ->select(DB::raw('GROUP_CONCAT(cate_id) as cate_id'))
            ->first();

        if ($cate_id) {
            $cate_data = Categorys::whereIn('id', explode(',', $cate_id->cate_id))->select('id', 'name')->get();
            foreach ($cate_data as $key => $val) {
                $return['user_fond_cate'][] = ['id' => $val->id, 'name' => $val->name];
            }
        }

        return ['message' => $return, 'status' => true];
    }

    //我的订单
    public function postMyorders($param)
    {
        $uid = intval($param['uid']);
        $page = $param['request']->input('page', 1);
        $pageSize = $param['request']->input('page_size', 10);
        $isComplete = $param['request']->input('is_complete', 1);
        $version = empty($param['version']) ? '' : trim($param['version']) ;
        $orderList = User::getMyorders($uid, $page, $pageSize, $isComplete ,$version);
        if (isset($orderList['error'])) {
            return ['message' => $orderList['message'], 'status' => false];
        }
        return ['message' => $orderList, 'status' => true];
    }

    //成单品牌
    public function postSuccessBrands($input)
    {
        $uid = intval($input['request']->input('uid'));
        if (empty($uid)) {
            return ['message' => '请输入用户id', 'status' => false];
        }
        $data = Agent::getSuccessBrandList($uid);
        if (isset($data['error'])) {
            return ['message' => $data['message'], 'status' => false];
        }
        return ['message' => $data, 'status' => true];
    }

    /*我的经纪人*/
    public function postFollowedAgents($input)
    {
        $uid = intval($input['request']->input('uid'));
        if (empty($uid)) {
            return ['message' => '请输入用户id', 'status' => false];
        }
        $data = Agent::getFollowedAgents($uid);
        if (isset($data['error'])) {
            return ['message' => $data['message'], 'status' => false];
        }
        return ['message' => $data, 'status' => true];
    }


    //我的活动邀请函列表
    public function postActivityInvites($input)
    {
        $uid = intval($input['request']->input('uid'));
        $type = intval($input['request']->input('type', 2));
        if (empty($uid)) {
            return ['message' => '请输入用户id', 'status' => false];
        }
        $data = Agent::getActivityInvitesList($uid, $type);
        if (isset($data['error'])) {
            return ['message' => $data['message'], 'status' => false];
        }
        return ['message' => $data, 'status' => true];
    }


    /**
     * 我的考察邀请函列表
     *
     * @param $param
     * @return array|string
     *
     * author zhaoyf
     * @internal param Request $request
     * @internal param null $version
     *
     */
    public function postInspectInvites($param)
    {
        $result = $param['request']->input();

        if (empty($result['uid']) || !intval($result['uid'])) {
            return ['message' => '缺少用户ID：uid；且用户ID只能是整形', 'status' => false];
        }

        //获取 并 返回数据结果
        $confirm_result = Agent::instance()->getInspectList($result);

        return ['message' => $confirm_result, 'status' => true];
    }


    public function postFollowedBrands($input)
    {
        $uid = intval($input['request']->input('uid'));
        if (empty($uid)) {
            return ['message' => '请输入用户id', 'status' => false];
        }
        $data = Agent::getFollowedBrandList($uid);
        if (isset($data['error'])) {
            return ['message' => $data['message'], 'status' => false];
        }
        return ['message' => $data, 'status' => true];
    }

    //我的经纪人

    public function postAgents($input)
    {
        $uid = intval($input['request']->input('uid'));
        if (empty($uid)) {
            return ['message' => '请输入用户id', 'status' => false];
        }
        $data = Agent::getAgentsInfo($uid);
        if (isset($data['error'])) {
            return ['message' => $data['message'], 'status' => false];
        }
        return ['message' => $data, 'status' => true];
    }


    /**
     * 我的邀请人  --数据中心版
     * @User yaokai
     * @param $input
     * @return array
     */
    public function postMyInviter($input)
    {
        $uid = intval($input['request']->input('uid'));
        if (empty($uid)) {
            return ['message' => '请输入用户id', 'status' => false];
        }
        $data = User::getMyInviterInfo($uid);
        if (isset($data['error'])) {
            return ['message' => $data['message'], 'status' => false];
        }
        return ['message' => $data, 'status' => true];
    }


    /*********** 经纪人 ************/

    /**
     * 经纪人详情
     *
     * @param $param
     * @internal param 投资人ID $customer_id
     * @internal param 被查看的经纪人ID $agent_id
     *
     * @return data_list|array
     */
    public function postDetails($param)
    {
        $result = $param['request']->input();

        //根据当前登录的投资人ID和传递的经纪人ID从agent_customer表里获取一条数据信息
        //同时获取被查看经纪人的基本信息（成单数，等级等...）
        $agent_result = AgentCustomer::with('belongsToAgent.hasOneAgentLevel')
            ->where('agent_id',    $result['agent_id'])
            ->where('uid',         $result['customer_id'])
            ->where('level', '<>', self::LOST_ID_TYPE)
            ->where('status','<>', self::LOST_ID_TYPE)
            ->first();

        //$agent_confirm_brand:已经代理的品牌
        //$agent_undetermined_brand：申请代理中的品牌
        $agent_confirm_brand      = $this->_passIDGainInfo($result, self::CONFIRM_AGENCY);
        $agent_undetermined_brand = $this->_gainCustomerAndAgentBrandInfo($result);

        //对经纪人代理的品牌进行处理
        if ($agent_confirm_brand) {
            $brand_data = array_map(function ($result) {
                $brand['id'] = $result->brand_id;
                $brand['name'] = Brand::where('id', $result->brand_id)->first()->name;
                $brand['logo'] = getImage(Brand::where('id', $result->brand_id)->first()->logo, '', '');
                $brand['category_name']  = $result->name;
                $brand['investment_min'] = number_format($result->investment_min);
                $brand['investment_max'] = number_format($result->investment_max);

                return $brand;
            }, $agent_confirm_brand);
        } else {
            $brand_data = [];
        }

        //对经纪人和投资人存在的派单品牌进行处理
        if ($agent_undetermined_brand) {
            $brand_undetermined_data['id']    = $agent_undetermined_brand['brand']['id'];
            $brand_undetermined_data['name']  = $agent_undetermined_brand['brand']['name'];
            $brand_undetermined_data['logo']  = getImage($agent_undetermined_brand['brand']['logo'], '', '');
            $brand_undetermined_data['category_name']  = $agent_undetermined_brand['brand']['categorys1']['name'];
            $brand_undetermined_data['investment_min'] = number_format($agent_undetermined_brand['brand']['investment_min']);
            $brand_undetermined_data['investment_max'] = number_format($agent_undetermined_brand['brand']['investment_max']);
        } else {
            $brand_undetermined_data = [];
        }

        //组合数据 获取品牌个数，用户昵称，头像，性别，地区等...
        if ($agent_result) {
            $agent['is_public_realname'] = $agent_result->belongsToAgent->is_public_realname ?: '';
            $agent['realname']  = $agent_result->belongsToAgent->realname ?: '';
            $agent['nickname']  = $agent_result->belongsToAgent->nickname ?: '';
            $agent['avatar']    = getImage($agent_result->belongsToAgent->avatar, 'avatar', '');
            $agent['level_num'] = $agent_result->belongsToAgent->agent_level_id ?: '';
            $agent['level']     = !empty($agent_result->belongsToAgent->hasOneAgentLevel) ? $agent_result->belongsToAgent->hasOneAgentLevel->name : '';
            $agent['gender']    = AgentScore::$AgentGender[$agent_result->belongsToAgent->gender];
            $agent['sign']      = !empty($agent_result->belongsToAgent->sign) ? $agent_result->belongsToAgent->sign : '为你提供最佳的加盟服务';
            $agent['username']  = $agent_result->belongsToAgent->non_reversible ?  Agent::getRealPhone($agent_result->belongsToAgent->non_reversible,'agent') : '';
            $agent['register_invite'] = $agent_result->belongsToAgent->register_invite ?: '';
            $agent['zone']      = Zone::pidNames([$agent_result->belongsToAgent->zone_id]) ?: '';
            $agent['tags'][]    = getTime($agent_result->belongsToAgent->identity_card);
            $agent['tags'][]    = !empty($agent_result->belongsToAgent->identity_card) ? getStarsign($agent_result->belongsToAgent->identity_card) : '';
        } else {
            $agent = [];
        }

        //获取用户感兴趣的行业
        //存在行业ID就获取用户感兴趣的行业
        $industry_id = DB::table('agent_category')
            ->where('agent_id', $result['agent_id'])
            ->select(DB::raw('GROUP_CONCAT(category_id) as category_id'))
            ->first();

        //获取经纪人感兴趣的行业分类
        if ($industry_id) {
            $like_industry = Categorys::whereIn('id', explode(',', $industry_id->category_id))
                ->select('id', 'name')
                ->orderByRaw('RAND()')
                ->limit(3)->get();

            foreach ($like_industry as $key => $vs) {
                $agent['tags'][] = $vs->name;
            }
        }

        //获取投资人和经纪人的相互关系

        //邀请关系
        $invite_relation = User::where('uid', $result['customer_id'])
            ->where('register_invite', $agent_result['non_reversible'])
            ->where('status', '<>', self::LOST_ID_TYPE)
            ->count();

        //跟单关系
        $documentary_relation = AgentCustomer::where('agent_id', $result['agent_id'])
            ->where('uid', $result['customer_id'])
            ->where('level', '<>', self::LOST_ID_TYPE)
            ->where('status','<>', self::LOST_ID_TYPE)
            ->first();

        //处理关系
        if ($documentary_relation && !empty($documentary_relation->source)) {
            $relation_data = AgentCustomer::$relation[$documentary_relation->source];
            if ($documentary_relation->source == self::RELATION_ID_1 ||
                $documentary_relation->source == self::RELATION_ID_2 ||
                $documentary_relation->source == self::RELATION_ID_3 ||
                $documentary_relation->source == self::RELATION_ID_4 ||
                $invite_relation
            ) {
                $relation = 1;  //邀请关系
            } elseif ($documentary_relation->source == self::RELATION_ID_5) {
                $relation = 2;  //派单关系
            } elseif ($documentary_relation->source == self::RELATION_ID_6) {
                $relation = 3;  //邀请和派单关系
            } elseif ($documentary_relation->source == self::RELATION_ID_7) {
                $relation = 4;  //派单和邀请关系
            } elseif ($documentary_relation->source == self::RELATION_ID_8) {
                $relation = 5;  //推荐关系
            } elseif ($documentary_relation->source == self::RELATION_ID_9) {
                $relation = 6;  //添加好友关系
            } else {
                $relation = 0;  //无关系
            }
        } else {
            $relation = 0;
            $relation_data = '无关系';
        }

        //获取投资人对经纪人ID评价
        $agent_score = AgentScore::where('agent_id', $result['agent_id'])
            ->where('status', '<>', '-1')
            ->select('service_score', 'ability_score', 'timely_score')
            ->get();

        //获取总评分人数
        $score_count = AgentScore::where('agent_id', $result['agent_id'])
            ->where('status', '<>', '-1')->count();

        $service_score_num = 0;    //总的服务态度得分
        $service_score_customer_num = 0;    //服务态度评分人数
        $ability_score_num = 0;    //总的专业能力得分
        $ability_score_customer_num = 0;    //专业能力评分人数
        $timely_score_num = 0;    //总的响应及时得分
        $timely_score_customer_num = 0;    //总响应及时评分人数

        if ($agent_score) {
            foreach ($agent_score as $keys => $vls) {
                $service_score_num += $vls->service_score;
                $ability_score_num += $vls->ability_score;
                $timely_score_num += $vls->timely_score;

                ++$service_score_customer_num;
                ++$ability_score_customer_num;
                ++$timely_score_customer_num;
            }

            //计算每个评分的平均分； 没有时默认为：0 ;
            if (!empty($service_score_num) && !empty($service_score_customer_num)) {
                $service_confirm_score = round($service_score_num / $service_score_customer_num);
            } else {
                $service_confirm_score = 0;
            }
            if (!empty($ability_score_num) && !empty($ability_score_customer_num)) {
                $ability_confirm_score = round($ability_score_num / $ability_score_customer_num);
            } else {
                $ability_confirm_score = 0;
            }
            if (!empty($timely_score_num) && !empty($timely_score_customer_num)) {
                $timely_confirm_score = round($timely_score_num / $timely_score_customer_num);
            } else {
                $timely_confirm_score = 0;
            }

            //获取总的评分和总的评分人数
            $gather_score__num = $service_score_num + $ability_score_num + $timely_score_num;
            $gather_score_customer_num = $service_score_customer_num + $ability_score_customer_num + $timely_score_customer_num;

            //最后的平均得分,如何没有默认为：0 ;
            if ($gather_score__num > 0 && $gather_score_customer_num > 0) {
                $overall_confirm_score = round($gather_score__num / $gather_score_customer_num);
            } else {
                $overall_confirm_score = 0;
            }
        }

        //评分数处理
        $score['service_score'] = !empty($service_confirm_score) ? $service_confirm_score : 0;
        $score['ability_score'] = !empty($ability_confirm_score) ? $ability_confirm_score : 0;
        $score['timely_score']  = !empty($timely_confirm_score) ? $timely_confirm_score : 0;
        $score['overall_score'] = !empty($overall_confirm_score) ? $overall_confirm_score : 0;

        //对最后的综合数据进行处理
        $data['agent']          = $agent ? $agent : [];
        $data['brands_count']   = count($agent_confirm_brand) ? count($agent_confirm_brand) : 0; //代理品牌个数(有显示实际的个数，没有就是零)
        $data['brands']         = !empty($brand_data) ? $brand_data : [];
        $data['brand_undetermined_data'] = !empty($brand_undetermined_data) ? $brand_undetermined_data : [];
        $data['score']            = !empty($score) ? $score : [];
        $data['score_count']      = !empty($score_count) ? $score_count : 0;
        $data['confirm_relation'] = $relation_data;
        $data['relation_id']      = $relation;

        return ['message' => $data, 'status' => true];
    }

    /**
     * author zhaoyf
     *
     * 根据传递的ID获取值
     *
     * @param $result
     * @param $id
     * @return
     */
    private function _passIDGainInfo($result, $id)
    {
        //获取经纪人代理的品牌
        $agent_brand = DB::table('agent_brand')
            ->leftJoin('brand', 'brand.id', '=', 'agent_brand.brand_id')
            ->leftJoin('categorys', 'categorys.id', '=', 'brand.categorys1_id')
            ->where('agent_id', $result['agent_id'])
            ->where(function ($query) use ($id) {
                if (is_array($id)) {
                    $query->whereIn('agent_brand.status', $id);
                } else {
                    $query->where('agent_brand.status', $id);
                }
            })
            ->get();

        return $agent_brand;
    }

    /**
     * author zhaoyf
     *
     * 获取投资人和经纪人存在的派单品牌数据
     *
     * @param $result
     * @return \Illuminate\Database\Eloquent\Collection|null|static[]
     */
    private function _gainCustomerAndAgentBrandInfo($result)
    {
        $gain_result = AgentCustomer::with('brand.categorys1')
            ->where('agent_id', $result['agent_id'])
            ->where('uid', $result['customer_id'])
            ->where('status', 0)
            ->where('brand_id', '<>', 0)
            ->whereIn('source', [1, 2, 3, 4, 5, 6])
            ->first();

        return $gain_result ? $gain_result : null;
    }

    /**
     * 我的基金列表
     *
     * @param $param
     * @return array
     * @author tangjb
     */
    public function postMyfundlist($param)
    {
        $contract = Contract::where('id', $param['contract_id'])->first();

        $query = Fund::with(['brand' => function ($query) {
            $query->select('name', 'id');
        }])->where('uid', $param['uid'])
            ->where('created_at', '>=', time() - (180 * 24 * 3600))
            ->where('status', 'unused');

        $query_clone = clone  $query;

        $funds = $query
            ->where('brand_id', '<>', $contract->brand_id)
            ->orderBy('created_at', 'desc');


        $funds = $query_clone
            ->where('brand_id', $contract->brand_id)
            ->union($funds)
            ->get();


        $data = [];
        foreach ($funds as $k => $v) {
            $title = $v['brand']['name'];
            $id = $v['id'];
            $code = $v['code'];
            $expire_time = date('Y-m-d H:i', $v['created_at'] + 180 * 24 * 3600);
            $fund = $v['fund'];
            if ($contract->brand_id == $v['brand_id']) {
                $status = self::FUND_VALID;
            } else {
                $status = self::FUND_INVALID;
            }

            $data[] = compact('title', 'code', 'expire_time', 'fund', 'status', 'id');
        }


        return ['message' => $data, 'status' => true];
    }


    /**
     * 客户评价完经纪人领取积分
     *
     * @param $param
     * @return string
     * @author tangjb
     */
    public function postFetchScore($param)
    {
        $exist = ScoreLog::where('type', 'comment_agent')->where('relation_id', $param['comment_id'])->first();


        if ($exist) {
            return ['message' => '领取失败，之前已领取过', 'status' => false];
        }

        $res = ScoreLog::add($param['uid'], 500, 'comment_agent', '评价经纪人，领取积分', 1, false, 'agent_score', $param['comment_id']);

        if ($res) {
            return ['message' => '领取成功', 'status' => true];
        } else {
            return ['message' => '领取失败', 'status' => false];
        }
    }


    /**
     * 考察邀请函支付成功
     *
     * @param $param
     * @return string
     * @author tangjb
     */
    public function postInspectPaySuccess($param)
    {
        if (!isset($param['order_no'])) {
            return ['message' => '数据异常', 'status' => false];
        }


        $order = Orders::with('hasOneOrdersItems')->where('order_no', $param['order_no'])->first();


        if ($order->hasOneOrdersItems->type != 'inspect_invite') {
            return ['message' => '数据异常', 'status' => false];
        }

        if ($order->status != 'pay') {
            return ['message' => '此单未支付成功', 'status' => false];
        }

        $invitation = Invitation::with('hasOneStore.hasOneBrand', 'belongsToAgent', 'hasOneStore.hasOneZone')
            ->where('id', $order->hasOneOrdersItems->product_id)->first();


        //如果公开真实姓名
        if($invitation->belongsToAgent->is_public_realname && $invitation->belongsToAgent->realname){
            $realname = $invitation->belongsToAgent->realname;
        }else{
            $realname = $invitation->belongsToAgent->nickname;
        }



        $data = [
            'brand_title' => $invitation->hasOneStore->hasOneBrand->name,
            'realname' => $realname,
            'store_name' => $invitation->hasOneStore->name,
            'address' => $invitation->hasOneStore->address,
            'zone' => Zone::getCityAndProvince($invitation->hasOneStore->zone_id),
            'amount' => numFormatWithComma(abandonZero($order->amount)),
            'pay_at' => date('Y年m月d日 H:i', $order->pay_at),
            'order_no' => $order->order_no,
            'pay_way' => Orders::$_PAYWAY[$order->pay_way],
            'inspect_time' => $invitation->inspect_time,

        ];


        return ['message' => $data, 'status' => true];
    }


    /**
     * 等待经纪人接单
     *
     * @param $param
     * @return string
     * @author tangjb
     */
    public function postWaitAccept($param)
    {
        //判断是否在保护期内
        //判断是否在保护期内
        $exist = AgentCustomer::with('belongsToAgent')->whereIn('source', [1,6])->where('uid', $param['uid'])
            ->where('protect_time', '>=', time())->first();

        $in_protect = 0;
        // 查看是否在保护
        if ($exist) {
            $in_protect = 1;
            //新增一条对接记录
            $log = AgentCustomerLog::where([
                'agent_customer_id' => $exist->id,
                'action' => 1,
                'post_id' => 0,
                'brand_id' => $param['brand_id'],
                'agent_id' => $exist->agent_id,
                'uid' => $param['uid'],
            ])->first();

            //没有就创建
            if (!$log) {
                $log = AgentCustomerLog::create([
                    'agent_customer_id' => $exist->id,
                    'action' => 1,
                    'post_id' => 0,
                    'brand_id' => $param['brand_id'],
                    'agent_id' => $exist->agent_id,
                    'uid' => $param['uid'],
                    'created_at' => time()
                ])->first();
            }

            isset($exist->belongsToAgent->nickname) ? $nickname = $exist->belongsToAgent->nickname : $nickname = $exist->belongsToAgent->realname;
            return ['message' => ['in_protect' => $in_protect,
                'agent_name' => $nickname,
                'agent_id' => $exist->belongsToAgent->id], 'status' => true];
        }

        //查看是否已经形成派单关系
        $is_accept = AgentCustomerLog::with('agent')
            ->whereHas('agent_customer', function ($query) {
                $query->where('status', '>', -1);
            })
            ->where('action', 0)
            ->where('uid', $param['uid'])
            ->where('brand_id', $param['brand_id'])
            ->orderBy('id', 'desc')
            ->first();


        if ($is_accept) {
            $in_protect = 1;

            return ['message' => ['in_protect' => $in_protect,
                'agent_name' => $is_accept->agent->nickname,
                'agent_id' => $is_accept->agent->id], 'status' => true];
        }


        //获取所有经纪人
        $agentlists = Agent::with('hasOneZone')
            ->whereIn('id', function ($query) use ($param) {
                $query->from('agent_brand')->where('status', 4)->where('brand_id', $param['brand_id'])->lists('agent_id');
            })
            ->select('id', 'avatar', 'zone_id')->where('is_online', 1)->get();

        //如果为空，那么就派给自己的经纪人
        if(!count($agentlists)){
            $agentlists = Agent::with('hasOneZone')
                ->where('account_type', 3)
                ->select('id', 'avatar', 'zone_id')->where('is_online', 1)->get();
        }



        $user = User::where('uid', $param['uid'])->first();



        foreach ($agentlists as $k => $v) {
            $sort = 0;
            //就近原则
            if ($v->zone_id == $user->zone_id) {
                $sort += 100;
            }
            //闲忙原则
            $counts = AgentCustomer::where('agent_id', $v->id)->where('uid', $param['uid'])
                ->where('created_at', '>', strtotime(date('Y-m-d')))->count();
            $sort += -$counts;
            //成交原则 成单率
            $success = Contract::where('status', 1)->where('brand_id', $param['brand_id'])->where('agent_id', $v->id)->count();
            $all = Contract::where('brand_id', $param['brand_id'])->where('agent_id', $v->id)->count();
            $rate = $all == 0 ? 0 : $success / $all;
            $sort += $rate;
            $v->sort = $sort;
            $v->avatar = getImage($v->avatar, 'avatar', '');
        }

        $count = count($agentlists);

        $agents = $agentlists->take(10);

        $agents = $agents->sortByDesc('sort');
        $brand = Brand::where('id', $param['brand_id'])->select('name', 'logo')->first();
        $title = $brand->name;
        $logo = getImage($brand->logo, 'activity', '');

        //判断是否已经派过单
        $user_accept = SendInvestor::where('brand_id',$param['brand_id'])
            ->where('uid',$param['uid'])
            ->where('status','0')
            ->value('id');

        //已经派过单的不再派单
        if ($user_accept){
            $data = compact('title', 'logo','user_accept', 'in_protect', 'is_accept_order');
            return ['message' => $data, 'status' => true];
        }



        $send_investor_id = BrandV020800::instances()->setSendQueue($param['brand_id'], $param['uid']);

        $data = compact('agents', 'count', 'title', 'logo', 'send_investor_id', 'in_protect');

        return ['message' => $data, 'status' => true];
    }


    /**
     * 被邀请人填写邀请码  --数据中心版
     *
     * @param $param
     * @return array
     * @author tangjb
     */
    public function postWritecode($param)
    {
        if (empty($param['_uid'])) {
            return ['message' => 'uid必填', 'status' => false];
        }

        if (!($user = User::where('uid', $param['_uid'])->first())) {
            return ['message' => '非法的uid', 'status' => false];
        }

        $request = $param['request'];
        $invite_code = trim($request->input('invite_code', ''));

        //md5加盐后的邀请号码
        $inviter_tel = encryptTel($invite_code);


        if (empty($invite_code)) {
            return ['message' => '邀请码不能为空', 'status' => false];
        }

        if (!preg_match("/(^1[34578]\d{9}$)|(^(\d){6}$)|(^(\d){8}$)/", $invite_code)) {
            return ['message' => '邀请码为不合法的手机号', 'status' => false];
        }

        if ($inviter_tel == $user->non_reversible) {
            return ['message' => '邀请码不能为自己手机号', 'status' => false];
        }


        $invite_user = User::where('non_reversible', $inviter_tel)->orWhere('my_invite', $invite_code)->first();

        //双轨制
        $agent = Agent::where(function ($query) use($inviter_tel,$invite_code){
            return $query->where('non_reversible',$inviter_tel)->orWhere('my_invite',$invite_code);
        })->where('status', 1)->first();


        if (!$invite_user && !$agent) {
            return ['message' => '邀请码有误，请核对', 'status' => false];
        }

        if(isset($agent->non_reversible) && $agent->non_reversible == $user->non_reversible){
            return ['message' => '自己不能是自己的邀请经纪人', 'status' => false];
        }



        if(isset($invite_user->my_invite) && $invite_user->non_reversible == $user->non_reversible){
            return ['message' => '自己不能是自己的邀请人', 'status' => false];
        }


        if ($user->register_invite) {
            return ['message' => '已经输入过邀请码', 'status' => false];
        }



        //那就强制把邀请码改为手机号，减轻双轨制的复杂度
        if($agent){
            $inviter_tel = $agent->non_reversible;
        }




        //写入数据
        $update = [
            'register_invite' => $inviter_tel
        ];

        $res = User::where('uid', $param['_uid'])->update($update);


        if ($agent) {
            $agentCustomer = AgentCustomer::where('agent_id', $agent->id)->where('uid', $param['_uid'])->first();

            if(!$agentCustomer){
                $agentCustomer = AgentCustomer::create([
                    'agent_id' => $agent->id,
                    'uid' => $param['_uid'],
                    'protect_time' => time() + 30 * 24 * 3600,
                    'source' => 1,
                    'brand_id' => 0,
                    'has_tel' => 1,
                ]);
            }


            AgentCustomerLog::create(
                [
                    'agent_customer_id' => $agentCustomer->id,
                    'action' => 2,
                    'post_id' => 0,
                    'brand_id' => 0,
                    'agent_id' => $agent->id,
                    'uid' => $param['_uid'],
                    'created_at' => time(),
                ]
            );


            AgentCustomerLog::create(
                [
                    'agent_customer_id' => $agentCustomer->id,
                    'action' => 14,
                    'post_id' => 0,
                    'brand_id' => 0,
                    'agent_id' => $agent->id,
                    'uid' => $param['_uid'],
                    'created_at' => time(),
                ]
            );


            //给经纪人发
            $res = send_transmission(json_encode(['type' => 'bind', 'style' => 'json', 'value' => ['username' => $agent->username, 'id' => $user->uid, 'realname' => $user->realname, 'nickname' => $user->nickname]]), $agent, null, 1);

            //给C端用户发
            $res = send_transmission(json_encode(['type' => 'bind', 'style' => 'json', 'value' => ['agent_id' => $agent->id]]), $user);

            $agent = Agent::with('pAgent')->where('id', $agent->id)->first();

            return ['message' => "已成功匹配经纪人{$agent->realname}", 'status' => true];
        }


        $name = $invite_user->realname ?: ($invite_user->nickname ?: '');
        //邀请人获得100积分
        if ($invitor = User::where('non_reversible', $inviter_tel)->first()) {
            //系统消息
            createMessage(
                $invitor->uid,
                $title = '获得了100积分',
                $content = "恭喜你,($name) 填写了你的邀请码并注册了账号,100积分已到账户中,打开app查看我的积分",
                $ext = '',
                $end = '<p>如有疑问，请致电服务热线<span>400-011-0061</span></p>',
                $type = 1,
                $delay = 300
            );

//            $content_sms = trans('sms.invite_score',['name'=>$name]);

            //给邀请人赠送积分
            ScoreLog::add($invitor->uid, 100, 'invite_register', '邀请用户注册');

            //短信
            @SendTemplateSMS('invite_score', $invitor->non_reversible, 'invite', ['name' => $name], $invitor->nation_code);
        }



        if ($res !== false) {
            return ['message' => "已成功匹配{$name}", 'status' => true];
        }

        return ['message' => '操作失败', 'status' => false];
    }

    /**
     * 我的全部经纪人   --数据中心版  没有影响 暂不处理
     * @User shiqy
     * @param $input
     * @return array
     */
    public function postMyAllAgents($input)
    {
        $validator = \Validator::make($input, [
            'uid' => 'required|exists:user,uid',
        ]);
        if ($validator->fails()) {
            $warnings = $validator->messages();
            $show_warning = $warnings->first();
            return ['message' => $show_warning, 'status' => false];
        }
        $data = Agent::getMyAllAgents(intval($input['uid']));
        if(isset($data['error'])){
            return ['message' => $data['message'], 'status' => false];
        }
        return ['message' => $data, 'status' => true];
    }


}