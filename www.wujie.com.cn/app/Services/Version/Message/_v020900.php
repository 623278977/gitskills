<?php namespace App\Services\Version\Message;

use App\Http\Requests\Agent\AgentRequest;
use App\Http\Requests\Agent\CustomerRequest;
use App\Http\Requests\CustomerAgentRequest;
use App\Models\Agent\Agent;
use App\Models\Agent\AgentCustomerLog;
use App\Models\Agent\AgentRongInfo;
use App\Models\Agent\BaseInfoAdd;
use App\Models\Brand\BrandStore;
use App\Models\RedPacket\RedPacket;
use App\Models\RedPacket\RedPacketPerson;
use App\Models\User\Entity as User;
use App\Services\MessageSendService;
use App\Services\Version\VersionSelect;
use App\Models\Activity\Entity;
use App\Models\Agent\AgentCustomer;
use App\Models\Agent\Invitation;
use App\Models\AgentScore;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Models\Message;
use App\Http\Controllers\Api\CommentController;
use App\Models\Zone;
use App\Models\Zone\Entity as Zones;
use App\Services\Chat\Methods\Message as SendMessage;
use App\Services\Chat\Example;
use App\Models\Comment\Entity as Comment;
use App\Models\Orders\Items as OrdersItems;
use App\Models\Activity\Sign;
use App\Models\Orders\Items;
use \App\Models\Contract\Contract as Contracts;
use DB;
use App\Models\Brand\Entity as Brand;

class _v020900 extends _v020802
{
    const ACTIVITY_TYPE = 1;          //活动类型
    const INSPECT_TYPE = 2;          //考察类型
    const CONTRACT_TYPE = 3;          //合同类型
    const CONSENT_TYPE = 1;          //同意的数字标记
    const REJECT_TYPE = -1;         //拒绝的数字标记
    const CONSENT = "consent";  //同意
    const REJECT = "reject";   //拒绝

    protected $dateFormat = 'U';

    public static $instance = null;

    public static function instance()
    {
        if (is_null(self::$instance)) {
            self::$instance = new self;
        }
        return self::$instance;
    }


