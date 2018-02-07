<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;

class AccountRequest extends Request
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
        $chPwdRules = [
            'oldPassword' => 'required',
            'password' => 'required|alpha_dash|between:6,16',
            'confirmation_password'=>'required|same:password',
            'captcha' => 'required|captcha'
        ];
        $editAccountRules =[
            'realname' => 'required',
            'email' => 'required|email',
            'bank_account'=>'required|numeric|digits_between:16,19',
            'bank'=>'required',
            'deposit_bank'=>'required',
            'cardholder_name'=>'required',
            'idcard' =>'required',
        ];
//        |regex:/^(\d{15}$|^\d{18}$|^\d{17}(\d|X|x))$/
        $data = Request::all();
        if($data['formType'] == 'editAccount')
        {

            return $editAccountRules;
        }elseif($data['formType'] == 'chPwd')
        {
            return $chPwdRules;
        }
    }
    public function attributes()
    {
        $attributes =[
            'oldPassword'=> '旧密码',
            'password'=> '新密码',
            'confirmation_password'=> '确认密码',
            'captcha'=> '验证码',
            'realname' => '姓名',
            'email' => '邮箱',
            'account'=>'银行卡号',
            'bank'=>'银行',
            'deposit_bank'=>'开户行',
            'holder_name'=>'持卡人姓名',
            'identity_number' =>'持卡人身份证'
        ];

        return $attributes;
    }

    public function messages()
    {
        return [
            'required' => ':attribute为必填选项',
            'same'  => ':attribute必须:other相同',
            'alpha_dash'      => ':attribute中包含非法字符',
            'between' => ':attribute格式错误',
            'captcha' =>':attribute必须相同',
            'email' =>':attribute格式错误',
            'numeric'=>':attribute必须为数字',
            'digits_between'=>':attribute格式错误',
        ];
    }
}
