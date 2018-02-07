<?php
/**活动模型
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/5/5
 * Time: 11:10
 */

namespace App\Models\Contract;

use App\Models\Agent\Agent;
use App\Models\Agent\AgentAchievementLog;
use App\Models\Agent\AgentCustomer;
use App\Models\Agent\AgentScore;
use App\Models\Agent\AgentCustomerLog;
use App\Models\Agent\Invitation;
use App\Models\Brand\BrandContract;
use App\Models\Brand\Entity as Brand;
use App\Models\Orders\Items;
use App\Models\RedPacket\RedPacketPerson;
use App\Models\User\Entity as User;
use App\Models\User\Fund;
use \DB;
use App\Models\Admin\Entity as Admin;
use Illuminate\Database\Eloquent\Model;
use App\Models\Orders\Entity as Orders;
use App\Models\Brand\Entity\V020800;

class Contract extends Model
{

    const CONTRACT_CONSENT_1 = 1;   //合同付过首款
    const CONTRACT_CONSENT_2 = 2;   //合同付过尾款
    const CONTRACT_REJECT    = -1;  //拒绝合同

    public $timestamps = true;

    protected $table = 'contract';

    protected $dateFormat = 'U';

    protected static $one_month = 2592000;//86400*30一个月

    public static $instance = null;

    //已经支付完成的状态
    public static $hasPayStatus = [2,4,5];

    public static function getInstance()
    {
        if (is_null(self::$instance)) {
            self::$instance = new self;
        }

        return self::$instance;
    }


    //黑名单
    protected $guarded = [];

    //关联品牌合同模板
    public function brand_contract()
    {
        return $this->hasOne(BrandContract::class, 'id', 'brand_contract_id');
    }

    //关联品牌
    public function brand()
    {
        return $this->hasOne(Brand::class, 'id', 'brand_id');
    }

    //关联用户
    public function user()
    {
        return $this->hasOne(User::class, 'uid', 'uid');
    }

    //关联用户
    public function orders_items()
    {
        return $this->hasMany(Items::class, 'product_id', 'id')->where('type','contract');
    }

    //关联经纪人
    public function agent()
    {
        return $this->hasOne(Agent::class, 'id', 'agent_id');
    }

    //关联创业基金
    public function user_fund()
    {
        return $this->hasOne(Fund::class, 'id', 'fund_id');
    }

    //关联创业基金
    public function red_packet()
    {
        return $this->hasOne(RedPacketPerson::class, 'id', 'fund_id');
    }

    //关联邀请函
    public function invitation()
    {
        return $this->hasOne(Invitation::class, 'id', 'invitation_id');
    }

    //关联经纪人用户日记
    public function hasOneAgentCustomerLog()
    {
        return $this->hasOne(AgentCustomerLog::class, 'agent_id', 'id');
    }

    /*
     * 关联管理员表
     * */
    public function admin()
    {
        return $this->hasOne(Admin::class, 'username', 'auditor');
    }

    /*
     * 关联经纪人评价表
     * */
    public function agent_score()
    {
        return $this->hasOne(AgentScore::class, 'contract_id', 'id');
    }


    /*
 * 关联业绩日志
 * */
    public function achievementLog()
    {
        return $this->hasOne(AgentAchievementLog::class, 'contract_id', 'id');
    }


    /**
     * 跟据相关参数获取合同相关信息
     * @User yaokai
     * @param $agent_id 经纪人id
     * @param $brand_id 品牌id
     * @param $uid  投资人id
     * @param $status -1拒绝 0待签订 1已首付 2已签订
     * return $builder
     */
    public static function contractInfo($agent_id, $brand_id, $uid, $status = '2')
    {
        $builder = self::where('agent_id', $agent_id)
            ->where('uid', $uid)
            ->where('brand_id', $brand_id)
            ->where('status', $status)
            ->first();

        return $builder;
    }

