<?php

namespace App\Http\Requests\Agent;

use App\Http\Requests\Request;

class AgentShareLike extends Request
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
            'agent_id'=>'required|exists:agent,id',
            'like_ids'=> 'required',
        ];
    }

    /**
     * 指定名称
     */
    public function attributes()
    {
        return [
            'agent_id'      => '经纪人ID',
            'like_ids'     => '标签ID字符串',
        ];
    }

    /**
     * 验证信息提示
     */
    public function messages()
    {
        return [
            'required'              => ':attribute 为必填选项',
            'exists'                => ':attribute 无效',
        ];
    }
}
