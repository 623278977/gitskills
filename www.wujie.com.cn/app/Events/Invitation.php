<?php namespace App\Events;

use App\Events\Event;
use Illuminate\Queue\SerializesModels;
use App\Models\Agent\Invitation as Invite;

class Invitation extends Event
{
    use SerializesModels;

    /**
     * Create a new event instance.
     *
     * 创建一个新的事件实例（邀请函表实例）
     *
     * @param Invitation|Invite $invitation
     */
    public function __construct($invitation)
    {
        $this->invitation = $invitation;
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
