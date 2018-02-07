<?php
/**
 * 对接申请池控制器
 */

namespace App\Http\Controllers\Api;

use App\Http\Libs\Helper_Huanxin;
use App\Models\Activity\Ticket;
use App\Models\Brand\Operation;
use App\Models\CacheTool;
use App\Models\Fund;
use App\Models\Opportunity;
use App\Models\ScoreLog;
use App\Models\User\BusinessCard;
use App\Models\User\Entity;
use App\Models\News\Entity as NewsEntity;
use App\Models\User\Favorite;
use App\Models\User\Feedback;
use App\Models\User\Friend;
use App\Models\User\Industry;
use App\Models\Video;
use App\Models\Zone;
use App\Services\Brand;
use App\Services\Categorys;
use App\Services\News;
use App\Services\Version\Brand\_v020400;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Api\CommonController;
use DB, Auth;
use Illuminate\Support\Facades\Cache;
use App\Models\Activity\Entity as Activity;
use App\Models\Activity\Maker;
use App\Models\Agent\Agent;

class UserController extends CommonController
{
    private $_uid;

    public function __construct(Request $request)
    {
        $this->_uid = $request->input('uid');
        if (!Entity::checkAuth($this->_uid)) {
            return AjaxCallbackMessage('账号异常', false);
        }
    }

    /**
     * 改了的字段就传 没改的字段不用传
     * 修改用户信息
     *
     * @param Request $request
     *
     * @param null $version
     * @return string
     */
    public function postUpdate(Request $request, $version = null)
    {
        if ($version && substr($version, 2) >= '020800') {
            $data = $request->input();
            $versionService = $this->init(__METHOD__, $version);
            $result = $versionService->bootstrap($data, ['request' => $request]);

            return AjaxCallbackMessage($result['message'], $result['status']);
        }

        $uid = $request->input('uid');
        if (!Entity::checkAuth($uid)) {
            return AjaxCallbackMessage('账号有误', false);
        }
        $_nickname = $request->get('nickname');
        if ($_nickname !== null) {
            $content = mb_convert_encoding((string)$_nickname, 'utf-16');
            $bin = bin2hex($content);
            $arr = str_split($bin, 4);
            $l = count($arr);
            $str = '';
            for ($n = 0; $n < $l; $n++) {
                if (isset($arr[$n + 1]) && ('0x' . $arr[$n] >= 0xd800 && '0x' . $arr[$n] <= 0xdbff && '0x' . $arr[$n + 1] >= 0xdc00 && '0x' . $arr[$n + 1] <= 0xdfff)) {
                    $n++;
                } else {
                    $str .= $arr[$n];
                }
            }
            $nickname = mb_convert_encoding(hex2bin($str), 'utf-8', 'utf-16');

            $request->merge(compact('nickname'));
            if (Entity::where('uid', '!=', $uid)->where("nickname", '=', $nickname)->count()) {
                return AjaxCallbackMessage('昵称已存在', false);
            }
        }
        $list = array('activity_remind', 'avatar', 'nickname', 'realname', 'gender', 'zone_id', 'sign', 'tel', 'birth', 'diploma', 'earning', 'profession');
        $this->editList($request, new Entity(), 'uid', $this->_uid, $list);
        if ($_nickname) {
            $request->merge(['nickname' => $_nickname]);
        }
        $avatar = $request->input('avatar');
        if (strpos($avatar, config('app.base_url')) !== false) {
            Entity::where('uid', $this->_uid)->update(array('avatar' => removeDomainStr($avatar)));
        }
        $industry = $request->input('industry');

        if (count($industry)) {
            $user = Entity::getRow(array('uid' => $this->_uid));
            Industry::dealUserIndustry($user, $industry);
            CacheTool::clearCache('industry_user_' . $user->uid);
            Industry::cache($user);
        }

        return AjaxCallbackMessage('修改成功', true);
    }

    /**
     * @param Request $request
     * 用户反馈
     */
    public function postUserfeedback(Request $request)
    {
        $content = $request->input('content');
        if (empty($content)) {
            return AjaxCallbackMessage('内容不能为空', false);
        }
        $nickname = $request->input('nickname');
        $tel = pseudoTel($request->input('tel'));
        $non_reversible=encryptTel($request->input('tel'));
        $uid = $this->_uid;
        depositTel($request->input('tel'),$non_reversible);
        Feedback::create(compact('uid', 'content', 'nickname', 'tel', 'non_reversible'));

        return AjaxCallbackMessage('提交成功', true);
    }

    /**
     * @param Request $request
     * 我的门票
     */
    public function postUserticketlist(Request $request, $version = null)
    {
        if (in_array($version, ['_v020400'])) {
            $version = null;
        }

        $data = $request->input();

        //初始化
        $versionService = $this->init(__METHOD__, $version);

        if ($versionService) {
            $response = $versionService->bootstrap($data, ['request' => $request, '_uid' => $this->_uid]);
            return AjaxCallbackMessage($response['message'], $response['status']);
        }

        $type = $request->input('type') ?: 'my'; //我的my  未完成notover
        $page = $request->input('page') ?: 1;
        $pageSize = $request->input('pageSize') ?: 15;
        $where = array();
        $where['ut.uid'] = $this->_uid;
        $tickets = \App\Models\User\Ticket::getTicketsList($where, $type, $page, $pageSize);

        return AjaxCallbackMessage($tickets, true);
    }

    /**
     * @param Request $request
     * @return string
     * 获取某人的积分
     */
    public function postScore(Request $request)
    {
        $user = Entity::getRow(array('uid' => $this->_uid));
        if (!is_object($user)) {
            return AjaxCallbackMessage('参数错误', false);
        }

        $rate = config('system.score_rate');
        $data = ['score' => $user->score, 'rate' => $rate];

        return AjaxCallbackMessage($data, true);
    }


    /**
     * @param Request $request
     * @return string
     * 获取某人的积分
     */
    public function postCurrency(Request $request, $version = null)
    {
        $data = $request->input();
        $_version = $request->input('_version', null);
        if ($version) {
            $versionService = $this->init(__METHOD__, $version, $_version);
            $result = $versionService->bootstrap($data);
            return AjaxCallbackMessage($result['message'], $result['status']);
        }

        return AjaxCallbackMessage('该接口停用', false);
    }

