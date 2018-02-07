<?php

namespace App\Http\Requests\Brand;

use App\Http\Requests\Request;

class DetailRequest extends Request
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
            'id'       => 'required|integer|min:1|exists:brand,id',
            'uid'      => 'required|integer',
            'type'      => 'sometimes|in:app,html5',
        ];

        return $rules;
    }

    public function attributes()
    {
        $attributes = [
            'id'       => '品牌id',
            'uid'      => '用户id',
            'type'      => '访问类型',
        ];

        return $attributes;
    }

    public function messages()
    {
        return [
            'required' => ':attribute为必传参数',
            'integer'  => ':attribute必须为整数',
            'exists'  => ':attribute不存在',
        ];
    }
}
