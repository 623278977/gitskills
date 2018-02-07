<?php namespace App\Http\Controllers\Agent;

use App\Http\Controllers\Api\CommonController;
use App\Models\Agent\Agent;
use Illuminate\Http\Request;

class PushController extends CommonController
{
    public function postPushInfo(Request $request)
    {
        $data = $request->input();

        $user = User::where('username', '18790288316')->first();

        $info   = '恭喜您品牌代理成功';
        $data   = json_encode(['type'=>'brand', 'style'=>'url', 'value'=>"/webapp/live/detail?pagetag=04-9"]);

        $result = send_notification($info, $data, $user);
    }


    /**
     * 经纪人端更新推送标识
     *
     * @param ReceiveRequest $request
     * @return string
     * @author tangjb
     */
    public function postReceive(Request $request)
    {
        $data = $request->input();

        if(!isset($data['agent_id'])){
            return AjaxCallbackMessage('agent_id参数必传', false);
        }

        if(!isset($data['platform'])){
            return AjaxCallbackMessage('platform参数必传', false);
        }

        if(!isset($data['identifier'])){
            return AjaxCallbackMessage('identifier参数必传', false);
        }


        $agent=Agent::find((int)$data['agent_id']);
        if(!$agent){
            return AjaxCallbackMessage('经纪人不存在！', false);
        }
        if($data['identifier'] != $agent->identifier || $data['platform'] != $agent->platform){//终端不一样，需要踢出一个
            if($agent->identifier){//存在标识，就推送清除
                $content = [
                    'type' => 'retreat_login',
                    'date' => date('Y-m-d H:i:s'),
                    'msg' => '您在账号在已经在另外终端登录！',
                ];
                send_transmission(json_encode($content), $agent,null, 1);
            }
            $agent->update(['platform'=>$data['platform'], 'identifier'=>$data['identifier']]);
        }
        //清除其它用户名下占用这个标识
        Agent::where('id', '!=', $agent->id)
            ->where('platform', '=', $data['platform'])
            ->where('identifier', '=', $data['identifier'])
            ->update(['identifier' => '']);
        return AjaxCallbackMessage('操作成功', true);
    }


}