    /**
     * @param Request $request
     * @return string
     * 积分明细
     */
    public function postScorelist(Request $request, $version = null)
    {
        //如果版本大于2.7就走版本控制
        if (substr($version, -5) >= 20700) {
            $data = $request->input();
            $_version = $request->input('_version', null);
            if ($version) {
                $versionService = $this->init(__METHOD__, $version, $_version);
                $result = $versionService->bootstrap($data);
                return AjaxCallbackMessage($result['message'], $result['status']);
            }

            return AjaxCallbackMessage('该接口停用', false);
        }


        $page = $request->input('page') ?: 0;
        $pageSize = $request->input('pageSize') ?: 10;
        $scorelist = ScoreLog::getRows(array('uid' => $this->_uid), $page, $pageSize);
        $data = array();
        if (count($scorelist)) {
            foreach ($scorelist as $k => $v) {
                $data[$k] = ScoreLog::getBase($v);
            }
        }

        return AjaxCallbackMessage($data, true);
    }

    /**
     * @param Request $request
     * @return string
     * 我的收藏
     */
    public function postFavorite(Request $request, $version = null)
    {
        $model = $request->input('model');
        if (empty($model)) {
            return AjaxCallbackMessage('参数不能为空', false);
        }
        $page = $request->input('page', 0);
        if ($version) {
            if ($page > 0) {
                $page = $page - 1;
            }
        }
        $pageSize = $request->input('pageSize') ?: 10;
        $keyword = trim($request->input('keyword'));
        $hotwords = trim($request->input('hotwords'));
        $where = $data = array();
        $where['uf.status'] = 1;
        $where['uf.model'] = $model;
        $where['uf.uid'] = $this->_uid;
        $newModel = ($model == 'activity' ? new \App\Models\Activity\Entity() : ($model == 'video' ? new Video() : ($model == 'brand' ? new \App\Models\Brand\Entity() : new Opportunity())));
        $query = "";

        switch ($model) {
            case "video":
                $query = $newModel::with('favorite', 'videoType')->where($where);
                break;
            case "opportunity":
                $query = $newModel::with('favorite', 'maker', 'industrys')->where($where);
                break;
            case "brand":
                $query = $newModel::with('favorite')->where($where);
                break;
            default:
                $query = $newModel::with('favorite')->where($where);
        }
        if (!empty($keyword)) {
            if ($model == 'brand') {
                $query->where("$model.name", 'like', '%' . $keyword . '%');
            } else {
                $query->where("$model.subject", 'like', '%' . $keyword . '%');
            }
        }

        if (!empty($hotwords)) {
            if ($model == 'brand') {
                $query->where("$model.name", 'like', '%' . $hotwords . '%');
            } else {
                $query->where("$model.subject", 'like', '%' . $hotwords . '%');
            }
        }
        $list = $query->Join("user_favorite as uf", "$model.id", '=', 'uf.post_id')->select(
            "$model.*",
            DB::raw('FROM_UNIXTIME(' . DB::getConfig('prefix') . 'uf.updated_at,"%Y-%m-%d %H:%i") as uf_created_at')
        )
            ->skip($page * $pageSize)
            ->take($pageSize)
            ->orderBy('uf.created_at', 'desc')//添加排序
            ->get();
//        print_r($list);exit;
        if (count($list) && $model != 'brand') {
            foreach ($list as $k => $v) {
                $data[$k] = $newModel::getBase($v);
                $data[$k]['created_at'] = $v->uf_created_at;
                $data[$k]['favorite_count'] = $data[$k]['count'] = $v->favorite->count();
                $data[$k]['description'] = substrwithdot($v->description, 18);
                $data[$k]['keywords'] = empty($v->keywords) ? [] : (strpos($v->keywords, ' ') !== FALSE ? explode(' ', $v->keywords) : [$v->keywords]);
                $data[$k]['brand_name'] = '';
                if ($model == "activity") {
                    $data[$k]['begin_time'] = date('m/d', $v->begin_time);
                    $data[$k]['description'] = substrwithdot(strip_tags($v->description), 18);
                    $data[$k]['brand_name'] = ($brand = \DB::table('activity_brand as ab')
                        ->join('brand as b', 'ab.activity_id', '=', 'b.id')
                        ->where('ab.activity_id', $v->id)
                        ->first()) ? $brand->name : '';
                }
                if ($model == "opportunity") {
                    $data[$k]['description'] = substrwithdot($v->park_info, 18);
                }
            }
        } else {
            $data = \App\Models\Brand\Entity::getFavoriteData($list);
        }

        return AjaxCallbackMessage($data, true);
    }

    //获取电子名片详情
    public function postBusinessCardDetail()
    {
        $card = BusinessCard::where('uid', '=', $this->_uid)->first(['name', 'gender', 'mobile', 'email', 'institution', 'job', 'phone', 'fax', 'zone_id', 'address', 'postcode', 'business_card']);
        if ($card) {
            $card->zone = array_get(Zone::find($card->zone_id), 'name');
            unset($card->zone_id);
        }

        return AjaxCallbackMessage($card ? $card->toArray() : [], true);
    }

    /**
     * 投资人详情 zhaoyf
     *
     * @param Request $request
     * @param null $version
     * @return string
     */
    public function postDetail(Request $request, $version = null)
    {
        $result = $request->input();
        if ($version) {
            $versionService = $this->init(__METHOD__, $version);
            $result = $versionService->bootstrap($result);

            return AjaxCallbackMessage($result['message'], $result['status']);
        } else {
            return AjaxCallbackMessage('该接口不存在', false);
        }
    }

    /**
     * @param Request $request
     * 上传名片
     */
    public function postBusinesscard(Request $request)
    {
        $uid = $this->_uid;
        $card = BusinessCard::getRow(array('uid' => $this->_uid));
        if (isset($card->id)) {
            //编辑
            $list = array('name', 'gender', 'mobile', 'email', 'institution', 'job', 'phone', 'fax', 'zone_id', 'address', 'postcode', 'business_card');
            $this->editList($request, new BusinessCard(), 'uid', $this->_uid, $list);
        } else {
            //添加
            $data = $request->only('uid', 'name', 'gender', 'mobile', 'email', 'institution', 'job', 'phone', 'fax', 'zone_id', 'address', 'postcode', 'business_card');
            BusinessCard::create($data);
        }
        //同步到个人资料中 名称
        $name = $request->input('name');
        empty($name) || Entity::where('uid', '=', $this->_uid)->update(['realname' => $name]);

        return AjaxCallbackMessage('更新成功', true);
    }

    /**
     * @param Request $request
     * 根据uid,username数组 获取一组人的基本信息
     */
    public function postGetuserbasic(Request $request)
    {
        $user_outh = $request->input('user_outh');
        if (!count($user_outh)) {
            return AjaxCallbackMessage('参数有误', false);
        }
        $data = Entity::getUserByuidOrUsername($user_outh, 'basic');
        if (Auth::check()) {
            foreach ($data as $k => $v) {
                if ($v['is_wjsq']) {
                    $data[$k]['friend'] = Friend::getRemark(Auth::id(), $v['uid']);
                }
            }
        }

        return AjaxCallbackMessage($data, true);
    }

