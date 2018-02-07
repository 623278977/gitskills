<?php namespace App\Http\Requests\Agent\Bank;

use App\Http\Requests\Request;

class BankRequest extends Request
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
            'agent_id'      => 'required|integer',
            'type'          => 'required|in:list,delete',
            'bank_id'       => 'required_if:type,delete|array',
        ];
    }

    /**
     * 指定名称
     */
    public function attributes()
    {
        return [
            'agent_id'   => '经纪人ID',
            'type'       => '操作类型',
            'bank_id'    => '银行卡ID',
        ];
    }

    /**
     * 验证信息提示
     */
    public function messages()
    {
        return [
            'required'         => ':attribute 为必填选项',
            'agent_id.integer' => ':attribute 值必须是整数',
            'type.in'          => ':attribute type 的值，只能为 list 或 delete'
        ];
    }
}
