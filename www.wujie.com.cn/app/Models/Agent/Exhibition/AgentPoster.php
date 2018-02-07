<?php

namespace App\Models\Agent\Exhibition;

use Illuminate\Database\Eloquent\Model;

class AgentPoster extends Model
{
    protected $table = 'agent_poster';
    protected $dateFormat = 'U';
    protected $guarded = [];
}
