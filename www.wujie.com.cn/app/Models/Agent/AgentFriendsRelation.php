<?php

namespace App\Models\Agent;

use Illuminate\Database\Eloquent\Model;

class AgentFriendsRelation extends Model
{
    protected $table = 'agent_friends_relation';
    protected $dateFormat = 'U';
    protected $guarded = [];
}
