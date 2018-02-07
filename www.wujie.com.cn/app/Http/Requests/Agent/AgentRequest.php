<?php namespace App\Http\Requests\Agent;

use App\Http\Requests\Request;

class AgentRequest extends Request
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
            'uid'           => 'required|integer',
            'post_id'       => 'required|integer',
            'type'          => 'required|integer',
            'default_money' => 'required_if:type,2|numeric',
            'inspect_time'  => 'required_if:type,2',
        ];
    }

    /**
     * 指定名称
     */
    public function attributes()
    {
       return [
            'agent_id'      => '经纪人ID',
            'uid'           => '投资人ID',
            'post_id'       => '邀请函类型ID：type=1为活动ID；type=2为门店ID',
            'type'          => '邀请函类型',
            'default_money' => '考察邀请需要支付的定金',
            'inspect_time'  => '考察时间',
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
            'uid.integer'           => ':attribute 值必须是整数',
            'type.integer'          => ':attribute 值必须是整数',
            'post_id'               => ':attribute 值必须是整形',
            'default_money.numeric' => ':attribute 必须是数值',
        ];
    }
}
