<?php
namespace App\Http\Libs\Weixin\Lib;
use \DB;
use App\Models\Order\Entity;
use App\Models\Activity\Entity as Activity;
class PayNotifyCallBack extends WxPayNotify
{
    //查询订单
    public function Queryorder($transaction_id)
    {
        $input = new WxPayOrderQuery();
        $input->SetTransaction_id($transaction_id);
        $result = WxPayApi::orderQuery($input);
        if(array_key_exists("return_code", $result) && array_key_exists("result_code", $result) && $result["return_code"] == "SUCCESS" && $result["result_code"] == "SUCCESS")
        {
            return $result;
        }
        return false;
    }

    //重写回调处理函数 这里是已经重写好了的
    public function NotifyProcess($data, &$msg)
    {
        if(!array_key_exists("transaction_id", $data)){
            $msg = "输入参数不正确";
            return false;
        }
        //查询订单，判断订单真实性
        if(!$this->Queryorder($data["transaction_id"])){
            $msg = "订单查询失败";
            return false;
        }
        $payResult = new WxPayResults();
        $payResult->values = $data;
        //验签
        if(!$payResult->CheckSign()){
            return false;
        }

        $result = $this->Queryorder($data["transaction_id"]);
        if(isset($result['trade_state']) && $result['trade_state']=='SUCCESS'){
            //判断是不是继续支付
            if(strpos($result['out_trade_no'], '_')){
                $result['out_trade_no'] = substr($result['out_trade_no'],0, strpos($result['out_trade_no'], '_'));
            }
            $order = Entity::getRow(['order_no'=>$result['out_trade_no']]);


            if(is_object($order)){
                //有数据就证明是order表
                Activity::activityAfterPay($order,'weixin-' . $data['transaction_id']);
            }
            
            //新 启用orders表
            Entity::afterPay($result['out_trade_no'], 'pay', 'weixin-' . $data['transaction_id'], $data['openid']);
        }
        return true;
    }

}