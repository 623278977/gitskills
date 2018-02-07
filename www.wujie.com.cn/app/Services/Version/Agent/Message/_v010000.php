<?php namespace App\Services\Version\Agent\Message;

use App\Models\Agent\Agent;
use App\Models\Agent\AgentCustomerLog;
use App\Models\Agent\AgentMessage;
use App\Models\Agent\Contract;
use App\Models\Zone;
use App\Services\Version\VersionSelect;
use App\Models\Agent\Invitation;
use App\Models\Agent\AgentCustomer;
use App\Models\Agent\BrandContract;
use App\Models\Brand\BrandStore;
use App\Models\Activity\Entity;
use \App\Models\Contract\Contract as Contracts;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Input;
use App\Models\Brand\Entity\V020800 as Brands;
use Illuminate\Support\Str;
use App\Models\Brand\Entity as Brand;

class _v010000 extends VersionSelect
{
    const MESSAGE_NOTICE  = 1;  //消息提醒
    const MESSAGE_INFORM  = 2;  //最新通知
    const ALREADY_AGENCY  = 4;  //已经代理的品牌数字标记
    const ACTIVITY_TYPE   = 1;  //活动类型
    const INSPECT_TYPE    = 2;  //考察类型
    const CONTRACT_TYPE   = 3;  //合同类型
    const OTHER_TYPE      = 4;  //其他类型

    /**
     * 添加跟进记录
     *
     * @param $param
     * @return array
     */
    public function postAddLog($param)
    {
        $result = $param['request']->input();

        switch ($result['type']) {
            case self::ACTIVITY_TYPE :
                $brand_id = $this->_withIDGainInfo($result['post_id'], self::ACTIVITY_TYPE);
                break;
            case self::INSPECT_TYPE :
                $brand_id = $this->_withIDGainInfo($result['post_id'], self::INSPECT_TYPE);
                break;
            case self::CONTRACT_TYPE:
                $brand_id = $this->_withIDGainInfo($result['post_id'], self::CONTRACT_TYPE);
                break;
            case self::OTHER_TYPE:
                $brand_id = !empty($result['brand_id']) ?  $result['brand_id'] : 0;
                break;
        }

        //组织要添加的数据
        $data = [
            'agent_customer_id' => 0,
            'agent_id'   => $result['agent_id'],
            'uid'        => $result['customer_id'],
            'action'     => $result['action'],
            'post_id'    => $result['post_id'],
            'brand_id'   => $brand_id,
            'created_at' => time(),
        ];

        $add_result = AgentCustomerLog::insert($data);
        if ($add_result) {
            return ['message' => '添加成功', 'status' => true];
        } else {
            return ['message' => '添加失败', 'status' => false];
        }
    }


    /**
     * 根据邀请函ID或合同ID获取对应信息
     *
     * @param $invite_id    邀请函ID | 合同ID
     * @param $type         类型: 1,2邀请函类型； 3 合同类型
     *
     * @return int          品牌ID
     */
    protected function _withIDGainInfo($invite_id, $type)
    {
        if ($type == self::ACTIVITY_TYPE) {
            $result   = Invitation::where('id', $invite_id)->first();
            $activity = Entity::with('brand')->where('id', $result->post_id)->first();
            $brand_id = $activity->brand[0]->brand_id ?  $activity->brand[0]->brand_id : 0;
        } elseif ($type == self::INSPECT_TYPE) {
            $result   = Invitation::where('id', $invite_id)->first();
            $brand_id = BrandStore::where('id', $result->post_id)->first()->brand_id;
            $brand_id = $brand_id ?  $brand_id : 0;
        } elseif ($type == self::CONTRACT_TYPE) {
            $brand_id = Contracts::find($invite_id)->brand_id;
            $brand_id = $brand_id ?  $brand_id : 0;
        } else {
            $brand_id = 0;
        }

        return $brand_id;
    }


    /**
     * 通讯录   --数据中心版
     *
     * @param $param
     * @return array
     */
    public function postContactList($param)
    {
        $result = $param['request']->input();

        if (!$result['agent_id']) {
            return ['message' => '参数错误：agent_id必传', 'status' => false];
        }

        //获取经济人和投资数据信息
        $results = Agent::instance()->passTypeGetData($result);
        if(is_null($results)) {
            return [null, 'status' => true];
        }

        return ['message' => $results, 'status' => true];
    }

