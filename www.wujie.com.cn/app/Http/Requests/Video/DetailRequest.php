<?php

namespace App\Http\Requests\Video;

use App\Http\Requests\Request;

class DetailRequest extends Request
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
            'id'    => 'required|integer',
        ];

        return $rules;
    }

    public function attributes()
    {
        $attributes = [
            'id'    => '点播id',

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
