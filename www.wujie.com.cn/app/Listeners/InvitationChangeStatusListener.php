<?php namespace APP\Listeners;

use App\Events\Invitation;

class InvitationChangeStatusListener
{
    const  UNDETERMINED_TYPE = 0;   //邀请函待确认类型
    const  INSPECT_TYPE      = 2;   //考察邀请函类型
    /**
     * Create the event listener.
     */
    public function __construct() {}

    /**
     * Handle the event.
     *
     * @param Invitation|OrderShipped $event
     * @return void
     */
    public function handle(Invitation $event)
    {
        if ($event->invitation && $event->invitation->status == self::UNDETERMINED_TYPE &&
            $event->invitation->type == self::INSPECT_TYPE &&
            $event->invitation->expiration_time < time()) {
            $this->_changeStatus($event->invitation->id);
        }
    }

    /**
     * 内部使用
     *
     * @param $invitation_id
     */
    private function _changeStatus($invitation_id)
    {
        \App\Models\Agent\Invitation::where('id', $invitation_id)->update(['status' => -2]);
    }
}