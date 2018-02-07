<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017-11-27
 * Time: 14:08
 */

namespace App\Services\Version\Agent\Comment;

use App\Models\Agent\Score\AgentScoreLog;
use \DB;
use App\Models\Comment\Zhan;
use App\Models\Comment\Entity;
use App\Models\Agent\Agent;
use App\Models\Agent\Comments;
use App\Models\Agent\Entity\_v010200 as Agentv010200;

class _v010005 extends _v010004
{
    public function postReplyList($data)//用户评论回复及点赞记录
    {
        $request=$data['request'];
        $uid=$request->input('agent_id');
        if(empty($uid)){
            return ['message'=>'请传递用户id', 'status'=>false];
        }
        $page=$request->input('page')?$request->input('page'):1;
        $page_size=$request->input('page_size')?$request->input('page_size'):10;
        //获取用户评论
        $comments=Entity::where('uid', $uid)
            ->select('id')
            ->whereIn('type',['News','Lesson','new_agent_detail'])
            ->get();
        $comments_id=[];
        foreach ($comments as $v){
            $comments_id[]=$v['id'];
        }
        if(empty($comments_id)){
            return ['message'=>[], 'status'=>true];
        }
        //获取赞
        $zan=Zhan::where('status', 1)
            ->select('id','created_at',DB::raw("'zan' as type"))
            ->whereIn('comment_id', $comments_id);
        //获取子评论
        $sub_comments=Entity::whereIn('upid', $comments_id)
            ->select('id', 'created_at',DB::raw("'reply' as type"));
        //混合排序分页
        $sequence=$zan->union($sub_comments)
            ->skip(($page-1)*$page_size)
            ->take($page_size)
            ->orderBy('created_at','desc')
            ->get()->toArray();

        $return = array_map($this->formatData(), $sequence);

        return ['message'=>$return ,'status'=>true];
    }

    private function formatData()
    {
        $func = function ($obj){
            $arr_obj=(array)$obj;
            switch ($arr_obj['type']){
                case 'zan':
                    $arr_obj=$this->getZanData($obj);
                    break;
                case 'reply':
                    $arr_obj=$this->getSubCommentData($obj);
                    break;
                default:
                    break;
            }

            return $arr_obj;
        };
        return $func;
    }

    private function getZanData($obj)
    {
        $zan_id=$obj['id'];
        $zan_detail=Zhan::with('comment','agent')
            ->where('id',$zan_id)
            ->first();
        $data['id']=$zan_id;
        $data['created_at']=$obj['created_at'];
        $data['type']=$obj['type'];
        $data['nickname']=$zan_detail['agent']['nickname'];
        $data['avatar']=getImage($zan_detail['agent']['avatar'], 'avatar');
        $data['comment']=$zan_detail['comment']['content'];
        $data['comment_id']=$zan_detail['comment']['id'];

        //跳转链接
        switch ($zan_detail['comment']['type']){
            case 'News':
                $data['url']=url('/webapp/agent/headline/detail/_v010200?agent_id='.$zan_detail['comment']['uid'].'&id='.$zan_detail['comment']['post_id']);
                break;
            case 'Lesson':
                $data['url']=url('/webapp/agent/videoclass/detail/_v010200?agent_id='.$zan_detail['comment']['uid'].'&id='.$zan_detail['comment']['post_id']);
                break;
            case 'new_agent_detail':

                break;
            case 'WeChat':
                $data['url']=url('/webapp/agent/wechatdetail/detail/_v010200?agent_id='.$zan_detail['comment']['uid'].'&id='.$zan_detail['comment']['post_id']);
                break;
            default:
                break;
        }

        return $data;
    }

