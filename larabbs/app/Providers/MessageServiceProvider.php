<?php

namespace App\Providers;

use SeedlingsZO\MessageHandler\Message;
use Illuminate\Support\ServiceProvider;

class MessageServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {

    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind('message', function ($app) {
            return new Message;
        });
    }
}
