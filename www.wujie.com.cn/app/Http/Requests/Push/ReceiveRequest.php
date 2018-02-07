<?php

namespace App\Http\Requests\Push;

use App\Http\Requests\Request;

class ReceiveRequest extends Request
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
            'identifier' => 'required',
            'uid'        => 'required|integer|min:1',
            'platform'   => 'sometimes|in:ios,android,weixin,other',

        ];

        return $rules;
    }

    public function attributes()
    {
        $attributes = [
            'identifier' => '唯一标识',
            'uid'        => '用户uid',
            'platform'   => '平台',
        ];

        return $attributes;
    }

    public function messages()
    {
        return [
            'required' => ':attribute为必传参数',
            'integer'  => ':attribute必须为整数',
            'platform.in'  => ':attribute必须为ios,android,weixin,other中的一种',
            'uid.min'  => ':attribute最小为1',
        ];
    }
}
