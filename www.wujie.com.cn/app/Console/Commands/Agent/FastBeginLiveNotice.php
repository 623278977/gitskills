<?php namespace App\Console\Commands\Agent;

use App\Models\Agent\AgentBrand;
use Illuminate\Console\Command;
use App\Models\Live\LiveBrandGoods;
use App\Models\Live\Entity as Live;
use App\Models\Agent\Entity as Agent;

class FastBeginLiveNotice  extends Command
{
    protected $signature = "Agent:FastBeginLiveNotice";

    //处理发送消息通知
    public function handle()
    {
        $this->_gainLiveBrandInfos();
    }

    /**
     * 获取直播品牌记录的对应的品牌ID
     */
    private  function _gainLiveBrandInfos()
    {
        $brand_id = array();
        $live_id  = array();

       $result = LiveBrandGoods::get();

       //对结果进行处理
        if ($result) {
            foreach ($result as $key => $vls) {
                $brand_id[] = $vls->brand_id;
                $live_id[]  = $vls->live_id;
            }
        }

        //获取直播ID 和 经纪人数据信息
        $live_results  = $this->_gainLives($live_id);
        $agent_results = $this->_gainAgentIds($brand_id);

        if ($live_results) {
            foreach ($live_results as $key => $vls) {
                $day = ceil(($vls->begin_time / 86400));
                if ($day > 0 && $day < 2) {

                    //直播即将开始时提示(红点展示)
                    send_transmission(json_encode([
                        'type'  => 'red_note',
                        'style' => 'json',
                        'value' => [
                            'id'    => "'$vls->id'",
                            'type'  => 'live_fast_begin'
                        ],
                    ]), $agent_results, null, true);
                }
            }
        }
    }

    //获取直播数据信息
    private function _gainLives(array $id)
    {
       $live_result = Live::whereIn('id', $id)
           ->where('begin_time', '>', time())
           ->select('id', 'begin_time')
           ->get();

       return $live_result ?: 0;
    }

    //获取经纪人ID信息
    private function _gainAgentIds(array $id)
    {
        //根据品牌ID获取代理过此品牌的经纪人
        $agent_ids = AgentBrand::whereIn('brand_id', $id)
            ->where('status', 4)
            ->list('agent_id');

        //获取经纪人信息
        $agents = Agent::whereIn('id', $agent_ids)
            ->where('status', 1)
            ->get();

        return $agents ?: "";
    }

}