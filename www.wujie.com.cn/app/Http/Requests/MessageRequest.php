<?php namespace App\Http\Requests;

use App\Http\Requests\Request;

class MessageRequest extends Request
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
            'invite_id'     => 'required|integer',
            'type'          => 'required|integer',
            'action_tags'   => 'required',
        ];
    }

    /**
     * 指定名称
     */
    public function attributes()
    {
        return [
            'invite_id'     => '邀请函ID',
            'type'          => '邀请函类型（1：活动；2：考察）',
            'action_tags'   => '动作状态：（consent：接受；reject：拒绝）',
        ];
    }

    /**
     * 验证信息提示
     */
    public function messages()
    {
        return [
            'required'           => ':attribute 为必填选项',
            'invite_id.integer'  => ':attribute 值必须是整数',
            'type.integer'       => ':attribute 值必须是整数',
        ];
    }
}