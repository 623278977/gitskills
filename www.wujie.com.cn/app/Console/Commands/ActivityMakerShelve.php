<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Foundation\Inspiring;
use DB;
use \Mail;

class ActivityMakerShelve extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'activity_maker_shelve';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '活动空间下架';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $lists = DB::table('activity_maker')
            ->leftJoin('activity', 'activity.id', '=', 'activity_maker.activity_id')
            ->where('response_type', 0)
            ->where('status', 0)
            ->select('activity.maker_num', 'activity.time_limit', 'activity_maker.id', 'activity_maker.activity_id')
            ->get();

        foreach ($lists as $k => $v) {
            $count = DB::table('activity_maker')->where('activity_id', $v->activity_id)
                ->where('response_type', 0)->where('status', 1)->count();

            if (time() > strtotime($v->time_limit)) {
                DB::table('activity_maker')
                    ->where('id', $v->id)
                    ->where('status', 0)
                    ->update(['status' => -1, 'updated_at' => time()]);
            }

        }

    }

}
