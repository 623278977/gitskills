<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018-1-23
 * Time: 15:58
 */

namespace App\Models\RedPacket;


use Illuminate\Database\Eloquent\Model;

class RedPacketPersonUseLog extends Model
{
    protected  $table =  'red_packet_person_use_log';
    protected $dateFormat = 'U';
    protected $guarded = [];
}