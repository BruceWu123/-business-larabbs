<?php

namespace App\Http;

use App\Http\Middleware\UserVisitLog;
use Illuminate\Foundation\Http\Kernel as HttpKernel;

class Kernel extends HttpKernel
{
    /**
     * The application's global HTTP middleware stack.
     *
     * These middleware are run during every request to your application.
     *
     * @var array
     */
    protected $middleware = [
        \Illuminate\Foundation\Http\Middleware\CheckForMaintenanceMode::class,
        \Clockwork\Support\Laravel\ClockworkMiddleware::class,
    ];

    /**
     * The application's route middleware groups.
     *
     * @var array
     */
    protected $middlewareGroups = [
        'web' => [
            \App\Http\Middleware\EncryptCookies::class,
            \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
            \Illuminate\Session\Middleware\StartSession::class,
            \Illuminate\View\Middleware\ShareErrorsFromSession::class,
            \App\Http\Middleware\VerifyCsrfToken::class,
            \App\Http\Middleware\ReferrerChannel::class, //推广渠道来源
        ],

        'api' => [
            'throttle:60,1',
        ],
    ];

    /**
     * The application's route middleware.
     *
     * These middleware may be assigned to groups or used individually.
     *
     * @var array
     */
    protected $routeMiddleware = [
        'auth'             => \App\Http\Middleware\Authenticate::class,
        'auth.basic'       => \Illuminate\Auth\Middleware\AuthenticateWithBasicAuth::class,
        'auth2'            => \App\Http\Middleware\AuthenticateTwo::class,
        'captcha'          => \App\Http\Middleware\VerifyCaptcha::class,
        'can'              => \Illuminate\Foundation\Http\Middleware\Authorize::class,
        'guest'            => \App\Http\Middleware\RedirectIfAuthenticated::class,
        'throttle'         => \Illuminate\Routing\Middleware\ThrottleRequests::class,
        'is_login.java'    => \App\Http\Middleware\AuthenticateWithJava::class,
        'auth.login_token' => \App\Http\Middleware\AuthenticateWithLoginToken::class,
        'user_visit_log'   => UserVisitLog::class,
        'cors'             => \App\Http\Middleware\EnableCrossRequest::class,
        'sms_code'         => \App\Http\Middleware\VerifySmsCode::class,
        'coupon_group'     => \App\Http\Middleware\CouponGroup::class,
        'coupon_group2'    => \App\Http\Middleware\CouponGroup2::class,
        'loginBySms'       => \App\Http\Middleware\LoginBySms::class,
    ];
}
