<?php

namespace App\Services;

use App\Models\Categorys as CategorysModel;


class Categorys
{

    /**
     * 作用:分类数据 二级
     * 参数:$id 品牌id  $uid  用户id
     *
     * 返回值:
     */
    public function lists()
    {
        $categorys_disable_ids = CategorysModel::where('status', 'disable')->where('type', 'brand')
            ->lists('id')->toArray();
        $categorys = CategorysModel::where('status', 'enable')->where('type', 'brand')->whereNotIn('pid', $categorys_disable_ids)
            ->orderBy('sort', 'desc')->select('id', 'name', 'logo', 'sort', 'pid')->get()->toArray();
        foreach($categorys as $k=>&$v){
            $v['logo'] = getImage($v['logo'], 'activity', '', 0);
        }
        $categorys = toTree($categorys, 'id', 'pid');
        array_unshift($categorys, ['name'=>'全部分类','children'=>[['name'=>'全部']]]);
        foreach($categorys as $k=>&$v){
            if($k>0){
                array_unshift($v['children'], ['name'=>'全部']);
                $v['children'][0]['id'] = $v['id'];
                $v['children'][0]['pid'] = $v['id'];
            }
        }

        return $categorys;
    }
}