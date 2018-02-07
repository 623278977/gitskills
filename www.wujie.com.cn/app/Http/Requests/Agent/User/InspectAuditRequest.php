<?php

namespace App\Http\Requests\Agent\User;

use App\Http\Requests\Request;

class InspectAuditRequest extends Request
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
            'auditor_id' => 'required|exists:agent,id,status,1',
            'invitation_id' => 'required|exists:invitation,id,type,2',
            'status' => 'required|in:-1,1',
        ];
    }

    /**
     * 指定名称
     */
    public function attributes()
    {
        return [
            'auditor_id' => '审核人',
            'invitation_id' => '考察邀请函id',
            'status' => '审核类型',
        ];
    }
}
