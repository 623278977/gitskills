<?php

namespace App\Services\Version\Activity;

use App\Models\Activity\Live;
use App\Models\Agent\AgentCurrencyLog;
use App\Models\User\Praise;
use App\Services\Version\VersionSelect;
use App\Models\Activity\Entity as Activity;
use App\Models\Order\Entity as Order;
use App\Models\Activity\Sign;
use App\Models\ScoreLog;
use App\Models\Activity\Ticket;
use App\Models\Maker\Entity as Maker;
use App\Models\User\Entity as User;
use App\Models\Brand\Entity as Brand;
use App\Models\Vip\Entity as Vip;
use App\Models\Vip\User as Vip_User;
use App\Models\Vip\Term as ViP_term;
use App\Models\User\Ticket as User_Ticket;
use DB;
use App\Services\ActivityService;

class _v020400 extends VersionSelect
{
    //static $enable = FALSE;  //版本是否启用

    /**
     * 活动报名列表  --数据中心版
     * @User yaokai
     * @param array $param
     * @return array|string
     */
    public function postSignuserlist($param = [])
    {
        $activity_id = $param['activity_id'];

        if (!$activity_id) {
            return ['status' => false, 'message' => '活动id不能为空'];
        }

        if (floor($activity_id) != $activity_id || $activity_id <= 0) {
            return AjaxCallbackMessage('活动id必须要为正整数', false);
        }

        //报名人数
        $uid = $param['uid'] ?: '';
        $ticket_info = Sign::getSignUserData($activity_id, $uid);//dd($ticket_info);

        $user_ticket_info_arr = $user_ticket_info = [];

        foreach ($ticket_info as $item) {
            $user_ticket_info = [];
            $user_ticket_info['name'] = $item->name ? $item->name : ($item->realname ? $item->realname : ($item->nickname ? $item->nickname : $item->name));
            $user_ticket_info['price'] = $item->real_ticket_price ? $item->real_ticket_price : $item->price;
            $user_ticket_info['type_name'] = $item->real_ticket_type ? ($item->real_ticket_type == 1 ? '现场票' : '直播票') : '现场票';
            $user_ticket_info['avatar'] = $item->avatar ? getImage($item->avatar) : getImage($item->image);
            $user_ticket_info['sign_time'] = timeDiff($item->created_at);
            if ($item->user_non_reversible != $item->sign_non_reversible) {
                $user_ticket_info['avatar'] = \Illuminate\Support\Facades\URL::asset('/') . "images/default/avator-m.png";
            }

            $user_ticket_info_arr[] = $user_ticket_info;
        }


        //去重
        //$data = unique_arr($user_ticket_info_arr);
        $data = $user_ticket_info_arr;

        return ['status' => true, 'message' => $data];
    }