    /**
     * 经纪人/投资人相关合同的数量
     * @User yaokai
     * @param $id 经纪人/投资人id
     * @param $type agent经纪人 wjsq投资人
     * return $count  array
     *      completes 已签订数
     *      waits     待签订数
     *      cancels   拒绝数
     */
    public static function ContractCount($id, $type = 'agent')
    {
        if ($type == 'agent') {
            $builder = self::where('agent_id', $id);
        } elseif ($type == 'wjsq') {
            $builder = self::where('uid', $id);
        } else {
            $builder = self::where('agent_id', $id);
        }

        $waits_builder = clone $builder;
        $cancels_builder = clone $builder;

        //已接受  // todo 020902需求需要： 只要签订的状态都要返回 zhaoyf 2018-2-1
        $completes = $builder->whereIn('status', [1, 2, 3, 4, 5])->count();

        //待签订 // todo 020902需求需要： 只要签订的状态都要返回 zhaoyf 2018-2-1
        $waits = $waits_builder->whereIn('status', [0, 6])->count();

        //拒绝
        $cancels = $cancels_builder->where('status', '-1')->count();

        $count = [
            'completes' => "$completes",
            'waits' => "$waits",
            'cancels' => "$cancels",
        ];

        return $count;

    }

    /**
     * 经纪人相关合同详情
     * @User yaokai
     * @param $agent_id 经纪人id
     * @param $status -1拒绝 0待签订 1已签订
     * @param $uid 用户id
     * @param $contract_id 用户id
     * return
     */
    public static function ContractDetail($agent_id, $status = '', $uid = '', $brand_id = '', $contract_id = '', $is_total = false)
    {
        //签订信息
        $builder = self::with(
            ['user' => function ($query) {
                $query->select('uid', 'realname', 'nickname', 'gender');
            }, 'brand' => function ($query) {
                $query->select('id', 'name');
            }, 'brand_contract' => function ($query) {
                $query->select('id', 'name', 'amount', 'address', 'pre_pay', 'brand_contract_no', 'league_type','league_type_id');
            }, 'agent' => function ($query) {
                $query->select('id', 'realname','nickname','is_public_realname');
            }, 'red_packet' => function ($query) {
                $query->select('id', 'amount');
            }, 'invitation' => function ($query) {
                $query->select('id', 'default_money', 'pay_time', 'use_red_packet')
                    ->whereIn('status',[1,2,3]);
            }, 'agent_score' => function ($query) {
                $query->select('id', 'contract_id');
            }]
        );

        if ($agent_id) {
            $builder->where('agent_id', $agent_id);
        }

        if ($contract_id) {
            $builder->where('id', $contract_id);
        }

        if (isset($status)) {
            //状态1,2都为已接受
            if ($status == '1' || $status == '2') { // todo 增加了3，4，5，6返回状态 zhaoyf 2018-1-31
                $builder->whereIn('status', [1, 2, 3, 4, 5]);
            } elseif ($status == '-1' ||  $status == '-2') {
                $builder->whereIn('status', ['-1','-2']);
            }elseif($status == '0') {
                $builder->whereIn('status', [0, 6]);
            }
        }else{
            $builder->whereNotIn('status', ['-3','-4']);
        }

        //针对品牌
        if ($brand_id) {
            $builder->where('brand_id', $brand_id);
        }

        if ($uid) {
            $builder->where('uid', $uid);
        }
        $item = $builder->orderBy('created_at', 'desc')->get()->toArray();


        //格式化数据
        $ret = [];
        foreach ($item as $k => $v) {
            $ret[$k]['id'] = $v['id'];//合同id
            $ret[$k]['brand_id'] = $v['brand']['id'];//品牌id
            $ret[$k]['brand'] = $v['brand']['name'];//品牌名称
            $ret[$k]['brand_contract_no'] = $v['brand_contract']['brand_contract_no'];//模板合同号
            $ret[$k]['league_type'] = $v['brand_contract']['league_type']; // todo 0103版本需求需要返回加盟类型 zhaoyf 2018-1-23 下午
            $ret[$k]['type'] = $v['brand_contract']['league_type_id']; // todo 0103版本需求需要返回加盟类型 zhaoyf 2018-1-23 下午
            $ret[$k]['contract_no'] = $v['contract_no'];//实际合同号
            $ret[$k]['uid'] = $v['user']['uid'];//投资人id
            $ret[$k]['nickname'] = $v['user']['nickname'];//投资人昵称
            $ret[$k]['realname'] = $v['user']['realname'];//投资人真实姓名
            $ret[$k]['gender'] = $v['user']['gender'];//投资人性别  0女 1男
            $ret[$k]['agent_id'] = $v['agent']['id'];//经纪人id
            $ret[$k]['agent_name'] = $v['agent']['is_public_realname']?$v['agent']['realname']:$v['agent']['nickname'];//经纪人真实姓名
            $ret[$k]['contract_title'] = $v['brand_contract']['name'];//合同名称
            $ret[$k]['amount'] = numFormatWithComma(abandonZero($v['amount']));//合同总额
            $ret[$k]['pre_pay'] = numFormatWithComma(abandonZero($v['pre_pay']));//首付金额
            $ret[$k]['tail_pay'] = numFormatWithComma(abandonZero($v['amount'] - $v['pre_pay']));//剩余尾款补齐金额
            $ret[$k]['remark'] = $v['remark'];//备注
            $ret[$k]['status'] = $v['status'];//合同状态
            $ret[$k]['created_at'] = $v['created_at'];//创建时间
            $ret[$k]['address'] = $v['brand_contract']['address'];//合同内容
            $ret[$k]['invitation'] = numFormatWithComma(abandonZero($v['invitation']['default_money'])) ?: '0';//定金抵扣


            if($v['invitation'] && $v['invitation']['pay_time'] +3600*24*30 <time() && $v['invitation']['use_red_packet'] ){
                $ret[$k]['invitation_expired'] = 1;//是否过期
            }else{
                $ret[$k]['invitation_expired'] = 0;//是否过期
            }

            if ($v['status'] != '0') {//拒绝或签订时返回
                $ret[$k]['confirm_time'] = $v['confirm_time'];//拒绝或签订时返回确认时间
            }   // todo 0103版本需求需要返回多个合同状态 zhaoyf 2018-1-23 下午
            if (in_array($v['status'], [1, 2, 3, 4, 5])) {//相关合同首付订单信息
                $order = Items::getOrderInfo('contract', $v['id']);
                $ret[$k]['pay_at'] = $order['orders']['pay_at'];//首付支付时间
                $ret[$k]['first_amount'] = numFormatWithComma(abandonZero($order['orders']['amount']));//首付实际支付
                $ret[$k]['order_no'] = $order['orders']['order_no'];//订单号
                $pay_way = $order['orders']['pay_way'];
                //转换
                $ret[$k]['pay_way'] = Items::pay_way($pay_way);//首付支付方式
                $ret[$k]['buyer_id'] = '';//首付支付账号取不到  返回空

                $ret[$k]['fund'] = numFormatWithComma(abandonZero($v['red_packet']['amount']));//首付使用基金数
                if ($v['status'] == '1') {
                    $ret[$k]['first_pay_status'] = '支付成功';//首付状态
                    $ret[$k]['tail_pay_status'] = '未支付';//尾款状态
                    $ret[$k]['tail_leftover'] = $order['orders']['pay_at'] + self::$one_month;//尾款等待时间
                } else {
                    $ret[$k]['first_pay_status'] = '支付成功';
                    $ret[$k]['tail_pay_status'] = '已结清';
                    $ret[$k]['is_score'] = $v['agent_score']['id'] ? '1' : '0';//是否已评分 1已评 0未评
                    $ret[$k]['bank_no'] = bankFormat($v['bank_no']);//尾款银行卡号
                    $ret[$k]['auditor'] = $v['auditor'];//尾款确认人
                    $ret[$k]['bank_name'] = $v['customer_bank_name'];//尾款银行名
                    $ret[$k]['tail_pay_at'] = $v['tail_pay_at'];//尾款到账时间
                }

                $ret[$k]['company_bank_no'] = config('bank.company_bank_no');//接受合同尾款的公司银行账号
                $ret[$k]['company_bank_name'] = config('bank.company_bank_name');//接受合同尾款的公司银行名
                $ret[$k]['company_name'] = config('bank.company_name');//接受合同尾款的公司名称
            }

            // todo 020902需求提出：需要返回的订单号，zhaoyf 2018-2-1 下午
            if ($v['status'] == 6) {
                $order = Items::getOrderInfo('contract', $v['id'], ['pay', 'npay']);
                $ret[$k]['order_no'] = $order['orders']['order_no'];//订单号
            }

            if ($v['status'] == 0) { // todo 状态等于 0 或者等于 6 的时候也需要返回订到号，020902需求提出的 zhaoyf 2018-2-1 下午
                $order = Items::getOrderInfo('contract', $v['id'], ['pay', 'npay']);
                $ret[$k]['order_no'] = $order['orders']['order_no'];//订单号
                $leftover = self::IsTimeout($v['created_at'], true);//有效时间
                if ($leftover){
                    $ret[$k]['leftover'] = $leftover;
                }else{
                    $ret[$k]['leftover'] = '已超时';
                    //超时修改相关数据
                    Contract::where('id',$v['id'])->update(['status'=>'-2','remark'=>'超时10天自动拒绝']);
                    $ret[$k]['status'] = '-2';
                    $ret[$k]['remark'] = '超时10天自动拒绝';//备注
                }
            }
        }
        //判断是否是针对品牌的合同(品牌，或合同id时不用统计数量 )
        if ($is_total) {
            $data['conreact'] = $ret;
            $data['totals'] = count($ret);
        } else {
            $data = $ret;
        }


        return $data;

    }

