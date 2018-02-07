<?php namespace App\Models\Agent\NewAgentAreas;

use Illuminate\Database\Eloquent\Model;

class NewAgentArea extends Model
{
    const ENABLE_TYPE  = 1;     //启用状态
    const LOST_TYPE    = 0;     //禁用状态

    protected $table  = 'new_agent_area';

    public static $instance = null;

    public static function instance()
    {
        if (is_null(self::$instance)) {
            self::$instance = new self;
        }

        return self::$instance;
    }

    /**
     * 关联：新手专区详情
     */
    public function hasOneNewAgentDetails()
    {
        return $this->hasOne(NewAgentDetail::class, 'id', 'new_agent_detail_id');
    }

    /**
     * 获取首页新手专区图片列表
     */
    public function gainIndexNewAgentImgLists()
    {
        $confirm_result = array();

        $gain_result = self::where('status', self::ENABLE_TYPE)
            ->orderBy('sort', 'desc')
            ->orderBy('id',   'desc')
            ->limit(5)
            ->get();

        //对结果进行处理
        if ($gain_result) {
            foreach ($gain_result as $key => $vls) {
                $confirm_result[] = [
                    'url'   => getImage($vls->url),
                    'value' => $vls->href ?  $vls->href : $vls->new_agent_detail_id,
                    'type'  => $vls->href ?  1 : 2,
                ];
            }
        }

        return $confirm_result;
    }
}