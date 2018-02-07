<?php

namespace App\Http\Requests\Live;

use App\Http\Requests\Request;

class DoSubscribeRequest extends Request
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
            'uid' => 'required|integer',
            'live_id' => 'required|integer',
            'type' => 'required|in:1,0',
        ];

        return $rules;
    }

    public function attributes()
    {
        $attributes = [
            'uid'     => '用户uid',
            'live_id' => '直播id',
            'type'    => '类型'
        ];

        return $attributes;
    }

    public function messages()
    {
        return [
            'required' => ':attribute为必传参数',
            'integer'  => ':attribute必须为整数',
            'in'  => ':attribute必须为1或者0',
        ];
    }
}
