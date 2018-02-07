<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;

class PublicRequest extends Request
{
    /**
     * Determine if the user is authorized to make this request.
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     * @return array
     */
    public function rules()
    {
        //验证规则
        $rules = [
            'login' => [
                'phone' => 'required',
                'password' => 'required|alpha_dash|between:6,16',
            ],
            'register' => [
                'phone' => 'required|max:25|min:6|unique:city_partner,username',
                'code' => 'required',
                'leadername' => 'required',
                'invite' => 'required',
                'password' => 'required|alpha_dash|between:6,16',
                'confirmpassword' => 'required|alpha_dash|same:password|between:6,16',
            ],
            'register2' => [
                'name' => 'required',
                'sex' => 'required|numeric',
                'province' => 'required',
                'city' => 'required',
            ],
            'reset' => [
                'phone' => 'required',
                'password' => 'required|alpha_dash|between:6,16',
                'confirmpassword' => 'required|alpha_dash|same:password|between:6,16',
            ],
            'forget_partner_pwd' => [
                'phone' => 'required',
                'code' => 'required|numeric',
            ],
        ];
        $param = Request::all();
        return self::validateByRules($param, $rules);
    }

    /**
     * 分配验证规则
     * @param $param
     * @param $rules
     * @return mixed
     */
    private function validateByRules($param, $rules)
    {
        if ($param['act'] == 'login') {
            return $rules['login'];
        } elseif ($param['act'] == 'register') {
            return $rules['register'];
        } elseif ($param['act'] == 'reset') {
            return $rules['reset'];
        } elseif ($param['act'] == 'register2') {
            return $rules['register2'];
        } elseif ($param['act'] == 'forget_partner_pwd') {
            return $rules['forget_partner_pwd'];
        }

    }

    /**
     * 属性
     * @return array
     */
    public function attributes()
    {
        return array(
            'phone' => '账号',
            'code' => '验证码',
            'leadername' => '领导名字',
            'invite' => '邀请码',
            'password' => '密码',
            'confirmpassword' => '确认密码',
            'name' => '真实姓名',
            'province' => '所在地区',
        );
    }

    /**
     * 自定义错误
     * @return array
     */
    public function messages()
    {
        return array(
            'required' => ':attribute为必填选项!',
            'numeric' => ':attribute必须为数字!',
            'unique' => ':attribute不能重复!',
            'url' => ':attribute必须为合乎规范的url地址!',
            'date' => ':attribute必须为日期!',
            'status . boolean' => ':attribute必须为启用、关闭中的一种!',
            'alpha_dash' => ':attribute中包含非法字符!',
            'between' => ':attribute长度必须介于6和16之间!',
            'same' => ':attribute和:other必须相同!',
            'exists' => ':attribute用户名已经存在!'
        );
    }
}