    /**
     * 获取经纪人ID消息通知
     *
     * @internal param 参数集合 $param
     * @param $param
     *
     * @return array
     */
    public function postAgentMessage($param)
    {
        $result = $param['request']->input();

        $agent_message = AgentMessage::where('agent_id', $result['agent_id'])
            ->orderBy('created_at', 'desc')
            ->get()
            ->toArray();

        $confirm_result = array_map(function($results) {
            return [
                'title'      => $results['title'],
                'image'      => $results['image'],
                'content'    => $results['content'],
                'created_at' => strtotime($results['created_at']),
                'url'        => $results['url']
            ];
        }, $agent_message);

       return ['message' => $confirm_result, 'status' => true];
    }

    /**
     * 显示指定的考察邀请函 zhaoyf
     *
     * @param $param
     *
     * @return array
     */
    public function anyShowInspectInvitation($param)
    {
        $result = $param['request']->input();
        $confirm_result = Invitation::instance()->getInspectInvitations($result);

        //返回处理后的结果
        return [
            'message' => $confirm_result['message'],
            'status'  => $confirm_result['status'],
        ];
    }

    /**
     * 可以发送邀请的活动列表
     *
     * @param $param
     * @return array|string
     * @internal param Request $request
     * @internal param null $version
     */
    public function postActivities($param)
    {
        $result     = $param['request']->input();
        $page       = $param['request']->input('page',      1);
        $page_size  = $param['request']->input('page_size', 10);

        $data  = Agent::instance()->inviteActivity($result, $page, $page_size);

        return ['message' => $data ?: [], 'status' => true];
    }

    /**
     * author zhaoyf
     *
     * 根据品牌ID获取合同
     *
     * @param $param
     * @return array|string
     * @internal param Request $request
     * @internal param null $version
     */
    public function postContracts($param)
    {
        $result = $param['request']->input();

        if (!$result['agent_id']) {
            return ['message' => '缺少经纪人ID', 'status' => false];
        }
        if (!empty($result['brand_id']) && isset($result['brand_id'])) {

            //判断品牌是否被禁用
            $query_result = Brand::where([
                'id'            => $result['brand_id'],
                'agent_status'  => 0,
            ])->count();
            if ($query_result) {
                return ['message' => '该品牌已经下架', 'status' => false];
            }

            $get_result = BrandContract::with('hasOneBrand')
                ->where('brand_id', $result['brand_id'])
                ->where('is_delete', '0')
                ->get()->toArray();
        } else {
            $get_band_id = Agent::with(['belongsToManyAgentBrand' => function($query) {
                $query->where('agent_brand.status', self::ALREADY_AGENCY)
                      ->select('brand_id', 'agent_status');
            }])
              ->where('id', $result['agent_id'])
              ->first();

            //获取品牌ID
            foreach ($get_band_id['belongsToManyAgentBrand'] as $ked => $val) {
                if ($val->agent_status == '1') {
                    $brand_id[] = $val->brand_id;
                }
            }

            //根据品牌ID获取具体的品牌信息
            $get_result = BrandContract::with('hasOneBrand')
                ->where('is_delete', 0)
                ->whereIn('brand_id', $brand_id)
                ->get()->toArray();
        }

        //获取合同品牌具体的数据信息
        $confirm_result = array_map(function($result) {
            return [
                'id'            => $result['id'],
                'brand_id'      => array_get($result['has_one_brand'], 'id'),
                'brand_name'    => array_get($result['has_one_brand'], 'name'),
                'contract_no'   => $result['brand_contract_no'],
                'contract_name' => $result['name'],
                'amount'        => number_format($result['amount']),
                'first_money'   => number_format($result['pre_pay']),
                'last_money'    => number_format($result['amount'] - $result['pre_pay']),
                'created_at'    => $result['created_at'],
            ];
        }, $get_result);

        return ['message' => $confirm_result, 'status' => true];
    }

