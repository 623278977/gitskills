<?php

namespace App\Http\Requests\Agent\User;

use App\Http\Requests\Request;

class PersonListRequest extends Request
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
            'person_ids'=> 'required',
            'type'=> 'required|in:1,2',
        ];
    }
    public function attributes()
    {
        return [
            'person_ids'=> 'id字符串',
            'type'=> '数据类型',
        ];
    }
}
