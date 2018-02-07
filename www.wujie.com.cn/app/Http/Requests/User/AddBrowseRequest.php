<?php

namespace App\Http\Requests\User;

use App\Http\Requests\Request;

class AddBrowseRequest extends Request
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
            'uid'      => 'required|integer',
            'relation'      => 'required|in:brand,activity',
            'relation_id'      => 'required|integer|min:1',
        ];

        return $rules;
    }

    public function attributes()
    {
        $attributes = [
            'uid'      => '用户id',
            'relation'      => '浏览目标',
            'relation_id'      => '目标id',
        ];

        return $attributes;
    }

    public function messages()
    {
        return [
            'required' => ':attribute为必传参数',
            'integer'  => ':attribute必须为整数',
            'min'  => ':attribute必须大于1',
            'in'  => ':attribute必须为brand或activity中的一种',
        ];
    }
}
