<?php
namespace App\Http\Controllers\Wap;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Message;
use MiddleHandler;
use Redirect;
use Tool;
use Log;

class PersonalCenterController extends Controller
{
    /**
     * 初始化
     *
     * @author chenfeng
     * @date 2016-12-26
     */
    public function __construct()
    {
        //$this->middleware('auth2');
    }

    /**
     * 个人中心
     *
     * @author chenfeng
     * @date 2016-12-26
     * @return view
     */
    public function getIndex()
    {
        //用户信息
        $user_arr = Tool::loginUser();
        
        //用户ID
        $member_id = $user_arr['mid'];

        $member_info = MiddleHandler::personalCenter($member_id);

        $user_info = Tool::getCacheUserInfo();

        return view('wap.personal_center.personal_index', compact('user_info', 'member_info'));
    }

    /**
     * 账号管理
     *
     * @author chenfeng
     * @date 2016-12-26
     * @return view
     */
    public function getAccountNumber()
    {
        $user_info = Tool::getCacheUserInfo();
        return view('wap.personal_center.account_number', compact('user_info'));
    }

    /**
     * 个人中心绑定手机
     *
     * @author chenfeng
     * @date 2016-12-26
     * @return view
     */
    public function getCenterBindMobile()
    {
        //用户信息
        $login_user = Tool::getCacheUserInfo();

        //如果已绑定手机号码
        if (!empty($login_user['mobile']) && $login_user['verify_mobile']) {
            return \Redirect::action('Wap\PersonalCenterController@getAccountNumber');
        }

        //发送短信应用场景别名
        $scenarios_alias = \Config::get('sms.scenarios_config.center_bind_mobile_alias');

        return view('wap.personal_center.center_bind_phone', compact('scenarios_alias'));
    }

    /**
     * 个人中心绑定手机号
     *
     * @author chenfeng
     * @date 2016-12-26
     * @param Request $request [description]
     * @return json
     */
    public function postCenterBindMobile(Request $request)
    {
        //如果不是json请求
        $is_ajax_request = Tool::isAjaxRequest();
        if (isset($is_ajax_request['err_code'])) {
            return Message::jsonMessage($is_ajax_request);
        }

        $data = ['url' => action('Wap\PersonalCenterController@getAccountNumber')];

        Tool::delCacheUserInfo();

        return Message::jsonMessage(MiddleHandler::centerBindMobile($request->input()), $data);
    }

    /**
     * 修改绑定手机号码第一步
     *
     * @author chenfeng
     * @date 2016-12-27
     * @return view
     */
    public function getModifyPhone()
    {
        //用户信息
        $user_info = Tool::getCacheUserInfo();

        return view('wap.personal_center.modify_phone', compact('user_info'));
    }

    /**
     * 个人中心修改手机密码校验
     *
     * @author chenfeng
     * @date 2016-12-26
     * @param Request $request array
     * @return array
     */
    public function postCheckPassword(Request $request)
    {
        //如果不是json请求
        $is_ajax_request = Tool::isAjaxRequest();
        if (isset($is_ajax_request['err_code'])) {
            return Message::jsonMessage($is_ajax_request);
        }

        $data = ['url' => action('Wap\PersonalCenterController@getModifyPhoneSecond')];

        return Message::jsonMessage(MiddleHandler::checkPassword($request->input()), $data);
    }

    /**
     * 修改绑定手机号码第二步
     *
     * @author chenfeng
     * @date 2016-12-26
     * @return view
     */
    public function getModifyPhoneSecond()
    {
        //用户信息
        $user_info = Tool::getCacheUserInfo();

        //发送短信应用场景别名
        $scenarios_alias = \Config::get('sms.scenarios_config.update_bind_mobile');

        return view('wap.personal_center.modify_phone_second', compact('scenarios_alias', 'user_info'));
    }

    /**
     * 个人中心修改手机提交
     *
     * @author chenfeng
     * @date 2016-12-26
     * @param Request $request array
     * @return array
     */
    public function postModifyPhoneSecond(Request $request)
    {
        //如果不是json请求
        $is_ajax_request = Tool::isAjaxRequest();
        if (isset($is_ajax_request['err_code'])) {
            return Message::jsonMessage($is_ajax_request);
        }

        $data = ['url' => action('Wap\PersonalCenterController@getModifyPhoneFinished')];

        Tool::delCacheUserInfo();

        return Message::jsonMessage(MiddleHandler::centerBindMobile($request->input(), 1), $data);
    }

