<?php namespace App\Models\Agent;

use App\Models\Activity\ApplyActiviy;
use App\Models\User\Entity as Users;
use App\Models\Brand\BrandStore;
use Illuminate\Database\Eloquent\Model;
use App\Models\Orders\Items;
use App\Models\Activity\Entity as Activity;
use App\Models\Activity\Maker as ActivityMaker;
use App\Models\Activity\Sign as ActivitySign;
use App\Models\Agent\Agent;
use App\Models\Zone\Entity as Zones;
use App\Models\Agent\AgentCustomer;

class Invitation extends Model
{
    protected $table = 'invitation';

    protected $dateFormat = 'U';
    const INSPECT_TYPE = 2;
    const NUMBER_ONE   = 1;     //数字1

    public static $instance = null;

    //接受考察邀请函之后的状态
    public static $afterComfirmStatus = [1,4];

    public static function instance()
    {
        if (is_null(self::$instance)) {
            self::$instance = new self;
        }
        return self::$instance;
    }

    /**
     * 关联：门店
     */
    public function hasOneStore()
    {
        return $this->hasOne(BrandStore::class, 'id', 'post_id');
    }

    /**
     * 关联：用户
     */
    public function hasOneUsers()
    {
        return $this->hasOne(Users::class, 'uid', 'uid');
    }

    /**
     * 关联：支付订单
     */
    public function hasOneOrderItems()
    {
        return $this->hasMany(Items::class, 'product_id', 'id')
            ->where('type', 'inspect_invite');
    }



    /*
     * 关联活动表
     * */
    public function hasOneActivity()
    {
        return $this->hasOne(Activity::class, 'id', 'post_id');
    }

    //关联活动会场
    public function hasManyActivityMaker()
    {
        return $this->hasMany(ActivityMaker::class, 'activity_id', 'post_id');
    }

    //关联活动签到表
    public function hasManyActivitySign()
    {
        return $this->hasMany(ActivitySign::class, 'uid', 'uid');
    }

    //关联经纪人表
    public function belongsToAgent()
    {
        return $this->belongsTo(Agent::class, 'agent_id', 'id');
    }

    //关联经纪人客户表
    public function agent_customer()
    {
        return $this->hasMany(AgentCustomer::class, 'agent_id', 'agent_id');
    }

    /*
     * 通过邀请函id获得邀请函相关信息
     * */
    public static function getActiveInvitationInfo($inviteId)
    {
        $inviteInfo = self::where('id', $inviteId)->first();
        if (!is_object($inviteInfo)) {
            return array(
                'error' => 1,
                'message' => "该邀请函不存在",
            );
        }
        if ($inviteInfo['type'] == 2) {
            return array(
                'error' => 1,
                'message' => "该邀请函不是活动邀请函",
            );
        }

        $data = [];
        $inviteInfos = self::with('belongsToAgent', 'hasOneUsers','hasOneActivity.brands',
            'hasOneActivity.makers.zone', 'hasManyActivitySign.belongsToMaker')
            ->where('id', $inviteId)
            ->first()->toArray();
        //排除有品牌已经被禁用的邀请函
        $disableBrands = collect($inviteInfos['has_one_activity']['brands'])->filter(function($item){
            return $item['agent_status'] == 0;
        })->count();
        if($inviteInfos['status'] == 0 && !empty($disableBrands)){
            return array(
                'error' => 1,
                'message' => "该邀请函对应的品牌已下架",
            );
        }
        $status = trim($inviteInfos['status']);
        $city = '';
        if ($status == 1) {
            $signArr = $inviteInfos['has_many_activity_sign'];
            $activeId = trim($inviteInfos['post_id']);
            $signCollect = collect($signArr)->filter(function ($item) use ($activeId) {
                return $item['activity_id'] == $activeId;
            })->first();
            $city = trim($signCollect['belongs_to_maker']['subject']);
        } else {
            $makerArr = $inviteInfos['has_one_activity']['makers'];
            $cityArr = [];
            foreach ($makerArr as $oneMaker) {
                $cityArr[] = trim(str_replace("市", "", $oneMaker['zone']['name']));
            }
            $city = implode(',', $cityArr);
        }

        $data = array(
            'custom_nickname' => trim($inviteInfos['has_one_users']['nickname']),
            'uid' => trim($inviteInfos['has_one_users']['uid']),
            'custom_realname' => trim($inviteInfos['has_one_users']['realname']),
            'custom_gender' => trim($inviteInfos['has_one_users']['gender']),
            'agent_nickname' => trim($inviteInfos['belongs_to_agent']['nickname']),
            'agent_realname' => trim($inviteInfos['belongs_to_agent']['realname']),
            'is_public_realname' => trim($inviteInfos['belongs_to_agent']['is_public_realname']),
            'agent_id' => trim($inviteInfos['belongs_to_agent']['id']),
            'img' => getImage($inviteInfos['has_one_activity']['list_img'], 'activity'),
            'title' => trim($inviteInfos['has_one_activity']['subject']),
            'begin_time' => trim($inviteInfos['has_one_activity']['begin_time']),
            'invite_time' => trim($inviteInfos['created_at']),
            'invite_id' => trim($inviteId),
            'status' => $status,
            'confirm_time' => trim($inviteInfos['updated_at']),
            'reason' => trim($inviteInfos['remark']),
            'citys' => $city,
            'activity_id' => trim($inviteInfos['has_one_activity']['id']),
        );
        return $data;
    }

