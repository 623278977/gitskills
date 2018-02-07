<?php

namespace App\Http\Requests\Brand;

use App\Http\Requests\Request;

class EnterRequest extends Request
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
            'uid'         => 'required|integer|min:1|exists:user,uid',
            'mobile'      => 'required',
            'realname'    => 'required',
            'brand_name'  => 'required',
            'category_id' => 'required|integer|min:1|exists:categorys,id',
//            'introduce'   => 'required'
        ];

        return $rules;
    }

    public function attributes()
    {
        $attributes = [
            'uid'         => '用户id',
            'mobile'      => '手机号',
            'realname'    => '真实姓名',
            'brand_name'  => '品牌名称',
            'category_id' => '分类id',
            'introduce'   => '项目介绍',
        ];

        return $attributes;
    }

    public function messages()
    {
        return [
            'required' => ':attribute为必传参数',
            'integer'  => ':attribute必须为整数',
            'uid.min'  => ':attribute必须大于1',
        ];
    }
}
