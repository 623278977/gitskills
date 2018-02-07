<?php namespace App\Http\Requests\Agent;

use App\Http\Requests\Request;

class MessageRequest extends  Request
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
            'customer_id'   => 'required|integer',
            'tags'          => 'required',
            'id'            => 'required_if:tags,submits|integer',
            'level_id'      => 'required_if:tags,submits|integer',
            'remark'        => 'required_if:tags,submits',
        ];
    }

    /**
     * 指定名称
     */
    public function attributes()
    {
        return [
            'agent_id'      => '经纪人ID',
            'customer_id'   => '客户ID',
            'level_id'      => '客户等级ID',
            'id'            => '品牌ID',
            'status'        => '邀请函类型',
            'remark'        => '备注信息',
            'tags'          => '标记类型（default|submits）',
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
            'level_id.integer'     => ':attribute 值必须是整数',
            'id.integer'           => ':attribute 值必须是整数',
        ];
    }
}