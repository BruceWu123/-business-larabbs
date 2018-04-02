<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesResources;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, AuthorizesResources, DispatchesJobs, ValidatesRequests;

    public function loginResponse()
    {
        $return = request()->input('return') ?: action('Wap\PersonalCenterController@getIndex');
        if (request()->ajax()) {
            return \Message::jsonMessage([], ['return' => $return]);
        }
        return redirect($return);
    }
    /**
     * 返回JSON格式的Swagger定义
     *
     * 这里需要一个主`Swagger`定义：
     * @SWG\Swagger(
     *   @SWG\Info(
     *     title="双十二组团红包接口文档",
     *     version="1.0.0",
     *   )
     * )
     */
    public function getJSON()
    {
        $swagger = \Swagger\scan(app_path('Http/Controllers/'));
        return response()->json($swagger, 200);
    }

}
