<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017-11-17
 * Time: 17:24
 */

namespace App\Services\Version\Agent\LecturerColumn;


use App\Models\Agent\Comments;
use App\Models\User\Praise;
use App\Services\Version\VersionSelect;
use App\Models\Agent\Academy\AgentLecturerColumns;
use App\Models\Agent\Academy\AgentColumnsContent;
use App\Models\Comment\Entity as Comment;
class _v010004 extends VersionSelect
{

    public function postDetail($data)
    {
        $request=$data['request'];
        $id=$request->input('id');
        if(empty($id)){
            return ['message'=>'请传入id','status'=>false];
        }
        $column=AgentLecturerColumns::with('agent_lecturers')
            ->where('id',$id)
            ->where('status',1)
            ->first();
        if(empty($column)){
            return ['message'=>'该专栏不存在','status'=>false];
        }
        $column_list=[
            'title'=>$column['title'],
            'photo'=>$column['banner'],
            'summary'=>$column['summary'],
            'name'=>$column['agent_lecturers']['name'],
            'appellation'=>$column['agent_lecturers']['appellation'],
        ];

        $article=AgentColumnsContent::with('agent_articles_knowledge','agent_articles_knowledge.news')
            ->where('columns_id',$id)
            ->where('type',2)
            ->where('status',1)
            ->get();
        $article_list=[];
        if(!empty($article)){
            foreach ($article as $val){
                $article_list[]=[
                    'id'=>$val['agent_articles_knowledge']['id'],
                    'title'=>$val['agent_articles_knowledge']['news']['title'],
                    'logo'=>$val['agent_articles_knowledge']['news']['logo'],
                    'summary'=>$val['agent_articles_knowledge']['news']['summary'],
                    'author' =>$column['agent_lecturers']['name'],
                    'view'=>$val['agent_articles_knowledge']['news']['view'],
                    'comments'=>Comment::commentsCount('news',$val['agent_articles_knowledge']['news']['id']),
                    'zan'=>Praise::ZanCount($val['agent_articles_knowledge']['news']['id']),
                ];
            }
        }

        $lessons=AgentColumnsContent::with('agent_lessons')
            ->where('columns_id',$id)
            ->where('type',1)
            ->where('status',1)
            ->get();

        $lesson_list=[];
        if(!empty($lessons)){
            foreach ($lessons as $item){
                $lesson_list[]=[
                    'id'=>$item['agent_lessons']['id'],
                    'subject'=>$item['agent_lessons']['subject'],
                    'view'=>$item['agent_lessons']['view'],
                    'image'=>getImage($item['agent_lessons']['image'],'video'),
                ];
            }
        }

        $total_view=0;
        foreach ($article_list as $value){
            $total_view += $value['view'];
        }
        foreach($lesson_list as $value){
            $total_view += $value['view'];
        }
        $column_list['study_times']= $total_view;
        $message=[
            'column'=>$column_list,
            'article'=>$article_list,
            'lesson'=>$lesson_list
        ];
        return ['message'=>$message, 'status'=> true];
    }
}