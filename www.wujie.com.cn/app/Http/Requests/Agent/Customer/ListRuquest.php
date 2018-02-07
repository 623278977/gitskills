<?php

namespace App\Http\Requests\Agent\Customer;

use App\Http\Requests\Request;

class ListRuquest extends Request
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
        return [
            'agent_id'    => 'required|exists:agent,id,status,1',
            'order_by'    => 'in:letter,intention,active,followed_time',
            'filter'    => 'in:all,ovo,inspected,signed_contract',
        ];
    }

    /**
     * 指定名称
     */
    public function attributes()
    {
        return [
            'agent_id' => '经纪人ID',
            'order_by' => '排序方式',
            'filter' => '过滤规则',
        ];
    }

    /**
     * 验证信息提示
     */
    public function messages()
    {
        return [
            'required'          => ':attribute 为必填选项',
            'exists'  => ':attribute 无效',
            'in'  => ':attribute 必须是以下几个之一： :values',
        ];
    }
}