    /**
     * @param Request $request
     * 根据uid,username数组 获取一组人的详细信息
     */
    public function postGetuserdetail(Request $request, $version = null)
    {
        $user_outh = $request->input('user_outh');
        if (!count($user_outh)) {
            return AjaxCallbackMessage('参数有误', false);
        }

        $data = $request->input();

        //初始化
        $versionService = $this->init(__METHOD__, $version);

        if ($versionService) {
            $response = $versionService->bootstrap($data, ['uid', $this->uid]);
            return AjaxCallbackMessage($response['message'], $response['status']);
        }


        $data = Entity::getUserByuidOrUsername(array($user_outh), 'detail');
        if (Auth::check()) {
            foreach ($data as $k => $v) {
                if ($v['is_wjsq']) {
                    $data[$k]['friend'] = Friend::getRemark(Auth::id(), $v['uid']);
                }
            }
        }

        return AjaxCallbackMessage($data, true);
    }

    /**
     * @param Request $request
     * 推荐用户
     */
    public function postRecommendusers(Request $request)
    {
        $uid = $request->input('uid') ?: 0;
        if (!Entity::checkAuth($uid)) {
            return AjaxCallbackMessage('账号有误', false);
        }
//        $users=Entity::getUsersByRand();
        $data = array();
        $users = Entity::where('status', '=', '1')
            ->where('uid', '!=', $uid)
            ->orderBy(DB::Raw('rand()'), 'desc')
            ->limit(10)
            ->get();
        if (count($users)) {
            foreach ($users as $k => $v) {
                $data[$k] = Entity::getUser($v);
                $data[$k]['zone'] = Zone::getZone($v->zone_id);
//                //$relations = Helper_Huanxin::isFriends($uid, array($v->uid));
//                //$relations = json_decode($relations,true);
//                //$data[$k]['relation']=isset($relations[0]['relation'])?$relations[0]['relation']:'stranger';
            }
        }

        return AjaxCallbackMessage($data, true);
    }

    /**
     * @param Request $request
     * 用户报名活动列表
     */
    public function postApplyActivityLists(Request $request)
    {
        $uid = (int)$request->input('uid');
        if (!$uid) {
            return AjaxCallbackMessage('参数有误', false);
        }
        $size = $request->has('page_size') && $request->input('page_size') > 1 ? (int)$request->input('page_size') : 10;
        //取活动报名列表
        $lists = Activity::whereIn(
            'id',
            function ($query) use ($uid) {
                $query->from('user_ticket')
                    ->where('uid', '=', $uid)
                    ->where('status', 1)
                    ->distinct()
                    ->select('activity_id');
            }
        )
            ->orderBy('id', 'desc')
            ->paginate($size, ['id', 'subject', 'view', 'list_img', 'description', 'begin_time', 'is_recommend']);
        //取OVO中心列表
        $_makers = Maker::whereIn('activity_id', $activity_ids = array_pluck($lists, 'id'))
            ->leftJoin('maker', 'activity_maker.maker_id', '=', 'maker.id')
            ->get(['activity_maker.maker_id', 'activity_maker.type', 'activity_maker.activity_id', 'maker.zone_id']);
        //取地区
        $zones = array_pluck(Zone::whereIn('id', array_pluck($_makers, 'zone_id'))->get(['id', 'name']), 'name', 'id');
        //取活动券列表
        $tickets = Ticket::whereIn('activity_id', $activity_ids)
            ->where('status', '=', '1')
            ->get(['price', 'type'])
            ->groupBy('activity_id');
        $data = [];
        $makers = $_makers->groupBy('activity_id');
        //整合数据
        foreach ($lists as $item) {
            $price = $ticket_type = $city = $maker = $type = [];
            if ($tickets->has($item->getKey())) {
                $tickets[$item->getKey()]->each(
                    function ($item) use (&$price, &$ticket_type) {
                        $price[] = $item->price;
                        $ticket_type[] = $item->type;
                    }
                );
            }
            if ($makers->has($item->getKey())) {
                $makers[$item->getKey()]->each(
                    function ($item) use (&$city, &$maker, &$type, &$zones) {
                        if ($item->zone_id) {
                            $city[] = array_get($zones, $item->zone_id);
                        }
                        $maker[] = $item->maker_id;
                        $type[] = $item->type;
                    }
                );
            }
            $data[] = array_only($item->toArray(), ['id', 'subject', 'view', 'list_img', 'description', 'is_recommend']) + [
                    'begin_time' => date('m/d', $item->begin_time),
                    'price' => implode('@', $price),
                    'ticket_type' => implode('@', $ticket_type),
                    'min_price' => count($price) ? min($price) : 0,
//                'now_city' => '',
//                'now_maker_id' => '',
                    'url' => 'api/activity/detail?id=' . $item->getKey(),
                    'city' => implode('@', $city),
                    'type' => implode('@', $type),
                ];
        }

        return AjaxCallbackMessage($data, true);
    }

    /**
     * 获取用户积分及汇率
     */
    public function postRate(Request $request)
    {
        $user = Entity::getRow(['uid' => $this->_uid]);
        if (!is_object($user)) {
            return AjaxCallbackMessage('参数有误', false);
        }

        $rate = config('system.score_rate');
        $data = ['score' => $user->score, 'rate' => $rate];

        return AjaxCallbackMessage($data, true);
    }

    /*
     * 我的品牌列表
     */
    public function postMybrandlist(Request $request, $version = null)
    {
        if (!$request->has('uid') || $request->input('uid') == 0) {
            return AjaxCallbackMessage('uid不能为空', FALSE);
        }
        return call_user_func_array([new BrandController(), 'postLists'], [$version, 0, $request->input('uid')]);
    }

