<?php

namespace App\Http\Requests\Live;

use App\Http\Requests\Request;

class OrderListRequest extends Request
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
            'live_id' => 'required|integer|min:1|exists:live,id',
            'real_order_max_id' => 'sometimes|integer',
            'sham_order_max_id' => 'sometimes|integer',
        ];

        return $rules;
    }

    public function attributes()
    {
        $attributes = [
            'live_id' => '直播id',
            'real_order_max_id' => '真实订单最大id',
            'sham_order_max_id' => '制造订单最大id',
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
