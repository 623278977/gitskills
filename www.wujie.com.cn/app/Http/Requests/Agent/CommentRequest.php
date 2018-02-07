<?php namespace App\Http\Requests\Agent;

use App\Http\Requests\Request;

class CommentRequest extends Request
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
            'uid'      => 'required|integer',
            'post_id'  => 'required|integer',
            'content'  => 'required_without:images',
        ];
    }

    /**
     * 指定名称
     */
    public function attributes()
    {
        return [
            'uid'      => '评论者ID',
            'post_id'  => '评论的资讯ID',
            'content'  => '评论内容',
        ];
    }

    /**
     * 验证信息提示
     */
    public function messages()
    {
        return [
            'required'     => ':attribute 为必填选项',
            'uid.integer'  => ':attribute 值必须是整数',
            'post_id'      => ':attribute 值必须是整数',
        ];
    }
}
