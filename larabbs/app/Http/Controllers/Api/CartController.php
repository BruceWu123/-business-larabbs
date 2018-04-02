<?php
namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use App\Http\Controllers\Controller;
use Redirect;
use SeedlingsZO\Extend\ApiClient;
use Validator;
use Input;
use Session;
use MiddleHandler;
use URL;
use \Tool;

class CartController extends Controller
{
    /**
     * 商品增加到购物车
     *
     * @param Request $request
     * @author jiangxianli
     * @created_at 2017-03-13 15:34:48
     */
    public function getAddCart(Request $request)
    {
        //来访地址
        \Cache::forever('api_from_url', $request->server('HTTP_REFERER'));

        //获取所有请求参数
        $params = $request->all();

        //校验接口签名
        $response = $this->checkApiSign($params);
        if ($response['code'] > 0) {
            return view('wap.cart.error')->withMsg($response['msg']);
        }

        //参数校验
        $validator_rule = [
            'standardSkuIdArr' => 'required|regex:/^[1-9]+[0-9,]+$/',
            'prescriptionId'   => 'required|integer|min:1',
        ];
        $validator      = Validator::make($params, $validator_rule);
        if ($validator->fails()) {
            $msg = '参数错误！';
            return view('wap.cart.error')->withMsg($msg);
        }

        //检查处方签是否被添加过
        $member_prescription = MiddleHandler::findPrescription($params['appId'] . '_' . $params['prescriptionId']);
        if ($member_prescription) {
            return redirect()->action('Wap\CartController@getIndex');
        }

        //获取生成的SKU信息
        $standard_sku_id_arr    = explode(',', $params['standardSkuIdArr']);
        $standard_sku_count_arr = array_count_values($standard_sku_id_arr);
        $response               = $this->getProductInfoByStandardSku($standard_sku_id_arr);
        if ($response['code'] > 0) {
            return view('wap.cart.error')->withMsg($response['msg']);
        }

        $better_sku_map = array_get($response, 'data.better_sku_map', []);

        //添加购物车错误
        $add_errors = [];

        if (empty($better_sku_map)) {
            //购物车已存在商品数量
            $cart_number = MiddleHandler::cartProductsNum();
            if ($cart_number <= 0) {
                $msg = '抱歉，医生推荐的药品暂时缺货。建议您可携用药建议到附近正规药房购买。';
                return view('wap.cart.error')->withMsg($msg);
            } else {
                $add_errors = [
                    'type' => 'empty', //没找到商品
                    'msg'  => '抱歉，医生推荐的药品暂时缺货，先看下之前推荐的其他商品吧。'
                ];
                \Session::put('api_add_cart_error', $add_errors);
                return redirect()->action('Wap\CartController@getIndex');
            }
        }

        $success_add_carts = []; //成功添加到购物车的商品信息
        $choose_pid        = []; //成功添加到购物车的商品ID
        foreach ($better_sku_map as $better_sku) {
            $data          = [
                'pid'           => $better_sku['id'],
                'shop_code'     => $better_sku['shop_code'],
                'quantity'      => $standard_sku_count_arr[$better_sku['standard_sku_id']],
                'limitId'       => 0,
                'type'          => 0,
                'skin_color_id' => "0",
                'size_id'       => "0",
                'productCode'   => "0",
                'adminId'       => "0",
                'adminAccount'  => "",
                'isBack'        => "0",
                't'             => time()
            ];
            $response_data = MiddleHandler::addCart($data);
            if ($response_data['status'] == 0) {
                $success_add_carts[] = $better_sku['common_name'] . $better_sku['specification_name'];
                $choose_pid[]        = $better_sku['id'];
            }
        }

        //未能成功添加所有商品，提示"抱歉，只有找到希爱力10片(产品名+规格)和金戈 5片（产品名+规格）"
        if (count($success_add_carts) > 0 && count($success_add_carts) < count($standard_sku_count_arr)) {
            $msg        = "抱歉，只有找到" . implode(' 和 ', $success_add_carts);
            $add_errors = [
                'type' => 'out_of_stock', //缺货
                'msg'  => $msg,
            ];
        }

        //没有成功添加商品
        if (empty($success_add_carts)) {
            $add_errors = [
                'type' => 'empty', //没找到商品
                'msg'  => '抱歉，医生推荐的药品暂时缺货，先看下之前推荐的其他商品吧。'
            ];
            \Session::put('api_add_cart_error', $add_errors);
            return redirect()->action('Wap\CartController@getIndex');
        }

        //添加到购物车错误信息保存到闪存中
        if (!empty($add_errors)) {
            \Session::put('api_add_cart_error', $add_errors);
        }
        //成功添加的商品ID保存到闪存中
        if (!empty($choose_pid)) {
            \Session::put('api_choose_pid', $choose_pid);
        }

        //获取叮叮医生处方签
        if (!empty($success_add_carts)) {
            $prescription = MiddleHandler::getDdysPrescription($params['prescriptionId']);
            //保存处方签
            if (is_array($prescription) && !isset($prescription['err_code'])) {
                $login_user                     = Tool::loginUser();
                $prescription['appId']          = $params['appId'];
                $prescription['prescriptionId'] = $params['prescriptionId'];
                MiddleHandler::savePrescription($prescription, $login_user);
            }

        }

        return redirect()->action('Wap\CartController@getIndex');
    }

    /**
     * 校验接口签名
     *
     * @param array $params
     * @return array|string
     * @author jiangxianli
     * @created_at 2017-03-13 15:46:41
     */
    private function checkApiSign($params = [])
    {
        //接口签名校验接口地址
        $url = config('api.userService.api-sign-check');

        $api_client = new ApiClient();

        //校验签名是否正确
        $response = $api_client->getApiContent($url, $params, 'POST', 10);
        $response = (array)json_decode($response, true);

        return $response;

    }

    /**
     * 生成商品信息
     *
     * @param array $standard_sku_id_arr
     * @return array|string
     * @author jiangxianli
     * @created_at 2017-03-13 15:08:50
     */
    private function getProductInfoByStandardSku($standard_sku_id_arr = [])
    {
        $url = config('api.pmsApi.choose-product-sku-by-standard');

        $params = [
            'standardSkuIdArr' => implode(',', $standard_sku_id_arr)
        ];

        $request_params = \Tool::makeApiParams($params);

        $api_client = new ApiClient();
        $response   = $api_client->getApiContent($url, $request_params, 'GET', 10);

        $response = (array)json_decode($response, true);

        return $response;
    }
}
