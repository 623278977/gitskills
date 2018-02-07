<?php

namespace App\Providers;

use Illuminate\Contracts\Events\Dispatcher as DispatcherContract;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        'App\Events\WJSQView' => [
            'App\Listeners\WJSQViewListener',
        ],
        'App\Events\Invitation' => [
            'App\Listeners\InvitationChangeStatusListener',
        ],
        'App\Events\AddRongInfo' => [
            'App\Listeners\AddRongInfoListener',
        ],
        //添加圣诞节集赞事件
        'App\Events\ChristmasWinPrize' => [
            'App\Listeners\ChristmasWinPrizeListener',
        ],
    ];

    /**
     * Register any other events for your application.
     *
     * @param  \Illuminate\Contracts\Events\Dispatcher  $events
     * @return void
     */
    public function boot(DispatcherContract $events)
    {
        parent::boot($events);

        //
    }
}
