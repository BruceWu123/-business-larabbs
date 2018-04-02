<?php

namespace App\Providers;

use SeedlingsZO\MiddleHandler\WapHandler;
use Illuminate\Support\ServiceProvider;

class MiddleHandlerSeviceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind('handler', function ($app) {
            return new WapHandler;
        });
    }
}
