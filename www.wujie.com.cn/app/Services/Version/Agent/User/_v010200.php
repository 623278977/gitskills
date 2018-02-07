<?php namespace App\Services\Version\Agent\User;

use App\Models\Agent\Agent;
use App\Models\Agent\AgentBankCard;
use App\Models\User\Entity as User;
use App\Models\Agent\AgentCustomer;
use App\Models\Agent\AgentScreenCapture;
use App\Models\Agent\Score\AgentScoreLog;
use App\Services\Version\VersionSelect;
use DB;
use Validator;
//use App\Models\Agent\Agent;
use App\Models\RedPacket\RedPacket;
use App\Models\RedPacket\RedPacketPerson;
use App\Models\Agent\AgentFeedback;
use App\Models\Agent\AgentFeedbackImages;
use App\Models\Agent\Entity\_v010200 as Agentv010200;


class _v010200 extends _v010100
{
    const TYPE_LIST     = 'list';       //显示列表
    const TYPE_DELETE   = 'delete';     //指定删除
    const NUMBER_1      = 1;            //数字 1

    /*
     * 经纪人意见反馈接口(新增图片上传)
     * */
    public function postAgentFeedback($input){
        $validator = \Validator::make($input,[
            'agent_id' => 'required|exists:agent,id',
            'content' => 'required',
        ]);
        if($validator->fails()){
            $warnings = $validator->messages();
            $show_warning = $warnings->first();
            return ['message'=>$show_warning ,'status'=>false];
        }
        $rel = AgentFeedback::create([
            'agent_id'=>$input['agent_id'],
            'content'=>$input['content'],
            'type' =>$input['type']
        ]);
        if(!is_object($rel)){
            return ['message'=>'提交失败' ,'status'=>false];
        }
        $image=(array)$input['images'];
        if(!empty($image)){
            foreach ($image as $v){
                AgentFeedbackImages::create(
                    [
                        'feedback_id' => $rel->id,
                        'url'        => $v,
                    ]
                );
            }
        }
        
        //给积分
        Agentv010200::add($input['agent_id'], AgentScoreLog::$TYPES_SCORE['16'], 16, '进行bug反馈', $rel->id, 1);

        return ['message'=>'您的宝贵意见已经反馈至无界商圈，请耐心等待，我们会对您进行反馈和恢复。感谢您的支持' ,'status'=>true];
    }

