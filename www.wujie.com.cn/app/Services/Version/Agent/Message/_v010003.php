<?php namespace App\Services\Version\Agent\Message;

use App\Models\Agent\Agent;
use App\Models\Agent\AgentCustomer;
use App\Models\Agent\AgentCustomerLog;
use App\Services\Version\Agent\Message\_v010002;
use App\Models\User\Entity as User;
use App\Models\Zone\Entity as Zone;
use Illuminate\Support\Facades\DB;

class _v010003 extends _v010002
{
    const BEAR =  -1;   //禁用标记

    /**
     * 经纪人添加好友 zhaoyf    --数据中心版
     *
     * @param Request $request
     * @param null $version
     * @return string
     */
    public function postAddFriends($param)
    {
        $tags = false;

        //参数获取
        $result = $param['request']->input();
        //手机号
        $tel = $result['phone'];

        //md5加盐后的注册号码
        $non_reversible = encryptTel($tel);

        //通过类型区分是搜溹还是添加
        if (trim($result['type']) === 'query') {

            $agent_relation = $user_relation = 0;

            //判断是否添加的好友为自己
            $query_result = $this->examineUserStatus(Agent::class, [
                'id'        => $result['agent_id'],
                'non_reversible'  => $non_reversible,
            ]);
            if ($query_result) {
                return ['message' => '不能自己添加自己哦~', 'status' => false];
            }

            $agent_result = Agent::where('non_reversible', $non_reversible)->first();
            $user_result  = User::where('non_reversible',  $non_reversible)->first();

            //获取添加经纪人时的关系
            if (is_object($agent_result)) {

                //判断要添加的好友是否被禁用
                $examine_result = $this->examineUserStatus(Agent::class, [
                    'id' => $agent_result->id,
                    'status' => self::BEAR
                ]);
                if ($examine_result) {
                    return ['message' => '该用户已被禁用，不能添加为好友~', 'status' => false];
                }

                $relation_result_data_1 = DB::table('agent_friends_relation')->where([
                    'execute_agent_id'  => $result['agent_id'],
                    'relation_agent_id' => $agent_result->id,
                    ])->first();

                $relation_result_data_2 = DB::table('agent_friends_relation')->where([
                    'execute_agent_id'  => $agent_result->id,
                    'relation_agent_id' => $result['agent_id']
                ])->first();

                //查询ID看是否等于当前传递的ID，如果一样关系为 0，如果不一样关系为 1
                if ($relation_result_data_1) {
                    if ($relation_result_data_1->relation_agent_id == $result['agent_id']) {
                        $agent_relation = 0;
                        $tags = true;
                    } else {
                        $agent_relation = 1;
                    }
                } elseif ($relation_result_data_2) {
                    if ($relation_result_data_2->relation_agent_id == $result['agent_id']) {
                        $agent_relation = 0;
                        $tags = true;
                    } else {
                        $agent_relation = 1;
                    }
                } else {
                    $up_agent_result   = Agent::where([          //获取经纪人上级
                        'id'       => $result['agent_id'],
                        'non_reversible' => $agent_result->register_invite
                    ])->count();
                    $down_agent_result = Agent::where([          //获取经纪人下级
                        'id'              => $result['agent_id'],
                        'register_invite' => $agent_result->non_reversible
                    ])->count();

                    if ($up_agent_result || $down_agent_result) {
                        $agent_relation = 1;
                    } else {
                        $agent_relation = 0;
                    }
                }
            }

            //查询需要添加的好友是否已经存在关系
            if (is_object($user_result)) {

                //判断要添加的好友是否被禁用
                $examine_result = $this->examineUserStatus(User::class, [
                    'uid'    => $user_result->uid,
                    'status' => self::BEAR
                ]);
                if ($examine_result) {
                    return ['message' => '该用户已被禁用，不能添加为好友~', 'status' => false];
                }

                $user_relation = $this->gainAgentToCustomerRelation($result['agent_id'], $user_result->uid);
            }

            $return_result = array();
            if (is_object($agent_result) && is_object($user_result)) {
                if (!$tags) {
                    $return_result[] = [
                        'id'       => $agent_result->id,
                        'name'     => $agent_result->realname ?: $agent_result->nickname,
                        'gender'   => $agent_result->gender,
                        'logo'     => getImage($agent_result->avatar),
                        'zone'     => abandonProvince(Zone::pidNames([$agent_result->zone_id])),
                        'relation' => $agent_relation,
                        'type'     => 'agent'
                    ];
                }
                $return_result[] = [
                    'id'        => $user_result->uid,
                    'name'      => $user_result->realname ?: $user_result->nickname,
                    'gender'    => $user_result->gender,
                    'logo'      => getImage($user_result->avatar),
                    'zone'      => abandonProvince(Zone::pidNames([$user_result->zone_id])),
                    'relation'  => $user_relation,
                    'type'      => 'user'
                ];
            } elseif (is_object($agent_result)) {
                if (!$tags) {
                    $return_result[] = [
                        'id'        => $agent_result->id,
                        'name'      => $agent_result->realname ?: $agent_result->nickname,
                        'gender'    => $agent_result->gender,
                        'logo'      => getImage($agent_result->avatar),
                        'zone'      => abandonProvince(Zone::pidNames([$agent_result->zone_id])),
                        'relation'  => $agent_relation,
                        'type'      => 'agent'
                    ];
                }
            } elseif (is_object($user_result)) {
                $return_result[] = [
                    'id'        => $user_result->uid,
                    'name'      => $user_result->realname ?: $user_result->nickname,
                    'gender'    => $user_result->gender,
                    'logo'      => getImage($user_result->avatar),
                    'zone'      => abandonProvince(Zone::pidNames([$user_result->zone_id])),
                    'relation'  => $user_relation,
                    'type'      => 'user'
                ];
            }

            //返回最后结果
            return ['message' => $return_result, 'status' => true];

            //执行添加好友操作
        } elseif (trim($result['type']) === 'add_friends') {
            if (trim(!isset($result['friends_type'])) || trim(empty($result['friends_type']))) {
                return ['message' => '添加的好友类型，不能为空', 'status' => false];
            }

            //对经纪人是否实名认证进行验证
            $identity_result = $this->AgentToAgentRelation($result['agent_id'], $result['friends_id'], 'identity');
            if (!$identity_result) {
                return ['message' => '您还没有实名认证', 'status' => false];
            }

            if ($result['friends_type'] === 'agent') {
                //对要添加的经纪人的关系进行验证
                $judge_result = $this->AgentToAgentRelation($result['agent_id'], $result['friends_id'], 'relation');
                if ($judge_result) {
                    return ['message' => '已经是好友关系啦~', 'status' => false];
                }

                //进行好友数据的添加
                $add_data = [
                    'execute_agent_id'  => $result['agent_id'],
                    'relation_agent_id' => $result['friends_id'],
                    'created_at'        => time(),
                    'updated_at'        => time(),
                ];
              $add_result = DB::table('agent_friends_relation')->insert($add_data);
            } elseif ($result['friends_type'] === 'user') {

                //判断要添加的投资人是否已经是好友关系
                $judge_result = $this->gainAgentToCustomerRelation($result['agent_id'], $result['friends_id']);
                if ($judge_result) {
                    return ['message' => '已经是好友关系啦~', 'status' => false];
                }

                //进行好友数据的添加
                $add_data = [
                    'agent_id'      => $result['agent_id'],
                    'uid'           => $result['friends_id'],
                    'protect_time'  => 0,
                    'created_at'    => time(),
                    'updated_at'    => time(),
                    'source'        => 9,
                    'brand_id'      => 0,
                ];
                $agent_id_result = AgentCustomer::insertGetId($add_data);
                if ($agent_id_result) {

                    //往日记表里添加数据
                    $add_new_log_data = [
                        'agent_customer_id' => $agent_id_result,
                        'action'            => 15,
                        'post_id'           => 0,
                        'brand_id'          => 0,
                        'agent_id'          => $result['agent_id'],
                        'uid'               => $result['friends_id'],
                        'created_at'        => time()
                    ];
                $add_result = AgentCustomerLog::insert($add_new_log_data);
                }
            } else {
                return ['message' => '需要添加的好友类型输入错误', 'status' => false];
            }

            //对添加好友结果进行处理
            if (isset($add_result) && $add_result) {
                return ['message' => '添加好友成功', 'status' => true];
            } else {
                return ['message' => '添加好友成功', 'status' => true];
            }
        } else {
            return ['message' =>'传递的类型错误', 'status' => false];
        }
    }


