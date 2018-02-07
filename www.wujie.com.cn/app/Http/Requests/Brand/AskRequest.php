<?php

namespace App\Http\Requests\Brand;

use App\Http\Requests\Request;

class AskRequest extends Request
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
            'id'       => 'required|integer|min:1',
            'uid'      => 'integer|min:1',
            'content'   => 'required',

        ];

        return $rules;
    }

    public function attributes()
    {
        $attributes = [
            'id'       => '品牌id',
            'uid'      => '用户id',
            'content'   => '提问内容',
        ];

        return $attributes;
    }

    public function messages()
    {
        return [
            'required' => ':attribute为必传参数',
            'integer'  => ':attribute必须为整数',
            'uid.min'  => ':attribute必须大于1',
            'id.min'  => ':attribute必须大于1',
        ];
    }
}
