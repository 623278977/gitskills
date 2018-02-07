<?php

namespace App\Http\Requests\Vip;

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
            'vip_id'    => 'required|integer',
            'uid'       => 'sometimes|integer',
            'attach'    => 'sometimes|integer',
            'agreement' => 'sometimes|integer',
            'package'   => 'sometimes|integer',
        ];

        return $rules;
    }

    public function attributes()
    {
        $attributes = [
            'vip_id'    => '专版id',
            'uid'       => '用户uid',
            'attach'    => '属性',
            'agreement' => '会员权益',
            'package'   => '专版套餐信息',

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
