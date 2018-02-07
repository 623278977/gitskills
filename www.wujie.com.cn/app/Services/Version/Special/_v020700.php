<?php

namespace App\Services\Version\Special;


use App\Services\Version\VersionSelect;
use Validator;
use \DB;
use App\Models\User\Praise;
use App\Models\Comment\Entity as Comment;
class _v020700 extends VersionSelect
{

    /*
     * 资讯详情
     */
    public function postDetail($param = [])
    {
        $data = $param['result'];
        //品牌数据处理
        if(!empty($data['brands'])){
            foreach ($data['brands'] as $k=>&$v){
                if (empty($v['brand_summary'])) {
                    $v['brand_summary'] = mb_substr(preg_replace('/\s+/i','',str_replace('&nbsp;','',strip_tags($v['detail']))),0,50);
                }
                //删除无用字段的返回
                unset($v['detail']);
            }
        }else{
            $data['brands'] = [];
        }


        //资讯数据处理
        foreach ($data['news'] as $k=>&$v){
            if (empty($v['summary'])) {
                $v['summary'] = mb_substr(preg_replace('/\s+/i','',str_replace('&nbsp;','',strip_tags($v['detail']))),0,50);
            }
            //点赞
            $v['count_zan'] = Praise::ZanCount($v['id']);
            //评论
            $v['count_comment'] = Comment::ConmmentCount($v['id'],'News');
            //删除无用字段的返回
            unset($v['detail']);
        }
        return ['message' => $data, 'status' => true];
    }

}