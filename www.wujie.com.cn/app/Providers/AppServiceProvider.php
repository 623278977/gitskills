<?php

namespace App\Providers;

use Illuminate\Support\Facades\Request;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
//        $url = Request::url();
////        if (Str::contains($url, 'apply-and-sign')) {
//            \DB::listen(
//                function ($query, $binds, $time) {
//                    foreach ($binds as $bind) {
//                        $query = preg_replace('/\?/', '\'' . $bind . '\'', $query, 1);
//                    }
//                    \Log::info($query);
//                    \Log::info($time);
//                }
//            );
//        }
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