    /*
     * 活动详情页
     */
    public function postDetail($param = [])
    {
        $activity_id = (int)$param['id'];

        if (!$activity_id) {
            return ['status' => false, 'message' => '活动id不能为空'];
        }

        if ($activity_id <= 0) {
            return AjaxCallbackMessage('活动id必须要为正整数', false);
        }

        $detail = Activity::activityDetail_v020400($activity_id);

        if (count($detail) == 0) {
            return ['status' => false, 'message' => $detail];
        }

        $data['id'] = $activity_id;

        //活动名称
        $data['subject'] = $detail->subject;

        //活动图片
        $data['detail_img'][] = getImage($detail->list_img, '', '');

        //专版会员
        $data['is_vip'] = $detail->vip_id ? 1 : 0;
        $vip = Vip::find($detail->vip_id);
        $vipUser = Vip_User::where('uid', $param['uid'])->where('vip_id', $detail->vip_id)->first();
        $data['can_buy'] = $vipUser ? ($vipUser->end_time > time() ? 0 : 1) : 1;

        if ($data['is_vip'] == 0) {
            $data['can_buy'] = 0;
        }

        $data['vip_name'] = $vip ? $vip->name : '';
        $data['vip_id'] = $detail->vip_id ? 1 : 0;

        //是否可以邀请好友
        $data['is_shareable'] = Activity::canShare($activity_id);

        //是否已经收藏
        $data['is_collect'] = $param['uid'] ? Activity::isFavorite($activity_id, $param['uid']) : 0;

        //分享图标
        $data['share_image'] = getImage($detail->share_image ?: 'images/share_image.png', '', '');

        //活动时间
        //$data['begin_time'] = date('Y年m月d日 H:i', $detail->begin_time);
        $data['begin_time'] = $detail->begin_time;
        $data['end_time'] = $detail->end_time;

        $data['begin_time_content']['begin_time'] = $data['begin_time'];
        $data['begin_time_content']['time_explain'] = $detail->time_explain;

        $period = bcdiv(abs($detail->end_time - $detail->begin_time), 3600, 1);

        if (strrpos('0', $period) == 0) {
            $period = str_replace('.0', '', $period);
        }

        $data['begin_time_content']['period'] = $period;

        //活动地点
        $maker_ids = strpos($detail->maker_ids, ',') !== false ? explode(',', $detail->maker_ids) : [$detail->maker_ids];
        $maker_info = Maker::getMakerInfo($maker_ids);

        $position = [];

        foreach ($maker_info as &$maker) {

            if (!$maker['zone']) {
                continue;
            }
            $position[] = str_replace('市', '', $maker['zone']);
            unset($maker['image'], $maker['logo'], $maker['image'], $maker['uid'], $maker['groupid'], $maker['alpha']);
        }

        $data['activity_location_arr'] = $maker_info;
        $data['activity_location'] = implode('、', count($position) > 5 ? array_slice($position, 0, 5) : $position);
        if (count($position) > 5) {
            $data['activity_location'] = $data['activity_location'] . '等';
        }

        //活动费用
        $ticket_info = Ticket::getMinTicket($activity_id);

        //价格最低的现场票
        $data['min_ticket_price'] = $ticket_info;
        $data['min_ticket_price_type'] = '现场票';//默认值

        if ($data['min_ticket_price'] == '免费') {
            $data['ticket_id'] = Ticket::where('activity_id', $activity_id)->where('type', 1)->where('status', 1)->first()->id;
        } else {
            $data['ticket_id'] = 0;
        }

        /*
        foreach ($ticket_price_arr as $key => $value) {
            if ($value == $data['min_ticket_price']) {
                $data['min_ticket_price_type'] = Ticket::getType($key);
                break;
            }
        }
        */

        //报名人数
        $data['sign_count'] = $detail->sign_count;

        $data['description'] = $detail->description;

        //活动嘉宾
        //$data['guest_info'] = Guest::getActivityGuests($activity_id);

        //相关品牌
        $brand = Brand::baseLists(
            'activityBrandList',
            ['activity_id' => $activity_id],
            function ($builder) {
                $builder->select(
                    'id',
                    'uid',
                    'logo',
                    'name',
                    'investment_min',
                    'investment_max',
                    'keywords',
                    DB::raw('(select concat_ws(",",id,name) from lab_zone WHERE lab_zone.id = lab_brand.zone_id) as zone_name'),
                    DB::raw('(select if(GROUP_CONCAT(activity_id), GROUP_CONCAT(activity_id), 0) from lab_activity_brand WHERE lab_activity_brand.brand_id = lab_brand.id) as activity_id'),
                    DB::raw('(select name from lab_categorys as c where c.id = lab_brand.categorys1_id ) AS category_name')
                );

                return $builder;
            },
            function ($data) {
                $obj = new \App\Services\Version\Brand\_v020400();
                foreach ($data as $item) {
                    $item->investment_min = formatMoney($item->investment_min);
                    $item->investment_max = formatMoney($item->investment_max);
                    $item->logo = getImage($item->logo);
                    $item->investment_arrange = $item->investment_min . '万-' . $item->investment_max . '万';
                    $item->zone_name = $obj->formatZoneName($item->zone_name);
                    //$item->remark = getBrandRemark($item->activity_id);
                    //$item->industry_ids = $this->getBrandIndustry($item->industry_ids);
                    if ($item->keywords) {
                        $item->keywords = strpos($item->keywords, ' ') !== false ? explode(' ', $item->keywords) : [$item->keywords];
                    } else {
                        $item->keywords = [];
                    }
                }

                return $data;
            }
        );

        $data['brand'] = $brand;

        //热度
        $data['view_count'] = $detail->view;
        $data['zan_count'] = $detail->zan_count;
        $data['comment_count'] = $detail->comment_count;
        $data['share_count'] = $detail->share_num;
        $data['hot_count'] = $detail->view * 0.5 + $detail->zan_count * 0.5 + $detail->comment_count * 10 + $detail->share_num;

        //点赞
        $zans = Praise::getActivityZan($activity_id);
        foreach ($zans as $zan) {
            $zan->avatar = getImage($zan->avatar ?: $zan->image);
        }
        $data['zans'] = $zans;

        //评论,调用以前接口

        return ['status' => true, 'message' => $data];
    }

