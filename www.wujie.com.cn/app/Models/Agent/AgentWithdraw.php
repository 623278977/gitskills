<?php namespace App\Models\Agent;

use Illuminate\Database\Eloquent\Model;

class AgentWithdraw extends  Model
{
    protected $table    = 'agent_withdraw';
    protected $fillable = ['agent_id','bank_name','account', 'currency'];
}