    /**
     * 显示指定的考察邀请函
     *
     * @param $inspect
     * @internal param $inspect_id
     *
     * @return  array
     */
    public function getInspectInvitations($inspect)
    {
        if (empty($inspect['inspect_id']) || !intval($inspect['inspect_id'])) {
            return ['message' => '缺少考察邀请函ID：inspect_id，并且只能是整形', 'status' => false];
        }

        //集合结果
        $gather_result = self::with(['hasOneUsers' => function ($query) {
                $query->select('uid', 'realname', 'nickname', 'gender');
            }, 'belongsToAgent' => function ($query) {
                $query->select('id', 'is_public_realname', 'nickname', 'username', 'realname', 'non_reversible');
            }, 'hasOneStore' => function ($query) {
                $query->select('id', 'name', 'address', 'brand_id', 'zone_id');
            }, 'hasOneStore.hasOneBrand' => function ($query) {
                $query->select('id', 'name', 'logo');
            }, 'hasOneStore.hasOneZone'  => function ($query) {
                $query->select('id', 'name');
            }, 'hasOneStore.hasOneBrand.contactor.agent',
               'hasOneOrderItems' => function($query) {
                $query->where('status', 'pay');
            } ,'hasOneOrderItems.orders' => function($query) {
                $query->where('status', 'pay');
            }
        ])
            ->where(function ($query) use ($inspect) {
                $query->where('id', $inspect['inspect_id'])
                    ->where('type', self::INSPECT_TYPE)
                    ->whereIn('status', [-1, 0, 1, 2, 3, 4]);
            })
            ->first();


        //对查询结果进过进行判断
        if (is_null($gather_result)) {
            return ['message' => '该考察邀请函不存在', 'status' => false];
        }

        //查看品牌是否已经下架
        $judge_result = Agent::instance()->brandStatusJudge($gather_result['hasOneStore']['hasOneBrand']['id']);
        if ($judge_result == '该品牌已经下架') {
            return ['message' => ['status' => -1, 'message' => $judge_result], 'status' => false];
        }

        //处理： 解析获取商务代表的手机号
        if (!is_null($gather_result['hasOneStore']['hasOneBrand'])
            && !is_null($gather_result['hasOneStore']['hasOneBrand']['contactor'])
            && !is_null($gather_result['hasOneStore']['hasOneBrand']['contactor']['agent'])) {
            $non_value = $gather_result['hasOneStore']['hasOneBrand']['contactor']['agent']['non_reversible'];
        } else {
            $non_value = null;
        }

        //组合数据，返回结果
        $confirm_result = [
            'uid'               => $gather_result['hasOneUsers']['uid'],
            'customer_realname' => $gather_result['hasOneUsers']['realname'] ? $gather_result['hasOneUsers']['realname'] : $gather_result['hasOneUsers']['nickname'],
            'customer_nickname' => $gather_result['hasOneUsers']['nickname'],
            'customer_gender'   => $gather_result['hasOneUsers']['gender'] == -1 ? '未知' : ($gather_result['hasOneUsers']['gender'] == 1 ?  '男' : '女'),
            'agent_id'          => $gather_result['belongsToAgent']['id'],
            'is_public_realname' => $gather_result['belongsToAgent']['is_public_realname'],
            'agent_realname'    => $gather_result['belongsToAgent']['realname'],
            'agent_nickname'    => $gather_result['belongsToAgent']['nickname'],
            'agent_tel'         => is_null($non_value) ?  '' : Agent::getRealPhone($non_value, 'agent'),
            'agent_contactor_non_reversible' => $gather_result['hasOneStore']['hasOneBrand']['contactor']['agent']['non_reversible'],
            'agent_tels'        => $gather_result['belongsToAgent']['username'],
            'agent_non_reversible' => $gather_result['belongsToAgent']['non_reversible'],//$gather_result['belongsToAgent']['username'],
            'store_id'          => $gather_result['hasOneStore']['id'],
            'store_name'        => $gather_result['hasOneStore']['name'],
            'brand_id'          => $gather_result['hasOneStore']['hasOneBrand']['id'],
            'img'               => getImage($gather_result['hasOneStore']['hasOneBrand']['logo']),
            'title'             => $gather_result['hasOneStore']['hasOneBrand']['name'],
            'inspect_time'      => $gather_result['inspect_time'],
            'head_address'      => Zones::pidNames([$gather_result['hasOneStore']['hasOneZone']['id']]),
            'totals_address'    => $gather_result['hasOneStore']['address'],
            'invite_time'       => $gather_result['created_at']->getTimestamp(),
            'invite_id'         => $gather_result['id'],
            'status'            => $gather_result['status'],
            'confirm_time'      => $gather_result['updated_at']->getTimestamp(),
            'reason'            => htmlspecialchars($gather_result['remark']),
            'default_money'     => number_format($gather_result['default_money']),
            'order_no'          => $gather_result['hasOneOrderItems']['orders']['order_no'],
        ];

        if ($gather_result['status'] == 1 || $gather_result['status'] == 2 || $gather_result['status'] == 4 || $gather_result['is_audit'] == 1) {
            foreach ($gather_result['hasOneOrderItems'] as $key => $vls) {
                if ($vls['orders']['status'] == 'pay') {
                    $confirm_result['order_no'] = $vls['orders']['order_no'];
                }
            }
        }

        return ['message' => $confirm_result, 'status' => true];
    }




