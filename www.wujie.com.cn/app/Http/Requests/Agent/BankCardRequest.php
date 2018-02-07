<?php namespace App\Http\Requests\Agent;

use App\Http\Requests\Request;

class BankCardRequest extends Request
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
            'bank_name'     => 'required',
            'card_no'       => 'required|numeric',
            'card_type'     => 'required',
        ];
    }

    /**
     * 指定名称
     */
    public function attributes()
    {
        return [
            'agent_id'     => '经纪人ID',
            'bank_name'    => '银行名',
            'card_no'      => '银行卡号',
            'card_type'    => '银行卡类型',
        ];
    }

    /**
     * 验证信息提示
     */
    public function messages()
    {
        return [
            'required'         => ':attribute 为必填选项',
            'card_no.numeric'  => ':attribute 必须是数字',
        ];
    }
}