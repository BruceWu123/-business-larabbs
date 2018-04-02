<?php
namespace App\Http\Controllers\Wap;

use App\Http\Controllers\Controller;
use MiddleHandler;
use Request;
use Message;

class CollectionController extends Controller
{
    /**
     * 个人中心
     *
     * @author yufuchu
     * @param Request $request
     * @return view
     */

    //  收藏
    public function getIndex()
    {
        $mid           = array_get(\Tool::loginUser(), 'mid');
        $response_data = MiddleHandler::myCollection($mid);
        //ddd($response_data);
        if (\Request::ajax()) {
            return $response_data;
        }
        return view('wap.personal_center.collection', compact('response_data'));
    }

    /**
     * 添加商品收藏
     * @author Evey-b <eveyb277@gmail.com>
     * @date   2017-01-18
     * @return string
     */
    public function store()
    {
        $mid           = array_get(\Tool::loginUser(), 'mid');
        $pid           = Request::input('pid');
        $shopCode      = Request::input('shopCode');
        $response_data = MiddleHandler::addMyConllection($mid, $pid, $shopCode);
        if (isset($response_data['err_code'])) {
            return Message::jsonMessage($response_data);
        }
        return Message::jsonMessage([]);
    }

    /**
     * 删除个人收藏
     * @author Evey-b <eveyb277@gmail.com>
     * @date   2016-12-28
     * @param  int     $id 收藏id
     * @return string
     */
    public function postDelete($id)
    {
        $mid = array_get(\Tool::loginUser(), 'mid');
        $res = MiddleHandler::delMyConllection($id, $mid);
        //失败返回{"err_code":15001, "err_msg":"用户收藏删除失败!", "data":[]}
        if (isset($res['err_code'])) {
            return $res;
        }
        //成功返回1
        return $res;
    }
}
