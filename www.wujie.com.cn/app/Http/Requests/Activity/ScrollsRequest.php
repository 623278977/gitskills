<?php

namespace App\Http\Requests\Activity;

use App\Http\Requests\Request;

class ScrollsRequest extends Request
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
            'size'         => 'sometimes|integer|min:1',
        ];

        return $rules;
    }

    public function attributes()
    {
        $attributes = [
            'size'         => 'size',

        ];

        return $attributes;
    }

    public function messages()
    {
        return [
            'required' => ':attribute为必传参数',
            'integer'  => ':attribute必须为整数',
            'id.min'  => ':attribute最小为1',
        ];
    }
}
