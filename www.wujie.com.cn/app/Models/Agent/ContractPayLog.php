<?php

namespace App\Models\Agent;

use App\Models\Orders\Items;
use Illuminate\Database\Eloquent\Model;
use App\Models\Orders\Entity as Orders;
use App\Models\Contract\Contract;

class ContractPayLog extends Model
{
    protected $table = 'contract_pay_log';
    protected $dateFormat = 'U';
    protected $guarded = [];



//'支付类型：1：考察订金抵扣；2：pos机支付；3：通用红包 4：品牌红包  5:奖励红包(车马费) 6：初创红包（邀请红包) 7:新年活动经纪人答题红包  8: 线下到帐 9 :品牌加盟预付金抵扣',
    public static $_REFUND_TYPES = [
        1, 3, 4, 5, 6, 7, 9
    ];

    public static $_PAY_TYPES = [
        2,8
    ];
    /**
     * 关联：考察邀请
     */
    public function hasOneInspect()
    {
        return $this->hasOne(Invitation::class, 'id', 'post_id');
    }

    /**
     * 关联：合同
     */
    public function hasOneContract()
    {
        return $this->hasOne(\App\Models\Contract\Contract::class, 'id', 'contract_id');
    }

    /**
     * 功能描述：获取合同的支付记录
     *
     * 参数说明：
     * @param $contractId           合同id ， 0：全部 ，非0：合同id
     * @param array $typeArray      支付类型：1：考察订金抵扣；2：pos机支付；3：通用红包 4：品牌红包
                 *                              5:奖励红包(车马费) 6：初创红包（邀请红包) 7:新年活动经纪人答题红包
                 *                              8: 线下到帐 9 :品牌加盟预付金抵扣    []代表所有的类型
     *                                          其中除了2，8 其他的都是优惠抵扣，最后可返现
     * @param string $payStatus     支付状态：success：成功的支付  非success：所有的支付记录
     * @param string $orderBy       排序 ：    created：支付时间降序   type   支付类型升序
     *
     * 返回值：
     * @return array
     *
     * 实例：
     * 结果：
     *
     * 作者： shiqy
     * 创作时间：@date 2018/1/19 0019 上午 9:53
     */

    public static function getPayDetailByType($contractId , $typeArray = [] , $payStatus = 'success' ,$orderBy = 'created'){
        $builder = null;
        if(!empty($contractId)){
            $builder = self::where('contract_id' , $contractId);
        }
        if(!empty($typeArray)){
            $typeArray = array_filter($typeArray);
            $builder = $builder->whereIn('type' , $typeArray);
        }
        $payStatus == 'success' && $builder = $builder->where('status' , 1);
        if($orderBy == 'type'){
            $builder = $builder->orderBy('type','asc');
        }
        $logList = $builder->orderBy('created_at','desc')->get();
        $data = [];
        $sum = 0;
        $data['list'] = [];
        foreach ($logList as $oneLog){
            $arr = [];
            if($oneLog->status == 1){
                $sum += $oneLog['num'];
            }
            $arr['num'] = trim(floatval($oneLog['num']));
            $arr['type'] = trim($oneLog['type']);
            $arr['pay_at'] = date('Y-m-d H:i:s',trim($oneLog['pay_at']));
            $arr['bank_card_no'] = trim($oneLog['bank_card_no']);
            $arr['bank_name'] = trim($oneLog['bank_name']);
            $data['list'][] = $arr;
        }
        $data['total'] = trim($sum);
        return $data;
    }




    /**
     * 判断该合同是否已超额
     */
    public static function isExcess($order, $num)
    {
        $sum = self::where('order_id', $order->id)
            ->where('status', '1')->sum('num');

        if(($sum+$num)>$order->amount){
            return true;
        }

        return false;
    }


