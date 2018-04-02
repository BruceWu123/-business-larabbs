<?php
namespace App\Http\Controllers\Wap;

use App\Http\Requests\Request;
use Illuminate\Support\Facades\Response;
use App\Http\Controllers\Controller;
use Redirect;
use Validator;
use Input;
use Session;
use MiddleHandler;

class CartController extends Controller
{
    /**
     * 初始化购物车
     * @author JokerLinly
     * @date   2016-10-20
     * @return array
     */
    public function getIndex()
    {
        $login_user = \Tool::loginUser();
        // 判断是否登录
        if (!$login_user) {
            if (!Session::has('cart_temp')) {
                return Redirect::action('Wap\CartController@getEmptyCart');
            }
            $cart_temp = Session::get('cart_temp');
            $response_data = MiddleHandler::updateTempSession($cart_temp);
            return view('wap.cart.index', compact('response_data'));
        }

        $user_id = $login_user['mid'];
        //判断登录前是否存在临时购物车 如果有合并到登录用户购物车表，并删除临时购物车session
        if (Session::has('cart_temp')) {
            $cart_temp = Session::get('cart_temp');
            $update = MiddleHandler::addSessionGoodsCart($cart_temp);
            if ($update) {
                Session::pull('cart_temp', 'default');
            }
            $response_data = MiddleHandler::getCartList($user_id);
            return view('wap.cart.index', compact('response_data'));
        }
        //刷新购物车直接查库
        $response_data = MiddleHandler::getCartList($user_id);
        if (empty($response_data['stops'])) {
            return Redirect::action('Wap\CartController@getEmptyCart');
        }

        return view('wap.cart.index', compact('response_data'));
    }

    /**
     * 空购物车页面
     * @author zhangjie
     * @date   2016-10-20
     * @return array
     */
    public function getEmptyCart()
    {
        $temp_list = Session::get('temp_goods');
        $recently_viewed = array();
        if (count($temp_list) > 0) {
            $recently_viewed = MiddleHandler::getTempGoodsInfo(array_reverse($temp_list));
        }
        $response_data = array(
            'is_login' => false,
            'recently_viewed' => $recently_viewed
        );

        $login_user = \Tool::loginUser();
        if ($login_user) {
            $response_data['is_login'] = true;
        }
        return view('wap.cart.empty_cart', compact('response_data'));
    }

    /**
     * 商品增加到购物车
     * @author JokerLinly
     * @date   2016-10-18
     * @return json
     */
    public function postAddCart()
    {
        //商品添加购物车限制
        $num = MiddleHandler::cartProductsNum();
        if ($num >= 70) {
            return Response::json(['status'=>"3"]);
        }

        $request_data = Input::all();
        $rules = [
            'pid'           => 'required|integer',
            'shop_code'     => 'required|integer',
            'quantity'      => 'required|integer',
            'limitId'       => 'required|integer',
        ];

        $validator = Validator::make($request_data, $rules);
        if ($validator->fails()) {
            return json_encode($validator->errors()->toArray());
        }
        $response_data = MiddleHandler::addCart($request_data);
        return Response::json($response_data);
    }

    /**
     * 增加套餐商品到购物车
     * @author JokerLinly
     * @date   2016-10-19
     * @return json
     */
    public function postAddPackageCart()
    {
        //商品添加购物车限制
        $num = MiddleHandler::cartProductsNum();
        if ($num >= 70) {
            return Response::json(['status'=>"3"]);
        }

        $request_data = Input::all();
        $rules = [
            'pids'          => 'required',
            'packageId'     => 'required|integer',
            'quantity'      => 'required|integer',
        ];

        $validator = Validator::make($request_data, $rules);
        if ($validator->fails()) {
            return json_encode($validator->errors()->toArray());
        }

        $products_id = explode(',', $request_data['pids']);
        if (count($products_id) <= 1) {
            return json_encode(['error'=>"数据异常"]);
        }
        foreach ($products_id as $key => $products) {
            $string = explode('_', $products);
            if (isset($request_data['products_id'])) {
                array_push($request_data['products_id'], $string[0]);
            } else {
                $request_data['products_id'] = [$string[0]];
            }
            $request_data['shop_code'] = $string[1];
        }
        $response_data = MiddleHandler::addPackageCart($request_data);
        return Response::json($response_data);
    }

    /**
     * 更新购物车商品数量
     * @author JokerLinly
     * @date   2016-10-26
     * @return json
     */
    public function postCartGoodsNum()
    {
        $request_data = Input::all();
        $response_data = MiddleHandler::changeCartGoodsNum($request_data);
        return Response::json($response_data);
    }

    /**
     * 删除购物车商品
     * @author JokerLinly
     * @date   2016-10-26
     * @return json
     */
    public function postCartGoodsDel()
    {
        $request_data = Input::all();
        $response_data = MiddleHandler::delCartGoods($request_data);
        return Response::json($response_data);
    }

    /**
     * 编辑购物车商品
     * @author JokerLinly
     * @date   2016-10-26
     * @param  json
     */
    public function postCartGoodsEdit()
    {
        $request_data = Input::all();
        $response_data = MiddleHandler::editCartGoods($request_data);
        return Response::json($response_data);
    }

    /**
     * 购物车结算判断
     * @author JokerLinly
     * @date   2016-11-02
     * @return json
     */
    public function postCartSettlement()
    {
        $request_data = Input::all();
        $response_data = MiddleHandler::cartSettlement($request_data);
        return Response::json($response_data);
    }

    /**
     * 获取商品规格列表接口
     * @author JokerLinly
     * @date   2016-11-08
     * @return json
     */
    public function postProductNormList()
    {
        $response_data = [];
        $request_data = Input::all();
        if (!empty($request_data)) {
            $response_data = MiddleHandler::productNormList($request_data);
        }
        return Response::json($response_data);
    }
}
