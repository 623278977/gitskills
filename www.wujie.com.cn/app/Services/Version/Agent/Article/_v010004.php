<?php
namespace App\Services\Version\Agent\Article;

use App\Models\Agent\Academy\AgentArticlesKnowledge;
use App\Models\Agent\Score\AgentScoreLog;
use App\Models\Keywords;
use App\Services\Version\VersionSelect;
use App\Models\News\Entity as News;
use App\Models\User\Praise;
use App\Models\Comment\Entity as Comment;
use App\Http\utils\randomViewUtil;
use App\Models\Agent\Entity\_v010200 as Agentv010200;


class _v010004 extends VersionSelect
{
    public function postList($data)
    {
        $request=$data['request'];
        $page=$request->input('page')?:1;
        $pageSize=$request->input('size')?:10;
        $keyword=$request->input('keyword');
        $builder=AgentArticlesKnowledge::with('news', 'lecturer')
            ->where('knowledge_type',0)
            ->where('status',1)
            ->orderBy('sort','desc')
            ->orderBy('created_at','desc');
        if($request->input('type')){
            $builder->where('articles_type', $request->input('type'));
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
                'news_id'=>$value['news']['id'],
                'title'=>$value['news']['title'],
                'logo'=>$value['news']['logo']?getImage($value['news']['logo'],'',''):'',
                'summary'=>$value['news']['summary'],
                'author'=>$value['lecturer']['name'],
                'view'=>($value['news']['sham_view'] > $value['news']['view'])?$value['news']['sham_view']:$value['news']['view'],
                'comments'=>Comment::commentsCount('news',$value['news']['id']),
                'zan'=>Praise::ZanCount($value['news']['id']),
                ];
        }
        $result=array_merge(['total'=>$total , 'data'=>$arr]);
        return ['message'=>$result, 'status'=> true ];
    }

    public function postDetail($data)
    {
        $request=$data['request'];
        $id=$request->input('id');
        $agent_id=$request->input('uid');
        $article=AgentArticlesKnowledge::with('lecturer','news')
            ->where('id', $id)
            ->first()->toArray();
        $news_id=$article['news_id'];

        //给积分
        Agentv010200::add($agent_id, AgentScoreLog::$TYPES_SCORE[26], 26, '学习商圈热文或专栏内资讯', $id);

        //浏览量加1
        News::where('id', $article['news_id'])->increment('view');
        //伪浏览量
        $sham_view = News::where('id', $article['news_id'])->value('sham_view') ? : 1;
        $increment = randomViewUtil::getRandViewCount($sham_view);  //增量
        News::where('id', $article['news_id'])->increment('sham_view', $increment);
            $detail=[
                'id'=>$article['id'],
                'news_id'=>$article['news_id'],
                'created_at'=>date('m月d日',$article['created_at']),
                'title'=>$article['news']['title'],
                'contents'=>$article['news']['detail'],
                'banner'=>getImage($article['news']['banner'],'',''),
                'share_image'=>$article['news']['logo']?getImage($article['news']['logo'],'',''):\Illuminate\Support\Facades\URL::asset('/') . "images/agent-share-logo.png",
                'avatar'=>getImage($article['lecturer']['avatar'],'',''),
                'name'=>$article['lecturer']['name'],
                'appellation'=>$article['lecturer']['appellation'],
                'share_summary' =>$article['news']['share_summary']?$article['news']['share_summary']:$article['news']['summary'],
                ];

        $recommend=AgentArticlesKnowledge::with('news')
            ->where('id','<>',$id)
            ->where('status', 1)
            ->orderBy('created_at','desc')
            ->limit(3)
            ->get()->toArray();
        $recommend_list=[];
        foreach ($recommend as $value){
            $recommend_list[]=[
                'id'=>$value['id'],
                'title'=>$value['news']['title'],
                'news_id' => $value['news']['id']
            ];
        }

        $result['count_zan']     = Praise::ZanCount($news_id);
        $result['is_zan']        = Praise::where('uid', $request->input('uid'))
            ->where('relation', 'news')
            ->where('relation_id', $article['news_id'])
            ->count() ?  1 : 0;

        $message=[
            'detail'=>$detail,
            'recommend'=>$recommend_list,
            'zan' =>$result,

        ];
        return ['message'=>$message,'status'=>true];
    }

    public function postTypes()
    {
        $types=Keywords::where('type','agent_article')
            ->where('status',1)
            ->orderBy('sort', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();
        $types_list=[];
        foreach ($types as $v){
            $types_list[]=[
                'id'=>$v->id,
                'contents'=>$v->contents
            ];
        }
        return ['message' => $types_list, 'status' =>true];
    }

    public function postCommentList($data)
    {
        $request=$data['request'];
        $page=$request->input('page')?:1;
        $pageSize=$request->input('size')?:10;
        $article=AgentArticlesKnowledge::where('id', $request->input('id'))->first();
        $comments=Comment::agentComments($article['news_id'],'News',$request->input('uid'), 0, $page, $pageSize);
        return ['message'=>$comments, 'status'=>true];
    }
}