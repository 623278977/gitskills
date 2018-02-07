<?php
namespace App\Services\Version\Agent\Knowledge;

use App\Models\Agent\Academy\AgentArticlesKnowledge;
use App\Models\Agent\AgentAd;
use App\Services\Version\VersionSelect;
use App\Models\Keywords ;
use App\Models\User\Praise;
use App\Models\Comment\Entity as Comment;

class _v010004 extends VersionSelect
{
    public function postList($data)
    {
        $request=$data['request'];
        $page=$request->input('page')?:1;
        $pageSize=$request->input('size')?:10;
        $keyword=$request->input('keyword');
        $builder=AgentArticlesKnowledge::with('news', 'lecturer')
            ->where('status',1)
            ->where('knowledge_type', '<>', 0)
            ->orderBy('sort','desc')
            ->orderBy('created_at','desc');
        if($request->input('type')){
            $builder->where('knowledge_type', $request->input('type'));
        }
        if($keyword){
            $builder->with(['keyword',function ($query) use ($keyword){
                $query->where('content', $keyword);
            }]);
        }
        $articles=$builder->skip(($page-1)*$pageSize)
            ->take($pageSize)
            ->paginate($pageSize);
        $total=$articles->total();
        $arr=[];
        foreach ($articles as $value){
            $arr[]=[
                'id'=>$value->id,
                'title'=>$value->news['title'],
                'logo'=>$value->news['logo'],
                'summary'=>$value->news['summary'],
                'view'=>($value['news']['sham_view'] > $value['news']['view'])?$value['news']['sham_view']:$value['news']['view'],
                'comments'=>Comment::commentsCount('news',$value['news']['id']),
                'author' => $value['lecturer']['name'],
                'zan'=>Praise::ZanCount($value['news']['id']),
            ];
        }
        $result=array_merge(['total'=>$total , 'data'=>$arr]);
        return ['message'=>$result, 'status'=> true ];
    }

    public function postTypes($data)
    {
        $types=Keywords::where('type','agent_knowledge')
            ->where('status', 1)
            ->orderBy('sort','desc')
            ->orderBy('created_at', 'desc')
            ->get();
        $lists=[];
        foreach ($types as $v){
            $lists[]=[
                'id'=>$v->id,
                'contents'=>$v->contents,
                'icon' => url($v->icon)
            ];
        }
        $banner=AgentAd::where('type',5)
            ->where('start_time', '<', date('Y-m-d H:i:s'))
            ->where('expired_time', '>', date('Y-m-d H:i:s'))
            ->where('status', 1)
            ->orderBy('sort','desc')
            ->orderBy('created_at','desc')
            ->first();
        $banner_detail=empty($banner)?[]:[
            'title'     => $banner->title,
            'link_url'  => $banner->link_url,
            'app_url'   => $banner->app_url ,
            'stay_at'   => $banner->stay_at,
            'image'     => url($banner->image)
        ];
        $lists=array_merge(['list'=>$lists,'banner'=>$banner_detail]);
        return ['message'=>$lists, 'status'=>true];
    }

}