    /**
     * 判断合同是否超时（默认10天超时）
     * @User yaokai
     * @param $time 合同创建时间
     * @param $format 是否格式化
     * return
     *
     */
    public static function IsTimeout($time, $format = false)
    {
        //十天的时间
        $ten_time = 10 * 24 * 3600;
        //为正数则没有超时
        $seconds = ($time + $ten_time) - time();
        if ($format) {
            if ($seconds < 86400 && $seconds > 0) {//如果不到一天
                $format_time = gmstrftime('%H时%M分', $seconds);
            } elseif ($seconds > 86400) {
                $time = explode(' ', gmstrftime('%j %H %M %S', $seconds));//Array ( [0] => 04 [1] => 14 [2] => 14 [3] => 35 )
                $format_time = '还剩' . ($time[0] - 1) . '天' . $time[1] . '时' . $time[2] . '分';
            } else {
                $format_time = '';
            }
        } else {

            return $seconds;
        }
        return $format_time;
    }


    /**
     * 找出尾款超时未支付的合同（默认30天超时）
     * @User yaokai
     * @param
     * return
     *
     */
    public static function tailIsTimeout()
    {
        //一个月30天的时间
        $month = self::$one_month;
        //当前时间离一个月之前的时间
        $time = time()-$month;

        //尾款超时未支付的相关合同信息
        $contracts = self::with(
            ['brand' => function($query){
                $query->select('id','name');
            }, 'user' => function($query){
                $query->select('uid','nickname','realname','zone_id');
            }, 'user.zone' => function($query){
                $query->select('id','name');
            }])
            ->where('confirm_time','<=',$time)
            ->where('status','1')
            ->get();

//         $agent_ids = array_column($contracts->toArray(),'agent_id');

         //合同关联的经纪人
//         $agents = Agent::whereIn('id',$agent_ids)->get();

        return $contracts;
    }


