<?php

namespace App\Services\Version\Agent\Video;

use App\Services\Version\VersionSelect;
use Illuminate\Support\Facades\Input;
use App\Models\Agent\BrandAgentCompleteQuiz;
use App\Models\Agent\Agent;
use App\Models\Agent\Academy\AgentLecturers;


class _v010004 extends _v010000
{
    /*
     * 资讯详情
     * shiqy
     * */
    public function postStudyVideoDetail($param)
    {
        $data = parent::postStudyVideoDetail($param);
        if(!$data['status']){
            return $data;
        }
        $data = $data['message'];
        $lecturerInfo = AgentLecturers::where('id',intval($data['lecturers_id']))->where('status',1)->first();
        $arr = null;
        if(is_object($lecturerInfo)){
            $arr['id'] = trim($lecturerInfo['id']);
            $arr['name'] = trim($lecturerInfo['name']);
            $arr['avatar'] = trim($lecturerInfo['avatar']);
            $arr['summary'] = trim($lecturerInfo['summary']);
        }
        $data['lecturers'] = $arr;
        $agentInfo = Agent::where('status',1)->find(intval($param['agent_id']));
        if(is_object($agentInfo)){
            $data['is_complete'] = '0';
            $isComplete = Agent::isAgentCompleteBrandContent($agentInfo['id'] , 'video',$param['id']);
            $isComplete && $data['is_complete'] = '1';
        }
        return ['message'=>$data ,'status'=>true];
    }


}