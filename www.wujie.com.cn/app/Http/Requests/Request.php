<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;

abstract class Request extends FormRequest
{
    public function formatErrors(Validator $validator)
    {
        $messageBag =  $validator->getMessageBag();
        if(count($messageBag)){
            return ['message'=> $messageBag->first(), 'status'=> false];
        }
        return ['message'=> '参数错误', 'status'=> false];
    }



    /*
     *
     * 以下为基本内容验证，补充或覆盖可以在具体request中修改
     * */
    public function rules()
    {
        return [
            'agent_id'    => 'required|exists:agent,id,status,1',
        ];
    }


    /**
     * 指定名称
     */
    public function attributes()
    {
        return [
            'agent_id' => '经纪人id',
        ];
    }

    /**
     * 验证信息提示
     */
    public function messages()
    {
        return [
            'required'          => ':attribute 为必填选项',
            'exists'          => ':attribute 无效',
            'in'  => 'The :attribute 必须在以下类型中: :values',
        ];
    }
}