    /**
     * author zhaoyf
     *
     * 可以用来发送邀请的品牌列表
     *
     * @param  $param
     *
     * @return array|string
     */
    public function postBrands($param)
    {
        $data = array();
        $result = $param['request']->input();

        if (!$result['agent_id']) return ['message' => '缺少经济人ID: agent_id', 'status' => false];

        $get_result = Agent::with(['belongsToManyAgentBrand' => function ($query) {
            $query->where('agent_brand.status', self::ALREADY_AGENCY)
                ->select('agent_brand.status',
                    'agent_brand.agent_id', 'agent_brand.brand_id',
                    'brand.categorys1_id',  'brand.id', 'brand.name', 'brand.logo',
                    'brand.investment_min', 'brand.investment_max', 'brand.keywords',
                    'brand.rebate', 'brand.brand_summary', 'brand.slogan', 'brand.details','brand.agent_status');
        }, 'belongsToManyAgentBrand.categorys1', 'belongsToManyAgentBrand.store'])
            ->where('id', $result['agent_id'])
            ->first();

        if (!$get_result) return ['message' => '没有查询到相关信息', 'status' => false];

        foreach ($get_result['belongsToManyAgentBrand'] as $key => $vss) {
           if($vss['agent_status'] == '1') {
                $data[$key]['status']   = $vss['status'];
                $data[$key]['id']       = $vss['id'];
                $data[$key]['title']    = $vss['name'];
                $data[$key]['slogan']   = $vss['slogan'];
                $data[$key]['logo']     = getImage($vss['logo'], 'avatar', '');
                $data[$key]['investment_min']  = number_format($vss['investment_min']);
                $data[$key]['investment_max']  = number_format($vss['investment_max']);
                $data[$key]['keywords']        = explode(' ', $vss['keywords']);
                $data[$key]['category_name']   = $vss['categorys1']['name'];
                //$data[$key]['rebate']          = number_format(Brands::instances()->getMaxCommission($vss['id']));
                $data[$key]['rebate']          = Brands::instances()->getMaxCommission($vss['id'], true);
                $data[$key]['brand_summary']   =  !empty($vss['brand_summary']) ? $vss['brand_summary'] : Str::limit(trim(preg_replace('#<\w+(\s+.*?)?>|</\w+>|&nbsp;#','', $vss['details'])), 50);
                $data[$key]['stores']          = BrandStore::where('brand_id', $vss['id'])->count();

                foreach (BrandStore::where('brand_id', $vss['id'])->select('name', 'address', 'id', 'zone_id')->get() as $keys => $vls) {
                    $data[$key]['stores_gather'][] = ['id'=> $vls->id, 'name' => $vls->name, 'address' => $vls->address, 'zone' => \App\Models\Zone\Entity::getCityAndProvince($vls->zone_id)];
                }
            }
        }

        return ['message' => $data, 'status' => true];
    }

    /**
     * 经纪人的跟单提醒
     *
     * @param    param $param  参数集合
     * @internal param Request $request
     * @internal param null $version
     *
     * @return gather_data|array
     */
    public function postMessageRecord($param)
    {
        $result = $param['request']->input();

        if (empty($result['page']) || !isset($result['page']))           $result['page']      = 1;
        if (empty($result['page_size']) || !isset($result['page_size'])) $result['page_size'] = 15;

        $result = Agent::instance()->documentaryHints($result);

        return ['message' => $result, 'status' => true];
    }

    /**
     * 获取经纪人的客户列表
     * @internal param  经纪人ID $agent_id
     * @param   $param
     * @return  array|string
     * @internal param Request $request
     *
     * @internal param null $version
     */
    public function postAgentCustomerList($param)
    {
        $result = $param['request']->input('agent_id', '');

        if(empty($result) || !intval($result)) {
            return ['message' => '经纪人ID不能为空:agent_id；且只能是整形', 'status' => false];
        }

        //获取数据集合
        $gain_result = AgentCustomer::with(['user' => function($query) {
            $query->select('uid', 'nickname', 'avatar');
        }, 'brand' => function($query) {
            $query->select('id', 'name');
        }])
            ->where('agent_id', $result)
            ->select('id', 'uid', 'brand_id')
            ->get()->toArray();

        //组合数据
        $gather_result = array_map(function($result) {
            return [
                'uid'      => $result['user']['uid'],
                'avatar'   => $result['user']['avatar'],
                'nickname' => $result['user']['nickname'],
                'brand_title' => $result['brand']['name'],
            ];
        }, $gain_result);

        //对数组进行重新排序且去除空的数据
        sort($gather_result); $new_data = array();
        foreach ($gather_result as $key => $vls) {
            $new_data[] = array_filter($vls);
        }

        return ['message' => $new_data, 'status' => true];
    }

    /*
    *
    *展示被指定的活动邀请函
    * */
    public function anyShowActiveInvitation($input){
        $inviteId = intval($input['invite_id']);
        if(empty($inviteId)){
            return ['message' => '请输入活动邀请函id', 'status'=> false];
        }
        $inviteInfo = Invitation::getActiveInvitationInfo($inviteId);
        if(isset($inviteInfo['error'])){
            return ['message' => $inviteInfo['message'], 'status'=> false];
        }
        return ['message' => $inviteInfo, 'status'=> true];
    }

}