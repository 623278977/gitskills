<?php namespace App\Models\Agent;

use Illuminate\Database\Eloquent\Model;

class AgentLoginLog extends Model
{
    protected $table = 'agent_login_log';

    protected $guarded = [];

    protected $dateFormat = 'U';

    private static $_instance;


    public static function getInstance()
    {
        if (!isset(self::$_instance)) {
            self::$_instance = new self;
        }

        return self::$_instance;
    }








}