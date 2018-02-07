<?php namespace App\Models\Agent;

use App\Models\Brand\Entity as Brand;
use App\Models\Orders\Items;
use App\Models\Orders\Order;
use Illuminate\Database\Eloquent\Model;
use App\Models\User\Entity as User;
use App\Models\Zone\Entity as Zone;
use App\Models\Contract\Contract;
use App\Models\Activity\Sign;
use App\Models\Agent\Invitation;
use App\Models\Agent\Agent;
use App\Models\Orders\Entity as Orders;
use Illuminate\Support\Facades\DB;

class AgentCustomer extends Model
{
    const INSPECT_TYPE        = 2;     //考察邀请函类型
    const ALL_MONEY_TYPE      = 2;     //付完所有款项的状态类型
    const REG_INVITE_TYPE     = 1;     //邀请注册状态
    const CONFIRM_AGENCY_TYPE = 4;     //已经代理品牌状态
    const PAI_TYPE            = 5;     //派单状态
    const RECOMMENT_TYPE      = 8;     //推荐投资人状态
    const ACCOUNT_TYPE        = 3;     //内部经纪人
    protected $table      = 'agent_customer';
    protected $dateFormat = 'U';


    //黑名单
    protected $guarded = [];
    private  static  $instance = null;
    public static function instance()
    {
        if(is_null(self::$instance)) {
            self::$instance = new self;
        }

        return self::$instance;
    }

