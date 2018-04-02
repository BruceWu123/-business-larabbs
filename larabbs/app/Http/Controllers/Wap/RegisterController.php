<?php
namespace App\Http\Controllers\Wap;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Message;
use MiddleHandler;

class RegisterController extends Controller
{
    /**
     * 绑定
     *
     * @author yufuchu
     * @param Request $request
     * @return view
     */
    public function getIndex()
    {
        //发送短信应用场景别名
        $scenarios_alias = \Config::get('sms.scenarios_config.register_account_alias');

        return view('wap.register.register_index', compact('scenarios_alias'));
    }

    public function store(Request $request)
    {
        /**
         * $accountData中需要包含account,password,sms_code 3个键值对
         * 如果Request请求传入的其它表单键值，则需要在控制器中转换
         * mode键值为手机账号注册模式
         * 由于wap端只有一种普通注册方式这里考虑不通过请求传参
         * @var array
         */

        $accountData                     = [];
        $accountData['account']          = $request->input('mobile_phone');
        $accountData['password']         = $request->input('password');
        $accountData['confirm_password'] = $request->input('confirm_password');
        $accountData['sms_code']         = $request->input('sms_code');
        $accountData['mode']             = 'mobile';
        $loginData                       = MiddleHandler::register($accountData);
        if (isset($loginData['err_code'])) {
            return Message::jsonMessage($loginData);
        }
        \Tool::authLogin($loginData);

        return Message::jsonMessage([], $loginData);

    }
}