    /**
     * 获取某合同已经使用过的优惠
     *
     * @param $contract_id
     * @author tangjb
     */
    public static function  usedDiscount($contract_id)
    {
//        初创红包
//        红包优惠
//        考察订金
//        意向加盟金

        $initial = self::where('contract_id', $contract_id)->where('type', 6)->sum('num');
        $invite = self::where('contract_id', $contract_id)->where('type', 1)->sum('num');
        $intent_brand = self::where('contract_id', $contract_id)->where('type', 9)->sum('num');
        $packet_sum = self::where('contract_id', $contract_id)->whereIn('type', [3,4,5,7])->sum('num');

        $total = $initial+$packet_sum+$invite+$intent_brand;
        $initial = numFormatWithComma($initial);
        $packet_sum = numFormatWithComma($packet_sum);
        $invite = numFormatWithComma($invite);
        $intent_brand = numFormatWithComma($intent_brand);
        $total = numFormatWithComma($total);


        return compact('initial', 'packet_sum', 'invite', 'intent_brand', 'total');
    }


    /**
     * 判断该合同是否已超额
     */
    public static function getRemark($order, $num)
    {
        //        合同：123456（六位合同号）
        //        姓名：张三
        //        品牌：过路人饭团（根据最大字数从头截取）
        //        金额：30000 （当要前支付的金额）
        $items = Items::with('belongsToContract')->where('order_id', $order->id)
            ->where('type', 'contract')->first();

        $data['contract_no'] = mb_substr($items->belongsToContract->contract_no, 0, 4);
        $data['cutomer_name'] = $order->user->realname ? $order->user->realname : $order->user->nickname;
        $data['brand_title'] = str_replace('|', '', $items->belongsToContract->brand->name);
        $data['brand_title'] = mb_substr($data['brand_title'], 0, 4);
        $data['brand_title'] = '测试';

        //合同|123456|姓名|张三|品牌|银盛|金额|30000元
        return "合同|{$data['contract_no']}|姓名|{$data['cutomer_name']}|品牌|{$data['brand_title']}|金额|{$num}元";
    }


    public static function produceNo()
    {
        $no = chr(rand(97, 122)) . chr(rand(97, 122)) . chr(rand(97, 122))
            . time() . chr(rand(97, 122)) . chr(rand(97, 122)) . chr(rand(97, 122));

        return $no;
    }


    /**
     * 改变合同支付状态
     */
    public static function changeStatus($log_no, $num)
    {
        $log  = self::where('order_no', $log_no)->first();
        $log->status = 1;
        $log->save();       //改变子订单
        self::where('order_no', $log_no)->update([
            'status'=>1
        ]);

        $order = Orders::find($log->order_id);

        //判断是否已经完成支付
        $is_over = self::isPayOver($order, $num);

        if($is_over){
            //改变主订单
            $order->status = 'pay';
            $order->save();

            $item = Items::where('order_id', $order->id)->first();
            $item->status = 'pay';
            $item->save();
            $contract = Contract::findOrFail($item->post_id);
            self::addLog($contract);
            //分档累进
            self::commission($contract);
            self::sendSuccessRong($contract);
        }

    }


    /**
     * 查询是否支付完毕
     */
    public static function isPayOver($order, $num)
    {
        $sum = self::where('order_id', $order->id)->where('type', '2')
            ->where('status', '1')->sum('num');

        if ($order->amount <= $sum + $num) {
            return true;
        }

        return false;
    }


    /**
     * 添加跟进日志
     */
    public static function addLog($result)
    {
        //发生时间
        $time = time();
        //修改经纪人客户id
        $agent_custoemr_id = AgentCustomer::where('agent_id', $result->agent_id)
            ->where('uid', $result->uid)
            ->where('status', '0')
            ->value('id');

        if (!$agent_custoemr_id) {
//            return $this->setError('error', '经纪人和客户还没有形成关系，就签合同了，数据异常，请检查~');
            $error = '分销档级，请补充，或联系开发人员,报错行数为'.__LINE__.'日期为'.date('Y-m-d H:i:s').PHP_EOL;
            file_put_contents(config('app.contract_commission_log'), $error, FILE_APPEND);
            throw new \Exception($error);
        }

        //客户跟进尾款到账日志
        AgentCustomerLog::create([
            'agent_customer_id' => $agent_custoemr_id,//经纪人客户id
            'action' => '13',//尾款到账
            'post_id' => $result->id,//合同id
            'remark' => '钱款已完全支付',//拒绝原因
            'brand_id' => $result->brand_id,//品牌id
            'agent_id' => $result->agent_id,//经纪人ID
            'created_at' => $time,//创建时间
            'uid' => $result->uid,//用户id
        ]);
    }


