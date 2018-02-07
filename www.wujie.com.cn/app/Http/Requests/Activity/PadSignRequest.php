<?php

namespace App\Http\Requests\Activity;

use App\Http\Requests\Request;

class PadSignRequest extends Request
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
            'ticket_no' => 'required|alpha_num',
        ];

        return $rules;
    }

    public function attributes()
    {
        $attributes = [
            'ticket_no' => '门票编号',
        ];

        return $attributes;
    }

    public function messages()
    {
        return [
            'required'  => ':attribute为必传参数',
            'alpha_num' => ':attribute必须为数字或字母',
        ];
    }
}
