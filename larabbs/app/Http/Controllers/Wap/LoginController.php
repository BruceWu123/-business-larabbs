<?php
namespace App\Http\Controllers\Wap;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Message;
use MiddleHandler;
use Redirect;
use Tool;
use Log;
use DB;
use Cookie;

class LoginController extends Controller
{
    /**
     * 登陆页面
     * @author yufuchu
     * @param Request $request
     * @return view
     */
    public function getIndex(Request $request)
    {
        if ($request->has('token')) {
            $user = \Cache::pull($request->token);
            \Tool::authLogin($user);

            //清除用户缓存
            Tool::delCacheUserInfo();
        }

        $login_user = \Tool::loginUser();

        if (!$login_user) {
//            return Redirect::action('Wap\LoginController@getPhone');//临时关闭账密登录
            return view('wap.login.login_index');
        }

        if (array_key_exists('is_sign', $login_user) && $login_user['is_sign'] && array_key_exists('verify_mobile', $login_user) && $login_user['verify_mobile'] == 0) {
            
            return Redirect::action('Wap\BindController@getIndex');
        
        }
        //统一登录响应返回函数
        return $this->loginResponse();
    }


    /**
     * 普通登录
     * @author JokerLinly
     * @date   2017-01-04
     * @param  Request    $request [description]
     * @return [objet]              [description]
     */
    public function postIndex(Request $request)
    {
        $user = MiddleHandler::login($request->only(['account', 'password']));

        if (isset($user['err_code'])) {
            return Message::jsonMessage($user);
        }


        //登录送积分
        $users = MiddleHandler::login_score($user);
        
        \Tool::authLogin($user);
        //var_dump(\Tool::authUser());exit;
        //统一登录响应返回函数

        return $this->loginResponse();
    }


    /**
     * 手机验证码快捷登录页面
     * @author JokerLinly
     * @date   2016-12-30
     * @return [type]     [description]
     */
    public function getPhone()
    {
        //发送短信别名
        $scenarios_alias = \Config::get('sms.scenarios_config.send_fastlogin_to_user_alias');

        return view('wap.login.login_phone', compact('scenarios_alias'));
    }

    /**
     * 检测手机号是否存在
     * @author JokerLinly
     * @date   2017-01-05
     * @param  Request    $request [description]
     * @return [type]              [description]
     */
    public function checkMobile(Request $request)
    {
        $mobile = $request->mobile_phone;

        if (empty($mobile)) {
            return Message::jsonMessage(Message::getErrorMessage(10001));
        }
        $user = MiddleHandler::checkMobile($mobile);
        if (!$user) {
            return ['code' => 0, 'msg' => '可以注册'];
        }
        return Message::jsonMessage(Message::getErrorMessage(11026));
    }

    /**
     * 手机验证码快捷登录
     * @author JokerLinly
     * @date   2016-12-30
     * @return [type]     [description]
     */
    public function postLoginByVcode(Request $request)
    {

        $data['mobile_phone'] = $request->mobile_phone;
        $data['sms_code']     = $request->sms_code;

        $user = MiddleHandler::loginByVcode($data);

        log::info($user);

        //登录送积分
        $users = MiddleHandler::login_score($user);

        if (isset($user['err_code'])) {
            return Message::jsonMessage($user);
        }
        \Tool::authLogin($user);

        //统一登录响应返回函数
        return $this->loginResponse();
    }

    /**
     * 获取图片验证码
     * @author chenfeng
     * @date 2016-12-20
     * @return resource
     * @SWG\GET(
     *     path="/captcha",
     *     tags={"获取图形验证码"},
     *     summary="获取图形验证码",
     *     description="",
     *     operationId="getCaptcha",
     *     produces={"image/jpeg"},
     *     @SWG\Parameter(
     *      in="formData",
     *      name="scenarios_alias",
     *      type="string",
     *      description="短信场景",
     *      default="send_fastlogin_to_user",
     *      required=true,
     *    ),
     *    @SWG\Response(response="default", description="操作成功")
     * )
     */
    public function getCaptcha(Request $request)
    {
        $scenarios_alias = $request->input('scenarios_alias');
        return \Tool::getCaptcha($scenarios_alias);
    }

