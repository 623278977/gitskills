<?php
namespace App\Http\Controllers\Api;

use App\Http\Requests\Live\DetailRequest;
use App\Http\Requests\Live\DoSubscribeRequest;
use App\Http\Requests\Live\IncreRequest;
use App\Http\Requests\Live\OrderListRequest;
use App\Http\Requests\Live\OrderRequest;
use App\Http\Requests\Live\SendcodeRequest;
use App\Http\Requests\Live\ShareRequest;
use App\Http\Requests\Live\ShareSubscribeRequest;
use App\Http\Requests\Live\SubscribeRequest;
use App\Http\utils\randomViewUtil;
use App\Models\Live\Entity;
use App\Models\User\ShareFrom;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Live\Entity as Live;
use App\Models\Video\Entity as Video;
use App\Models\User\Entity as User;
use App\Models\Live\Subscribe as Subscribe;
use Illuminate\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator as lPaginator;
use DB;
use App\Models\Order\Entity as Order;
use App\Models\User\Ticket;
use App\Models\ScoreLog;
use App\Models\Identify;
use App\Models\Orders\Entity as Orders;
use App\Services\Live as LiveService;

class LiveController extends CommonController
{
    /**
     * 获取直播预告列表
     */
    public function postList(Request $request, $version,$fetch_end = 0)
    {
        if(in_array($version , ['_v020400','_v020500','_v020600','_v020602'])){
            $version = null;
        }
        if (strlen($fetch_end) > 1) {
            $fetch_end = 0;
        }

        $data = $request->input();

        if (isset($data['keywords'])) {
            $keywords = $data['keywords'];
        } elseif (isset($data['keyword'])) {
            $keywords = $data['keyword'];
        } else {
            $keywords = '';
        }

        $page = isset($data['page']) ? $data['page'] : 1;
        $size = isset($data['size']) ? $data['size'] : 10;
        $vip_id = isset($data['vip_id']) ? $data['vip_id'] : 0;
        $fetch_end = isset($data['fetch_end']) ? $data['fetch_end'] : $fetch_end;
        if ($keywords != '') {
            $lists = Live::lists($this->uid, $keywords, $page, $size, $vip_id, 1, $fetch_end);
        } else {
            if ($data['hotwords']) {
                $lists = Live::lists($this->uid, $keywords, $page, $size, $vip_id, 1, $fetch_end, $data['hotwords']);
            } else {
                $lists = Live::lists($this->uid, $keywords, $page, $size, $vip_id, 1, $fetch_end);
            }
        }
        $is_return = $request->input('is_return') ?: 0;

        //v020700版本对接口做相关处理
        $versionService = $this->init(__METHOD__, $version);
        if($versionService){
            $response = $versionService->bootstrap(['result' => $lists]);
            return ($is_return==1) ? $response['message']: AjaxCallbackMessage($response['message'], $response['status']);
        }

        return ($is_return == 1) ? $lists : AjaxCallbackMessage($lists, true);
    }

    /**
     * 获取今日直播列表
     */
    public function postToday(Request $request)
    {
        $data = $request->input();

        $page = isset($data['page']) ? $data['page'] : 1;
        $size = isset($data['size']) ? $data['size'] : 10;
        $vip_id = isset($data['vip_id']) ? $data['vip_id'] : 0;
        $lists = Live::today($page, $size, $vip_id);

        return AjaxCallbackMessage($lists, true);
    }

    /**
     *订阅或取消一个直播
     */
    public function postSubscribe(DoSubscribeRequest $request)
    {
        $data = $request->input();
        $result = Subscribe::subscribe($data);

        if ($result === -1) {
            return AjaxCallbackMessage('操作失败,已订阅', false, '');
        } elseif ($result === -2) {
            return AjaxCallbackMessage('操作失败，还未订阅', false, '');
        } elseif ($result === -3) {
            return AjaxCallbackMessage('操作失败，已取消订阅', false, '');
        } else {
            return AjaxCallbackMessage('操作成功', true, '');
        }
    }

    /**
     *获取用户的订阅列表
     */
    public function postUserSubscribe(Request $request)
    {
        $data = $request->input();
        $is_return = $request->input('is_return') ?: 0;;
        if (!$data['uid'] && $is_return == 1) {
            return [];
        }
        if (!$data['uid'] && $is_return == 0) {
            return AjaxCallbackMessage('uid为必传参数或uid不能为0', false);
        }
        $page = isset($data['page']) ? $data['page'] : 1;
        $size = isset($data['size']) ? $data['size'] : 10;
        $vip_id = isset($data['vip_id']) ? $data['vip_id'] : 0;
        $keywords = isset($data['keywords']) ? $data['keywords'] : '';
        $keywords = isset($data['keyword']) ? $data['keyword'] : $keywords = $keywords;
        $lists = Subscribe::user($this->uid, $keywords, $page, $size, 1, $vip_id);

        return ($is_return == 1) ? $lists : AjaxCallbackMessage($lists, true);
    }