    /*
     * 我的品牌相关操作
     */
    public function postBrandoperation(Requests\UserRequest $request)
    {
        if (!$request->has('uid') || $request->input('uid') == 0) {
            return AjaxCallbackMessage('uid不能为空', FALSE);
        }
        if (!$request->has('brand_id') || $request->input('brand_id') == 0) {
            return AjaxCallbackMessage('品牌id不能为空', FALSE);
        }
        if (!$request->has('action') || $request->input('action') == '') {
            return AjaxCallbackMessage('操作动作不能为空', FALSE);
        }
        $param = $request->input();
        if (!\App\Models\Brand\Entity::where('uid', $param['uid'])->where('id', $param['brand_id'])->first()) {
            return AjaxCallbackMessage('你没有入住该品牌', FALSE);
        }
        $return = Operation::baseBuilder($param);
        if (!$return) {
            //提交操作
            if (!Operation::doJob($param)) {
                return AjaxCallbackMessage('操作失败', FALSE);
            } else {
                return AjaxCallbackMessage('操作成功', true);
            }
        } else {
            //24小时内不能重复提交
            $last_time = $return ? $return->updated_at->timestamp : 0;//上一次操作的时间
            if ((time() - $last_time) < 24 * 3600) {
                return AjaxCallbackMessage('24小时内勿重复申请，稍后会有客服人员跟你联系', FALSE);
            } else {
                Operation::baseBuilder($param, function ($builder) use ($param) {
                    $builder->update([
                        'uid' => $param['uid'],
                        'created_at' => time(),
                        'updated_at' => time()
                    ]);
                });
            }

            return AjaxCallbackMessage('操作成功', true);
        }
    }

    /*
     * 我的订单列表
     */
    public function postMyorders(Request $request, $version = null)
    {
        if (in_array($version, ['_v020400'])) {
            $version = null;
        }

        if (!$request->has('uid')) {
            return AjaxCallbackMessage('uid不能为空', false);
        }

        //v020500版本对接口做相关处理
        $versionService = $this->init(__METHOD__, $version);
        if ($versionService) {
            $response = $versionService->bootstrap($request->all(), ['request' => $request , 'version'=>$version]);
            return AjaxCallbackMessage($response['message'], $response['status']);
        }

        if ($data = \App\Models\Orders\Entity::myOrders($request->all())) {
            foreach ($data as $item) {
                //$product_ids = strpos($item->product_id,',')?explode(',',$item->product_id):[$item->product_id];
                //$product_typs = strpos($item->type,',')?explode(',',$item->type):[$item->type];
                $product = $this->productInfo($item);
                $item->status = $this->payStatus($item->oi_status);//支付状态
                $item->brand = $product ? $product->name : '';//品牌
                $item->code = $product ? $product->code : '';//商品代号
                $item->created_at_time = date('Y-m-d H:i:s', $item->created_at->timestamp);
                $item->amount = formatMoney($item->amount);
                unset($item->created_at, $item->product_id, $item->type, $item->oi_status);
            }
        }

        return AjaxCallbackMessage($data ? $data->toArray()['data'] : [], true);

    }

    /*
     * 我的订单详情
     */
    public function postMyorderinfo(Request $request, $version = null)
    {
        if (in_array($version, ['_v020400'])) {
            $version = null;
        }

        //v020500版本对接口做相关处理
        $versionService = $this->init(__METHOD__, $version);
        if ($versionService) {
            $response = $versionService->bootstrap($request->all(), ['request' => $request]);
            return AjaxCallbackMessage($response['message'], $response['status']);
        }

        if (!$request->has('oi_id')) {
            return AjaxCallbackMessage('订单认购项id不能为空', false);//订单认购项id
        }
        if (!$request->has('uid')) {
            return AjaxCallbackMessage('uid不能为空', false);
        }
        if ($data = \App\Models\Orders\Entity::orderInfo($request->all())) {
            $orders_items_data = $data->orders_items_data;
            $orders_items_data_arr = [];
            if (strpos($orders_items_data, ',')) {
                $orders_items_data_arr = explode(',', $orders_items_data);
            } else {
                $orders_items_data_arr[] = $orders_items_data;
            }
            $data->product = $return = $product[] = [];
            foreach ($orders_items_data_arr as $item) {
                $param = explode('|', $item);
                $res = $this->productInfo($param);
                if ($res) {
                    $return[] = $res->toArray();
                }
                unset($param);
            }
            $data->product = $return;
            $data->isDonePayment = $data->status == 'pay' ? 1 : 0;
            $consult = $this->consultStatus($return, $request->input('oi_id'));
            $data->isReply = $consult ? ($consult->admin_id > 0 ? 1 : 0) : 0;
            $data->isReplyTime = str_replace(' ', ' \n ', date('Y-m-d H:i:s', $consult->communication_at));//回访时间
            $data->isDoneJob = $consult ? ($consult->status == 'finish' ? 1 : 0) : 0;
            $data->status = $this->payStatus($data->status);//支付状态
            $data->created_at_time = date('Y-m-d H:i:s', $data->created_at->timestamp);
            $data->pay_at_time = str_replace(' ', ' \n ', date('Y-m-d H:i:s', $data->pay_at));
            $data->finish_time = str_replace(' ', ' \n ', date('Y-m-d H:i:s', $consult->finish_at));//完成时间
            //格式化金额
            $data->amount = formatMoney($data->amount);
            $data->score_money = formatMoney($data->score_money);
            $data->online_money = formatMoney($data->online_money);
            unset($data->orders_items_id, $data->created_at, $data->pay_at, $data->orders_items_data);
        }
        return AjaxCallbackMessage($data ?: [], true);
    }


    /*
     * 洽谈
     */
    public function consultStatus($param, $oi_id)
    {
        if (is_array($param)) {
            $id = $param[0]['id'];
        } else {
            $id = $param['id'];
        }
        $data = DB::table('brand_consult')->where('type', 'prepay')
            ->where('brand_id', $id)
            ->where('relation_id', $oi_id)
            ->first();
        return $data;
    }

    /*
     * 订单支付状态
     */
    public function payStatus($status)
    {
        $array = [
            'pay' => '已支付',
            'npay' => '未完成支付环节',
            'expire' => '超出支付时间',
            'delete' => '删除',
            'cancel' => '取消',
            2 => '已完成',
            1 => '已支付',
            0 => '未完成支付环节',
            -2 => '删除',
            -1 => '超出支付时间',
        ];
        foreach ($array as $k => $v) {
            if ($status == $k) {
                return $v;
            }
        }
    }

