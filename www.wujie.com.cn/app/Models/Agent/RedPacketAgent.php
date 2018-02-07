<?php namespace App\Models\Agent;

use App\Models\RedPacket\RedPacket;
use App\Models\RedPacket\RedPacketPerson;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class  RedPacketAgent extends Model
{
    protected $table      = 'red_packet_agent';
    protected $dateFormat = 'U';
    protected $guarded = [];

    protected $num  = 3;   //总抽奖次数

    const NUMBER_1_ = -1;  //数字 -1
    const NUMBER_0  = 0;   //数字 0
    const NUMBER_1  = 1;   //数字 1
    const NUMBER_2  = 2;   //数字 2
    const NUMBER_3  = 3;   //数字 3
    const NUMBER_4  = 4;   //数字 4
    const NUMBER_5  = 5;   //数字 5

    public static $instance = null;

    //红包类型对应名称添加
    public static $RED_TYPE_NAME = [
        1 => '通用红包',
        2 => '品牌红包',
        3 => '邀请红包',
        4 => '奖励红包',
        5 => '福字红包',
        //6 => '新年大吉现金红包',
        //7 => '现金红包',
    ];

    //红包的状态
    public static $RED_USE_STATUS = [
        -1 => '已过期',
        0  => '未使用',
        1  => '已使用'
    ];

    //红包的使用场景
    public static $RED_USE_PLACE = [
        1 => '考察抵扣',
        2 => '合同支付抵扣',
        3 => '考察抵扣和合同支付抵扣（二选一）'
    ];

    public static function instance()
    {
        if (is_null(self::$instance)) {
            self::$instance = new self;
        }

        return self::$instance;
    }


    //关联agent
    public function agent()
    {
        return $this->belongsTo(Agent::class , 'agent_id' , 'id');
    }

    /**
     * 关联： 红包
     */
    public function hasOneRedpacket()
    {
        return $this->hasOne(RedPacket::class, 'id', 'red_packet_id');
    }

    /**
     * 关联：红包领取人
     */
    public function hasOneRedGainPersons()
    {
        return $this->hasOne(RedPacketPerson::class, 'red_packet_agent_id', 'id');
    }

    /**
     * 关联：红包领取人 多个
     */
    public function hasManyRedGainPersons()
    {
        return $this->hasMany(RedPacketPerson::class, 'red_packet_agent_id', 'id');
    }

    /**
     * author zhaoyf
     *
     * 获取经纪人福袋相关红包
     *
     * @param agent_id   经纪人ID
     * @param $source    红包来源
     * @param $uid       用户ID
     * @param $page      分页值
     * @param $page_size 分页值
     *
     * @return arrays | nulls
     */
    public function gainAgentLuckyBagReds($agent_id, $source = self::NUMBER_1, $uid = null, $page, $page_size, $status = null, $record = null)
    {
        $confirm_data = array();

        //改变过期红包状态
        $this->_changeOverdueRedStatus(RedPacketPerson::class);
        $this->_changeOverdueRedStatus(RedPacketAgent::class);

        //获取集合数据信息
        $gain_result = self::with(['hasOneRedpacket.brand',
            'hasManyRedGainPersons' => function($query) {
                $query->where('expire_at', '<>', self::NUMBER_1_)
                      ->where('gain_source', self::NUMBER_1);
            }, 'hasManyRedGainPersons.red_packet.brand', 'hasOneRedGainPersons.user'])
            ->where(['agent_id' => $agent_id, 'source' => $source])
            ->where('expire_at', '<>', -1);

        //区分福袋红包已发送和未发送
        if (!is_null($status)) {
            if ($status == self::NUMBER_0) {
                $gain_result->where('uid', self::NUMBER_0)
                            ->where('expire_at', '>', time());
            } elseif ($status == self::NUMBER_1) {
                $gain_result->where('uid', '>', self::NUMBER_0);
            }
        }

        //判断投资人ID知否存在
        if (!is_null($uid)) {
            $gain_results = $gain_result->where('uid', $uid)
                ->offset(($page - self::NUMBER_1) * $page_size)
                ->limit($page_size)
                ->orderBy('created_at', 'desc')
                ->get();
        } else {
            $gain_results = $gain_result->offset(($page - self::NUMBER_1) * $page_size)
                ->limit($page_size)
                ->orderBy('created_at', 'desc')
                ->get();
        }

        //对结果进行处理
        if ($gain_results) {
            foreach ($gain_results as $key => $vls) {
                if ($record && $record == 'gain_record') {
                    if (is_null($vls->hasOneRedGainPersons)) continue;
                }

                //对红包类型进行判断
                if ($vls->hasOneRedpacket->type == self::NUMBER_1) {
                    $confirm_data[$key]['red_name'] = $vls->hasOneRedpacket->name;
                    $confirm_data[$key]['type']     = self::NUMBER_1;
                } elseif ($vls->hasOneRedpacket->type == self::NUMBER_2) {
                    $confirm_data[$key]['brand_logo'] = getImage($vls->hasOneRedpacket->brand->logo, '', '');
                    $confirm_data[$key]['brand_name'] = $vls->hasOneRedpacket->brand->name;
                    $confirm_data[$key]['type']       = self::NUMBER_2;
                }

                //判断是否已经发送给过了投资人（true | false）
                $handle_result = $this->_getAgentSendRedRecordDatas($vls->id, $agent_id, $vls->hasOneRedpacket->id);

                //公用部分
                $confirm_data[$key]['agent_get_red_id'] = $vls->id;
                $confirm_data[$key]['red_id']           = $vls->hasOneRedpacket->id;
                $confirm_data[$key]['red_name']         = $vls->hasOneRedpacket->name;
                $confirm_data[$key]['red_support_type'] = self::$RED_USE_PLACE[$vls->hasOneRedpacket->use_scence];
                $confirm_data[$key]['red_expire_at']    = date("Y.m.d", $vls->expire_at);
                $confirm_data[$key]['min_consume']      = $vls->hasOneRedpacket->min_consume;
                $confirm_data[$key]['red_limit']        = str_replace(".00", '', $vls->amount);
                $confirm_data[$key]['red_status']       = $vls->status;
                $confirm_data[$key]['red_is_send_give_customer'] = $handle_result ?  1 : 0;

                //判断获取福袋红包有没有给投资人发送过，
                //有的情况下返回三个状态：已使用，未使用，过期）
                //如果没有使用人，返回空数组
                if (!is_null($vls->hasOneRedGainPersons)) {
                    $data = $this->_gainRedUseResults($vls->hasManyRedGainPersons, $key);

                    $confirm_data[$key]['brand_name']    = $data[$key]['brand_name'] ?: '';
                    $confirm_data[$key]['user_name']     = $data[$key]['user_name']  ?: '';
                    $confirm_data[$key]['user_tel']      = $data[$key]['user_tel']   ?: '';
                    $confirm_data[$key]['red_status']    = $data[$key]['red_status'];
                    $confirm_data[$key]['used_at']       = $data[$key]['used_at']       ?: 0;
                    $confirm_data[$key]['confirm_day']   = date("Y年m月d日", $data[$key]['confirm_day'])   ?: 0;
                    $confirm_data[$key]['red_packet_id'] = $data[$key]['red_packet_id'] ?: 0;
                    $confirm_data[$key]['expire_at']     = $data[$key]['expire_at']     ?: 0;
                }
            }
        }

        //返回指定投资人的红包领取记录
        if (!is_null($uid)) {
            $datas = array();
            foreach ($confirm_data as $key => $vls) {
                $datas[$vls['confirm_day']]['confirm_day'] = $vls['confirm_day'];
                $datas[$vls['confirm_day']]['data'][] = $confirm_data[$key];
            } rsort($datas);

           return $datas;
        }

        //获取抽奖剩余的次数
        $origin_tag = false;
        if (Cache::has('num_'.$agent_id)) {
           $remain_num = ($this->num - (Cache::get('num_'.$agent_id)-1));
           if ($remain_num > self::NUMBER_0) {
               $remain_num = $remain_num;
           } else {
               $remain_num = self::NUMBER_0;
           }
        } else {
            $remain_num = self::NUMBER_3; //如果不存在，返回总的抽奖次数
            $origin_tag = true;
        }

        //返回结果
        return ['data' => $confirm_data, 'valid_num' => $origin_tag ?  $remain_num : $remain_num-1];
    }

    /**
     * author zhaoyf
     *
     * 处理： 经纪人发送指定红包给指定投资人--设置发送红包后，红包的有效时间（五个小时）
     *
     * @param $agent_get_red_id 经纪人获取的红包对应表的ID
     * @param $red_id           经纪人发送给投资人的红包ID
     * @param $uid              投资人ID
     * @param $agent_id         经纪人ID
     *
     * @return bool
     */
    public function setSendRedLaterOfValidTime($agent_get_red_id, $red_id, $uid, $agent_id)
    {
        //默认五个小时有效时间
        $valid_time = strtotime(date("Y-m-d H:i:s", strtotime( "+5 hour")));
        //$valid_time = strtotime(date("Y-m-d H:i:s", strtotime( "+5 minute")));

        //记录已经发送过的红包数据信息
        return $this->_agentSendRedRecordAddOrUpdateDatas($agent_get_red_id, $red_id, $agent_id, $valid_time, $uid);

    }

    /**
     * author zhaoyf
     *
     * 从经纪人发送红包记录表里获取对应数据
     *
     * @param $red_packet_agent_id  经纪人红包领取对应ID
     * @param $agent_id             经纪人ID
     * @param $red_id               红包ID
     * @param $uid                  用户ID
     * @param $type                 区分哪个端的查看
     *
     * @return bool
     */
    private function _getAgentSendRedRecordDatas($red_packet_agent_id, $agent_id, $red_id, $uid = null, $type = null)
    {
        $builder = DB::table('agent_send_red_record')->where([
            'red_packet_agent_id' => $red_packet_agent_id,
            'agent_id'            => $agent_id,
            'red_packet_id'       => $red_id,
        ]);

        //如果用户ID不为空，根据用户ID进行查询
        if (!is_null($uid)) {
            $gain_result = $builder->where('uid', $uid)
                ->orderBy('id', 'desc')
                ->orderBy('created_at', 'desc')
                ->first();

            //根据用户ID获取到数据，返回结果
            if ($gain_result) {
                if ($gain_result->is_get == self::NUMBER_1) {
                    return 'red_yet_get';
                } elseif ($gain_result->expire_status == self::NUMBER_1) {
                    return 'red_yet_expire';
                } else {
                    return false;
                }
            } else {
                return false;
            }
        }

        //如果是经纪人查看
        if (!is_null($type) && $type == 'agent') {
            $get_results = $builder->orderBy('id', 'desc')
                ->orderBy('created_at', 'desc')
                ->select('expire_status')
                ->first();

            //对结果进行处理
            return $get_results;
        }

        //处理红包是否被领取或者是否已过期
        $get_result = $builder->where(function($query) {
             $query->where('is_get', self::NUMBER_1)
                   ->orWhere('expire_status', self::NUMBER_0);
         })->first();

        //对结果进行处理
        if ($get_result) return true;

        return false;
    }

    /**
     * author zhaoyf
     *
     * 往经纪人红包发送记录表存储数据信息
     *
     * @param  $agent_get_red_id    经纪人红包领取对应ID
     * @param  $red_id              红包表ID
     * @param  $uid                 投资ID
     * @param  $agent_id            经纪人ID
     * @param  $valid_time          有效时间（过期时间）
     *
     * @return  bool
     */
    private function _agentSendRedRecordAddOrUpdateDatas($agent_get_red_id, $red_id, $agent_id, $valid_time = null, $uid = null, $form = 'insert')
    {
        $builder = DB::table('agent_send_red_record');

        if ($form == 'insert') {
            //组织需要添加的数据
            $insert_data = [
                'agent_id'      => $agent_id,               //经纪人ID
                'uid'           => $uid,                    //用户ID
                'red_packet_id' => $red_id,                 //红包ID
                'expire_at'     => $valid_time,             //有效时间
                'expire_status' => self::NUMBER_0,          //过期状态
                'created_at'    => time(),                  //创建时间
                'updated_at'    => time(),                  //更新时间
                'red_packet_agent_id' => $agent_get_red_id  // 经纪人红包获取对应ID
            ];

            //进行数据添加
            $get_result = $builder->insert($insert_data);

            return true;
        } elseif ($form == 'update') {
            $builder->where([
                'red_packet_agent_id' => $agent_get_red_id,
                'agent_id'            => $agent_id,
                'red_packet_id'       => $red_id,
                'uid'                 => $uid
            ])->update([
                'is_get'   => self::NUMBER_1,   //是否领取（已领取）
                'get_time' => time(),           //领取时间
            ]);

            return true;
        }
    }

    /**
     * author zhaoyf
     *
     * 获取红包使用后的具体结果
     *
     * @param $use_person  object
     * @param $red_agent_id 经纪人红包对应表ID
     *
     * @return arrays
     */
    private function _gainRedUseResults($use_persons, $keys)
    {
        $confirm_data = array();

        //判断获取福袋红包有没有给投资人发送过（有的情况下返回三个状态：已使用，未使用，过期）
        foreach ($use_persons as $key => $use_person) {
            if ($use_person->status == self::NUMBER_0) {
                $confirm_data[$keys]['user_name']     = $use_person->user->realname ?: $use_person->user->nickname;
                $confirm_data[$keys]['user_tel']      = $use_person->user->username;//Agent::getRealPhone($use_person->user->username, 'wjsq');
                $confirm_data[$keys]['red_status']    = $use_person->status;
                $confirm_data[$keys]['confirm_day']   = strtotime($use_person->created_at);
                $confirm_data[$keys]['expire_at']     = date("Y.m.d", $use_person->expire_at);
                $confirm_data[$keys]['red_packet_id'] = $use_person->red_packet_id;
                $confirm_data[$keys]['red_use_person_id'] = $use_person->id;
            }

            //如果红包状态为已过期，获取过期时间
            elseif ($use_person->status == self::NUMBER_1_) {
                $confirm_data[$keys]['user_name']     = $use_person->user->realname ?: $use_person->user->nickname;
                $confirm_data[$keys]['user_tel']      = $use_person->user->username;//Agent::getRealPhone($use_person->user->non_reversible, 'wjsq');
                $confirm_data[$keys]['red_status']    = $use_person->status;
                $confirm_data[$keys]['expire_at']     = date("Y.m.d", $use_person->expire_at);
                $confirm_data[$keys]['confirm_day']   = strtotime($use_person->created_at);
                $confirm_data[$keys]['red_packet_id'] = $use_person->red_packet_id;
                $confirm_data[$keys]['red_use_person_id'] = $use_person->id;
            }

            //如果已经使用：
            //1、通用红包，关联：contract_pay_Log表，获取通用红包具体的用途（考察抵扣 | 合同支付抵扣）
            //2、如果是品牌红包，关联：red_packet表 和 brand表，获取具体支付后的加盟品牌
            elseif ($use_person->status == self::NUMBER_1) {
                if ($use_person->red_packet->type == self::NUMBER_1) {
                    $confirm_data[$keys]['brand_name'] = self::_redUsePlaces($use_person->id)['brand_name'];
                    $confirm_data[$keys]['brand_logo'] = self::_redUsePlaces($use_person->id)['brand_logo'];
                } elseif ($use_person->red_packet->type == self::NUMBER_2) {
                    $confirm_data[$keys]['brand_name'] = $use_person->red_packet->brand->name;
                    $confirm_data[$keys]['brand_logo'] = getImage($use_person->red_packet->brand->logo, '', '');
                }

                //公用部分
                $confirm_data[$keys]['user_name']     = $use_person->user->realname ?: $use_person->user->nickname;
                $confirm_data[$keys]['user_tel']      = $use_person->user->username;//Agent::getRealPhone($use_person->user->non_reversible, 'wjsq');
                $confirm_data[$keys]['red_status']    = $use_person->status;
                $confirm_data[$keys]['used_at']       = date("Y.m.d", $use_person->used_at);
                $confirm_data[$keys]['confirm_day']   = strtotime($use_person->created_at);
                $confirm_data[$keys]['red_packet_id'] = $use_person->red_packet_id;
                $confirm_data[$keys]['red_use_person_id'] = $use_person->id;
            }
        }

        //返回结果
        return $confirm_data;
    }

    /**
     * author zhaoyf
     *
     * 获取通用红包使用地方（考察定金抵扣、品牌合同支付）
     *
     * @param $use_id  红包使用人表ID
     *
     * @return string
     */
    private function _redUsePlaces($use_id)
    {
        $confirm_data = array();

        $gain_result = RedPacketPerson::with(
            ['hasOneContractPayLogs.hasOneInspect.hasOneStore.hasOneBrand' => function($query) {
                $query->select('id', 'logo', 'name');
            }, 'hasOneContractPayLogs.hasOneContract.brand' => function($query) {
                $query->select('id', 'logo', 'name');
            }])
            ->where('id',     $use_id)
            ->where('status', self::NUMBER_1)
            ->first();

        //对结果进行处理
        if ($gain_result && $gain_result->hasOneContractPayLogs) {

            //对支付的类型进行判断处理
            if ($gain_result->hasOneContractPayLogs->type == self::NUMBER_1) {
                $confirm_data = [
                    'brand_name' => $gain_result->hasOneContractPayLogs->hasOneInspect->hasOneStore->hasOneBrand->name,
                    'brand_logo' => getImage($gain_result->hasOneContractPayLogs->hasOneInspect->hasOneStore->hasOneBrand->logo, '', '')
                ];
            } elseif ($gain_result->hasOneContractPayLogs->type == self::NUMBER_3) {
                $confirm_data = [
                    'brand_name' => $gain_result->hasOneContractPayLogs->hasOneContract->brand->name,
                    'brand_logo' => getImage($gain_result->hasOneContractPayLogs->hasOneContract->brand->logo, '', '')
                ];
            }
        }

        //返回结果
        return $confirm_data;
    }

    //获取福卡信息列表
    public static function getFuCardList($agentId){
        $fCardInfo = self::where('agent_id',$agentId)->where('type',5)
            ->whereIn('status',[0,1])->where('source' , 2)->get()->toArray();
        $data = [];
        $collectFCard = collect($fCardInfo)->groupBy(function($item){
            return trim($item['remark']);
        })->toArray();

        //获取所有的福包信息
        $allFRedPacket = RedPacket::where('type',5)->where('status',1)->select('id','remark')->get();

        foreach ($allFRedPacket as $oneF){
            $count = 0;
            if(array_key_exists(trim($oneF['remark']) , $collectFCard)){
                $count = count($collectFCard[$oneF]);
            }
            $f_key = '';
            switch ($oneF){
                case '无': $f_key = 'wu' ;break;
                case '界': $f_key = 'jie' ;break;
                case '商': $f_key = 'shang' ;break;
                case '圈': $f_key = 'quan' ;break;
                case '福': $f_key = 'fu' ;break;
            }
            $data[$f_key] = ['count'=> $count , 'id'=>$oneF['id']];
        }
        return $data;
    }

    /**
     * author zhaoyf
     *
     * 红包获取概率
     *
     * @param $agent_id     经纪人ID
     *
     * @return arrays
     */
    public function redGainPros($agent_id)
    {
        $_red_data = array();

        //处理抽奖次数
        $gain_award_num = $this->_OneDayAwardNumbers($agent_id);

        //对结果次数处理
        if ($gain_award_num == self::NUMBER_0) {
            return ['data' => [], 'valid_num' => $gain_award_num, 'notice' => '今天抽奖次数用完了'];
        }

        //从红包表获取创建的福袋红包数据信息
        $gain_result = RedPacket::where('red_source', self::NUMBER_1)
            ->whereIn('type', [self::NUMBER_1, self::NUMBER_2])
            ->where('status', self::NUMBER_1)
            ->where(function($query) {
                $query->where('expire_at', '>', time())
                      ->orWhere('expire_at', -1);
            })->get();

        //对结果进行处理
        if ($gain_result) {
            foreach ($gain_result as $key => $vls) {
                $_red_data[] = [
                    'id'               => $vls->id,               //红包ID
                    'type'             => $vls->type,             //红包类型
                    'post_id'          => $vls->post_id,          //关联ID
                    'amount'           => $vls->amount,           //红包总额度
                    'expire_at'        => $vls->expire_at,        //红包的过期时间
                    'total'            => $vls->total,            //红包的总个数
                    'gives'            => $vls->gives,            //红包已经使用个数
                    'red_name'         => $vls->name,             //红包名称
                    'created_at'       => $vls->created_at,       //创建时间
                    'status'           => $vls->status,           //红包状态
                    'gain_probability' => $vls->gain_probability  //红包的获取概率
                ];
            }

            //进行红包获取的概率进行处理
            if (!is_null($_red_data)) {
                return $this->_redProbabilityHandles($_red_data, $agent_id, $gain_award_num-1);
            } else {
                return [];
            }
        }

        return [];
    }

    /**
     * author zhaoyf
     *
     * 红包获取概率处理
     *
     * @param   $_red_data       arrays
     * @param   $agent_id        经纪人ID
     * @param   $gain_award_num  抽奖次数
     *
     * @return arrays
     */
    private function _redProbabilityHandles(array $_red_data = [], $agent_id, $gain_award_num = self::NUMBER_0)
    {
        $confirm_data = array();
        $return_data  = array();

        //处理抽奖红包, 同时返回获取到的红包信息
        return DB::transaction(function() use($_red_data, $agent_id, $gain_award_num, $return_data) {
            if (!is_null($_red_data)) {
                foreach ($_red_data as $key => $vls) {
                    if ($vls['total'] > self::NUMBER_0 && $vls['gives'] < $vls['total']) {
                        $pro = round($vls['total'] / $vls['gain_probability']);
                        $rand_nums = mt_rand(1, $pro);

                        //对结果进行处理
                        if ($rand_nums <= $vls['total']) {
                            $confirm_data[] = [
                                'agent_id'      => $agent_id,
                                'red_packet_id' => $vls['id'],
                                'expire_at'     => $vls['expire_at'],
                                'created_at'    => time(),//strtotime($vls['created_at']),
                                'updated_at'    => time(),
                                'type'          => $vls['type'],
                                'amount'        => $vls['amount'],
                                'source'        => 1
                            ];

                            //已领取红包数量加一
                            $this-> _addNumberValues(RedPacket::class, ['id' => $vls['id']],'gives');
                        }
                    }
                }
            }

            //进行红包数据存储
            $red_id = array();
            if (!is_null($confirm_data)) {
                foreach ($confirm_data as $key => $vls) {
                    $this->addDatas($vls);
                    $red_id[] = $vls['red_packet_id'];
                }

                //获取福袋红包的详细数据信息
                $gain_results = RedPacket::with('brand')
                    ->where('red_source', self::NUMBER_1)
                    ->whereIn('id', $red_id)
                    ->get();

                if ($gain_results) {
                   foreach ($gain_results as $key => $vls) {
                       $return_data[] = [
                           'red_type'         => self::$RED_TYPE_NAME[$vls->type],
                           'red_name'         => $vls->name,
                           'brand_logo'       => getImage($vls->brand->logo, '', ''),
                           'brand_name'       => $vls->brand ?  $vls->brand->name : '',
                           'red_support_type' => self::$RED_USE_PLACE[$vls->use_scence],
                           'red_expire_at'    => date("Y-m-d H:i", $vls->expire_at),
                           'min_consume'      => $vls->min_consume,
                           'red_limit'        => str_replace(".00", '', $vls->amount),
                       ];
                   }
                }
            }

            //返回结果值
            return ['data' => $return_data, 'valid_num' => $gain_award_num];
        });
    }

    /**
     * author zhaoyf
     *
     * 福袋红包详情
     *
     * @param $id                 用户ID值（经纪人 | 投资人）
     * @param $agent_get_red_id   经纪人领取红包对应表ID
     * @param $type               类型--用于区分是经纪人查看还是投资人查看
     *
     * @return arrays
     */
    public function luckyBagRedDetails($agent_id, $uid = null, $agent_get_red_id, $type)
    {
        $status      = self::NUMBER_0;
        $return_data = array();

        $gain_result = self::with(['agent' => function($query) {
            $query->select('id', 'nickname', 'realname', 'username', 'avatar', 'non_reversible');
        }, 'hasOneRedpacket.brand', 'hasOneRedGainPersons' => function($query) {
            $query->orderBy('created_at', 'desc');
        }, 'hasOneRedGainPersons.user' => function($query) {
            $query->select('uid', 'nickname', 'avatar', 'username', 'non_reversible');
        }])
         ->where(['id'  => $agent_get_red_id,
             'agent_id' => $agent_id,
             'source'   => self::NUMBER_1
         ])
          //->where('expire_at', '>', time())
          ->first();

        //对结果进行处理
        if ($gain_result) {
            if ($type == 'customer') {
                $handle_result = $this->_getAgentSendRedRecordDatas($agent_get_red_id, $agent_id, $gain_result->hasOneRedpacket->id, $uid);

                //返回红包已经过期
                if (is_string($handle_result) && $handle_result == 'red_yet_expire') {
                    return ['expire_status' => self::NUMBER_1];
                }

                //判断相同的红包投资人是否已经领取过，
                //如果已经领取过，就直接查看详情，
                //如果没有，就进行红包数据添加的操作
                if ($handle_result != 'red_yet_get' && !$handle_result) {
                    $add_data = [
                        'receiver_id'   => $uid,
                        'red_packet_id' => $gain_result->hasOneRedpacket->id,
                        'expire_at'     => $gain_result->expire_at,
                        'created_at'    => time(),
                        'updated_at'    => time(),
                        'type'          => $gain_result->type,
                        'amount'        => $gain_result->amount,
                        'gain_source'   => self::NUMBER_1,
                        'red_packet_agent_id' => $agent_get_red_id
                    ];

                    //插入红包数据
                    RedPacketPerson::insert($add_data);

                    //更新经纪人领取红包表，把投资人ID存入进去
                    self::where(['agent_id' => $agent_id, 'id' => $agent_get_red_id])->update(['uid' => $uid, 'status' => self::NUMBER_1]);

                    //更新经纪人红包发送记录表，存储已经领取过红包的投资人信息
                    $this->_agentSendRedRecordAddOrUpdateDatas($agent_get_red_id, $gain_result->hasOneRedpacket->id, $agent_id, null, $uid, 'update');

                    //发送领取红包后的融云消息
                    $this->_gainAgentRedSendRoundInfos($agent_id, $uid);
                }
            } elseif ($type == 'agent') {
                $expires_status  = $this->_getAgentSendRedRecordDatas($agent_get_red_id, $agent_id, $gain_result->hasOneRedpacket->id, $uid, 'agent');
                if ($expires_status && $expires_status->expire_status == 1) {
                    $status = self::NUMBER_1;
                }
            }

            //获取红包的相关数据信息
            if ($gain_result->type == self::NUMBER_1) {
                $return_data['type']       = self::NUMBER_1;
                $return_data['brand_name'] = $gain_result->hasOneRedGainPersons ?  self::_redUsePlaces($gain_result->hasOneRedGainPersons->id)['brand_name'] : '';
                $return_data['brand_logo'] = $gain_result->hasOneRedGainPersons ?  self::_redUsePlaces($gain_result->hasOneRedGainPersons->id)['brand_logo'] : '';

            } elseif ($gain_result->type == self::NUMBER_2) {
                $return_data['brand_logo'] = getImage($gain_result->hasOneRedpacket->brand->logo, '', '');
                $return_data['brand_name'] = $gain_result->hasOneRedpacket->brand->name;
                $return_data['type']       = self::NUMBER_2;
            }

            //公用部分
            $return_data['agent_get_red_id'] = $gain_result->id;
            $return_data['red_name']         = $gain_result->hasOneRedpacket->name;
            $return_data['red_support_type'] = self::$RED_USE_PLACE[$gain_result->hasOneRedpacket->use_scence];
            $return_data['red_expire_at']    = date("Y.m.d H:i", $gain_result->expire_at);
            $return_data['min_consume']      = $gain_result->hasOneRedpacket->min_consume;
            $return_data['red_limit']        = str_replace(".00", '', $gain_result->amount);
            $return_data['expire_status']    = $status;
            $return_data['send_status']      = $gain_result->uid > self::NUMBER_0 ?  1 : 0;

            //返回投资人是否已经领取了这个红包
            if (is_string($handle_result) && $handle_result == 'red_yet_get') {
                $return_data['user_is_gain'] = 1;
            } else {
                $return_data['user_is_gain'] = 0;
            }

            //经纪人部分
            $return_data['use_agent'] = [
                'agent_avatar'   => getImage($gain_result->agent->avatar, '', ''),
                'agent_nickname' => $gain_result->agent->nickname,
                'agent_tel'      => $gain_result->agent->username,
                'agent_id'       => $gain_result->agent->id,
                'agent_non_reversible' => $gain_result->agent->non_reversible
            ];

            //如果存在已经领取的投资人
            if ($gain_result->uid > self::NUMBER_0) {
                $gain_send_time = DB::table('agent_send_red_record')
                    ->where([
                        'agent_id'            => $agent_id,
                        'red_packet_agent_id' => $agent_get_red_id,
                        'red_packet_id'       => $gain_result->hasOneRedpacket->id,
                        'is_get'              => self::NUMBER_1,
                        'uid'                 => $gain_result->uid
                    ])->first()->created_at;

                $return_data['use_person'] = [
                    'uid'       => $gain_result->hasOneRedGainPersons->user->uid,
                    'user_name' => $gain_result->hasOneRedGainPersons->user->nickname,
                    'open_time' => $gain_send_time ? openTime($gain_send_time, strtotime($gain_result->hasOneRedGainPersons->created_at)) : '未打开',
                    'user_logo' => $gain_result->hasOneRedGainPersons->user->avatar ?  getImage($gain_result->hasOneRedGainPersons->user->avatar, '', '') : '',
                    'user_tel'  => $gain_result->hasOneRedGainPersons->user->username,
                    'time'      => $gain_result->hasOneRedGainPersons->updated_at ? strtotime($gain_result->hasOneRedGainPersons->updated_at) : 0,
                    'red_status'=> $gain_result->hasOneRedGainPersons->status,
                    'used_at'   => $gain_result->hasOneRedGainPersons->status == 1 ?  date("Y-m-d H:i", $gain_result->hasOneRedGainPersons->used_at) : 0
                ];
            } else {
                $return_data['use_person'] = '';
            }
        }

        //返回结果
        return $return_data;
    }

    /**
     * author zhaoyf
     *
     * 获取对某个投资人的红包发放记录
     *
     * @param   $gent_id    经纪人ID
     * @param   $uid        投资人ID
     * @param   $page       分页值
     * @param   $page_size  分页值
     *
     * @return arrays
     */
    public function gainOneCustomerRedDatas($agent_id, $uid, $page, $page_size)
    {
       return $this->gainAgentLuckyBagReds($agent_id, self::NUMBER_1, $uid, $page, $page_size, null, 'gain_record');
    }

    /**
     * author zhaoyf
     *
     * 进行数据存储
     *
     * @param  $data arrays 需要添加的数据
     * @param  $return_form 返回形式——create | insert
     *
     * @return bool | object
     */
    public function addDatas(array $data = [], $return_form = 'insert')
    {
        if (!is_null($data)) {
            return self::$return_form($data);
        }

        return [];
    }

    /**
     * author zhaoyf
     *
     * 改变过期红包的状态
     *
     * @param   需要改变状态的对象
     *
     * @return bool
     */
    private function _changeOverdueRedStatus($object)
    {
        //改变过期的福袋红包状态
        $object::where('status', self::NUMBER_0)
            ->where('expire_at', '<>', -1)
            ->where('expire_at', '<', time())
            ->update(['status' => -1]);
    }

    /**
     * author zhaoyf
     *
     * 数值加加
     *
     * @param 需要操作的数据对象
     * @param 指定的参数值
     * @param 需要增加数的值
     * @param 执行条件，默认：where
     *
     * @return bool
     */
    private function _addNumberValues($object, $param, $value, $where = 'where')
    {
        $object::$where($param)->increment($value);
    }

    /**
     * 判断一天之内的抽奖次数
     *
     * @param   $agent_id   经纪人ID
     */
    private function _OneDayAwardNumbers($agent_id)
    {
        $valid_time = strtotime(date("Y-m-d 23:59:59"));

        //一天之内的抽奖次数 3 次
        $extract_number = Cache::increment('num_'.$agent_id, 1, $valid_time);

        //对抽奖的次数进行判断处理
        if ($extract_number > $this->num) {
            return 0;
        } else {
            return $this->num - ($extract_number-1);
        }
    }

    public static function receiveOpenRedpacket($agentId){
        $openRedPacket = RedPacket::showWhere()->where('type' , 6)->first();
        if(!is_object($openRedPacket)){
            return false;
        }
        $data = [];
        $data['agent_id'] = $agentId;
        $data['type'] = 6;
        $data['red_packet_id'] = $openRedPacket['id'];
        $data['expire_at'] = $openRedPacket['expire_at'];
        $data['source'] = $openRedPacket['red_source'];
        $data['amount'] = mt_rand(floatval($openRedPacket['amount']) , floatval($openRedPacket['max_amount']));

        \DB::transaction(function()use($data , $agentId){
            $rel = self::create($data);
            RedPacket::where('id' ,$data['red_packet_id'])->increment('gives');
            AgentAdd::where('agent_id' , $agentId)->update(['has_receive_open' => 1]);
            Agent::where('id',$agentId)->increment('currency',$data['amount']);
        });
        return $data;
    }

    /**
     * author zhaoyf
     *
     * 返回已经获得答题红包的用户信息
     */
    public function returnGetAnswerRedUseDatas()
    {
        $confirm_data = array();

        $gain_result = self::with(['agent' => function($query) {
            $query->select('id', 'realname', 'nickname');
        }])
            ->where('type', 8)
            ->groupBy(DB::raw('agent_id'))
            ->select(DB::raw("*, count(*) as red_num"))
            ->get();

        //获取数据结果
        if ($gain_result) {
            foreach ($gain_result as $key => $vls) {
                $confirm_data[] = [
                    'agent_name' => Str::limit($vls->agent->nickname, 1, ""),
                    'red_amount' => $vls->amount,
                    'red_num'    => $vls->red_num,
                    'type'       => '通用红包'
                ];
            }
        }

       return $confirm_data;
    }

    /**
     * 投资人领取了经纪人的红包后，发送融云消息
     *
     * @param $agent_id 经纪人ID
     * @param $uid      用户ID
     *
     * @return results
     */
    private function _gainAgentRedSendRoundInfos($agent_id = null, $uid = null)
    {
        $user_result = \App\Models\User\Entity::where('uid', $uid)
            ->select('uid', 'realname', 'nickname', 'username')
            ->first();

        $user_name = $user_result->realname ?: $user_result->nickname;
        $data      = ['content' => "投资人：{$user_name}打开了你的红包", 'extra' => '2'];

        //发送融云消息
        SendCloudMessage($uid, 'agent' .$agent_id,  $data, 'TY:TipMsg', $data, 'custom', 'one_user');
    }
}

