<?php
namespace App\Http\Controllers;

use App\Http\Requests\Request;
use Illuminate\Support\Facades\Input;
use MiddleHandler;
use Tool;
use SeedlingsZO\MemberModule\MemberCenter;
use Illuminate\Session\Middleware\StartSession;
use Gregwar\Captcha\CaptchaBuilder;
use Cookie;
use Session;
use EasyWeChat\Foundation\Application;


class TestController extends Controller
{
    public function getIndex(Application $wechat)
    {
        $js = $wechat->js;
        $res = $js->config(array('onMenuShareQQ', 'onMenuShareWeibo'), false, true);
        dd($res);
        // $data = [
        //     'account' =>'18613031321',
        //     'password' => '123456'
        // ];

        // $value = \MiddleHandler::postLogin($data, false);
        // dd($value);
//        die;
//        $otherSession = \App::make('otherSession');
//        $otherSession->put('test', '111');
//        $otherSession->save();
//
//        $res = response('test');
//
//        $session_hanlder = new StartSession($otherSession);
//        $session_hanlder->addCookieToResponse($res, $otherSession);

//        $otherSession->save();

//        return '1111';
//        \Redis::set('12312', 'sss');
//        \Session::put('key2','123456');
//        $value = \Session::get('key2');
//        return var_dump($value);
//        die;
//return '';


//        $handler = new WapHandler();
//        $response = $handler->productDetail(['product_id'=>1235466,'shop_code'=>100629]);

//        $response = $handler->productDetail(['product_id'=>1095652,'shop_code'=>100084]);

//        $response = $handler->productDetail(['product_id'=>375575,'shop_code'=>100084]);

//        $response = $handler->productDetail(['product_id'=>44822,'shop_code'=>55000]);

//        $response = $handler->productComments(Input::all());

//        \Kint::dump($response);
//        \Debugbar::info($response);
//        echo json_encode($response);
    }

    public function proxyJava()
    {
        /*use Proxy\Proxy;
        use Proxy\Adapter\Guzzle\GuzzleAdapter;
        use GuzzleHttp\Client;
        use Proxy\Filter\RemoveEncodingFilter;
        use Zend\Diactoros\ServerRequestFactory;
        use Zend\Diactoros\Response\SapiEmitter;

        $proxy_site = "http://192.168.0.102";

        $request_url = $_SERVER['REQUEST_URI'];
        $request_cookies = $_COOKIE;

        $url = $proxy_site . $request_url;


        // Create a PSR7 request based on the current browser request.
        $request = ServerRequestFactory::fromGlobals();

        // Create a guzzle client
        $guzzle = new Client();

        // Create the proxy instance
        $proxy = new Proxy(new GuzzleAdapter($guzzle));

        // Add a response filter that removes the encoding headers.
        $proxy->filter(new RemoveEncodingFilter());

        // Forward the request and get the response.
        $response = $proxy->forward($request)->to($proxy_site);
        // Output response to the browser.
        (new SapiEmitter())->emit($response);*/
    }

    public function getCoupons()
    {
        return view('test-coupons');
    }

    public function getWxshare()
    {
        $login_user = \Tool::loginUser();
        return view('test-wxshare', compact('login_user'));
    }
}
