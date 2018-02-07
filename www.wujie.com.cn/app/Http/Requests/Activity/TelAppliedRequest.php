<?php

namespace App\Http\Requests\Activity;

use App\Http\Requests\Request;

class TelAppliedRequest extends Request
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
            'activity_id' => 'required|integer',
            'tel'   => 'required',
        ];

        return $rules;
    }

    public function attributes()
    {
        $attributes = [
            'tel'         => '手机号',
            'activity_id' => '活动id',
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
