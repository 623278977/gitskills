<?php

namespace App\Http\Requests\Vip;

use App\Http\Requests\Request;

class RecordRequest extends Request
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
            'uid'    => 'required|integer',
            'vip_id' => 'sometimes|integer',
        ];

        return $rules;
    }

    public function attributes()
    {
        $attributes = array(
            'uid'    => '用户uid',
            'vip_id' => '专版id',
        );

        return $attributes;
    }

    public function messages()
    {
        $messages = [
            'required'            => ':attribute为必传参数',
            'integer'              => ':attribute必须为自然数',
        ];

        return $messages;
    }
}
