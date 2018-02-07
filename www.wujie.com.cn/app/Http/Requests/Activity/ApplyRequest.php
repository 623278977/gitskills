<?php

namespace App\Http\Requests\Activity;

use App\Http\Requests\Request;

class ApplyRequest extends Request
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
            'uid'         => 'required|integer',
            'activity_id' => 'required|integer',
            'pay_way'     => 'required',
            'product'     => 'required',
            'body'     => 'required',
//            'body'        => 'required',
            'cost'        => 'required',
            'maker_id'    => 'sometimes|integer',
//            'company'     => 'required',
//            'job'         => 'required',
            'ticket_id'   => 'required',
            'name'   => 'required',
            'tel'   => 'required',
        ];

        return $rules;
    }

    public function attributes()
    {
        $attributes = [
            'uid'         => '用户uid',
            'activity_id' => '活动id',
            'pay_way'     => '支付方式',
            'product'     => '产品标题',
            'body'        => '产品描述',
            'cost'        => '总价',
            'maker_id'    => '空间id',
            'company'     => '公司',
            'job'         => '职位',
            'ticket_id'   => '门票id',
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
