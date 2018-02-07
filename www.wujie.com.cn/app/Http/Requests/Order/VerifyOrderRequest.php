<?php

namespace App\Http\Requests\Order;

use App\Http\Requests\Request;

class VerifyOrderRequest extends Request
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
            'order_no' => 'required|alpha_num',
            'uid'      => 'required|integer'
        ];

        return $rules;
    }

    public function attributes()
    {
        $attributes = [
            'order_no' => '订单编号',
            'uid'      => '用户uid'
        ];

        return $attributes;
    }

    public function messages()
    {
        return [
            'required'  => ':attribute为必传参数',
            'alpha_num' => ':attribute必须为数字和字母的组合',
            'integer'   => ':attribute必须为整数',
        ];
    }
}