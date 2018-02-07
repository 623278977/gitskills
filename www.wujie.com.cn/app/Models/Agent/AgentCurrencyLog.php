<?php namespace App\Models\Agent;

use Illuminate\Database\Eloquent\Model;

class AgentCurrencyLog extends Model
{
    protected $table = 'agent_currency_log';

    protected $guarded = [];

    //佣金季度数字格式转换
    protected static $money   = [
        '1' => '一',
        '2' => '二',
        '3' => '三',
        '4' => '四'
    ];

    protected $dateFormat = 'U';

    private static $_instance;


    public static function getInstance()
    {
        if (!isset(self::$_instance)) {
            self::$_instance = new self;
        }
        return self::$_instance;
    }


    public static $_OPERATION = [
        '1' => '+',
        '-1' => '-',
    ];


    /**
     * 关联到提现表
     */
    public function withdraw()
    {

        return $this->belongsTo(AgentWithdraw::class, 'post_id', 'id');
    }


    /**
     * 关联到业绩表
     */
    public function agentAchievement()
    {

        return $this->belongsTo(AgentAchievement::class, 'post_id', 'id');
    }


    /**
     * 关联到业绩日志表
     */
    public function achievementLog()
    {
        return $this->belongsTo(AgentAchievementLog::class, 'post_id', 'id');
    }


    /**
     * 关联：合同
     */
    public function contract()
    {
        return $this->hasOne(Contract::class, 'id', 'post_id');
    }

    /**
     * 关联：经纪人
     */
    public function agents()
    {
        return $this->hasOne(Agent::class,'id', 'agent_id');
    }

    //todo  多态关联
    public function post()
    {
        switch ($this->type) {
            case 1:
                $list = AgentWithdraw::find($this->post_id);
                break;
            case 4:
                $list = AgentAchievement::find($this->post_id);
                break;
            case 5:
                $list = AgentAchievementLog::find($this->post_id);
                break;
            case 6:
                $list = \App\Models\Contract\Contract::find($this->post_id);
                break;
            default:
                $list = new \stdClass();
        }

        return $list;
    }


    /**
     * @param $agent_id
     */
    public function details($agent_id, $page = 1, $page_size = 10)
    {
        $logs = self::where('agent_id', $agent_id)
            ->skip(($page - 1) * $page_size)
            ->take($page_size)
            ->orderBy('created_at', 'desc')
            ->where('type', '<>', 7)
            ->where('type', '<>', 0)
            ->get();


        $details = [];
        foreach ($logs as $k => $v) {
            $detail = $v->post();
            if (in_array($v->type, [6,5])) {//如果是额外奖励就把日志本身传入
                $title = $this->getDetailTitle($v->type, $v);
            } else {
                $title = $this->getDetailTitle($v->type, $detail);
            }

            $created_at = date('Y-m-d H:i:s', $v->created_at->timestamp);
            $num = self::$_OPERATION[$v->operation] . abandonZero($v->num);
            $id = $v->post_id;
            $log_id = $v->id;
            $type = $v->type;
            $details [] = compact('title', 'created_at', 'num', 'id', 'type', 'log_id');
        }


        return $details;
    }


    public function getDetailTitle($type, $data)
    {
        switch ($type) {
            case 1:
//                '转账状态1:等待2,:成功-1: 失败默认1',
                if ($data->status == 1) {
                    return ['title' => '提现', 'remark' => '处理中'];
                } elseif ($data->status == 2) {
                    return ['title' => '提现', 'remark' => '成功'];
                } else {
                    return ['title' => '提现', 'remark' => '失败'];
                }
            case 4:
                return ['title' => '成单提成', 'remark' => ''];
            case 5:
                return ['title' => '团队分佣', 'remark' => $this->getLastMonth($data->created_at->timestamp)];
            case 6:
                return ['title' => '额外奖励', 'remark' => date('Y年m月', $data->created_at->timestamp)];
//                return ['title' => '额外奖励', 'remark' => $this->getQuarter($data->created_at->timestamp)];
            case 7:
                return ['title' => '提现失败，返回余额', 'remark' => $this->$timestamp($data->created_at->timestamp)];
            case 8:
                return ['title' => '团队发展', 'remark' => ''];
            case 9:
                return ['title' => '团队成长', 'remark' => ''];
            case 10:
                return ['title' => '邀约投资人', 'remark' => ''];
            case 11:
            case 12:
            case 13:
                return ['title' => '育成奖励', 'remark' => ''];
            case 14:
                return ['title' => '活动邀约', 'remark' => ''];
            case 15:
                return ['title' => '考察到票', 'remark' => ''];
            case 16:
                return ['title' => '项目入驻', 'remark' => ''];
            case 17:
                return ['title' => '集赞截屏', 'remark' => ''];
        }
    }


    /**
     * 获取当前季度信息
     */
    public function getQuarter($timestamp)
    {
        //定义每季度的开始和结束日期
        $first_qurater = mktime(0, 0, 0, 1, 1, date('Y'));
        $second_qurater = mktime(0, 0, 0, 4, 1, date('Y'));
        $third_qurater = mktime(0, 0, 0, 7, 1, date('Y'));
        $fourth_qurater = mktime(0, 0, 0, 10, 1, date('Y'));
        $fiveth_qurater = mktime(0, 0, 0, 1, 1, date('Y') + 1);

        if ($timestamp >= $first_qurater && $timestamp < $second_qurater) {
            return '（季度：' . date('Y') . '年第1季）';
        } elseif ($timestamp >= $second_qurater && $timestamp < $third_qurater) {
            return '（季度：' . date('Y') . '年第2季）';
        } elseif ($timestamp >= $third_qurater && $timestamp < $fourth_qurater) {
            return '（季度：' . date('Y') . '年第3季）';
        } elseif ($timestamp >= $fourth_qurater && $timestamp < $fiveth_qurater) {
            return '（季度：' . date('Y') . '年第4季）';
        } else {
            return '';
        }
    }


