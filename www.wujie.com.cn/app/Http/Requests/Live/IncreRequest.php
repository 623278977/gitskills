<?php

namespace App\Http\Requests\Live;

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
            'num'  => 'required|integer',
            'type' => 'required|in:live,video',
        ];

        return $rules;
    }

    public function attributes()
    {
        $attributes = [
            'id'   => '直播id',
            'num'  => '增长数',
            'type' => '类型',
        ];

        return $attributes;
    }

    public function messages()
    {
        return [
            'required' => ':attribute为必传参数',
            'integer'  => ':attribute必须为整数',
            'in'       => ':attribute只能为live或video',
        ];
    }
}
