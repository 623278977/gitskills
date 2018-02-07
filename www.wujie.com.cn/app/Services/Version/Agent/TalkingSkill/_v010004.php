<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017-11-15
 * Time: 14:20
 */

namespace App\Services\Version\Agent\TalkingSkill;


use App\Models\Agent\Academy\AgentTalkingSkills;
use App\Models\Agent\AgentAd;
use App\Models\Agent\Score\AgentScoreLog;
use App\Services\Version\VersionSelect;
use App\Models\Agent\Entity\_v010200 as Agentv010200;
class _v010004 extends VersionSelect
{
    //列表
    public function postList($data)
    {
        $request=$data['request'];
        $page=$request->input('page')?:1;
        $pageSize=$request->input('page_size')?:10;
        $lists=AgentTalkingSkills::where('status',1)
            ->orderBy('created_at','desc')
            ->orderBy('sort','desc')
            ->skip(($page-1)*$pageSize)
            ->take($pageSize)
            ->paginate($pageSize);
        $arr=[];
        foreach ($lists as $value){
            $arr[]=[
                'id'=>$value->id,
                'subject'=>$value->subject,
                'view'=>$value->view,
                'audio_len'=>formatAudioLen($value->audio_len),
                'audio_size'=>$value->audio_size.'KB'
            ];
        }
        $banner_type=array_search('agent_talking_skill',AgentAd::$_TYPE);
        $banner=AgentAd::where('type',$banner_type)
            ->where('start_time', '<', date('Y-m-d H:i:s'))
            ->where('expired_time', '>', date('Y-m-d H:i:s'))
            ->where('status', '1')
            ->orderBy('created_at', 'desc')
            ->first();
        $banner=AgentAd::getBase($banner);
        $result=array_merge(['list'=>$arr,'banner'=>$banner]);
        return ['message' => $result, 'status' => true];
    }
    //详情
    public function postDetail($data)
    {
        $request=$data['request'];
        $id=$request->input('id');
        $agent_id=$request->input('agent_id');
        //浏览量加1
        AgentTalkingSkills::where('id', $id)->increment('view');
        $detail=AgentTalkingSkills::where('id', $id)
            ->where('status', 1)
            ->first();

        //给积分
        Agentv010200::add($agent_id, AgentScoreLog::$TYPES_SCORE[28], 28, '学习话术随身听', $id);

        $result=$detail?array(
            'id'=>$detail->id,
            'subject'=>$detail->subject,
            'audio_len'=>date('i:s',$detail->audio_len),
            'audio_url'=>$detail->audio_url,
            'image'=>getImage($detail->image,'',''),
            'description'=>$detail->description,
            'share_summary'=>$detail->share_summary?$detail->share_summary:'最全经纪人话术宝典！一分钟搞定客户，教你快速成单秘诀。',
        ):[];
        return ['message' => $result, 'status'=> true];
    }
}