<?php namespace App\Http\Requests;

use App\Http\Requests\Request;

class CustomerAgentRequest extends Request
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
            'agent_id'    => 'required|integer',
            'brand_id'    => 'required|integer',
            //'contract_id' => 'required|integer',
            'level'     => 'required|integer',
            //'customer_id'   => 'required|integer',
        ];
    }

    /**
     * 指定名称
     */
    public function attributes()
    {
        return [
            'agent_id'    => '经纪人ID',
            'brand_id'    => '品牌ID',
            //'contract_id' => '合同ID',
            'level'     => '品牌评价',
            //'customer_id'   => '投资人ID',
        ];
    }

    /**
     * 验证信息提示
     */
    public function messages()
    {
        return [
            'required'           => ':attribute 为必填选项',
            'agent_id.integer'   => ':attribute 值必须是整数',
            'brand_id.integer'   => ':attribute 值必须是整数',
           // 'contract_id.integer'=> ':attribute 值必须是整数',
            'level.integer'    => ':attribute 值必须是数值',
            //'customer_id.integer'   => ':attribute 值必须是整数',
        ];
    }
}