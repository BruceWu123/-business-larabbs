<?php
namespace App\Http\Controllers\Wap;

use App\Http\Controllers\Controller;
use App\Http\Requests\Request;
use Illuminate\Support\Facades\Response;
use Input;
use MiddleHandler;
use Session;
use Cache;

class GoodsController extends Controller
{
    /**
     * 下架商品索引
     *
     * @return view
     */
    public function getOffSaleGoodsList()
    {
        $request_data = Input::all();
        $respone_data = MiddleHandler::offSaleProductList($request_data);

        if (\Request::ajax()) {
            return json_encode($respone_data);
        }

        return view('wap.off_sale_goods.index', compact('respone_data'));
    }

    /**
     * 产品库
     *
     * @return view
     */
    public function getGoodsLibrary()
    {
        $request_data = Input::all();
        $respone_data = MiddleHandler::indexProductLibrary($request_data);
        if (\Request::ajax()) {
            return json_encode($respone_data);
        }

        return view('wap.product_storehouse.index', compact('respone_data'));
    }

    /**
     * 商品详情
     * @param \Illuminate\Http\Request $request
     * @param $shop_id
     * @param $sku_id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function getGoodsInfo(\Illuminate\Http\Request $request, $shop_id, $sku_id)
    {
        $request_data               = Input::all();
        $request_data['product_id'] = $sku_id;
        $request_data['shop_code']  = $shop_id;
        $request_data['ip']         = Session::get('visit_user_ip', '0.0.0.0');
        // 需求3426
        if(in_array($sku_id,[1296144,1094844,1413103,663137])){
            return redirect('/');
        }
        if (Session::has('temp_goods')) {
            $temp_list          = Session::get('temp_goods');
            $temp_list[$sku_id] = $request_data;
            Session::put('temp_goods', $temp_list);
        } else {
            $temp_list[$sku_id] = $request_data;
            Session::put('temp_goods', $temp_list);
        }
        $respone_data = MiddleHandler::productDetail($request_data);
        if (empty($respone_data['product_info'])) {
            return view('wap.goods.no_goods_info');
        }
        return view('wap.goods.index', compact('respone_data'));
    }

    /**
     * 评论列表页
     *
     * @param int $shop_code
     * @param int $goods_id
     * @return \View|string
     * @author  jiangxianli
     * @created_at  2016-09-07
     */
    public function getComment($shop_code, $goods_id)
    {
        $current_page               = Input::get('page', 1);
        $input_data['product_id']   = $goods_id;
        $input_data['shop_code']    = $shop_code;
        $input_data['current_page'] = $current_page;
        $response_data              = MiddleHandler::productComments($input_data);

        if (\Request::ajax()) {
            return json_encode($response_data);
        }
        return view('wap.comment.list', compact('response_data'));
    }

    /**
     * 药目分类
     *
     * @author chenfeng
     * @created_at 2016-09-02
     * @return view
     */
    public function getDrugGroup($base_id)
    {
        //请求参数
        $request_data = array_merge(['base_id' => $base_id], Input::all());
        if (\Request::ajax()) {
            $request_data['is_ajax'] = 1;
            $respone_data            = MiddleHandler::drugCategory($request_data);
            return json_encode($respone_data);
        }

        $respone_data = MiddleHandler::drugCategory($request_data);
        $respone_data['new_base_info'] = MiddleHandler::getTempDrugInstruction($respone_data['base_info']['pzwh']);
        if (isset($respone_data['err_code'])) {
            return \Redirect::action('Wap\ErrorController@getIndex');
        }
        return view('wap.drug_head.index', compact('respone_data'));
    }

    /**
     * 购物车商品数量
     *
     * @return mixed
     * @author  jiangxianli
     * @created_at 2016-9-6 10:21:54
     */
    public function postCartNum()
    {
        $response_data = MiddleHandler::cartProductsNum();

        return Response::json($response_data);
    }

