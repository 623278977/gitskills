<?php

namespace App\Http\Requests\Brand;

use App\Http\Requests\Request;

class FetchFundRequest extends Request
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
            'uid'       => 'required|integer|exists:user,uid',
            'fund'       => 'required|integer',
        ];

        return $rules;
    }

    public function attributes()
    {
        $attributes = [
            'brand_id'       => '品牌id',
            'uid'       => '用户id',
            'fund'       => '基金数',
        ];

        return $attributes;
    }

    public function messages()
    {
        return [
            'required' => ':attribute为必传参数',
            'integer'  => ':attribute必须为整数',
            'uid.exists'  => ':attribute该id在user表中不存在',
            'brand_id.exists'  => ':attribute该id在brand表中不存在',
        ];
    }
}
