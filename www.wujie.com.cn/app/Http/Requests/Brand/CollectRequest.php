<?php

namespace App\Http\Requests\Brand;

use App\Http\Requests\Request;

class CollectRequest extends Request
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
            'id'       => 'required|integer|exists:brand,id',
            'uid'      => 'required|integer|exists:user,uid',
            'type'   => 'required|in:do,undo',

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
            'type.in'  => ':attribute必须为do或undo',
            'uid.exists'  => ':attribute在user表中不存在',
            'id.exists'  => ':attribute在brand表中不存在',
        ];
    }
}