    public static function sendSuccessRong($result)
    {
        $brand_name    = \App\Models\Brand\Entity::where('id', $result->brand_id)->first();
        $customer_name = Agent::where('id', $result->agent_id)->first();

        $extra = [
            'agent_id'    => $result->agent_id,
            'uid'         => $result->uid,
            'brand_id'    => $result->brand_id,
            'contract_id' => $result->id
        ];

        $gain_data = [
            'brand_name' => $brand_name->name,
            'urls'       => shortUrl($_SERVER['HTTP_HOST'] . '/comment/agent?agent_id=' . $result->agent_id . '&uid=' . $result->uid . '&brand_id=' . $result->brand_id . '&contract_id=' . $result->id),
        ];

        $confirm_data = [
            'content' => trans('tui.last_money_success_notice', $gain_data),
            'extra'   => json_encode($extra),
            'user' => [
                'id'   => $customer_name->id,
                'name' => $customer_name->realname,
                'icon' => !empty($customer_name->avatar) ? getImage($customer_name->avatar, 'avatar', '') : getImage('', 'avatar', '')
            ],
        ];

        //发送融云消息
        SendCloudMessage('agent' . $result->agent_id, $result->uid, $confirm_data, 'RC:TxtMsg', '', 'custom');

        //agent_rong_info表里添加发送后融云的消息——进行数据记录
        $_array = [
            'send_id'        => 'agent'. $result->agent_id,
            'receive_id'     => $result->uid,
            'info_type'      => "RC:TxtMsg",
            'content'        => json_encode($confirm_data),
            'channel_type'   => "PERSON",
            'msg_time'       => time(),
            'msgUID'         => 0,
            'sensitive_type' => 0,
            'source'         => 0
        ];

        DB::table('agent_rong_info')->insert($_array);

    }


