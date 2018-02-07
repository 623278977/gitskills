<?php
namespace App\Http\Controllers\Api;

use DB;
use App\Http\Requests\Push\ReceiveRequest;
use App\Models\User\Entity as User;
use Illuminate\Http\Request;
class PushController extends CommonController
{
    /**
     * 个推用户信息接收
     */
    public function postReceive(ReceiveRequest $request)
    {
        $data = $request->input();
        $user=User::find((int)$data['uid']);
        if(!$user){
            return AjaxCallbackMessage('用户不存在！', false);
        }
        if($data['identifier'] != $user->identifier || $data['platform'] != $user->platform){//终端不一样，需要踢出一个
            if($user->identifier){//存在标识，就推送清除
                $content = [
                    'type' => 'retreat_login',
                    'date' => date('Y-m-d H:i:s'),
                    'msg' => '您在账号在已经在另外终端登录！',
                ];
                send_transmission(json_encode($content), $user);
            }
            $user->update(['platform'=>$data['platform'], 'identifier'=>$data['identifier']]);
        }
        //清除其它用户名下占用这个标识
        User::where('uid', '!=', $user->uid)
                ->where('platform', '=', $data['platform'])
                ->where('identifier', '=', $data['identifier'])
                ->update(['identifier' => '']);
        return AjaxCallbackMessage('操作成功', true);
    }



    public function postTest(Request $request)
    {
        $data = $request->input();

        $user = User::where('username', '18790288316')->first();

        $result = send_notification('订阅的直播，将在10分钟后开始直播', '你订阅的直播10分钟后即将开启，点击查看更多',
            json_encode(['type'=>'live_detail', 'style'=>'url',
                         'value'=>"/webapp/live/detail?pagetag=04-9"]), $user);
        dd($result);
    }


}