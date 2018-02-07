<?php

namespace App\Http\Requests\Order;

use App\Http\Requests\Request;

class OrderSignRequest extends Request
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
            'uid'       => 'required|integer',
            'amount'    => 'required',
            'pay_way'   => 'required|in:weixin,ali,unionpay,red_packet',
            'score_num' => 'sometimes|integer',
//            'items'     => 'required|array',
        ];

        return $rules;
    }

    public function attributes()
    {
        $attributes = [
            'uid'       => '用户uid',
            'amount'    => '订单总价',
            'pay_way'   => '支付方式',
            'score_num' => '使用积分数',
            'items'     => '购买项',
        ];

        return $attributes;
    }

    public function messages()
    {
        return [
            'required' => ':attribute为必传参数',
            'integer'  => ':attribute必须为整数',
            'in'       => ':attribute只能为ali或weixin',
            'array'    => ':attribute必须为数组',
        ];
    }
}