    /**
     * 邀请经纪人投资人是的通讯录导入过滤优化  --todo 新加的 暂不处理
     * @User shiqy
     * @param $input
     * @return array
     */
    public function postPhoneInvite($input){
        $phoneStr = $input['mobile'];
        $nativePhoneArr = explode(',',$phoneStr);
        $phoneArr = [];
        $md5ToPhoneArr = [];
        //生成密文数组，和，密文号码字典
        foreach ($nativePhoneArr as $phone ){
            $md5Phone = encryptTel($phone);
            $phoneArr[] = $md5Phone;
            $md5ToPhoneArr[$md5Phone] = $phone;
        }
        $type = $input['type'];
        $agentId = intval($input['agent_id']);
        $agent = Agent::find($agentId);
        //过滤自己的号码
        $phoneArr = array_filter($phoneArr , function($item)use($agent){
            if(empty($item) || $item == $agent['non_reversible']){
                return false;
            }
            return true;
        });
        if(!is_array($phoneArr)){
            return ['message' => 'mobile应为一个数组' , 'status' => false];
        }
        $data = [];

        if($type == 1){ //投资人
            //筛选出已经注册并且有邀请人的投资人，另外看看该经纪人是否和这个投资人是好友关系
            $userList = User::with(['agent_customer' => function($query)use($agentId){
                    $query->where('agent_id' , $agentId);
                }])
                ->whereIn('non_reversible' , $phoneArr)->get()->toArray();
            //获取所有注册的投资人手机号
            $hasRegUsers = array_pluck($userList , 'non_reversible');
            //获取有所有和该经纪人有关系的手机号
            $friendUsers = array_pluck(array_where($userList ,function ($key , $item){
                if( !empty($item['agent_customer'] )){
                    return true;
                }
                return false;
            }) , 'non_reversible');
            foreach ($phoneArr as $phone){
                $arr = [];
                $arr['phone'] = trim($md5ToPhoneArr[$phone]);
                if (!checkMobile($arr['phone'] , getNationCode($arr['phone']))) {
                    $arr['type'] = '0';
                }
                else if(!in_array($phone , $hasRegUsers)){
                    $arr['type'] = '1';
                }
                else{
                    $userInfo = head(array_where($userList, function ($key, $item) use($phone){
                        return $item['non_reversible'] == $phone;
                    }));
                    //查看该投资人是否被禁用
                    if(!in_array($userInfo['status'] , [1,2])){
                        $arr['type'] = '-1';
                    }
                    else if( in_array($phone , $friendUsers) ){
                        $arr['type'] = '3';
                    }
                    else{
                        $arr['ID'] = trim($userInfo['uid']);
                        $arr['type'] = '2';
                    }
                }
                $data[] = $arr;
            }
        }
        else{
            //筛选出已经注册经纪人，另外看看该经纪人是否和这个经纪人是好友关系
            $agentList = Agent::with(['agent_friends_relation'=>function($query)use($agentId){
                    $query->where('relation_agent_id' , $agentId);
                }])->with(['agent_friends_relation1' => function($query)use($agentId){
                    $query->where('execute_agent_id' , $agentId);
                }])
                ->whereIn('non_reversible' , $phoneArr)->get()->toArray();

            $hasRegAgents = array_pluck($agentList , 'non_reversible');
            $friendAgents = array_pluck(array_where($agentList , function ($key , $item)use($agent){
                //上下级关系默认好友关系
                if($item['non_reversible'] == $agent['register_invite'] || $item['register_invite'] == $agent['non_reversible']){
                    return true;
                }
                if( !empty($item['agent_friends_relation'] ) || !empty($item['agent_friends_relation1'] ) ){
                    return true;
                }
                return false;
            }) , 'non_reversible');
            foreach ($phoneArr as $phone){
                $arr = [];
                $arr['phone'] = trim($md5ToPhoneArr[$phone]);
                if (!checkMobile($arr['phone'] , getNationCode($arr['phone']))) {
                    $arr['type'] = '0';
                }
                else if(!in_array($phone , $hasRegAgents)){
                    $arr['type'] = '1';
                }
                else{
                    $agentInfo = head(array_where($agentList, function ($key, $item) use($phone){
                        return $item['non_reversible'] == $phone;
                    }));
                    //查看该投资人是否被禁用
                    if($agentInfo['status'] == -1){
                        $arr['type'] = '-1';
                    }
                    else if( in_array($phone , $friendAgents) ){
                        $arr['type'] = '3';
                    }
                    else{
                        $arr['ID'] = trim($agentInfo['id']);
                        $arr['type'] = '2';
                    }
                }
                $data[] = $arr;
            }
        }
        return ['message'=> $data , 'status'=>true ];
    }





    /**
     * 经纪人积分列表
     *
     * @param $input
     * @return array
     * @author tangjb
     */
    public function postScoreList($input)
    {
        $agent_id = array_get($input, 'agent_id');
        $page = array_get($input, 'page', 1);
        $pageSize = array_get($input, 'pageSize', 10);
        $operation = array_get($input, 'operation');
        $validator = \Validator::make($input, [
            'agent_id' => 'required|exists:agent,id',
        ], [], [
            'agent_id'=>'经纪人id',
        ]);

        if($validator->fails()){
            $warnings = $validator->messages();
            $show_warning = $warnings->first();
            return ['message'=>$show_warning ,'status'=>false];
        }

        $list = AgentScoreLog::where('agent_id', $agent_id)
            ->where(function($query) use ($operation){
                if(!empty($operation)){
                    $query->where('operation', $operation);
                }
            })
            ->orderBy('created_at', 'desc')
            ->forpage($page, $pageSize)->get();

        $data = [];
        foreach($list as $k=>$v){
            //增减
            $data[$k]['operation'] = $v->operation;
            //积分
            $data[$k]['num'] = $v->num;
            //时间
            $data[$k]['created_at'] =date('Y-m-d H:i', $v->created_at->timestamp);
            //描述
            $data[$k]['descri'] =$v->getDescri() ;
            //类型
            $data[$k]['type'] =$v->getType() ;
        }


        return ['message'=>$data ,'status'=>true];
    }




