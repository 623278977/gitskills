<?php

namespace App\Http\Requests\Api\Login;

use App\Http\Requests\Request;

class RegisterBy400Request extends Request
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
            'username' => 'required',
            'nation_code' => 'required',
            'type' => 'required',
            'code' => 'required',
        ];
    }
    /**
     * 指定名称
     */
    public function attributes()
    {
        return [
            'username' => '手机号',
            'nation_code' => '国家码',
            'type' => '短信验证类型',
            'code' => '短信验证码',
        ];
    }
}