    /**
     * 合同审核佣金相关
     *
     * @author tangjb
     */
    protected static function commission($item)
    {
//        $quarter_info =self::getQuarter(time());
        //季度结算改成月度结算
        $month_info = date('Y年m月');

        //签订的合同表lab_contract，对应数据状态改为2
        $item->status = 2;
        $item->save();

        //查询经纪人当月业绩 如果没有就创建
        $agentAchievement = AgentAchievement::firstOrCreate([
            'agent_id' => $item->agent_id,
//            'quarter' => $quarter_info[0]
            'month' => $month_info
        ]);


        //更新经纪人业绩表lab_agent_achievement，更新id=10的数据
        $agentAchievement->total_achievement = $agentAchievement->total_achievement + 1;
        $agentAchievement->my_achievement = $agentAchievement->my_achievement + 1;

//        $count = $agentAchievement->total_achievement;
        //成单数不清零，历史累积
        $count = AgentAchievement::where('agent_id', $item->agent_id)->count();

        //给自己上级育成奖励
        Agent::addGrowthReward($item->agent_id);

        //查询档位信息
        $level = CommissionLevel::where('min', '<=', $count)->where('max', '>=', $count)
            ->where('brand_id', $item->brand_id)->first();

        //查询该品牌的所有的档级信息
        $levels = CommissionLevel::where('brand_id', $item->brand_id)->get();


        if (!count($levels)) {
//            return $this->setError('error', 'id为' . $item->brand_id . '的品牌没有定义好单数为' . $count . '分销档级，请补充，或联系开发人员');
            $error = 'id为' . $item->brand_id . '的品牌没有定义好单数为' . $count . '分销档级，请补充，或联系开发人员行数为'.__LINE__.'日期为'.date('Y-m-d H:i:s').PHP_EOL;
            file_put_contents(config('app.contract_commission_log'), $error, FILE_APPEND);
            throw new \Exception($error);
        }

        //签订的合同佣金档位表lab_contract_commission_level，增加对应数据
        foreach ($levels as $k => $v) {
            ContractCommissionLevel::create([
                'contract_id' => $item->id,
                'name' => $v->name,
                'min' => $v->min,
                'max' => $v->max,
                'push_money_type' => $v->push_money_type,
                'commission' => $v->commission,
                'scale' => $v->scale,
            ]);
        }


        //经纪人业绩日志表lab_agent_achievement_log，增加对应数据
        $achieve_log = AgentAchievementLog::create([
            'agent_achievement_id' => $agentAchievement->id,
            'contract_id' => $item->id,
            'agent_id' => $item->agent_id,
        ]);


        if ($level->push_money_type == 1) { //固定金额
            $commission = $level->commission;
        } elseif ($level->push_money_type == 2) { //百分比
            $commission = $item->getCommissionAmount() * $level->scale;
        } else {
//            return $this->setError('error', '提成类型数据有问题, 请联系开发人员');
            $error = '提成类型数据有问题, 请联系开发人员, 行数为'.__LINE__.'日期为'.date('Y-m-d H:i:s').PHP_EOL;
            file_put_contents(config('app.contract_commission_log'), $error, FILE_APPEND);
            throw new \Exception($error);
        }


        if ($commission < 0) {
//            return $this->setError('error', '佣金居然为负数, 请联系开发人员');
            $error = '佣金居然为负数, 请联系开发人员, 行数为'.__LINE__.'日期为'.date('Y-m-d H:i:s').PHP_EOL;
            file_put_contents(config('app.contract_commission_log'), $error, FILE_APPEND);
            throw new \Exception($error);
        }


        $achieve_log->commission = $commission;

        //查询自己和团队本月的成单总数
        $success_contracts = AgentAchievementLog::with('contract')
            ->where('agent_achievement_id', $agentAchievement->id)->get();


        $all_commission = 0;
        foreach ($success_contracts as $m => $n) {
            //去形成这笔日志的当时使用的档级找
            $level = ContractCommissionLevel::
            where('contract_id', $n->contract_id)
                ->where('min', '<=', $count)
                ->where('max', '>=', $count)->first();
            if (!$level) {
//                return $this->setError('error', 'id为' . $n->contract->id . '的合同在ContractCommissionLevel
//                        表中没有定义好单数为' . $count . '分销档级，请联系开发人员，计算自己业绩');

                $error = 'id为' . $n->contract->id . '的合同在ContractCommissionLevel
                        表中没有定义好单数为' . $count .'分销档级，请联系开发人员，计算自己业绩, 行数为'.__LINE__.'日期为'.
                    date('Y-m-d H:i:s').PHP_EOL;
                file_put_contents(config('app.contract_commission_log'), $error, FILE_APPEND);
                throw new \Exception($error);
            }

            if ($level->push_money_type == 1) { //固定金额
                $temp_commission = $level->commission;
            } elseif ($level->push_money_type == 2) { //百分比
                $temp_commission = $n->contract->getCommissionAmount() * $level->scale;
            } else {
//                return $this->setError('error', '提成类型数据有问题, 请联系开发人员');

                $error = '提成类型数据有问题, 请联系开发人员, 行数为'.__LINE__.'日期为'.date('Y-m-d H:i:s').PHP_EOL;
                file_put_contents(config('app.contract_commission_log'), $error, FILE_APPEND);
                throw new \Exception($error);
            }

            $all_commission = $all_commission + $temp_commission;
        }

        $achieve_log->save();


        $all_my_commissions = AgentAchievementLog::where('agent_achievement_id', $agentAchievement->id)->sum('commission');

        //因为在入库的时候会四舍五入，在回来减的时候有可能会导致四舍五入，所以先四舍五入
        $all_commission =    sprintf("%.2f", $all_commission);
        $agentAchievement->my_commission = $all_commission - $agentAchievement->team_commission;
        $agentAchievement->total_commission = $all_commission;
        //结算中的金额（我的佣金-我成单时立即获得的佣金总和）
        $agentAchievement->frozen_commission = $agentAchievement->my_commission - $all_my_commissions;

        $agentAchievement->save();

        $agent_self = Agent::find($item->agent_id);

        $agent_self->currency = $agent_self->currency + $commission;
        $agent_self->save();
        self::hoistLevel($agent_self);

        //佣金日志写入数据
        AgentCurrencyLog::create([
            'agent_id' => $item->agent_id,
            'operation' => 1,
            'num' => $commission,
            'type' => 4,
            'post_id' => $achieve_log->id,
            'currency' => $agent_self->currency
        ]);


        //查询该经纪人所有的上级经纪人
        $superiors = Agent::instance()->getSuperiors($item->agent_id);


        $superior_ids = $superior_usernames = [];
        if ($superiors) {
            $superior_ids = array_pluck($superiors, 'id');//所有的上级id
            $superior_usernames = array_pluck($superiors, 'non_reversible');//所有的上级手机号  todo 这里是获取手机号md5值
        }

        //查询该经纪人的上级经纪人id，如为4
        foreach ($superior_ids as $k => $v) {
            //查看该上级当季度业绩 如果没有就创建
            $superiorAchievement = AgentAchievement::firstOrCreate([
                'agent_id' => $v,
//                'quarter' => $quarter_info[0]
                'month' => $month_info
            ]);

            //上级经纪人业绩日志表lab_agent_achievement_log，增加对应数据
            AgentAchievementLog::create([
                'agent_achievement_id' => $superiorAchievement->id,
                'contract_id' => $item->id,
                'agent_id' => $item->agent_id,
            ]);


            //更新上级经纪人业绩表lab_agent_achievement
            $superiorAchievement->total_achievement = $superiorAchievement->total_achievement + 1;
            $superiorAchievement->team_achievement = $superiorAchievement->team_achievement + 1;



            //查出上级经纪人的本季度订单构成
            $logs = AgentAchievementLog::with('contract')->where('agent_achievement_id', $superiorAchievement->id)->get();
            $agent = Agent::find($v);

            if (count($logs) != $superiorAchievement->total_achievement) {
//                return $this->setError('error', '名称为' . $agent->realname . '经纪人的成单总数数据有问题，请联系开发人员');

                $error = '经纪人的成单总数数据有问题, 行数为'.__LINE__.'日期为'.date('Y-m-d H:i:s').PHP_EOL;
                file_put_contents(config('app.contract_commission_log'), $error, FILE_APPEND);
                throw new \Exception($error);
            }


            //求出每个成单的应该所得佣金
            $all_commission = 0;
            $superior_count = AgentAchievement::where('agent_id', $v)->count();

            //计算升档之后的，该团队应该获得的总佣金
            foreach ($logs as $key => $val) {
                //去形成这笔日志的当时使用的档级找
                $level = ContractCommissionLevel::
                where('contract_id', $val->contract_id)
                    ->where('min', '<=', $superior_count)
                    ->where('max', '>=', $superior_count)->first();


                if (!$level) {
//                    return $this->setError('error', 'id为' . $val->contract->id . '的合同在ContractCommissionLevel
//                        表中没有定义好单数为' . $superiorAchievement->total_achievement . '分销档级，请联系开发人员，计算上级业绩');
                    $error = 'id为' . $val->contract->id . '的合同在ContractCommissionLevel
                        表中没有定义好单数为' . $superior_count .'的分销档级, 行数为'.__LINE__.'日期为'.date('Y-m-d H:i:s').PHP_EOL;
                    file_put_contents(config('app.contract_commission_log'), $error, FILE_APPEND);
                    throw new \Exception($error);

                }

                if ($level->push_money_type == 1) { //固定金额
                    $commission = $level->commission;
                } elseif ($level->push_money_type == 2) { //百分比
                    $commission = $val->contract->getCommissionAmount() * $level->scale;
                } else {
//                    return $this->setError('error', '提成类型数据有问题, 请联系开发人员');
                    $error = '提成类型数据有问题, 行数为'.__LINE__.'日期为'.date('Y-m-d H:i:s').PHP_EOL;
                    file_put_contents(config('app.contract_commission_log'), $error, FILE_APPEND);
                    throw new \Exception($error);
                }

                $all_commission = $all_commission + $commission;
            }

            //因为在入库的时候会四舍五入，在回来减的时候有可能会导致四舍五入，所以先四舍五入
            $all_commission =    sprintf("%.2f", $all_commission);

            //该上级经纪人的直属下属
            $subordinate = Agent::instance()->getDirectlySubordinate($superior_usernames[$k]);

            //这些下属该季度获得的总业绩
            $total_commission = AgentAchievement::where('month', $month_info)
                ->whereIn('agent_id', array_pluck($subordinate, 'id'))->sum('total_commission');

            //此单之后该经纪人应该拥有的个人佣金
            $incre = $all_commission - $total_commission;


            if ($incre < 0) {
//                return $this->setError('error', '数据出现问题，给某个经纪人的团队分佣居然是为负数，请联系开发人员');
                $error = '数据出现问题，给某个经纪人的团队分佣居然是为负数, 行数为'.__LINE__.'日期为'.date('Y-m-d H:i:s').PHP_EOL;
                file_put_contents(config('app.contract_commission_log'), $error, FILE_APPEND);
                throw new \Exception($error);
            }

            $all_my_commissions = AgentAchievementLog::where('agent_achievement_id', $superiorAchievement->id)->sum('commission');
            $superiorAchievement->total_commission = $all_commission;
            $superiorAchievement->team_commission = $total_commission;
            $superiorAchievement->frozen_commission = $incre - $all_my_commissions;
            $superiorAchievement->my_commission = $incre;
            $superiorAchievement->save();


            //给自己上级育成奖励
            Agent::addGrowthReward($v);
        }

        $user = User::where('uid', $item->uid)->first();

        //查询该客户的归属经纪人
        $exist = AgentCustomer::with('agent')->whereIn('source', [1,6,7])->where('uid', $item->uid)->first();

        //给归属的经纪人1000块的额外奖励
        if ($exist) {
            $invite_agent = Agent::find($exist->agent_id);
            $invite_agent->currency = $invite_agent->currency + 1000;
            $invite_agent->save();
            $user->realname ? $name = $user->realname : $name = $user->nickname;
            $brand = Brand::find($item->brand_id);
            //查询是否已经给该经纪人奖励过
            $contract_ids = Contract::where('uid', $item->uid)->lists('id')->toArray();
            $forward = AgentCurrencyLog::where('agent_id', $exist->agent_id)->where('type', 6)->whereIn('post_id', $contract_ids)->first();


            if (!$forward) {
                $log = AgentCurrencyLog::create([
                    'agent_id' => $exist->agent_id,
                    'operation' => 1,
                    'num' => 1000,
                    'type' => 6,
                    'post_id' => $item->id,
                    'currency' => $invite_agent->currency,
                ]);

                $exist->contract_id = $item->id;
                $exist->save();

                $text = [
                    'customer_name' => $name,   //客户名称
                    'customer_zone' => Zone::getCityAndProvince($user->zone_id),   //地区  浙江 杭州
                    'brand_name' => $brand->name,   //品牌名称
                    'brand_slogan' => $brand->slogan,   //品牌slogan
                ];

                $text = trans('notification.contract_compelte', $text);

                $agent_version = config('app.agent_version');
                $url = "webapp/agent/mycharge/datas/{$agent_version}?id={$item->id}&type=6&log_id={$log->id}";

                $content = json_encode(['type' => 'contract_compelte', 'style' => 'url', 'value' => $url]);

                @send_notification('邀请投资人有成单', $text, $content, $exist->agent,
                    null, 1);


                ############### 尾款确定后发送融云消息 zhaoyf ##################
                // $invite_agent
                $_datas = trans('tui.confirm_sign_contract',[
                    'brand_name'  => $brand->name,
                    'agent_name'  => $invite_agent->nickname,
                    'zone_name'   => abandonProvince(\App\Models\Zone\Entity::pidNames([$invite_agent->zone_id]))
                ]);
                $send_notice_result = SendCloudMessage($item->uid, 'agent' . $exist->agent_id,  $_datas, 'RC:TxtMsg', '', true, 'one_user');

                //尾款付过后发送短信进行通知 zhaoyf 2017-12-13 18:50
                self::_sendInform(['id' => $item->id]);
            }
        }

        $url = $_SERVER['HTTP_HOST'] . '/webapp/client/pactdetails/'.config('app.version').'?contract_id=' . $item->id . '&uid=' . $item->uid . '&is_out=1';

        //给客户发送短信
        $res = SendTemplateSMS('contract_tail_pay_customer',
            $user->non_reversible, 'contract_tail_pay_customer',
            [
                'name' => $agent_self->realname,   //经纪人名称
                'zone' => Zone::getCityAndProvince($agent_self->zone_id),   //地区  浙江 杭州
                'shorturl' => substr(shortUrl($url), 7),   //短链接
            ],
            $user->nation_code);


        $refund = $item->getRefund();
        send_transmission(json_encode(['type' => 'refund', 'style' => 'json',
            'value' => [
                'contract_id' => $item->id,
                'money' => $refund,
            ]]),
            $user, null);


        return true;
    }


