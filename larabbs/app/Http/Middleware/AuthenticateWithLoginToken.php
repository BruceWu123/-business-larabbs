<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use SeedlingsZO\Extend\ApiClient;
use \Tool;

class AuthenticateWithLoginToken
{
    /**
     * @param $request
     * @param Closure $next
     * @param null $guard
     * @return mixed
     * @author jiangxianli
     * @created_at 2017-03-13 15:58:19
     */
    public function handle($request, Closure $next, $guard = null)
    {

        $login_token = $request->get('loginToken');
        if ($login_token && trim($login_token)) {

            $login_status_url = config('api.userService.login-status');

            $params = ['loginToken' => $login_token];
            $params = \Tool::makeApiParams($params);

            $api_client = new ApiClient();

            $response = $api_client->getApiContent($login_status_url, $params, 'POST', 10);

            $response = (array)json_decode($response, true);
            if (isset($response['code']) && $response['code'] == 0) {
                $login_user = array_get($response, 'data', []);
                $request->session()->put('user', $login_user);
            }
        }


        return $next($request);
    }
}
