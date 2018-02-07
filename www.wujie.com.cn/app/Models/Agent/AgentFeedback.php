<?php namespace App\Models\Agent;

use Illuminate\Database\Eloquent\Model;

class AgentFeedback extends Model
{
    protected  $table =  'agent_feedback';
    //黑名单
    protected $guarded = [];

    protected $dateFormat = 'U';
}