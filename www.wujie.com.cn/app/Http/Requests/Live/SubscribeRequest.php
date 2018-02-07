<?php

namespace App\Http\Requests\Live;

use App\Http\Requests\Request;

class SubscribeRequest extends Request
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
            'uid'   => 'required|integer',
        ];

        return $rules;
    }

    public function attributes()
    {
        $attributes = [
            'uid'   => '用户uid',

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