    /**
     * 经纪人跟单提醒--合同状态列表显示
     *
     * @param  $agent_id
     *
     * @return contract_list|array|string
     */
    public static function contractNotice($agent_id)
    {
        //集合结果
        $gather_result = self::with(
            ['user' => function ($query) {
                $query->select('uid', 'avatar', 'realname', 'nickname');
            }, 'brand' => function ($query) {
                $query->select('id', 'name');
            }, 'agent' => function ($query) {
                $query->select('id', 'realname');
            }, 'user_fund' => function ($query) {
                $query->select('id', 'fund')
                    ->where('status', 'used');
            }, 'invitation' => function ($query) {
                $query->select('id', 'default_money')
                    ->whereIn('status', [1, 2]);
            }]
        )
            ->where('agent_id', $agent_id)
            ->whereIn('status', [-2, -1, 1, 2])
            ->orderBy('created_at', 'desc')
            ->get()
            ->toArray();

        // 数字状态说明
        // -1 合同拒绝; 1：已付首款; 2：已付尾款

        $ret = array();
        foreach ($gather_result as $k => $v) {
            if ($v['status'] == self::CONTRACT_REJECT) {
                $ret[$k]['id']          = $v['id'];                        //合同id
                $ret[$k]['brand']       = $v['brand']['name'];             //品牌名称
                $ret[$k]['contract_no'] = $v['contract_no'];               //合同号
                $ret[$k]['uid']         = $v['user']['uid'];               //投资人id
                $ret[$k]['nickname']    = $v['user']['nickname'];          //投资人昵称
                $ret[$k]['realname']    = $v['user']['realname'];          //投资人真实姓名
                $ret[$k]['avatar']      = getImage($v['user']['avatar'], 'avatar', '');   //投资人头像
                $ret[$k]['contract_title'] = $v['name'];                   //合同名称
                $ret[$k]['amount']         = number_format($v['amount']);  //合同总额
                $ret[$k]['address']        = $v['address'];                //合同内容
                $ret[$k]['remark']         = $v['remark'];                 //拒绝说明
                $ret[$k]['confirm_time']   = $v['confirm_time'];           //确定时间
                $ret[$k]['confirm_day']    = date("Y/m/d", $v['confirm_time']);           //确定时间
                $ret[$k]['status']         = $v['status'];                 //合同状态
                $ret[$k]['type']           = 3;                           //区分类型
            }
            if ($v['status'] == self::CONTRACT_CONSENT_1 || $v['status'] == self::CONTRACT_CONSENT_2) {
                $order = Items::getOrderInfo('contract', $v['id']);

                $ret[$k]['id']             = $v['id'];                         //合同id
                $ret[$k]['brand']          = $v['brand']['name'];              //品牌名称
                $ret[$k]['contract_no']    = $v['contract_no'];                //合同号
                $ret[$k]['uid']            = $v['user']['uid'];                //投资人id
                $ret[$k]['nickname']       = $v['user']['nickname'];           //投资人昵称
                $ret[$k]['realname']       = $v['user']['realname'];           //投资人真实姓名
                $ret[$k]['avatar']         = getImage($v['user']['avatar'], 'avatar', '');   //投资人头像
                $ret[$k]['contract_title'] = $v['name'];                    //合同名称
                $ret[$k]['amount']         = number_format($v['amount']);   //合同总额
                $ret[$k]['confirm_time']   = $v['confirm_time'];            //确定时间
                $ret[$k]['confirm_day']    = date("Y/m/d", $v['confirm_time']);
                $ret[$k]['pre_pay']        = number_format($v['pre_pay']);  //首付金额
                $ret[$k]['status']         = $v['status'];                  //合同状态
                $ret[$k]['created_at']     = $v['created_at'];              //创建时间
                $ret[$k]['pay_time']       = $order['orders']['pay_at'];    //支付时间
                $ret[$k]['address']        = $v['address'];                 //合同内容
                $ret[$k]['pay_way']        = !empty(Items::pay_way($order->orders->pay_way)) ?  Items::pay_way($order->orders->pay_way) : ''; //支付方式
                $ret[$k]['default_money']  = $v['invitation']['default_money'] ? number_format($v['invitation']['default_money']) : 0;//定金抵扣
                $ret[$k]['fund']           = number_format($v['user_fund']['fund']);           //创业基金抵扣额度
                $ret[$k]['indeed_money']   = self::_calculateMoney($v['pre_pay'], '', 'first', $v['user_fund']['fund'], $v['invitation']['default_money']); //实际支付
                $ret[$k]['first_pay_status'] = $order->status == "pay" ? '已支付' : '未支付';   //首付状态
                $ret[$k]['buyer_id']         = !empty($order->orders->buyer_id) ? $order->orders->buyer_id : '';
                $ret[$k]['tail_pay_status']  = $v['status'] == 1 ? '未支付' : '已支付';         //尾款支付状态
                $ret[$k]['type']             = 3;    //区分类型
                if ($v['status'] == self::CONTRACT_CONSENT_2) {
                    $ret[$k]['band_no']            = substr_replace($v['bank_no'], '******', 4, -4);
                    $ret[$k]['tail_pay_at']        = $v['tail_pay_at'];
                    $ret[$k]['customer_bank_name'] = $v['customer_bank_name'];
                    $ret[$k]['auditor']            = $v['auditor'];
                }
                $ret[$k]['last_money'] = self::_calculateMoney($v['pre_pay'], $v['amount'], 'last');  //尾款额度
            }
        }
        return $ret ?  $ret : '';
    }

