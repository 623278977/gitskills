<?php

namespace App\Http\Requests\Share;

use App\Http\Requests\Request;

class CollectRequest extends Request
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

        $rules = [
            'type'      => 'required|in:share,relay,watch,view,enroll,sign,intent',
            'share_mark'   => 'required',
            'uid'   => 'required',
            'relation_id'   => 'required',
        ];

        return $rules;
    }

    public function attributes()
    {
        $attributes = [
            'type'      => '类型',
            'share_mark'   => '分享标识码',
            'uid'   => '当前登录用户id',
            'relation_id'   => '关联id',
        ];

        return $attributes;
    }

    public function messages()
    {
        return [
            'required' => ':attribute为必传参数',
            'integer'  => ':attribute必须为整数',
        ];
    }
}
