<?php

namespace App\Http\Requests\Brand;

use App\Http\Requests\Request;

class SingleBrandRequest extends Request
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

    public function rules()
    {

        $rules = [
            'id'       => 'required|exists:brand,id,status,enable',
        ];

        return $rules;
    }

    public function attributes()
    {
        $attributes = [
            'id'       => '品牌id'
        ];

        return $attributes;
    }

    public function messages()
    {
        return [
            'required' => ':attribute为必传参数',
            'exists'  => ':attribute 无效',
        ];
    }
}