    /**
     * author zhaoyf
     *
     * 计算首付实际支付的额度和尾款剩余额度
     *
     * @param  $chiefly_money   首付额度
     * @param  $amount          合同额度
     * @param  $tags            收款和尾款标记
     * @param  $fund_money      创业基金额度
     * @param  $default_money   支付的定金额度
     *
     * @return money_result
     */
    private static function _calculateMoney($chiefly_money, $amount, $tags, $fund_money = null, $default_money = null)
    {
        if ($tags == "first") {
            $result_money = ($chiefly_money - $fund_money - $default_money);
            if ($result_money <= 0) {
                $result_money = 0.00;
            }
        } elseif ($tags == "last") {
            $result_money = ($amount - $chiefly_money);
        }

        return number_format($result_money);
    }


    /**
     * 生成合同号
     *
     * @author tangjb
     */
    public function produceNo($contract_id)
    {
        $contract = self::with('brand_contract')->where('id', $contract_id)->first();

        if ($contract->contract_no) {
            return $contract->contract_no;
        }
        $count = self::where('contract_no', 'like', '%'.$contract->brand_contract->brand_contract_no.'%')
            ->count();
        $contract_no = $contract->brand_contract->brand_contract_no . '-' . date('Y') . str_pad($count, 5, 0, STR_PAD_LEFT);

        return $contract_no;
    }

