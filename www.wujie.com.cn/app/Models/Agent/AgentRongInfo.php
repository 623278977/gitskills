<?php namespace App\Models\Agent;

use Illuminate\Database\Eloquent\Model;

class AgentRongInfo extends Model
{
    protected $table = 'agent_rong_info';

    public static $instance = null;
    public static function instance()
    {
        if (is_null(self::$instance)) {
            self::$instance = new self;
        }

        return self::$instance;
    }
}