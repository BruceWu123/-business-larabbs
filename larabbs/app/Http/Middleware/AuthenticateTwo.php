<?php

namespace App\Http\Middleware;

use Closure;
use \Tool;
use MiddleHandler;
use Message;

class AuthenticateTwo
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
        if (!Tool::authUserCheck()) {
            if ($request->ajax() || $request->wantsJson()) {
                return Message::jsonMessage(Message::getErrorMessage(11032), []);
            } else {
                $return = $request->input('return') ?: urlencode($request->fullUrl());
                return redirect(action('Wap\LoginController@getIndex') . '?return=' . $return);
            }
        }

        $login_user = \Tool::loginUser();

        // 如果用户第一次微信登录即送红包
        if (isset($login_user['login_type']) && isset($login_user['subscribe']) && $login_user['subscribe'] == 1 && $login_user['login_type'] == 'wechat' && array_key_exists('is_sign', $login_user) && $login_user['is_sign']) {
            MiddleHandler::siginMemberCouponOnlyWechat($login_user['mid'], '4770');
        }

        //是否刚注册，如果是的话创建积分和检查是否登录送红包
        if (array_key_exists('is_sign', $login_user) && $login_user['is_sign']) {
            $user = MiddleHandler::signAfter($login_user['mid']);
            $login_user['is_sign'] = false;
            $request->session()->put('user', $login_user);
        }

        return $next($request);
    }
}
