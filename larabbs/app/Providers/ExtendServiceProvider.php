<?php

namespace App\Providers;

use SeedlingsZO\Extend\ApiClient;
use SeedlingsZO\Extend\RedisSession;
use SeedlingsZO\Extend\Tool;
use SeedlingsZO\Extend\Blogger;
use Illuminate\Support\ServiceProvider;

class ExtendServiceProvider extends ServiceProvider
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
        $this->app->bind('tool', function ($app) {
            return new Tool;
        });
        $this->app->bind('redis_session', function ($app) {
            return new RedisSession;
        });
        $this->app->bind('api_client', function($app) {
           return new ApiClient;
        });
        $this->app->bind('blogger', function($app) {
            return new Blogger;
        });
    }
}
