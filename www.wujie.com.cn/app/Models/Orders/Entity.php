<?php
/**活动模型
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/5/5
 * Time: 11:10
 */

namespace App\Models\Orders;

use App\Models\Agent\Invitation;
use App\Models\ScoreLog;
use Illuminate\Database\Eloquent\Model;
use \DB;
use App\Models\Orders\Items as Items;
use App\Http\Libs\Weixin\Lib\WxPayUnifiedOrder;
use App\Http\Libs\Weixin\Lib\WxPayApi;
use App\Http\Libs\Weixin\Lib\WxPayDataBase;
use App\Http\Libs\Weixin\Lib\WxPayOrderQuery;
use App\Models\Activity\Entity as Activity;
use App\Models\Brand\Entity as Brand;
use App\Models\Vip\Entity as Vip;
use App\Models\Live\Entity as Live;
use App\Models\Brand\Goods as LiveGoods;
use App\Models\Brand\BrandGoods as BrandGoods;
use App\Models\Video\Entity as Video;
use App\Models\News\Entity as News;
use App\Models\Score\Goods\V020700 as ScoreGoods;
use App\Http\Libs\Unionpay\sdk\SDKConfig;
use App\Http\Libs\Unionpay\sdk\AcpService;
use App\Models\User\Entity as User;


class Entity extends Model
{

    protected $dateFormat = 'U';

    protected $table = 'orders';


    public static $_PAYWAY = [
        'ali' => '支付宝',
        'weixin' => '微信支付',
        'unionpay' => '银联支付',
        'score' => '积分支付',
        'red_packet' => '邀请红包抵扣',
    ];


    public function getPayWay()
    {
        if (isset(self::$_PAYWAY[$this->pay_way])) {
            return self::$_PAYWAY[$this->pay_way];
        } else {
            return '';
        }
    }

    //黑名单
    protected $guarded = [];

    //关联订单信息表
    public function orders_items()
    {
        return $this->hasOne(Items::class, 'order_id', 'id')
            ->whereIn('type', ['vip', 'video', 'brand', 'brand_goods', 'news', 'score']);
    }

    //关联订单认购表  应该是一对多关系，但是目前数据都是1对1关系
    public function hasOneOrdersItems()
    {
        return $this->hasOne(Items::class, 'order_id', 'id');
    }


    public function user()
    {
        return $this->hasOne(User::class, 'uid', 'uid');
    }


    static function getRow($where)
    {
        return self::where($where)->first();
    }

    static function getRows($where)
    {
        return self::where($where)->get();
    }

    static function updateOrderByField(Array $array, Array $field)
    {
        $result = self::where(array_keys($field)[0], array_values($field)[0])
            ->update($array);

        return $result;
    }

    /**
     * 下订单
     */
    static function place($uid, $amount, Array $items, $pay_way, $score_num, $score_money, $online_money, $status = 'npay', $mobile = '', $realname = '', $zone_id = 0, $address = '')
    {
        $order_no = chr(rand(97, 122)) . chr(rand(97, 122)) . chr(rand(97, 122)) . time() . chr(rand(97, 122)) . chr(rand(97, 122)) . chr(rand(97, 122));
        $place = self::create(
            [
                'uid' => $uid,
                'order_no' => $order_no,
                'pay_way' => $pay_way,
                'amount' => $amount,
                'status' => $status,
                'score_num' => $score_num,
                'score_money' => $score_money,
                'online_money' => $online_money,
                'mobile' => $mobile,
                'realname' => $realname,
                'zone_id' => $zone_id,
                'address' => $address
            ]
        );

        //加入认购项
        foreach ($items as $k => $v) {
            $v['price'] = abondonComma($v['price']);
            $item = Items::produce($place->id, $v);
            if (!$item) {
                return false;
            }
            if ($pay_way == 'score') {
                //积分日志  用户减积分这步逻辑已经有了
                ScoreLog::add($uid, $v['score_price'], $item['type'] . '_buy', $item['type'] . '购买使用积分', -1, false, 'ordrers_items', $item->id);
            }
        }

        return $place;
    }

