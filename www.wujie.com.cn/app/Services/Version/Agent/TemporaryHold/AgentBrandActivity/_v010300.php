<?php namespace App\Services\Version\Agent\TemporaryHold\AgentBrandActivity;

use App\Models\Agent\RedPacketAgent;
use App\Models\Agent\TemporaryHold\TemporaryActivityQuiz;
use App\Models\Validate;
use App\Services\Version\VersionSelect;

class _v010300 extends VersionSelect
{
    /**
     * 整体思路：
     * 1、从缓存里获取用户值，有——表示今天已经答过了
     * 2、如果没有——开始答题
     * 3、如果第一道题就答错了，缓存里记录用户ID值，设置有效时间，然后今天不能再答题了
     *    3.1、然后答题日记记录表里记录一条数据，最后返回提示
     * 4、如果第一道答对了，返回第二条
     *    4.1、如果第二道题答错了，缓存里设置用户ID值，设置有效时间，今天不能再答题了
     *    4.2、然后日记表里插入答题的两道题的数据，最后返回提示
     * 5、如果前两条答对了，第三道答错了——流程如上，最后返回提示
     * 6、如果三道都答对了，把三道题存入答题日记记录表里
     *    6.1、然后根据当前的经纪人ID在答题日记记录表里，根据日期分组，获取答对的题目
     *    6.2、求出经纪人总共的答题次数
     *    6.3、然后去红包表里，根据经纪人实际答题次数，获取对应次数的额度的答题红包
     *    6.4、设置缓存记录用户的ID值，今天不能再答题了，最后返回红包数据信息
     */

    /**
     * author zhaoyf
     *
     * 返回已经获得答题红包的用户信息
     */
    public function postReturnGetAnswerRedUseDatas()
    {
        $gain_result = RedPacketAgent::instance()->returnGetAnswerRedUseDatas();

        return ['message' => $gain_result, 'status' => true];
    }

    /**
     * author zhaoyf
     *
     * 春节临时活动——答题送红包
     *
     * @param   $agent_id   经纪人ID
     */
    public function postAnswerGiveReds($param)
    {
        $agent_id = $param['agent_id'];

        //处理传递的参数值
        $validate_result = Validate::validateIsNumericOrIsSetOrNoEmpty($agent_id);
        if (!$validate_result) {
            return ['message' => '缺少经纪人ID，且只能为数字值', 'status' => false];
        }

        //获取经纪人是否已经答过题
        $get_result = TemporaryActivityQuiz::instance()->getAgentIsAnswer($agent_id);
        if ($get_result) return ['message' => ['is_answer' => 1], 'status' => true];

        //获取数据结果值
        $gain_result = TemporaryActivityQuiz::instance()->gainActivityAnswerIssue();

        //对结果进行处理
        if (!is_null($gain_result)) {

            //返回结果
            return ['message' => [
                'stem'       => $gain_result['stem'],       //问答题目和描述
                'brand_name' => $gain_result['brand_name'], //品牌名称
                'lists'  => $gain_result['options']],       //问题题目选项
                'status' => true                            //状态值
            ];
        }

        //返回结果
        return ['message' => [], 'status' => true];
    }

    /**
     * author zhaoyf
     *
     * 处理经纪人的活动答题
     *
     * @param  agent_id     经纪人ID
     * @param  temporary_activity_quiz_id 活动问答ID
     * @param  options_num   问答选项ID
     */
    public function postHandleAgentAnswer($param)
    {
        $agent_id   = $param['agent_id'];                     //经纪人ID
        $quiz_id    = $param['temporary_activity_quiz_id'];   //答题题目ID
        $options_id = $param['options_num_id'];               //答题选项ID（用户选择的答案ID）

        //获取经纪人是否已经答过题
        $get_result = TemporaryActivityQuiz::instance()->getAgentIsAnswer($agent_id);
        if ($get_result) return ['message' => ['is_answer' => 1], 'status' => true];

        //处理传递的参数值
        $validate_result = Validate::validateGroupValue([$agent_id, $quiz_id, $options_id]);
        if (!$validate_result) {
            return ['message' => '经纪人ID|答题ID|答题选项ID 都不能空，且都只能为数字值', 'status' => false];
        }

        //获取结果值
        $gain_result = TemporaryActivityQuiz::instance()->gainAnswerQuiz($quiz_id, $options_id, $agent_id);

        //对结果进行判断
        if (!$gain_result) {

            //答过题的用户ID值，进行记录
            TemporaryActivityQuiz::instance()->setAgentCacheDatas($agent_id);

            return ['message' => ['answer_result' => 0], 'status' => false];
        } elseif (is_string($gain_result) && $gain_result == 'all_correct') {

            //答过题的用户ID值，进行记录
            TemporaryActivityQuiz::instance()->setAgentCacheDatas($agent_id);

            $gain_red_result = TemporaryActivityQuiz::instance()->assignAnswerNumReturnReds($agent_id);

            return ['message' => $gain_red_result, 'status' => true];
        } else {    //返回下一题
            if (!is_null($gain_result)) {
                return ['message' => [
                    'stem'       => $gain_result['stem'],       //问答题目和描述
                    'brand_name' => $gain_result['brand_name'], //品牌名称
                    'lists'      => $gain_result['options']],   //问题题目选项
                    'status'     => true                        //状态值
                ];
            }

            //返回结果
            return ['message' => [], 'status' => true];
        }
    }
}