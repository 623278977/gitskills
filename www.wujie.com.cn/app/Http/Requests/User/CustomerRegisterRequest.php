<?php

namespace App\Http\Requests\User;

use App\Http\Requests\Request;

class CustomerRegisterRequest extends Request
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
            'agent_id' => 'required|exists:agent,id,status,1',
            'username' => 'required',
            'type' => 'required',
        ];
    }

    /**
     * 指定名称
     */
    public function attributes()
    {
        return [
            'type'      => '验证类型',
            'agent_id'      => '经纪人ID',
            'username'      => '手机号',
        ];
    }

    /**
     * 验证信息提示
     */
    public function messages()
    {
        return [
            'required'              => ':attribute 为必填选项',
            'exists'                => ':attribute 无效',
        ];
    }
}
