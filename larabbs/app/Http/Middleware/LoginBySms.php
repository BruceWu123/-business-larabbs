<?php

namespace App\Http\Middleware;

use Closure;
use MiddleHandler;
use Message;
use Tool;

class LoginBySms
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
        $user = MiddleHandler::loginByVcode($request->all());
        if(isset($user['err_code'])){
            return Message::jsonMessage($user);
        }
        return $next($request);
    }
}