    /**
     * 关联：客户跟进记录
     *
     */
    public function hasManyAgentCustomerLog()
    {
        return $this->hasMany(AgentCustomerLog::class, 'agent_customer_id', 'uid');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     *  关联经纪人客户日记表里的uid
     */
    public function hasManyAgentCustomerLogUid()
    {
        return $this->hasMany(AgentCustomerLog::class, 'uid', 'uid');
    }

    /*
     * 关联经纪人客户日志
     * */

    public function hasManyAgentCustomerLogs(){
        return $this->hasMany(AgentCustomerLog::class, 'agent_customer_id', 'id');
    }

    //关联客户信息一对一
    public function user()
    {
        return $this->hasOne(User::class, 'uid', 'uid');
    }

    //关联品牌信息
    public function brand()
    {
        return $this->hasOne(Brand::class, 'id', 'brand_id');
    }

    public function contract()
    {
        return $this->hasOne(Contract::class, 'id', 'contract_id');
    }



    /*
     * 关联合同
     * */
    public function hasManyContract(){
        $data=$this->hasMany(Contract::class,'agent_id','agent_id');
        return $data;
    }

    /*
     * 关联合同1
     * */
    public function contracts(){
        return $this->hasMany(Contract::class , 'uid','uid');
    }

    /*
     * 关联客户签到
     * */
    public function hasManyActivitySign(){
        return $this->hasMany(Sign::class,'uid','uid');
    }

    /*
     * 关联邀请表
     * */
    public function hasManyInvitation(){
        return $this->hasMany(Invitation::class,'agent_id','agent_id');
    }

    /*
     * 用uid关联邀请函
     * */
    public function invitation(){
        return $this->hasMany(Invitation::class,'uid' , 'uid');
    }

    /*
     * 关联经纪人
     * */
    public function belongsToAgent(){
        return $this->belongsTo(Agent::class,'agent_id','id');
    }


    /**
     * 客户等级
     */
    public static $customerLevel = [
        '-1'  => '遗失客户',
        '1'   => '普通客户',
        '2'   => '主要客户',
        '3'   => '关键客户'
    ];

    /**
     * 经纪人和投资人的关联关系
     *
     * 1：主动导入，
     * 2:活动邀请，
     * 3:直播邀请，
     * 4:其他邀请方式,
     * 5:派单获得，
     * 6：先邀请后派单获得'
     */
    public static $relation = [
        '0' => '无关系',
        '1' => '主动导入',
        '2' => '活动邀请',
        '3' => '直播邀请',
        '4' => '其他邀请方式',
        '5' => '派单客户',
        '6' => '邀请客户、跟单客户',
        '7' => '跟单客户、邀请客户',
        '8' => '推荐客户',
        '9' => '添加好友'
    ];

    /**
     * 邀请状态
     *
     * -2：过期
     * -1：拒绝
     * 0：待确认
     * 1：接受
     */
    public static $InviteStatus = [
        '-2' => '已过期',
        '-1' => '已拒绝',
        '0'  => '待确认',
        '1'  => '已接受',
    ];

    /**
     * 根据经纪人id对应品牌id获取的相应客户信息
     * @param $agent_id 经纪人id
     * @param $brand_id 品牌id
     */
    public static function CustomerInfo($agent_id, $brand_id)
    {
        //找出跟进的经纪人客户id
//        $result = AgentCustomerLog::where('agent_id', $agent_id)
//            ->where('brand_id', $brand_id)
//            ->where('action', '>=', '0')
//            ->groupBy('agent_customer_id')
//            ->lists('uid')->toArray();

//        $agent_customer_ids = array_unique($result);

//        dd($result,$agent_customer_ids);

        //找出对应的客户信息
//        $info = self::with(
//            ['user' => function ($query) {
//                $query->select('uid', 'nickname', 'avatar', 'gender', 'zone_id');
//            },
//            'user.zone' => function ($query) {
//                $query->select('id', 'name');
//            }])
//            ->select('uid', 'status', 'created_at')
//            ->whereIn('id', $agent_customer_ids)
//            ->where('agent_id', $agent_id)
//            ->where('status', '>=', '0')
//            ->get();
        $info = AgentCustomerLog::with(
            ['user' => function ($query) {
                $query->select('uid', 'nickname', 'avatar', 'gender', 'zone_id');
            },
            'user.zone' => function ($query) {
                $query->select('id', 'name');
            },
             'agent_customer' =>function($query){
                $query->select('id','source','created_at');
            }])
            ->select(DB::Raw('MAX(action) as action,uid,agent_customer_id,post_id'))
            ->where('brand_id', $brand_id)
            ->where('agent_id', $agent_id)
            ->where('action', '>=', '0')
            ->groupBy('agent_customer_id')
            ->get();

//        dd($info->toArray());
        $following_customers = [];//跟进中用户信息
        $success_customers = [];//成单的客户信息
        foreach ($info as $k => $v) {
            //跟进中用户信息
            if ($v->action != '13') {
                $following_customers[$k]['uid'] = $v->user->uid;
                $following_customers[$k]['nickname'] = $v->user->nickname;
                $following_customers[$k]['source'] = $v->agent_customer->source; //客户来源
                $following_customers[$k]['avatar'] = getImage($v->user->avatar);
                $following_customers[$k]['gender'] = User::getGender($v->user->gender);
                $following_customers[$k]['begin_time'] = strtotime($v->agent_customer->created_at);
                $following_customers[$k]['followed_days'] = self::followDays(strtotime($v->agent_customer->created_at));
                //地区处理
                $zone2_name = str_replace('市', '', $v->user->zone->name);
                $zone1_name = str_replace('省', '', Zone::pidName($v->user->zone_id));
                $following_customers[$k]['city'] = $zone1_name . ' ' . $zone2_name;
                //成单的客户信息
            } else {
                $success_customers[$k]['uid'] = $v->user->uid;
                $success_customers[$k]['nickname'] = $v->user->nickname;
                $success_customers[$k]['source'] = $v->agent_customer->source; //客户来源
                $success_customers[$k]['avatar'] = getImage($v->user->avatar);
                $success_customers[$k]['gender'] = User::getGender($v->user->gender);
                $success_customers[$k]['begin_time'] = strtotime($v->agent_customer->created_at);
                $success_customers[$k]['followed_days'] = self::followDays(strtotime($v->agent_customer->created_at));
                //地区处理
                $zone2_name = str_replace('市', '', $v->user->zone->name);
                $zone1_name = str_replace('省', '', Zone::pidName($v->user->zone_id));
                $success_customers[$k]['city'] = $zone1_name . ' ' . $zone2_name;
                //组合数据（事件发生的时间点）
                $success_customers[$k]['event'] = self::event($agent_id,$brand_id,$v->user->uid);
                //相关合同信息
//                $success_customers[$k]['address'] = $v->contracts->address;//合同地址
//                $success_customers[$k]['contract_id'] = $v->contracts->id;//合同id
                $contract_info = Contract::contractInfo($agent_id,$brand_id,$v->user->uid,'2');
                $success_customers[$k]['contract_id'] = $contract_info?$contract_info->id:'';//合同id
                $success_customers[$k]['address'] = $contract_info?$contract_info->address:'';//合同地址
            }

        }
        return compact('following_customers', 'success_customers');

    }

    /**
     * 品牌跟进客户相关事件
     * @User yaokai
     * @param $agent_id 经纪人id
     * @param $brand_id 品牌id
     * @param $uid  用户id
     * @return array
     */
    public static function event($agent_id,$brand_id,$uid)
    {
        $log = AgentCustomerLog::where('agent_id', $agent_id)
            ->where('brand_id', $brand_id)
            ->where('uid', $uid)
//            ->orderBy('action' , 'desc')
            ->get();

        if ($log) {
            foreach ($log as $k => $v) {
                switch ($v->action){
                    case 1://1对接品牌
                        $data[$k]['event_time'] = $v->created_at;
                        $data[$k]['schedule'] = '1';
                        break;
                    case 2://2获得联系方式
                        $data[$k]['event_time'] = $v->created_at;
                        $data[$k]['schedule'] = '2';
                        break;
                    case 5://5接受参加发布会
                        $data[$k]['event_time'] = $v->created_at;
                        $data[$k]['schedule'] = '3';
                        break;
                    case 8://8接受考察
                        $data[$k]['event_time'] = $v->created_at;
                        $data[$k]['schedule'] = '4';
                        break;
                    case 11://11签订合同
                        $data[$k]['event_time'] = $v->created_at;
                        $data[$k]['schedule'] = '5';
                        //判断有没有付尾款，如果已付点亮这个事件点
                       /* $contract = Contract::where('brand_id', $brand_id)
                            ->where('agent_id',$agent_id)
                            ->where('uid',$uid)
                            ->where('status', 2)
                            ->value('id');*/
                        break;
                    case 13: //付过尾款
                        $data[$k]['event_time'] = $v->created_at;
                        $data[$k]['schedule'] = '6';
                        break;

                    default:
                        break;
                }
            }
        }

        return $data;
    }

    /**
     * 根据经纪人id对应品牌id获取的相应客户信息
     * @param $agent_id 经纪人id
     * @param $contrat_id 品牌合同模板id
     */
    public static function ContractInfo($agent_id, $contrat_id)
    {
        //找出品牌id
        $brand = BrandContract::with(['hasOneBrand' => function($query){
            $query->select('id','name');
        }])->where('id',$contrat_id)->first();

        //找出跟进的经纪人客户id
        $result = AgentCustomerLog::where('agent_id', $agent_id)
            ->where('brand_id', $brand->brand_id)
            ->where('action', '>=', '0')
            ->lists('agent_customer_id')->toArray();
        $agent_customer_ids = array_unique($result);
//dd($agent_customer_ids);
        //找出对应的客户信息
        $info = self::with(
            ['user' => function ($query) {
                $query->select('uid', 'nickname', 'avatar', 'gender', 'zone_id');
            }],
            ['user.zone' => function ($query) {
                $query->select('id', 'name');
            }])
            ->select('uid', 'status', 'created_at')
            ->whereIn('id', $agent_customer_ids)
            ->where('agent_id', $agent_id)
            ->where('status', '>=', '0')
            ->orderBy('created_at','desc')
            ->get();

        $following_customers = [];
        $success_customers = [];
        foreach ($info as $k => $v) {
            //跟进中用户信息
            if ($v->status == '0') {
                $following_customers[$k]['uid'] = $v->user->uid;
                $following_customers[$k]['nickname'] = $v->user->nickname;
                $following_customers[$k]['avatar'] = getImage($v->user->avatar);
                $following_customers[$k]['gender'] = User::getGender($v->user->gender);
                $following_customers[$k]['begin_time'] = strtotime($v->created_at);
                $following_customers[$k]['followed_days'] = self::followDays(strtotime($v->created_at));
                //地区处理
                $zone2_name = str_replace('市', '', $v->user->zone->name);
                $zone1_name = str_replace('省', '', Zone::pidName($v->user->zone_id));
                $following_customers[$k]['city'] = $zone1_name . ' ' . $zone2_name;
                //成单的客户信息
            } else {
                $success_customers[$k]['uid'] = $v->user->uid;
                $success_customers[$k]['nickname'] = $v->user->nickname;
                $success_customers[$k]['avatar'] = getImage($v->user->avatar);
                $success_customers[$k]['gender'] = User::getGender($v->user->gender);
                $success_customers[$k]['begin_time'] = strtotime($v->created_at);
                $success_customers[$k]['followed_days'] = self::followDays(strtotime($v->created_at));
                //地区处理
                $zone2_name = str_replace('市', '', $v->user->zone->name);
                $zone1_name = str_replace('省', '', Zone::pidName($v->user->zone_id));
                $success_customers[$k]['city'] = $zone1_name . ' ' . $zone2_name;
            }
        }
        //品牌名称
        $brand_name = $brand->hasOnebrand->name;

        return compact('following_customers', 'success_customers','brand_name');

    }

    /**
     * 根据某个时间戳计算距离当前时间的天数
     * @param $time 要计算的时间戳
     * retun 返回相差的天数(整数)
     *
     * @return int
     */
    public static function followDays($time)
    {
        $day = intval((time() - $time) / 86400);
        return $day;
    }

    /**
     * 品牌详情相关客户跟进数据
     * @param $agent_id 经纪人id
     * @param $brand_id 品牌id
     *
     * @return array
     */
    public static function agentBrandCount($agent_id, $brand_id)
    {
        //我的成单量
        $my_own_orders = AgentCustomerLog::where('agent_id', $agent_id)
            ->where('brand_id', $brand_id)
            ->where('action', '13')//已完成的单
            ->count();

//        //所有下线的id集
//        $ids = Agent::getInviteIds($agent_id);
//
//
//        $my_subordinate_orders = AgentCustomerLog::whereIn('agent_id', $ids)
//            ->where('brand_id', $brand_id)
//            ->where('action', '13')//已完成的单
//            ->count();
        //所有下线成单量
        $my_subordinate_orders = AgentAchievementLog::getBranchAchievement($agent_id,$brand_id);


        //当前品牌累计跟进客户数
        $total_customers = count(AgentCustomerLog::where('agent_id', $agent_id)
            ->where('brand_id', $brand_id)
            ->groupBy('agent_customer_id')
            ->get()->toArray());

        //当前品牌不在跟单的客户数
        $success_customers = count(AgentCustomerLog::where('agent_id', $agent_id)
            ->where('brand_id', $brand_id)
            ->whereIn('action', ['-1', '13'])
            ->groupBy('agent_customer_id')
            ->get()->toArray());

        //当前品牌跟进客户数
        $now_customers = $total_customers - $success_customers;

        return compact('my_own_orders', 'my_subordinate_orders', 'total_customers', 'now_customers');

    }

    /**
     * 客户详情--考察邀请 zhaoyf
     * @param $param
     * @return array
     */
    public function inspectInvites($param)
    {
        $gather_result = Invitation::with(['hasOneStore.hasOneBrand' => function($query) {
            $query->where('status', 'enable')->where('agent_status', 1);
        }, 'hasOneStore.hasOneZone', 'hasOneOrderItems.orders'
        ])
            ->where('agent_id', $param['agent_id'])
            ->where('uid',  $param['customer_id'])
            ->where('type', self::INSPECT_TYPE)
            ->get();

        //判断品牌是否下架
        foreach ($gather_result as $key => $res) {
            $brand_result = Brand::where('id', $res->hasOneStore->hasOneBrand->id)->first()->agent_status;
            if ($brand_result == self::REG_INVITE_TYPE) {

                //获取考察邀请总个数
                $inspect_invite_num = Invitation::where('agent_id', $param['agent_id'])
                    ->where('uid', $param['customer_id'])
                    ->where('status', '<>', -3)
                    ->where('type', self::INSPECT_TYPE)
                    ->count();
            }
        }

        //处理过期邀请函--将状态改成为 -2
        foreach ($gather_result as $key => $vls) {
            event(new \App\Events\Invitation($vls));
        }

        //获取考察邀请函结果集合 状态=4的0103版本加上的
        foreach ($gather_result as $key => $res) {
            $brand_result = Brand::where('id', $res->hasOneStore->hasOneBrand->id)->first()->agent_status;
            if ($brand_result == self::REG_INVITE_TYPE) {
                if ($res->status == 1 || $res->status == 2 || $res->status == 4 || $res->is_audit == 1) {
                    if (!empty($res->hasOneOrderItems)) {
                        foreach ($res->hasOneOrderItems as $keys => $vls) {
                            if ($vls['orders']['status'] == 'pay') {
                                $confirms_result['confirm_time'][] = [
                                    'store_name'    => array_get($res->hasOneStore, 'name'),
                                    'month'         => date('m月', strtotime($res->created_at)),
                                    'brand_title'   => array_get($res->hasOneStore->hasOneBrand, 'name'),
                                    'image'         => getImage(array_get($res->hasOneStore->hasOneBrand, 'logo')),
                                    'id'            => $res->id,
                                    'time'          => $res->inspect_time,
                                    'status'        => $res->status,
                                    'currency'      => $res->default_money,
                                    'head_address'  => array_get($res->hasOneStore->hasOneZone, 'name'),
                                    'inspect_address' => array_get($res->hasOneStore, 'address'),
                                    'pay_way'         => Orders::$_PAYWAY[array_get($vls['orders'], 'pay_way')],
                                    'confirm_time'    => $res->updated_at->getTimestamp(),
                                ];
                            }
                        }
                    }
                }
                if ($res['status'] == 0) {
                    $confirms_result['undetermined_result'][] = [
                        'id' => $res['id'],
                        'store_name'    => array_get($res->hasOneStore, 'name'),
                        'month'         => date('m月', strtotime($res->created_at)),
                        'brand_title'   => array_get($res->hasOneStore->hasOneBrand, 'name'),
                        'image'         => getImage(array_get($res->hasOneStore->hasOneBrand, 'logo')),
                        'time'          => $res->inspect_time,
                        'status'        => $res->status,
                        'currency'      => $res->default_money,
                        'head_address'  => array_get($res->hasOneStore->hasOneZone, 'name'),
                        'inspect_address' => array_get($res->hasOneStore, 'address'),
                        'status_summary'  => countDown($res->expiration_time),
                    ];
                }
                if ($res['status'] == -1 || $res['status'] == -2) {
                    $confirms_result['reject_result'][] = [
                        'store_name'    => array_get($res->hasOneStore, 'name'),
                        'month'         => date('m月', strtotime($res->created_at)),
                        'brand_title'   => array_get($res->hasOneStore->hasOneBrand, 'name'),
                        'image'         => getImage(array_get($res->hasOneStore->hasOneBrand, 'logo')),
                        'time'          => $res->inspect_time,
                        'status'        => $res->status,
                        'currency'      => $res->default_money,
                        'remark'        => $res->remark,
                        'head_address'  => array_get($res->hasOneStore->hasOneZone, 'name'),
                        'inspect_address' => array_get($res->hasOneStore, 'address'),
                        'confirm_time'    => $res->updated_at->getTimestamp(),
                    ];
                }
            }
        }

        //对月份相同的数据进行归类处理
        $confirm_result_array = array();
        foreach ($confirms_result as $key => $vls) {
            foreach ($vls as $keys => $vs) {
                $confirm_result_array[$vs['month']]['month']  = $vs['month'];
                $confirm_result_array[$vs['month']]['inspect_list'][] = $vs;
            }
        } rsort($confirm_result_array);

        //返回总和数据
        return [
            'totals'              => isset($inspect_invite_num) ?  $inspect_invite_num : 0,
            'gather_inspect_list' => $confirm_result_array
        ];
    }

    /**
     * 客户详情--跟进品牌 zhaoyf       --数据中心版
     *
     * @internal param 经纪人ID $agent_id
     * @internal param 客户ID   $customer_id
     * @param   $result
     *
     * @return array|string
     */
    public function DetailBrand($result)
    {
        //集合结果
        $gather_result = self::with(['user',
            'hasManyAgentCustomerLogs' => function($query) use($result) {
                $query->where('agent_id', $result['agent_id'])
                      ->where('uid',      $result['customer_id']);
        }, 'hasManyAgentCustomerLogs.hasOneBrand' => function($query) {
                $query->where('agent_status', 1);
        }, 'hasManyContract' => function($query) use($result) {
                $query->where('agent_id', $result['agent_id'])
                      ->where('uid',      $result['customer_id'])
                      ->whereIn('status', [1, 2])
                      ->select('id', 'agent_id', 'uid', 'brand_contract_id', 'status', 'confirm_time');
        }])
            ->where('agent_id', $result['agent_id'])
            ->where('uid',      $result['customer_id'])
            ->first();

        if (!$gather_result) return '没有相关信息，请核查下传递的对应的值是否准确或存在';

        //获取客户的来源关系（邀请）
        $judge_result = User::where('uid', $result['customer_id'])
                            ->where('register_invite', Agent::where('id', $result['agent_id'])->value('non_reversible'))
                            ->value('register_invite');

        $judge_result_two = AgentCustomer::where('uid', $result['customer_id'])
                    ->where('agent_id', $result['agent_id'])
                    ->where('source', '<>', self::RECOMMENT_TYPE)
                    ->first();

        //判断邀请关系结果
        if ( $judge_result ) {
           $relation = 1;
        } elseif ($judge_result_two) {
            switch ($judge_result_two->source) {
                case 1: case 2:
                case 3: case 4:
                case 6: case 7:
                    $relation = 1;
                    break;
                case 5:
                    $relation = 2;
                    break;
                default:
                    $relation = 0;
            }
        }

        //客户的基本信息
        $customer = [
            'id'             => $gather_result['user']['uid'],
            'avatar'         => $gather_result['user']['avatar'] ?  getImage($gather_result['user']['avatar'], 'avatar', '') : getImage('', 'avatar', ''),
            'remark'         => $gather_result['remark'],
            'nickname'       => $gather_result['user']['nickname'],
            'username'       => $gather_result->has_tel == 1 ?  Agent::getRealPhone($gather_result['user']['non_reversible'], 'wjsq') : $gather_result['user']['username'],
            'non_reversible' => $gather_result['user']['non_reversible'],
            'is_invite'      => isset($relation) ?  $relation : 0,
            'level'          => AgentCustomer::$customerLevel[$gather_result['level']],
            'is_public_tel'  => $gather_result->has_tel,
        ];

        if (!is_null($gather_result['hasManyAgentCustomerLogs'])) {

            //品牌基本信息
            foreach ($gather_result['hasManyAgentCustomerLogs'] as $key => $results) {
                if (!is_null($results['hasOneBrand'])) {
                    $brand_list[$key] = [
                        'id'           => $results['hasOneBrand']['id'],
                        'logo'         => getImage($results['hasOneBrand']['logo'], 'logo', ''),
                        'brand_title'  => $results['hasOneBrand']['name'],
                        'brand_id'     => $results['hasOneBrand']['id'],
                        'success_time' => $results['created_at'],
                        'action'       => $results['action'],
                        'event'        => self::event($result['agent_id'], $results['hasOneBrand']['id'], $result['customer_id']),
                    ];
                } else {
                    $brand_list = [];
                }
            }
        }

        //过滤重复的品牌
        $brand_list = Agent::instance()->_removeRepitition($brand_list);

        //返回组合后的数据信息
        return ['customer' => $customer, 'brand_list' => $brand_list];
    }

    /**
     * author zhaoyf
     *
     * 跟进情况--考察邀请
     * @param $param
     *
     * @return array
     */
    public function recordsInspects($param)
    {
        $result = Invitation::with(['hasOneUsers', 'belongsToAgent' => function($query) {
                $query->select('id', 'is_public_realname', 'nickname', 'realname');
        }, 'hasOneStore.hasOneBrand' => function($query) use($param) {
                $query->where('brand.id', $param['brand_id']);
        }, 'hasOneStore.hasOneZone',
            'hasOneOrderItems.orders'
        ])
         ->where('type',     self::INSPECT_TYPE)
         ->where('agent_id', $param['agent_id'])
         ->where('uid',      $param['customer_id'])
         ->whereIn('status', [-1, 0, 1, 2, 3, 4])
         ->get();

        $tags = false; $i = 0;

        //获取考察邀请函结果集合
        foreach ($result as $key => $res) {
            $brand_result = Brand::where('id', $res['hasOneStore']['hasOneBrand']['id'])->first()->agent_status;
            if ($brand_result == self::REG_INVITE_TYPE) {
                if (empty($res['hasOneStore']['hasOneBrand']) || is_null($res['hasOneStore']['hasOneBrand'])) {
                    $tags = true;
                } else {
                    $tags = false; $i++;
                    $result_data[$key] = [
                        'uid'           => $res['hasOneUsers']['uid'],
                        'user_nickname' => $res['hasOneUsers']['nickname'],
                        'created_at'    => $res['created_at']->getTimestamp(),
                        'brand_title'   => array_get($res['hasOneStore']['hasOneBrand'], 'name'),
                        'brand_id'      => array_get($res['hasOneStore']['hasOneBrand'], 'id'),
                        'brand_logo'    => getImage(array_get($res['hasOneStore']['hasOneBrand'], 'logo')),
                        'inspect_id'    => $res['id'],
                        'inspect_time'  => $res['inspect_time'],
                        'status'        => $res['status'],
                        'confirm_time'  => $res['updated_at']->getTimestamp(),
                        'currency'      => number_format($res['default_money']),
                        'head_address'  => array_get($res['hasOneStore']['hasOneZone'], 'name'),
                        'inspect_address'    => array_get($res['hasOneStore'], 'address'),
                        'status_summary'     => self::$InviteStatus[$res['status']],
                        'inspect_store_name' => array_get($res['hasOneStore'], 'name'),
                    ];
                    if ($res['status'] == 1 || $res['status'] == 2 || $res['status'] == 4  || $res['is_audit'] == 1 && !empty($res['hasOneOrderItems'])) {
                        foreach ($res['hasOneOrderItems'] as $keys => $vls) {
                            if ($vls['orders']['status'] == 'pay') {
                                $result_data[$key]['pay_way'] = Items::pay_way(array_get($vls['orders'], 'pay_way'));
                            }
                        }
                    }
                    if ($res['status'] == 0) {
                        $result_data[$key]['undetermined_time'] = countDown($res['expiration_time']);
                    }
                }
            }
        }

        //判断品牌是否存在
         if ( !$tags || $i > 0 ) {

             return [   //返回组合后的数据
                 'agent_nickname' => $result[0]['belongsToAgent']['is_public_realname'] ?  $result[0]['belongsToAgent']['realname'] : $result[0]['belongsToAgent']['nickname'],
                 'details'        => $i,
                 'record_list'    => $result_data,
             ];
         } else {
             return ['message' => '相关品牌不存在', 'status' => false];
         }
    }

    /**
     *  author zhaoyf     --数据中心版  已处理  相关接口处理后用不上该方法
     *
     * 投资人是否对经纪人公开了手机进行处理
     * 1. 公开了显示完整的手机号
     * 2. 未公开中间用星号代替
     *
     * @param $result
     *
     * @return string
     */
    public function customerIsPublicTelToAgentResultHandle($result)
    {
        //判断用于是否对经纪人公开手机号
        if ($result->has_tel == '1') {
            $user_tel = $result->user->non_reversible ? $result->user->non_reversible : '';
        } else {
            $user_tel = $result->user->username ? $result->user->username : '';
        }


        return $user_tel;
    }

    /**
     *  author zhaoyf
     *
     * 获取经纪人和投资人融云聊天数据信息
     * @param $data
     * 
     * @return success: return 200
     */
    public function anyReceiveRongChatInfos($data)
    {
        if ($data) {
            try{
               $result_data = json_decode(json_encode($data), true);
               $add_data = [
                   'send_id'        => $result_data['fromUserId'],
                   'receive_id'     => $result_data['toUserId'],
                   'info_type'      => $result_data['objectName'],
                   'content'        => $result_data['content'],
                   'channel_type'   => $result_data['channelType'],
                   'msg_time'       => $result_data['msgTimestamp'],
                   'msgUID'         => $result_data['msgUID'],
                   'sensitive_type' => $result_data['sensitiveType'],
                   'source'         => $result_data['source'],
               ];

               //对添加结果进行处理
               //获取数据并且添加成功，返回200
               AgentRongInfo::insert($add_data);

               return header("HTTP/1.1 200 0k");
           } catch (\Exception $e) {
                \Log::info($e->getMessage());
           }
        }
    }

    /**
     * author zhaoyf
     *
     * 添加通过分享过来的客户与经纪人的数据信息
     * @param $param
     *
     * @return array
     */
    public function addShareCustomerInfos($param)
    {
        //对客户的级别ID进行判断处理
        if (empty($param['customer_level']) || !is_numeric($param['customer_level'])) {
            return ['message' => '缺少客户级别ID，且只能是整数', 'status' => false];
        }

        //判断分享过来的投资人是否跟当前的经纪人已经存在关系
        // 1、如果有：不允许重复分享
        // 2、如果没有：走正常的流程
        /*$query_result = self::where('agent_id', $param['agent_id'])
            ->where('uid', $param['customer_id'])->first();

        //对查询结果进行处理
        if ($query_result) {
            return ['message' => '当前分享的客户已经存在，请不要重复分享', 'status' => false];
        }*/

        //组合需要添加的数据
        $data = [
            'agent_id'     => $param['agent_id'],
            'uid'          => $param['customer_id'],
            'level'        => $param['customer_level'],
            'protect_time' => 0,
            'created_at'   => time(),
            'updated_at'   => time(),
            'source'       => 8,
            'brand_id'     => 0,
        ];

        //执行添加数据操作
        $add_result = self::insert($data);


        //对添加后的结果进行处理
        if ($add_result) {
            return ['message' => '添加成功', 'status' => true];
        } else {
            return ['message' => '添加失败', 'status' => false];
        }
    }

    /**
     * author zhaoyf
     *
     * 根据指定值获取信息
     *
     * @param $param
     *
     * @return array
     */
    public function gainAssignConditionData($param)
    {
        //柑橘当前投资人ID和经纪人ID，获取其和经纪人的关系数据信息
       $query_result = self::where('uid', $param['customer_id'])
            ->where('agent_id', $param['agent_id'])
            ->where('source', '<>', self::RECOMMENT_TYPE)
            ->where('status', '<>' , -1)
            ->first();

       //对结果进行处理
       if (is_null($query_result)) return null;

       //对传递的brand_id进行判断
        if (!isset($param['brand_id']) || empty($param['brand_id'])) {
            $type_result = AgentBrand::where('agent_id', $param['agent_id'])
                ->where('status', '<>', -2)->first();
            if (is_null($type_result) || $type_result->status != self::CONFIRM_AGENCY_TYPE) {
                return 'inner_agent';
            } else {
                $param['brand_id'] = $type_result->brand_id;
            }
        }

       //对查询结果进行处理
       if ($query_result) {
            $agent_query_result = AgentBrand::where('agent_id', $query_result->agent_id)
                ->where('brand_id', $param['brand_id'])
                ->where('status', self::CONFIRM_AGENCY_TYPE)
                ->first();

            //对结果结果进行处理
            if ($agent_query_result) {
               $brand_result = Brand::where('id', $agent_query_result->brand_id)->first();

                //返回结果数据
               if ($brand_result) {
                   return [
                       'agent_id'        => $query_result->agent_id,
                       'message'         => $brand_result,
                       'relation_result' => $query_result,
                   ];
               } else {
                   return null;
               }
            }
       } else {
           return null;
       }
    }

    /**
     * author zhaoyf
     *
     * 获取邀请关系的投资人--经纪人
     *
     * @param uid       用户ID
     * @param source    客户来源：默认是查询邀请关系
     */
    public function gainCustomerAgentRelationDatas($uid, $source = [1, 2, 3, 4, 6, 7])
    {
        $gain_result = self::where('uid', $uid)
            ->where('level',  '<>', -1)
            ->where('status', '<>', -1)
            ->whereIn('source', $source)
            ->first();

        //对查询的结果进行判断
        if ($gain_result) {
            return $gain_result;
        } else {
            return null;
        }
    }

    /*
     *合同等待对方确认耗时
     */
    public static function waitConfirmTime($time)
    {
        $date=floor((time()-$time)/86400);

        $hour=floor((time()-$time)%86400/3600);

        $minute=floor((time()-$time)%3600/60);

        return $date.'天'.$hour.'小时'.$minute.'分';

    }
}