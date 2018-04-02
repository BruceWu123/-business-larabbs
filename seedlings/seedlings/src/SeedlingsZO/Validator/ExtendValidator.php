<?php

namespace SeedlingsZO\Validator;

/**
 * 校验扩展
 *
 * @package    Extends
 * @category   validator
 * @author     wangqin @create_at 2014-11-13 12:04
 */
class ExtendValidator extends \Illuminate\Validation\Validator
{

    /**
     * Validate the size of an attribute is less than a maximum value.
     *
     * @param  string $attribute
     * @param  mixed $value
     * @param  array $parameters
     * @return bool
     */
    public function validateStrMax($attribute, $value, $parameters)
    {
        return strlen($value) <= $parameters[0];
    }

    /**
     * Replace all place-holders for the max rule.
     *
     * @param  string $message
     * @param  string $attribute
     * @param  string $rule
     * @param  array $parameters
     * @return string
     */
    protected function replaceStrMax($message, $attribute, $rule, $parameters)
    {
        return str_replace(':strmax', $parameters[0], $message);
    }

    /**
     * 验证手机号码的格式是否正确.
     *
     * @param  string $attribute
     * @param  mixed $value
     * @param  array $parameters
     * @return bool
     */
    public function validateMobile($attribute, $value, $parameters)
    {
        return preg_match('/^1[0-9]{10}$/u', $value);
    }

    /**
     * 验证用户密码格式是否正确.
     *  规则未定，请在后面添加规则
     * @param  string  $attribute
     * @param  mixed   $value
     * @param  array   $parameters
     * @return bool
     */
    public function validateInputPwd($attribute, $value, $parameters)
    {
        return true;
    }

    /**
     * Replace all place-holders for the mobile rule.
     *
     * @param  string $message
     * @param  string $attribute
     * @param  string $rule
     * @param  array $parameters
     * @return string
     */
    protected function replaceMobile($message, $attribute, $rule, $parameters)
    {
        return str_replace(':mobile', $attribute, $message);
    }

    /**
     * 验证字符串长度
     * @param   $attribute  string  验证的键值
     * @param   $value  string  需要验证的值
     * @param   $parameters array 例子：min:3中的
     *
     */
    protected function validateLength($attribute, $value, $parameters)
    {
        return mb_strlen($value, 'utf-8') == $parameters[0];
    }

    /**
     * Replace all place-holders for the mobile rule.
     *
     * @param  string $message
     */
    protected function replaceLength($message, $attribute, $rule, $parameters)
    {
        return str_replace(':mobile', $attribute, $message);
    }

    /**
     * 验证字符串是否数字或者英文
     * notice，当$parameters[0] == 'i'，那么匹配大小写
     */
    protected function validateStr($attribute, $value, $parameters)
    {
        return preg_match('/^[\w]+$/Ui', $value);
    }

    /**
     * 验证字符串是否数字或者英文
     * notice，当$parameters[0] == 'i'，那么匹配大小写
     */
    protected function replaceStr($message, $attribute, $rule, $parameters)
    {
        return str_replace(':str', $attribute, $message);
    }
}
