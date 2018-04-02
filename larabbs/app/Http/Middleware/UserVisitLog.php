<?php

namespace App\Http\Middleware;

use Carbon\Carbon;
use Closure;
use Tool;
use Cookie;

class UserVisitLog
{
    /**
     * 访问日志
     *
     * @param $request
     * @param Closure $next
     * @param null $guard
     * @author jiangxianli
     * @created_at 2017-05-03 09:38:51
     */
    public function handle($request, Closure $next, $guard = null)
    {
        //分隔符号
        $separator = '@@8@@';
        //当前时间
        $now = Carbon::now();
        //毫秒数
        list($milli_second) = explode(' ', microtime());

        //时间记录
        $log = '[' . $now->format('Y-m-d H:i:s') . ',' . substr($milli_second, 2, 3) . '] [INFO ] ';

        //当前访问地址
        $access_url = $request->getUri();
        $log .= $separator . $access_url . $separator . ',';

        //当前Session ID
        $session_id = array_get($_COOKIE, 'userlogin_session', '');
        $log .= $separator . $session_id . $separator . ',';

        //当前登录会员ID
        $member_id = Tool::getUserId();
        $log .= $separator . $member_id . $separator . ',';

        //当前访问IP
        if ($trusted_proxies = env('TRUSTED_PROXIES')) {
            $request->setTrustedProxies(array($trusted_proxies));
        }
        $ip = $request->server('HTTP_X_FORWARDED_FOR');
        $ip = array_get(explode(',', $ip), '0', '');
        if (!$ip || $ip == 'unknown') {
            $ip = $request->server('REMOTE_ADDR');
        }
        if (!$ip || $ip == 'unknown') {
            $ip = $request->ip();
        }
        \Session::put('visit_user_ip', $ip);
        $log .= $separator . $ip . $separator . ',';

        //URL参数
        $query_str = $request->getQueryString();
        $log .= $separator . ($query_str ? '?' . $query_str : '') . $separator . ',';

        //来源地址
        $http_referer = $request->server('HTTP_REFERER');
        $log .= $separator . $http_referer . $separator . ',';

        //客户端浏览器和操作系统信息
        $user_agent = $request->server('HTTP_USER_AGENT');
        $log .= $separator . $user_agent . $separator . ',';

        //是否第一次访问
        $first_access_flag = Cookie::get('m_last_visit_time') ? 1 : 0;
        $log .= $separator . $first_access_flag . $separator . "\n";

        //写入日志文件
        $log_file_path = storage_path('logs/user_visit_log/' . $now->format('Y-m') . '/' . $now->format('Y-m-d') . '.log');
        $log_file_dir  = dirname($log_file_path);
        if (!file_exists($log_file_dir)) {
            mkdir($log_file_dir, 0755, true);
        }
        file_put_contents($log_file_path, $log, FILE_APPEND);

        //记录访问cookie
        $cookie = Cookie::forever('m_last_visit_time', $now->format('Y-m-d H:i:s'));

        return $next($request)->withCookie($cookie);
    }
}
