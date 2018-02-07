<?php

namespace App\Services\Version\Agent\Video;

use App\Models\Agent\AgentBrandLog;
use App\Models\Agent\AgentCustomerLog;
use App\Models\Agent\Score\AgentScoreLog;
use App\Services\Version\VersionSelect;
use App\Models\Video\Entity as VideoModel;
use App\Models\Brand\Entity as Brand;
use Illuminate\Support\Facades\Input;
use App\Models\Agent\Entity\_v010200 as Agentv010200;
class _v010000 extends VersionSelect
{
    /*
     * 招商现场详情接口
     *
     * */
    public function postDetail($input){
        $videoId=intval($input['id']);
        if(empty($videoId)){
            return ['message' => "请传递视频id", 'status' => false];
        }
        $data=VideoModel::getDetailById($videoId);
        if(isset($data['error'])){
            return ['message' => $data['message'], 'status' => false];
        }
        return ['message' => $data, 'status' => true];
    }

    /*
    * 品牌学习视频详情接口
    *
    * */
    public function postStudyVideoDetail($input){
        $videoId=intval($input['id']);
        $agent_id=intval($input['agent_id']);
        if(empty($videoId)){
            return ['message' => "请传递视频id", 'status' => false];
        }

        if(empty($agent_id)){
            return ['message' => "请传递经纪人id", 'status' => false];
        }

        $data=VideoModel::getStudyVideoDetailById($videoId);

        //给积分
        Agentv010200::add($agent_id, AgentScoreLog::$TYPES_SCORE[21], 21, '学习视频奖励', $videoId, 1);

        if(isset($data['error'])){
            return ['message' => $data['message'], 'status' => false];
        }
        return ['message' => $data, 'status' => true];
    }

    /*
     * 招商现场列表接口
     *
     * */
    public function postList($input){
        $page=empty($input['page']) ? 1 :intval($input['page']);
        $pageCount=empty($input['page_size']) ? 10 :intval($input['page_size']);
        $hotwords=empty($input['hotwords']) ? "" :trim($input['hotwords']);
        $theList=Brand::getViedoList($page,$pageCount,$hotwords);
        if(isset($theList['error'])){
            return ['message' => $theList['message'], 'status' => false];
        }
        return ['message' => $theList, 'status' => true];
    }


    /**
     * 品牌课程视频打卡列表
     * @User yaokai
     */
    public function postClock($input = [])
    {
        $video_id = $input['video_id'];
        $page_size = Input::input('page_size', 10);

        //相关视频打卡信息
        $data = AgentBrandLog::clockList('video',$video_id,$page_size);


        return ['message' => $data, 'status' => true];
    }


}