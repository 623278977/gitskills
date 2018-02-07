<?php

namespace App\Models\Agent;

use Illuminate\Database\Eloquent\Model;

class AgentChristmasHeros extends Model
{
    protected $table = 'agent_christmas_heros';

    protected $dateFormat = 'U';
    /**
     * 黑名单
     */
    protected $guarded = [];
}
