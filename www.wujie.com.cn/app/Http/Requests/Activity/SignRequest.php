<?php

namespace App\Http\Requests\Activity;

use App\Http\Requests\Request;

class SignRequest extends Request
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
            'uid'         => 'required|integer',
            'activity_id' => 'required|integer',
            'maker_id'    => 'required|integer',
        ];

        return $rules;
    }

    public function attributes()
    {
        $attributes = [
            'uid'         => '当前登录用户uid',
            'activity_id' => '活动id',
            'maker_id'    => '空间id',
        ];

        return $attributes;
    }

    public function messages()
    {
        return [
            'required'  => ':attribute为必传参数',
            'integer' => ':attribute必须为整数',
        ];
    }
}