    /**
     * 活动报名签到(半开放签到和标准)   --弃用  数据中心不处理
     * @User
     * @param array $param
     * @return array
     */
    public function postTempsign($param = [])
    {
        $uid = $param['uid'];
        $activity_id = $param['activity_id'];
        $activity = Activity::where('id', $activity_id)->first();
        //找出这个活动相同标签的活动
        $tag_id = $activity->tag_id;
        $activitys = Activity::where('tag_id', $tag_id)->get()->toArray();
        $activity_ids = array_pluck($activitys, 'id');//这个标签所有的关联活动id

        //用户报名状态
//        $exist = Sign::getRow(['uid' => $uid, 'status' => 0, 'maker_id' => $param['maker_id'], 'activity_id' => $param['activity_id']]);
        $exist = Sign::where('uid', $uid)
            ->where('status', '0')
            ->where('maker_id', $param['maker_id'])
            ->whereIn('activity_id', $activity_ids)
            ->get();
        $this->updateStatus($exist, $uid, $param['activity_id'], $param['maker_id'], $param['name'], $param['tel'], encryptTel($param['tel']));

        $activity = Activity::where('id', $param['activity_id'])->first();

        return ['status' => true, 'message' => $activity->subject . '@' . date('H:i', $activity->begin_time)];
    }

    /**
     * 活动签到   --数据中心版
     * @User
     * @param array $param
     * @return array
     */
    public function postSign($param = [])
    {
        $uid = $param['uid'];

        if (!\App\Models\User\Entity::checkAuth($uid)) {
            return ['status' => false, 'message' => '账号异常'];
        }

        $activity_id = trim($param['activity_id']);
        $maker_id = trim($param['maker_id']);

        //活动类型
        $activity = Activity::where('id', $activity_id)->first();
        //找出这个活动相同标签的活动
        $tag_id = $activity->tag_id;
        $activitys = Activity::where('tag_id', $tag_id)->get()->toArray();
        $activity_ids = array_pluck($activitys, 'id');//这个标签所有的关联活动id
        //用户报名状态
        $exist = Sign::where(['uid' => $uid, 'maker_id' => $maker_id])
            ->whereIn('activity_id', $activity_ids)
            ->whereIn('status', [0, 1])
            ->get();
        $status = '';
        if ($exist) {
            foreach ($exist as $v) {
                $status = $v->status;
            }
        }
        switch ($activity->sign) {
            //全开放,只统计人数
            case 2 :
                $user = User::where('uid', $uid)->first();
                if ($user) {
                    $name = $user->nickname ?: $user->username;
                    $tel = $user->username;
                    $non_reversible = $user->non_reversible;
                } else {
                    return ['status' => false, 'message' => '用户未找到'];
                }
                //报名状态更新
                $sign = $this->updateStatus($exist, $uid, $activity_id, $maker_id, $name, $tel, $non_reversible);
                if ($sign) {
                    $return = ['status' => false, 'message' => '该会员已经签到'];
                } else {
                    $return = ['status' => true, 'message' => $activity->subject . '@' . date('H:i', $activity->begin_time)];
                }

                break;

            //半开放,需要手机号码验证
            case 1:
                //是否已经报名
                $sign = Sign::where('uid', $uid)
                    ->whereIn('activity_id', $activity_ids)
                    ->where('maker_id', $maker_id)
                    ->where('status', 0)
                    ->get()->toArray();
                if (!empty($sign)) {
                    return ['status' => true, 'message' => $this->getSignInfo($uid, $maker_id, $activity_id, $activity, $activity_ids)];
                }

                if ($status == 1) {
                    $return = ['status' => false, 'message' => '该会员已经签到'];
                } else {
                    $return = ['status' => true, 'message' => 'half_open'];
                }
                break;

            //标准
            case 0:
                if (count($exist) == 0) {
                    return ['status' => false, 'message' => '没有在该会场报名活动'];
                }

                if ($status == 1) {
                    return ['status' => false, 'message' => '该会员已经签到'];
                }


                $return = ['status' => true, 'message' => $this->getSignInfo($uid, $maker_id, $activity_id, $activity, $activity_ids)];

                break;

            default:
                $return = ['status' => false, 'message' => '活动签到限制异常'];
                break;
        }

        return $return;
    }

