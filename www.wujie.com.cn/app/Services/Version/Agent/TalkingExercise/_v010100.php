<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017-12-5
 * Time: 17:51
 */

namespace App\Services\Version\Agent\TalkingExercise;
use App\Services\Version\VersionSelect;
use App\Models\Agent\Exhibition\TalkingExercise;

class _v010100 extends VersionSelect
{
    public function postList($data)
    {
        $request=$data['request'];
        $page=$request->input('page')?:1;
        $pageSize=$request->input('page_size')?:10;
        $lists=TalkingExercise::where('status',1)
            ->orderBy('created_at','desc')
            ->orderBy('sort','desc')
            ->skip(($page-1)*$pageSize)
            ->take($pageSize)
            ->paginate($pageSize);
        $arr=[];
        foreach ($lists as $value){
            $arr[]=[
                'id'=>$value->id,
                'title'=>$value->title,
            ];
        }
        return ['message'=>$arr,'status'=>true];
    }

    public function postDetail($data)
    {
        $request=$data['request'];
        $id=$request->input('id');
        if(empty($id)){
            return ['message' => '参数丢失', 'status'=> false];
        }
        //浏览量加1
        TalkingExercise::where('id', $id)->increment('view');
        $detail=TalkingExercise::where('id', $id)
            ->where('status', 1)
            ->first()->toArray();
        $result=$detail?array(
            'id'=>$detail['id'],
            'title'=>$detail['title'],
            'detail'=>$detail['detail'],
            'date' =>date('m月d日', $detail['created_at']),
            'share_image'=>\Illuminate\Support\Facades\URL::asset('/') . "images/agent-share-logo.png",
            'share_summary'=>$detail['share_summary']?$detail['share_summary']:strip_tags($detail['detail']),
        ):[];
        return ['message' => $result, 'status'=> true];
    }

}