    /**
     * 经纪人积分列表
     *
     * @param $input
     * @return array
     * @author tangjb
     */
    public function postInfo($input)
    {
        $type = array_get($input, 'type');
        $id = array_get($input, 'id');

        $validator = \Validator::make($input, [
            'id' => 'required|numeric',
            'type' => 'required|in:a,c',
        ], [], [
            'id'=>'id',
            'type'=>'类型',
        ]);

        if($validator->fails()){
            $warnings = $validator->messages();
            $show_warning = $warnings->first();
            return ['message'=>$show_warning ,'status'=>false];
        }


        if($type=='a'){
            $entity = Agent::where('id', $id)->select('id', 'avatar', 'token', 'nickname', 'realname')->first();
        }else{
            $entity =  User::where('uid', $id)->select('uid as id', 'avatar', 'token', 'nickname', 'realname')->first();
        }

        if(!$entity){
            return ['message'=>'该用户不存在' ,'status'=>false];
        }


        $entity->avatar = getImage($entity->avatar, 'avatar', '');

        return ['message'=>$entity ,'status'=>true];
    }

    /**
     * author zhaoyf
     *
     * 经纪人删除银行卡 agent-delete-banks
     *
     * @param $param = [
     *  'agent_id' => '经纪人ID' int
     *  'type'     => '区分是显示银行卡列表信息，还是删除某张银行卡'(list, delete) string
     *  'bank_id'  => '删除银行卡时，需要传递删除某个银行卡的ID' int
     * ]
     *
     * @return list | bool
     */
    public function postAgentDeleteBanks($param)
    {
        //获取操作类型
        $type       = $param['type'];
        $agent_id   = $param['agent_id'];

        //type 等于list表示显示银行卡列表信息
        //type 等于delete表示删除某张银行卡
        if ($type == self::TYPE_LIST) {
           $gain_result = AgentBankCard::instances()->gainDifferentTypeDatas($agent_id, self::TYPE_LIST);
        } elseif ($type == self::TYPE_DELETE) {
           $gain_result = AgentBankCard::instances()->gainDifferentTypeDatas($agent_id, self::TYPE_DELETE, $param['bank_id']);
        }

       //对结果进行处理
        if (is_numeric($gain_result)) {
            if ($gain_result == self::NUMBER_1) {
                return ['message' => '删除成功', 'status' => true];
            } else {
                return ['message' => '删除失败', 'status' => false];
            }
        } elseif (is_array($gain_result)) {
            if (!empty($gain_result)) {
                return ['message'=> $gain_result, 'status' => true];
            } else {
                return ['message' => '该经纪人没有相关银行卡信息', 'status' => false];
            }
        }
    }

