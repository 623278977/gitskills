<?php namespace App\Models\Agent;

use Illuminate\Database\Eloquent\Model;
use App\Models\User\Fund;
use App\Models\Agent\AgentAchievement;

class AgentAchievementLog extends Model
{
    protected  $table = 'agent_achievement_log';
    protected $guarded = [];

    /**
     * 关联：合同
     */
    public function contract()
    {
        return $this->hasOne(Contract::class, 'id', 'contract_id');
    }


    /**
     * 关联：创业基金
     */
    public function fund()
    {
        return $this->hasOne(Fund::class, 'id', 'fund_id');
    }



    /**
     * 关联：邀请函
     */
    public function invitation()
    {
        return $this->hasOne(Fund::class, 'id', 'invitation_id');
    }


    /**
     * 关联：经纪人
     */
    public function agent()
    {
        return $this->hasOne(Agent::class, 'id', 'agent_id');
    }

    /**
     * 关联：经纪人
     */
    public function achievement_agent()
    {
        return $this->belongsTo(AgentAchievement::class, 'agent_achievement_id', 'id');
    }


    /**
     * 下属经纪人成单量
     * @User yaokai
     * @param $agent_id
     * @param $brand_id
     */
    public static function getBranchAchievement($agent_id,$brand_id)
    {
        //经纪人业绩id
        $achievement_ids = AgentAchievement::where('agent_id',$agent_id)->lists('id');

        $num = self::whereIn('agent_achievement_id',$achievement_ids)
            ->where('agent_id','!=',$agent_id)
            ->whereIn('contract_id',function ($query) use ($brand_id){
               $query->from('contract')
                   ->where('brand_id',$brand_id)
                   ->lists('id');
            })
            ->count();

        return $num?:0;

    }
    
    

}