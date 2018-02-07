<?php

namespace App\Models\Agent;

use Illuminate\Database\Eloquent\Model;

class AgentGiveGetRedPacket extends Model
{
    protected $table = 'agent_give_get_red_packet';
    protected $dateFormat = 'U';
    protected $guarded = [];

    //关联经纪人领取红包表
    public function red_packet_agent(){
        return $this->belongsTo(RedPacketAgent::class , 'red_packet_agent_id' , 'id');
    }

    //关联经纪人表 give
    public function give_agent(){
        return $this->belongsTo(Agent::class , 'give_agent_id' , 'id');
    }

    //关联经纪人表 get
    public function get_agent(){
        return $this->belongsTo(Agent::class , 'get_agent_id' , 'id');
    }

}
