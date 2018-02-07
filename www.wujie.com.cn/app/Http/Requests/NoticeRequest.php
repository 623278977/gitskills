<?php namespace App\Http\Requests;

use App\Http\Requests\Request;

class NoticeRequest extends Request
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
            'action_type'  => 'required|integer',
            'action_id'    => 'required|integer',
            'action'       => 'required',
            'remark'       => 'required_if:action,reject',
        ];
    }

    /**
     * 指定名称
     */
    public function attributes()
    {
        return [
            'action_type'   => '动作类型ID',
            'action_id'     => '动作ID',
            'action'        => '动作（同意 | 拒绝）',

        ];
    }

    /**
     * 验证信息提示
     */
    public function messages()
    {
        return [
            'required'              => ':attribute 为必填选项',
            'action_type.integer'   => ':attribute 值必须是整数',
            'action_id.integer'     => ':attribute 值必须是整数',

        ];
    }
}