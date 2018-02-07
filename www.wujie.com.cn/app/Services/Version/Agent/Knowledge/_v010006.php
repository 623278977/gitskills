<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017-12-4
 * Time: 10:45
 */

namespace App\Services\Version\Agent\Knowledge;

use App\Models\Agent\Academy\AgentArticlesKnowledge;
use App\Models\User\Praise;
use App\Models\Comment\Entity as Comment;

class _v010006 extends _v010004
{
    public function postTypes($data)
    {
        $message = parent::postTypes($data);
        if(!$message['status']){
            return $message;
        }
        $types = $message['message'];
        $recommend = AgentArticlesKnowledge::with('news', 'lecturer')
            ->where('status',1)
            ->where('knowledge_type', '<>', 0)
            ->orderBy('sort','desc')
            ->orderBy('created_at','desc')
            ->limit(15)
            ->get();
        $arr=[];
        foreach ($recommend as $value){
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
        $list=array_merge($types,['recommend'=>$arr]);
        return ['message'=>$list, 'status'=>true];
    }


}