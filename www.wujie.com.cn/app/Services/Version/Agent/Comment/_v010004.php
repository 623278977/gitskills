<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017-11-22
 * Time: 15:41
 */

namespace App\Services\Version\Agent\Comment;
use Illuminate\Http\Request;
use App\Models\Comment\Entity as Comment;
use DB;

class _v010004 extends _v010001
{
    public function postAddComment($data)
    {

        $request=$data['request'];
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
        if (!in_array($type, ['Activity', 'Live', 'Video','News', 'Opportunity','Lesson' ,'activity', 'live', 'opportunity','video','news','lesson' ])) {
            return AjaxCallbackMessage('目标类型只能为Activity, Live, Video,News, Opportunity ,Lesson,activity, live, video或opportunity, lesson', false);
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
        $count = Comment::commentsCount($type, $post_id);

        $audit = $this->getAudit($type, $post_id, $content, $uid);

        $comment = Comment::add($post_id, $uid, $type, $content, $upid, $nickname, $uid_at, $images, $form, $audit);
        //$flowerCount = Comment::where('uid', $uid)->where('form', 'flower')->where('created_at','>',time()-60)->count();

        return ['message'=>'评论成功', 'status'=>'true'];
        //return AjaxCallbackMessage(['count' => $count, 'comment' => $comment, 'flowerCount'=>$flowerCount], true);
    }

    /**
     * 获取状态值
     */
    public function getAudit($type, $post_id, $content, $uid)
    {
        $audit ='adopt';
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
     * 生成4个不重复的数字
     */
    protected function makeNum($arr)
    {
        $num = rand(0, 9) . rand(0, 9) . rand(0, 9) . rand(0, 9);
        if (in_array($num, $arr)) {
            $num = self::makeNum($arr);
        }

        return $num;
    }

    /**
     * 获取一个目标的所有评论列表
     */
    public function postCommentList($param)
    {
        $data = $param['request'];
        if (!isset($data['type']) || $data['type'] == '') {
            return ['message'=>'缺少必须参数目标类型', 'status'=>false];
        }
        if (!isset($data['id'])) {
            return ['status'=>'缺少必须参数目标id', 'status'=>false];
        }

        $page = isset($data['page']) ? $data['page'] : 1;
        $size = isset($data['page_size']) ? $data['page_size'] : 10;
        $uid = isset($data['uid']) ? $data['uid'] : 0;
        $section = isset($data['section']) ? $data['section'] : 1;
        $use = isset($data['use']) ? $data['use'] : 'normal';
        $data['pre_id'] = $data->input('pre_id', 0);

        $comments = Comment::agentComments($data['id'], $data['type'], $uid, $data['pre_id'], $page, $size,1,$section, $use);

        return ['message'=>$comments, 'status'=>true];
    }
}