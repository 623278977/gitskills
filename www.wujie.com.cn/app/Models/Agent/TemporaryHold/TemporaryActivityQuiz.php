<?php namespace App\Models\Agent\TemporaryHold;

use App\Models\RedPacket\RedPacket;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class TemporaryActivityQuiz extends Model
{
    //临时活动问答表(存储各种临时需要的活动等)
    protected $table = 'temporary_activity_quiz';

    const ANSWER_NUM = 3;     //答题次数
    const NUMBER_0   = 0;     //数字 0
    const NUMBER_1   = 1;     //数字 1

    public static $instance = null;

    public static function instance()
    {
        if (is_null(self::$instance)) {
            self::$instance = new self;
        }

        return self::$instance;
    }

    //红包的使用场景
    public static $RED_USE_PLACE = [
        1 => '考察抵扣',
        2 => '合同支付抵扣',
        3 => '考察抵扣和合同支付抵扣（二选一）'
    ];

    /**
     * 关联：活动问答题选项
     */
    public function hasManyTemporaryActivityQuizOptions()
    {
        return $this->hasMany(TemporaryActivityQuizOptions::class, 'temporary_activity_quiz_id', 'id');
    }

    /**
     * 关联：品牌
     */
    public function hasOneBrands()
    {
        return $this->hasOne(\App\Models\Brand\Entity::class, 'id', 'brand_id');
    }

    /**
     * author zhaoyf
     *
     * 获取缓存项值
     *
     * @param $agent_id     经纪人ID
     *
     * @return bool
     */
    public function getAgentIsAnswer($agent_id)
    {
        return Cache::has('agent_answer_'.$agent_id);
    }

    /**
     * author zhaoyf
     *
     * 设置缓存项值
     *
     * @param $agent_id     经纪人ID
     *
     * @return result
     */
    public function setAgentCacheDatas($agent_id)
    {
        //设置有效时间
        $valid_time = strtotime(date("Y-m-d 23:59:59"));

        //设置缓存项
        Cache::put('agent_answer_'.$agent_id, 'agent_answer_'.$agent_id, $valid_time);
    }

    /**
     * author zhaoyf
     *
     * 获取活动问答问题
     */
    public function gainActivityAnswerIssue()
    {
        $confirm_data = array();

        $gain_result = self::with(['hasOneBrands' => function($query) {
            $query->select('id', 'name');
        }, 'hasManyTemporaryActivityQuizOptions' => function($query) {
            $query->where('content', '<>', '');
        }])
         ->where('status', self::NUMBER_1)
         ->orderByRaw("RAND()")
         ->first();

        //对结果进行处理
        if ($gain_result && !is_null($gain_result->hasManyTemporaryActivityQuizOptions)) {
           foreach ($gain_result->hasManyTemporaryActivityQuizOptions as $key => $vls) {
                $confirm_data[$key] = [
                    'temporary_activity_quiz_id' => $vls->temporary_activity_quiz_id,
                    'options_num_id' => $vls->options_num,
                    'content'        => $vls->content,
                ];
           }

            //对当前问题的调用次数进行加一
            self::where('id', $gain_result->id)->increment('use_num');

            //获取问题题目和描述
           $stem['stem']       = $gain_result->stem;
           $stem['brand_name'] = $gain_result->hasOneBrands->name ?: '';
           $stem['dec']  = htmlspecialchars_decode($gain_result->answer_dec);

           //返回结果
           return ['stem' => $stem, 'options' => $confirm_data];
        }

        return null;
    }

    /**
     * author zhaoyf
     *
     * 获取活动问答信息
     *
     * @param $id           活动问答ID
     * @param $answer_id    经纪人选择的题目选项ID
     * @param $agent_id     经纪人ID
     *
     * @return array | bool
     */
    public function gainAnswerQuiz($id, $answer_id, $agent_id)
    {
        $gain_result = self::where('id', $id);

        //获取结果值
        $gain_results = $gain_result->first();

        //对答题结果进行处理
        if ($gain_results->answer == $answer_id) {

            //对答题正确的次数进行记录
            $valid_time = strtotime(date("Y-m-d 23:59:59"));
            $answer_num = Cache::increment('agent_answer_nums_'.$agent_id, self::NUMBER_1, $valid_time);

            //对答对问题个数加一
            $gain_result->increment('success_num');

            //如果答对题目个数小于3，继续返回答题，
            //否则进行答题日记数据记录的添加
            //同时根据经纪人的总答题次数返回对应的红包数据
            if ($answer_num < self::ANSWER_NUM) {

                //进行答题日记数据的添加记录（答题正确）
                ActivityAnswerRecord::instance()->addAnswerRecordDatas($agent_id, $id, $answer_id, self::NUMBER_1);

                //返回下一道题
                return $this->gainActivityAnswerIssue();
            } else {
                return 'all_correct';
            }
        } else {

            //进行答题日记数据的添加记录（答题错误）
            ActivityAnswerRecord::instance()->addAnswerRecordDatas($agent_id, $id, $answer_id, self::NUMBER_0);

            return false;
        }
    }

    /**
     * author zhaoyf
     *
     * 获取经纪人答题次数，同时返回获取的红包数据信息
     *
     * @param   $agent_id   经纪人ID
     *
     * @return bool
     */
    public function assignAnswerNumReturnReds($agent_id)
    {
        $return_data = array();

        //获取答题次数，同时返回获取到的红包数据
       $gain_red_result = ActivityAnswerRecord::instance()->getAssignAgentAnswerNumsAndReturnResult($agent_id);

        //获取通用红包的详细数据信息
        if ($gain_red_result) {
            $gain_results = RedPacket::with('brand')
                ->where('id', $gain_red_result->red_packet_id)
                ->first();

            //对结果进行处理
            if ($gain_results) {
                $return_data = [
                    'red_name'         => '新年活动经纪人答题红包',
                    'red_support_type' => self::$RED_USE_PLACE[$gain_results->use_scence],
                    'red_expire_at'    => $gain_results->expire_at,
                    'min_consume'      => $gain_results->min_consume,
                    'red_limit'        => str_replace(".00", '', $gain_results->amount),
                    'is_answer'        => self::NUMBER_1    //答题结果
                ];
            }
        }

        return $return_data;
    }
}