<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;

class ActivityApplyRequest extends Request
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

    public function rules()
    {
        $rules['apply_end_time'] = 'required|date';
        $rules['subject'] = 'required';
        $rules['begin_time'] = 'required|date';

        return $rules;
    }

    public function attributes()
    {
        $attributes = array(
            'id'                => '活动id',
            'organizer'         => '主办方',
            'organizer_url'     => '主办方网址',
            'view'             => '浏览量',
        );

        return $attributes;
    }

    public function messages()
    {
        $messages = [
            'required' => ':attribute为必填选项',
            'numeric'  => ':attribute必须为数字',
            'url'      => ':attribute必须为合乎规范的url地址',
            'date'     => ':attribute必须为日期',
        ];

        return $messages;
    }
}
