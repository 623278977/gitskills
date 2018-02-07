<?php

namespace App\Http\Requests\Live;

use App\Http\Requests\Request;

class ShareSubscribeRequest extends Request
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

        $rules = [
            'tel'     => 'required',
            'live_id' => 'required|integer',
            'code'    => 'required',
        ];

        return $rules;
    }

    public function attributes()
    {
        $attributes = [
            'tel'     => '手机号码',
            'code'    => '验证码',
            'live_id' => '直播id',
        ];

        return $attributes;
    }

    public function messages()
    {
        return [
            'required' => ':attribute为必传参数',
            'integer'  => ':attribute必须为整数',
        ];
    }
}
