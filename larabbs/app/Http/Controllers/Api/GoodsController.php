<?php
namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ConnectException;
use MiddleHandler;

class GoodsController extends Controller
{
    /**
     * 获取快递信息
     * @date 2017-5-23
     * @author lili
     * @param Request $request
     * @return array|mixed
     */
    public function getExpress(Request $request)
    {
        $time      = time() * 1000;
        $shop_code = $request->get('shop_code', 0);
        $city_name = $request->get('city_name', '');
        $city_id   = $request->get('city_id', 0);
        $url       = 'http://www.800pharm.com/shop/shipping_info.html?t=';
        $url .= $time . '&cityId=' . $city_id . '&shopcode=' . $shop_code . '&cityName=' . $city_name;

        $httpClient = new Client([
            'timeout' => 2 //2秒
        ]);
        try {
            $res = $httpClient->request('GET', $url)->getBody()->getContents();

            if (stripos($res, 'shippinglist') !== false) {
                return json_decode($res, true);
            }

        } catch (ConnectException  $e) { //超时
            \Log::error('请求' . __CLASS__ . '/' . __METHOD__ . '接口超时');
            \Log::error($e->getMessage());
        } catch (\Exception $e) {
            \Log::error('请求' . __CLASS__ . '/' . __METHOD__ . '失败');
            \Log::error($e->getMessage());
        }
        return ['shippinglist' => []];
    }

}
