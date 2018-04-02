<?php
namespace App\Http\Controllers\Wap;

use App\Http\Controllers\Controller;
use MiddleHandler;
use Illuminate\Http\Request;


class NewsController extends Controller
{
    /**
     * NewsController constructor.
     */
    public function __construct()
    {
        $categories = MiddleHandler::getNewsCategories();
        view()->share('news_categories', $categories);
    }

    /**
     * 资讯首页
     * @date 2017.6.9
     * @author lili
     * @return array
     */
    public function index()
    {
        $list_data = MiddleHandler::getNewsHomeListData();
        $seo       = MiddleHandler::getNewsSeoData('home_page');
        return view('wap.news.index', compact('seo', 'list_data'));
    }

    /**
     * 二级列表页
     * @data 2017-6-9
     * @author lili
     * @param Request $request
     * @param $cid
     * @return array
     */
    public function categoryList(Request $request, $cid)
    {
        $response_data = MiddleHandler::getNewsCategoryListData($cid);
        // dd(compact('response_data'));
        return view('wap.news.list', compact('response_data'));
    }

    /**
     * 资讯搜索
     *
     * @param Request $request 搜索关键字
     *
     * @return mixed
     * @author Tang3
     * @date   2017-06-09
     */
    public function search(Request $request)
    {
        $keyword = $request->input('keyword');

        if ($request->ajax()) {
            $response_data = MiddleHandler::getNewsSearchList($keyword, 1);
            return response()->json($response_data);
        }

        $response_data = MiddleHandler::getNewsSearchList($keyword);

        return view('wap.news.search', compact('response_data', 'keyword'));
    }

    /**
     * 资讯列表控制器
     *
     * @param Request $request
     * @param int $cate 分类ID
     *
     * @return mixed
     * @author Tang3
     * @date   2017-06-12
     */
    public function getNews(Request $request, $cate = 0)
    {
        $route = $request->route()->getName();


        if ($request->ajax()) {
            $response_data = MiddleHandler::getCategoryNews($route, $cate, 1);
            return response()->json($response_data);
        }

        $response_data = MiddleHandler::getCategoryNews($route, $cate);

        return view('wap.news.yp_more_list', compact('response_data'));

    }

    /**
     * 资讯详情
     *
     * @param Request $request
     * @param $news_id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @author jiangxianli
     * @created_at 2017-06-14 14:21:43
     */
    public function getNewsDetail(Request $request, $news_id)
    {
        $response_data = MiddleHandler::informationDetail($news_id);
        $response_data = \Tool::object2Array($response_data);
        return view('wap.news.info', compact('response_data'));
    }

    /**
     * 标签详情
     * @param Request $request
     * @param $tag_id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @author jiangxianli
     * @created_at
     */
    public function getTagDetail(Request $request, $tag_id)
    {
        if ($request->ajax()) {
            $response_data = MiddleHandler::tagDetail($tag_id, 1);
            return response()->json($response_data);
        }
        $response_data = MiddleHandler::tagDetail($tag_id);
        return view('wap.news.tag', compact('response_data'));
    }

    /**
     * 标签相关联更多资讯
     *
     * @param Request $request
     * @param $tag_id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @author jiangxianli
     * @created_at 2017-06-16 09:23:30
     */
    public function getMoreTagNews(Request $request, $tag_id)
    {
        $response_data = MiddleHandler::tagMoreNews($tag_id);
        return view('wap.news.more_list', compact('response_data'));
    }

    /**
     * 资讯点赞
     *
     * @param Request $request
     * @param $information_id
     * @return \Illuminate\Http\JsonResponse
     * @author jiangxianli
     * @created_at 2017-06-15 10:46:16
     */
    public function postPraiseInformation(Request $request, $information_id)
    {
        $response_data = MiddleHandler::incrementInformationPraiseCount($information_id);

        return response()->json(['code' => $response_data ? 0 : 1]);
    }

    /**
     * 资讯分类
     *
     * @param Request $request
     * @param $information_id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @author zhangjie
     * @created_at 2017-07-05 15:04:54
     */
    public function getNewsClassify(Request $request, $classify_id)
    {
        $news_categories = MiddleHandler::getNewsCategories();
        $cur_classify_id = $classify_id;
        $cur_classify_name = '';

        foreach ($news_categories as $value) {
            if($value['id'] == $classify_id) {
                $cur_classify_name = $value['category_name'];
            }
        }

        return view('wap.news.classify', compact('news_categories', 'cur_classify_id', 'cur_classify_name'));
    }
}