    /*
     * 订单品牌
     */
    public function productInfo($item)
    {
        $type = is_array($item) ? $item[2] : $item->type;
        $product_id = is_array($item) ? $item[1] : $item->product_id;
        $return = [];
        switch ($type) {
            case 'vip':
                break;
            case 'video_reward':
                break;
            case 'live_reward':
                break;
            case 'video':
                $return = Video::where('id', $product_id)->first();
                if (!$return) {
                    return [];
                }
                break;
            //v020700新增资讯
            case 'news':
                $return = NewsEntity::where('id', $product_id)->first();
                if (!$return) {
                    return [];
                }
                break;
            case 'brand':
                $return = \App\Models\Brand\Entity::productInfo($product_id, 'brand');
                if (!$return) {
                    return [];
                }
                $obj = new _v020400();
                $return->investment_min = formatMoney($return->investment_min);
                $return->investment_max = formatMoney($return->investment_max);
                $return->investment_arrange = $return->investment_min . '万-' . $return->investment_max . '万';
                $return->zone_name = $obj->formatZoneName($return->zone_name);
                $return->remark = $obj->getBrandRemark($return->activity_id);
                $return->logo = getImage($return->logo);
                if ($return->keywords) {
                    $return->keywords = strpos($return->keywords, ' ') !== FALSE ? explode(' ', $return->keywords) : [$return->keywords];
                } else {
                    $return->keywords = [];
                }
                $return->product_name = $return->title ?: '';
                break;
            case 'brand_goods':
                $return = \App\Models\Brand\Entity::productInfo($product_id, 'brand');
                if (!$return) {
                    return [];
                }
                $obj = new _v020400();
                $return->investment_min = formatMoney($return->investment_min);
                $return->investment_max = formatMoney($return->investment_max);
                $return->investment_arrange = $return->investment_min . '万-' . $return->investment_max . '万';
                $return->zone_name = $obj->formatZoneName($return->zone_name);
                $return->remark = $obj->getBrandRemark($return->activity_id);
                $return->logo = getImage($return->logo);
                if ($return->keywords) {
                    $return->keywords = strpos($return->keywords, ' ') !== FALSE ? explode(' ', $return->keywords) : [$return->keywords];
                } else {
                    $return->keywords = [];
                }
                $return->product_name = $return->title ?: '';
                break;
            default:
                break;
        }
        return $return;
    }

    /*
     * 我的意向品牌
     */
    public function postIntentBrands(Requests\User\IntentRequest $request, $version = null)
    {
        $data = $request->input();
        $page = $request->input('page', 1);
        $pageSize = $request->input('pageSize', 15);
        $keywords = $request->input('keywords', '');
        if ($version) {
            $versionService = $this->init(__METHOD__, $version);
            $result = $versionService->bootstrap($data, ['page' => $page, 'page_size' => $pageSize, 'keywords' => $keywords]);
            return AjaxCallbackMessage($result, true);
        }

        return AjaxCallbackMessage('该接口停用', false);
    }


    /*
     * 我的品牌浏览记录
     */
    public function postBrandsBrowse(Requests\User\BrandsBrowseRequest $request, $version = null)
    {
        $data = $request->input();
        $page = $request->input('page', 1);
        $pageSize = $request->input('pageSize', 15);
        $type = $request->input('type', 'brand');
        $_version = $request->input('_version', null);
        if ($version) {
            $versionService = $this->init(__METHOD__, $version, $_version);
            $result = $versionService->bootstrap($data, ['page' => $page, 'page_size' => $pageSize, 'type' => $type]);
            return AjaxCallbackMessage($result, true);
        }

        return AjaxCallbackMessage('该接口停用', false);
    }


    /*
     * 添加浏览记录
     */
    public function postAddBrowse(Requests\User\AddBrowseRequest $request, $version = null)
    {
        $data = $request->input();
        if ($version) {
            $versionService = $this->init(__METHOD__, $version);
            $result = $versionService->bootstrap($data);
            return AjaxCallbackMessage($result['data'], $result['status']);
        }

        return AjaxCallbackMessage('该接口停用', false);
    }


    /*
    * 分享次数加1
    */
    public function postAddShareCount(Requests\User\AddShareCountRequest $request, $version = null)
    {
        $data = $request->input();
        if ($version) {
            $versionService = $this->init(__METHOD__, $version);
            $result = $versionService->bootstrap($data);
            return AjaxCallbackMessage($result['data'], $result['status']);
        }

        return AjaxCallbackMessage('该接口停用', false);
    }


    /*
     * 创业基金
     */
    public function postMyfundlist(Request $request, $version = null)
    {
        $data = $request->input();
        if($version && isset($data['contract_id'])){
            //初始化
            $versionService = $this->init(__METHOD__, $version);

            if ($versionService) {
                $response = $versionService->bootstrap($data);
                return AjaxCallbackMessage($response['message'], $response['status']);
            }

            return AjaxCallbackMessage('api接口不再维护', false);
        }

        $uid = $request->input('uid', '');
        $page_size = $request->input('page_size', 10);


        if (empty($uid)) {
            return AjaxCallbackMessage('uid不能为空', false);
        }

        $uid = (int)abs($uid);

        $list = Fund::baseQuery($page_size, function ($builder) use ($uid) {
            $data = $builder->where('user_fund.uid', $uid)
                ->join('brand', 'brand.id', '=', 'user_fund.brand_id')
                ->select('brand.name', 'user_fund.fund', 'user_fund.created_at', 'user_fund.code')
                ->paginate(10);

            return $data;
        }, $this->formatFundList());

        return AjaxCallbackMessage($list->items(), true);
    }

    /*
     * 格式化列表
     */
    private function formatFundList()
    {
        $func = function ($data) {
            foreach ($data as $k => $v) {
                $v->expire_time = date('Y-m-d H:i', $v->created_at + 6 * 30 * 24 * 3600);
                $v->serial_no = $v->code;
            }

            return $data;
        };

        return $func;
    }

    /*
     * 用户提现列表
     */
    public function postWithdrawlist(Request $request, $version = null)
    {
        $data = $request->input();

        //初始化
        $versionService = $this->init(__METHOD__, $version);

        if ($versionService) {
            $response = $versionService->bootstrap($data, ['request' => $request]);
            return AjaxCallbackMessage($response['message'], $response['status']);
        }

        return AjaxCallbackMessage('api接口不再维护', false);
    }

    /*
     * 提现详情
     */
    public function postWithdrawdetail(Request $request, $version = null)
    {
        $data = $request->input();

        //初始化
        $versionService = $this->init(__METHOD__, $version);

        if ($versionService) {
            $response = $versionService->bootstrap($data, ['request' => $request]);
            return AjaxCallbackMessage($response['message'], $response['status']);
        }

        return AjaxCallbackMessage('api接口不再维护', false);
    }

    /*
     * 积分提现
     */
    public function postWithdraw(Request $request, $version = null)
    {
        $data = $request->input();

        //初始化
        $versionService = $this->init(__METHOD__, $version);

        if ($versionService) {
            $response = $versionService->bootstrap($data, ['request' => $request]);
            return AjaxCallbackMessage($response['message'], $response['status']);
        }

        return AjaxCallbackMessage('api接口不再维护', false);
    }

