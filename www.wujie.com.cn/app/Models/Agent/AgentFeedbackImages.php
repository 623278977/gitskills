<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017-12-20
 * Time: 15:50
 */

namespace App\Models\Agent;
use Illuminate\Database\Eloquent\Model;


class AgentFeedbackImages extends Model
{
    protected  $table =  'agent_feedback_images';
    //黑名单
    protected $guarded = [];

    protected $dateFormat = 'U';
}