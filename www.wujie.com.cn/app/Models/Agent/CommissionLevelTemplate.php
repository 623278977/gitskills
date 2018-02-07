<?php namespace App\Models\Agent;

use Illuminate\Database\Eloquent\Model;
use App\Models\Agent\AgentAchievementLog;
class CommissionLevelTemplate extends Model
{
    protected  $table = 'commission_level_template';

    //protected $fillable=['agent_id','brand_id','status'];

    /**
     * 黑名单
     */
    protected $guarded = [];

    protected $dateFormat = 'U';


}