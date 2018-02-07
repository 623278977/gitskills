<?php

namespace App\Http\Requests\Agent\AgentRedPacket;

use App\Http\Requests\Request;

class GiveFRedpacket extends Request
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
            'give_agent_id'=> 'required|exists:agent,id,status,1',
            'get_agent_id'=> 'required|exists:agent,id,status,1',
            'card_id'=> 'required|exists:red_packet,id,status,1',
        ];
    }
    /**
     * 指定名称
     */
    public function attributes()
    {
        return [
            'give_agent_id' => '赠送经纪人',
            'get_agent_id' => '被赠送经纪人',
            'card_id' => '福卡',
        ];
    }

}