    /**
     * 报名成功返回信息   --数据中心版
     * @User
     * @param $uid
     * @param $maker_id
     * @param $activity_id
     * @param $maker_id
     * @param $activity
     * @param string $activity_ids
     * @return array
     */
    public function getSignInfo($uid, $maker_id, $activity_id, $activity, $activity_ids = '')
    {
        $user = User::find($uid);
        $maker = Maker::where('id', $maker_id)->first();
        $sign_time = Sign::where('uid', $uid)
            ->whereIn('activity_id', $activity_ids)
            ->where('maker_id', $maker_id)
            ->where('status', 0)
            ->orderBy('created_at', 'desc')
            ->first()
            ->created_at
            ->timestamp;

        Sign::where('uid', $uid)
            ->whereIn('activity_id', $activity_ids)
            ->where('maker_id', $maker_id)
            ->where('status', 0)->update(['updated_at'=>time(), 'sign_time'=>time()]);


        $live = Live::where('activity_id', $activity_id)->first();

        $data = [
            'subject'             => $activity->subject,
            'list_img'            => getImage($activity->list_img),
            'activity_begin_time' => date('Y-m-d H:i', $activity->begin_time),
            'live_begin_time'     => $live ? date('Y-m-d H:i', $live->begin_time) : '',
            'user'                => $user->nickname ? $user->nickname : ($user->realname ?: $user->username),
            'user_avatar'         => getImage($user->avatar),
            'user_tel'            => $user->username,
            'maker'               => $maker->subject ?: '-',
            'maker_address'       => $maker->address ?: '-',
            'sign_time'           => date('Y-m-d H:i', $sign_time),
        ];

        return $data;
    }

    /*
     * 活动门票
     */
    public function postTickets($param = [])
    {
        $data = Activity::tickets($param['id'], 0, 1, isset($param['uid']) ? $param['uid'] : 0);

        foreach ($data as $key => $item) {

            if ($param['version'] == '_v020500') {
                $item->price = $item->price == '0.00' ? '0' : $item->price;
            }

            //现场免费票已售完
            if ($item->type == 1 && $item->left == 0) {
                $out_ticket = $item;
                unset($data[$key]);
            }

            //直播票只有一张
            if ($item->type == 2) {
                $item->name = $item->name ?: '直播票';
            } elseif ($item->type == 1) {
                //现场票可能有多张
                if (empty($item->name)) {
                    $item->name = (int)$item->price > 0 ? '标准票' : '免费票';
                }
            }
        }

        if ($out_ticket) {
            array_push($data, $out_ticket);
        }

        return ['status' => true, 'message' => $data];
    }

    /**
     * 签到送积分  -- FIXME: 已经用不上  不处理  yaokai 2017.12.22
     * @User
     * @param $uid
     * @param int $sign_id
     */
    private function getScoreBySign($uid, $sign_id = 0)
    {
        $count = Sign::getCount(['uid' => $uid]);
    }

