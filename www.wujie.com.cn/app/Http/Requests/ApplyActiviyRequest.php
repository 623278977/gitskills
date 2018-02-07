<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;

class ApplyActiviyRequest extends Request
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
        $baseRules = [
            'subject' => 'required',
            'maker_id' => 'required',
            'description' => 'required',
            'begin_time' => 'required',
            'end_time' => 'required',
            'type' => 'required',
        ];
        return $baseRules;
    }

    /**
     * 属性
     * @return array
     */
    public function attributes()
    {
        return array(
            'subject' => '活动名称',
            'maker_id' => 'OVO中心',
            'description' => '活动描述',
            'begin_time' => '开始时间',
            'end_time' => '结束时间',
            'type' => '活动类型',
        );
    }

    /**
     * 自定义错误消息
     *
     * @return array
     */
    public function messages()
    {
        return array(
            'required' => ':attribute为必填选项',
            'numeric' => ':attribute必须为数字',
            'unique' => ':attribute不能重复',
            'url' => ':attribute必须为合乎规范的url地址',
            'date' => ':attribute必须为日期',
        );
    }
}
