<?php

namespace App\Http\Controllers\Wap;

use App\Http\Controllers\Controller;
use Input;
use Redirect;

/**
 * 按病找药&分类找药控制器
 * @author xiaoyang
 */
class CategoryController extends Controller
{

    public function getDrugCategory($illness)
    {
        if (empty($illness)) {
            return Redirect::action('Wap\IndexController@getIndex');
        }

        $response_data = \MiddleHandler::drugPage($illness);

        return view('wap.sorts.index', compact('response_data'));
    }

    public function getCatalog()
    {
        return view('wap.sorts.index_allsorts');
    }
}