    public function getLastMonth($timestamp)
    {

        $month = date('Y年m月', strtotime(date('Y', $timestamp) . '-' . (date('m', $timestamp) - 1)));

        return $month;
    }


    /**
     * 添加收入
     *
     * @author tangjb
     *
     */
    public static function addCurrency($agent_id, $num, $type, $post_id, $is_unique = false)
    {
        if($num<=0){
            return  false;
        }

        //拉新到12月1号就不要了
        if(in_array($type, [8,9,10]) && time() > strtotime('17-12-01')){
            return false;
        }

        if ($is_unique) {
            if(in_array($type, [11,12,13])){
                $where =                 [
                    'agent_id' => $agent_id,
                    'operation' => 1,
                    'num' => $num,
                    'type' => $type,
                ];
            }else{
                $where =                 [
                    'agent_id' => $agent_id,
                    'operation' => 1,
                    'num' => $num,
                    'type' => $type,
                    'post_id' => $post_id,
                ];
            }


            $exist = self::where(
                $where
            )->first();


            if ($exist) {
                return false;
            }
        }


        $agent = Agent::find($agent_id);
        $agent->currency = $agent->currency + $num;
        $agent->save();

//        Agent::where('id', $agent_id)->increment('currency', $num);

        $log = self::create([
            'agent_id' => $agent_id,
            'operation' => 1,
            'num' => $num,
            'type' => $type,
            'post_id' => $post_id,
            'currency' => $agent->currency,
        ]);

        if(11==$type){
            Agent::where('id', $agent_id)->update(['badge'=>1]);
        }

        if(12==$type){
            Agent::where('id', $agent_id)->update(['badge'=>2]);
        }

        if(13==$type){
            Agent::where('id', $agent_id)->update(['badge'=>3]);
        }

        return $log;
    }

    /**
     * author zhaoyf
     *
     * 根据指定经纪人ID获取季度佣金，然后发送通知信息
     *
     * @param   param    经纪人ID array
     *
     * @return data_list
     */
    public static function sendInform($param)
    {
        $confirm_result = array();

        //获取结算周期数据；例如：2017年1月
        //$quarter_info   = Agent::instance()->getQuarter(time());

        //根据指定经纪人ID获取佣金结算信息
        $_result = self::where([
            'agent_id'  => $param['agent_id'],
            'operation' => 1,
            'type'      => 5,
        ])->get();

        //对结果进行处理，然后返回结果
        if ($_result) {

            //获取佣金对应的数字值
            //$gain_result = self::$money[number_format(ceil((date('n')) / 3))];
            $month_info = date('Y年m月份', strtotime(" -2 day"));

            //组合需要返回的通知数据信息
            foreach ($_result as $key => $value) {
                $confirm_result[$key] = [
                    'time'  => strtotime($value->created_at),
                    'type'  => 'currency_info',
                    'cont'  => [
                        'title'         => "【{$month_info}】佣金结清啦！",
                        'id'            => $value->id,
                        'settle_time'   => $month_info,
                        'settle_scope'  => 'a、促单佣金；b、邀请奖励；c、团队分佣；d、其他奖励',
                        'commission'    => number_format($value->num),
                        'describe'      => trans('notification.commission_describe'),
                    ]
                ];
            }
        }

        return $confirm_result;
    }

    /**
     * author zhaoyf
     *
     * 获取对应佣金数据信息
     *
     * @param agent_id  经纪人ID int
     * @param result    获取数据个数，默认为查询全部 object
     * @param $type     获取数据的过滤条件ID值 array
     *
     * return results
     */
    public function gainAgentToDatas($agent_id = null, $result = 'get', array $type = [6, 14, 15, 17])
    {
        $start_time = strtotime('2017-12-18 00:00:00');
        $end_time   = strtotime('2018-01-08 23:59:59');

        $object_result = self::with('agents')
                         ->whereBetween('created_at', [$start_time, $end_time])
                         ->where('operation', 1)
                         ->whereIn('type', $type);

        //复制一份对象，独立于获取全部经纪人使用
        //$new_object = clone $object_result;

        //根据指定经纪人ID获取数据信息 $mys_result
        //获取全部经纪人相关数据信息   $gather_result
        $mys_result    = $object_result->where('agent_id', $agent_id)->$result();
        //$gather_result = $new_object->orderBy('created_at', 'desc')->$result();

       /* //对获取的结果进行判断
        if ($mys_result && $gather_result) {
            return ['mys' => $mys_result, 'gather' => $gather_result];
        } elseif (isset($mys_result) && $mys_result) {
            return ['mys' => $mys_result, 'gather' => null];
        } elseif (isset($gather_result) && $gather_result) {
            return ['mys' => null, 'gather' => $gather_result];
        }*/

       if ($mys_result) {
           return $mys_result;
       }

        return null;
    }
}