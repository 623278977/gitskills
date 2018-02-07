<?php namespace App\Services\Version\Agent\User;

use App\Services\Version\VersionSelect;
use DB;
use Validator;
use App\Models\Agent\Agent;
use App\Models\Agent\Invitation;
use App\Models\Agent\AgentKeyword;
use App\Models\Keywords;
use App\Models\User\Entity as User;
use Illuminate\Database\Eloquent\Model;
use App\Models\Identify as Identify;


class _v010002 extends _v010001
{

    /*shiqy
     * 撤销考察、活动邀请函
     * */
    public function postBackOut($input){
        $validator = \Validator::make($input,[
            'invite_id' => 'required|exists:invitation,id',
        ]);
        if($validator->fails()){
            $warnings = $validator->messages();
            $show_warning = $warnings->first();
            return ['message'=>$show_warning ,'status'=>false];
        }
        Invitation::where('id',$input['invite_id'])->update(['status' => -3]);
        return ['message'=>'ok' ,'status'=>true];
    }

    //个人详情页（名片）
    /*
     * 添加：
     *  1、级别字段拆分
     *  2、添加分享标签展示
     * */
    public function postCard($input)
    {
        $data = parent::postCard($input);
        if(!$data['status']){
            return ['message'=>$data['message'] ,'status'=>false];
        }
        $data = $data['message'];
        //获取分享标签
        if(!empty($input['is_share'])){
            $agentKeywordsInfo = AgentKeyword::getAgentKeywords('agent_share' , $input['agent_id']);
            foreach ($agentKeywordsInfo as $oneKeyword){
                $arr = [
                    'keyword_id'=>  trim($oneKeyword['keyword_id']),
                    'keyword_name'=>  trim($oneKeyword['keywords']['contents']),
                    'likes'=>  trim($oneKeyword['likes']),
                ];
                $data['share_label'][] = $arr;
            }
        }

        return ['message'=>$data ,'status'=>true];
    }

    /*
     * 获取指定类型所有关键词
     * */
    public function postKeywords($input)
    {
        $data = [];
        $allKeywords = Keywords::where('type',$input['type'])->chunk(6,function($items)use(&$data){
            $arr = [];
            foreach ($items as $item){
                $one = [];
                $one['keyword_id'] = $item['id'];
                $one['keyword_name'] = $item['contents'];
                $arr[] = $one;
            }
            $data[] = $arr;
        });
        return ['message'=>$data ,'status'=>true];
    }

    /*
    * 在经纪人分享页，点赞提交接口
    * */
    public function postShareLike($input)
    {
        $keywordIds = array_filter(explode(',',$input['like_ids']));
        foreach ($keywordIds as $keywordId){
            AgentKeyword::firstOrCreate(['agent_id' => $input['agent_id'],'keyword_id'=>$keywordId]);
            AgentKeyword::where('agent_id',$input['agent_id'])->where('keyword_id',$keywordId)->increment('likes');
        }
        return ['message'=>'ok' ,'status'=>true];
    }

    /**
     * 被邀请的情况下注册成经纪人 功能添加：添加push通知  --数据中心版
     * @User shiqy
     * @param $input
     * @return array
     */
    public function postAgentRegister($input){
        $data = parent::postAgentRegister($input);
        if($data['status'] == false){
            return $data;
        }
        $downAgent = $data['message'];
        $agent = Agent::find($input['agent_id']);
        //发送通知
        $rel = SendTemplateNotifi('agent_title', [], "invite_agent",
            [
                'name'=> empty($input['nickname']) ? '新成员'.$downAgent['id'] : trim($input['nickname']),
                'phone'=> $downAgent['username'],
            ],
            json_encode([
                'type' => 'invite_agent',
                'style' => 'url',
                'value' => trim($downAgent['id']),
            ]), $agent, null, 1);
        return ['message'=> 'ok' , 'status'=>true];
    }


    /**
     * 被邀请的情况下注册成投资人  --数据中心版
     * @User shiqy
     * @param $input
     * @return array|static
     */
    public function postCustomerRegister($input)
    {
        //md5加盐后的号码
        $non_reversible = encryptTel($input['username']);
        //伪号码
        $en_username = pseudoTel($input['username']);

        $agentInfo = Agent::find($input['agent_id']);
        $input['app_name'] = empty($input['app_name']) ? 'wjsq' : trim($input['app_name']);
        $input['phone_code'] = empty($input['phone_code']) ? '86' : trim($input['phone_code']);
        //如果是先写的是经纪人自己的号码，则在投资人表中快速注册，但不绑定。
        if ($agentInfo['non_reversible'] == $non_reversible) {
            $code = trim($input['code']);
            $type = trim($input['type']);
            $indentResult = Identify::checkIdentify($non_reversible,$type,$code,$time=900,$input['app_name']);
            if($indentResult != 'success'){
                return array(
                    'error'=>1,
                    'message' =>$indentResult
                );
            }
            $userInfo = User::where('non_reversible', $non_reversible)->first();
            if (is_object($userInfo)) {
                return ['message' => '该号码已经被邀请过了', 'status' => false];
            }
            $data = User::create(
                [
                    'nation_code' => trim($input['phone_code']),
                    'username' => $en_username,
                    'non_reversible' => $non_reversible,
                    //为了向下兼容，做这样的处理
                    'nickname' => empty($input['nickname']) ? getRandomString(5) : trim($input['nickname']),
                    'created_at' => time(),
                    'my_invite' => User::generateUniqueInviteCode(),
                ]
            );
            //获取投资人的token
            $user_token = GainToken($data['uid'], $data['nickname'], '');
            User::where('uid', $data['uid'])->update(['token' => $user_token]);
        } else {
            $data = parent::postCustomerRegister($input);
            if ($data['status'] == false) {
                return $data;
            }
            $data = $data['message'];
        }



        //红点
        $res = send_transmission(json_encode(['type' => 'red_packet_invite_customer', 'style' => 'json', 'value' =>
            ['username' => $agentInfo->username, 'id' => $data['uid'], 'realname' => $data['realname'], 'nickname' => $data['nickname']]]),
            $agentInfo, null, 1);

        //发送通知
        SendTemplateNotifi('agent_title', [], "invite_customer",
            [
                'name' => empty($input['nickname']) ? '新用户' . $data['uid'] : trim($input['nickname']),
                'phone' => $data['username'],
            ],
            json_encode([
                'type' => 'invite_customer',
                'style' => 'url',
                'value' => trim($data['uid']),
            ]), $agentInfo, null, 1);

        return ['message' => $data, 'status' => true];
    }


}