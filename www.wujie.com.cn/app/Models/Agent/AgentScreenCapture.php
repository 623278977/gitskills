<?php namespace App\Models\Agent;

use Illuminate\Database\Eloquent\Model;
use App\Models\User\Fund;
use App\Models\Agent\AgentAchievement;

class AgentScreenCapture extends Model
{
    protected  $table = 'agent_screen_capture';
    protected $guarded = [];
    protected $dateFormat = 'U';
}