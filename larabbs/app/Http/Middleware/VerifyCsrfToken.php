<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as BaseVerifier;

class VerifyCsrfToken extends BaseVerifier
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array
     */
    protected $except = [
        '/pay/notify',
        '/personal/activities',
        '/wechat_jssdk'
    ];

    /**
     * Determine if the request has a URI that should pass through CSRF verification.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return bool
     */
//    protected function shouldPassThrough($request)
//    {
//        if (env('APP_ENV') == 'local') {
//            array_unshift($this->except, '*');
//        }
//        return parent::shouldPassThrough($request);
//    }
}
