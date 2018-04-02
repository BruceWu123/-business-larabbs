<?php

namespace App\Http\Middleware;

use Closure;
use \Message;
use \Tool;

class VerifyCaptcha
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $guard
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {
        if (!Tool::captchaCheck()) {
            return Message::jsonMessage(Message::getErrorMessage(15019));
        }
        return $next($request);
    }
}