    /**
     * 获取当前时间所处的季度
     */
    public static function getQuarter($timestamp)
    {
        //定义每季度的开始和结束日期
        $first_qurater = mktime(0, 0, 0, 1, 1, date('Y'));
        $second_qurater = mktime(0, 0, 0, 4, 1, date('Y'));
        $third_qurater = mktime(0, 0, 0, 7, 1, date('Y'));
        $fourth_qurater = mktime(0, 0, 0, 10, 1, date('Y'));
        $fiveth_qurater = mktime(0, 0, 0, 1, 1, date('Y') + 1);

        if ($timestamp >= $first_qurater && $timestamp < $second_qurater) {
            return [date('Y') . '年1月-3月', $first_qurater, $second_qurater];
        } elseif ($timestamp >= $second_qurater && $timestamp < $third_qurater) {
            return [date('Y') . '年4月-6月', $second_qurater, $third_qurater];
        } elseif ($timestamp >= $third_qurater && $timestamp < $fourth_qurater) {
            return [date('Y') . '年7月-9月', $third_qurater, $fourth_qurater];
        } elseif ($timestamp >= $fourth_qurater && $timestamp < $fiveth_qurater) {
            return [date('Y') . '年10月-12月', $fourth_qurater, $fiveth_qurater];
        } else {
//            return $this->setError('error', '日期不在今年的范围内,请联系开发人员');

            $error = '日期不在今年的范围内, 行数为'.__LINE__.'日期为'.date('Y-m-d H:i:s').PHP_EOL;
            file_put_contents(config('app.contract_commission_log'), $error, FILE_APPEND);
            throw new \Exception($error);
        }
    }



