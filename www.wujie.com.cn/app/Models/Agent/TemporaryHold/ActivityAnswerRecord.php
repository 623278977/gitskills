<?php namespace App\Models\Agent\TemporaryHold;

use App\Models\RedPacket\RedPacket;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class ActivityAnswerRecord extends Model
{
    //临时活动答题日记记录表
    protected $table = 'activity_answer_record';

    const NUMBER_0 = 0; //数字 0
    const NUMBER_1 = 1; //数字 1
    const NUMBER_2 = 2; //数字 2

    public static $instance = null;

    public static function instance()
    {
        if (is_null(self::$instance)) {
            self::$instance = new self;
        }

        return self::$instance;
    }

    /**
     * author zhaoyf
     *
     * 添加答题日记数据信息
     *
     * @param $agent_id                     经纪人ID
     * @param $temporary_activity_quiz_id   答题题目ID
     * @param $activity_quiz_options_id     答题题目选项ID
     * @param $is_yes                       答题结果
     *
     * @return object | bool
     */
    public function addAnswerRecordDatas($agent_id, $temporary_activity_quiz_id, $activity_quiz_options_id, $is_yes)
    {
        //组织需要添加的数据信息
        $insert_data = [
            'type'       => 1,          //用户类型
            'user_id'    => $agent_id,
            'temporary_activity_quiz_id'         => $temporary_activity_quiz_id, //答题题目表ID
            'temporary_activity_quiz_options_id' => $activity_quiz_options_id,   //题目表对应的选项ID
            'is_yes'     => $is_yes,    //答题结果（1：正确；0：错误）
            'created_at' => time(),     //答题日记数据的创建时间
            'updated_at' => time(),     //答题日记数据的更新时间
        ];

        //进行数据添加
        return self::insert($insert_data);
    }

    /**
     * 根据指定经纪人ID获取答题次数，并返回得到的红包的详情数据结果
     *
     * @param  $agent_id    经纪人ID
     *
     * @return  results
     */
    public function getAssignAgentAnswerNumsAndReturnResult($agent_id)
    {
        //获取经纪人的总答题次数（次数以天作为计算单位）
        $gain_num_result = self::where([
            'type'    => self::NUMBER_1,
            'user_id' => $agent_id,
            'is_yes'  => self::NUMBER_1
        ])
         ->groupBy(DB::raw("FROM_UNIXTIME(created_at, '%Y-%m-%d')"))
         ->count();

        //对获取到的答题次数进行处理：
        //1、根据答题次数获取到对应额度的通用红包
        //2、将对应红包数据存储进经纪人领取红包表里
        //3、返回经纪人具体得到的红包数据信息
        if ($gain_num_result && $gain_num_result > self::NUMBER_0) {
            return RedPacket::assignAgentAnswerNumReturnRedDatas($gain_num_result, $agent_id);
        }

        return null;
    }
}