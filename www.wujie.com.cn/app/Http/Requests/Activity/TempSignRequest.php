<?php

namespace App\Http\Requests\Activity;

use App\Http\Requests\Request;
use Illuminate\Support\Facades\Input;

class TempSignRequest extends Request
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
        $sign_type = Input::get('sign_type','half_open');

        if($sign_type == 'half_open'){
            $rules = [
                'sign_type'    => 'required',
                'uid'         => 'required|integer',
                'activity_id' => 'required|integer',
                'maker_id'    => 'required|integer',
                'name'    => 'required',
                'tel'    => 'required',
            ];
        }else{
            $rules = [
                'sign_type'    => 'required',
                'uid'         => 'required|integer',
                'activity_id' => 'required|integer',
                'maker_id'    => 'required|integer',
            ];
        }

        return $rules;
    }

    public function attributes()
    {
        $attributes = [
            'uid'         => '当前登录用户uid',
            'activity_id' => '活动id',
            'maker_id'    => '空间id',
            'name'    => '真实姓名',
            'tel'    => '手机号',
            'sign_type' => '活动类型'
        ];

        return $attributes;
    }

    public function messages()
    {
        return [
            'required'  => ':attribute为必传参数',
            'integer' => ':attribute必须为整数',
        ];
    }
}
