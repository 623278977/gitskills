<?php

namespace App\Http\Requests\User;

use App\Http\Requests\Request;

class GetKeywordRequest extends Request
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
            //
            'type' => 'required|in:search,filter,share,agent,agent_share',
        ];
    }

    /**
     * 指定名称
     */
    public function attributes()
    {
        return [
            'type'      => '关键词类型',
        ];
    }

    /**
     * 验证信息提示
     */
    public function messages()
    {
        return [
            'required'              => ':attribute 为必填选项',
            'in'                => ':attribute 无效',
        ];
    }
}
