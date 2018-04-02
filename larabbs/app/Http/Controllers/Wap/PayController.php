<?php
namespace App\Http\Controllers\Wap;

use App\Http\Controllers\Controller;
use Config;
use Input;
use MiddleHandler;
use Tool;
use Log;

class PayController extends Controller
{
    /**
     * 支付页面
     *
     * @author yufuchu
     * @param Request $request
     * @return view
     */
    public function getIndex($pid, $orderno = 0, $type = 0)
    {
        $pid = addslashes($pid);
        if (!empty($orderno)) {
            $orderno = addslashes($orderno);
        } else {
            $orderno = 0;
        }

        //判断用户是否登陆
        $login_user = Tool::loginUser();
        if (!$login_user) {
            return redirect('/login');
        }

        //如果是微信登陆则需要微信openid，用户微信支付
        $code = Input::get('code', '');
        if (strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') && empty($code)) {
            /*$redirect_uri = urlencode(action("Wap\PayController@getIndex", [$pid, $orderno, $type]));
            $scope        = Config::get('pay.wechat_pay.scope'); //"snsapi_base";
            $appid        = Config::get('pay.wechat_pay.appid');
            $url          = Config::get('pay.wechat_pay.code_url') . "?appid={$appid}&redirect_uri={$redirect_uri}&response_type=code&scope={$scope}&state=1#wechat_redirect";*/
            $url = "http://user.800pharm.com/wechat/snsapibase/{$pid}/{$orderno}/{$type}";
            return redirect()->to($url);
        }

        if (!empty($code) && empty(\Cache::get($code))) {
            $secret       = Config::get('pay.wechat_pay.secret');
            $appid        = Config::get('pay.wechat_pay.appid');
            $openid_url   = Config::get('pay.wechat_pay.openid_url');
            $requset_data = [
                'appid'      => $appid,
                'secret'     => $secret,
                'code'       => $code,
                'grant_type' => 'authorization_code',
            ];
            $send_request = \Tool::sendRequest($openid_url, 'GET', $requset_data);
            if (isset($send_request['result']) && isset($send_request['response']) && $send_request['result'] == true) {
                $response = json_decode($send_request['response'], true);
                if (!isset($response['openid'])) {
                    return redirect()->action('Wap\PayController@getIndex', [$pid, $orderno, $type]);
                }

                \Cache::add($code, $response['openid'], 10);
            }
        }

        $open_id = \Cache::get($code);

        $other    = ['open_id' => $open_id];
        $pay_info = MiddleHandler::orderPayInfo($pid, $other, $orderno);

        //简单处理如果是线上并且已经支付的订单跳转去到订单提醒页面
        if (!empty($pay_info['online_price']) && $pay_info['pay_status']) {
            // $pid = empty($orderno) ? $pid : $orderno;
            return redirect()->action('Wap\PayController@getReturn', ['order_id' => $pid, 'orderno' => $orderno]);
        }

        //如果全部是货到付款单
        if (empty($pay_info['online_price'])) {
            return redirect()->action('Wap\PayController@getOffline', ['order_id' => $pid]);
        }

        //微信支付暂时用java的支付@updated_at:2017-03-31 @author:chenfneg; 屏蔽一下逻辑，公众号已升级采用新
        /*$wechat_url = '';
        if (strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger')) {
        $weatch_data = [
        'ono'      => empty($orderno) ? $pid : $orderno,
        'payType'  => 10049,
        'orderno'  => empty($orderno) ? 0 : $orderno,
        'batchId'  => empty($orderno) ? $pid : $orderno,
        'entityId' => '',
        ];
        $wechat_url = "http://www.800pharm.com/shop/pay/paySubmit_m.html?" . http_build_query($weatch_data);
        }*/
        
        $pay_info = MiddleHandler::orderPayInfo($pid, $other, $orderno, ['pay_status' => 0]);
        $wechat_alipay_url = action('Wap\PayController@getPrompt', [$pid, $orderno]);

        return view('wap.pay.index', compact('pay_info', 'wechat_alipay_url', 'type'));
    }

    /**
     * 货到付款订单显示
     *
     * @author chenfeng
     * @date 2016-11-29
     * @param string $batch_oid 订单批号
     * @return view
     */
    public function getOffline()
    {
        //订单信息
        $order_id = addslashes(Input::get('order_id', ''));
        $param    = ['order_id' => $order_id];
        $return   = MiddleHandler::payReturnOperation($param);

        //将数组元素初始化为变量
        extract($return);

        return view('wap.pay.index_pay_success', compact('return_result', 'recently_viewed', 'adver_info'));
    }

    /**
     * 支付异步回调
     *
     * @author chenfeng
     * @return string
     */
    public function postNotify()
    {
        $request_data  = Input::all();
        $notify_result = MiddleHandler::payNotifyOperation($request_data);
        if (isset($notify_result['err_code'])) {
            return ['result' => 'fail'];
        }

        return ['result' => 'success'];
    }

    /**
     * 支付同步通知
     *
     * @author yufuchu
     * @return view
     */
    public function getReturn()
    {
        //订单信息
        $param  = Input::all();

        //log::info($param);

        $return = MiddleHandler::payReturnOperation($param);

        //将数组元素初始化为变量
        extract($return);

        return view('wap.pay.index_pay_success', compact('return_result', 'recently_viewed', 'adver_info'));
    }

    /**
     * 支付提醒跳转
     *
     * @author yufuchu
     * @param Request $request
     * @return view
     */
    public function getPrompt($pid, $orderno = '')
    {
        $pid = addslashes($pid);
        if (!empty($orderno)) {
            $orderno = addslashes($orderno);
        }
        if (strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger')) {
            //判断用户是否登陆
            $login_user = Tool::loginUser();
            if (!$login_user) {
                return redirect('/login');
            }

            return view('wap.pay.index_pay_prompt');
        }

        $url = MiddleHandler::getWechatAlipayUrl($pid, $orderno);

        return redirect()->to($url);
    }
}
