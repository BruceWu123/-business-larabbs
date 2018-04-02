<?php
namespace App\Http\Controllers\Wap;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Input;
use Message;
use MiddleHandler;
use Tool;

class BindController extends Controller
{
    /**
     * 第三方登陆绑定手机
     *
     * @author yufuchu
     * @param Request $request
     * @return view
     */
    public function getIndex()
    {
        //发送短信应用场景别名
        $scenarios_alias = \Config::get('sms.scenarios_config.third_party_bind_mobile_alias');

        return view('wap.bind_phone.bind_index', compact('scenarios_alias'));
    }

    /**
     * 提交第三方登陆绑定手机
     *
     * @author chenfeng
     * @date 2016-12-26
     * @param Request $request 请求参数
     * @return array
     */
    public function postAuthorizationBindMobile(Request $request)
    {
        //如果不是json请求
        $is_ajax_request = Tool::isAjaxRequest();
        if (isset($is_ajax_request['err_code'])) {
            return Message::jsonMessage($is_ajax_request);
        }

        $data = ['url' => action('Wap\PersonalCenterController@getAccountNumber')];

        return Message::jsonMessage(MiddleHandler::authorizationBindMobile($request->input()), $data);
    }

    /**
     * 检测手机号码是否绑定
     *
     * @author chenfeng
     * @date 2016-12-26
     * @param Request $request 请求数据
     * @return array
     */
    public function postCheckMobileIsBind(Request $request)
    {
        //如果不是json请求
        $is_ajax_request = Tool::isAjaxRequest();
        if (isset($is_ajax_request['err_code'])) {
            return Message::jsonMessage($is_ajax_request);
        }

        return Message::jsonMessage(MiddleHandler::checkMobileIsBind($request->input()));
    }
}
