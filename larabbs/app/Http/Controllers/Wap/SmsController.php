<?php

namespace App\Http\Controllers\Wap;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Cookie;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use SeedlingsZO\MessageHandler\Facades\Message;
use Carbon\Carbon;

class SmsController extends Controller
{
    public function postSendSms(Request $request)
    {
        //存储短信验证码场景发送次数,用于判定是否超出短信发送界限值
        if ($scenarios_alias = $request->input('scenarios_alias')) {
            $ckey  = 's2c-' . $request->get('mobile_phone') . '.scenarios_alias.' . $scenarios_alias . '.count';
            $count = Cache::get($ckey, 0) + 1;
            Cache::put($ckey, $count, Carbon::now()->addMinutes(5));
            //用户未有提交图片验证码，并且5分钟内发送3次短信则返回提示开启图片验证码
            if (!($captcha_code = $request->input('captcha_code')) && $count >= 3) {
                return Message::jsonMessage(Message::getErrorMessage(15020));
            }
        }
        $request_data                  = $request->all();
        $request_data['platform_code'] = 'm.800pharm.com';
        $send_url                      = \Config::get('sms.send_code_url');
        $send_result                   = \Tool::sendSmsService($request_data, $send_url);
        if (isset($send_result['err_code'])) {
            return Message::jsonMessage($send_result);
        }

        $sms_code = $send_result['verify_code'];
        //保存短信验证码到cookie用于前台验证判断
        if ($scenarios_alias = $request->input('scenarios_alias')) {
            //保存验证码到cookie，用于前台验证效果
            Cookie::queue('sms_code.' . $scenarios_alias, md5($sms_code), 5, null, null, false, false);
        }

        //返回至浏览器前端时一定需要去掉验证码字段，避免被用于破解
        unset($send_result['verify_code']);
        return Message::jsonMessage([], $send_result);
    }
}
