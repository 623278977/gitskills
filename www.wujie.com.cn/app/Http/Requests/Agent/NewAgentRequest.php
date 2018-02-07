<?php namespace App\Http\Requests\Agent;

use App\Http\Requests\Request;

class NewAgentRequest extends Request
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
            'id'        => 'required|integer',
            'agent_id'  => 'required|integer',
        ];
    }

    /**
     * 指定名称
     */
    public function attributes()
    {
        return [
            'id'        => '详情ID',
            'agent_id'  => '当前登录的经纪人ID',
        ];
    }

    /**
     * 验证信息提示
     */
    public function messages()
    {
        return [
            'required'          => ':attribute 为必填选项',
            'id.integer'        => ':attribute 值必须是整数',
            'agent_id.integer'  => ':attribute 值必须是整数',
        ];
    }
}
