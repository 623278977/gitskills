<?php

namespace App\Jobs;

use App\Jobs\Job;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Bus\SelfHandling;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\DB;

class Insertintomysql extends Job implements SelfHandling, ShouldQueue
{
    use InteractsWithQueue, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
        echo '__construct';
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        echo 'handle';
        for($i=0;$i<10000;$i++){
            DB::insert("insert into lab_video (activity_id,subject,status) VALUES ('$i','subject$i','1')");
        }

    }
}