    /**
     * 修改绑定手机号码完成页面
     *
     * @author chenfeng
     * @date 2016-12-26
     * @return view
     */
    public function getModifyPhoneFinished()
    {
        //用户信息
        $user_info = Tool::getCacheUserInfo();

        $data = [
            'mobile_phone' => $user_info['mobile'],
        ];

        return view('wap.personal_center.modify_phone_finished', compact('data'));
    }

    /**
     * 修改密码
     *
     * @author chenfeng
     * @date 2016-12-26
     * @return view
     */
    public function getModifyPassword()
    {
        return view('wap.personal_center.modify_password');
    }

    /**
     * 修改密码提交
     *
     * @author chenfeng
     * @date 2016-12-26
     * @param Request $request array
     * @return json
     */
    public function postModifyPassword(Request $request)
    {
        //如果不是json请求
        $is_ajax_request = Tool::isAjaxRequest();
        if (isset($is_ajax_request['err_code'])) {
            return Message::jsonMessage($is_ajax_request);
        }

        $request_data = $request->input();

        //获取用户信息
        $request_data['user_id'] = Tool::getUserId(); //1135721

        //返回数据
        $return_data = [
            'url' => action('Wap\PersonalCenterController@getAccountNumber'),
        ];

        return Message::jsonMessage(MiddleHandler::centerUpdatePassword($request_data), $return_data);
    }

    /**
     * 地址列表
     *
     * @author chenfeng
     * @date 2016-12-26
     * @return view
     */
    public function getAddressList()
    {
        //用户信息
        $member_id = Tool::getUserId();
        //$member_id       = 1075392;
        $consi_info_list = MiddleHandler::memberConsiInfoLogList($member_id);

        return view('wap.personal_center.address_list', compact('consi_info_list'));
    }

    /**
     * 修改地址
     *
     * @author chenfeng
     * @date 2016-12-27
     * @return view
     */
    public function getEditAddress($id)
    {
        //用户信息
        $member_id = Tool::getUserId();
        //$member_id = 1075392;

        $consi_info_log_info = MiddleHandler::getConsiInfoLogInfo($id, $member_id);
        // dd($consi_info_log_info);
        if (isset($consi_info_log_info['err_code'])) {
            return Redirect::action('Wap\PersonalCenterController@getAddressList');
        }

        return view('wap.personal_center.address_modify', compact('consi_info_log_info'));
    }

    /**
     * 编辑地址
     *
     * @author chenfeng
     * @date 2016-12-27
     * @param Request $request [description]
     * @return json
     */
    public function postEditAddress(Request $request)
    {
        //用户信息
        $member_id = Tool::getUserId();
        //$member_id = 1075392;

        //如果不是json请求
        $is_ajax_request = Tool::isAjaxRequest();
        if (isset($is_ajax_request['err_code'])) {
            return Message::jsonMessage($is_ajax_request);
        }

        $data = ['url' => action('Wap\PersonalCenterController@getAddressList')];

        return Message::jsonMessage(MiddleHandler::editConsiInfoLog($member_id, $request->input()), $data);
    }

    /**
     * 新增地址
     *
     * @author chenfeng
     * @date 2016-12-27
     * @return view
     */
    public function getAddressAdd()
    {
        return view('wap.personal_center.address_add');
    }

    /**
     * 新增收货地址
     *
     * @author chenfeng
     * @date 2016-12-28
     * @param Request $request 请求数据
     * @return json
     */
    public function postAddressAdd(Request $request)
    {
        //用户信息
        $member_id = Tool::getUserId();

        //如果不是json请求
        $is_ajax_request = Tool::isAjaxRequest();
        if (isset($is_ajax_request['err_code'])) {
            return Message::jsonMessage($is_ajax_request);
        }

        $data = ['url' => action('Wap\PersonalCenterController@getAddressList')];

        return Message::jsonMessage(MiddleHandler::addConsiInfoLog($member_id, $request->input()), $data);
    }

    /**
     * 删除用户地址
     *
     * @author chenfeng
     * @date 2016-12-28
     * @param integer $id 地址ID
     * @return json
     */
    public function postDeleteAddress($id)
    {
        //用户信息
        $member_id = Tool::getUserId();
        //$member_id = 1075392;

        //如果不是json请求
        $is_ajax_request = Tool::isAjaxRequest();
        if (isset($is_ajax_request['err_code'])) {
            return Message::jsonMessage($is_ajax_request);
        }

        $data = ['url' => action('Wap\PersonalCenterController@getAddressList')];

        return Message::jsonMessage(MiddleHandler::deleteConsiInfoLog($id, $member_id), $data);
    }

