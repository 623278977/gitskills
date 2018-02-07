<?php

namespace App\Http\Requests\Live;

use App\Http\Requests\Request;

class OrderRequest extends Request
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
            'product'   => 'required',
            'body'      => 'required',
            'cost'      => 'required',
            'ticket_id' => 'required',
            'type'      => 'required|in:live,video',
        ];

        return $rules;
    }

    public function attributes()
    {
        $attributes = [
            'uid'       => '用户uid',
            'product'   => '产品标题',
            'body'      => '产品内容',
            'cost'      => '总价',
            'ticket_id' => '票id',
            'type'      => '类型',
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
