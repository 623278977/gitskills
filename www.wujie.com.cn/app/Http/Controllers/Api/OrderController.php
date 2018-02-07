<?php
/****订单控制器********/

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\CommonController;
use App\Http\Requests\Order\CheckRequest;
use App\Http\Requests\Order\OrderSignRequest;
use App\Http\Requests\Order\SignRequest;
use App\Models\Ad;
use App\Models\Brand\Goods;
use App\Models\Order\Entity;
use App\Models\ScoreLog;
use App\Models\User\Entity as User;
use App\Models\Vip\Entity as Vip;
use App\Models\Orders\Items;
use App\Models\User\Ticket;
use App\Models\Vip\Entity as VipEntity;
use Illuminate\Http\Request;
use App\Http\Libs\Alipay\AlipayNotify;
use App\Http\Libs\Weixin\Lib\PayNotifyCallBack;
use App\Http\Libs\Helper_Huanxin;
use App\Http\Libs\Weixin\Lib\WxPayUnifiedOrder;
use App\Http\Libs\Weixin\Lib\WxPayApi;
use App\Http\Libs\Weixin\Lib\WxPayDataBase;
use App\Http\Libs\Weixin\Lib\WxPayOrderQuery;
use Log;
use \Mail;
use \DB;
use App\Http\Libs\Alipay\AopClient;
use App\Http\Libs\Alipay\AlipayTradeQueryRequest;
use App\Models\Orders\Entity as Orders;
use App\Models\Vip\User as UserVip;
use App\Models\Vip\Term as VipTerm;
use App\Models\Message;
use App\Http\Requests\Order\VerifyOrderRequest;
use App\Models\Activity\Entity as Activity;
use App\Http\Libs\Unionpay\sdk\AcpService;
class OrderController extends CommonController
{
    /**
     * @param Request $request
     * 获取某个订单还有多久未支付
     */
    public function postDeadline(Request $request)
    {
        $ticket_no = $request->input('ticket_no');
        if (empty($ticket_no)) {
            return AjaxCallbackMessage('参数有误', false);
        }
        $user_ticket = Ticket::getRow(array('ticket_no' => $ticket_no));
        if (!isset($user_ticket->order_id) || !$user_ticket->order_id) {
            return AjaxCallbackMessage('订单不存在', false);
        }
        $order = Entity::getRow(array('id' => $user_ticket->order_id));
        if (!$order->id) {
            return AjaxCallbackMessage('订单不存在', false);
        }
        $data = array();
        $data['order_no'] = $order->order_no;
        $data['product'] = $order->product;
        $data['body'] = $order->body;
        $data['order_status'] = $order->status;
        $data['order_lefttime'] = $order->deadline - time();

        return AjaxCallbackMessage($data, true);
    }

    /**
     * 执行签名
     */
    public function postPay(Request $request)
    {
        $data = $request->input();
        if (!isset($data['order_no']) || !isset($data['pay_way'])) {
            return AjaxCallbackMessage('订单号和支付方式是必传参数', false);
        }
        if (!in_array($data['pay_way'], array('ali', 'weixin'))) {
            return AjaxCallbackMessage('支付方式只能为ali或微信', false);
        }

        //如果传递过来的order_no，带有video_id字样，就去orders表格里面去查
        if (strstr($data['order_no'], 'video_id')) {
            $str = Orders::sign(substr($data['order_no'], 8), $data['pay_way']);
            if ($str == -2) {
                return AjaxCallbackMessage('支付方式只能为ali或微信', false);
            } elseif ($str == -3) {
                return AjaxCallbackMessage('不存在该订单', false);
            } elseif ($str == -4) {
                return AjaxCallbackMessage('获取prepay_id失败', false);
            }
            return AjaxCallbackMessage($str, true);
        } else {
            $order = Entity::getRow(['order_no' => $data['order_no']]);
        }

        if (!is_object($order)) {
            return AjaxCallbackMessage('不存在该订单', false);
        }
        $rate = config('system.score_rate');


        if ($order->online_money * $rate != (string)($order->cost * $rate - (string)($order->score_num))) {
            return AjaxCallbackMessage('在线支付金额不合法', false);
        }


        //如果全部为积分支付，就暂时让现金支付为0.01
        $order->online_money == 0 && $order->online_money = 0.01;

        if ($data['pay_way'] == 'ali') {
            DB::table('order')->where('order_no', $order->order_no)->update(['pay_way' => 'ali']);
//            $str = $this->aliSign($order->order_no, $order->product, 0.01, $order->body);
            $str = $this->aliSign($order->order_no, $order->product, $order->online_money, $order->body);
        } else {
            DB::table('order')->where('order_no', $data['order_no'])->update(['pay_way' => 'weixin']);
//            $str = $this->weixinsign($order->product, $order->order_no, 1, config('weixin.weixin.NOTIFYURL'));
            $str = $this->weixinsign($order->product, $order->order_no, ($order->online_money) * 100, config('weixin.weixin.NOTIFYURL'));
            if (-1 === $str) {
                return AjaxCallbackMessage('获取prepay_id失败', false);
            }
        }

        return AjaxCallbackMessage($str, true, '', 0);
    }

