<?php namespace App\Models\Agent;

use Illuminate\Database\Eloquent\Model;
use App\Models\User\Fund;
use App\Models\Agent\AgentAchievement;

class AgentDevelopTeamLog extends Model
{
    protected  $table = 'agent_develop_team_log';
    protected $guarded = [];
    protected $dateFormat = 'U';
}