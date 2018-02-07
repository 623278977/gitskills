<?php namespace App\Services\Version\Agent\WorkSpace;

use App\Services\Version\VersionSelect;
use DB;
use Illuminate\Support\Str;
use Validator;
use App\Models\Agent\Agent;
use App\Models\Agent\Academy\AgentSuggestions;
use App\Models\Agent\Exhibition\WeChat;
use App\Models\User\Praise;
use App\Models\Agent\Exhibition\AgentPoster;
use App\Models\Keywords;
use App\Models\Agent\AgentAd;

/**
 * 经纪人展业夹
 *
 */
class _v010100 extends VersionSelect
{

    /**
     * 首页
     * shiqy
     * */
    public function postIndex($input){
        //获取谏言背景图片
        $data = [];
        $suggest = AgentSuggestions::where('type',2)->where('is_curr',1)->where('status',1)->first();
        $data['suggestions_url'] = getImage($suggest['back_img'],'','');
        $data['date'] = time();
        //获取海报
        $agentPosters = AgentPoster::where('status',1)
            ->orderBy('is_recommend',1)
            ->orderBy('recommend_sort','desc')
            ->orderBy('created_at','desc')
            ->skip(0)->take(10)
            ->get();
        $data['poster'] = [];
        foreach ($agentPosters as $onePoster){
            $arr = [];
            $arr['id'] = trim($onePoster['id']);
            $arr['url'] = getImage($onePoster['img'],'','');
            $data['poster'][] = $arr;
        }
        return ['message'=>$data , 'status'=>true];
    }

    //微信营销列表
    public function postWeChatList($data)
    {
        $request=$data['request'];
//        $page=$request->input('page')?:1;
//        $pageSize=$request->input('page_size')?:10;
        $user=Agent::where('id',$request->input('agent_id'))->select('avatar')->first();
        $avatar=getImage($user['avatar'],'avatar','');

        $builder=WeChat::where('status',1)
            ->orderBy('sort','desc')
            ->orderBy('created_at','desc');
        if($request->input('keywords')){
            $builder->where('keywords',$request->input('keywords'));
        }
        $lists=$builder->get();
//            ->skip(($page-1)*$pageSize)
//            ->take($pageSize)
//            ->paginate($pageSize);
        $arr=[];
        foreach ($lists as $value){
            $arr[]=[  //todo 增加title content share_image share_summary字段的返回 zhaoyf 2018-1-2 17:20
                'id'=>$value->id,
                'title' => strip_tags($value->subject),
                'content' => Str::limit(strip_tags($value->detail, 50)),
                'share_summary' => strip_tags($value->share_summary),
                'summary'=>$value->summary,
                'teacher'=>$value->teacher,
                'teacher_avatar'=>getImage($value->teacher_avatar,'avatar',''),
                'share_image' => $value->effect_image ?  getImage($value->effect_image, '', '') : \Illuminate\Support\Facades\URL::asset('/') . "images/agent-share-logo.png",
                'share_count' => $value->share_count,
                'image' =>getImage($value->effect_image,'','')
            ];
        }
        //分类
        $types=Keywords::where('status',1)
            ->where('type','agent_we_chat')
            ->orderBy('sort', 'desc')
            ->orderBy('created_at', 'desc')
            ->get()
            ->toArray();

        $keywords=[];
        foreach ($types as $v){
            $keywords[]=[
                'id'=>$v['id'],
                'contents'=>$v['contents']
            ];
        }
        //banner图
        $banner=AgentAd::where('status',1)
        ->where('start_time', '<', date('Y-m-d H:i:s'))
        ->where('expired_time', '>', date('Y-m-d H:i:s'))
        ->where('type',6)
        ->first();

        $banner=$banner?getImage($banner['image'],'',''):'';
        $list=[
            'keywords'=>$keywords,
            'data' =>$arr,
            'banner'=>$banner,
            'avatar' =>$avatar
        ];
        return ['message'=>$list,'status'=>true];
    }
    //微信营销详情
    public function postWeChatDetail($data)
    {
        $request=$data['request'];
        $id=$request->input('id');
        if(empty($id)){
            return ['message' => '参数丢失', 'status'=> false];
        }

        $detail=WeChat::where('id', $id)
            ->where('status', 1)
            ->first()->toArray();
        $result=$detail?array(
            'id'=>$detail['id'],
            'title'=>$detail['subject'],
            'contents'=>$detail['detail'],
            'date' =>date('m月d日', $detail['created_at']),
            'teacher'=>$detail['teacher'],
            'teacher_avatar'=>getImage($detail['teacher_avatar'],'avatar',''),
            'share_count' => $detail['share_count'],
            'appellation' => $detail['teacher_intro'],
            'share_image' => $detail['effect_image']?getImage($detail['effect_image'],'',''):\Illuminate\Support\Facades\URL::asset('/') . "images/agent-share-logo.png",
            'share_summary'=>$detail['share_summary']?$detail['share_summary']:$detail['summary'],
        ):[];

        //点赞
        $result['count_zan']     = Praise::ZanCount($id,'we_chat');
        $result['is_zan']        = Praise::where('uid', $request->input('agent_id'))
            ->where('relation', 'we_chat')
            ->where('relation_id', $id)
            ->count() ?  1 : 0;

        //推荐
        $recommend=WeChat::where('id','<>',$id)
            ->where('status', 1)
            ->orderBy('created_at','desc')
            ->limit(3)
            ->get()->toArray();
        $recommend_list=[];
        foreach ($recommend as $value){
            $recommend_list[]=[
                'id'=>$value['id'],
                'title'=>$value['subject'],
            ];
        }
        $result=array_merge(['detail'=>$result,'recommend'=>$recommend_list]);
        return ['message' => $result, 'status'=> true];
    }
    //分享后分享数增加
    public function postWeChatShare($data)
    {
        $request=$data['request'];
        $id=$request['id'];
        $result= WeChat::where('id',$id)->increment('share_count');
        return ['message'=>'','status'=>$result];
    }

