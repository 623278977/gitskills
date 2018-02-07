<?php namespace App\Services\Version\Agent\Customer;

use App\Models\Agent\AgentCustomer;
use App\Models\Agent\Agent;
use App\Models\Agent\AgentCustomerLog;
use App\Models\Agent\Message\AgentCustomerMessage;
use App\Models\User\Entity as User;

class _v010002 extends _v010001
{
    const LOST_TYPE    = -1;   //失去标记
    const CONFIRM_TYPE = 1;    //确定标记
    const AWAIT_TYPE   = 0;     //等待标记

    /*
     * shiqy
     * 邀请客户概览
     *
     * */
    public function postInviteOverview($input){
        $data = parent::postInviteOverview($input);
        if($data['status'] == false){
            return $data;
        }
        $data = $data['message'];
        unset($data['protect_customers']);
        return ['message' => $data,'status' => true];
    }

    /**
     * author zhaoyf
     *
     * 客户详情 -- 意向记录
     *
     * @param $param    投资人ID
     * @return array|string
     */
    public function postCustomerIntentionRecord($param)
    {
        //获取参数
        $result = $param['request']->input();

        //调用方法，传递参数，获取结果数据
        $confirm_result = Agent::instance()->customerIntentionRecords($result);

        //对结果进行处理
        if (is_null($confirm_result)) {
            return ['message' => '没有数据', 'status' => true];
        }

        //返回数据
        return ['message' => $confirm_result, 'status' => true];
    }

    /**
     * 客户详情 zhaoyf
     *
     * @param $param
     * @return array|string
     * @internal param CustomerRequest $request
     * @internal param null $version
     * @internal param 经济人ID $agent_id
     * @internal param 投资人ID $customer_id return detail_data*
     */
    public function postDetailInfos($param)
    {
        //接受的参数
        $result = $param['request']->input();

        //获取投资人的基本信息
        $user = User::with('hasManyCategory.hasOneCategorys')
            ->where('uid', $result['customer_id'])
            ->first();

        //对结果进行判断
        if (!$user) return ['message' => '没有相关信息', 'status' => false];

        //获取城市
        $zone      = new \App\Models\Zone\Entity();
        $zone_name = $zone->pidNames([$user->zone->id]);

        //星座:$constellation;
        //几零后:$customer_time;
        //哪里人:$customer_zone;
        $constellation = getStarsignByMonth(substr($user->birth, 5, 2), substr($user->birth, 8, 2));
        $customer_time = getTime($user->birth, 'birth_time');
        $customer_zone = abandonProvince($user->zone->name);

        //对数据格式进行处理
        if ($customer_zone) {
            if ('区' == mb_substr($customer_zone, -1, 1)) {
                $customer_zone = mb_substr($customer_zone, 0, -1) . '人';
            } elseif ('地区' == mb_substr($customer_zone, -2, 2)) {
                $customer_zone = mb_substr($customer_zone, 0, -2) . '人';
            } else {
                $customer_zone = $customer_zone . '人';
            }
        } else {
            $customer_zone = '';
        }

        //投资意向:$intention；
        //投资额度:$customer_money;
        $intention      = User::$IFIntention[$user->invest_intention];
        $customer_money = abandonZero($user->investment_min) . '~' . abandonZero($user->investment_max);

        //对数据格式进行处理
        if ($user->investment_min > 0 && $user->investment_max > 0 ) {
            $customer_money = '投资额度：' . $customer_money;
        } elseif ($user->investment_min <= 0 && $user->investment_max > 0) {
            $customer_money = '投资额度：' . abandonZero($user->investment_max);
        } elseif ($user->investment_min > 0 && $user->investment_max <= 0) {
            $customer_money = abandonZero($user->investment_min);
        } else {
            $customer_money = '';
        }

        //获取用户意向的品牌
        $brand_result = AgentCustomer::with(['brand' => function($query) {
            $query->where('agent_status', self::CONFIRM_TYPE)
                  ->select('id', 'name', 'logo', 'agent_status', 'categorys1_id', 'categorys2_id', 'investment_min', 'investment_max');
        }, 'brand.categorys1' => function($query) {
            $query->where('status', 'enable')
                  ->select('id', 'name', 'logo');
        }])
          ->where('uid', $result['customer_id'])
          ->where('level',  '<>', self::LOST_TYPE)
          ->where('status', '<>', self::LOST_TYPE)
          ->where('brand_id', '<>', self::AWAIT_TYPE)
          ->select('brand_id')
          ->get();

        //处理用户的意向品牌
        if ($brand_result) {
            $user_font_brand = array();
            foreach ($brand_result as $key => $vls) {
                if ($vls->brand->agent_status == self::CONFIRM_TYPE) {
                    $user_font_brand[$key] = [
                        'brand_id'    => $vls->brand->id,
                        'brand_name'  => $vls->brand->name,
                        'brand_logo'  => getImage($vls->brand->logo),
                        'brand_cate'  => $vls->brand->categorys1->name,
                        'start_money' => number_format($vls->brand->investment_min) . '~' . number_format($vls->brand->investment_max) . '万'
                    ];
                }
            }
        } else {
            $user_font_brand = 0;
        }

        //用户感兴趣的行业分类
        foreach ($user['hasManyCategory'] as $key => $vls) {
            $user_font_cate[$key] = $vls['hasOneCategorys']['name'];
        }

        //组合用户标签
        $tagss = [
            'customer_time'  => !empty($customer_time) ?  $customer_time : '',
            'constellation'  => !empty($constellation) ?  $constellation : '',
            'customer_zone'  => !empty($customer_zone) ?  $customer_zone : '',
            'intention'      => !empty($intention) ?  $intention : '',
            'customer_money' => !empty($customer_money) ?  $customer_money : '',
            'fond_cate'      => $user_font_cate
        ];

        //处理投资人的手机号
        //$user_tel = AgentCustomer::instance()->customerIsPublicTelToAgentResultHandle($results);

        //判断当前投资人和经纪人的关系
        $relation_result = AgentCustomer::where([
            'agent_id'  => $result['agent_id'],
            'uid'       => $result['customer_id']
        ])
         ->where('level',  '<>', self::LOST_TYPE)
         ->where('status', '<>', self::LOST_TYPE)
         ->first();

        //获取经纪人的接单状态
        $status = AgentCustomerMessage::where('customer_id', $result['customer_id'])
            ->where('agent_id', $result['agent_id'])->first();

        //组合数据
        $data = [
            'realname'        => $user->realname,
            'nickname'        => $user->nickname,
            'avatar'          => getImage($user->avatar, 'avatar', ''),
            'gender'          => $user->gender,
            'last_login'      => $user->last_login,
            'city'            => $zone_name ? $zone_name : '',
            'relation'        => $relation_result ?  1 : 0,
            'status'          => $status ?  $status->status : "",
            'tags'            => $tagss,
            'recommend_brand' => !empty($user_font_brand) ?  $user_font_brand : [],
            'brand_count'     => count($user_font_brand),
        ];

        //返回结果
        return ['message' => $data, 'status' => true];
    }

    /**
     * 版本迭代 010002 版本    --数据中心版
     * 客户详情--跟进品牌页 zhaoyf
     *
     * @param $param
     *
     * @return array
     */
    public function postDetailBrands($param)
    {
        $result = $param['request']->input();

        $customer_and_brand_result = AgentCustomer::instance()->DetailBrand($result);

        return ['message' => $customer_and_brand_result, 'status' => true];
    }
}