    /*
     * 新手任务列表
     */
    public function postTask($input)
    {
        $agent_id=$input['agent_id'];
        $validator = \Validator::make($input, [
            'agent_id' => 'required|exists:agent,id',
        ], [], [
            'agent_id'=>'经纪人id',
        ]);

        if($validator->fails()){
            $warnings = $validator->messages();
            $show_warning = $warnings->first();
            return ['message'=>$show_warning ,'status'=>false];
        }
        $total_score=AgentScoreLog::where('agent_id',$agent_id)
            ->orderBy('id','desc')
            ->select('score')
            ->first();

        $score_log=AgentScoreLog::where('agent_id',$agent_id)
            ->where('operation',1)
            ->groupBy('type')
            ->get();

        $data['total_score']=is_object($total_score)?$total_score->score:'0';//当前总积分

        $data['task_complete']=[
            'complete_info'=>$score_log->where('type','17')->count()?AgentScoreLog::$TYPES_SCORE['17']:0, //完成个人信息
            'verified'=>$score_log->where('type','18')->count()?AgentScoreLog::$TYPES_SCORE['18']:0,  //实名认证
            'brand_agent'=>$score_log->where('type','15')->count()?AgentScoreLog::$TYPES_SCORE['15']:0,  //代理品牌
            'invite_user'=>$score_log->where('type','12')->count()?AgentScoreLog::$TYPES_SCORE['12']:0,  //邀请投资人
            'develop_team'=>$score_log->where('type','13')->count()?AgentScoreLog::$TYPES_SCORE['13']:0, //发展团队
            'ovo_apply'=>$score_log->where('type','2')->count()?AgentScoreLog::$TYPES_SCORE['2']:0, //OVO活动报名
            'consult'=>$score_log->where('type','11')->count()?AgentScoreLog::$TYPES_SCORE['11']:0,  //接受派单咨询
            'activity_invitation'=>$score_log->where('type','4')->count()?AgentScoreLog::$TYPES_SCORE['4']:0,  //发送活动邀请函
            'inspect_invitation'=>$score_log->where('type','5')->count()?AgentScoreLog::$TYPES_SCORE['5']:0,  //发送考察邀请函
            'contract'=>$score_log->where('type','6')->count()?AgentScoreLog::$TYPES_SCORE['6']:0,  //发送加盟合同
        ];
        
        return ['message'=>$data, 'status'=>true];
    }



    /**
     * 经纪人红包列表
     *
     * @param $input
     * @return array
     * @author tangjb
     */
    public function postPackageList($input)
    {
        $agent_id = array_get($input, 'agent_id');
        $type = array_get($input, 'type', 0);
        $page = array_get($input, 'page', 1);
        $pageSize = array_get($input, 'pageSize', 10);

        $validator = \Validator::make($input, [
            'agent_id' => 'required|exists:agent,id',
        ], [], ['agent_id'=>'经纪人id']);

        if ($validator->fails()) {
            $warnings = $validator->messages()->all();
            return ['message' => $warnings[0], 'status' => false];
        }


        $lists = AgentCustomer::with('user', 'contract.achievementLog')->where('agent_id', $agent_id)
            ->where(function ($query) use ($type) {
                if ($type) {
                    $query->where('contract_id', '>', 0);
                } else {
                    $query->where('contract_id', 0);
                }
            })
            ->orderBy('created_at', 'desc')
            ->whereIn('source', ['1', '6', '7'])
            ->forPage($page, $pageSize)
            ->get();


        $data = [];
        foreach ($lists as $k => $v) {
            //被邀请的投资人
            $data[$k]['uid'] = $v->uid;
            $data[$k]['customer_name'] = $v->user->realname ? $v->user->realname : $v->user->nickname;
            $data[$k]['username'] = getRealTel($v->user->non_reversible, 'wjsq');
            //入账时间
            $data[$k]['in_time'] = date('Y.m.d H:i:s', $v->created_at->timestamp);

            if ($type) {
                //激活时间
                $data[$k]['active_time'] = date('Y.m.d H:i:s', $v->contract->created_at->timestamp);
                //加盟品牌
                $data[$k]['brand_name'] = $v->contract->brand->name;
                //加盟金额
                $data[$k]['amount'] = $v->contract->amount;
                //促单经纪人
                $data[$k]['follow_agent'] = $v->contract->agent->realname;
                //邀请经纪人
                $data[$k]['invite_agent'] = Agent::find($agent_id)->realname;
                $data[$k]['commission_type'] = 6;
                $data[$k]['commission_id'] = $v->contract->id;
            }
        }

        return ['message' => $data, 'status' => true];
    }

}