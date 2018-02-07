<?php namespace App\Models\Agent;

use DB;
use App\Models\Comment\Entity as Comment;
class Comments extends Comment
{
    public static $instance = null;

    public static function instance()
    {
        if (is_null(self::$instance)) {
            self::$instance = new self;
        }

        return self::$instance;
    }

    /**
     *  author zhaoyf
     *
     * 添加评论     根据传递的不同类型来决定添加的什么类型评论
     *
     * @param $param
     *
     * @return Comments|Comment
     */
    public function addNesComments($param, $type = 'News')
    {
        //处理评论的数据内容
        $content = $this->_handleContent($param['content']);

        //对参数进行初始的处理
        $upid    = $param['upid']?: 0;
        $uid_at  = empty($param['uid_at']) ?  [] : (array)$param['uid_at'];
        $images  = empty($param['images']) ?  [] : (array)$param['images'];

       //获取经纪人昵称
        $nickname = DB::table('agent')
            ->where('id', $param['uid'])
            ->first()->nickname;


        $res = Comment::add(
            $param['post_id'],
            $param['uid'],
            $type, $content,
            $upid, $nickname,
            $uid_at, $images,
            'normal', 'adopt'
        );

        //进行评论的添加，返回结果
        return $res;
    }

    /**
     * author zhaoyf
     *
     * 处理评论数据内容
     *
     * @param $contents
     * @return mixed|string
     */
    private function _handleContent($contents)
    {
        $content = mb_convert_encoding($contents, 'utf-16');
        $bin     = bin2hex($content);
        $arr     = str_split($bin, 4);
        $l       = count($arr);

        $str     = '';
        for ($n = 0; $n < $l; $n++) {
            if (isset($arr[$n + 1]) && ('0x' . $arr[$n] >= 0xd800 && '0x' . $arr[$n] <= 0xdbff && '0x' . $arr[$n + 1] >= 0xdc00 && '0x' . $arr[$n + 1] <= 0xdfff)) {
                $H    = '0x' . $arr[$n];
                $L    = '0x' . $arr[$n + 1];
                $code = ($H - 0xD800) * 0x400 + 0x10000 + $L - 0xDC00;
                $str .= '&#' . $code . ';';
                $n++;

            } else {
                $str .= mb_convert_encoding(hex2bin($arr[$n]), 'utf-8', 'utf-16');
            }
        }

        return $str;
    }

    /**
     * author zhaoyf
     *
     * 获取指定资讯的所有评论数据列表
     *
     * @param $data
     * @return array|string
     * @internal param $param
     *
     */
    public function GainAssignNewsCommentList($data)
    {
        //对指定资讯ID进行处理
        if (!is_numeric($data['id']) || !isset($data['id'])) {
            return ['message' => '缺少必须目标参数id,且只能是整数', 'status' => false];
        }

        $page    = isset($data['page'])      ?  $data['page']       : 1;
        $size    = isset($data['page_size']) ?  $data['page_size']  : 10;
        $uid     = isset($data['uid'])       ?  $data['uid']        : 0;
        $section = isset($data['section'])   ?  $data['section']    : 1;

        $data['pre_id'] = $data['pre_id'] ?: 0 ;

        $comments = Comment::agentComments($data['id'], 'News', $uid, $data['pre_id'], $page, $size,1, $section, 'normal');

        return ['message' => $comments, 'status' => true];
    }

    /**
     * author zhaoyf
     *
     * 对指定用户的评论进行赞
     *
     * @param $param
     *
     * @return bool|int|string
     */
    public function AssignUserCommentZan($param)
    {
        return Comment::zhan($param['id'], $param['uid'], $param['type']);
    }

    //添加评论
    public function addComment($param)
    {
        //处理评论的数据内容
        $content = $this->_handleContent($param['content']);

        //对参数进行初始的处理
        $type    = $param['type']?: 'News';
        $upid    = $param['upid']?: 0;
        $uid_at  = empty($param['uid_at']) ?  [] : (array)$param['uid_at'];
        $images  = empty($param['images']) ?  [] : (array)$param['images'];

        //获取经纪人昵称
        $nickname = DB::table('agent')
            ->where('id', $param['uid'])
            ->first()->nickname;

        //进行评论的添加，返回结果
        return Comment::add(
            $param['post_id'],
            $param['uid'],
            $type, $content,
            $upid, $nickname,
            $uid_at, $images,
            'normal', 'adopt'
        );
    }
    //获取评论
    public function CommentList($data)
    {
        //对指定资讯ID进行处理
        if (!is_numeric($data['id']) || !isset($data['id'])) {
            return ['message' => '缺少必须目标参数id,且只能是整数', 'status' => false];
        }

        $page    = isset($data['page'])      ?  $data['page']       : 1;
        $size    = isset($data['page_size']) ?  $data['page_size']  : 10;
        $uid     = isset($data['uid'])       ?  $data['uid']        : 0;
        $section = isset($data['section'])   ?  $data['section']    : 1;
        $type    = isset($data['type'])      ?  $data['type']       : 'News';
        $data['pre_id'] = $data['pre_id'] ?: 0 ;

        $comments = Comment::agentComments($data['id'], $type, $uid, $data['pre_id'], $page, $size,1, $section, 'normal');

        return ['message' => $comments, 'status' => true];
    }
}