    /**
     * 签名
     */
    static function sign($order_no, $pay_way, $is_continue = 0)
    {
        if (!isset($order_no) || !isset($pay_way)) {
            return -1;
        }
        if (!in_array($pay_way, array('ali', 'weixin', 'unionpay'))) {
            return -2;
        }

        $order = self::where('order_no', $order_no)->first();

        $items = DB::table('orders_items')->where('order_id', $order->id)->lists('product_id', 'type');
        $order->pay_way = $pay_way;
        $order->save();
        $items = self::titleAndDesc($items);

        if (!is_object($order)) {
            return -3;
        }

        //如果全部为积分支付，就认为的让现金支付为0.01
        $order->online_money == 0 && $order->online_money = 0.01;

        if ($pay_way == 'ali') {
            DB::table('orders')->where('order_no', $order_no)->update(['pay_way' => 'ali']);
//            $str = self::aliSign($order->order_no, $items['product'], 0.01, $items['body']);
            $str = self::aliSign($order->order_no, $items['product'], $order->online_money, $order->body);
        } elseif ($pay_way == 'weixin') {
            DB::table('orders')->where('order_no', $order_no)->update(['pay_way' => 'weixin']);
//            $str = self::weixinsign($items['product'], $order->order_no, 1, config('weixin.weixin.NOTIFYURL'));
            if ($is_continue) {
                $str = self::weixinsign($items['product'], $order->order_no . '_' . rand(10000, 99999), ($order->online_money) * 100, config('weixin.weixin.NOTIFYURL'));
            } else {
                $str = self::weixinsign($items['product'], $order->order_no, ($order->online_money) * 100, config('weixin.weixin.NOTIFYURL'));
            }

            if (-1 === $str) {
                return -4;
            }
        } else {
            DB::table('orders')->where('order_no', $order_no)->update(['pay_way' => 'unionpay']);
            $res = self::unionPaySign($order->order_no . rand(10000, 99999), ($order->online_money) * 100);
            $str = $res['res'];
        }

        return $str;
    }

    /**
     * 获取标题和描述供签名
     */
    public static function titleAndDesc(Array $array)
    {
        $product = $body = $item = '';

        foreach ($array as $k => $v) {
            if ($k == 'ticket') {
                $ticket = DB::table('activity_ticket')->where('id', $v)->first();
                $item = $ticket->intro;
            }

            if ($k == 'vip') {
                $vip_term = DB::table('vip_term')->where('id', $v)->first();
                $item = $vip_term->name;
            }

            if ($k == 'video_reward') {
                $item = '视频打赏';
            }

            if ($k == 'live_reward') {
                $item = '直播打赏';
            }

            if ($k == 'video') {
                $video = DB::table('video')->where('id', $v)->first();
                $item = $video->subject;
            }

            if ($k == 'brand') {
                $goods = DB::table('live_brand_goods')->where('id', $v)->first();
                $item = $goods->title;
            }

            if ($k == 'brand_goods') {
                $goods = DB::table('brand_goods')->where('id', $v)->first();
                $item = $goods->title;
            }

            if ($k == 'score') {
                $goods = DB::table('score_goods')->where('id', $v)->first();
                $item = $goods->subject;
            }


            //邀请函
            if ($k == 'inspect_invite') {
                $item = Invitation::with('hasOneStore')->where('id', $v)->first()->getDescription();
            }


            //合同
            if ($k == 'contract') {
                $item = DB::table('contract')->where('id', $v)->first();
                $item = $item->name;
            }


            //todo


            if (count($array) == 1) {
                $product = $item;
            } else {
                $product = $item . '等';
            }
            $body .= $item . '；';
        }

        $body = mb_substr($body, 0, 510, 'utf-8');


        return ['product' => $product, 'body' => $body];
    }

