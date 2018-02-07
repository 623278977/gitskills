<?php namespace App\Models\Agent;

use Illuminate\Database\Eloquent\Model;
use App\Models\Agent\AgentAchievementLog;

class AgentAchievement extends Model
{
    protected $table = 'agent_achievement';

    protected $dateFormat = 'U';

    //protected $fillable=['agent_id','brand_id','status'];

    /**
     * 黑名单
     */
    protected $guarded = [];


    public static $instance = null;

    public static function getInstance()
    {
        if (is_null(self::$instance)) {
            self::$instance = new self;
        }

        return self::$instance;
    }


    /**
     * 关联：佣金档位提成
     */
    public function hasOneCommissionLevel()
    {
        return $this->hasOne(CommissionLevel::class, 'id', 'commission_level_id');
    }

    /**
     * 关联：关联业绩记录表
     */
    public function hasManyAgentAchievementLog()
    {
        return $this->hasMany(AgentAchievementLog::class, 'agent_achievement_id', 'id');
    }


    /**
     * 从汉字形式的季度格式转化为字母格式的季度，如'2017年10月-12月'到17Q4
     *
     * @param $quarter
     * @author tangjb
     */
    public function transformQuarter($quarter)
    {
        $year = mb_substr($quarter, 2, 2);

        $month = mb_substr($quarter, 5);

        switch ($month) {
            case '1月-3月':
                $q = 'Q1';
                break;
            case '4月-6月':
                $q = 'Q2';
                break;
            case '7月-9月':
                $q = 'Q3';
                break;
            case '10月-12月':
                $q = 'Q4';
                break;
            default:
                $q = '';
        }

        return $year.$q;


    }


}