    /**
     * 活动签到更新状态    --数据中心版
     * @User yaokai
     * @param $exist
     * @param $uid
     * @param $activity_id
     * @param $maker_id
     * @param string $name
     * @param string $tel
     * @return int
     */
    protected function updateStatus($exist, $uid, $activity_id, $maker_id, $name = '', $tel = '',$non_reversible = '')
    {
        $activity = Activity::where('id', $activity_id)->first();
        //找出这个活动相同标签的活动
        $tag_id = $activity->tag_id;
        $activitys = Activity::where('tag_id', $tag_id)->get()->toArray();
        $activity_ids = array_pluck($activitys, 'id');//这个标签所有的关联活动id
        $status = '';
        //访问者是否已经报名
        if (count($exist) > 0) {
            foreach ($exist as $v) {
                $status = $v->status;
            }
            if ($status == 0) {
                $updateData = ['status' => 1, 'updated_at' => time()];
                if (!empty($name)) {
                    $updateData['name'] = $name;
                    $updateData['tel'] = pseudoTel($tel);
                    $updateData['non_reversible'] = $non_reversible;
                }
                $updateData['sign_time'] = time();
                //更新状态
                User_Ticket::where('uid', $uid)
                    ->whereIn('activity_id', $activity_ids)
                    ->where('maker_id', $maker_id)
                    ->update(['is_check' => 1]);
                Sign::updateBy(['uid' => $uid, 'status' => 0, 'maker_id' => $maker_id], $updateData);
                //积分赠送
//                $this->getScoreBySign($uid, $exist[0]->id);    FIXME: 这里已经用不上了  屏蔽  yaokai  2017.12.22
            } else {
                return 1;
            }
        } else {
            //临时签到的uid是0，所以有可能导致前面的判断失效
            if($tel){
                $sign = Sign::where('non_reversible', $non_reversible)
                    ->where('status', '0')
                    ->where('activity_id', $activity_id)
                    ->first();
            }

            if($sign){
                $sign->status = 1;
                $sign->sign_time = time();
                $sign->save();
            }else{
                $insertData = [
                    'uid'         => $uid,
                    'activity_id' => $activity_id,
                    'maker_id'    => $maker_id,
                    'status'      => 1,
                    'sign_time'      => time(),
                ];
                if (!empty($name)) {
                    $insertData['name'] = $name;
                    $insertData['tel'] = pseudoTel($tel);
                    $insertData['non_reversible'] = $non_reversible;
                }

                $sign = Sign::create($insertData);
            }

            //积分赠送
//            $this->getScoreBySign($uid, $sign->id);  FIXME: 这里已经用不上  屏蔽   yaokai 2017.12.22
        }
    }

    /*
     * 活动评论点赞
     */
    public function postZan($param = [])
    {
        if (!$param['uid'] || !$param['activity_id']) {
            return ['status' => false, 'message' => 'uid和活动id是必填项'];
        }
        $result = Praise::add($param['uid'], 'activity', $param['activity_id']);

        return ['status' => !is_string($result), 'message' => $result];
    }

    /**
     * 检测支付结果并改变活动报名及订单状态  --数据中心版
     * @User
     * @param $param
     * @return array
     */
    public function postCheckAndApply($param)
    {
        $activityService = new ActivityService();
        $order = Order::with('ticket')->where('order_no', $param['order_no'])->first();
        if ($order->status == 1 || $order->status == 2) {
            $data = $activityService->getOrderDetail($param['order_no']);
        } else {
            $is_orders = 0;
            //检验
            if (strstr($param['order_no'], 'video_id')) {
                $param['order_no'] = substr($param['order_no'], 8);
                $is_orders = 1;
            }
            $pay_result = $activityService->postThirdResult($param['order_no'], $is_orders);
            if ($pay_result == 1) {
                $data = $activityService->getOrderDetail($param['order_no']);
            } else {
                return ['data' => '支付失败', 'status' => false];
            }
        }

        return ['data' => $data, 'status' => true];
    }

    /**
     * 报名不支付  --数据中心版
     * @User
     * @param $data
     * @return array
     */
    public function postApplyNoPay($data)
    {
        $non_reversible = encryptTel($data['tel']);

        //判断其是否已经报名
        $sign = Sign::whereIn('status', [0, 1])->where(['non_reversible' => $non_reversible, 'activity_id' => $data['activity_id']])->first();
        if (is_object($sign)) {
            return ['data' => '该手机号已报名', 'status' => false];
        }
        $activityService = new ActivityService();
        $result = $activityService->postApplyNoPay($data);

        return $result;
    }

    /**
     * 判断某手机号是否已经报名过  --数据中心版
     * @User tangjb
     * @param $data
     * @return array
     */
    public function postTelApplied($data)
    {

        //用户加密后的手机号
        $non_reversible = encryptTel($data['tel']);

        $check = checkMobile($data['tel']);
        if (!$check) {
            return ['data' => '该手机号格式不正确', 'status' => false];
        }

        //判断其是否已经报名
        $sign = Sign::whereIn('status', [0, 1])->where(['non_reversible' => $non_reversible , 'activity_id' => $data['activity_id']])->first();

        if (is_object($sign)) {
            return ['data' => '该手机号已报名', 'status' => false];
        } else {
            return ['data' => '该手机号可用', 'status' => true];
        }
    }

}