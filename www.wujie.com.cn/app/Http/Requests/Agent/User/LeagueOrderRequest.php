<?php

namespace App\Http\Requests\Agent\User;

use App\Http\Requests\Request;

class LeagueOrderRequest extends Request
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
        $data['type'] = 'in:0,1';
        return $data;
    }

    /**
     * 指定名称
     */
    public function attributes()
    {
        $data = parent::attributes();
        $data['type'] = '是否付清';
        return $data;
    }
}
