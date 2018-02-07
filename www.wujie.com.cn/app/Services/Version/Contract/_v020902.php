<?php namespace App\Services\Version\Contract;

use App\Models\Contract\Contract;
use App\Models\Contract\ContractSuccessCertify;
use App\Models\Orders\Items;

class _v020902 extends _v020800
{
    /**
     * author zhaoyf
     *
     * 根据合同id获取合同信息
     *
     * @param array $input
     *
     * @return array
     */
    public function postDetail($input = [])
    {
        $contract_id = $input['contract_id'];
        $uid = $input['uid'];

        //相关合同信息
        $data = Contract::ContractDetails('', '', $uid,'', $contract_id);


        if($data){
            return ['message' => $data, 'status' => true];
        }else{
            return ['message' => '合同不存在', 'status' => false];
        }

    }


    /**
     * 合同支付完成凭证上传
     *
     * @param array $input
     * @return array
     * @author tangjb
     */
    public function postSuccess($input = [])
    {
        $validator_result = \Validator::make($input, [
            'uid' => 'required|integer|exists:user,uid',
            'contract_id'  => 'required|integer|exists:contract,id',
            'image' => 'required',
        ],[
            'required' => ':attribute为必填项',
            'integer' => ':attribute必须为整数',
        ], [
            'uid' => '当前登录用户ID',
            'contract_id'  => '合同ID',
        ]);

        if ($validator_result->fails()) {
            return ['message' => $validator_result->messages(), 'status' => false];
        }

        $uid = $input['uid'];

        $remark = array_get($input, 'remark', '');

        $item = Items::where('type', 'contract')->where('product_id', $input['contract_id'])->whereIn('order_id', function($query) use($uid){
            $query->from('orders')->where('uid', $uid)->lists('id');
        })->first();

        $res = ContractSuccessCertify::create([
            'order_id' =>$item->order_id,
            'contract_id' => $input['contract_id'],
            'image' => $input['image'],
            'remark' => $remark,
            'uid' => $uid,
        ]);

        if($res){
            return ['message' =>'上传成功' , 'status' => true];
        }else{
            return ['message' =>'上传失败' , 'status' => false];
        }
    }
}