    /**
     * 商品详情页调取价格接口
     * @author JokerLinly
     * @date   2016-11-04
     * @return [type]     [description]
     */
    public function postProductPrice()
    {
        $request_data = Input::all();
        //数据验证
        $response_data = MiddleHandler::productDetailOfPrice($request_data);
        return Response::json($response_data);
    }

    /**
     * 品牌库
     * @author JokerLinly
     * @date   2017-03-02
     * @return [array]           [description]
     */
    public function getBrandLibrary()
    {
        $request_data = Input::all();

        $respone_data = MiddleHandler::indexBrandLibrary($request_data);
        if (\Request::ajax()) {
            return json_encode($respone_data);
        }
        return view('wap.brand.index', compact('respone_data'));
    }

    /**
     * 品牌详情页
     * @author JokerLinly
     * @date   2017-03-02
     * @param  [int]     $brand_id [description]
     * @return [array]               [description]
     */
    public function getBrandInfo($brand_id)
    {
        //请求参数
        $request_data = array_merge(['need_id' => $brand_id], Input::all());
        if (\Request::ajax()) {
            $request_data['is_ajax'] = 1;
            $respone_data            = MiddleHandler::brandCategory($request_data);
            return json_encode($respone_data);
        }

        $respone_data = MiddleHandler::brandCategory($request_data);

        if (isset($respone_data['err_code'])) {
            return \Redirect::action('Wap\ErrorController@getIndex');
        }
        return view('wap.brand.info', compact('respone_data'));
    }

    /**
     * 通用名库
     * @author JokerLinly
     * @date   2017-03-02
     * @return [array]     [description]
     */
    public function getPronameLibrary()
    {
        $request_data = Input::all();

        $respone_data = MiddleHandler::indexPronameLibrary($request_data);
        if (\Request::ajax()) {
            return json_encode($respone_data);
        }
        return view('wap.tyk.index', compact('respone_data'));
    }

    /**
     * 通用名详情页
     * @author JokerLinly
     * @date   2017-03-02
     * @param  [int]     $proname_id [description]
     * @return [array]              [description]
     */
    public function getPronameInfo($proname_id)
    {
        //请求参数
        $request_data = array_merge(['need_id' => $proname_id], Input::all());
        if (\Request::ajax()) {
            $request_data['is_ajax'] = 1;
            $respone_data            = MiddleHandler::pronameCategory($request_data);
            return json_encode($respone_data);
        }

        $respone_data = MiddleHandler::pronameCategory($request_data);
        if (isset($respone_data['err_code'])) {
            return \Redirect::action('Wap\ErrorController@getIndex');
        }
        // dd($respone_data);
        return view('wap.tyk.info', compact('respone_data'));
    }

    /**
     * 搜索词库
     * @author JokerLinly 2017-04-13
     * @return [array] [description]
     */
    public function getSearchibrary()
    {
        $request_data = Input::all();

        $respone_data = MiddleHandler::indexSearchLibrary($request_data);
        if (\Request::ajax()) {
            return json_encode($respone_data);
        }
        return view('wap.ck.index', compact('respone_data'));
    }

    /**
     * 搜索词库详情页
     * @author JokerLinly 2017-04-13
     * @param  [int] $search_id [description]
     * @return [array] or json             [description]
     */
    public function getSearchInfo($search_id)
    {
        //请求参数
        $request_data = array_merge(['need_id' => $search_id], \Request::all());
        if (\Request::ajax()) {
            $request_data['is_ajax'] = 1;
            $respone_data            = MiddleHandler::searchCategory($request_data);
            return json_encode($respone_data);
        }

        $respone_data = MiddleHandler::searchCategory($request_data);
        if (isset($respone_data['err_code'])) {
            return \Redirect::action('Wap\ErrorController@getIndex');
        }

        return view('wap.ck.info', compact('respone_data'));
    }
}
