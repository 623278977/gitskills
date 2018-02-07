<?php namespace App\Services\Version\Agent\Customer;

use App\Services\Version\VersionSelect;
use App\Models\Contract\Contract;
use App\Models\Agent\Agent;
use App\Models\User\Entity as User;
use App\Models\Agent\AgentCustomer;

class _v010003 extends _v010002
{
    /**
     * author zhaoyf    --数据中心版
     *
     * 客户详情--经纪人和投资人没有关系时查看
     */
    public function postDetailInfos($param)
    {
        $result = $param['request']->input();

        //查看经纪人和投资人是否存在关系
        $is_relation_result = AgentCustomer::where([
            'agent_id'  => $result['agent_id'],
            'uid'       => $result['customer_id']
        ])
         ->where('level',  '<>', '-1')
         ->where('status', '<>', '-1')
         ->first();

        //如果存在关系，走_v010000,如果没有关系，往下走
        if (is_object($is_relation_result)) {
            $customer_infos = new _v010000();
           return $customer_infos->postDetailInfos($param);
        }

        $results = User::with('zone', 'hasManyCategory.hasOneCategorys')
            ->where('uid', $result['customer_id'])
            ->first();

        if (!$results) {
            return ['message' => '没有相关信息', 'status' => false];
        }

        //获取城市
        $zone      = new \App\Models\Zone\Entity();
        $zone_name = $zone->pidNames([$results->zone->id]);

        //星座
        $constellation  = getStarsignByMonth(substr($results->birth, 5, 2), substr($results->birth, 8, 2));
        //几零后
        $customer_time  = getTime($results->birth, 'birth_time');
        //哪里人
        $customer_zone  = abandonProvince($results->zone->name);
        if($customer_zone) {
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
        //投资意向
        $intention      = User::$IFIntention[$results->invest_intention];
        //投资额度
        $customer_money = abandonZero($results->investment_min) . '~' . abandonZero($results->investment_max);
        if ($results->investment_min > 0 && $results->investment_max > 0 ) {
            $customer_money = '投资额度：' . $customer_money;
        } elseif ($results->investment_min <= 0 && $results->investment_max > 0) {
            $customer_money = '投资额度：' . abandonZero($results->investment_max);
        } elseif ($results->investment_min > 0 && $results->investment_max <= 0) {
            $customer_money = abandonZero($results->investment_min);
        } else {
            $customer_money = '';
        }

        //获取用户感兴趣的行业分类
        foreach ($results->hasManyCategory as $key => $vls) {
            $user_font_cate[$key] = $vls->hasOneCategorys->name;
        }

        //组合用户标签
        $tagss = [
            'customer_time'  => !empty($customer_time) ?  $customer_time : '',
            'constellation'  => !empty($constellation) ?  $constellation : '',
            'customer_zone'  => !empty($customer_zone) ?  $customer_zone : '',
            'intention'      => !empty($intention) ?  $intention : '',
            'customer_money' => !empty($customer_money) ?  $customer_money : ''
        ];
        $tagss['customer_cate'] = $user_font_cate ?  $user_font_cate : '';

        //组合数据
        $data = [
            'realname'             => $results['realname'] ?  $results['realname'] : $results['nickname'],
            'nickname'             => $results['nickname'] ?  $results['nickname'] : $results['realname'],
            'remark'               => $results['remark'] ? $results['remark'] : '',
            'relation_tel'         => $results->username,
            'non_reversible'       => $results->non_reversible,
            'user_tel'             => $results->username,
            'avatar'               => getImage($results['avatar'], 'avatar', ''),
            'gender'               => $results['gender'] == -1 ?  '未知' : ($results['gender'] == 1 ?  '男' : '女'),
            'last_login'           => $results['last_login'],
            'city'                 => $zone_name ? $zone_name : '',
            'diploma'              => $results['diploma'] ?  $results['diploma'] : '',
            'positions'            => $results['profession'] ?  $results['profession'] : '',
            'earning'              => $results['earning'] ? $results['earning'] : '',
            'interest_industries'  => $user_font_cate ?  $user_font_cate : '',
            'invest_intention'     => User::$IFIntention[$results['invest_intention']] ?  User::$IFIntention[$results['invest_intention']] : '',
            'invest_quota'         => abandonZero($results['investment_min']) >= 100 ? abandonZero($results['investment_min']) . '万元以上' : abandonZero($results['investment_min']).' - '.abandonZero($results['investment_max']).'万元',
            'created_at'           => $results['created_at']->getTimeStamp(),
            'has_tel'              => $results['has_tel'],
            'is_relation'          => 0,
            'tags'                 => $tagss
        ];

        return ['message' => $data, 'status' => true];
    }


}