    /**
     * 我的红包&优惠券
     * @author JokerLinly
     * @date   2017-01-06
     * @return [type]     [description]
     */
    public function getCouponAndCard(Request $request, $type)
    {
        $type_page = 1;
        if ($request->has('page')) {
            $type_page = $request->get('page');
        }
        $member_id     = Tool::getUserId();
        $response_data = MiddleHandler::getCouponAndCard($member_id, $type, $type_page);
        if (\Request::ajax()) {
            return json_encode($response_data);
        }
        $count = MiddleHandler::getCouponAndCardCount($member_id);
        return view('wap.personal_center.' . $type, compact('count', 'response_data'));
    }

    /**
     * 关于我们
     */
    public function getAboutMe()
    {
        return view('wap.personal_center.about_me');
    }

    /**
     * 会员积分
     * @author JokerLinly
     * @date   2017-01-09
     * @return [type]     [description]
     */
    public function getIntegral(Request $request)
    {
        $member_id = Tool::getUserId();
        $page      = 1;
        if ($request->has('page')) {
            $page = $request->get('page');
        }
        $response_data = MiddleHandler::getScoreDetail($member_id, $page, 10);

        if (\Request::ajax()) {
            return json_encode($response_data);
        }

        $score_total = MiddleHandler::getPersonalScore($member_id);
        if (isset($score_total['err_code'])) {
            return Message::jsonMessage($score_total);
        }

        return view('wap.personal_center.integral', compact('response_data', 'score_total'));
    }

    public function getIntegralRule()
    {
        return view('wap.personal_center.integral_rule');
    }

    /**
     * 资料管理
     * @author JokerLinly
     * @date   2017-01-10
     * @return [type]     [description]
     */
    public function getInfoManagement()
    {
        $member_id = Tool::getUserId();
        $data      = ['mid', 'sex', 'birthday', 'growth_value'];
        $member    = MiddleHandler::getPersonalData($member_id, $data);
        if (isset($member['err_code'])) {
            return Message::jsonMessage($member);
        }
        return view('wap.personal_center.data_management', compact('member'));
    }

    /**
 * 2018.1会员专题日
 * @author Bruce
 * @date   2017-12-29
 * @param  string     $value [description]
 */
    public function memberLottery(Request $request)
    {
        $response_data = MiddleHandler::memberLottery();
        return $response_data;
    }

    /**
     * 2018.2会员专题日
     * @author Bruce
     * @date   2017-1-29
     * @param  string     $value [description]
     */
    public function FmemberLottery(Request $request)
    {
        $response_data = MiddleHandler::FmemberLottery();
        return $response_data;
    }

    /**
     * 2018.1呼吸系统专题签到赢取积分功能
     * @author Bruce
     * @date   2018-1-2
     * @param  string     $value [description]
     */
    public function  getScore(Request $request)
    {
        $response_data = MiddleHandler::getScore();
        return $response_data;

    }


    /**
     * 修改个人资料
     * @author JokerLinly
     * @date   2017-01-10
     * @param  string     $value [description]
     */
    public function postPersonData(Request $request)
    {
        $member_id = Tool::getUserId();
        $data      = [];

        if ($request->has('user_sex')) {
            $data['sex'] = $request->get('user_sex');
        }

        if ($request->has('user_birthday')) {
            $data['birthday'] = $request->get('user_birthday');
        }

        $result = MiddleHandler::postPersonData($member_id, $data);
        if (isset($result['err_code'])) {
            return Message::jsonMessage($result);
        }
        return json_encode($result);
    }

    /**
     * 退出登录
     * @author JokerLinly
     * @date   2017-02-08
     * @return [type]     [description]
     */
    public function getLogout(Request $request)
    {
        \Session::pull('user', 'default');
        return redirect()->action('Wap\LoginController@getIndex');
    }

    /**
     * 获取 Java 活动数据
     * @author JokerLinly 2017-05-02
     * @param Request $request 请求数据
     * @return json
     */
    public function getJavaActivitiesData(Request $request)
    {
        $activity = $request->activit;
        $url = \Config::get('other.'.$activity.'.query_url');
        if (empty($url)) {
            return Message::jsonMessage(Message::getErrorMessage(10000), []);
        }
        $data = $request->all();
        $data['mid'] = Tool::getUserId();
        $method = \Config::get('other.'.$activity.'.method');
        return \Tool::sendRequestForJava($url, $data, $method);
    }
}
