<?php
namespace App\Http\Controllers\Wap;

use Illuminate\Support\Facades\Response;
use App\Http\Controllers\Controller;
use Input;
use MiddleHandler;

class ErrorController extends Controller
{
    /**
     * 下架商品索引
     *
     * @return view
     */
    public function getIndex()
    {
        $respone_data = ['err_msg' => '抱歉，你访问的内容不存在'];

        return view('wap.errors.non_page', compact('respone_data'));
    }
}
