<?php namespace App\Http\Requests\Agent;

use App\Http\Requests\Request;

class MessageAddLogRequest extends  Request
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
            'customer_id'  => 'required|integer',
            'agent_id'     => 'required|integer',
            'type'         => 'required|integer',
            'post_id'      => 'required|integer',
            'action'       => 'required|integer',
        ];
    }

    /**
     * 指定名称
     */
    public function attributes()
    {
        return [
            'customer_id'  => '客户ID',
            'agent_id'     => '经纪人ID',
            'type'         => '类型 1：活动，2：品牌，3：合同',
            'post_id'      => '操作ID：活动ID，品牌ID，合同ID',
            'action'       => '动作类型：-1：失去客户 0：接受派单 1：对接品牌 2：获得联系方式...',
        ];
    }

    /**
     * 验证信息提示
     */
    public function messages()
    {
        return [
            'required'             => ':attribute 为必填选项',
            'agent_id.integer'     => ':attribute 值必须是整数',
            'customer_id.integer'  => ':attribute 值必须是整数',
            'type.integer'         => ':attribute 值必须是整数',
            'post_id.integer'      => ':attribute 值必须是整数',
            'action.integer'       => ':attribute 值必须是整数',
        ];
    }
}