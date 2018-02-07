<?php

namespace App\Http\Requests\Brand;

use App\Http\Requests\Request;

class TodayGoodsRequest extends Request
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
            'brand_id'       => 'required|integer|exists:brand,id',
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
            'integer'  => ':attribute必须为整数',
            'exists'  => ':attribute该id在brand表中不存在',
        ];
    }
}
