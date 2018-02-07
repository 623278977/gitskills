<?php

namespace App\Services\Version\Search;

use App\Models\Keywords;
use App\Services\News;
use App\Http\Controllers\Api\VideoController;
use App\Http\Controllers\Api\ActivityController;
use App\Http\Controllers\Api\BrandController;
use App\Http\Controllers\Api\NewsController;
use DB;
use App\Models\User\Praise;
use App\Models\Comment\Entity as Comment;

class _v020700 extends _v020500
{
    /*
     * 全局搜索
     */
    public function postList($param)
    {
        $data = [
            'video' => call_user_func_array(array(new VideoController(), 'postList'), array($param['request'],$param['version'])),
            //'live' => call_user_func_array(array(new LiveController(), 'postList'), array($param['request'], 1)),
            'activity' => call_user_func_array(array(new ActivityController(), 'postList'), array($param['request'])),
            //'subscribe' => call_user_func_array(array(new LiveController(), 'postUserSubscribe'), array($request)),
            'brand' => call_user_func_array(array(new BrandController(), 'postLists'), array($param['version'], $param['request']->input('is_return', 1), 0)),
            'news' => call_user_func_array(array(new NewsController(), 'postList'), array($param['request'] , new News(),$param['version'])),
        ];

        if (!empty($param['type']) && array_key_exists(trim($param['type']), $data)) {
            $data = $data[$param['type']];
        }
//        //新增 点赞数 评论数
//        foreach ($data as $k=>$v){
//            if ($k == 'video' || $k == 'brand' || $k == 'activity'){
//                continue;
//            }
//            foreach ($v as $key=>$value){
//                //点赞
//                $value['count_zan'] = Praise::ZanCount($value['id'],$k);//$value['count_zan']
//                //评论
//                $value['count_comment'] = Comment::ConmmentCount($value['id'],$k);
//            }
//
//
//        }
        return ['message' => $data, 'status' => true];

    }

    /*
     * 热门关键字
     */
    public function postHotwords($param)
    {
        $request = $param['request'];
        $search_type = $request->input('search_type' , 'search');

        $data = Keywords::getDataByType($search_type , 10 ,function($builder){
            return $builder->orderBy('sort' , 'desc')
                ->select('contents');
        });

        $return = [];
        foreach($data as $item){
            $return[] = $item->contents;
        }

        return ['message' => $return, 'status' => true];
    }
}