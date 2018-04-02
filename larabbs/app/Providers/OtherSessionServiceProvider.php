<?php

namespace App\Providers;


use SeedlingsZO\OtherSessionHandler\OtherSession;
use Illuminate\Session\SessionManager;
use Illuminate\Support\ServiceProvider;


class OtherSessionServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {

    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind('otherSession', function ($app) {
            return new OtherSession($app);
        });
//        $this->registerSessionManager();
//
////        $this->registerSessionDriver();
//
//        $this->app->singleton('Illuminate\Session\Middleware\StartSession');
    }

    /**
     * Register the session manager instance.
     *
     * @return void
     */
    protected function registerSessionManager()
    {
        $this->app->singleton('otherSession', function ($app) {
            $app->config->set('session.cookie', 'java_session');
            $app->config->set('session.driver', 'redis');
            $app->config->set('session.connection', null);
            return new SessionManager($app);
        });
    }

    /**
     * Register the session driver instance.
     *
     * @return void
     */
    protected function registerSessionDriver()
    {
        $this->app->singleton('otherSession.store', function ($app) {
            // First, we will create the session manager which is responsible for the
            // creation of the various session drivers when they are needed by the
            // application instance, and will resolve them on a lazy load basis.
            $manager = $app['session'];

            return $manager->driver();
        });
    }

}
