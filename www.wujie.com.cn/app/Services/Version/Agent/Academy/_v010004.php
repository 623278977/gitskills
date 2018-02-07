<?php

namespace App\Services\Version\Agent\Academy;

use App\Models\Activity\Brand;
use App\Models\Agent\Academy\BrandAgentQuiz;
use App\Models\Agent\Academy\BrandQuestionAnswer;
use App\Models\Agent\BrandAgentCompleteQuiz;
use App\Models\Agent\BrandChapter;
use App\Models\Agent\Score\AgentScoreLog;
use App\Services\Version\VersionSelect;
use Illuminate\Support\Facades\Input;
use App\Models\Agent\Academy\Academy;
use App\Models\Agent\Academy\AgentLecturerColumns;
use App\Models\Agent\Academy\AgentClock;
use App\Models\Agent\Academy\AgentSuggestions;
use DB;
use App\Services\Version\Agent\Lesson\_v010004 as LessonController;
use Illuminate\Http\Request;
use App\Models\Agent\BrandCourse;
use App\Models\Agent\Entity\_v010200 as Agentv010200;

class _v010004 extends VersionSelect
{
    public function postIndex($input){
        $nowTime = time();
        $todayStart = strtotime('today');
        $todayEnd = $todayStart + 86400 -1;
        $data = [];
        //获取谏言
        $suggest = AgentSuggestions::where('type',1)->where('is_curr',1)->where('status',1)->first();
        $data['expostulation'] = trim($suggest['content']);
        //打卡
        $isClock = AgentClock::where('created_at','>=',$todayStart)->where('created_at','<',$todayEnd)->first();
        if(!is_object($isClock)){
            AgentClock::create(['agent_id'=>$input['agent_id'] , 'created_at'=>$nowTime]);
        }
        $data['date'] = $nowTime;
        //获取每日推荐
        $data['recommend'] = Academy::getDailyRecommendation();
        //获取专栏内容
        $data['column'] = AgentLecturerColumns::getIndexColumns();
        //获取精品课程
        $data['lesson'] = [];
        $lessonController = new LessonController();
        $request = new Request();
        $lessons = $lessonController->postList(['request'=> $request]);
        if($lessons['status']){
            $data['lesson'] = $lessons['message'];
        }
        return ['message'=>$data , 'status'=>true];
    }

    /**
     * author zhaoyf
     *
     * 学习 —— 题目列表 agent-study-topic-list
     *
     * @param param = [
     *   'brand_id'    => 品牌ID,
     *   'post_id'     => 类型ID（视频  or 咨询）,
     *   'study_type'  => 学习类型（1：视频； 2：咨询）,
     * ]
     *
     * @return Lists
     */
    public function postAgentStudyTopicList($param)
    {
        //获取处理后的参数
        $result = $param['request']->input();

        //获取数据信息
        $confirm_result = BrandAgentQuiz::instance()->agentStudyTopicLists($result);

        //对结果进行处理
        if (!is_null($confirm_result)) {
            foreach ($confirm_result->hasManyBrandQuizOptions as $key => $vls) {
                $return_result[$key] = [
                    'quiz_id'     => $vls->quiz_id,
                    'option_num'  => $vls->options_num,
                    'content'     => $vls->content,
                ];
            }
            $stem = $confirm_result->stem;
        } else {
            $return_result = [];
            $stem = "";
        }

        //返回结果
        return ['message' => [
            'stem'  => $stem,
            'lists' => $return_result], 'status' => true];
    }

    /**
     * author zhaoyf
     *
     * 学习 —— 答题检验 agent-study-check-out
     *
     * @param param = [
     *   'answer_id' => 答案ID,
     *   'issue_id'  => 问题题目ID
     * ]
     *
     * @return bool
     */
    public function postAgentStudyCheckOut($param)
    {
        //获取处理后的参数
        $result = $param['request']->input();

        //对传递的ID进行判断
        if (empty($result['answer_id']) || !is_numeric($result['answer_id'])) {
            return ['message' => '缺少答案ID，且只能为整数值', 'status' => false];
        }
        if (empty($result['issue_id']) || !is_numeric($result['issue_id'])) {
            return ['message' => '缺少问题题目ID，且只能为整数值', 'status' => false];
        }
        if (empty($result['agent_id']) || !is_numeric($result['agent_id'])) {
            return ['message' => '缺少经纪人ID，且只能为整数值', 'status' => false];
        }

        //获取数据信息
        $confirm_result = BrandAgentQuiz::instance()->gainTopicAnswer($result);

        //对结果进行处理
        if (!is_null($confirm_result)) {
            if ($confirm_result->answer === $param['answer_id']) {

                //往数据完成brand_agent_complete_quiz表里插入一条数据
                $res = BrandAgentCompleteQuiz::instance()->addCompleteDatas($result);

                $quizInfo = BrandAgentQuiz::where('id', $result['issue_id'])
                    ->first();
                $courseInfo = BrandCourse::getNextCourse($result['agent_id'] , $quizInfo['brand_id'] , $quizInfo['post_id'] ,$quizInfo['content_type'] );
                //给积分
                if(is_object($res)){
                    Agentv010200::add($result['agent_id'], AgentScoreLog::$TYPES_SCORE[14], 14, '完成品牌学习', $res->id, 1);
                }



                if($courseInfo['status'] == false){
                    return $courseInfo;
                }
                return $courseInfo;
            } else {
                return ['message' => '答题失败', 'status' => false];
            }
        } else {
            return ['message' => '没有查询到相关测试题数据信息', 'status' => false];
        }
    }

    /**
     * author zhaoyf
     *
     * 问答数据列表
     *
     * @param param = [
     *      'brand_id'  => 品牌ID
     * ]
     *
     * @return list
     */
    public function postGainAnswerDatasList($param)
    {
        //获取参数
        $result = $param['request']->input();

        //对传递的品牌ID进行处理
        if (!isset($result['brand_id']) || empty($result['brand_id']) || !is_numeric($result['brand_id'])) {
            return ['message' => '缺少品牌ID，且只能是整数', 'status' => false];
        }

        //获取数据信息
        $confirm_result = BrandQuestionAnswer::instance()->gainAnswerDatasLists($result);

        //对结果进行处理
        if (!is_null($confirm_result)) {
            foreach ($confirm_result as $key => $vls) {
                $return_result[$key] = [
                    'id'       => $vls->id,
                    'question' => $vls->question,
                    'answer'   => $vls->answer,
                    'brand_id' => $vls->brand_id
                ];
            }
        } else {
            $return_result = [];
        }

        //返回结果
        return ['message' => $return_result, 'status' => true];
    }

}