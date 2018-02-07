<?php

namespace App\Http\Requests\Brand;

use App\Http\Requests\Request;

class BrandRedpacketRequest extends Request
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
            'brand_id'       => 'required|exists:brand,id,status,enable',
            'uid'           => 'required|exists:user,uid'
        ];

        return $rules;
    }

    public function attributes()
    {
        $attributes = [
            'id'       => '品牌id',
            'uid'       => '用户id'
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
