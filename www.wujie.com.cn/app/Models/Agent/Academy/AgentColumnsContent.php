<?php

namespace App\Models\Agent\Academy;

use Illuminate\Database\Eloquent\Model;

class AgentColumnsContent extends Model
{
    protected  $table =  'agent_columns_content';
    protected $dateFormat = 'U';
    protected $guarded = [];

    //关联课程表
    public function agent_lessons(){
        return $this->belongsTo(AgentLessons::class,'post_id','id');
    }
    //关联商圈热文表
    public function agent_articles_knowledge(){
        return $this->belongsTo(AgentArticlesKnowledge::class,'post_id','id');
    }
}
