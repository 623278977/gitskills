<?php
/****pos机控制器********/

namespace App\Http\Controllers\Api;

use App\Models\Agent\ContractPayLog;
use Illuminate\Http\Request;
use Log;
use \Mail;
use \DB;
use App\Models\Orders\Entity as Orders;

class PosController extends CommonController
{

    /*
    * 查询订单
    */
    public function postOrder(Request $request)
    {
        $xml = isset($GLOBALS['HTTP_RAW_POST_DATA']) ? $GLOBALS['HTTP_RAW_POST_DATA'] : file_get_contents("php://input");

        $xml = json_decode($xml, true);

//        $xml = $request->input();
        $key = config('pos.pos.key');
        if(!isset($xml['varidatecode'])){
//            return AjaxCallbackMessage('缺少参数varidatecode', false);
            $data['responsecode'] =  9;
            $data['remark'] =  '缺少参数varidatecode';
            return response($data);
//            return AjaxCallbackMessage($data, false);
        }

        if(!isset($xml['pos_no'])){
//            return AjaxCallbackMessage('缺少参数pos_no', false);
            $data['responsecode'] =  9;
            $data['remark'] =  '缺少参数pos_no';
            return response($data);
//            return AjaxCallbackMessage($data, false);
        }

//        59域扩展域  组成  62|00490004|时间戳格式(yyyyMMddHHmmss)|终端输入金额
        if(!isset($xml['data59'])){
//            return AjaxCallbackMessage('缺少参数data59', false);
            $data['responsecode'] =  9;
            $data['remark'] =  '缺少参数data59';
            return response($data);
//            return AjaxCallbackMessage($data, false);
        }

        $data59 = explode('|', $xml['data59']);

        if(empty($data59[3])){
//            return AjaxCallbackMessage('参数data59与约定不符合', false);
            $data['responsecode'] =  9;
            $data['remark'] =  '参数data59与约定不符合';
            return response($data);
//            return AjaxCallbackMessage($data, false);
        }


        $num = preg_replace('/^0*/', '', $data59[3]);


        $md5 = md5($xml['varidatecode'].$xml['pos_no'].$key);

        $data = [];

//      1成功2验证码失效，3内容被篡改，4秘钥不一致，5订单已支付，6非货到付款订单,7订单不存在9超额
        //验签不通过
        if($md5!==$xml['checkValue']){
            $data['responsecode'] =  4;
            return response($data);
//            return AjaxCallbackMessage($data, false);
        }

        $order = Orders::find($xml['varidatecode']);

        if(!$order){
            $data['responsecode'] =  7;
            return response($data);
//            return AjaxCallbackMessage($data, false);
        }

        if($order['status'] == 'pay'){
            $data['responsecode'] =  5;
            return response($data);
//            return AjaxCallbackMessage($data, false);
        }


        //判断是否超额
        $isExcess = ContractPayLog::isExcess($order, $num/100);

        if($isExcess){
            $data['responsecode'] =  9;
            $data['remark'] =  '超额';
            return response($data);
//            return AjaxCallbackMessage($data, false);
        }


        $data['responsecode'] =  1;

        //生成子订单  即使是第一次也生成子订单
        $log = ContractPayLog::create(
            [
                'contract_id' => $order->hasOneOrdersItems->belongsToContract->id,
                'type' => 2,
                'post_id' => 0,
                'num' => $num/100,
                'order_no' => ContractPayLog::produceNo(),
                'status' => 0,
                'order_id' => $order->id,
            ]
        );

        $data['orderid'] =  $log->order_no;
        $data['money'] =  $num;
//        MD5(orderid + money +key)
        $data['checkValue'] =  md5($xml['varidatecode'].$data['money'].$key);
        $data['remark'] =  ContractPayLog::getRemark($order, $num/100);

//        合同：123456（六位合同号）
//        姓名：张三
//        品牌：过路人饭团（根据最大字数从头截取）
//        金额：30000 （当要前支付的金额）


        return response($data);
//        return AjaxCallbackMessage($data, true);
    }


    /*
    * 接受通知
    */
    public function postNotify(Request $request)
    {
        $xml = isset($GLOBALS['HTTP_RAW_POST_DATA']) ? $GLOBALS['HTTP_RAW_POST_DATA'] : file_get_contents("php://input");
        $xml = json_decode($xml, true);
        $pubKey = file_get_contents(config('pos.pos.ys_public_key_path'));
        $res = openssl_get_publickey($pubKey);
        $data = $xml['ysOrderId'].$xml['orderId'].$xml['mercId'].$xml['logNo'].$xml['settleDate']
            .$xml['amount'].$xml['createTime'].$xml['payType'].$xml['tradeType'].$xml['cardNo'].$xml['cardType'];
        $result = (bool)openssl_verify($data, base64_decode($xml['signData']), $res);
        openssl_free_key($res);


        if(!$result){
            $res_data['code'] = '99';
            return response($res_data);
        }

        $res_data['code'] = '00';
        return response($res_data);

        //开始事务
        \DB::beginTransaction();
        try {
            //改变订单状态
            ContractPayLog::changeStatus($xml['orderId'], ($xml['amount'] / 100));
            \DB::commit();
        } catch (\Exception $e) {
            \DB::rollBack();
            throw new \Exception($e->getMessage(), 0, $e);
        }

        $res_data['code'] = '00';
        return response($res_data);
    }


}