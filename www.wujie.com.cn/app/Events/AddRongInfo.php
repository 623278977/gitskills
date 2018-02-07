<?php namespace App\Events;

use Illuminate\Queue\SerializesModels;

class AddRongInfo extends Event
{
    use SerializesModels;

    /**
     * Create a new event instance.
     *
     * 创建一个新的事件实例（邀请函表实例）
     *
     * @param $add_rong_info
     * @internal param Invitation|Invite $invitation
     */
    public function __construct($add_rong_infos)
    {
        $this->add_rong_info = $add_rong_infos;
    }

    /**
     * Get the channels the event should be broadcast on.
     *
     * @return array
     */
    public function broadcastOn()
    {
        return [];
    }
}
