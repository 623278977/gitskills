<?php

namespace App\Http\Requests\Brand;

use App\Http\Requests\Request;

class ConsultRequest extends Request
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
            'size'      => 'sometimes|integer|min:1',
            'id'      => 'sometimes|integer|min:1',

        ];

        return $rules;
    }

    public function attributes()
    {
        $attributes = [
            'size'      => '规格',
            'id'      => 'id',
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