    /**
     * @param Request $request
     * 根据活动id 获取直播地址
     */
    public function postLivedata(Request $request)
    {
        $id = $request->input('id'); //活动id
        if (empty($id)) {
            return AjaxCallbackMessage('参数有误', false);
        }
        $live = Entity::getRow(array('activity_id' => $id));
        if (!isset($live->id)) {
            return AjaxCallbackMessage('该活动暂无直播', false);
        }
        $data = array();
        $data['begin_time'] = date('m月d日 H:i', $live->begin_time);
        $data['live_id'] = $live->id;
        $_NOW = time();
        if ($live->begin_time > $_NOW) {//未开始
            $data['status'] = '1';
        } elseif ($live->end_time >= $_NOW) {//进行中
            $data['status'] = '2';
        } else {//已经结束
            $data['status'] = '3';
        }
        $data['url'] = createUrl('live/detail', array('id' => $live->id, 'pagetag' => config('app.live_detail')));

        return AjaxCallbackMessage($data, true);
    }

    /**
     *获取某个直播详情
     */
    public function postDetail(DetailRequest $request, $version = null)
    {
        $data = $request->input();

        //初始化
        $versionService = $this->init(__METHOD__, $version);

        if ($versionService) {
            $response = $versionService->bootstrap($data, ['uid' => $this->uid]);

            return AjaxCallbackMessage($response['message'], $response['status']);
        }

        $lists = Live::detail($data['id'], $this->uid);
        $platform = $request->get('platform');
        if ($platform) {
            \App\Models\Log\Live::add($platform, $data['id'], $this->uid);
        }
        $rec = Live::recommend($data['id']);
        $lists['rec'] = $rec;
        $lists['page_url'] = createUrl('live/detail', array('id' => $data['id'], 'uid' => $this->uid, 'pagetag' => config('app.live_detail')));

        return AjaxCallbackMessage($lists, true);
    }

    /**
     * 下单购买直播
     */
    public function postOrder(OrderRequest $request)
    {
        $data = $request->input();
        $rate = config('system.score_rate');
        $data['score_num'] = $request->get('score_num', 0);
        //检查积分是否合法
        $check = Order::checkScore($data['score_num'], $data['uid'], $rate);
        $arr = ['-1' => '不是正整数', '-2' => '积分大于会员拥有的积分', '-3' => '积分应该为汇率的百分之一的倍数'];
        if ($check != 1) {
            return AjaxCallbackMessage($arr[$check], false);
        }

        //如果含有video_id字符串，则使用新下单接口
        if (strstr($data['ticket_id'], 'video_id')) {
            //下单
            $orders = Orders::place(
                $data['uid'],
                $data['cost'],
                [
                    ['type' => 'video', 'product_id' => substr($data['ticket_id'], 8), 'price' => $data['cost'], 'num' => 1],
                ],
                '',
                $data['score_num'],
                ($data['score_num'] / $rate),
                ($data['cost'] - ($data['score_num'] / $rate))
            );
            $video = Video::where('id', substr($data['ticket_id'], 8))->first();
            ScoreLog::add($data['uid'], $data['score_num'], 'video_buy', '视频或直播购买使用积分', -1, false, 'video', $video->id);

            if (is_object($orders)) {
                return AjaxCallbackMessage('video_id' . $orders->order_no, true);
            } else {
                return AjaxCallbackMessage('下单失败', false);
            }
        }

        //先下单
        $order = Order::place(
            $data['uid'],
            $data['ticket_id'],
            $data['cost'],
            $data['product'],
            $data['body'],
            '',
            $data['score_num'],
            $data['score_num'] / $rate,
            ($data['cost'] - ($data['score_num'] / $rate)),
            0,
            'live'
        );
        //减去积分
        ScoreLog::add($data['uid'], $data['score_num'], 'live_video_buy', '视频或直播购买使用积分', -1, false);

        if (is_object($order)) {
            return AjaxCallbackMessage($order['order_no'], true);
        } else {
            return AjaxCallbackMessage('下单失败', false);
        }
    }

    /**
     * 自增1
     */
    public function postIncre(IncreRequest $request)
    {
        $data = $request->input();

        if ($data['type'] == 'live') {
            Live::incre(['view' => $data['num']], ['id' => $data['id']]);
            //伪浏览量
            $sham_view = Live::where('id', $data['id'])->value('sham_view');
            $increment = randomViewUtil::getRandViewCount($sham_view);//增量
            Live::where('id', $data['id'])->increment('sham_view', $increment);
        } else {
            Video::incre(['view' => $data['num']], ['id' => $data['id']]);
        }

        return AjaxCallbackMessage('操作成功', true);
    }