    /**
     * 阿里签名
     */
    private function aliSign($out_trade_no, $subject, $total_fee, $body)
    {
        $ali = [
            'partner' => '2088801170381412',//
            'seller_id' => '461839223@qq.com',//支付宝账号
            'out_trade_no' => $out_trade_no,
            'subject' => $subject,
            'body' => $body,
            'total_fee' => $total_fee,
            'notify_url' => config('alipay.alipay.notifyurl'),
            'service' => 'mobile.securitypay.pay',
            'payment_type' => 1,//支付类型
            '_input_charset' => 'utf-8',
            'sign_type' => 'RSA',
            'sign' => '',
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
    private function weixinsign($subject, $out_trade_no, $total_fee, $notify_url)
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

    /**
     * 去第三方库查询该笔订单的支付结果
     */
    public function postThirdResult(Request $request)
    {
        $data = $request->input();
        if (!isset($data['order_no'])) {
            return AjaxCallbackMessage('订单是order_no是必需的', false);
        }

        $order = DB::table('orders')->where('order_no', $data['order_no'])->first();

        if (!in_array($order->pay_way, ['ali', 'weixin'])) {
            return AjaxCallbackMessage('无法查询，该笔订单的支付方式既不是支付宝也不是微信', false);
        }

        if ($order->pay_way == 'weixin') {
            $pay = new WxPayApi();
            $query = new WxPayOrderQuery();
            $query->SetOut_trade_no($order->order_no);
            $result = $pay->orderQuery($query);
            if ($result['trade_state'] == 'SUCCESS') {
                DB::table('order')->where('order_no', $data['order_no'])->update(['status' => 1, 'third_no' => $result['transaction_id']]);
                DB::table('user_ticket')->where('order_id', $order->id)->where('uid', $order->uid)->update(['status' => 1]);
            }
            if (isset($result['trade_state'])) {
                return AjaxCallbackMessage($result['trade_state'], true);
            } else {
                return AjaxCallbackMessage('查询失败', false);
            }
        } else {
            //构造请求参数
            $parameter = array(
                'service' => 'single_trade_query',
                'partner' => config('alipay.alipay.partner'),
                '_input_charset' => strtolower('utf-8'),
                'out_trade_no' => $data['order_no'],
            );
            ksort($parameter);
            reset($parameter);
            $param = '';
            $sign = '';
            foreach ($parameter as $key => $val) {
                $param .= "$key=" . urlencode($val) . "&";
                $sign .= "$key=$val&";
            }
            $param = substr($param, 0, -1);
            $sign = substr($sign, 0, -1) . config('alipay.alipay.key');
            $url = 'https://mapi.alipay.com/gateway.do?' . $param . '&sign=' . md5($sign) . '&sign_type=MD5';
            $result = file_get_contents($url);
            $result = $this->FromXml($result);
            if (isset($result['response']['trade']['trade_status'])) {
                return AjaxCallbackMessage($result['response']['trade']['trade_status'], true);
            } else {
                return AjaxCallbackMessage('查询失败', false);
            }
        }
    }

    /**
     * 将xml转为array
     *
     * @param string $xml
     * @throws WxPayException
     */
    private function FromXml($xml)
    {
        //将XML转为array
        //禁止引用外部xml实体
        libxml_disable_entity_loader(true);
        $this->values = json_decode(json_encode(simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA)), true);

        return $this->values;
    }

    /**
     * 获取订单详情
     */
    public function detail(Request $request)
    {
        $data = $request->input();
        $list = Entity::where('id', $data['id'])->first();

        return AjaxCallbackMessage($list, true);
    }

    /**
     * 下单并签名
     */
    public function postOrderAndSign(OrderSignRequest $request, $version = null)
    {
        $data = $request->input();
        $mobile = $request->get('mobile', '');
        $realname = $request->get('realname', '');
        $address = $request->get('address', '');
        $zone_id = $request->get('zone_id', 0);
        $data['score_num'] = $request->get('score_num', 0);
        if ($mobile && !checkMobileBlur(trim($data['mobile']))) {
            return AjaxCallbackMessage('手机号格式不对', false, '');
        }
        $rate = config('system.score_rate');

        //检查积分是否合法
        $check = Entity::checkScore($data['score_num'], $data['uid'], $rate);
        $arr = ['-1' => '不是正整数', '-2' => '积分大于会员拥有的积分', '-3' => '积分应该为汇率的百分之一的倍数'];
        if ($check != 1) {
            return AjaxCallbackMessage($arr[$check], false);
        }


        //检查购买项中是否含有已经禁用的专版
        foreach ($data['items'] as $k => $v) {
            if ($v['type'] == 'vip') {
                $vip = Vip::getRowByTerm($v['product_id']);
                if ($vip->status == 'disable') {
                    return AjaxCallbackMessage("{$vip->name}已被禁止购买", false);
                }
            }
        }

        $buy_type = $this->getBuyType($data);
        if (!$buy_type) {
            return AjaxCallbackMessage('该商品库存出现问题', false);
        }

        $order_type = $buy_type['order_type'];
        $order_type_des = $buy_type['order_type_des'];

        if ($version) {
            $versionService = $this->init(__METHOD__, $version);
            $result = $versionService->bootstrap($data, ['mobile' => $mobile, 'realname' => $realname,
                'address' => $address, 'zone_id' => $zone_id,
                'order_type' => $order_type, 'order_type_des' => $order_type_des
            ]);
            return AjaxCallbackMessage($result['message'], $result['status'], '', 0);
        }

        //下单
        $orders = Orders::place(
            $data['uid'],
            $data['amount'],
            $data['items'],
            $data['pay_way'],
            $data['score_num'],
            ($data['score_num'] / $rate),
            ($data['amount'] - ($data['score_num'] / $rate)),
            'npay',
            $mobile,
            $realname,
            $zone_id,
            $address
        );
        //减去积分
        $data['score_num'] > 0 && ScoreLog::add($data['uid'], $data['score_num'], $order_type, $order_type_des, -1, false, 'orders', $orders->id);
        //签名
        $str = Orders::sign($orders->order_no, $data['pay_way']);
        if ($str == -2) {
            return AjaxCallbackMessage('支付方式只能为ali或微信', false);
        } elseif ($str == -3) {
            return AjaxCallbackMessage('不存在该订单', false);
        } elseif ($str == -4) {
            return AjaxCallbackMessage('获取prepay_id失败', false);
        }

        return AjaxCallbackMessage(['str' => $str, 'order_no' => $orders->order_no, '', 0], true);
    }


    private function getBuyType($data)
    {
        if ($data['items'][0]['type'] == 'brand') {
            $order_type = 'brand_goods_buy';
            $order_type_des = '品牌商品购买使用积分';
            //物品减1
            $reduce = Goods::reduceNum(1, $data['items'][0]['product_id']);
            if (!$reduce) {
                return false;
            }
        } elseif ($data['items'][0]['type'] == 'vip') {
            $order_type = 'vip_term_buy';
            $order_type_des = '专版会员购买使用积分';
        } elseif ($data['items'][0]['type'] == 'video_reward') {
            $order_type = 'video_reward';
            $order_type_des = '视频打赏使用积分';
        } elseif ($data['items'][0]['type'] == 'live_reward') {
            $order_type = 'live_reward';
            $order_type_des = '直播打赏使用积分';
        } elseif ($data['items'][0]['type'] == 'video') {
            $order_type = 'video_buy';
            $order_type_des = '视频购买使用积分';
        } elseif ($data['items'][0]['type'] == 'brand_goods') {
            $order_type = 'brand_join';
            $order_type_des = '品牌商品加盟';
        } elseif ($data['items'][0]['type'] == 'news') {
            $order_type = 'news_buy';
            $order_type_des = '资讯购买';
        } else {
            $order_type = 'other';
            $order_type_des = '其他';
        }


        return ['order_type' => $order_type, 'order_type_des' => $order_type_des];
    }


    /**
     *签名
     */
    public function postSign(SignRequest $request)
    {
        $data = $request->input();
        $str = Orders::sign($data['order_no'], $data['pay_way']);

        if ($str == -1) {
            return AjaxCallbackMessage('订单号和支付方式是必传参数', false);
        } elseif ($str == -2) {
            return AjaxCallbackMessage('支付方式只能为ali或微信', false);
        } elseif ($str == -3) {
            return AjaxCallbackMessage('不存在该订单', false);
        } elseif ($str == -4) {
            return AjaxCallbackMessage('获取prepay_id失败', false);
        }

        return AjaxCallbackMessage($str, true);
    }

    /**
     *去第三方库检查 支付结果
     */
    public function postCheck(CheckRequest $request)
    {
        $data = $request->input();
        $check = Orders::check($data['order_no']);

        if ($check == -1) {
            return AjaxCallbackMessage('无法查询，该笔订单的支付方式既不是支付宝也不是微信', false);
        } elseif ($check == -2) {
            return AjaxCallbackMessage('支付失败', false);
        } else {
//            $this->afterPay($data['order_no'], 'pay');
//            Entity::afterPay($data['order_no'], 'pay');

            return AjaxCallbackMessage('支付成功', true);
        }
    }


    /**
     * 支付宝通知回调地址
     */
    public function postAlinotify(Request $request)
    {

        $data = $request->input();
        $config = config('alipay.alipay');
        $alipayNotify = new AlipayNotify($config);
        //验证通知是否来自支付宝。
        $verify_result = $alipayNotify->verifyNotify($data);


        if ($verify_result) {
            //获取支付宝的通知返回参数，可参考技术文档中服务器异步通知参数列表
            //商户订单号
            $out_trade_no = $data['out_trade_no'];
            //判断是不是继续支付
            if (strpos($out_trade_no, '_')) {
                $out_trade_no = substr($out_trade_no, 0, strpos($out_trade_no, '_'));
            }
            //支付宝交易号
            $trade_no = $data['trade_no'];

            //交易状态
            $trade_status = $data['trade_status'];

            //卖家id
            $seller_id = $data['seller_id'];

            //交易总价
            $total_fee = $data['total_fee'];
            $order = Entity::getRow(['order_no' => $out_trade_no]);
            if ($seller_id != $config['partner']) {
                echo "fail";
                return false;
            }
            if ($data['trade_status'] == 'TRADE_FINISHED') {
                //老，使用order表
                if (is_object($order)) {
                    //有数据就证明是order表
                    Activity::activityAfterPay($order, 'ali-' . $trade_no, 2);
                } else {
                    //新 启用orders表
                    Entity::afterPay($out_trade_no, 'pay', 'ali-' . $trade_no, $data['buyer_id']);
                }
            } else {
                if ($data['trade_status'] == 'TRADE_SUCCESS') {
                    //老，使用order表
                    if (is_object($order)) {
                        //有数据就证明是order表
                        Activity::activityAfterPay($order, 'ali-' . $trade_no, 1);
                    } else {
                        //新 启用orders表
                        Entity::afterPay($out_trade_no, 'pay', 'ali-' . $trade_no, $data['buyer_id']);
                    }
                }
            }
            echo "success";
        } else {
            echo "fail";
        }
    }

    /**
     *微信支付通知回调地址
     */
    public function postWeixinnotify(Request $request)
    {
//        $xml = isset($GLOBALS['HTTP_RAW_POST_DATA']) ? $GLOBALS['HTTP_RAW_POST_DATA'] : file_get_contents("php://input");
        $notify = new PayNotifyCallBack();
        $result = $notify->Handle();
    }


    /**
     * 银联支付 通知回调地址
     *
     * @param Request $request
     * @author tangjb
     */
    public function postUnionpaynotify(Request $request)
    {
        $data = $request->input();
        
        //验签
        $res = AcpService::validate($data);

        if(!$res){
            return false;
        }

        if($data['respCode']!='00'){
            return false;
        }

        //判断是不是继续支付
        if(strlen($data['orderId'])>16){
            $data['orderId'] = substr($data['orderId'], 0, -5);
        }

        if(!isset($data['accNo'])){
            $data['accNo'] = '';
        }


        $order = Entity::getRow(['order_no' => $data['orderId']]);
        if (is_object($order)) {
            //有数据就证明是order表
            Activity::activityAfterPay($order, 'unionpay-' . $data['queryId'], 1);
        } else {
            //新 启用orders表
            Entity::afterPay($data['orderId'], 'pay', 'unionpay-' . $data['queryId'], $data['accNo']);
        }
    }


    /**
     * 在自身数据库中查询
     */
    public function postVerify(VerifyOrderRequest $request)
    {
        $data = $request->input();
        $orders = Orders::getRow(['order_no' => $data['order_no']]);
        if ($this->uid != $orders->uid) {
            return AjaxCallbackMessage('你没权限查询该笔订单', false);
        }

        $items = Items::getByNo($data['order_no']);


        //将items转化成实体
        $entities = $this->toEntity($items);
        if ($orders->status != 'pay') {
            $err = '未支付或支付失败';
            $result = 0;
            return AjaxCallbackMessage(compact("err", "result", "entities"), true);
        } else {
            $result = 1;
            return AjaxCallbackMessage(compact("result", "entities"), true);
        }
    }


    //将items转化成实体
    private function toEntity($items)
    {
        $entities = [];
        foreach ($items as $k => $v) {
            $entity = Items::toEntity($v->type, $v->product_id, $v->id);
            $entities[] = $entity;
        }

        return $entities;
    }

    /**
     * 继续支付
     */
    public function postContinuePay(Request $request, $version = null)
    {
        $versionService = $this->init(__METHOD__, $version);

        if ($versionService) {
            $response = $versionService->bootstrap($request->all());

            return AjaxCallbackMessage($response['message'], $response['status'], '', 0);
        }

        return AjaxCallbackMessage('api接口不再维护', false);
    }

    /**
     * 下单并支付（积分）
     */
    public function postOrderAndPay(Request $request, $version = null)
    {
        $data = $request->input();
        $buy_type = $this->getBuyType($data);
        if (!$buy_type) {
            return AjaxCallbackMessage('该商品库存出现问题', false);
        }

        $order_type = $buy_type['order_type'];
        $order_type_des = $buy_type['order_type_des'];

        $versionService = $this->init(__METHOD__, $version);

        if ($versionService) {
            $response = $versionService->bootstrap($request->all(), ['order_type' => $order_type,
                'order_type_des' => $order_type_des]);

            return AjaxCallbackMessage($response['message'], $response['status'], '', 0);
        }

        return AjaxCallbackMessage('api接口不再维护', false);
    }



    /**
     * 混合支付 合同下单
     */
    public function postMixPay(Request $request, $version = null)
    {
        $versionService = $this->init(__METHOD__, $version);

        if ($versionService) {
            $response = $versionService->bootstrap($request->all());

            return AjaxCallbackMessage($response['message'], $response['status'], '', 0);
        }

        return AjaxCallbackMessage('api接口不再维护', false);
    }


}