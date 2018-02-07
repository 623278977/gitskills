<?php
namespace App\Services\Version\Agent\Lesson;

use App\Models\Agent\Comments;
use App\Models\Agent\Score\AgentScoreLog;
use App\Models\Keywords;
use App\Services\Version\VersionSelect;
use App\Models\Agent\Academy\AgentLessons;
use App\Models\Comment\Entity as Comment;
use App\Models\Agent\Entity\_v010200 as Agentv010200;
class _v010004 extends VersionSelect
{
    public function postList($data, $page = 1, $pageSize = 10)
    {
        $request=$data['request'];
        $keyword=$request->input('keyword');
        $builder=AgentLessons::where('status',1)
            ->orderBy('sort', 'desc')
            ->orderBy('created_at','desc');
        if($request->input('type')){
            $builder->where('type', $request->input('type'));
        }
        if($keyword){
            $builder->with(['keyword',function ($query) use ($keyword){
                $query->where('content', $keyword);
            }]);
        }
        $lessons=$builder->skip(($page-1)*$pageSize)
            ->take($pageSize)
            ->paginate($pageSize);
        $arr=[];
        foreach ($lessons as $value){
            $arr[]=[
                'id'=>$value->id,
                'subject'=>$value->subject,
                'view'=>$value->view,
                'image'=>getImage($value->image,'video',''),
                //'url'=> trim($value->video_url),
            ];
        }

        return ['message' => $arr, 'status' => true];
    }

    public function postDetail($data)
    {
        $request=$data['request'];
        $id = $request->input('id');
        $agent_id = $request->input('uid');

        //对传递ID值进行处理 zhaoyf 2017-12-20 16:40
        if (!isset($id) || empty($id) || !is_numeric($id)) {
            return ['message' => '缺少视频ID，且只能为整数', 'status' => false];
        }

        AgentLessons::where('id', $id)->increment('view');
        $lesson=AgentLessons::with('lecturer')
            ->where('id', $id)
            ->first()->toArray();

        //给积分
        Agentv010200::add($agent_id, AgentScoreLog::$TYPES_SCORE[27], 27, '学习视频课堂或专栏视频', $id, 1);

            $detail=[
                'id'=>$id,
                'subject'=>$lesson['subject'],
                'image'=>getImage($lesson['image'],'video',''),
                'view' => $lesson['view'],
                'url'=> trim($lesson['video_url']),
                'introduce'=>$lesson['introduce'],
                'lecturer_avatar'=>$lesson['lecturer']['avatar'],
                'lecturer_name'=>$lesson['lecturer']['name'],
                'lecturer_summary'=>$lesson['lecturer']['summary'],
                'share_summary'=> !empty($lesson['share_summary'])? extractText($lesson['share_summary']) : extractText($lesson['introduce']),
            ];

        $recommend=AgentLessons::where('id','<>',$id)
            ->where('status', 1)
            ->where('lecturer_id',$lesson['lecturer_id'])
            ->orWhere('lecturer_id','<>',$lesson['lecturer_id'])
            ->orderBy('created_at','desc')
            ->limit(2)
            ->get()->toArray();
        $arr=[];
        foreach ($recommend as $value){
            $arr[]=[
                'id'=>$value['id'],
                'subject'=>$value['subject'],
                'image'=>getImage($value['image'],'video',''),
                //'url'=> trim($value['video_url']),
                'view'=> $value['view'],
            ];
        }
        //$comments=Comment::agentComments($id,'Lesson');
        $result=[
            'detail'=>$detail,
            'recommend'=>$arr,
            //'comments'=>$comments
        ];
        return ['message'=>$result, 'status'=>true];
    }

    public function postTypes()
    {
        $types=Keywords::where('type','agent_lesson')
            ->where('status',1)
            ->orderBy('sort', 'desc')
            ->get();
        $types_list=[];
        foreach ($types as $v){
            $types_list[]=[
                'id'=>$v->id,
                'contents'=>$v->contents
            ];
        }
        return ['message' =>$types_list, 'status' =>true];
    }


}