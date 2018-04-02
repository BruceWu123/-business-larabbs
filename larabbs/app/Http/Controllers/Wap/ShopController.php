<?php
namespace App\Http\Controllers\Wap;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use MiddleHandler;
use Message;
use Log;

/**
 * 店铺控制器
 * @author huangcan
 */
class ShopController extends Controller
{
    /**
     * 店铺首页
     * @author huangcan
     * @param Request $request
     * @param int  $shop_id
     * @return mixed
     */
    public function index(Request $request, $shop_id)
    {
        // 组成首页的数据为 ['cdt_list','premium_list','merchant_info','mobile_shelf']
        $data_index = ['cdt_list','premium_list','merchant_info', 'mobile_shelf'];

        $response_data = MiddleHandler::shopInfo((int) $shop_id, $data_index);

        if (\Request::ajax()) {
            return json_encode($response_data);
        }

        return view('wap.shop.index', compact('response_data', 'shop_id'));
        //return response(compact('response_data', 'shop_id'));
    }

    /**
     * 药店资质
     * @param  [type] $shop_id
     * @return [type]          [description]
     */
    public function info($shop_id)
    {
        $response_data = MiddleHandler::shopInfo(
            (int) $shop_id,
            ['merchant_info', 'merchant_quality', 'merchant_map']
        );
        return view('wap.shop.info', compact('response_data', 'shop_id'));
    }

    /**
     * 店铺内分类搜索页
     * @author huangcan
     * @param Request $request
     * @param [type]  $shop_id 店铺ID
     */
    public function search(Request $request, $shop_id = null, $cid = 0)
    {
        // 合并店铺信息和检索商品数据
        $response_data = array_merge(
            MiddleHandler::shopSearch($shop_id, $cid, $request),
            MiddleHandler::shopInfo($shop_id, ['merchant_info']),
            MiddleHandler::shopInfo($shop_id, ['cur_catalog_name'], $cid)
        );
        // $response_data = MiddleHandler::shopInfo($shop_id, ['merchant_info']);
        $keyword = trim($request->input('keyword'));

        if (\Request::ajax()) {
            return json_encode($response_data);
        }

        return view('wap.shop.search', compact('response_data', 'shop_id', 'keyword'));
    }

    /**
     * 店铺内关键字搜索页
     * @author zhangjie
     * @param Request $request
     * @param [type]  $shop_id 店铺ID
     */
    public function keywordSearch(Request $request, $shop_id = null, $cid = 0)
    {
        // 合并店铺信息和检索商品数据
        $response_data = array_merge(
            MiddleHandler::shopSearch($shop_id, $cid, $request),
            MiddleHandler::shopInfo($shop_id, ['merchant_info'])
        );
        // $response_data = MiddleHandler::shopInfo($shop_id, ['merchant_info']);
        $keyword = trim($request->input('keyword'));

        if (\Request::ajax()) {
            return json_encode($response_data);
        }

        return view('wap.shop.search', compact('response_data', 'shop_id', 'keyword'));
    }

    /**
     * 店铺分类页
     * @author huangcan
     * @param Request $request
     * @param [type]  $shop_id 店铺ID
     * @return view
     */
    public function categories($shop_id)
    {
        $response_data = array_merge(
            MiddleHandler::shopInfo($shop_id, ['catalog']),
            MiddleHandler::shopInfo($shop_id, ['merchant_info'])
        );
        return view('wap.shop.categories', compact('response_data', 'shop_id'));
    }

    /**
     * 店铺推荐商品
     * @param Request $request
     * @param int $shop_id
     * @return view
     */
    public function mobileShelf(Request $request, $shop_id)
    {
        $response_data = MiddleHandler::mobileShelf((int) $shop_id, $request);

        if (\Request::ajax()) {
            return json_encode($response_data);
        }
    }

    /**
     * 明星商家列表
     * @author 542207975@qq.com
     * @param Request $request
     * @return mixed
     */
    public function shopStar(Request $request)
    {
        $num = 8;
        $page = $request->get('page', 1);
        $shops = MiddleHandler::starMerchantList($num, $page);
        return view('wap.shop_star.index', compact('shops'));
    }
}