    /*
     * 获取用户提现账号记录
     */
    public function postWithdrawrecord(Request $request, $version = null)
    {
        $data = $request->input();

        //初始化
        $versionService = $this->init(__METHOD__, $version);

        if ($versionService) {
            $response = $versionService->bootstrap($data, ['request' => $request]);
            return AjaxCallbackMessage($response['message'], $response['status']);
        }

        return AjaxCallbackMessage('api接口不再维护', false);
    }

    /*
     * 接收用户填写邀请码
     */
    public function postWritecode(Request $request, $version = null)
    {
        $data = $request->input();

        //初始化
        $versionService = $this->init(__METHOD__, $version);

        if ($versionService) {
            $response = $versionService->bootstrap($data, ['request' => $request, '_uid' => $this->_uid]);
            return AjaxCallbackMessage($response['message'], $response['status']);
        }

        return AjaxCallbackMessage('api接口不再维护', false);
    }

    /*
     * 用户信息(020500新增)
     */
    public function postUserinfoext(Request $request, $version = null)
    {
        if ($version && !in_array($version, ['_v020500', '_v020502', '_v020600', '_v020602'])) {
            //初始化
            $versionService = $this->init(__METHOD__, $version);
            $data = $request->input();
            if ($versionService) {
                $response = $versionService->bootstrap($data);
                return AjaxCallbackMessage($response['message'], $response['status']);
            }
            return AjaxCallbackMessage('api接口不再维护', false);
        }
        $uid = $request->input('uid', '');

        if (!($user = Entity::where('uid', $uid)->first())) {
            return ['message' => '非法的uid', 'status' => false];
        }

        //无界币
        $return['currency'] = $user->currency;

        //分享次数
        $share_currency_count = \DB::table('currency_log')
            ->where('uid', $uid)
            ->whereIn('action', ['share_distribution', 'relay_distribution'])
            ->count();

        $share_score_count = \DB::table('score_log')
            ->where('uid', $uid)
            ->whereIn('type', ['share_distribution', 'relay_distribution'])
            ->count();

        $return['share_count'] = $share_currency_count + $share_score_count;

        //阅读量
        $currency_reward_count = \DB::table('currency_log')
            ->where('uid', $uid)
            ->where('action', 'view_distribution')
            ->count();

        $score_reward_count = \DB::table('score_log')
            ->where('uid', $uid)
            ->where('type', 'view_distribution')
            ->count();

        $return['read_count'] = $currency_reward_count + $score_reward_count;


        //意向客户
        $intend_brand_ids = \DB::table('distribution_log as a')
            ->where('a.uid', $uid)
            ->where('a.relation_type', 'brand')
            ->where('a.genus_type', 'intent')
            ->select('a.id')
            ->get();

        $return['intend_count'] = count($intend_brand_ids);

        //累计佣金
        $currency = \DB::table('currency_log')
            ->where('uid', $uid)
            ->where('operation', 1)
            ->select(\DB::raw('SUM(num) as currency_total'))
            ->first();

        $return['currency_total'] = $currency ? ($currency->currency_total ?: 0) : 0;

        $return['is_done_invitecode'] = $user->register_invite ? 1 : 0;

        $arr = json_decode((new MessageController())->postUnreadcounts($request), true);
        $return['unread_messages'] = array_get($arr, 'message', 0);

        //有多少人填写了我的邀请码
        $return['invite_count'] = Entity::where('register_invite', $user->my_invite)->count();
        //今天是否签到
        $return['is_sign'] = ScoreLog::typeCount($user->uid, 'user_sign', date('Y-m-d')) ? 1 : 0;
        if ($user->serial_sign > 0 && !ScoreLog::typeCount($user->uid, 'user_sign', date('Y-m-d', time() - 86400))) {//未连续
            $user->update(['serial_sign' => 0]);
        }
        //连续签到几次
        $return['serial_sign'] = $user->serial_sign;
        //本次签到赠送
        if ($user->serial_sign >= 30) {
            $return['sign_score'] = 100;
        } else {
            $return['sign_score'] = ScoreLog::typeCount($user->uid, 'user_sign') == 0 ? 10 : min($user->serial_sign * 5 + 5, 150);
        }
        //已经提取佣金
        $return['extracted'] = \App\Models\User\Withdraw::where('uid', $uid)->where('status', '!=', 'fail')->sum(\DB::raw('if(status="pending",money,actual)'));

        return AjaxCallbackMessage($return, true);
    }

    //签到
    public function postSign(Request $request, $version = null)
    {

        //如果版本大于2.7就走版本控制
        if (substr($version, -5) >= 20700) {
            $data = $request->input();
            $_version = $request->input('_version', null);
            if ($version) {
                $versionService = $this->init(__METHOD__, $version, $_version);
                $result = $versionService->bootstrap($data);
                return AjaxCallbackMessage($result['message'], $result['status']);
            }

            return AjaxCallbackMessage('该接口停用', false);
        }


        $uid = $request->input('uid', '');
        if (!($user = Entity::where('uid', $uid)->first())) {
            return ['message' => '非法的uid', 'status' => false];
        }
        if (ScoreLog::typeCount($user->uid, 'user_sign', date('Y-m-d'))) {
            return ['message' => '今天已经签到过', 'status' => false];
        }
        $serial_sign = $user->serial_sign + 1;
        //首次
        if (!ScoreLog::typeCount($user->uid, 'user_sign')) {
            $num = 10;
            $msg = '首次签到成功';
        } elseif (ScoreLog::typeCount($user->uid, 'user_sign', date('Y-m-d', time() - 86400))) {//连续
            $num = min($serial_sign * 5, 150);
            if ($serial_sign > 30) {
                $num = 100;
            }
            $msg = '第' . $serial_sign . '次签到成功';
        } else {
            $num = 5;
            $msg = '签到成功';
            $serial_sign = 1;
        }
        if (!Entity::where('uid', $uid)
            ->where('serial_sign', '=', $user->serial_sign)
            ->update(['serial_sign' => $serial_sign])
        ) {
            return ['message' => '签到失败', 'status' => false];
        }
        //赠送积分
        ScoreLog::add($user->uid, $num, 'user_sign', $msg);
        $reward_num = 0;
        switch ($serial_sign) {//额外赠送
            case 7:
                $reward_num = 20;
                break;
            case 15:
                $reward_num = 50;
                send_notification('这么勤快，额外再赠送50积分！', '好腻害！已经连续签到15天了，去积分商城看看吧！', json_encode(['type' => 'duiba']), $user);
                break;
            case 30:
                $reward_num = 100;
                break;
        }
//        $reward_num > 0 && ScoreLog::add($user->uid, $reward_num, 'user_sign', '连续签到' . $serial_sign . '天额外奖励');
        $reward_num > 0 && ScoreLog::add($user->uid, $reward_num, 'user_sign_reward', '连续签到' . $serial_sign . '天额外奖励');
        return AjaxCallbackMessage($msg, true);
    }