    /**
     * author zhaoyf
     *
     * 签订合同发送短信通知
     *
     * @param id    合同ID
     *
     */
    /*public function sendInform($param)
    {
        $result = self::where([
            'id'     => $param['id'],
            'status' => 1,
        ])
         ->select('agent_id', 'uid', 'brand_id')
         ->first();

        //获取签订合同的投资人与经纪人的关系（目前只能是邀请关系）
        if ($result) {
            $gain_result = AgentCustomer::with(['belongsToAgent' => function($query) {
                $query->where('status', 1)->select('id', 'username');
            }, 'user' => function($query) {
                $query->where('status', 1)->select('uid', 'username', 'realname', 'nickname');
            }, 'brand' => function($query) {
                $query->where('agent_status', 1)->select('id', 'name');
            }])
             ->where([
                 'agent_id' => $result->agent_id,
                 'uid'      => $result->uid,
                 'brand_id' => $result->brand_id,
             ])
             ->whereIn('source', [1, 2, 3, 4, 6, 7])
             ->first();

            //对结果进行处理
            if ($gain_result && !is_null($gain_result->belongsToAgent)
                && !is_null($gain_result->user)
                && !is_null($gain_result->brand) )
             {
                //发送短信通知
                SendTemplateSMS(
                    'to_join_success_note_inform',
                    $gain_result->belongsToAgent->username,
                    'to_join_success_note_inform', [
                        'user_names'    => $gain_result->user->realname ?: $gain_result->user->nickname,
                        'user_tels'     => $gain_result->user->username,
                        'brand_names'   => $gain_result->brand->name,
                        'urls'          => ''
                    ]
                );

                return ['status' => true];
            };
        }
    }*/

