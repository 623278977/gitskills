<?php

namespace App\Services;


use DB;
use App\Http\Libs\Weixin\Lib\WxPayUnifiedOrder;
use App\Http\Libs\Weixin\Lib\WxPayApi;
use App\Http\Libs\Weixin\Lib\WxPayDataBase;
use App\Http\Libs\Weixin\Lib\WxPayOrderQuery;
class OrderService
{

    /**
     * 阿里签名
     */
    public function aliSign($out_trade_no, $subject, $total_fee, $body)
    {
        $ali = [
            'partner'        => '2088801170381412',//
            'seller_id'      => '461839223@qq.com',//支付宝账号
            'out_trade_no'   => $out_trade_no,
            'subject'        => $subject,
            'body'           => $body,
            'total_fee'      => $total_fee,
            'notify_url'     => config('alipay.alipay.notifyurl'),
            'service'        => 'mobile.securitypay.pay',
            'payment_type'   => 1,//支付类型
            '_input_charset' => 'utf-8',
            'sign_type'      => 'RSA',
            'sign'           => '',
        ];
        $ali = paraFilter($ali);
        $ali = createLinkstring($ali);
        $sign = rsaSign($ali, config('alipay.alipay.private_key_path'));
        $str = $ali . '&sign=' . '"' . urlencode($sign) . '"' . '&sign_type=' . '"' . 'RSA' . '"';
        return $str;
    }


    /**
     * 微信签名
     */
    public function weixinsign($subject, $out_trade_no, $total_fee, $notify_url)
    {
        $input = new WxPayUnifiedOrder();
        $input->SetAppid(config('weixin.weixin.APPID'));
        $input->SetMch_id(config('weixin.weixin.MCHID'));
        $pay = new WxPayApi();
        $nonce_str = $pay->getNonceStr();
        $input->SetNonce_str($nonce_str);
        $input->SetBody($subject);
        $input->SetOut_trade_no($out_trade_no);
        $input->SetTotal_fee(floor($total_fee));
        $input->SetNotify_url($notify_url);
//        $input->SetOpenid($jsapi->GetOpenid());
        $input->SetTrade_type('APP');
//        print_r($input);exit;
        //统一下单
        $order = $pay->unifiedOrder($input);
        if (!isset($order['prepay_id'])) {
            return -1;
        }

        //执行第二次签名
        $wxpay = new WxPayDataBase();
        $wxpay->values['appid'] = config('weixin.weixin.APPID');
        $wxpay->values['partnerid'] = config('weixin.weixin.MCHID');
        $wxpay->values['prepayid'] = $order['prepay_id'];
        $wxpay->values['noncestr'] = $nonce_str;
        $wxpay->values['timestamp'] = time();
        $wxpay->values['package'] = "Sign=WXPay";

        $sign = $wxpay->MakeSign();
        $result['sign'] = $sign;
        $result['appid'] = config('weixin.weixin.APPID');
        $result['partnerid'] = config('weixin.weixin.MCHID');
        $result['prepayid'] = $order['prepay_id'];
        $result['packageValue'] = "Sign=WXPay";
        $result['noncestr'] = $nonce_str;
        $result['timestamp'] = time();
        $result['body'] = $subject;
        $result['out_trade_no'] = $out_trade_no;
        $result['spbill_create_ip'] = $_SERVER['REMOTE_ADDR'];

        return $result;
    }
}