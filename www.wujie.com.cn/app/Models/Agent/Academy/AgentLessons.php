<?php

namespace App\Models\Agent\Academy;

use App\Models\Keywords;
use Illuminate\Database\Eloquent\Model;
use App\Models\Comment\Entity as Comment;

class AgentLessons extends Model
{
    protected  $table =  'agent_lessons';
    protected $dateFormat = 'U';
    protected $guarded = [];


    public function keywords()
    {
        return $this->belongsTo(Keywords::class,'id','type');
    }

    public function comment()
    {
        return $this->hasMany(Comment::class, 'post_id', 'id')->where('type','Lesson');
    }

    public function lecturer()
    {
        return $this->belongsTo(AgentLecturers::class);
    }
}
