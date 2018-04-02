<?php
namespace App\Http\Controllers\Wap;

use Input;
use MiddleHandler;
use App\Http\Controllers\Controller;
use Log;

class CouponController extends Controller
{
    /**
     * 优惠券列表页
     *
     * @return view
     */
    public function getCouponList()
    {
        $request_data = Input::all();

        $login_user = \Tool::loginUser();
        if ($login_user) {
            $request_data['mid'] = $login_user['mid'];
        }
        $response_data = \MiddleHandler::couponPage($request_data);

        if (\Request::ajax()) {
            return json_encode($response_data);
        }

        return view('wap.coupon.list', compact('response_data'));
    }

    /**
     * 优惠券领取
     */
    public function getCouponCard()
    {
        $request_data = Input::all();
        return json_encode(\MiddleHandler::getCouponCard($request_data['id']));
    }

    /**
     * 红包领取
     * @author JokerLinly
     * @date   2017-02-28
     * @return [type]     [description]
     */
    public function getRedCard()
    {
        $request_data = Input::all();
        return json_encode(\MiddleHandler::getRedCard($request_data['redcard_id']));
    }
}
