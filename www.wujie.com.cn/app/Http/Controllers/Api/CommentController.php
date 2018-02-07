<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Comment\Entity as Comment;
use DB;
use App\Models\ScoreLog;

class CommentController extends CommonController
{

    /**
     * 添加评论
     *
     * @param Request $request
     * @return string
     */
    public function postAdd(Request $request)
    {

        $uid = isset($uid) ? $uid : $request->input('uid', 0);
        $post_id = (int)$request->input('post_id', 0);
        $content = $request->input('content','');
        $type = ucfirst($request->input('type'));
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
        if (!in_array($type, ['Activity', 'Live', 'Video','News', 'Opportunity', 'activity', 'live', 'opportunity','video','news' ])) {
            return AjaxCallbackMessage('目标类型只能为Activity, Live, Video,News, Opportunity ,activity, live, video或opportunity', false);
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
            $user = DB::table('user')->where('uid', $uid)->first();
            $nickname = $user->nickname;
        }
        $count = Comment::commentsCount($type, $post_id);
        $audit = $this->getAudit($type, $post_id, $content, $uid);

        $comment = Comment::add($post_id, $uid, $type, $content, $upid, $nickname, $uid_at, $images, $form, $audit);
        $flowerCount = Comment::where('uid', $uid)->where('form', 'flower')->where('created_at','>',time()-60)->count();


        return AjaxCallbackMessage(['count' => $count, 'comment' => $comment, 'flowerCount'=>$flowerCount], true);
    }

    /**
     * 获取状态值
     */
    public function getAudit($type, $post_id, $content, $uid)
    {
        $audit ='pending';
        if($type=='Live'){
            $wall = \DB::table('data_wall')->where('live_id', $post_id)->first();
            $filters = \DB::table('keywords')->where('type', 'filter')->lists('contents');
            $black = 0;
            foreach($filters as $k=>$v){
                str_contains($content, $v) && $black=1;
            }

            if($wall->is_filter==1 && $black){
                $audit ='reject';
            }

            if((($wall->is_filter==1 && !$black) ||$wall->is_filter==0) && $wall->is_manul_audit==0 ){
                $audit ='adopt';
            }

            //是否在黑名单内
            if(str_contains($wall->black_list, $uid)){
                $audit ='reject';
            }
        }
        return  $audit;
    }




    /**
     * 给一个评论点赞或者取消赞
     */
    public function postZhan(Request $request)
    {
        $data = $request->input();
        if (!isset($data['uid']) || $data['uid']==0) {
            return AjaxCallbackMessage('缺少必须参数uid，并且uid不能为0', false);
        }
        if (!isset($data['id'])) {
            return AjaxCallbackMessage('缺少必须参数评论id', false);
        }
        if (!isset($data['type']) || !in_array($data['type'], [1, 0])) {
            return AjaxCallbackMessage('缺少必须参数操作类型type', false);
        }
        $result = Comment::zhan($data['id'], $data['uid'], $data['type']);
        if ($result === true) {
            return AjaxCallbackMessage('操作成功', true);
        } elseif ($result === 1) {
            return AjaxCallbackMessage('你已经点过赞了', false);
        } elseif ($result === 2) {
            return AjaxCallbackMessage('无法取消赞，你没有点过赞', false);
        } else {
            return AjaxCallbackMessage('操作失败', false);
        }
    }

    /**
     * 生成4个不重复的数字
     */
    private function makeNum($arr)
    {
        $num = rand(0, 9) . rand(0, 9) . rand(0, 9) . rand(0, 9);
        if (in_array($num, $arr)) {
            $num = self::makeNum($arr);
        }

        return $num;
    }

    /**
     * 删除一条评论
     */
    public function postDelete(Request $request)
    {
        $data = $request->input();
        $count = Comment::totalComments($data['id']);
        $result = Comment::deleteComment($data['id'], $data['uid']);

        if ($result) {
            return AjaxCallbackMessage(['count' => $count], true);
        } else {
            return AjaxCallbackMessage('删除失败', false);
        }
    }

    /**
     * 获取一个目标的所有评论列表
     */
    public function postList(Request $request)
    {
        $data = $request->input();
        if (!isset($data['type']) || $data['type'] == '') {
            return AjaxCallbackMessage('缺少必须参数目标类型', false);
        }
        if (!isset($data['id'])) {
            return AjaxCallbackMessage('缺少必须参数目标id', false);
        }

        $page = isset($data['page']) ? $data['page'] : 1;
        $size = isset($data['page_size']) ? $data['page_size'] : 10;
        $uid = isset($data['uid']) ? $data['uid'] : 0;
        $section = isset($data['section']) ? $data['section'] : 1;
        $use = isset($data['use']) ? $data['use'] : 'normal';
        $data['pre_id'] = $request->input('pre_id', 0);

        $comments = Comment::comments($data['id'], $data['type'], $uid, $data['pre_id'], $page, $size,1,$section, $use);

        return AjaxCallbackMessage($comments, true);
    }

    /**
     * 获取刷新评论
     */
    public function postFreshList(Request $request)
    {
        $data = $request->input();
        if (!isset($data['type']) || $data['type'] == '') {
            return AjaxCallbackMessage('缺少必须参数目标类型', false);
        }

        if (!isset($data['id'])) {
            return AjaxCallbackMessage('缺少必须参数目标id', false);
        }

        if (!isset($data['fromId'])) {
            return AjaxCallbackMessage('缺少必须参数fromId', false);
        }

        if (!isset($data['fecthSize'])) {
            return AjaxCallbackMessage('缺少必须参数fecthSize', false);
        }

        $uid = isset($data['uid']) ? $data['uid'] : 0;
        $fecthSize = $request->input('fecthSize');
        $update = $request->input('update', 'new');
        $use = $request->input('use', 'normal');
        $fromId = $request->input('fromId');
        //最新  历史
        $comments = Comment::freshComments($data['id'], $data['type'], $uid, $update, $fromId, $fecthSize, $use);

        return AjaxCallbackMessage($comments, true);
    }


    /**
     * 获取单条评论
     */
    public function postSingleComment(Request $request, $version=null)
    {
        $versionService = $this->init(__METHOD__, $version);

        if($versionService){
            $response = $versionService->bootstrap($request->all());
            return AjaxCallbackMessage($response['data'],$response['status']);
        }

        return AjaxCallbackMessage('api接口不再维护',false);
    }


    /**
     * 留言墙刷新评论
     */
    public function postFreshLists(Request $request)
    {
        $data = $request->input();
        if (!isset($data['type']) || $data['type'] == '') {
            return AjaxCallbackMessage('缺少必须参数目标类型', false);
        }

        if (!isset($data['id'])) {
            return AjaxCallbackMessage('缺少必须参数目标id', false);
        }

        if (!isset($data['fromId'])) {
            return AjaxCallbackMessage('缺少必须参数fromId', false);
        }

        if (!isset($data['fecthSize'])) {
            return AjaxCallbackMessage('缺少必须参数fecthSize', false);
        }

        $uid = isset($data['uid']) ? $data['uid'] : 0;
        $fecthSize = $request->input('fecthSize');
        $update = $request->input('update', 'new');
        $use = $request->input('use', 'normal');
        $fromId = $request->input('fromId');
        //最新  历史
        $comments = Comment::freshComments_ly($data['id'], $data['type'], $uid, $update, $fromId, $fecthSize, $use);

        return AjaxCallbackMessage($comments, true);
    }

}