    /**
     * 发送短信
     * @author chenfeng
     * @date 2016-12-29
     * @return [type] [description]
     *  @SWG\POST(path="/send/sms",
     *   tags={"发送短信"},
     *   summary="发送短信",
     *   description="",
     *   operationId="postSendSms",
     *   produces={"application/json"},
     *   @SWG\Parameter(
     *     in="formData",
     *     name="scenarios_alias",
     *     type="string",
     *     description="短信场景",
     *     required=true,
     *     default="send_fastlogin_to_user"
     *   ),
     *   @SWG\Parameter(
     *     in="formData",
     *     name="mobile_phone",
     *     type="integer",
     *     description="手机号",
     *     required=true,
     *   ),
     *   @SWG\Parameter(
     *     in="formData",
     *     name="captcha_code",
     *     type="string",
     *     description="图形验证码",
     *     required=false,
     *   ),
     *   @SWG\Response(response="default", description="操作成功")
     * )
     */
    public function postSendSms(Request $request)
    {
        //存储短信验证码场景发送次数,用于判定是否超出短信发送界限值
        if ($scenarios_alias = $request->input('scenarios_alias')) {
            //$ckey  = 's2c-' . \Session::getId() . '.scenarios_alias.' . $scenarios_alias . '.count';//sessionid每次请求变动，所以无法对请求计总数
            $ckey  = 's2c-' . $request->get('mobile_phone') . '.scenarios_alias.' . $scenarios_alias . '.count';
            $count = \Cache::get($ckey, 0) + 1;
            \Cache::put($ckey, $count, Carbon::now()->addMinutes(5));
            //用户未有提交图片验证码，并且5分钟内发送3次短信则返回提示开启图片验证码
            if (!($captcha_code = $request->input('captcha_code')) && $count >= 3) {
                return Message::jsonMessage(Message::getErrorMessage(15020));
            }
        }

        $requset_data                  = $request->all();
        $requset_data['platform_code'] = 'm.800pharm.com';
        $send_url                      = \Config::get('sms.send_code_url');
        $send_result                   = \Tool::sendSmsService($requset_data, $send_url);
        if (isset($send_result['err_code'])) {
            return Message::jsonMessage($send_result);
        }

        $sms_code = $send_result['verify_code'];
        //保存短信验证码到cookie用于前台验证判断
        if ($scenarios_alias = $request->input('scenarios_alias')) {
            //保存验证码到cookie，用于前台验证效果
            \Cookie::queue('sms_code.' . $scenarios_alias, md5($sms_code), 5, null, null, false, false);
        }

        //返回至浏览器前端时一定需要去掉验证码字段，避免被用于破解
        unset($send_result['verify_code']);
        return Message::jsonMessage([], $send_result);
    }

    /**
     * 忘记密码
     * @author chenfeng
     * @date 2016-12-28
     * @return view
     */
    public function getForgetPassword()
    {
        //发送短信应用场景别名
        $scenarios_alias = \Config::get('sms.scenarios_config.login_foget_password_alias');

        return view('wap.login.forget_password', compact('scenarios_alias'));
    }

    /**
     * 提交找回密码
     * @author chenfeng
     * @date 2016-12-20
     * @return [type] [description]
     */
    public function postForgetPassword()
    {
        $requset_data = \Input::all();

        //如果不是ajax请求
        $is_ajax_request = Tool::isAjaxRequest();
        if (isset($is_ajax_request['err_code'])) {
            return Message::jsonMessage($is_ajax_request);
        }

        $data = ['url' => action('Wap\LoginController@getIndex')];

        return Message::jsonMessage(MiddleHandler::forgetPassword($requset_data));
    }
}
