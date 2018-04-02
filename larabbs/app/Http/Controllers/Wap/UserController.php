<?php
namespace App\Http\Controllers\Wap;

use App\Http\Controllers\Controller;
use MiddleHandler;

/**
 * 用户控制器
 */
class UserController extends Controller
{

    /**
     * 用户登录页
     * @author JokerLinly
     * @date   2016-12-19
     * @return [type]     [description]
     */
    public function getLogin()
    {
        return view('wap.login.login_index');
    }

    /**
     * 用户普通登录
     * @author JokerLinly
     * @date   2016-12-19
     * @return [type]     [description]
     */
    public function postLogin()
    {
        if (session()->get('user')) {
            return Redircet::to('/');
        }

        // $account  = Input::get('account');
        // $password = Input::get('password');
        $is_email = true;
        $account  = '18613031321';
        $password = '123456';
        $data     = [
            'account'  => $account,
            'password' => $password,
        ];
        $user = MiddleHandler::login($data);
        if (!$user) {
            return false;
        }
        session('user', $user);
        return true;
    }

    /**
     *
     * @author JokerLinly
     * @date   2016-12-16
     * @return [type]     [description]
     */
    public function postUserLoginByVcode()
    {
        #
    }

    /**
     * 判断手机是否存在用户表中,不存在就提示让他注册
     * @author JokerLinly
     * @date   2016-12-26
     * @return [json]     [description]
     */
    public function postMobileIsSign()
    {

    }

    /**
     *
     * @author JokerLinly
     * @date   2016-12-26
     * @return [json]     [description]
     */
    public function postMobileGetVcode()
    {
        # code...
    }
}