    private function getSubCommentData($obj)
    {
        $reply_id=$obj['id'];
        $comment_detail=Entity::with('agent')->where('id',$reply_id)->first();
        $upid=$comment_detail['upid'];
        $pre_comment=Entity::where('id',$upid)->first();
        $data['id']=$reply_id;
        $data['created_at']=$obj['created_at'];
        $data['type']=$obj['type'];
        $data['nickname']=$comment_detail['nickname'];
        $data['avatar']=getImage($comment_detail['agent']['avatar'],'avatar');
        $data['reply']=$comment_detail['content'];
        $data['my_nickname']=$pre_comment['nickname'];
        $data['comment']=$pre_comment['content'];
        $data['comment_id']=$upid;
        //跳转链接
        switch ($comment_detail['type']){
            case 'News':
                    $data['url']=url('/webapp/agent/headline/detail/_v010005?agent_id='.$pre_comment['uid'].'&id='.$comment_detail['post_id']);
                break;
            case 'Lesson':
                $data['url']=url('/webapp/agent/videoclass/detail/_v010005?agent_id='.$pre_comment['uid'].'&id='.$comment_detail['post_id']);
                break;
            case 'new_agent_detail':

                break;
            case 'WeChat':
                $data['url']=url('/webapp/agent/wechatdetail/detail/_v010005?agent_id='.$pre_comment['uid'].'&id='.$comment_detail['post_id']);
                break;
            default:
                break;
        }
        return $data;
    }
    //新版本增加透传
    public function postAddComment($data)
    {

        $request=$data['request'];
        $uid = isset($uid) ? $uid : $request->input('uid', 0);
        $post_id = (int)$request->input('post_id', 0);
        $content = $request->input('content','');
        $type = $request->input('type');
        $form = $request->input('form','normal');
        if($form == 'flower' && $type=='Live'){
            $content='送上一束花，对直播点赞';
        }
        if($form == 'flower' && $type=='Video'){
            $content='送上一束花，对视频点赞';
        }
        $content = mb_convert_encoding($content, 'utf-16');
        $bin = bin2hex($content);
        $arr = str_split($bin, 4);
        $l = count($arr);
        $str = '';
        for ($n = 0; $n < $l; $n++) {
            if (isset($arr[$n + 1]) && ('0x' . $arr[$n] >= 0xd800 && '0x' . $arr[$n] <= 0xdbff && '0x' . $arr[$n + 1] >= 0xdc00 && '0x' . $arr[$n + 1] <= 0xdfff)) {
                $H = '0x' . $arr[$n];
                $L = '0x' . $arr[$n + 1];
                $code = ($H - 0xD800) * 0x400 + 0x10000 + $L - 0xDC00;
                $str.= '&#' . $code . ';';
                $n++;
            } else {
                $str.=mb_convert_encoding(hex2bin($arr[$n]),'utf-8','utf-16');
            }
        }
        $content=$str;
//        $content = json_encode(urlencode($content));
        $upid = (int)$request->input('upid', 0);
        $uid_at = (array)$request->input('uid_at', []);
        $images = (array)$request->input('images', []);
        if (empty($content) && count($images)==0) {
            return AjaxCallbackMessage('请填写内容或者至少上传一张图片', false);
        }
        if ($type == '') {
            return AjaxCallbackMessage('请填写评论目标类型', false);
        }
        if (!in_array($type, ['Activity', 'Live', 'Video','News', 'Opportunity','Lesson' ,'new_agent_detail','WeChat' ])) {
            return AjaxCallbackMessage('目标类型只能为Activity, Live, Video,News, Opportunity ,Lesson ,new_agent_detail, WeChat', false);
        }
        if ($post_id == 0) {
            return AjaxCallbackMessage('评论目标id不能为空', false);
        }

        if (($form == 'flower' || $form=='reward') && !$uid) {
            return AjaxCallbackMessage('未登录用户不能送花或打赏', false);
        }

        if ($uid == 0) {
            $nicknames = DB::table('comment')->where('type', $type)->where('post_id', $post_id)
                ->where('uid', 0)->lists('nickname');
            $arr = array();
            foreach ($nicknames as $k => $v) {
                $arr[] = substr($v, -4);
            }
            $num = $this->makeNum($arr);
            $nickname = '游客' . $num;
        } else {
            $user = DB::table('agent')->where('id', $uid)->first();
            $nickname = $user->nickname;
        }
        //$count = Entity::commentsCount($type, $post_id);

        $audit = $this->getAudit($type, $post_id, $content, $uid);

        $comment = Entity::add($post_id, $uid, $type, $content, $upid, $nickname, $uid_at, $images, $form, $audit);
        //$flowerCount = Comment::where('uid', $uid)->where('form', 'flower')->where('created_at','>',time()-60)->count();
        if(!$comment){
            return ['message'=>'评论失败', 'status'=>false];
        }
        if($upid !==0){
            $agent_id=Entity::where('id',$upid)->first()->uid;
            $agent=Agent::where('id', $agent_id)->first();
            if($agent){
                $trans=json_encode([
                    'type'=>'comment_message',
                    'style' =>'json',
                    'value' =>[
                        'title'=>$nickname.'回复了你的评论，快去看一下吧！',
                        'sendTime'=>time(),
                    ]
                ]);
                send_transmission($trans, $agent, null, 1);
            }
        }
        return ['message'=>'评论成功', 'status'=>true];
    }
    //新版本增加透传
    public function postAssignUserCommentAddZan($param)
    {
        $result = $param['request']->input();

        //对传递的数据值进行验证
        $validator_result = \Validator::make($param['request']->input(), [
            'uid' => 'required|integer',
            'id'  => 'required|integer',
        ],[
            'required' => ':attribute为必填项',
        ], [
            'uid' => '当前登录用户ID',
            'id'  => '评论ID',
        ]);

        //对验证结果进行处理
        if ($validator_result->fails()) {
            return ['message' => $validator_result->messages(), 'status' => false];
        }

        //进行评论点赞处理
        $zan_result = Comments::instance()->AssignUserCommentZan($result);

        if ($zan_result) {
            $zan_status=DB::table('comment_zhan')->where('comment_id', $result['id'])->where('uid', $result['uid'])->first()->status;
            if($zan_status==1){
                $agent_id=Entity::where('id',$result['id'])->first()->uid;
                $agent=Agent::where('id', $agent_id)->first();
                if($agent){
                    $trans=json_encode([
                        'type'=>'comment_message',
                        'style' =>'json',
                        'value' =>[
                            'title'=>'赞了你的评论，快去看一下吧！',
                            'sendTime'=>time(),
                        ]
                    ]);
                    send_transmission($trans, $agent, null, 1);
                }
            }
            return ['message' => '操作成功', 'status' => true];
        } elseif ($zan_result == self::CONFIRM_ZAN) {
            return ['message' => '你已经点过赞了', 'status' => false];
        } elseif ($zan_result == self::NOT_ZAN) {
            return ['message' => '无法取消赞，你没有点过赞', 'status' => false];
        } else {
            return ['message' => '操作失败', 'status' => false];
        }
    }
    //新版本增加透传
    public function postNewsAddComment($param)
    {
        $result = $param['request']->input();
        $result = Comments::instance()->addNesComments($result);

        //对评论结果进行处理
        if ($result) {
            $upid=$result['upid'];
            if($upid !==0){

                $agent_id=Entity::where('id',$upid)->first()->uid;
                $agent=Agent::where('id', $agent_id)->first();
                if($agent){
                    $trans=json_encode([
                        'type'=>'comment_message',
                        'style' =>'json',
                        'value' =>[
                            'title'=>$result['nickname'].'回复了你的评论，快去看一下吧！',
                            'sendTime'=>time(),
                        ]
                    ]);
                    send_transmission($trans, $agent, null, 1);
                }
            }

            //给积分
            Agentv010200::add($param['uid'], AgentScoreLog::$TYPES_SCORE[23], 23, '对资讯留言', $param['post_id']);

            return ['message' => '评论成功', 'status' => true];
        } else {
            return ['message' => '评论失败', 'status' => false];
        }
    }
}