    //海报列表
    public function postPosterList($data)
    {
        $request=$data['request'];
        $page=$request->input('page')?:1;
        $pageSize=$request->input('page_size')?:9;

        $builder=AgentPoster::where('status',1)
            ->orderBy('is_recommend','desc')
            ->orderBy('recommend_sort','desc')
            ->orderBy('created_at','desc');
        if($request->input('keywords_id')){
            $builder->where('keywords_id',$request->input('keywords_id'));
        }
        $lists=$builder->skip(($page-1)*$pageSize)
            ->take($pageSize)
            ->paginate($pageSize);
        $arr=[];
        foreach ($lists as $value){
            $arr[]=[
                'id'=>$value->id,
                'title'=>$value->title,
                'image' =>getImage($value->img,'','')
            ];
        }
        //分类
        $types=Keywords::where('status',1)
            ->where('type','agent_poster')
            ->orderBy('sort', 'desc')
            ->orderBy('created_at', 'desc')
            ->get()
            ->toArray();

        $keywords=[];
        foreach ($types as $v){
            $keywords[]=[
                'id'=>$v['id'],
                'contents'=>$v['contents']
            ];
        }
        //banner图
        $banner=AgentAd::where('status',1)
            ->where('start_time', '<', date('Y-m-d H:i:s'))
            ->where('expired_time', '>', date('Y-m-d H:i:s'))
            ->where('type',7)
            ->first();

        $banner=$banner?getImage($banner['image'],'',''):'';
        $list=[
            'keywords'=>$keywords,
            'data' =>$arr,
            'banner'=>$banner
        ];
        return ['message'=>$list,'status'=>true];

    }

    //分享后分享数增加
    public function postPosterShare($data)
    {
        $request=$data['request'];
        $id=$request['id'];
        $result= AgentPoster::where('id',$id)->increment('share_count');

        //return ['message'=>'', 'status' => $result];
        // todo 移动端报错，这里我把status直接改成 true了，移动端那里才能解析 zhaoyf 2018-1-02
        return ['message'=> '', 'status' => true];
    }
    

}