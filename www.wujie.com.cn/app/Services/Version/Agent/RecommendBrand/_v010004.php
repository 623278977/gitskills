<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017-11-16
 * Time: 18:00
 */

namespace App\Services\Version\Agent\RecommendBrand;


use App\Models\Brand\Enter;
use App\Services\Version\VersionSelect;

class _v010004 extends VersionSelect
{
    public function postCommit($input)
    {
        $validator_result =\Validator::make($input['request']->input(),
            Enter::$_RULES, [
            'required' => ':attribute为必填项',
        ], Enter::$_MESSAGES);

        if ($validator_result->fails()) {
            return ['message' => $validator_result->messages(), 'status' => false];
        }
        $data = $input['request']->input();
        $en_tel = encryptTel($data['mobile']);
        $data=array_merge($input['request']->input(),['from'=>'agent', 'non_reversible'=>$en_tel, 'mobile'=>pseudoTel($data['mobile'])]);
        $desposit = depositTel($data['mobile'], 'agent');

        $insert=Enter::create($data);
        if(!$insert){
            return ['message'=>'提交失败', 'status'=>false];
        }
        return ['message'=>'提交成功', 'status'=>true];
    }


    public function postRecord($input ,$page = 1, $pageSize = 10)
    {
        $id=$input['agent_id'];
        if(empty($id)){
            return ['message' => "请传递经纪人id",'status' => false];
        }
        $lists=Enter::where('uid', $id)
            ->where('from', 'agent')
            ->orderBy('created_at', 'desc')
            ->skip(($page-1)*$pageSize)
            ->take($pageSize)
            ->paginate($pageSize)->toArray();
        $result=[];
        foreach ($lists['data'] as $value){
            $result[]=[
                'id'=>$value['id'],
                'brand_name'=>$value['brand_name'],
                'category'=>Enter::getCategory($value['categorys1_id']),
                'realname'=>$value['realname'],
                'duties'=>$value['duties'],
                'mobile'=>$value['mobile'],
                'introduce' => $value['introduce'],
                'commit_time'=>date('Y-m-d H:i:s',$value['created_at']),
            ];
        }
        return ['message'=>$result, 'status'=> true];
    }
}