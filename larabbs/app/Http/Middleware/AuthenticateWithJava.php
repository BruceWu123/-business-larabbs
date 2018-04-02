<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use \Tool;

class AuthenticateWithJava
{
    /**
     * 如果没有登录，则返回Java登录页面[无法保证正常返回]
     * 但不使用中间件，在现在的状态下，Member、Order这样必须登录的控制器中，
     * 每个Controller::Action中都要判断登录。过于麻烦
     *
     * @warning 暂时使用这个Middleware，判断登录和跳转.
     * 如果你知道它是做什么的，但是用不上，请不要使用。
     * 如果你不知道，那请不要用。
     *
     * -- huangcan
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $guard
     * @author huangcan
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {
        $login_user = Tool::loginUser();

        $is_login = false;

        if (isset($login_user['mid']) && $login_user['mid'] > 0) {
            $is_login = true;
        }

        if (!$is_login) {
            if ($request->ajax() || $request->wantsJson()) {
                $response_data['status'] = 1;
                $response_data['msg']    = "用户未登录！";
                return response($response_data);
            } else {
                $url = $request->url();
                // 未登录的非异步请求，跳转到Java登录
                // 如果需要跳回到和当前不同的url，需要再加入这些逻辑
                $cart_url = 'http://' . $_SERVER['SERVER_NAME'] . '/cart';
                if ($url) {
                    if (strstr($url, 'order')) {
                        return redirect('/login?return=' . $cart_url);
                    }
                    return redirect('/login?return=' . $cart_url);
                } else {
                    // 参数url错误则不设跳回
                    return redirect('/login');
                }
            }
        }

        return $next($request);
    }
}