    /*
    * 抽奖
    */
    public function postLottery(Request $request, $version = null)
    {
        $data = $request->input();

//        $platform = $this->isPc($request);
//        if($platform=='pc'){
//            return AjaxCallbackMessage('非法访问', false);
//        }

        //初始化
        $versionService = $this->init(__METHOD__, $version);

        if ($versionService) {
            $response = $versionService->bootstrap($data, ['request' => $request]);
            return AjaxCallbackMessage($response['message'], $response['status']);
        }

        return AjaxCallbackMessage('api接口不再维护', false);
    }

    /**
     * 判断是否来自pc的访问
     */
    private function isPc(Request $request)
    {
        $agent = $request->header('USER_AGENT');
        if (strpos($agent, 'MicroMessenger') !== false) {//微信内置
            $platform = 'weixin';
        } elseif (strpos($agent, 'android') !== false) {//安卓
            $platform = 'android';
        } elseif (strpos($agent, 'iPhone') !== false) {//iPhone
            $platform = 'iPhone';
        } elseif (strpos($agent, 'iPod') !== false) {//iPod
            $platform = 'iPod';
        } elseif (preg_match('/mozilla|m3gate|winwap|openwave|Windows NT|Windows 3.1|95|Blackcomb|98|ME|XWindow|ubuntu|Longhorn|AIX|Linux|AmigaOS|BEOS|HP-UX|OpenBSD|FreeBSD|NetBSD|OS\/2|OSF1|SUN/i', $agent)) {
            $platform = 'pc';
        } else {
            $platform = 'other';
        }

        return $platform;

    }


    /*
    * 还剩下多少机会
    */
    public function postSurplus(Request $request, $version = null)
    {
        $data = $request->input();
//        $platform = $this->isPc($request);
//        if($platform=='pc'){
//            return AjaxCallbackMessage('非法访问', false);
//        }

        //初始化
        $versionService = $this->init(__METHOD__, $version);

        if ($versionService) {
            $response = $versionService->bootstrap($data, ['request' => $request]);
            return AjaxCallbackMessage($response['message'], $response['status']);
        }

        return AjaxCallbackMessage('api接口不再维护', false);
    }


    /*
    * 抽奖记录
    */
    public function postRecords(Request $request, $version = null)
    {
        $data = $request->input();
        $page = $request->get('page', 1);
        $page_size = $request->get('page_size', 10);
        $page == 1 && $page_size = 9;

        //初始化
        $versionService = $this->init(__METHOD__, $version);

        if ($versionService) {
            $response = $versionService->bootstrap($data, ['request' => $request, 'page_size' => $page_size, 'page' => $page]);
            return AjaxCallbackMessage($response['message'], $response['status']);
        }

        return AjaxCallbackMessage('api接口不再维护', false);
    }


    /*
    * 接收用户填写邀请码
    */
    public function postDoubt(Request $request, $version = null)
    {
        $data = $request->input();

        //初始化
        $versionService = $this->init(__METHOD__, $version);

        if ($versionService) {
            $response = $versionService->bootstrap($data, ['request' => $request]);
            return AjaxCallbackMessage($response['message'], $response['status']);
        }

        return AjaxCallbackMessage('api接口不再维护', false);
    }

    //我的经纪人

    public function postAgents(Request $request, $version = null)
    {
        $data = $request->input();

        //初始化
        $versionService = $this->init(__METHOD__, $version);

        if ($versionService) {
            $response = $versionService->bootstrap($data, ['request' => $request]);
            return AjaxCallbackMessage($response['message'], $response['status']);
        }
        return AjaxCallbackMessage('api接口不再维护', false);
    }

    //成单品牌
    public function postSuccessBrands(Request $request, $version = null)
    {
        $data = $request->input();

        //初始化
        $versionService = $this->init(__METHOD__, $version);

        if ($versionService) {
            $response = $versionService->bootstrap($data, ['request' => $request]);
            return AjaxCallbackMessage($response['message'], $response['status']);
        }
        return AjaxCallbackMessage('api接口不再维护', false);
    }

    //成单品牌
    public function postFollowedBrands(Request $request, $version = null)
    {
        $data = $request->input();

        //初始化
        $versionService = $this->init(__METHOD__, $version);

        if ($versionService) {
            $response = $versionService->bootstrap($data, ['request' => $request]);
            return AjaxCallbackMessage($response['message'], $response['status']);
        }
        return AjaxCallbackMessage('api接口不再维护', false);
    }

    /*我的经纪人*/
    public function postFollowedAgents(Request $request, $version = null)
    {
        $data = $request->input();

        //初始化
        $versionService = $this->init(__METHOD__, $version);

        if ($versionService) {
            $response = $versionService->bootstrap($data, ['request' => $request]);
            return AjaxCallbackMessage($response['message'], $response['status']);
        }
        return AjaxCallbackMessage('api接口不再维护', false);
    }

    //我的活动邀请函列表
    public function postActivityInvites(Request $request, $version = null)
    {
        $data = $request->input();
        //初始化
        $versionService = $this->init(__METHOD__, $version);

        if ($versionService) {
            $response = $versionService->bootstrap($data, ['request' => $request]);
            return AjaxCallbackMessage($response['message'], $response['status']);
        }
        return AjaxCallbackMessage('api接口不再维护', false);
    }

    /**
     * 我的考察邀请函列表
     *
     * @param Request $request
     * @param null $version
     * @return string author zhaoyf
     *
     */
    public function postInspectInvites(Request $request, $version = null)
    {
        $result = $request->input();
        $versionService = $this->init(__METHOD__, $version);

        if ($versionService) {
            $response = $versionService->bootstrap($result, ['request' => $request]);

            return AjaxCallbackMessage($response['message'], $response['status']);
        }

        return AjaxCallbackMessage('该接口不存在', false);
    }

    //我的邀请人
    /*
     * shiqy
     * */
    public function postMyInviter(Request $request, $version = null)
    {
        $data = $request->input();
        //初始化
        $versionService = $this->init(__METHOD__, $version);

        if ($versionService) {
            $response = $versionService->bootstrap($data, ['request' => $request]);
            return AjaxCallbackMessage($response['message'], $response['status']);
        }
        return AjaxCallbackMessage('api接口不再维护', false);
    }

