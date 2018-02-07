<?php namespace App\Http\Requests\Agent\Academy;

use App\Http\Requests\Request;

class StudyRequest extends  Request
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
            'brand_id'      => 'required|integer',
            'study_type'    => 'required|in:1,2',
            'post_id'       => 'required|integer',
            //'select_result' => 'required',
        ];
    }

    /**
     * 指定名称
     */
    public function attributes()
    {
        return [
            'brand_id'      => '品牌ID',
            'study_type'    => '学习类型',
            'post_id'       => '学习类型ID',
            //'select_result' => '答题',
        ];
    }

    /**
     * 验证信息提示
     */
    public function messages()
    {
        return [
            'required'          => ':attribute 为必填选项',
            'brand_id.integer'  => ':attribute 值必须是整数',
            'post_id.integer'   => ':attribute 值必须是整数',
            'study_type.in'     => ':attribute 只能是：1（视频）或 2（咨询）'
        ];
    }
}