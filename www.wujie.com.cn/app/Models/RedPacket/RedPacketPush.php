<?php
namespace App\Models\RedPacket;


use Illuminate\Database\Eloquent\Model;

class RedPacketPush extends Model
{
    protected $table =  'red_packet_push';
    protected $dateFormat = 'U';
    protected $guarded = [];

    public function red_packet()
    {
        return $this->belongsToMany(RedPacket::class,'red_packet_push_relation','push_id','red_packet_id');
    }
}