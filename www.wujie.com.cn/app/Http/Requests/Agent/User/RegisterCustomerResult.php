<?php

namespace App\Http\Requests\Agent\User;

use App\Http\Requests\Request;

class RegisterCustomerResult extends Request
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
            'uid'    => 'required|exists:user,uid',
        ];
    }

    /**
     * 指定名称
     */
    public function attributes()
    {
        return [
            'uid' => '投资人ID',
        ];
    }

    /**
     * 验证信息提示
     */
    public function messages()
    {
        return [
            'required'          => ':attribute 为必填选项',
            'exists'  => ':attribute 无效',
        ];
    }
}