    public function postShare(ShareRequest $request)
    {
        $data = $request->input();

        return true;
    }




    /**
     * 分享直播订阅  --数据中心版
     * @User yaokai
     * @param ShareSubscribeRequest $request
     * @return string
     */
    public function postSharesubscibe(ShareSubscribeRequest $request)
    {
        $data = $request->input();

        //伪号码
        $username = pseudoTel($data['tel']);

        //用户加密后的手机号
        $non_reversible = encryptTel($data['tel']);

        $nation_code = $request->input('nation_code', '86');

        if (!checkMobile(trim($data['tel']), $nation_code)) {
            return AjaxCallbackMessage('手机号格式不对', false, '');
        }
        //验证
        $identify = Identify::checkIdentify($non_reversible, 'live_share_authorize', $data['code']);
        if ($identify != 'success') {
            return AjaxCallbackMessage($identify, false, '');
        }
        $live = Live::getRow(['id' => $data['live_id']]);
        if (!is_object($live)) {
            return AjaxCallbackMessage('不存在与该直播id对应的直播', false, '');
        }
        $live->begin_time > time() ? $is_begin = 0 : $is_begin = 1;
        //判断该手机号是否已经注册过
        $existed = User::where('non_reversible', '=', $non_reversible)->first();
        if (is_object($existed)) {
            if ($is_begin == 0) {
                //订阅直播
                Subscribe::subscribe(['uid' => $existed->uid, 'live_id' => $data['live_id'], 'type' => 1, 'path' => 'h5']);

                SendTemplateSMS('live_share_subscribe',$data['tel'],'live_share_subscribe', [], $nation_code, 'wjsq', false);
            }

            return AjaxCallbackMessage('1', true);
            //如果不存在就注册。
        } else {
            $shares = ShareFrom::with('user')->get();
            $nicknames = array_map(
                function ($v) {
                    return substr($v['user']['nickname'], -5, 5);
                },
                $shares->toArray()
            );
            $nickname = "匿名用户{$this->makeNum($nicknames)}";

            //数据中心处理
            $url = config('system.data_center.hosts') . config('system.data_center.encrypt');
            $datas = [
                'nation_code' => $data['nation_code']?:'86',
                'tel' => $data['tel'],
                'platform' => 'wjsq',//来源无界商圈注册
                'en_tel' => $non_reversible,//通过加盐后得到手机号码
            ];

            //请求数据中心接口
            $result = json_decode(getHttpDataCenter($url, '', $datas));


            //如果异常则停止
            if (!$result) {
                return AjaxCallbackMessage('服务器异常！',false);
            } elseif ($result->status == false) {
                return AjaxCallbackMessage($result->message,false);
            }

            //正常写数据
            $user = User::create(['username' => $username,'non_reversible'=>$non_reversible, 'password' => md5($data['tel']), 'nickname' => $nickname, 'zone_id' => 175]);
            if ($is_begin == 0) {
                //订阅直播
                Subscribe::subscribe(['uid' => $user->uid, 'live_id' => $data['live_id'], 'type' => 1, 'path' => 'h5']);

                SendTemplateSMS('live_share_subscribe',$data['tel'],'live_share_subscribe', [], $nation_code, 'wjsq', false);
                ShareFrom::create(['uid' => $user->uid, 'share_type' => 'live', 'post_id' => $data['live_id'], 'period' => 'prepare']);
            } else {
                ShareFrom::create(['uid' => $user->uid, 'share_type' => 'live', 'post_id' => $data['live_id'], 'period' => 'processing']);
            }

            return AjaxCallbackMessage('2', true);
        }
    }