    /**
     *  author zhaoyf
     *
     * 邀请函动作（拒绝 | 接受；活动直接为拒绝）
     * @param $param
     *
     * @return data_list|array
     */
    public function postInviteAction($param)
    {
        $result = $param['request']->input();

        //等于活动时，获取活动详情数据（action只能为：reject）
        if ($result['type'] == self::ACTIVITY_TYPE) {
            if ($result['action_tags'] !== self::REJECT) {
                return ['message' => '缺少对应的活动动作行为，且只能为：reject', 'status' => false];
            }

            //根据邀请函ID获取到对应的数据信息
            $activity = Invitation::with('hasOneUsers', 'belongsToAgent', 'hasOneActivity')
                ->where('id', $result['invite_id'])->first();

            //获取经纪人和客户的关系
            $relative = $this->_agentCustomerRelative($activity['hasOneUsers']['uid'], $activity['belongsToAgent']['id'], $activity['hasOneUsers']['register_invite'], $activity['belongsToAgent']['non_reversible']);

            //活动对应的地区信息
            $site = DB::table('activity_maker')
                ->leftJoin('maker', 'activity_maker.maker_id', '=', 'maker.id')
                ->leftJoin('zone', 'maker.zone_id', '=', 'zone.id')
                ->where('activity_maker.activity_id', $activity->hasOneActivity->id)
                ->where('activity_maker.status', 1)
                ->select(DB::raw('GROUP_CONCAT(lab_zone.name) as site'))
                ->first();

            //获取经纪人数据信息
            $agent = [
                'is_public_realname' => $activity['belongsToAgent']['is_public_realname'],
                'realname' => $activity['belongsToAgent']['realname'],
                'nickname' => $activity['belongsToAgent']['nickname'],
                'avatar' => getImage($activity['belongsToAgent']['avatar'], 'avatar', ''),
                'level' => $activity['belongsToAgent']['agent_level_id'],
                'level_title' => Agent::$Agentlevel[$activity['belongsToAgent']['agent_level_id']],
                'relative' => $relative,
            ];

            //获取活动数据信息
            $activity = [
                'subject' => $activity->hasOneActivity->subject,
                'detail_img' => $activity->hasOneActivity->detail_img,
                'begin_time' => $activity->hasOneActivity->begin_time,
                'address' => $site->site,
                'keywords' => $activity->hasOneActivity->keywords ? explode(' ', $activity->hasOneActivity->keywords) : [],
            ];

            //返回组合后的具体信息
            return ['message' => ['activity' => $activity, 'agent' => $agent], 'status' => true];

            //等于考察邀请时，获取考察详情数据信息
        } elseif ($result['type'] == self::INSPECT_TYPE) {

            //根据邀请函ID获取到对应的数据信息
            $inspect = Invitation::with(['hasOneUsers' => function ($query) {
                $query->select('uid', 'register_invite');
            }, 'belongsToAgent' => function ($query) {
                $query->select('id', 'is_public_realname', 'nickname', 'realname', 'avatar', 'agent_level_id', 'username', 'non_reversible', 'zone_id');
            }, 'hasOneStore' => function ($query) {
                $query->select('id', 'brand_id', 'name', 'address', 'zone_id');
            }, 'hasOneStore.hasOneBrand' => function ($query) {
                $query->select('id', 'name', 'logo', 'slogan', 'categorys1_id', 'investment_min', 'investment_max', 'keywords');
            }, 'hasOneStore.hasOneBrand.categorys1' => function ($query) {
                $query->select('id', 'name', 'pid');
            }])
                ->where('id', $result['invite_id'])
                ->where('type', self::INSPECT_TYPE)
                ->first();


            //对查询结果进行处理
            if (is_null($inspect)) {
                return ['message' => '没有查询到任何信息', 'status' => false];
            }

            //判断品牌是否已经下架
            if ($inspect['hasOneStore']['hasOneBrand']) {
                $brand_result = Brand::where('id', $inspect['hasOneStore']['hasOneBrand']['id'])->first();
                if ($brand_result->status == 'disable' || $brand_result->agent_status == '0') {
                    return ['message' => ['status' => -1, 'message' => '此产品已经下架'], 'status' => false];
                }
            }

            //获取经纪人和客户的关系
            $relative = $this->_agentCustomerRelative($inspect['hasOneUsers']['uid'], $inspect['belongsToAgent']['id'], $inspect['hasOneUsers']['register_invite'], $inspect['belongsToAgent']['non_reversible']);

            $zone = Zone::getselfandparent($inspect['belongsToAgent']['zone_id']);

            //获取经纪人数据信息
            $agent = [
                'is_public_realname' => $inspect['belongsToAgent']['is_public_realname'],
                'realname' => $inspect['belongsToAgent']['realname'],
                'nickname' => $inspect['belongsToAgent']['nickname'],
                'avatar' => getImage($inspect['belongsToAgent']['avatar'], 'avatar', ''),
                'level' => $inspect['belongsToAgent']['agent_level_id'],
                'level_title' => Agent::$Agentlevel[$inspect['belongsToAgent']['agent_level_id']],
                'relative' => $relative,
                'zone' => $zone
            ];

            //获取品牌信息
            $inspect_data = [
                'brand_name' => $inspect['hasOneStore']['hasOneBrand']['name'],
                'brand_logo' => getImage($inspect['hasOneStore']['hasOneBrand']['logo']),
                'brand_slogan' => $inspect['hasOneStore']['hasOneBrand']['slogan'],
                'inspect_industry_cate' => $inspect['hasOneStore']['hasOneBrand']['categorys1']['name'],
                'start_money' => $inspect['hasOneStore']['hasOneBrand']['investment_min'] >= 100 ? $inspect['hasOneStore']['hasOneBrand']['investment_min'] : abandonZero($inspect['hasOneStore']['hasOneBrand']['investment_min']) . ' - ' . abandonZero($inspect['hasOneStore']['hasOneBrand']['investment_max']),
                'inspect_store' => $inspect['hasOneStore']['name'],
                'inspect_header_region' => Zones::pidNames([$inspect['hasOneStore']['zone_id']]),
                'inspect_detail_site' => $inspect['hasOneStore']['address'],
                'inspect_time' => $inspect->inspect_time,
                'keywords' => $inspect['hasOneStore']['hasOneBrand']['keywords'] ? explode(' ', $inspect['hasOneStore']['hasOneBrand']['keywords']) : [],
            ];

            if ($result['action_tags'] === self::CONSENT) {
                $inspect_data['default_money'] = number_format($inspect->default_money);
                //判断该用户是否有邀请红包
                $inspect_data['can_deduction'] = 0;

                //类型为3的永远只有一条
                $packet = RedPacket::where('type', 3)->first();

                if ($packet) {
                    $person = RedPacketPerson::where('receiver_id', $inspect['hasOneUsers']['uid'])
                        ->where('red_packet_id', $packet->id)->where('status', 0)
                        ->first();
                }

                if (!empty($person)) {
                    $inspect_data['can_deduction'] = 1;
                }
            }

            //返回组合后的数据信息
            return ['message' => ['agent' => $agent, 'inspect' => $inspect_data], 'status' => true];
        }

    }


}