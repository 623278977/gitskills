<?php

namespace App\Http\Requests\Brand;

use App\Http\Requests\Request;

class MessageRequest extends Request
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
            'id'       => 'required|integer',
            'uid'      => 'required|integer',
            'mobile'   => 'required',
            'realname' => 'required',
//            'zone_id'  => 'required|integer',
//            'address'  => 'required',
            'consult'  => 'required',
//            'consult_type'  => 'required',
        ];

        return $rules;
    }

    public function attributes()
    {
        $attributes = [
            'id'       => '品牌id',
            'uid'      => '用户id',
            'mobile'   => '手机号',
            'realname' => '真实姓名',
            'zone_id'  => '地区id',
            'address'  => '地址',
            'consult'  => '留言',

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