    public function getDescription()
    {
        if($this->type==1){//活动
            return '活动邀请函';
        }else{//门店考察
            return '品牌考察邀请函';
        }
    }

    /**
     * 考察邀请信息
     * @User yaokai
     * @param $agent_id 经纪人id
     * @param $brand_id 品牌id
     * @param $uid  用户id
     * @return string
     */
    public static function getBrandInviId($agent_id,$brand_id,$uid)
    {

        //找出品牌所有的门店id
        $brand_store_id = BrandStore::where('brand_id',$brand_id)
            ->where('is_delete','0')
            ->lists('id');

        //找出考察邀请信息
        $invitation = Invitation::where('agent_id',$agent_id)
            ->where('uid',$uid)
            ->whereIn('post_id',$brand_store_id)
            ->where('type','2')
            ->whereIn('status',[1,3])
            ->value('id');

        return $invitation?:'0';

    }

    /**
     * 用户接受考察邀请函数
     * @User yaokai
     * @param $uid 用户id
     * @return string
     */
    public static function userInvitations($uid)
    {
        $count = Invitation::where('uid',$uid)
            ->whereIn('status',['1','2'])
            ->count();

        return $count?:'0';

    }

    /**
     * author zhaoyf
     *
     * 发送通知消息   --数据中心版
     *
     * @param  param [
     *  'id'    => 邀请函ID  int
     * ]
     *
     * return results
     *
     */
    public static function sendInform(array $param = [])
    {
        //根据ID获取一条邀请函数据
        $_result = self::with('hasOneUsers')
            ->where([
                'id'   => $param['id'],
                'type' => self::INSPECT_TYPE,
                'use_red_packet' => self::NUMBER_ONE
            ])
            ->first();

        //对结果进行处理
        if ($_result && !is_null($_result->hasOneUsers)) {

            //发送短信消息
            $a = SendTemplateSMS('red_deduction_info_notice',    //短信模板名称
                $_result->hasOneUsers->non_reversible,            //投资人手机号
                'red_deduction_notice_customer',            //短信类型
                [],                                         //需要传递的参数：默认空
                $_result->hasOneUsers->nation_code);        //国家码，默认：86

            return ['status' => true];
        }

        return ['status' => false];
    }
    
}

