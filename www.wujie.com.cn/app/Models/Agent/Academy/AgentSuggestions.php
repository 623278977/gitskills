<?php

namespace App\Models\Agent\Academy;

use Illuminate\Database\Eloquent\Model;

class AgentSuggestions extends Model
{
    protected  $table =  'agent_suggestions';
    protected $dateFormat = 'U';
    protected $guarded = [];
}