    //我的全部经纪人
    /*
     * shiqy
     * */
    public function postMyAllAgents(Request $request, $version = null)
    {
        $data = $request->input();
        //初始化
        $versionService = $this->init(__METHOD__, $version);

        if ($versionService) {
            $response = $versionService->bootstrap($data, ['request' => $request]);
            return AjaxCallbackMessage($response['message'], $response['status']);
        }
        return AjaxCallbackMessage('api接口不再维护', false);
    }


    //如果客户没有邀请人，那么可以调用此接口进行主动绑定邀请人
    /*该接口与   填写邀请码  接口重复，调用那个接口，此接口作废
     * shiqy
     *
     * */
    public function postBindInviter(Request $request, $version = null)
    {
        return AjaxCallbackMessage('api接口不再维护', false);


        $data = $request->input();
        //初始化
        $versionService = $this->init(__METHOD__, $version);

        if ($versionService) {
            $response = $versionService->bootstrap($data, ['request' => $request]);
            return AjaxCallbackMessage($response['message'], $response['status']);
        }
        return AjaxCallbackMessage('api接口不再维护', false);
    }

    /**
     *  author zhaoyf
     *
     * c端额外补充字符数据
     *
     * @param Request $request
     * @param null $version
     *
     * @return string
     */
    public function postAddNewInfo(Request $request, $version = null)
    {
        $data = $request->input();

        //初始化
        $versionService = $this->init(__METHOD__, $version);

        if ($versionService) {
            $response = $versionService->bootstrap($data, ['request' => $request]);
            return AjaxCallbackMessage($response['message'], $response['status']);
        }
        return AjaxCallbackMessage('api接口不再维护', false);
    }


    /*********** 经纪人 ************/

    /**
     * 经纪人详情
     *
     * @param Requests\Agent\CustomerRequest $request
     * @param customer_id  投资人ID
     * @param agent_id     被查看的经纪人ID
     * @return data_list
     */
    public function postDetails(Requests\Agent\CustomerRequest $request, $version = null)
    {
        $data = $request->input();

        $versionService = $this->init(__METHOD__, $version);
        if ($versionService) {
            $response = $versionService->bootstrap($data, ['request' => $request]);

            return AjaxCallbackMessage($response['message'], $response['status']);
        }
        return AjaxCallbackMessage('该接口不存在', false);
    }


    /**
     * 客户评价玩经纪人领取积分
     *
     * @param Request $request
     * @param null $version
     * @return string
     * @author tangjb
     */
    public function postFetchScore(Request $request, $version = null)
    {
        $data = $request->input();

        $versionService = $this->init(__METHOD__, $version);
        if ($versionService) {
            $response = $versionService->bootstrap($data, ['request' => $request]);

            return AjaxCallbackMessage($response['message'], $response['status']);
        }
        return AjaxCallbackMessage('该接口不存在', false);
    }



    /**
     * 考察邀请函支付成功
     *
     * @param Request $request
     * @param null $version
     * @return string
     * @author tangjb
     */
    public function postInspectPaySuccess(Request $request, $version = null)
    {
        $data = $request->input();

        $versionService = $this->init(__METHOD__, $version);
        if ($versionService) {
            $response = $versionService->bootstrap($data, ['request' => $request]);

            return AjaxCallbackMessage($response['message'], $response['status']);
        }
        return AjaxCallbackMessage('该接口不存在', false);
    }




    /**
     * 等待经纪人接单
     *
     * @param Request $request
     * @param null $version
     * @return string
     * @author tangjb
     */
    public function postWaitAccept(Request $request, $version = null)
    {
        $data = $request->input();

        if (empty($data['brand_id'])) {
            return AjaxCallbackMessage('缺少品牌id', false);
        }


        if (empty($data['uid'])) {
            return AjaxCallbackMessage('缺少用户id', false);
        }

        $versionService = $this->init(__METHOD__, $version);
        if ($versionService) {
            $response = $versionService->bootstrap($data);

            return AjaxCallbackMessage($response['message'], $response['status']);
        }
        return AjaxCallbackMessage('该接口不存在', false);
    }



    /*
     * 用户红包列表
    */
    public function postPackagelist(Request $request, $version = null)
    {
        $data = $request->input();

        //初始化
        $versionService = $this->init(__METHOD__, $version);

        if ($versionService) {
            $response = $versionService->bootstrap($data);
            return AjaxCallbackMessage($response['message'], $response['status']);
        }

        return AjaxCallbackMessage('api接口不再维护', false);
    }

    /*
     * 兑换红包
    */
    public function postSwapPackage(Request $request, $version = null)
    {
        $data = $request->input();

        //初始化
        $versionService = $this->init(__METHOD__, $version);

        if ($versionService) {
            $response = $versionService->bootstrap($data);
            return AjaxCallbackMessage($response['message'], $response['status']);
        }

        return AjaxCallbackMessage('api接口不再维护', false);
    }

    /*
     * 红包详情
     */
    public function postPackageDetail(Request $request, $version = null)
    {
        $data = $request->input();

        //初始化
        $versionService = $this->init(__METHOD__, $version);

        if ($versionService) {
            $response = $versionService->bootstrap($data);
            return AjaxCallbackMessage($response['message'], $response['status']);
        }

        return AjaxCallbackMessage('api接口不再维护', false);
    }


    /**
     * 现金提现申请
     *
     * @param Request $request
     * @param null $version
     * @return string
     * @author tangjb
     */
    public function postMoneyWithdraw(Request $request, $version = null)
    {
        $data = $request->input();

        //初始化
        $versionService = $this->init(__METHOD__, $version);

        if ($versionService) {
            $response = $versionService->bootstrap($data);
            return AjaxCallbackMessage($response['message'], $response['status']);
        }

        return AjaxCallbackMessage('api接口不再维护', false);
    }



    /**
     * 现打开邀请函
     *
     * @param Request $request
     * @param null $version
     * @return string
     * @author tangjb
     */
    public function postOpenInvitation(Request $request, $version = null)
    {
        $data = $request->input();

        //初始化
        $versionService = $this->init(__METHOD__, $version);

        if ($versionService) {
            $response = $versionService->bootstrap($data);
            return AjaxCallbackMessage($response['message'], $response['status']);
        }

        return AjaxCallbackMessage('api接口不再维护', false);
    }



    /**
     * 打开合同
     *
     * @param Request $request
     * @param null $version
     * @return string
     * @author tangjb
     */
    public function postOpenContract(Request $request, $version = null)
    {
        $data = $request->input();

        //初始化
        $versionService = $this->init(__METHOD__, $version);

        if ($versionService) {
            $response = $versionService->bootstrap($data);
            return AjaxCallbackMessage($response['message'], $response['status']);
        }

        return AjaxCallbackMessage('api接口不再维护', false);
    }


}
