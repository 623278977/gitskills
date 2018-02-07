<?php

namespace App\Models\Agent;

use Illuminate\Database\Eloquent\Model;

class AgentAdd extends Model
{
    protected $table = 'agent_add';
    protected $dateFormat = 'U';
    protected $guarded = [];

    public function agent(){
        return $this->belongsTo(Agent::class , 'agent_id' , 'id');
    }
}
