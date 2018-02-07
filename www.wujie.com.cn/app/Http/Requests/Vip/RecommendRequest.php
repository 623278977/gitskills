<?php

namespace App\Http\Requests\Vip;

use App\Http\Requests\Request;

class RecommendRequest extends Request
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
            'vip_id'      => 'required|integer',
            'uid'         => 'sometimes|integer',
            'position_id' => 'sometimes|integer',
            'resource'    => 'required|array',
        ];

        return $rules;
    }

    public function attributes()
    {
        $attributes = [
            'vip_id'      => '专版id',
            'uid'         => '登陆者uid',
            'position_id' => '当前所在地区的id',
            'resource'    => '推荐资源',

        ];

        return $attributes;
    }

    public function messages()
    {
        return [
            'required' => ':attribute为必传参数',
            'integer'  => ':attribute必须为整数',
            'array'    => ':attribute必须为数组',
        ];
    }
}