    /**
     * 阿里签名
     */
    public static function aliSign($out_trade_no, $subject, $total_fee, $body)
    {
        if(\App::environment()!='production'){
            $total_fee = 0.01;
        }
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
    public static function weixinsign($subject, $out_trade_no, $total_fee, $notify_url)
    {
        if(\App::environment()!='production'){
            $total_fee = 1;
        }
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
        //判断是否含有_
        if (strpos($out_trade_no, '_')) {
            $out_trade_no = substr($out_trade_no, 0, strpos($out_trade_no, '_'));
        }
        $result['out_trade_no'] = $out_trade_no;
        $result['spbill_create_ip'] = $_SERVER['REMOTE_ADDR'];

        return $result;
    }

    public static function check($order_no)
    {
        $order = DB::table('orders')->where('order_no', $order_no)->first();
        if (!is_object($order)) {
            $order = DB::table('order')->where('order_no', $order_no)->first();
        }

        if (!in_array($order->pay_way, ['ali', 'weixin', 'unionpay'])) {
            return -1;
//            return AjaxCallbackMessage('无法查询，该笔订单的支付方式既不是支付宝也不是微信', false);
        }

        if ($order->pay_way == 'weixin') {
            $pay = new WxPayApi();
            $query = new WxPayOrderQuery();
            $query->SetOut_trade_no($order->order_no);
            $result = $pay->orderQuery($query);

            if (isset($result['trade_state']) && $result['trade_state'] == 'SUCCESS') {
                $third_no = 'weixin-' . $result['transaction_id'];
                Activity::activityAfterPay($order, $third_no);

                return 1;
//                return AjaxCallbackMessage($result['trade_state'], true);
            } else {
                return -2;
//                return AjaxCallbackMessage('查询失败', false);
            }
        } else {
            //构造请求参数
            $parameter = array(
                'service' => 'single_trade_query',
                'partner' => config('alipay.alipay.partner'),
                '_input_charset' => strtolower('utf-8'),
                'out_trade_no' => $order_no,
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
            $result = self::FromXml($result);
            if (isset($result['response']['trade']['trade_status']) && in_array(
                    $result['response']['trade']['trade_status'],
                    [
                        'WAIT_SELLER_SEND_GOODS',
                        'WAIT_BUYER_CONFIRM_GOODS',
                        'TRADE_FINISHED',
                        'WAIT_SYS_PAY_SELLER',
                        'TRADE_PENDING',
                        'TRADE_SUCCESS',
                        'BUYER_PRE_AUTH'
                    ]
                )
            ) {
                $third_no = 'ali-' . $result['response']['trade']['trade_no'];
                Activity::activityAfterPay($order, $third_no);

//                return AjaxCallbackMessage($result['response']['trade']['trade_status'], true);
                return 1;
            } else {
                return -2;
//                return AjaxCallbackMessage('查询失败', false);
            }
        }
    }

    /**
     * 将xml转为array
     *
     * @param string $xml
     * @throws WxPayException
     */
    public static function FromXml($xml)
    {
        //将XML转为array
        //禁止引用外部xml实体
        libxml_disable_entity_loader(true);
        $values = json_decode(json_encode(simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA)), true);

        return $values;
    }

    /*
     * 我的订单列表
     */
    static function myOrders(array $param, \Closure $callback = null)
    {
        $builder = self::join('orders_items as oi', 'oi.order_id', '=', 'orders.id')
            ->where('orders.uid', (int)$param['uid'])
            ->whereIn('oi.type', ['brand', 'brand_goods', 'video'])
//            ->where('orders.status','pay')
            ->select(
                'orders.id',
                'orders.order_no',
                'orders.status',
                'oi.type',
                'orders.online_money as amount',
                'orders.created_at',
                'orders.status',
                'oi.product_id',
                'oi.status as oi_status',
                'oi.id as oi_id',
                'orders.pay_way',
                'orders.pay_at',
                'oi.type as oi_type',
                DB::raw('(select price from lab_orders_items  as a WHERE a.order_id = lab_orders.id) as price')
            )
            ->orderBy('oi.created_at', 'desc');
        if ($callback) {
            return $callback($builder);
        }
        $data = $builder->paginate(isset($param['page_size']) ? ((int)$param['page_size']) : 10);

        return $data;
    }

    /*
     * 未完成的订单
     */
    static function myOrdersIncomplte($param)
    {
        $data = self::join('orders_items as oi', 'oi.order_id', '=', 'orders.id')
            ->where('orders.uid', (int)$param['uid'])
            ->whereIn('oi.type', ['brand', 'brand_goods', 'video'])
            ->whereIn('orders.status', ['expire', 'npay'])
            ->select(
                'orders.id',
                'orders.order_no',
                'orders.status',
                'oi.type',
                'orders.online_money as amount',
                'orders.created_at',
                'orders.status',
                'oi.product_id',
                'oi.status as oi_status',
                'oi.id as oi_id',
                'orders.pay_way',
                'oi.type as oi_type',
                'orders.amount as price',
                DB::raw('(select price from lab_orders_items as a WHERE a.order_id = lab_orders.id) as price')
            )
            ->orderBy('oi.created_at', 'desc')
            ->get();

        return $data;
    }

    /*
     * 订单详情页
     */
    static function orderInfo(array $param)
    {
        $order_id = \DB::table('orders_items')->where('id', $param['oi_id'])->first()->order_id;
        $data = self::where('id', $order_id)
            ->where('uid', $param['uid'])
            ->select(
                'id',
                'order_no',
                'status',
                'pay_at',
                'score_num',
                'realname',
                'mobile',
                'online_money',
                'created_at',
                'score_money',
                'amount',
                //\DB::raw('(select product_id from lab_orders_items as oi WHERE oi.order_id = lab_orders.id) as product_id'),
                \DB::raw(
                    '(select group_concat(concat_ws("|",id,product_id,type)) from lab_orders_items as oi WHERE oi.order_id = lab_orders.id and oi.id = ' . $param['oi_id'] . ') as orders_items_data'
                )
            //\DB::raw('(select type from lab_orders_items as oi WHERE oi.order_id = lab_orders.id) as type')
            )
            ->first();

        return $data;
    }

    //关联的订单商品数据
    //'商品类型，专版，点播打赏，直播打赏，点播，直播品牌预付加盟，品牌商品加盟'
    static public function getProduct($type, $product_id)
    {
        switch ($type) {
            case 'vip':
                $data['title'] = Vip::where('id', $product_id)->value('name');
                $data['type'] = '专版';
                $data['order_type'] = '专版购买';
                break;
//            case 'video_reward';
//                $data['title'] = Video::where('id', $product_id)->value('subject');
//                $data['type'] = '点播打赏';
//                break;
//            case 'live_reward';
//                $data['title'] = Live::where('id', $product_id)->value('subject');
//                $data['type'] = '直播打赏';
//                break;
            case 'video';
                $data['title'] = Video::where('id', $product_id)->value('subject');
                $data['type'] = '录播';
                $data['order_type'] = '录播视频观看';
                break;
            case 'brand';
                $data['title'] = LiveGoods::where('id', $product_id)->value('title');
                $data['type'] = '品牌加盟定金';
                $data['order_type'] = '品牌加盟定金';
                break;
            case 'brand_goods';
                $data['title'] = BrandGoods::where('id', $product_id)->value('title');
                $data['type'] = '品牌商品加盟';
                $data['order_type'] = '品牌商品加盟';
                break;
            case 'news';
                $data['title'] = News::where('id', $product_id)->value('title');
                $data['type'] = '资讯';
                $data['order_type'] = '资讯阅读';
                break;
            case 'score';
                $data['title'] = ScoreGoods::where('id', $product_id)->value('subject');
                $data['type'] = '积分充值';
                $data['order_type'] = '充值积分';
                break;
            default:
                $data['title'] = '商品君正在路上';
                $data['type'] = '未知';
                $data['order_type'] = '未知';
                break;
        }
        $data['title'] = $data['title'] ? $data['title'] : '商品君正在路上';
        return $data;
    }


    /**
     * 银联支付签名
     *
     * @param $subject
     * @param $out_trade_no
     * @param $total_fee
     * @param $notify_url
     * @return mixed
     * @author tangjb
     */
    public static function unionPaySign($orderId, $amount)
    {
        if(\App::environment()!='production'){
            $amount = 1;
        }
        header('Content-type:text/html;charset=utf-8');
        include_once app_path('Http\Libs') . '/Unionpay/sdk/acp_service.php';

        $params = array(
            //以下信息非特殊情况不需要改动
            'version' => SDKConfig::getSDKConfig()->version,  //版本号
            'encoding' => 'utf-8',                  //编码方式
            'txnType' => '01',                      //交易类型
            'txnSubType' => '01',                  //交易子类
            'bizType' => '000201',                  //业务类型
            'frontUrl' => SDKConfig::getSDKConfig()->frontUrl,  //前台通知地址
            'backUrl' => config('unionpay.unionpay.acpsdk_backUrl'),      //后台通知地址
            'signMethod' => SDKConfig::getSDKConfig()->signMethod,                  //签名方法
            'channelType' => '08',                  //渠道类型，07-PC，08-手机
            'accessType' => '0',                  //接入类型
            'currencyCode' => '156',              //交易币种，境内商户固定156

            'merId' => config('unionpay.unionpay.merId'),        //商户代码，请改自己的测试商户号，此处默认取demo演示页面传递的参数
            'orderId' => $orderId,    //商户订单号，8-32位数字字母，不能含“-”或“_”，此处默认取demo演示页面传递的参数，可以自行定制规则
            'txnTime' => date('YmdHis'),    //订单发送时间，格式为YYYYMMDDhhmmss，取北京时间，此处默认取demo演示页面传递的参数
            'txnAmt' => $amount,    //交易金额，单位分，此处默认取demo演示页面传递的参数

            // 请求方保留域，
            // 透传字段，查询、通知、对账文件中均会原样出现，如有需要请启用并修改自己希望透传的数据。
            // 出现部分特殊字符时可能影响解析，请按下面建议的方式填写：
            // 1. 如果能确定内容不会出现&={}[]"'等符号时，可以直接填写数据，建议的方法如下。
            //    'reqReserved' =>'透传信息1|透传信息2|透传信息3',
            // 2. 内容可能出现&={}[]"'符号时：
            // 1) 如果需要对账文件里能显示，可将字符替换成全角＆＝｛｝【】“‘字符（自己写代码，此处不演示）；
            // 2) 如果对账文件没有显示要求，可做一下base64（如下）。
            //    注意控制数据长度，实际传输的数据长度不能超过1024位。
            //    查询、通知等接口解析时使用base64_decode解base64后再对数据做后续解析。
            //    'reqReserved' => base64_encode('任意格式的信息都可以'),

        );

//        dd(SDKConfig::getSDKConfig()->version);
        AcpService::sign($params); // 签名
        $url = SDKConfig::getSDKConfig()->appTransUrl;

        $result_arr = AcpService::post($params, $url);
        if (count($result_arr) <= 0) { //没收到200应答的情况
            printResult($url, $params, "");
            return;
        }

        if (!AcpService::validate($result_arr)) {
            return ['res' => false, 'message' => '验签失败'];
        }

        if ($result_arr["respCode"] == "00") {
            return ['res' => $result_arr["tn"], 'message' => '成功'];
        } else {
            return ['res' => false, 'message' => $result_arr["respMsg"]];
        }

    }


}
