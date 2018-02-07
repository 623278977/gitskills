<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;

class UserRequest extends Request
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
            'brand_id' => 'required|integer',
            'uid'      => 'required|integer',
            'action'   => 'required|in:update,business,recommend',

        ];

        return $rules;
    }

    public function attributes()
    {
        $attributes = [
            'brand_id' => '品牌id',
            'uid'      => '用户id',
            'action'   => '操作动作',
        ];

        return $attributes;
    }

    public function messages()
    {
        return [
            'required' => ':attribute为必传参数',
            'integer'  => ':attribute必须为整数',
            'in'  => ':attribute必须为更新,参加招商,或者推荐',
        ];
    }
}
