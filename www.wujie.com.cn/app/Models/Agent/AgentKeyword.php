<?php namespace App\Models\Agent;

use Illuminate\Database\Eloquent\Model;
use App\Models\Keywords;

class AgentKeyword extends Model
{
    protected $table    = 'agent_keywords';
    //黑名单
    protected $guarded = [];

    protected $dateFormat = 'U';

    public function keywords(){
        return $this->belongsTo(Keywords::class,'keyword_id','id');
    }

    /*
     *
     * 获取指定经纪人指定类型关键词
     * agent = 0 时，获取某种类型的全部关键词
    */
    public static function getAgentKeywords($type,$agentId = 0){
        $agentKeywords = self::with('keywords')
            ->whereHas('keywords',function($query)use($type){
            $query->where('type','agent_share');
        });
        if($agentId){
            $agentKeywords->where('agent_id',$agentId);
        }
        $agentKeywords = $agentKeywords->get()->toArray();
        return $agentKeywords;
    }

}