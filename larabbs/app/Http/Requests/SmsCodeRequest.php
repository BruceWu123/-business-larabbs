<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;
use Illuminate\Contracts\Validation\Validator;
use Message;

class SmsCodeRequest extends Request
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'mobile_phone' => 'required|mobile',
            'sms_code'     => 'required|size:6'
        ];
    }
    public function messages(){
        return [
            'mobile_phone.required' => '请输入手机号',
            'mobile_phone.mobile' => '手机号格式错误',
            'sms_code.required' => '请输入短信验证码',
            'sms_code.size' => '请输入6位短信验证码'
        ];
    }
    protected function formatErrors(Validator $validator){
        return ['code'=>9,'msg'=>'参数错误','data'=>$validator->errors()];
    }
}
