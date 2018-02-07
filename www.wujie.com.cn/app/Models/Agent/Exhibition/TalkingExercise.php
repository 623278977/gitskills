<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017-12-5
 * Time: 17:42
 */

namespace App\Models\Agent\Exhibition;
use Illuminate\Database\Eloquent\Model;

class TalkingExercise  extends Model
{
    protected  $table =  'agent_talking_exercise';
    protected $dateFormat = 'U';
    protected $guarded = [];
}