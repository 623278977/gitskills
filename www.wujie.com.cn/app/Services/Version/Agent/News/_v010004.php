<?php namespace App\Services\Version\Agent\News;


use App\Models\Agent\Score\AgentScoreLog;
use App\Services\Version\VersionSelect;
use App\Models\Agent\Agent;
use App\Models\Agent\BrandAgentCompleteQuiz;
use App\Models\Agent\AgentBrandLog;
use Illuminate\Support\Facades\Input;
use App\Models\News\Entity as News;
use App\Models\Agent\Entity\_v010200 as Agentv010200;

class _v010004 extends _v010000
{

    /*
     * 资讯详情
     * shiqy
     * */
    public function postDetail($param)
    {
        $data = parent::postDetail($param);
        if(!$data['status']){
            return $data;
        }
        $data = $data['message'];
        $agentInfo = Agent::where('status',1)->find(intval($param['agent_id']));
        if(is_object($agentInfo)){
            $data['is_complete'] = '0';
            $isComplete = Agent::isAgentCompleteBrandContent($agentInfo['id'] , 'new',$param['id']);
            $isComplete && $data['is_complete'] = '1';

            //给积分
            Agentv010200::add($param['agent_id'], AgentScoreLog::$TYPES_SCORE[22], 22, '学习资讯奖励', $data['id']);
        }
        return ['message'=>$data ,'status'=>true];
    }


    /**
     * 品牌课程资讯打卡列表
     * @User yaokai
     * @param array $input
     * @return array
     */
    public function postClock($input = [])
    {
        $video_id = $input['news_id'];
        $page_size = Input::input('page_size', 10);

        //相关视频打卡信息
        $data = AgentBrandLog::clockList('news',$video_id,$page_size);


        return ['message' => $data, 'status' => true];
    }







}