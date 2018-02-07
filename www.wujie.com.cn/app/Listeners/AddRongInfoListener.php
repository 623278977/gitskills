<?php namespace APP\Listeners;

use App\Events\AddRongInfo;
use App\Models\Agent\AgentRongInfo;

class AddRongInfoListener
{
    public function __construct() {}

    /**
     * Handle the event.
     *
     * @param    AddRongInfo $addRongInfo
     * @return   void
     * @internal param AddRongInfo|Invitation|OrderShipped $event
     */
    public function handle(AddRongInfo $addRongInfo)
    {
        if ($addRongInfo->add_rong_info) {
            $add_result = json_decode($addRongInfo->add_rong_info, true);

            //进行数据的添加
            AgentRongInfo::insert($add_result);
        }
    }
}