<?php

namespace App\Http\Requests\News;

use App\Http\Requests\Request;
//use Illuminate\Http\Exception\HttpResponseException;
class DetailRequest extends Request
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
            'id'       => 'required|integer|min:1',
        ];

        return $rules;
    }

    public function attributes()
    {
        $attributes = [
            'id'       => '资讯id',
            'uid'      => '用户id',
        ];

        return $attributes;
    }

    public function messages()
    {
        return [
            'required' => ':attribute为必传参数',
            'integer'  => ':attribute必须为整数',
            'min'  => ':attribute最小为1',
        ];
    }
}