    public static function hoistLevel($agent)
    {
        $num = Contract::where('agent_id', $agent->id)->where('status',2)->count();
        $level = AgentLevel::where('min', '<=', $num)->where('max', '>=', $num)->first();
        if($agent->agent_level_id!=$level->id){
            $agent->agent_level_id = $level->id;
            $agent->save();
        }
    }


    /**
     * author zhaoyf
     *
     * 签订合同付完尾款后 发送短信通知
     *
     * @param id    合同ID int
     *
     */
    public static function _sendInform($param)
    {
        $result = Contract::with(['brand' => function($query) {
            $query->where('agent_status', 1)->select('id', 'name');
        }])->where([
            'id'     => $param['id'],   //合同ID
            'status' => 2,              //付完尾款ID
        ])->first();

        //获取签订合同的投资人与经纪人的关系（目前只能是邀请关系）
        if ($result) {
            $gain_result = AgentCustomer::with(['agent' => function($query) {
                $query->where('status', 1)->select('id', 'username','non_reversible');
            }, 'user' => function($query) {
                $query->where('status', 1)->select('uid', 'username','non_reversible', 'realname', 'nickname');
            }])
                ->where('uid', $result->uid)
                ->whereIn('source', [1, 2, 3, 4, 6, 7])
                ->first();

            //对结果进行处理
            if ($gain_result && $result->brand && !is_null($gain_result->agent) && !is_null($gain_result->user)) {

                //发送短信通知
                SendTemplateSMS(
                    'to_join_success_note_inform',          //短信配置key值
                    $gain_result->agent->non_reversible,    //需要接受短信的人手机号
                    'to_join_success_note_inform', [        //模板名称和对应需要传递的值
                        'user_names'  => $gain_result->user->realname ?: $gain_result->user->nickname,
                        'user_tels'   => $gain_result->user->username,
                        'brand_names' => $result->brand->name,
                        'urls'        => substr(shortUrl(config('system.wjtrUrl') . '/webapp/agent/mycharge/datas/_v010005?type=6&id='.$result->id), 7)
                    ]
                );
            };
        }
    }




}
