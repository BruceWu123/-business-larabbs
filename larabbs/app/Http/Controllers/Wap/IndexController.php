<?php
namespace App\Http\Controllers\Wap;

use App\Http\Controllers\Controller;

/**
 *wap首页控制器
 *
 *@author:chenfeng
 */
class IndexController extends Controller
{
    /**
     *wap首页页面
     *
     * @author chenfeng
     * @created_at 2016-08-30
     * @return view
     */
    public function getIndex()
    {
        if (env('PAGE_CACHE') == true) {
            $response_data = \Cache::get('LaravelwapIndexPage');
            if (!$response_data) {
                $response_data = \MiddleHandler::indexPage();
                \Cache::put('LaravelwapIndexPage', $response_data, 10);
            }
        } else {
            $response_data = \MiddleHandler::indexPage();
        }
        //限时抢购起止时间
        $now_time = time();
        $end_time = strtotime(date('Y-m-d 10:00:00', strtotime("+1 day")));
        return view('wap.home.index', compact('response_data', 'now_time', 'end_time'));
    }
}
