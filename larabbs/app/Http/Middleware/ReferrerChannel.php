<?php

namespace App\Http\Middleware;

use Carbon\Carbon;
use Closure;
use Illuminate\Support\Facades\Auth;

class ReferrerChannel
{
    /**
     * 推广渠道来源
     *
     * @param $request
     * @param Closure $next
     * @param null $guard
     * @return mixed
     * @author  jiangxianli
     * @created_at 2016-9-5 17:52:21
     */
    public function handle($request, Closure $next, $guard = null)
    {
        //限制时间
        $max_time = time() - 2592000;
        
        //在没有推广渠道参数的情况下，清除原有cookie设置过长的数据
        if (!$request->get('from_url') && \Cookie::get('params')) {
            parse_str(\Cookie::get('params'), $params);
            $createdates = strtotime(array_get($params, 'createdates'));
            if ($max_time > $createdates) {
                \Cookie::queue('fromurl_800pharm_visitor', null);
                \Cookie::queue('src_800pharm_visitor', null);
                \Cookie::queue('params', null);
            }
        }
        //在没有推广渠道参数的情况下，清除原有cookie设置过长的数据
        if (!$request->get('sem') && \Cookie::get('sem_params')) {
            parse_str(\Cookie::get('sem_params'), $sem_params);
            $createdates = strtotime(array_get($sem_params, 'createdates'));
            if ($max_time > $createdates) {
                \Cookie::queue('sem', null);
                \Cookie::queue('sem_src', null);
                \Cookie::queue('sem_createdates', null);
                \Cookie::queue('sem_params', null);
            }
        }

        $cookies = [];
        //cookie保存时长,分钟数
        $max_minutes = 30 * 24 * 60;

        //from_url形式推广链接
        if ($referrer_channel = $request->get('from_url')) {
            /*
            $channels = [
            'wechat', //微信-移动商城
            'wxq', //药店圈
            'alipay', //支付宝-移动商城
            'xywy', //寻医问药pc端
            'xywy2', //寻医问药右侧文字链推荐
            'xywy3', //寻医问药3G端
            'bdjk', //百度健康
            'qqyy', //全球医院网
            'cpc315jg', //315价格网
            'emar', //亿起发
            'bbfInfomation', //资讯pc
            'mbbfInfo', //资讯移动
            'bfd', //百分点推荐
            'tk', //云药箱
            'wxgd' //无线广东APP
            ];

            if (in_array($referrer_channel, $channels)) {
            $cookies[] = cookie('fromurl_800pharm_visitor', $referrer_channel, $max_minutes);
            }
             */

            $cookies[] = \Cookie('fromurl_800pharm_visitor', $referrer_channel, $max_minutes);
        }
        //推广参数
        if ($request->has('sem')) {
            $src[] = "sem_" . $request->get('sem');
        }

        if ($request->has('from_url')) {
            $src[] = $request->get('from_url');
        }

        if ($request->has('src')) {
            $src[] = $request->get('src');
        }

        if (\Cookie::has('yiqifa_src')) {
            $src[] = \Cookie::get('yiqifa_src');
        }

        $now      = Carbon::now()->format('Y-m-d H:i:s');
        $referrer = array_get($_SERVER, 'HTTP_REFERER', '');
        if ($referrer && strlen($referrer) > 30) {
            $referrer = substr($referrer, 0, 30);
        }

        if (isset($src) && !empty($src)) {
            if (\Cookie::has('src_800pharm_visitor')) {
                $merge = array_merge($src, explode(',', \Cookie::get('src_800pharm_visitor')));
                $src   = array_unique($merge);
            }
            $string    = implode(',', $src);
            $cookies[] = cookie('src_800pharm_visitor', $string, $max_minutes);
            $params    = "src=" . $string . "&createdates=" . $now . "&referer=" . $referrer;
            if ($request->has('sem')) {
                $params = "src=sem&sem=" . $string . "&createdates=" . $now . "&referer=" . $referrer;
            }
            $cookies[] = cookie('params', $params, $max_minutes);
        }

        //sem形式推广链接
        if ($referrer_channel = $request->get('sem')) {
            /*
            if (in_array($referrer_channel, ['1', '2', '3', '4'])) {
            $now      = Carbon::now()->format('Y-m-d H:i:s');
            $referrer = array_get($_SERVER, 'HTTP_REFERER', '');
            if ($referrer && strlen($referrer) > 30) {
            $referrer = substr($referrer, 0, 30);
            }
            $params    = "src=sem&sem=" . $referrer_channel . "&createdates=" . $now . "&referer=" . $referrer;
            $cookies[] = cookie('sem', $referrer_channel, $max_minutes);
            $cookies[] = cookie('sem_src', 'sem_' . $referrer_channel, $max_minutes);
            $cookies[] = cookie('sem_createdates', $now, $max_minutes);
            $cookies[] = cookie('sem_params', $params, $max_minutes);
            }
             */

            $params    = "src=sem&sem=" . $referrer_channel . "&createdates=" . $now . "&referer=" . $referrer;
            $cookies[] = cookie('sem', $referrer_channel, $max_minutes);
            $cookies[] = cookie('sem_src', 'sem_' . $referrer_channel, $max_minutes);
            $cookies[] = cookie('sem_createdates', $now, $max_minutes);
            $cookies[] = cookie('sem_params', $params, $max_minutes);
        }

        $response = $next($request);
        //添加cookie
        foreach ($cookies as $cookie) {
            $response->withCookie($cookie);
        }

        return $response;
    }
}