    /**
     * 直播留存发送短信  --数据中心版
     * @User yaokai
     * @param Request $request
     * @return string
     */
    public function postSendcode(Request $request)
    {
        $data = $request->input();

        //伪号码
        $username = pseudoTel($data['tel']);

        //用户加密后的手机号
        $non_reversible = encryptTel($data['tel']);

        $nation_code = $request->input('nation_code', '86');

        if (empty($data['tel'])) {
            return AjaxCallbackMessage('手机号不能为空', false);
        }
        if (!checkMobile(trim($data['tel']), $nation_code)) {
            return AjaxCallbackMessage('手机号格式不对', false, '');
        }

        //判断该手机号是否已经注册过
        $existed = User::getRow(['non_reversible' => $non_reversible]);

        /***如果半个小时之内 有 没有使用的code 就不产生新的验证码 ***/
        $time = time() - 15 * 60;
        $identify = Identify::where('non_reversible', $non_reversible)->where('type', 'live_share_authorize')
            ->where('status', 0)->where('created_at', '>', $time)->orderBy('id', 'desc')->first();
        if($identify && $identify->created_at->timestamp>time()-60){
            return AjaxCallbackMessage('休息会吧，一分钟只能发送一次',false);
        }
//        if (!isset($identify->id)) {
            $code = mt_rand(10000, 99999);
//        } else {
//            $code = $identify->code;
//        }

        if (is_object($existed)) {
            $res = SendTemplateSMS('live_share_registered',$data['tel'],'live_share_authorize',['code' => $code], $nation_code,'wjsq',false);
        } else {
            User::create(['username' => $username,'non_reversible'=>$non_reversible,
                'password' => md5($data['tel']), 'nickname' => uniqid('wjsq_'), 'zone_id' => 175]);

            $res = SendTemplateSMS('live_share_unregister',$data['tel'],'live_share_authorize',['code' => $code], $nation_code,'wjsq',false);
        }
        if ($res != 1) {
            return AjaxCallbackMessage('验证码发送失败', false);
        }
        $identifyData = array(
            'uid'    => 0, //todo 暂不处理 可以通过手机号关联的 yaokai 2017.12.13
            'code'   => $code,
            'nation_code' => $nation_code,
            'mobile' => $username,
            'type'   => 'live_share_authorize',
            'non_reversible'   => $non_reversible
        );
        //这里取消判断是否有数据(有就修改，没有就创建的判断)，直接保存所有发短信记录
//        if ($identify) {
//            $identify->fillable(['created_at','code'])
//                ->update(
//                    [
//                        'created_at' => time(),
//                        'code' => $code,
//                    ]
//                );
//        } else {
            Identify::create($identifyData);
//        }

        return AjaxCallbackMessage('验证码发送成功', true);
    }

    /**
     * 生成5个不重复的数字
     */
    private function makeNum($arr)
    {
        $num = rand(0, 9) . rand(0, 9) . rand(0, 9) . rand(0, 9) . rand(0, 9);
        if (in_array($num, $arr)) {
            $num = self::makeNum($arr);
        }

        return $num;
    }

    /**
     * 获取某场直播订单列表
     */
    public function postOrderList(OrderListRequest $request, LiveService $live = null)
    {
        $data = $request->input();
        $real_order_max_id = $request->input('real_order_max_id', 0);
        $sham_order_max_id = $request->input('sham_order_max_id', 0);
        $type = $request->input('type', 'mix');

        $result = $live->orderList($data['live_id'], $real_order_max_id, $sham_order_max_id, $type);

        return AjaxCallbackMessage($result, true);
    }

    /**
     * 获取某场直播的在线人数及登录用户头像
     */
    public function postViewers(Request $request, $version = null)
    {
        $versionService = $this->init(__METHOD__, $version);
        $log_id = $request->get('log_id', 0);
        $fetch_size = $request->get('fetch_size', 0);
        $with_anonymous = $request->get('with_anonymous', 0);
        if ($versionService) {
            $response = $versionService->bootstrap($request->all(), compact('log_id', 'fetch_size', 'with_anonymous'));

            return AjaxCallbackMessage($response['data'], $response['status']);
        }

        return AjaxCallbackMessage('api接口不再维护', false);
    }

    /**
     * 获取某个数据墙的基本信息
     */
    public function postWallInfo(Request $request, $version = null)
    {
        $versionService = $this->init(__METHOD__, $version);
        if ($versionService) {
            $response = $versionService->bootstrap($request->all());

            return AjaxCallbackMessage($response['data'], $response['status']);
        }

        return AjaxCallbackMessage('api接口不再维护', false);
    }

    /**
     * 获取某个数据墙是商品的基本信息
     */
    public function postGoodsdetail(Request $request, $version = null)
    {
        $data = $request->input();
        if (empty($data['live_id'])) {
            return AjaxCallbackMessage('直播id不能为空', false);
        }
        $versionService = $this->init(__METHOD__, $version);
        if ($versionService) {
            $response = $versionService->bootstrap($request->all());

            return AjaxCallbackMessage($response['data'], $response['status']);
        }

        return AjaxCallbackMessage('api接口不再维护', false);
    }



    /**
     * 获取直播购买信息
     */
    public function postBuyInfo(Request $request, $version = null)
    {
        $data = $request->input();
        if (empty($data['id'])) {
            return AjaxCallbackMessage('直播id不能为空', false);
        }

        if (empty($data['type']) && !in_array($data['type'], ['activity', 'live'])) {
            return AjaxCallbackMessage('type只能为activity或live', false);
        }



        if (empty($data['uid'])) {
            return AjaxCallbackMessage('用户uid不能为空', false);
        }

        $versionService = $this->init(__METHOD__, $version);
        if ($versionService) {
            $response = $versionService->bootstrap($request->all());

            return AjaxCallbackMessage($response['data'], $response['status']);
        }

        return AjaxCallbackMessage('api接口不再维护', false);
    }
}