<?php

namespace App\Http\Middleware;

use Closure;
use Message;
use Tool;

class VerifySmsCode
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $check_code = Tool::checkSmsCode($request->all());
        if (isset($check_code['err_code'])) {
            return Message::jsonMessage($check_code);
        }
        return $next($request);
    }
}
