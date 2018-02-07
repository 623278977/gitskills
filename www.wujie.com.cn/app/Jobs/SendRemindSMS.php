<?php

namespace App\Jobs;

use App\Jobs\Job;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Bus\SelfHandling;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Http\Controllers\Api\SmsController;

class SendRemindSMS extends Job implements SelfHandling, ShouldQueue
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
    public function handle(SmsController $job)
    {
        if ($this->attempts() > 3) {
            return;
        }
        $job->createSMS($this->param);
    }
}
