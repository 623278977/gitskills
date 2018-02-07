<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017-12-6
 * Time: 18:27
 */

namespace App\Models\Agent\Exhibition;
use Illuminate\Database\Eloquent\Model;

class WeChat extends Model
{
    protected  $table =  'agent_we_chat';
    protected $dateFormat = 'U';
    protected $guarded = [];
}