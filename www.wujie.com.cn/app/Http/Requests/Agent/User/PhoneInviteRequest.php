<?php

namespace App\Http\Requests\Agent\User;

use App\Http\Requests\Request;

class PhoneInviteRequest extends Request
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
        $pRules = parent::rules();
        $rules =  [
            'mobile'    => 'required',
            'type'    => 'required|in:1,2',
        ];
        return $rules + $pRules;
    }

    /**
     * 指定名称
     */
    public function attributes()
    {
        $pAttributes = parent::attributes();
        $attributes =  [
            'mobile'    => '手机号',
            'type'    => '调用类型',
        ];
        return $attributes + $pAttributes;
    }

}