    /**
     * 获取经纪人和投资人的好友关系
     */
    public function gainAgentToCustomerRelation($agent_id, $uid)
    {
        //查询需要添加的好友是否已经存在关系
        $relation_result = AgentCustomer::where('agent_id', $agent_id)
            ->where('uid', $uid)
            ->where('level',  '<>', -1)
            ->where('status', '<>', -1)
            ->first();

        //判断经纪人和客户的关系
        if (is_object($relation_result)) {
            $relation = 1;
        } else {
            $relation = 0;
        }

        return $relation;
    }

    /**
     * 判断经纪人和经纪人之间的关系   --数据中心版
     *
     * @param agent_id      经纪人ID
     * @param friends_id    关系ID
     * @param type          判断经纪人的身份和要添加的经纪人之间的关系
     *
     * return bool
     */
    public function AgentToAgentRelation($agent_id, $friends_id, $type)
    {
        if ($type == 'relation') {
            //查询关系表
            $relation_result = DB::table('agent_friends_relation');

            $relation_1 = $relation_result->where([
                'execute_agent_id' => $agent_id,
                'relation_agent_id' => $friends_id,
            ])->first();

            $relation_2 = $relation_result->where([
                'relation_agent_id' => $agent_id,
                'execute_agent_id' => $friends_id,
            ])->first();

            if (is_object($relation_1) || is_object($relation_2)) {
                return true;
            }

            //查询agent表
            $agent_relation_1 = Agent::where('id', $agent_id)
                ->where('status', '<>', '-1')->first();

            $agent_relation_2 = Agent::where('id', $friends_id)
                ->where('status', '<>', '-1')->first();

            if ($agent_relation_1 && $agent_relation_2) {
                if ($agent_relation_1->non_reversible == $agent_relation_2->register_invite) {
                    return true;
                } elseif ($agent_relation_1->register_invite == $agent_relation_2->non_reversible) {
                    return true;
                }
            }

            return false;
        } elseif ($type == 'identity') {
            $identity_result = Agent::where('id', $agent_id)
                ->where('status', '<>', '-1')->first();

            if ($identity_result->is_verified == '1') {
                return true;
            } else {
                return false;
            }
        }
    }

    /**
     * 判断用户状态
     *
     * @param table    要查询的表
     * @param param    数组集合：用户ID（经纪人、投资人）
     *
     * @consult：[ 'agent_id' => 1, 'status' => -1 ]
     *
     * return bool
     */
    public function examineUserStatus($table, array $param)
    {
        $result = $table::where($param)->first();

        //对结果进行处理
        if(is_object($result)) {
            return true;
        }

        return false;
    }
}