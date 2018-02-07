<?php namespace App\Models\Agent;

use Illuminate\Database\Eloquent\Model;

class AgentScore extends Model
{
    protected  $table =  'agent_score';
    protected $dateFormat = 'U';

    protected $guarded = [];

    public $timestamps = true;

}