<?php

namespace App\Models\Agent\Academy;

use App\Models\Keywords;
use Illuminate\Database\Eloquent\Model;
use App\Models\News\Entity as News;

class AgentArticlesKnowledge extends Model
{
    protected  $table =  'agent_articles_knowledge';
    protected $dateFormat = 'U';
    protected $guarded = [];

    //public $timestamps = true;


    //关联资讯表
    public function news(){
        return $this->belongsTo(News::class , 'news_id','id')
            ->where('type','agent_normal');
    }

    public function lecturer()
    {
        return $this->belongsTo(AgentLecturers::class, 'lecturer_id', 'id');
    }


}
