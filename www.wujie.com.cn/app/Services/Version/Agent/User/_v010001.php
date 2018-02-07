<?php namespace App\Services\Version\Agent\User;

use App\Services\Version\VersionSelect;
use DB;
use Validator;
use App\Models\Agent\Agent;
use App\Models\Agent\AgentCategory;
use App\Models\Agent\AgentAchievementLog;


class _v010001 extends _v010000
{

    /**
     * 被邀请的情况下注册成经纪人  --数据中心版
     * @User shiqy
     * @param $input
     * @return array
     */
    public function postAgentRegister($input){
        $validator = \Validator::make($input,[
            'agent_id' => 'required|exists:agent,id,status,1',
            'username' => 'required',
            'type' => 'required',
        ]);
        if($validator->fails()){
            $warnings = $validator->messages();
            $show_warning = $warnings->first();
            return ['message'=>$show_warning ,'status'=>false];
        }
        $agentId = intval($input['agent_id']);
        $username = trim($input['username']);
        $code = trim($input['code']);
        $type = trim($input['type']);
        $nickname = empty($input['nickname'])? '' : trim($input['nickname']) ;
        $password = empty($input['password'])? '' : trim($input['password']) ;
        $input['phone_code'] = empty($input['phone_code'])? '86' : trim($input['phone_code']) ;
        $data = Agent::getAgentRegister($agentId,$username,$code,$type , $nickname,$password,$input['phone_code']);
        if(isset($data['error'])){
            return ['message'=>$data['message'],'status'=>false];
        }
        return ['message'=>$data ,'status'=>true];
    }

    /**
     * 被邀请的情况下注册成投资人   --数据中心版
     * @User yaokai
     * @param $input
     * @return array
     */
    public function postCustomerRegister($input){
        $agentId = intval($input['agent_id']);
        $username = trim($input['username']);
        $code = trim($input['code']);
        $type = trim($input['type']);
        $input['app_name'] = empty($input['app_name'])? 'wjsq' : trim($input['app_name']) ;
        $input['phone_code'] = empty($input['phone_code'])? '86' : trim($input['phone_code']) ;
        $nickname = empty($input['nickname'])? '':trim($input['nickname']);
        $data = Agent::getCustomerRegister($agentId,$username,$code,$type , $nickname,$input['app_name'],$input['phone_code']);
        if(isset($data['error'])){
            return ['message'=>$data['message'],'status'=>false];
        }
        return ['message'=>$data ,'status'=>true];
    }

    //编辑个人（经纪人）信息
    public function postEdit($input)
    {
        $data = parent::postEdit($input);
        if($data['status'] == false){
            return $data;
        }
        $data = [];
        $agentId = $input['agent_id'];
        isset($input['is_public_realname']) && $data['is_public_realname'] = trim($input['is_public_realname']);
        if(empty($input['nickname'])){
            return ['message' => "昵称不能为空", 'status' => false];
        }

        //判断昵称是否修改过
        $agentInfo = Agent::where('id',$agentId)->where('status',1)->first();
        if(empty($agentInfo['is_alter_nickname']) && $agentInfo['nickname'] != $input['nickname']){
            $data['is_alter_nickname'] = 1;
        }
        $data['nickname'] = trim($input['nickname']);
        Agent::where('id',$agentId)->update($data);

        return ['message' => "保存成功", 'status' => true];
    }

    //个人详情页（名片）
    public function postCard($input)
    {
        $agentId = intval($input['agent_id']);
        $validator = \Validator::make($input,[
            'agent_id' => 'exists:agent,id,status,1',
        ],[
            'exists'    => '邀请人无效',
        ]);
        if($validator->fails()){
            $warnings = $validator->messages();
            $show_warning = $warnings->first();
            return ['message'=>$show_warning ,'status'=>false];
        }
        $data = Agent::getAgentCard_v010001($agentId);
        return ['message' => $data, 'status' => true];
    }

    //获取指定表中指定字段
    public function postTableValue($input)
    {
        //获取所有的模型别名,生成字符串
        $modelAlias = config('model_alias');
        $keys = array_keys($modelAlias);
        $keyStr = implode(',',$keys);
        //对数组中每个元素进行去空格
        $input = arrayTrim($input);
        //验证
        $validator = \Validator::make($input,[
            'table' => "required|in:{$keyStr}",
            'where_field' => 'required',
            'where_value' => "required|exists:{$input['table']},{$input['where_field']}",
            'fields' => 'required',
        ]);
        if($validator->fails()){
            $warnings = $validator->messages();
            $show_warning = $warnings->first();
            return ['message'=>$show_warning ,'status'=>false];
        }
        $fieldArr = explode(',',$input['fields']);
        $field = array_filter($fieldArr);
        $field[] = 'non_reversible';
        $data = $modelAlias[$input['table']]::where($input['where_field'],$input['where_value'])->get($field)->toArray();
        if(empty($data)){
            return ['message'=>[] ,'status'=>true];
        }
        else if(count($data) == 1){
            $data[0]['username'] = getRealTel($data[0]['non_reversible'] , 'agent');
            return ['message'=>$data[0] ,'status'=>true];
        }
        else{
            return ['message'=>$data ,'status'=>true];
        }
    }

    /**
     * 业绩明细   --数据中心版
     * @User shiqy
     * @param $input
     * @return array
     */
    public function postSalesDetail($input)
    {
        $result = parent::postSalesDetail($input);
        if($result['status'] === false){
            return $result;
        }
        $data = $result['message'];
        //二级经纪人集合
        $twoAgentBox = [];
        $twoAgentIdBox = [];
        foreach ($data as &$oneOrder){
            //判断是不是自己成的单
            if($oneOrder['agent_id'] == $oneOrder['order_agent_id']){
                continue;
            }
            //获取该经纪人下左右的二级经纪人
            if(empty($twoAgentBox)){
                $allTwoAgents = Agent::where('register_invite',$oneOrder['agent_invite'])->orWhere('register_invite',$oneOrder['non_reversible'])
                    ->get(['id','realname'])->toArray();
                foreach ($allTwoAgents as $oneAgent){
                    $twoAgentBox[$oneAgent['id']] = $oneAgent;
                    $twoAgentIdBox[] = $oneAgent['id'];
                }
            }
            //判断是不是二级经纪人
            if(in_array($oneOrder['order_agent_id'],$twoAgentIdBox)){
                continue;
            }

            //获取该成单经纪人所有的上级集合
            $logs = AgentAchievementLog::with('achievement_agent')->where('contract_id',$oneOrder['contract_id'])
                ->get()->toArray();
            $upAgents = [];
            foreach ($logs as $oneLog){
                $upAgents[] = $oneLog['achievement_agent']['agent_id'];
            }
            $theTwoAgent = array_values(array_intersect($twoAgentIdBox,$upAgents));
            if(empty($theTwoAgent)){
                $oneOrder['agent'] = '';
            }
            $oneOrder['agent'] = $twoAgentBox[$theTwoAgent[0]]['realname'].'团队';

        }
        return ['message' => $data, 'status' => true];
    }
}