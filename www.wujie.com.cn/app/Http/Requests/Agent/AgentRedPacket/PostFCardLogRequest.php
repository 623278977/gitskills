<?php

namespace App\Http\Requests\Agent\AgentRedPacket;

use App\Http\Requests\Request;

class PostFCardLogRequest extends Request
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
        $data['card_id'] = 'exists:red_packet,id,status,1';
        return $data;
    }

    /**
     * 指定名称
     */
    public function attributes()
    {
        $data = parent::attributes();
        $data['card_id'] = '福卡id';
        return $data;
    }
}
