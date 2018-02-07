<?php

namespace App\Listeners;

use App\Events\ChristmasWinPrize;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Models\Agent\AgentChristmasHeros;
use App\Models\Zone\Entity as Zone;

class ChristmasWinPrizeListener
{

    //数组索引  是  奖励金对应的类型
    protected $bonusList = [
        1 => 5,
        2 => 80,
        3 => 200,
        4 => 1000,
    ];

    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {

    }

    /**
     * Handle the event.
     *
     * @param  ChristmasWinPrize  $event
     * @return void
     */
    public function handle(ChristmasWinPrize $event)
    {
        $data = [];
        $param = $event->param;
        $type = intval($param['type']);
        $data['type'] = $type;
        $data['bonus'] = $this->bonusList[$type];
        $agent = $param['agent'];
        $data['avatar'] = getImage($agent['avatar'] , 'avatar', '');
        $data['nickname'] = trim($agent['nickname']);
        $data['username'] = trim($agent['username']);
        $data['zone'] = Zone::getCityAndProvince($agent['zone_id']);
        if($type == 2){
            $data['detail'] = $param['activty_name']."/".$param['username'];
        }
        else if($type == 3){
            $data['detail'] = $param['brandName']."/".$param['username'];
        }
        AgentChristmasHeros::create($data);
    }
}
