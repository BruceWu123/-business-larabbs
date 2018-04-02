<?php
namespace App\Http\Controllers\Wap;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use MiddleHandler;
use Illuminate\Support\Facades\Cookie;

/**
 * Class SearchController
 * 搜索控制器
 *
 * @package App\Http\Controllers\Wap
 */
class SearchController extends Controller
{
    /**
     * 搜索列表
     *
     * @param Request $request    参数输入
     * @param string  $search_key 搜索关键字
     *
     * @author zhangjie
     * @date   2017-02-14
     * @return view
     */
    public function getSearchList(Request $request, $search_key = '')
    {

        $response_data = MiddleHandler::getSearchList(array_merge($request->all(), ['search_key' => $search_key]));

        if (isset($response_data['err_code'])) {
            return view('wap.search.empty_page', compact('response_data'));
        }

        return view('wap.search.index', compact('response_data'));
    }

    /**
     * 按病搜索列表
     *
     * @param int  $category_id 按病找药二级分类id
     *
     * @author zhangjie
     * @date   2017-07-06 09:25:38
     * @return view
     */
    public function getCategorySearchList(Request $request, $category_id)
    {
        $response_data = MiddleHandler::getSearchList(array_merge($request->all(), ['categoryId' => $category_id]));

        if (isset($response_data['err_code'])) {
            return view('wap.search.empty_page', compact('response_data'));
        }

        return view('wap.search.index', compact('response_data'));
    }

    /**
     * 关键字搜索列表
     *
     * @param Request $request    参数输入
     * @param string  $search_key 搜索关键字
     *
     * @author zhangjie
     * @date   2017-02-14
     * @return view
     */

    public function getKeywordSearch(Request $request, $search_key = '')
    {
        //判断是否已经有历史记录数组
        $history_record = $request->cookie('history_record');

        if(!$history_record)
        {
            // 创建cookie的数组
            $history_record=[];
        }

        //计算历史记录需要缓存多少个
        $length = count($history_record);

        //存储当前需要存储的字符串,将字符串入栈到数组中
        array_unshift($history_record,$search_key);

        //如果大于7个，出栈
        if($length>6){
            array_pop($history_record);

        }
        // 将$history_record存入cozokie中

        Cookie::queue('history_record',$history_record,10);

        //$history_records = $request->cookie('history_record');

        $response_data = MiddleHandler::getSearchList(array_merge($request->all(), ['search_key' => $search_key]));

        if (isset($response_data['err_code'])) {
            return view('wap.search.empty_page', compact('response_data'));
        }

       // log::info($history_records);

        return view('wap.search.index', compact('response_data'));
    }

    /**
     * 搜索商品归集信息显示
     *
     * @param Request $request 筛选、排序参数
     * @param int     $groupId 群组ID
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @author Tang3
     * @date   2017-02-24
     */
    public function getGroupList(Request $request, $groupId)
    {
        $response_data = MiddleHandler::getGroupList($groupId, $request->all());
        if (isset($response_data['err_code'])) {
            return view('wap.goods.no_goods_info');
        }
        return view('wap.search.group', compact('response_data'));
    }
}
