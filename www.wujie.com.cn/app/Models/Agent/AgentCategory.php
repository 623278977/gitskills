<?php

namespace App\Models\Agent;

use Illuminate\Database\Eloquent\Model;
use App\Models\Categorys;

class AgentCategory extends Model
{
    protected $table = 'agent_category';
    protected $guarded = [];
    //关联行业分类表
    public function categorys(){
        return $this->belongsTo(Categorys::class,'category_id','id');
    }
}