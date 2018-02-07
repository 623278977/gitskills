<?php

namespace App\Http\Requests\Activity;

use App\Http\Requests\Request;

class IncreRequest extends Request
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
            'id'   => 'required|integer',
            'type' => 'required|in:1,-1',
            'col'  => 'required|in:likes,view',
        ];

        return $rules;
    }

    public function attributes()
    {
        $attributes = [
            'id'   => '活动id',
            'type' => '操作类型',
            'col'  => '列',
        ];

        return $attributes;
    }

    public function messages()
    {
        return [
            'required' => ':attribute为必传参数',
            'integer'  => ':attribute必须为整数',
            'type.in'  => ':attribute必须为1或-1',
            'col.in'  => ':attribute必须为view或likes',
        ];
    }
}
