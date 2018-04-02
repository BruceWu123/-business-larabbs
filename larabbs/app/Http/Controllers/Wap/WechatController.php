<?php

namespace App\Http\Controllers\Wap;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use EasyWeChat\Foundation\Application;
use Message;

class WechatController extends Controller
{
    public function getJsSdkInfo(Application $wechat, Request $request)
    {
        $url = $request->share_url;
        $js = $wechat->js;
        $js->setUrl($url);
        $json_data = $js->config([], false, true);
        return $json_data;
    }
}
