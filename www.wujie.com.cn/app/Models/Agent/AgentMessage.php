<?php  namespace App\Models\Agent;

use Illuminate\Database\Eloquent\Model;

class AgentMessage extends Model
{
    protected  $table = 'agent_message';
    //黑名单
    protected $guarded = [];
    protected $dateFormat = 'U';
}