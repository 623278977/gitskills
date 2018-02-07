<?php

namespace App\Models\Agent\Academy;

use Illuminate\Database\Eloquent\Model;

class AgentClock extends Model
{
    protected  $table =  'agent_clock';
    protected $dateFormat = 'U';
    protected $guarded = [];
}
