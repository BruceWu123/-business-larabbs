<?php

namespace App\Http\Controllers\Wap;

use App\Http\Controllers\Controller;
use MiddleHandler;
use Request;

/**
 * 问答详情页控制器
 * @author xiaoyang
 */
class ConsultativeController extends Controller
{
    /**
     * 问答详情页
     * @author xiaoyang
     * @date   2016-10-17
     * @return array
     */
    public function getConsultative($question_id)
    {
        $respone_data = MiddleHandler::consultativeInfoPage($question_id);
        //增加品牌和通用名标签
        $respone_data = MiddleHandler::addBrandAndCommonNameTags($respone_data);
        $advertisements = MiddleHandler::getPageArea();
        if (empty($respone_data['problem_answer'])) {
            return view('wap.ask.no_ask_info');
        }
        return view('wap.ask.index', compact('respone_data', 'advertisements'));
    }

    /**
     * 问答首页
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @author Tang3
     * @date   2017-03-16
     */
    public function askIndex()
    {
        if (Request::ajax()) {
            $response_data = MiddleHandler::getAskIndexInfo(1);
            if (empty($response_data['asks']['data'])) {
                $response_data = ['status' => 422, 'message' => '当前页码无数据'];
            } else {
                $response_data = array_merge(['status' => 0], $response_data);
            }
            return response()->json($response_data);
        } else {
            $response_data = MiddleHandler::getAskIndexInfo(0);
        }
        return view('wap.health_ask.index', compact('response_data'));
    }

    /**
     * 问答分类页
     *
     * @param int $id 二级分类ID
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @author Tang3
     * @date   2017-03-17
     */
    public function categoryAsk($id)
    {
        if (Request::ajax()) {
            $response_data = MiddleHandler::getCategoryAskInfo($id, 1);

            if (empty($response_data['asks']['data'])) {
                $response_data = ['status' => 422, 'message' => '当前页码无数据'];
            } else {
                $response_data = array_merge(['status' => 0], $response_data);
            }
            return response()->json($response_data);
        } else {
            $response_data = MiddleHandler::getCategoryAskInfo($id, 0);
        }

        return view('wap.health_ask.classification', compact('response_data'));
    }

    /**
     * 问答关键字tag页
     *
     * @param string $tag 关键字
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\JsonResponse|\Illuminate\View\View
     * @author Tang3
     * @date   2017-03-17
     */
    public function tagAsk($tag)
    {
        $response_data = MiddleHandler::getTagAskInfo($tag);
        if (Request::ajax()) {
            if (empty($response_data['data'])) {
                $response_data = ['status' => 422, 'message' => '当前页码无数据'];
            } else {
                $response_data = array_merge(['status' => 0], $response_data);
            }
            return response()->json($response_data);
        }
        return view('wap.health_ask.tags', compact('response_data'));
    }
}
