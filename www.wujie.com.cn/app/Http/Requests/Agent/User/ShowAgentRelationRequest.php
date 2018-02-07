<?php

namespace App\Http\Requests\Agent\User;

use App\Http\Requests\Request;

class ShowAgentRelationRequest extends Request
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
        $data = parent::rules();
        $data['type'] = 'required';
        return $data;
    }
    /**
     * 指定名称
     */
    public function attributes()
    {
        $data = parent::attributes();
        $data['type'] = '关系类型';
        return $data;
    }

}
