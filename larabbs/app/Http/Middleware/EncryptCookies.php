<?php

namespace App\Http\Middleware;

use Illuminate\Cookie\Middleware\EncryptCookies as BaseEncrypter;

class EncryptCookies extends BaseEncrypter
{
    /**
     * The names of the cookies that should not be encrypted.
     *
     * @var array
     */
    protected $except = [
        'sem',
        'sem_src',
        'sem_createdates',
        'sem_params',
        //全局加密使用，赞用于图片验证码加密
        'erycode',
        'fromurl_800pharm_visitor',
        'userlogin_session',
        //第三方绑定手机场景别名
        'phrase.third_party_bind_mobil_phone',
        'sms_code.third_party_bind_mobil_phone',
        //登陆忘记密码场景别名
        'phrase.login_forget_password',
        'sms_code.login_forget_password',
        //个人中心绑定手机场景别名
        'phrase.center_bind_mobile',
        'sms_code.center_bind_mobile',
        //第三方绑定手机发送密码给用户
        'phrase.send_password_to_user',
        'sms_code.send_password_to_user',
        //手机快捷登录发送验证码
        'phrase.send_fastlogin_to_user',
        'sms_code.send_fastlogin_to_user',
        //更新绑定手机
        'phrase.update_bind_mobile',
        'sms_code.update_bind_mobile',
        //Wap端手机账号注册
        'phrase.register_account_alias',
        'sms_code.register_account_alias',
        'yiqifa_src',
        'yiqifa_cid',
        'yiqifa_wi',
    ];
}
