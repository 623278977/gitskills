<?php namespace App\Http\Requests\Agent;

use App\Http\Requests\Request;

class InspectInviteRequest extends  Request
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
            'status'        => 'required|integer',
        ];
    }

    /**
     * 指定名称
     */
    public function attributes()
    {
        return [
            'agent_id'      => '经纪人ID',
            'status'        => '邀请函类型',
        ];
    }

    /**
     * 验证信息提示
     */
    public function messages()
    {
        return [
            'required'              => ':attribute 为必填选项',
            'agent_id.integer'      => ':attribute 值必须是整数',
            'status.integer'        => ':attribute 值必须是整数',
        ];
    }
}