    /**
     * author zhaoyf
     *
     * 经纪人相关合同详情 020902 版本迭代 拒绝 和 待确认
     *
     * @param $agent_id     经纪人id
     * @param $status       -1拒绝  0待签订
     * @param $uid          用户id
     * @param $contract_id  用户id
     *
     * @return arrays
     */
    public static function ContractDetails($agent_id, $status = '', $uid = '', $brand_id = '', $contract_id = '', $is_total = false)
    {
        //合同相关的集合信息
        $builder = self::with(['user' => function ($query) {
                $query->select('uid', 'realname', 'nickname', 'gender');
            }, 'brand' => function ($query) {
                $query->select('id', 'name', 'categorys1_id', 'slogan');
            }, 'invitation' => function ($query) {
                $query->whereIn('status', [1, 2])
                      ->select('id', 'default_money');
            }, 'brand.categorys1' => function($query) {
                $query->select('id', 'name');
            }, 'brand_contract' => function ($query) {
                $query->select('id', 'name', 'amount', 'address', 'pre_pay', 'brand_contract_no', 'league_type');
            }, 'brand_contract.brandContractCost' => function($query) {
                $query->where('is_delete', 0)
                      ->orderBy('sort', 'asc')
                      ->select('brand_contract_id', 'cost_type', 'cost_limit', 'is_commission');
            }, 'agent' => function ($query) {
                $query->select('id', 'realname', 'nickname', 'is_public_realname');
            }]
        );

        //经纪人ID存在时,增加判断条件
        if ($agent_id) $builder->where('agent_id', $agent_id);

        //合同ID存在时,增加判断条件
        if ($contract_id) $builder->where('id', $contract_id);


        //用户ID存在时,增加判断条件
        if ($uid) $builder->where('uid', $uid);

        //状态存在时，增加判断条件
        if (isset($status) && $status != '') {
            if ($status == -1) { $ss = [-1, -2];
            } elseif ($status == 0) { $ss = 0; }
            $builder->whereIn('status', $ss);
        } else {
            $builder->whereNotIn('status', [-3, -4]);
        }

        $item = $builder->orderBy('created_at', 'desc')->first();

        if(!$item) return false;

        //进行数据整合
        $ret = [];
        $ret['id']          = $item['id'];                              //合同id
        $ret['brand_id']    = $item['brand']['id'];                     //品牌id
        $ret['brand_name']  = $item['brand']['name'];                   //品牌名称
        $ret['slogan']      = $item['brand']['slogan'];                 //品牌短语
        $ret['brand_cate']  = $item['brand']['categorys1']['name'];     //品牌分类

        $ret['contract_title']  = $item['brand_contract']['name'];          //合同名称
        $ret['league_type']     = $item['brand_contract']['league_type'];   //加盟类型
        $ret['address']         = $item['brand_contract']['address'];       //合同内容
        $ret['amount']          = number_format($item['brand_contract']['amount']);  //合同总费用
        //$ret['commission_deduct'] = V020800::instances()->getMaxCommission($item['brand']['id'], true); //佣金提成：百分比显示

        $ret['uid']             = $item['user']['uid'];                     //投资人id
        $ret['nickname']        = $item['user']['nickname'];                //投资人昵称
        $ret['realname']        = $item['user']['realname'];                //投资人真实姓名
        $ret['gender']          = $item['user']['gender'];                  //投资人性别  0女 1男
        $ret['agent_id']        = $item['agent']['id'];                     //经纪人id
        $ret['agent_name']      = $item['agent']['is_public_realname'] ? $item['agent']['realname'] : $item['agent']['nickname']; //经纪人姓名
        //$ret['invitation']    = $item['invitation']['default_money'] ? number_format($item['invitation']['default_money']) : 0;//定金抵扣

        $red_packet = RedPacketPerson::getAllDiscount($item->uid, $item->brand_id, $item->amount);
        $ret['initial_packet']  = $red_packet['initial'];
        $ret['packet_sum']      = $red_packet['packet_sum'];
        $ret['invite_packet']   = $red_packet['invite'];
        $ret['intent_packet']   = $red_packet['intent_brand'];
        $ret['total_packet']    = $red_packet['total'];

        //获取合同模板费用
        if (!is_null($item->brand_contract->brandContractCost)) {
            foreach ($item->brand_contract->brandContractCost as $key => $vls) {
                $ret['cost'][$key] = [
                    'cost_type' => $vls->cost_type,
                    'cost_limit' => number_format($vls->cost_limit),
                ];
            }
        }

        //如果合同的状态为已经支付的返回订单号
        if (in_array($item['status'], [1, 2, 3, 4, 5, 6])) {
            $order = Items::getOrderInfo('contract', $item['id'], ['pay', 'npay']);

            $ret['order_no'] = !empty($order['orders']) ?  $order['orders']['order_no'] : '';  //订单号
        }

        $ret['status']      = $item['status'];                       //合同状态
        $ret['created_at']  = $item['created_at']->getTimestamp();   //创建时间
        $ret['contract_no'] = $item['contract_no'];                  //合同编号

        //合同待确认时
        if ($item['status'] == 0) {
            $leftover = self::IsTimeout(strtotime($item['created_at']), true); //有效时间

            if ($leftover) {
                $ret['leftover'] = $leftover;
            } else {
                $ret['leftover'] = '已超时';

                //超时修改相关数据
                Contract::where('id', $item['id'])->update(['status'=>'-2', 'remark' => '超时10天自动拒绝']);
                $ret['status'] = '-2';
                $ret['remark'] = '超时10天自动拒绝'; //备注

            }
        }

        //合同被拒绝时，显示拒绝时间和拒接理由
        if ($item['status'] == -1) {
            $ret['confirm_time'] = $item['confirm_time'];  //拒绝时返回确认时间
            $ret['remark']       = $item['remark'];        //拒绝理由
        }

//判断是否是针对品牌的合同(品牌，或合同id时不用统计数量 )
//        if ($is_total) {
//            $data['conreact'] = $ret;
//            $data['totals'] = count($ret);
//        } else {
//            $data = $ret;
//        }

        return $ret;
    }

    public static  function isCommented($contractId, $uid)
    {
        $res = AgentScore::where('contract_id', $contractId)->where('customer_id', $uid)->first();

        return $res ?1 :0;
    }


    /**
     * 获取某合同需要返回的金额
     *
     * @return mixed
     * @author tangjb
     */
    public function getRefund()
    {
        $sum =  ContractPayLog::where('status', 1)->where('contract_id', $this->id)
            ->whereIn('type', ContractPayLog::$_REFUND_TYPES)
            ->sum('num');

        return $sum;
    }


    /**
     * 获取该合同真正计入佣金计算的amount
     *
     * @return mixed
     * @author tangjb
     */
    public function getCommissionAmount()
    {
        $amount = BrandContractFee::where('contract_id', $this->id)->where('is_commission', 1)->sum('cost_limit');

        if(!$amount){
            return $this->amount;
        }
    }

}