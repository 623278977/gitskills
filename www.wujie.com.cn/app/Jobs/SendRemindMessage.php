<?php

namespace App\Jobs;

use App\Jobs\Job;
use App\Http\Controllers\Api\MessageController;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Bus\SelfHandling;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendRemindMessage extends Job implements SelfHandling, ShouldQueue
{
    use InteractsWithQueue, SerializesModels;

    protected $param;

    /**
     * 获得job实例
     * SendRemindMessage constructor.
     */
    public function __construct($param)
    {
        $this->param = $param;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(MessageController $message)
    {
        $message->createMessage($this->param);
    }
}
