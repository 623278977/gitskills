<?php namespace App\Models\Agent;

use App\Models\Agent\Score\AgentScoreLog;
use App\Models\AgentScore;
use App\Models\Brand\BrandContactor;
use App\Models\Brand\BrandStore;
use App\Models\Categorys;
use App\Models\LoginLog;
use App\Models\News\Entity\AgentNews;
use App\Models\User\Entity as User;
use App\Models\Video\Entity\AgentVideo;
use App\Services\News;
use DB;
use foo\func;
use Illuminate\Database\Eloquent\Model;
use App\Services\Version\Brand\_v020700 as Brands;
use App\Models\Brand\Entity as Brand;
use App\Models\Activity\Entity as Activitys;
use App\Models\Agent\AgentBrand;
use App\Models\Agent\AgentCustomer;
use App\Models\Zone\Entity as Zones;
use App\Models\Agent\AgentLevel;
use App\Models\Agent\AgentAchievementLog;
use App\Models\Agent\Invitation;
use App\Models\Contract\Contract;
use Illuminate\Support\Collection;
use App\Models\Zone\Entity as Zone;
use App\Models\Config;
use App\Models\Orders\Entity as Orders;
use App\Models\Orders\Items;
use App\Models\Identify as Identify;
use App\Models\Agent\AgentCustomerLog;
use App\Models\Agent\AgentKeyword;
use App\Models\Agent\AgentCategory;
use App\Models\Brand\Entity\V020800 as Brandss;
use App\Models\News\Entity as Newss;
use Illuminate\Support\Facades\Hash;
use App\Models\User\Favorite;
use App\Models\Activity\Sign;
use App\Models\Live\Subscribe as Subscription;
use App\Models\Agent\BrandAgentCompleteQuiz;
use App\Models\Brand\BrandVideo;
use App\Models\Agent\AgentDevelopTeamLog;
use App\Models\RedPacket\RedPacket;
use App\Models\Agent\Entity\_v010200 as Agentv010200;
use App\Models\Activity\Brand as ActivityBrand;
class Agent extends Model
{
    const AlreadyAgency  = 4;    //已代理
    const APPLY_ONE      = 1;    //申请中
    const APPLY_TWO      = 2;    //申请中
    const APPLY_three    = 3;    //申请中
    const RECOMMENT_TYPE = 8;    //推荐客户标记
    const FRIENDS_TYPE   = 9;    //添加好友标记
    const INSPECT_TYPE   = 2;    //考察邀请函类型
    const CONSENT_TYPE   = 1;    //接受考察邀请函
    const NUMBER_ZERO    = 0;    //数字 0
    const REJECT_TYPE    = -1;   //拒绝考察邀请函
    const LOST_CUSTOMER_TYPE = -1; //失去客户标记
    const C_ENABLE       = 'enable';    //c端品牌状态

    public static $info_type = 'RC:TxtMsg';  //融云消息类型
    public static $channel_type = 'PERSON';     //融云会话类型

    //默认个性签名
    public static $sign = '为你提供优质的加盟服务';

    public static function getSign()
    {
        return self::$sign;
    }


    protected $table = "agent";
    //黑名单
    protected $guarded = [];

    private static $params;

    protected $dateFormat = 'U';

    public static $instance = null;

    public static function instance()
    {
        if (is_null(self::$instance)) {
            self::$instance = new self;
        }

        return self::$instance;
    }

    //经济人级别
    public static $Agentlevel = [
        '1' => '初级经纪人',
        '2' => '中级经纪人',
        '3' => '高级经纪人',
    ];

    /**
     * 关联：经纪人行业品牌
     */
    public function belongsToManyIndustryBrand()
    {
        return $this->belongsToMany(Categorys::class, 'agent_category', 'agent_id', 'category_id');
    }

    /**
     * 关联：用户业绩
     */
    public function hasOneAgentAchievement()
    {
        return $this->hasOne(AgentAchievement::class, 'agent_id', 'id');
    }


    /**
     * 关联：用户业绩
     */
    public function hasManyAgentAchievement()
    {
        return $this->hasMany(AgentAchievement::class, 'agent_id', 'id');
    }

    /**
     * 关联：用户级别
     */
    public function hasOneAgentLevel()
    {
        return $this->hasOne(AgentLevel::class, 'id', 'agent_level_id');
    }

    /**
     * 关联：经纪人客户
     */
    public function hasManyCustomer()
    {
        return $this->hasMany(AgentCustomer::class, 'agent_id', 'id');
    }

    /**
     * 关联：邀请函
     */
    public function hasManyInvite()
    {
        $param = self::$params;
        if (empty($param) || !isset($param)) {
            return $this->hasMany(Invitation::class, 'agent_id', 'id');
        } else {
            return $this->hasMany(Invitation::class, 'agent_id', 'id')
                ->where('type', 2)
                ->offset(($param['page'] - 1) * $param['page_size'])
                ->limit($param['page_size']);
        }
    }

    /**
     * 关联：经纪人代理品牌
     */
    public function belongsToManyAgentBrand()
    {
        return $this->belongsToMany('App\Models\Brand\Entity', 'agent_brand', 'agent_id', 'brand_id');
    }

    /*
     *
     *关联经纪人品牌表
     * */
    public function agent_brand()
    {
        return $this->hasMany(AgentBrand::class, 'agent_id', 'id');
    }

    /**
     * 关联：经纪人地区
     */
    public function hasOneZone()
    {
        return $this->hasOne(\App\Models\Zone::class, 'id', 'zone_id');
    }


    /**
     * 关联：父经纪人
     */
    public function pAgent()
    {
        return $this->hasOne(self::class, 'non_reversible', 'register_invite');
    }

    /*
     * 关联：子经纪人
     * */
    public function c_agent(){
        return $this->hasMany(self::class , 'register_invite','non_reversible');
    }


    /**
     * 关联：经纪人地区
     */
    public function zone()
    {
        return $this->hasOne(Zone::class, 'id', 'zone_id');
    }

    /*
     * 关联：经纪人拉新拓客表
     * */
    public function agent_develop_team_log(){
        return $this->hasMany(AgentDevelopTeamLog::class , 'agent_id','id');
    }

    /*
     * 关联：经纪人和经纪人好友关系表
     * */
    public function agent_friends_relation(){
        return $this->hasMany(AgentFriendsRelation::class , 'execute_agent_id' , 'id');
    }

    /*
     * 关联：经纪人和经纪人好友关系表
     * */
    public function agent_friends_relation1(){
        return $this->hasMany(AgentFriendsRelation::class , 'relation_agent_id' , 'id');
    }

    public function agent_add(){
        return $this->hasOne(AgentAdd::class , 'agent_id' , 'id');
    }



    /*
    * 规则：if 非经纪人端调用
        *       if  真实名字不存在  or  经纪人设置不公开
        *           显示昵称
        *       else
        *           if  不加括号
        *               真实名字
        *           else
        *               昵称（真实名字）
    *       else
    *           if  加括号
    *           真实名字？：昵称
    *
    * param: $agent  是agent数组或对象
    *       $add    ‘’代表不加括号，‘add’代表加括号 昵称（真实姓名）
    *
    * */
    public static function unifiHandleName($agent, $add = '', $source = '')
    {
        $A = empty($agent['realname']);
        $B = $source != 'agent';
        $C = empty($agent['is_public_realname']);
        $D = empty($add);
        if ($A || ($B && $C)) {
            return $agent['nickname'];
        } else {
            if (((($B && !$C) || !$A) && $D)) {
                return $agent['realname'];
            } else {
                return $agent['nickname'] . "({$agent['realname']})";
            }
        }
    }

    /*
     * 获取经纪人的等级序号
     * */
    public static function getAgentLevelNum($agentLevelId = 1)
    {
        if (empty($agentLevelId)) {
            return 1;
        }
        $allLevelArr = array_flatten(AgentLevel::orderBy('min', 'asc')->get(['id'])->toArray());
        $key = array_search($agentLevelId, $allLevelArr);
        return $key + 1;
    }

    /*
  * 按规则生成不重复不含4的8位邀请码
  *
  */
    public static function createInviteNum($model, $field)
    {
//        $inviteContainer = ['0','1','2','3','5','6','7','8','9',
//            'a','b','c','d','e','f','g','h','i','g','k','l','m','n','o','p','q','r','s','t','u','v','w','x','y','z',
//            'A','B','C','D','E','F','G','H','I','G','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z'
//        ];
//
//        $inviteArr = array_rand($inviteContainer, 6);
//        $inviteCode = implode('',$inviteArr);
        $numArr = ['0', '1', '2', '3', '5', '6', '7', '8', '9'];
        shuffle($numArr);
        $inviteCode = (string)rand(100001, 999999);
        if (strpos($inviteCode, '4') !== false) {
            $inviteCode = str_replace('4', $numArr[0], $inviteCode);
        }
        $isHave = $model::where($field, $inviteCode)->first();
        if (is_object($isHave)) {
            return self::createInviteNum($model, $field);
        }
        return $inviteCode;
    }

    /**
     * 经纪人首页基本数据
     *
     * @param $agent_id
     * @param $page
     * @param $page_size
     * @return array
     */
    public function agentIndex($agent_id, $page, $page_size, $version = null)
    {
        //数据集合
        $gather_result = self::with(['hasOneAgentAchievement' => function ($query) {
                $query->where('quarter', getQuarterFormat());
            }, 'hasOneAgentLevel']
        )
            ->where('id', $agent_id)
            ->first();

        //获取经济人离下一级还有多少单
        $current_quarter_achievement = $gather_result['hasOneAgentAchievement']['my_achievement'];
        $current_level  = $gather_result['hasOneAgentLevel']['id'];
        $max_bill       = $gather_result['hasOneAgentLevel']['max'];
        $bill_result    = $max_bill - $current_quarter_achievement;

        if ($current_level < 3) {
            if ($bill_result > 0) {
                $next_level_bill = $bill_result + 1;
            } else {
                if ($bill_result < 0) {
                    $result_bill = $this->AgentUpgrade($agent_id, ++$current_level);
                    $new_bill_result = $result_bill->hasOneAgentLevel->max - $current_quarter_achievement;

                    if ($new_bill_result > 0) {
                        $next_level_bill = $bill_result + 1;
                    } else {
                        if ($new_bill_result < 0) {
                            $this->AgentUpgrade($agent_id, ++$current_level);
                            $next_level_bill = 0;
                        }
                        if ($new_bill_result == 0) {
                            $next_level_bill = 1;
                        }
                    }
                }
                if ($bill_result == 0) {
                    $next_level_bill = 1;
                }
            }
        } else {
            $next_level_bill = 0;
        }

        //组合数据
        $brand_data = [
            'nickname'  => $gather_result['realname'] ?: $gather_result['nickname'],
            'avatar'    => getImage($gather_result['avatar'],''),
            'orders'    => $gather_result['hasOneAgentAchievement']['my_achievement'],
            'is_online' => $gather_result['is_online'],
            'level'     => $current_level,
            'next_level_bill' => $next_level_bill,
            'register_invite' => trim($gather_result['register_invite']),
        ];

        $brand_result = $this->agentRecommendBrand($agent_id, $page, $page_size, $version);   //获取品牌信息
        $new_list     = AgentNews::instance()->newsList($page, $page_size);         //获取资讯列表
        //$study_schedule = $this->studySchedule($agent_id, $brand_result['agent_apply_brand']);  // 获取学习的总进度

        //返回组合后的综合信息
        return [
            'agent_header_info'     => $brand_data,
            'agent_agency_brand'    => $brand_result['agent_agency_brand']   ?   $brand_result['agent_agency_brand']    : [],
            'agent_apply_brand'     => $brand_result['agent_apply_brand']    ?   $brand_result['agent_apply_brand']     : [],
            'rand_recommend_brand'  => $brand_result['rand_recommend_brand'] ?   $brand_result['rand_recommend_brand']  : [],
            'new_list'              => $new_list ? $new_list : [],
            'is_alter_nickname'     => isset($gather_result['is_alter_nickname']) ? trim($gather_result['is_alter_nickname']) : '' ,
        ];
    }

    /**
     * 获取向经纪人推荐的品牌（包含已代理和代理中的）
     *
     * @internal param 经纪人ID $agent_id
     *
     * @param $agent_id
     * @param $page
     * @param $page_size
     * @return data_list|array
     */
    public function agentRecommendBrand($agent_id, $page, $page_size, $version = null)
    {
        $gather_result = self::with(['belongsToManyAgentBrand' => function ($query) use ($page, $page_size) {
            $query->whereIn('agent_brand.status', [1, 2, 3, 4])
                ->select('agent_brand.status',
                    'agent_brand.brand_id',
                    'agent_brand.agent_id',
                    'agent_brand.created_at',
                    'agent_brand.updated_at',
                    'brand.id', 'brand.name',
                    'brand.logo', 'brand.brand_summary', 'brand.slogan',
                    'brand.rebate', 'brand.categorys1_id')
                ->orderBy('agent_brand.updated_at');
        }, 'belongsToManyAgentBrand.categorys1' => function ($query) {
            $query->select('categorys.id', 'categorys.name');
        }])
            ->select('id')
            ->where('id', $agent_id)
            ->first();

        //循环获取代理和未代理的品牌以及推荐的品牌
        foreach ($gather_result['belongsToManyAgentBrand'] as $key => $vs) {
            $brand_results = Brand::where('id', $vs['id'])
                ->where('agent_status', self::CONSENT_TYPE)
                ->where('status',       self::C_ENABLE)
                ->first();

            if ($brand_results) {
                if ($vs['status'] == self::AlreadyAgency) {  //已代理的品牌

                    //新版本增加首页新代理成功品牌的红点提示
                    if ($version && $version >= '0101') {
                        $days = ceil((time() - strtotime($vs['updated_at'])) / 86400);
                        if ($days > 0 && $days < 3) {
                            $is_index_red_show = self::APPLY_ONE;
                        } else {
                            $is_index_red_show = self::NUMBER_ZERO;
                        }
                    }

                    $agent_agency_brand[] = [
                        'id'            => $vs['id'],
                        'title'         => $vs['name'],
                        'logo'          => getImage($vs['logo'], 'avatar', ''),
                        'category_name' => $vs['categorys1']['name'],
                        //'commission'    => number_format(Brandss::instances()->getMaxCommission($vs['id'])),
                        'commission'    => Brandss::instances()->getMaxCommission($vs['id'], true),
                        'brand_summary' => empty($vs['brand_summary']) ? trim(str_replace("\r\n\t", "", mb_substr(strip_tags($vs['details']), 0, 50))) : trim(str_replace("\r\n\t", "", strip_tags($vs['brand_summary']))),
                        'status'        => $vs['status'],
                        'documentary'   => $this->getFollowCustomer($agent_id, $vs['id']),
                        'slogan'        => $vs['slogan'],
                        'updated_at'    => strtotime($vs['updated_at']),
                        'is_index_red_show' => isset($is_index_red_show) ?  $is_index_red_show : 0,
                    ];
                }
                if ($vs['status'] == self::APPLY_ONE ||      //申请代理的品牌
                    $vs['status'] == self::APPLY_TWO ||
                    $vs['status'] == self::APPLY_three
                ) {

                    //对版本进行兼容处理
                    if ($version && $version > '03') {
                        $agent_apply_brand[] = [
                            'id'            => $vs['id'],
                            'title'         => $vs['name'],
                            'logo'          => getImage($vs['logo'], 'avatar', ''),
                            'category_name' => $vs['categorys1']['name'],
                            //'commission'    => number_format(Brandss::instances()->getMaxCommission($vs['id'], true)),
                            'commission'    => Brandss::instances()->getMaxCommission($vs['id'], true),
                            'brand_summary' => empty($vs['brand_summary']) ? trim(str_replace("\r\n\t", "", mb_substr(strip_tags($vs['details']), 0, 50))) : trim(str_replace("\r\n\t", "", strip_tags($vs['brand_summary']))),
                            'status'        => $vs['status'],
                            'documentary'   => '申请代理中',
                            'slogan'        => $vs['slogan'],
                            'studySchedule' => BrandChapter::getChapterCompleteness($vs['id'], $agent_id)
                        ];
                    } else {
                        $agent_apply_brand[] = [
                            'id'            => $vs['id'],
                            'title'         => $vs['name'],
                            'logo'          => getImage($vs['logo'], 'avatar', ''),
                            'category_name' => $vs['categorys1']['name'],
                            //'commission'    => number_format(Brandss::instances()->getMaxCommission($vs['id'])),
                            'commission'    => Brandss::instances()->getMaxCommission($vs['id'], true),
                            'brand_summary' => empty($vs['brand_summary']) ? trim(str_replace("\r\n\t", "", mb_substr(strip_tags($vs['details']), 0, 50))) : trim(str_replace("\r\n\t", "", strip_tags($vs['brand_summary']))),
                            'status'        => $vs['status'],
                            'documentary'   => '申请代理中',
                            'slogan'        => $vs['slogan'],
                            'studySchedule' => $this->studySchedule($agent_id, $vs['id']),
                        ];
                    }
                }
            }
        }

        //随机返回推荐的品牌（4个）
        $brand_id = AgentBrand::where('agent_id', $agent_id)
            ->select('brand_id')->get()->toArray();

        //版本处理
        if ($version && $version >= '0101') {

            //获取随机推荐的品牌
            $rand_brand_data = Brand::with('categorys1')
                ->orderByRaw('RAND()')
                ->where('agent_status', self::CONSENT_TYPE)
                ->where('status', self::C_ENABLE)
                ->whereNotIn('id', array_flatten($brand_id));

            //获取后台设置显示的品牌
            $back_brand_data = Brand::with('categorys1')
                ->where([
                    'agent_status' => self::CONSENT_TYPE,
                    'status'       => self::C_ENABLE,
                    'is_manual_show' => 1,
                ])
                ->orderBy('agent_sort', 'desc')
                ->whereNotIn('id', array_flatten($brand_id))
                ->limit(4);

            //三种操作
            $back_data      = $back_brand_data->get();                  //1、获取数据
            $back_brand_id  = $back_brand_data->select('id')->get();    //2、获取后台手动设置的品牌ID
            $back_num       = $back_brand_data->count();                //3、获取后台手动设置推荐品牌显示的个数

            //当后台手动设置的品牌数大于0时，走组合，否则走随机推荐
            if ($back_num) {
                if ($back_num < self::AlreadyAgency) {
                    $ids = array();
                    foreach ($back_brand_id as $id) { $ids[] = $id->id; }
                    $rand_brand_result    = $rand_brand_data->whereNotIn('id', $ids)->limit(self::AlreadyAgency - $back_num)->get();
                    $confirm_brand_result = array_merge($back_data->toArray(), $rand_brand_result->toArray());
                } else {
                    $confirm_brand_result = $back_data;
                }
            } else {
                $confirm_brand_result = $rand_brand_data->limit(4)->get();
            }

            //组合结果
            foreach ($confirm_brand_result as $key => $vs) {
                $rand_recommend_brand[] = [               //推荐的品牌
                    'id'            => $vs['id'],
                    'title'         => $vs['name'],
                    'logo'          => getImage($vs['logo'], 'avatar', ''),
                    'category_name' => $vs['categorys1']['name'],
                    //'commission'    => number_format(Brandss::instances()->getMaxCommission($vs['id'])),
                    'commission'    => Brandss::instances()->getMaxCommission($vs['id'], true),
                    'status'        => $vs['status'],
                    'brand_summary' => empty($vs['brand_summary']) ? str_replace("\r\n\t", "", trim(mb_substr(strip_tags(trim($vs['details'])), 0, 50))) : str_replace("\r\n\t", "", trim(strip_tags(trim($vs['brand_summary'])))),
                    'is_recommend'  => $vs['is_recommend'] == 'yes' ? 1 : 0,
                    'slogan'        => $vs['slogan'],
                    'is_manual_show'=> isset($vs['is_manual_show']) ?  $vs['is_manual_show'] : 0
                ];
            }
        } else {

            //获取随机推荐的品牌
            $rand_brand_data = Brand::with('categorys1')
                ->orderByRaw('RAND()')
                ->where('agent_status', self::CONSENT_TYPE)
                ->where('status',       self::C_ENABLE)
                ->whereNotIn('id', array_flatten($brand_id))
                ->limit(4)
                ->get();

            foreach ($rand_brand_data as $key => $vs) {
                $rand_recommend_brand[] = [               //推荐的品牌
                    'id'            => $vs['id'],
                    'title'         => $vs['name'],
                    'logo'          => getImage($vs['logo'], 'avatar', ''),
                    'category_name' => $vs['categorys1']['name'],
                    //'commission'    => number_format(Brandss::instances()->getMaxCommission($vs['id'])),
                    'commission'    => Brandss::instances()->getMaxCommission($vs['id'], true),
                    'status'        => $vs['status'],
                    'brand_summary' => empty($vs['brand_summary']) ? str_replace("\r\n\t", "", trim(mb_substr(strip_tags(trim($vs['details'])), 0, 50))) : str_replace("\r\n\t", "", trim(strip_tags(trim($vs['brand_summary'])))),
                    'slogan'        => $vs['slogan'],
                ];
            }
        }

        //组合返回品牌数据（包含代理和未代理以及推荐的品牌）
        return [
            'agent_agency_brand'    => multiArraySort($agent_agency_brand,'updated_at'),   //已代理的品牌
            'agent_apply_brand'     => $agent_apply_brand,    //未代理的品牌
            'rand_recommend_brand'  => $rand_recommend_brand, //推荐的品牌
        ];
    }

    //返回指定品牌跟进的客户数量（包含拒绝、接受、和签订合同过的）
    public function getFollowCustomer($agent_id, $brand_id)
    {
        //当前品牌累计跟进客户数
        $total_customers = AgentCustomerLog::where('agent_id', $agent_id)
            ->where('brand_id', $brand_id)
            ->where('action', '<>', -1)
            ->groupBy('uid')
            ->get();

        return $total_customers ? count($total_customers) : 0;
    }

    /**
     * author zhaoyf
     *
     * 获取经纪人的学习进度
     *
     * @param $agent_id
     * @param $brand_id     品牌ID
     * @internal param 参数 $param
     *
     * @return array|string
     */
    public function studySchedule($agent_id, $brand_id)
    {
        $agent_brand_id = AgentBrand::where('agent_id', $agent_id)
            ->where('status', '<>', -2) //拒绝代理
            ->where('status', '<>', -1) //失去代理权
            ->where('brand_id', $brand_id)
            ->select('id')->get()->toArray();

        //处理获取到的品牌ID
        if (isset($agent_brand_id) && !empty($agent_brand_id)) {

            //格式化品牌ID
            $brand_ids = array_flatten($agent_brand_id);

            //获取经纪人已经完成的学习资料
            $perform_task = AgentBrandLog::whereIn('agent_brand_id', $brand_ids)
                ->where('action', self::APPLY_TWO)->get();

            //获取到经纪人学习的视频和资讯
            $video_count = 0;
            $news_count  = 0;
            foreach ($perform_task as $key => $vls) {
                if ($vls->type === 'video') {
                    ++$video_count;
                }
                if ($vls->type === 'news') {
                    ++$news_count;
                }
            }

            //根据品牌ID获取这个品牌总共的需要学习总资讯数
            $agent_news_result = Newss::where('type', 'agent')
                ->where('status', 'show')
                ->where('relation_id', $brand_id)
                ->count();

            //根据品牌ID获取这个品牌总共需要学习总的视频数
            $agent_video_result = DB::table('brand_video')
                ->where('brand_id', $brand_id)
                ->where('is_delete', 0)
                ->count();

            //返回最后结果
            return round(($news_count + $video_count) / ($agent_news_result + $agent_video_result) * 100) . "%";
        } else {
            return [];
        }
    }

    /**
     * 删除各种标签
     *
     * @param $tags
     * @param $str
     * @param bool $content
     * @return mixed
     */
    public function stripHtmlTags($tags, $str, $content = true)
    {
        $html = [];
        // 是否保留标签内的text字符
        if ($content) {
            foreach ($tags as $tag) {
                $html[] = '/(<' . $tag . '.*?>(.|\n)*?<\/' . $tag . '>)/is';
            }
        } else {
            foreach ($tags as $tag) {
                $html[] = "/(<(?:\/" . $tag . "|" . $tag . ")[^>]*>)/is";
            }
        }

        $data = preg_replace($html, '', $str);
        return $data;
    }

    /**
     * 经济人级别升级
     * @param $agent_id
     * @param $level
     *
     * @resutn level_data
     */
    private function AgentUpgrade($agent_id, $level)
    {
        self::where('id', $agent_id)->update(['agent_level_id' => $level]);
        $new_data = self::with('hasOneAgentLevel')
            ->select('id', 'agent_level_id')
            ->where('id', $agent_id)
            ->first();

        return $new_data;
    }

    /**
     * 获取经纪人的基本信息(星座，地区，年代 ...)
     * @param  $idcard 身份证号
     *
     * @return array|string
     */
    public function getAgentInfo($idcard)
    {
        $data = array();
        if ($idcard) return null;

        $address = getAddress($idcard);    //获取地区
        $times = getTime($idcard);       //获取某个年代
        $starsign = getStarsign($idcard);   //获取星座

        if (is_null(getAddress($address))) {
            return "addres_null";
        }
        if (is_null($times)) {
            return "times_null";
        }
        if (is_null($starsign)) {
            return "starsign_null";
        }

        $data['zone'] = $address->region;
        $data['times'] = $times;
        $data['starsign'] = $starsign;

        return $data;
    }

    /**
     * 获取经纪人感兴趣的行业和相关信息（zhaoyf）
     * @param   $agent_id 经纪人ID
     * @internal param    身份证号 $idcard
     *
     * @return AgentInfo|array|string
     */
    public function getAgentLikeIndustry($agent_id)
    {
        $data = array();

        //被查看经纪人的基本信息（成单数，等级...）
        $agent_result = self::with('hasManyAgentAchievement', 'hasOneAgentLevel')
            ->where('id', $agent_id)
            ->first();

        //获取经纪人代理的品牌
        $agent_brand = DB::table('agent_brand')
            ->leftJoin('brand', 'brand.id', '=', 'agent_brand.brand_id')
            ->leftJoin('categorys', 'categorys.id', '=', 'brand.categorys1_id')
            ->where('agent_id', $agent_id)
            ->where('agent_brand.status', self::AlreadyAgency)
            ->where('brand.status', 'enable')
            ->where('brand.agent_status', self::CONSENT_TYPE)
            ->get();

        if ($agent_brand) {
            $brand_data = array_map(function ($result) {
                $data['id'] = $result->brand_id;
                $data['name'] = Brand::where('id', $result->brand_id)->first()->name;
                $data['logo'] = Brand::where('id', $result->brand_id)->first()->logo;
                $data['category_name'] = $result->name;
                $data['investment_min'] = number_format($result->investment_min);
                $data['investment_max'] = number_format($result->investment_max);
                return $data;
            }, $agent_brand);
        }

        //组合数据 获取品牌个数，用户昵称，头像，性别，地区等...
        if ($agent_result) {
            $data['is_public_realname'] = $agent_result->is_public_realname;
            $data['realname']   = $agent_result->realname;
            $data['nickname']   = $agent_result->nickname;
            $data['avatar']     = getImage($agent_result->avatar, 'avatar', '');
            $data['level']      = $agent_result->hasOneAgentLevel->name;
            $data['level_id']   = $agent_result->hasOneAgentLevel->id;
            $data['gender']     = AgentScore::$AgentGender[$agent_result->gender];
            $data['sign']       = $agent_result->sign ? $agent_result->sign : '为你提供最佳的加盟服务';
            $data['username']   = Agent::getRealPhone($agent_result->non_reversible, 'agent');
            $data['non_reversible']  = $agent_result->non_reversible;
            $data['register_invite'] = $agent_result->register_invite;
            $data['zone']            = Zone::pidNames([$agent_result->zone_id]);
            $data['is_attestation']  = $agent_result->realname ? 1 : 0;
            $data['tags'][]          = getTime($agent_result->identity_card);
            $data['tags'][]          = getStarsign($agent_result->identity_card);
        } else {
            return "by_agent_null";
        }

        //获取用户感兴趣的行业
        //存在行业ID就获取用户感兴趣的行业分类
        $industry_id = DB::table('agent_category')
            ->where('agent_id', $agent_id)
            ->select(DB::raw('GROUP_CONCAT(category_id) as cate_id'))
            ->first();

        //获取经纪人感兴趣的行业
        if ($industry_id) {
            $like_industry = Categorys::whereIn('id', explode(',', $industry_id->cate_id))
                ->select('id', 'name')->limit(3)->get();

            foreach ($like_industry as $key => $vs) {
                $industry_data[$key]['like_industry'] = $vs->name;
                $data['tags'][] = $vs->name;
            }
        } else {
            $industry_data = [];
        }

        $data['brands_count'] = count($agent_brand) ? count($agent_brand) : 0; //代理品牌个数(有显示实际的个数，没有就是零)
        $data['brands'] = !empty($brand_data) ? $brand_data : '';
        $data['industry'] = !empty($industry_data) ? $industry_data : '';

        return $data;
    }

    /**
     * 获取登录经纪人和被查看经纪人的关系  --数据中心版
     *
     * @param $login_agent_id  登录的经济人ID
     * @param $username        经纪人账号
     * @param $by_examine
     * @return array|string
     * @by_examine $by_examine 被查看的经纪人ID
     *
     */
    public function getRelationAgent($login_agent_id, $username, $by_examine)
    {
        $data = array();
        $tags = false;

        if (!empty($login_agent_id) && isset($login_agent_id)) {

            //登录经济人
            $get_account = Agent::where('id', $login_agent_id)
                ->select('id', 'username', 'non_reversible', 'realname', 'register_invite')
                ->first();

            if (!$get_account) return ['relation' => 0, 'not_agent' => "not_agent"];

            // 查看登录经济人和被查看经纪人的关系
            // 1：是登陆经纪人的上线，
            // 2：是登陆经纪人的下线
            // 3：添加的好友关系
            // 0：没有关系，

            //从好友关系表里去获取对应的关系
            $relation_result_1 = DB::table('agent_friends_relation')->where([
                'execute_agent_id' => $login_agent_id,
                'relation_agent_id' => $by_examine,
            ])->first();

            $relation_result_2 = DB::table('agent_friends_relation')->where([
                'relation_agent_id' => $login_agent_id,
                'execute_agent_id' => $by_examine,
            ])->first();

            if ($get_account['register_invite'] == $username['non_reversible']) {
                $relation_tag = 1;  //登录经纪人是被查看经纪人的下级
            } elseif ($username['register_invite'] == $get_account['non_reversible']) {
                $relation_tag = 2;  //被查看经纪人是登录经纪人的下级
                $tags = true;
            } elseif (is_object($relation_result_1) || is_object($relation_result_2)) {
                $relation_tag = 3;    //添加的好友关系
            } else {
                $relation_tag = 0;    //没有关系
            }

            if ($tags) {
                $documentary_num = DB::table('agent')
                    ->leftJoin('agent_customer', 'agent_customer.agent_id', '=', 'agent.id')
                    ->where('agent.id', $by_examine)
                    ->whereIn('agent_customer.source', [1, 2, 3, 4, 5])
                    ->get();

                $i = 0;
                $new_num = array();
                if ($documentary_num) {
                    foreach ($documentary_num as $key => $value) {
                        if (!in_array($value->uid, $new_num)) {
                            $i++;
                        }
                        $new_num[] = $value->uid;
                    }
                }

                $data['follow_customers'] = $i;

                //获取下线经纪人的促单业绩
                $res = self::with('hasManyAgentAchievement')
                    ->where('id', $by_examine)->first();

                $documentarys = '';
                if ($res && !empty($res->hasManyAgentAchievement)) {
                    foreach ($res->hasManyAgentAchievement as $items) {
                        $data['quarter_follow_orders'] = $items->my_achievement;
                        $documentarys += $items->my_achievement;
                    }
                    $data['quarter_follow_orders'] = !empty($data['quarter_follow_orders']) ? $data['quarter_follow_orders'] : 0;
                } else {
                    $data['quarter_follow_orders'] = 0;
                }
                $data['total_follow_orders'] = !empty($documentarys) ? $documentarys : 0;

                $data['relation'] = $relation_tag;
                return $data;
            } else {
                $data['relation'] = $relation_tag;
                return $data;
            }
        } else {
            return null;
        }
    }

    /*
     * 获取指定id的经纪人的相关统计信息
     *
     * param:$agentId
     * return
     *
     * */
    public static function getStatisticInfo($agentId)
    {
        $agentInfo = self::where("id", $agentId)->where('status', 1)->first();
        if (is_object($agentInfo)) {
            $quarter = date('Y年m月');
            $totals = $agentInfo->hasManyAgentAchievement()
                ->select('my_achievement')
                ->sum('my_achievement');
            $agentLevels = AgentLevel::orderBy('min','asc')->get();
            $agentLevelName = '';
            foreach ($agentLevels as $oneLevel) {
                if ($oneLevel['max'] >= $totals) {
                    $agentLevelName = trim($oneLevel['name']);
                    break;
                }
            }
            $nameStr = empty($agentInfo['realname']) ? trim($agentInfo['nickname']) : trim($agentInfo['realname']);
            $data = array(
                'nickname' => $agentInfo['nickname'],
                'realname' => $agentInfo['realname'],
                'com_name' => $nameStr,
                'username' => getRealTel($agentInfo['non_reversible'],'agent'),
                'avatar' => getImage($agentInfo['avatar']),
                'auth_status' => empty($agentInfo['is_verified']) ? 0 : 1,
                'qcode' => trim($agentInfo['qcode']),
                'level' => $agentLevelName,
                'level_num' => self::getAgentLevelNum($agentInfo['agent_level_id']),
                'level_id' => trim($agentInfo['agent_level_id']),
                'is_pub_realname' => trim($agentInfo['is_public_realname']),
                'score' => trim($agentInfo['score']),
            );
            //判断是否是商务
            $brandContactorNum = BrandContactor::where('agent_id',$agentId)->count();
            $data['is_contactor'] = '0';
            if($brandContactorNum){
                $data['is_contactor'] = '1';
                $data['charge_brands'] = trim($brandContactorNum);
            }
            //获取本季度的字段

            $agentAchievement = $agentInfo->hasManyAgentAchievement()->where("month", $quarter)->first();
            $data['currency'] = doFormatMoney(floatval($agentInfo['currency']));
            if (is_object($agentAchievement)) {
                $data['frozen_currency'] = doFormatMoney(floatval($agentAchievement['frozen_commission']));
//                $data['currency']=doFormatMoney(floatval($agentAchievement['my_commission'] - $agentAchievement['frozen_commission']));
                $data['my_achieve'] = trim($agentAchievement['my_achievement']);
                $data['team_achieve'] = trim(intval($agentAchievement['team_achievement']) + $data['my_achieve']);
            } else {
                $data['frozen_currency'] = trim(0);
                $data['my_achieve'] = trim(0);
                $data['team_achieve'] = trim(0);
            }
            $contracts = Contract::where(function ($query) use ($agentId) {
                $query->where('agent_id', $agentId);
                $query->whereIn('status', [0,6,3]);
            })->count();
            $data['contracts'] = trim($contracts);

            $agentedBrands = AgentBrand::where("agent_id", $agentInfo['id'])->where('status', 4)->whereHas('brand', function ($query) {
                $query->where('status', 'enable');
                $query->where('agent_status', 1);
            })->count();
            $applyBrands = AgentBrand::where("agent_id", $agentInfo['id'])->whereIn('status', [1, 2, 3])->whereHas('brand', function ($query) {
                $query->where('status', 'enable');
                $query->where('agent_status', 1);
            })->count();
            $downLineCount = self::where('register_invite', $agentInfo['non_reversible'])->count();
//            $downLineCount=self::getAllDownlineTotal($agentInfo["username"]);
            $data['my_agents'] = trim($agentedBrands);
            $data['apply_agents'] = trim($applyBrands);
            $customers = AgentCustomer::where('agent_id',$agentId)->whereIn('source',[1,2,3,4,6,7])->count();
            $data['customers'] = trim($customers);
            $data['downlines'] = trim($downLineCount);
            return $data;
        } else {
            return false;
        }
    }

    /*
     * param : agentId 制定经纪人手机号码
     *      *
     * return  人数
     *
     * */

    public static function getAllDownlineTotal($agentPhone)
    {
        if (empty($agentPhone)) {
            return false;
        }
        $allAgent = self::all();
        $count = $allAgent->count();
        if ($count == 0) {
            return false;
        }
        $agentList = array();
        foreach ($allAgent as $agent) {
            $invitePhone = trim($agent['register_invite']);
            if (empty($invitePhone)) {
                $agentList[0][] = $agent;
            } else {
                $agentList[$invitePhone][] = $agent;
            }
        }
        $count = self::getDownLineCount($agentPhone, $agentList);
        return --$count;
    }

    //根据经纪人手机号获取其下属经纪人个数
    /*其中包括经纪人本人
     * param: $agentPhone   经纪人手机号，为0，获取所有的经纪人下线人数。
     * param:$arr           经过整理之后的数组，其每个元素的键是一个经纪人的手机号码（string类型）
     *                      值是一个数组，包含这个号码对应经纪人的所有下属的信息。
     *                      $arr=array(
     *                          '0'=[‘数据库取出的经纪人的信息记录’，‘数据库取出的经纪人的信息记录’，‘数据库取出的经纪人的信息记录’，]
     *                          '17196691952'=[‘数据库取出的经纪人的信息记录’，‘数据库取出的经纪人的信息记录’，‘数据库取出的经纪人的信息记录’，]
     *                      )
     * return 人数（int）
     *
     * */
    protected static function getDownLineCount($agentPhone, $arr)
    {
        if (!isset($arr[$agentPhone])) {
            return 1;
        }
        $sum = 1;
        foreach ($arr[$agentPhone] as $oneAgent) {
            $sum += self::getDownLineCount($oneAgent['username'], $arr);
        }
        return $sum;
    }

    /**
     * author zhaoyf
     *
     * 根据类型返回对应数据
     *
     * @param $result
     *
     * 类型：0:全部；1:我的团队；2:邀请投资人；3:跟进投资人；
     * 4：获取邀请和跟进投资人； 5：推荐投资人；默认0
     *
     * @return array
     */
    public function passTypeGetData($result)
    {
        if (!$result) return null;

        switch ($result['type']) {
            case 0:
                return $this->agentCommunication($result);
            case 1:
                return $this->agentBelow($result);
                break;
            case 2:
                return $this->agentInvite($result);
                break;
            case 3:
                return $this->agentFollowUp($result);
            case 4:
                return $this->agentFondAndFllowUpUser($result);
            case 5:
                return $this->agentRecommentCustomer($result);
            default:
                return $this->agentCommunication($result);
                break;
        }
    }

    /**
     * author zhaoyf   --数据中心版
     *
     * 经纪人通讯录
     *
     * @param $result
     * @internal param 经纪人ID $agent_id
     *
     * @return array
     */
    public function agentCommunication($result)
    {
        $gather_result = array();

        //获取当前经济人的账号（手机号）
        $username = self::where('id', $result['agent_id'])
            ->select('id', 'username', 'non_reversible', 'register_invite')->first();

        //获取我的下线经纪人
        $agent_down = self::where('register_invite', $username['non_reversible'])
            ->select('id', 'zone_id', 'is_public_realname', 'nickname', 'realname', 'avatar', 'username', 'non_reversible', 'is_verified')
            ->where('status', 1)->get();

        //添加type类型（当前经纪人的下线）
        if ($agent_down) {
            foreach ($agent_down as $ke => $vls) {
                $vls->avatar   = empty($vls->avatar) ? getImage('') : getImage($vls->avatar, 'avatar', '');
                //$vls->username = Agent::getRealPhone($vls->non_reversible, 'agent');
                $agent_down[$ke]['type'] = 1;
                $agent_down[$ke]['is_attestation'] = $vls->is_verified ? 1 : 0;
            }
            $gather_result[] = $agent_down;
        }

        //获取我的上线经纪人
        $agent_up = self::where('non_reversible', $username['register_invite'])
            ->select('id', 'zone_id', 'is_public_realname', 'nickname', 'realname', 'avatar', 'username', 'non_reversible', 'is_verified')
            ->where('status', 1)->get();

        //添加type类型，（当前经纪人的上线）
        if ($agent_up) {
            foreach ($agent_up as $kes => $vls) {
                $vls->avatar    = empty($vls->avatar) ? getImage('') : getImage($vls->avatar, 'avatar', '');
                //$vls->username  = Agent::getRealPhone($vls->non_reversible, 'agent');
                $agent_up[$kes]['type'] = 5;
                $agent_up[$kes]['is_attestation'] = $vls->is_verified ? 1 : 0;
            }
            $gather_result[] = $agent_up;
        }

        //添加type类型、（我添加的好友经纪人）
        $friends_agent_data = DB::table('agent_friends_relation')
            ->where('execute_agent_id', $result['agent_id'])
            ->select(DB::raw('GROUP_CONCAT(relation_agent_id) as relation_agents_id'))
            ->first();

        $relation_result_data = DB::table('agent_friends_relation')
            ->where('relation_agent_id', $result['agent_id'])
            ->select(DB::raw('GROUP_CONCAT(execute_agent_id) as relation_agent_id'))
            ->first();

        //添加好友后，在对方的通讯里都能看到
        if (!is_null($friends_agent_data->relation_agents_id)) {
            $agent_id_1 = explode(',', $friends_agent_data->relation_agents_id);
        }
        if (!is_null($relation_result_data->relation_agent_id)) {
            $agent_id_2 = explode(',', $relation_result_data->relation_agent_id);
        }

        //组合判断
        if ($agent_id_1 && $agent_id_2) {
            $agent_id = array_merge($agent_id_1, $agent_id_2);
        } elseif ($agent_id_1) {
            $agent_id = $agent_id_1;
        } elseif ($agent_id_2) {
            $agent_id = $agent_id_2;
        } else {
            $agent_id = null;
        }

        //对结果进行处理
        if (!is_null($agent_id)) {
            $relation_result = self::whereIn('id', $agent_id)
                ->select('id', 'is_public_realname', 'nickname', 'realname', 'avatar', 'gender', 'zone_id', 'is_verified', 'username', 'non_reversible')
                ->get();

            //对结果进行处理
            if (is_object($relation_result)) {
                foreach ($relation_result as $kes => $vls) {
                    $vls->avatar = getImage($vls->avatar, 'avatar', '');
                    //$vls->username = Agent::getRealPhone($vls->non_reversible, 'agent');
                    $relation_result[$kes]['type'] = 6; //好友关系
                    $relation_result[$kes]['is_attestation'] = $vls->is_verified ? 1 : 0;
                }
                $gather_result[] = $relation_result;
            }
        }

        //获取当前经纪人的客户（投资人：跟进投资人）
        $agent_current_contact_customer = DB::table('agent_customer')
            ->leftJoin('user', 'user.uid', '=', 'agent_customer.uid')
            ->leftJoin('brand', 'brand.id', '=', 'agent_customer.brand_id')
            ->where('agent_customer.agent_id', $result['agent_id'])
            ->where('agent_customer.status', 0)
            ->where('agent_customer.source', 5)
            ->select(
                DB::raw(
                    'lab_user.uid     as id,                 
                 lab_user.zone_id  as zone_id,     
                 lab_user.realname as realname,
                 lab_user.nickname as nickname,
                 lab_user.avatar   as avatar,
                 lab_user.username as username,
                 lab_user.non_reversible as non_reversible,
                 lab_brand.name    as title,
                 lab_agent_customer.has_tel as is_public_tel'
                ))
            ->get();

        //添加type类型（跟进投资人）
        if ($agent_current_contact_customer) {
            foreach ($agent_current_contact_customer as $ke => $vls) {
                $vls->realname  = empty($vls->realname) ? $vls->nickname : $vls->realname;
                $vls->avatar    = empty($vls->avatar) ? getImage('', 'avatar') : getImage($vls->avatar, 'avatar', '');
                //$vls->username  = Agent::getRealPhone($vls->non_reversible, 'wjsq');
                $agent_current_contact_customer[$ke] = (array)$vls;
                $agent_current_contact_customer[$ke]['type'] = 3;
            }
            $gather_result[] = $agent_current_contact_customer;
        }


        //获取当前经纪人的客户（投资人：邀请投资人）
        $agent_invite_customer = DB::table('agent_customer')
            ->leftJoin('user', 'user.uid', '=', 'agent_customer.uid')
            ->where('user.register_invite', $username['non_reversible'])
            ->orwhere(function ($query) {
                $query->whereIn('agent_customer.source', [1, 2, 3, 4]);
            })
            ->where('agent_customer.agent_id', $result['agent_id'])
            ->where('agent_customer.status', 0)
            ->select(
                DB::raw(
                    'lab_user.uid     as id,                 
                 lab_user.zone_id  as zone_id,     
                 lab_user.realname as realname,
                 lab_user.nickname as nickname,
                 lab_user.avatar   as avatar,
                 lab_user.username as username,        
                 lab_user.non_reversible as non_reversible,        
                 lab_agent_customer.has_tel as is_public_tel'
                ))
            ->get();

        //添加type类型（邀请投资人）
        if ($agent_invite_customer) {
            foreach ($agent_invite_customer as $kks => $vss) {
                $vss->realname  = empty($vss->realname) ? $vss->nickname : $vss->realname;
                $vss->avatar    = empty($vss->avatar) ? getImage('', 'avatar', '') : getImage($vss->avatar, 'avatar', '');
                //$vss->username  = Agent::getRealPhone($vss->non_reversible, 'wjsq');
                $agent_invite_customer[$kks] = (array)$agent_invite_customer[$kks];
                $agent_invite_customer[$kks]['type'] = 2;
            }
            $gather_result[] = $agent_invite_customer;
        }


        //获取当前经纪人的客户（投资人：推荐投资人）
        $agent_recomment_customer = DB::table('agent_customer')
            ->leftJoin('user', 'user.uid', '=', 'agent_customer.uid')
            ->where('agent_customer.agent_id', $result['agent_id'])
            ->where('agent_customer.source', 8)
            ->where('agent_customer.status', '<>', -1)
            ->select(
                DB::raw(
                 'lab_user.uid     as id,                 
                 lab_user.zone_id  as zone_id,     
                 lab_user.realname as realname,
                 lab_user.nickname as nickname,
                 lab_user.avatar   as avatar,
                 lab_user.username as username,
                 lab_user.non_reversible as non_reversible,        
                 lab_agent_customer.has_tel as is_public_tel'
                ))
            ->get();

        //添加type类型（推荐投资人）
        if ($agent_recomment_customer) {
            foreach ($agent_recomment_customer as $kks => $vss) {
                $vss->realname  = empty($vss->realname) ? $vss->nickname : $vss->realname;
                $vss->avatar    = empty($vss->avatar) ? getImage('', 'avatar', '') : getImage($vss->avatar, 'avatar', '');
                //$vss->username  = Agent::getRealPhone($vss->non_reversible, 'wjsq');
                $agent_recomment_customer[$kks] = (array)$agent_recomment_customer[$kks];
                $agent_recomment_customer[$kks]['type'] = 4;
            }
            $gather_result[] = $agent_recomment_customer;
        }

        //获取经纪人添加的好友（投资人好友）
        $friends_result = AgentCustomer::where([
            'agent_id' => $result['agent_id'],
            'source'   => self::FRIENDS_TYPE,
        ])
        ->select(DB::raw('GROUP_CONCAT(uid) as uid'))
        ->first();

        //对结果数进行处理
        if (is_object($friends_result)) {
            $friends_result_uid = explode(',', $friends_result->uid);
            $user_result = User::whereIn('uid', $friends_result_uid)
                ->select('uid', 'zone_id', 'realname', 'nickname', 'avatar', 'username', 'non_reversible')
                ->get();

            //对结果数据进行处理
            if (is_object($user_result)) {
                $new_datas = array();
                foreach ($user_result as $key => $vlas) {
                    $new_datas[$key]['id']       = $vlas->uid;
                    $new_datas[$key]['zone_id']  = $vlas->zone_id;
                    $new_datas[$key]['realname'] = empty($vlas->realname) ? $vlas->nickname : $vlas->realname;
                    $new_datas[$key]['nickname'] = $vlas->nickname;
                    $new_datas[$key]['avatar']   = getImage($vlas->avatar, 'avatar', '');
                    //$new_datas[$key]['username'] = Agent::getRealPhone($vlas->non_reversible, 'wjsq');
                    $new_datas[$key]['non_reversible'] = $vlas->non_reversible;
                    $new_datas[$key]['type']     = 7;
                }

                $gather_result[] = $new_datas;
            }
        }

        $result_data = array();
        if ($gather_result) {
            foreach ($gather_result as $ke => $vss) {
                foreach ($vss as $key => $vlas) {
                    $result_data[] = $vlas;
                }
            }
        } else {
            return null;
        }

        $confirm_result = $this->_removeRepitition($result_data);    //去重处理
        $data           = $this->_getZone($confirm_result);          //获取城市地区
        $new_data       = $this->_agentKsort($data);                 //进行字母排序

        return $new_data ? $new_data : null;
    }

    /**
     * author zhaoyf   -- 数据中心版
     *
     * 获取经纪人的团队（包含上下级）
     *
     * @param $result
     * @return array|null
     */
    public function agentBelow($result)
    {
        //获取当前经济人的账号（手机号）
        $username = self::where('id', $result['agent_id'])
            ->select('id', 'username', 'non_reversible', 'register_invite')->first();

        //获取我的下线经纪人
        $agent_down = self::where('register_invite', $username['non_reversible'])
            ->select('id', 'zone_id', 'is_public_realname', 'nickname', 'realname', 'avatar', 'username', 'non_reversible')
            ->where('status', 1)->get();

        //添加type类型，（当前经纪人的下线）
        if ($agent_down) {
            foreach ($agent_down as $key => $vls) {
                $vls->avatar   = empty($vls->avatar) ? getImage('') : getImage($vls->avatar, 'avatar', '');
                //$vls->username = Agent::getRealPhone($vls->non_reversible, 'agent');
                $agent_down[$key]['type'] = 1;
                $agent_down[$key]['is_attestation'] = $vls->realname ? 1 : 0;
            }
            $gather_results[] = $agent_down;
        }

        //获取我的上线经纪人  //todo 先给你注释
//        $agent_up = self::where('username', $username['register_invite'])
//            ->select('id', 'zone_id', 'is_public_realname', 'nickname', 'realname', 'avatar', 'username')
//            ->where('status', 1)->get();
//
//        //添加type类型，（当前经纪人的上线）
//        if ($agent_up) {
//            foreach ($agent_up as $ke => $vls) {
//                $vls->avatar = empty($vls->avatar) ? getImage('') : getImage($vls->avatar, 'avatar' , '');
//                $agent_up[$ke]['type'] = 5;
//                $agent_up[$ke]['is_attestation'] = $vls->realname ?  1 : 0;
//            }
//            $gather_results[] = $agent_up;
//        }

        $result_data = [];
        if ($gather_results) {
            foreach ($gather_results as $ke => $vss) {
                foreach ($vss as $key => $vlas) {
                    $result_data[] = $vlas;
                }
            }
        } else {
            return null;
        }

        $data     = $this->_getZone($result_data);     //获取地区
        $new_data = $this->_agentKsort($data);         //进行字母排序

        return $new_data ? $new_data : null;
    }

    /**
     * author zhaoyf   --数据中心版
     *
     * 获取经纪人的跟进投资人
     *
     * @param $result
     * @return array|null
     */
    public function agentFollowUp($result)
    {
        //获取当前经纪人的客户（投资人：跟进投资人）
        $data = DB::table('agent_customer')
            ->leftJoin('user', 'user.uid', '=', 'agent_customer.uid')
            ->leftJoin('brand', 'brand.id', '=', 'agent_customer.brand_id')
            ->where('agent_customer.agent_id', $result['agent_id'])
            ->where('agent_customer.status', 0)
            ->where('agent_customer.source', 5)
            ->select(
                DB::raw(
                    'lab_user.uid     as id,                 
                 lab_user.zone_id  as zone_id,     
                 lab_user.realname as realname,
                 lab_user.nickname as nickname,
                 lab_user.avatar   as avatar,
                 lab_user.username as username,
                 lab_user.non_reversible as non_reversible,
                 lab_brand.name    as title,
                 lab_agent_customer.has_tel as is_public_tel'
                ))
            ->get();

        if ($data) {
            foreach ($data as $ke => $vls) {
                $vls->realname = empty($vls->realname) ? $vls->nickname : $vls->realname;
                $vls->avatar   = getImage($vls->avatar, 'avatar', '');
                //$vls->username  = Agent::getRealPhone($vls->non_reversible, 'wjsq');
                $data[$ke]     = (array)$data[$ke];
                $data[$ke]['type'] = 3;
            }
        } else {
            return null;
        }

        $datas = array();
        foreach ($data as $key => $vs) {
            $datas[] = (array)$vs;
        }

        //去除重复
        $confirm_result = $this->_removeRepitition($datas);

        $data     = $this->_getZone($confirm_result);   //获取地区
        $new_data = $this->_agentKsort($data);          //进行字母排序

        return $new_data ? $new_data : null;
    }

    /**
     * author zhaoyf   --数据中心版
     *
     * 获取经济人邀请的投资人
     *
     * @param $result
     * @return array
     */
    public function agentInvite($result)
    {
        //获取当前经济人的账号（手机号）
        $username = self::where('id', $result['agent_id'])
            ->select('id', 'username','non_reversible')->first();

        //获取当前经纪人的客户（投资人：邀请投资人）
        $data = DB::table('agent_customer')
            ->leftJoin('user', 'user.uid', '=', 'agent_customer.uid')
            ->where('user.register_invite', $username['non_reversible'])
            ->orwhere(function ($query) {
                $query->whereIn('agent_customer.source', [1, 2, 3, 4]);
            })
            ->where('agent_customer.agent_id', $result['agent_id'])
            ->where('agent_customer.status', 0)
            ->select(
                DB::raw(
                    'lab_user.uid     as id,                 
                 lab_user.zone_id  as zone_id,     
                 lab_user.realname as realname,
                 lab_user.nickname as nickname,
                 lab_user.avatar   as avatar,
                 lab_user.username as username,        
                 lab_user.non_reversible as non_reversible,        
                 lab_agent_customer.has_tel as is_public_tel'
                ))
            ->get();

        //添加type类型（邀请投资人）
        if ($data) {
            foreach ($data as $kks => $vss) {
                $vss->realname = empty($vss->realname) ? $vss->nickname : $vss->realname;
                $vss->avatar = empty($vss->avatar) ? getImage('', 'avatar', '') : getImage($vss->avatar, 'avatar', '');
                //$vss->username  = Agent::getRealPhone($vss->non_reversible, 'wjsq');
                $data[$kks] = (array)$data[$kks];
                $data[$kks]['type'] = 2;
            }
        } else {
            return null;
        }

        $datas = array();
        foreach ($data as $key => $vs) {
            $datas[] = (array)$vs;
        }

        //去除重复
        $confirm_result = $this->_removeRepitition($datas);

        $data     = $this->_getZone($confirm_result);   //获取地区
        $new_data = $this->_agentKsort($data);          //进行字母排序

        return $new_data ? $new_data : null;
    }

    /**
     * author zhaoyf
     *
     * 返回经纪人的邀请和跟进投资人数据信息
     *
     * @param $result
     *
     * @return array
     */
    public function agentFondAndFllowUpUser($result)
    {
        $gather[] = $this->agentInvite($result);
        $gather[] = $this->agentFollowUp($result);

        foreach ($gather as $key => $vls) {
            foreach ($vls as $keys => $value) {
                foreach ($value as $k => $v) {
                    $results[] = $v;
                }
            }
        }

        $confirm_result = $this->_removeRepitition($results);         //去除重复
        $data           = $this->_getZone($confirm_result);           //获取地区
        $new_data       = $this->_agentKsort($data);                  //进行字母排序

        return $new_data ? $new_data : null;
    }

    /**
     * author zhaoyf
     *
     * 获取经纪人推荐的投资人
     *
     * @param $result
     *
     * @return array|null
     */
    public function agentRecommentCustomer($result)
    {
        //获取当前经纪人的客户（投资人：推荐投资人）
        $agent_recomment_customer = DB::table('agent_customer')
            ->leftJoin('user', 'user.uid', '=', 'agent_customer.uid')
            ->where('agent_customer.agent_id', $result['agent_id'])
            ->where('agent_customer.source', 8)
            ->where('agent_customer.status', '<>', -1)
            ->select(
                DB::raw(
                 'lab_user.uid     as id,                 
                 lab_user.zone_id  as zone_id,     
                 lab_user.realname as realname,
                 lab_user.nickname as nickname,
                 lab_user.avatar   as avatar,
                 lab_user.username as username,
                 lab_user.non_reversible as non_reversible,            
                 lab_agent_customer.has_tel as is_public_tel'
                ))
            ->get();

        //添加type类型（推荐投资人）
        if ($agent_recomment_customer) {
            foreach ($agent_recomment_customer as $kks => $vss) {
                $vss->realname  = empty($vss->realname) ? $vss->nickname : $vss->realname;
                $vss->avatar    = empty($vss->avatar) ? getImage('', 'avatar', '') : getImage($vss->avatar, 'avatar', '');
                //$vss->username  = Agent::getRealPhone($vss->non_reversible, 'wjsq');
                $agent_recomment_customer[$kks] = (array)$agent_recomment_customer[$kks];
                $agent_recomment_customer[$kks]['type'] = 4;
            }
        } else {
            return null;
        }

        $datas = array();
        foreach ($agent_recomment_customer as $key => $vs) {
            $datas[] = (array)$vs;
        }

        //去除重复
        $confirm_result = $this->_removeRepitition($datas);

        $data       = $this->_getZone($confirm_result);   //获取地区
        $new_data   = $this->_agentKsort($data);          //进行字母排序

        return $new_data ? $new_data : null;
    }

    /**
     * author zhaoyf
     *
     * 对中文的首字母进行排序
     *
     * @param $data
     * @return array
     */
    private function _agentKsort($data)
    {
        $new_array = array();
        foreach ($data as $keyss => $sett) {
            if (empty($sett['realname']) && empty($sett['nickname'])) {
                continue;
            }
            if ((!empty($sett['realname']) && is_numeric(substr($sett['realname'], 0, 1))) &&
                is_numeric(substr($sett['nickname'], 0, 1))
            ) {
                $sett['letter']         = '#';
                $new_array['special'][] = $sett;
            }
            if ((!empty($sett['realname']) && !is_numeric(substr($sett['realname'], 0, 1))) ||
                !is_numeric(substr($sett['nickname'], 0, 1))
            ) {

                $names  = $sett['realname'] ? $sett['realname'] : $sett['nickname'];
                $key    = getfirstchar($names);
                $sett['letter']     = $key;
                $new_array[$key][]  = $sett;
            }

        }
        ksort($new_array);

        return $new_array ? $new_array : null;
    }

    /**
     * author zhaoyf
     *
     * 根据ID获取城市地区
     */
    private function _cityZone($zone_id, $upid = 0)
    {
        $zone = Zones::pidNames($zone_id);
        return $zone;
    }

    /**
     * 获取城市和地区
     * @param $data
     * @return
     */
    private function _getZone($data)
    {
        foreach ($data as $keys => $vls) {
            $data[$keys] = $data[$keys];
            $data[$keys]['zone'] = $this->_cityZone([$vls['zone_id']]);
        }

        return $data;
    }


    /**
     * author zhaoyf
     *
     * 去重处理
     *
     * @param $data
     * @return array
     */
    public function _removeRepitition($data)
    {
        //去重处理
        $confirm_result = array();
        $centre_filer_id = array();

        foreach ($data as $k => $v) {
            if (!in_array($v['id'], $centre_filer_id)) {
                $confirm_result[] = $v;
            }
            $centre_filer_id[] = $v['id'];
        }
        unset($centre_filer_id);

        return $confirm_result;
    }

    /**
     * 获取邀请活动列表
     *
     * @param $param
     * @param $page
     * @param $page_size
     * @return array
     */
    public function inviteActivity($param, $page, $page_size)
    {
        //  todo 这个方法里的逻辑进行了重新处理 zhaoyf 2018-1-05

        $new_result_data = array(); //存储最终结果数据信息

        //获取活动所绑定的品牌全部是启用状态的下的活动ID
        $activity_ids = ActivityBrand::gainEnableBrandRelevanceToActivityIds();

        //对结果进行处理
        if (is_null($activity_ids) || empty($activity_ids)) {
            return '该活动没有对应绑定的品牌';
        }

        //根据指定的活动ID获取活动对象~
        $activity_result = Activitys::whereIn('id', $activity_ids)
                           ->where('end_time', '>', time());

        //模糊查询
        if ($param['hotwords']) {
            $activity_result->where('subject', 'like', '%' . $param['hotwords'] . '%');
        }

        //进行排序和获取指定字段处理
        $lists = $activity_result->orderBy('begin_time', 'desc')
                       ->select('id', 'list_img', 'share_image', 'begin_time', 'subject as title', 'publish_time')
                       ->get();

        if ($lists) {
            foreach ($lists as $k => $v) {
                $new_result_data[$k]['id']           = $v->id;
                $new_result_data[$k]['title']        = $v->title;
                $new_result_data[$k]['publish_time'] = $v->publish_time;

                if ($v->banner) {
                    $new_result_data[$k]['list_img'] = getImage($v->banner,  'activity', '',  0);
                } else {
                    $new_result_data[$k]['list_img'] = getImage($v->list_img, 'activity', '', 0);
                }

                // todo 添加分享图片字段 zhaoyf 2017-11-30
                $new_result_data[$k]['share_image'] = getImage($v->share_image, 'activity', '', 0);

                $maker = DB::table('activity_maker')
                    ->where('activity_id', $v->id)
                    ->where('status', 1)->first();

                $new_result_data[$k]['maker_id']    = $maker->maker_id;
                $new_result_data[$k]['begin_time']  = date("m月d日 H:i", $v->begin_time);
                $new_result_data[$k]['cities']      = implode(' ', Zone::instance()->getZone($v));

                if ($v->publish_time) {
                    $new_result_data[$k]['is_new'] = strtotime($v->publish_time . "+3 day") > time() ? 1 : 0;
                } else {
                    $new_result_data[$k]['is_new'] = 0;
                }
            }
        }

        return $new_result_data;
    }

    /**
     * 发送短信信息
     *
     * @param $mobile     手机号
     * @param $agent_id   注册成功的经纪人ID
     */
    public function sendInfo($mobile, $agent_id)
    {
        //为中国手机号发送短信信息（长度为11位）
        if (strlen($mobile) == 11) {
            $param = [
                'url' => substr(shortUrl('https://api.wujie.com.cn/webapp/agent/headline/detail?id=257&agent_id=2&is_share=1&from=singlemessage&isappinstalled=0'), 7)];

            //为避免骚扰用户，营销短信只允许在8点到22点发送'   --yaokai


            @SendTemplateSMS('agent_register_success_info', $mobile, 'agent_register_success_info', $param, '86', 'agent',false);
        }

        //发送站内消息（系统消息）
        createMessage(
            $agent_id,
            $title = trans('sms.system_info_head'),
            $content = trans('sms.system_info_body'),
            $ext = '',
            $end = '',
            $type = 1    //通知类型
        );
    }

    /**
     * author zhaoyf
     *
     * 经纪人考察邀请函
     * @param $param 参数集合
     * type： 1：接受，0：待确认，-1：拒绝，-2过期）
     *
     * @return data_result
     */
    public function getAgentInvites($param)
    {
        self::$params = $param; //传递参数给静态方法，是为了hasManyInvite()方法需要参数值
        $results = Invitation::with(['hasOneStore.hasOneBrand' => function ($query) {
            $query->where('status', 'enable')->where('agent_status', 1);
        }, 'hasOneUsers', 'hasOneStore.hasOneZone',
            'hasOneOrderItems' => function ($query) {
                $query->where('type', 'inspect_invite');
            }, 'hasOneOrderItems.orders', 'belongsToAgent'
        ])
            ->where('type', self::INSPECT_TYPE)
            ->where('agent_id', $param['agent_id'])
            ->get();

        //改变过期邀请函的状态为 -2
        foreach ($results as $K => $v) {
            event(new \App\Events\Invitation($v));
        }

        //待确认的考察邀请函
        if ($param['status'] == 0) {
            foreach ($results as $key => $res) {
                $brand_result = Brand::where('id', $res['hasOneStore']['hasOneBrand']['id'])->first()->agent_status;
                if ($brand_result == self::CONSENT_TYPE) {
                    if ($res['status'] == 0) {
                        $undetermined_result[$key] = [
                            'inspect_id'   => $res['id'],
                            'create_time'  => $res['created_at']->getTimestamp(),
                            'uid'          => array_get($res['hasOneUsers'], 'uid'),
                            'nickname'     => array_get($res['hasOneUsers'], 'nickname'),
                            'agent_name'   => $res->belongsToAgent->is_public_realname ? $res->belongsToAgent->nickname . '（' . $res->belongsToAgent->realname . '）' : $res->belongsToAgent->nickname,
                            'avatar'       => getImage(array_get($res['hasOneUsers'], 'avatar'), 'avatar', ''),
                            'brand_name'   => array_get($res['hasOneStore']['hasOneBrand'], 'name'),
                            'brand_logo'   => getImage(array_get($res['hasOneStore']['hasOneBrand'], 'logo')),
                            'store_name'   => array_get($res['hasOneStore'], 'name'),
                            'head_address' => array_get($res['hasOneStore']['hasOneZone'], 'name'),
                            'inspect_address' => array_get($res['hasOneStore'], 'address'),
                            'inspect_time'    => $res['inspect_time'],
                            'currency'        => $res['default_money'],
                            'status_summary'  => countDown($res['expiration_time']),
                        ];
                    }
                }
            }

            return $undetermined_result ? $undetermined_result : '';
        }

        //已接受的考察邀请函
        if ($param['status'] == 1) {
            foreach ($results as $key => $res) {
                $brand_result = Brand::where('id', $res['hasOneStore']['hasOneBrand']['id'])->first()->agent_status;
                if ($brand_result == self::CONSENT_TYPE) {
                    if ($res['status'] == 1 || $res['status'] == 2 || $res['status'] == 4 || $res['is_audit'] == 1) {
                        if (!empty($res['hasOneOrderItems'])) {
                            foreach ($res['hasOneOrderItems'] as $k => $vs) {
                                if ($vs['orders']['status'] == 'pay') {
                                    $confirm_result[$key] = [
                                        'inspect_id'   => $res['id'],
                                        'create_time'  => $res['created_at']->getTimestamp(),
                                        'uid'          => array_get($res['hasOneUsers'], 'uid'),
                                        'nickname'     => array_get($res['hasOneUsers'], 'nickname'),
                                        'agent_name'   => $res->belongsToAgent->is_public_realname ? $res->belongsToAgent->nickname . '（' . $res->belongsToAgent->realname . '）' : $res->belongsToAgent->nickname,
                                        'avatar'       => getImage(array_get($res['hasOneUsers'], 'avatar'), 'avatar', ''),
                                        'brand_name'   => array_get($res['hasOneStore']['hasOneBrand'], 'name'),
                                        'brand_logo'   => getImage(array_get($res['hasOneStore']['hasOneBrand'], 'logo')),
                                        'store_name'   => array_get($res['hasOneStore'], 'name'),
                                        'head_address' => array_get($res['hasOneStore']['hasOneZone'], 'name'),
                                        'inspect_address' => array_get($res['hasOneStore'], 'address'),
                                        'inspect_time'    => $res['inspect_time'],
                                        'currency'        => $res['default_money'],
                                        'pay_way'         => Orders::$_PAYWAY[array_get($vs['orders'], 'pay_way')],
                                        'status_summary'  => '已接受邀请',
                                        'confirm_time'    => $res['updated_at']->getTimestamp(),
                                        'status'          => $res['status']
                                    ];
                                }
                            }
                        }
                    }
                }
            }

            return $confirm_result ? $confirm_result : '';
        }

        //已拒绝的考察邀请函
        if ($param['status'] == -1) {
            foreach ($results as $key => $res) {
                $brand_result = Brand::where('id', $res['hasOneStore']['hasOneBrand']['id'])->first()->agent_status;
                if ($brand_result == self::CONSENT_TYPE) {
                    if ($res['status'] == -1) {
                        $reject_result[$key] = [
                            'inspect_id'   => $res['id'],
                            'create_time'  => $res['created_at']->getTimestamp(),
                            'uid'          => array_get($res['hasOneUsers'], 'uid'),
                            'nickname'     => array_get($res['hasOneUsers'], 'nickname'),
                            'agent_name'   => $res->belongsToAgent->is_public_realname ? $res->belongsToAgent->nickname . '（' . $res->belongsToAgent->realname . '）' : $res->belongsToAgent->nickname,
                            'avatar'       => getImage(array_get($res['hasOneUsers'], 'avatar'), 'avatar', ''),
                            'brand_name'   => array_get($res['hasOneStore']['hasOneBrand'], 'name'),
                            'brand_logo'   => getImage(array_get($res['hasOneStore']['hasOneBrand'], 'logo')),
                            'store_name'   => array_get($res['hasOneStore'], 'name'),
                            'head_address' => array_get($res['hasOneStore']['hasOneZone'], 'name'),
                            'inspect_address' => array_get($res['hasOneStore'], 'address'),
                            'inspect_time'    => $res['inspect_time'],
                            'currency'        => $res['default_money'],
                            'pay_way'         => Orders::$_PAYWAY[array_get($res['hasOneOrderItems']['orders'], 'pay_way')],
                            'status_summary'  => '已拒绝',
                            'reson'           => $res['remark'],
                            'confirm_time'    => $res['updated_at']->getTimestamp(),
                        ];
                    }

                    if ($res['status'] == -2) {
                        $reject_result[$key] = [
                            'inspect_id'   => $res['id'],
                            'create_time'  => $res['created_at']->getTimestamp(),
                            'uid'          => array_get($res['hasOneUsers'], 'uid'),
                            'nickname'     => array_get($res['hasOneUsers'], 'nickname'),
                            'agent_name'   => $res->belongsToAgent->is_public_realname ? $res->belongsToAgent->nickname . '（' . $res->belongsToAgent->realname . '）' : $res->belongsToAgent->nickname,
                            'avatar'       => getImage(array_get($res['hasOneUsers'], 'avatar'), 'avatar', ''),
                            'brand_name'   => array_get($res['hasOneStore']['hasOneBrand'], 'name'),
                            'brand_logo'   => getImage(array_get($res['hasOneStore']['hasOneBrand'], 'logo')),
                            'store_name'   => array_get($res['hasOneStore'], 'name'),
                            'head_address' => array_get($res['hasOneStore']['hasOneZone'], 'name'),
                            'inspect_address' => array_get($res['hasOneStore'], 'address'),
                            'inspect_time'    => $res['inspect_time'],
                            'currency'        => $res['default_money'],
                            'pay_way'         => Orders::$_PAYWAY[array_get($res['hasOneOrderItems']['orders'], 'pay_way')],
                            'status_summary'  => '已过期',
                            'reson'           => '已过期',
                            'confirm_time'    => $res['updated_at']->getTimestamp(),
                        ];
                    }
                }
            }

            return $reject_result ? $reject_result : '';
        }

    }

    public static function getAgentCard($agentId)
    {
        $agentInfo = self::where("id", $agentId)->first();
        if (is_object($agentInfo)) {
            $data = array(
                'avatar' => getImage($agentInfo['avatar']),
                'realname' => trim($agentInfo['realname']),
                'nickname' => trim($agentInfo['nickname']),
                'is_public_realname' => trim($agentInfo['is_public_realname']),
                'signature' => empty($agentInfo['sign']) ? self::getSign() : trim($agentInfo['sign']),
                'gender' => intval($agentInfo["gender"]),
                'level_name' => "",
                'zone_name' => "",
                'keywords' => array(),
                'my_agents' => 0,
            );
            $levelInfo = $agentInfo->hasOneAgentLevel()->first();
            if (is_object($levelInfo)) {
                $data['level_name'] = trim($levelInfo['name']);
                $data['level_id'] = trim($levelInfo['id']);
                $data['level_num'] = self::getAgentLevelNum($agentInfo["agent_level_id"]);
            }
            $zoneId = intval($agentInfo['zone_id']);
            if ($zoneId) {
                $data['zone_name'] = Zone::getCityAndProvince($zoneId);
            }

            $agentId = intval($agentInfo['id']);
            $brandInfos = AgentBrand::with('brand.categorys1')->where(function ($query) use ($agentId) {
                $query->where('status', 4);
                $query->where('agent_id', $agentId);
            })->get();

            $data['my_agents'] = $brandInfos->count();
            $brandData = array();
            foreach ($brandInfos as $brandInfo) {
                $brandData[] = array(
                    'id' => trim($brandInfo['brand']["id"]),
                    "logo" => getImage($brandInfo['brand']["logo"]),
                    "name" => trim($brandInfo['brand']["name"]),
                    "cateName" => trim($brandInfo['brand']['categorys1']["name"]),
                    "investment_min" => floatval($brandInfo['brand']["investment_min"]),
                    "investment_max" => floatval($brandInfo['brand']["investment_max"]),
                );
            }
            $data['brand'] = $brandData;
            //获取经纪人关键字
            $data['keywords'] = self::getAgentKeywords($agentId);
            return $data;
        } else {
            return false;
        }
    }

    public static function getAgentCard_v010001($agentId)
    {
        $agentInfo = self::with('hasOneAgentLevel', 'agent_brand.brand.categorys1')
            ->with(['agent_brand' => function ($query) {
                $query->where('status', 4);
            }])
            ->where('id', $agentId)
            ->first()->toArray();
        $data = [];

        //经纪人关注的品牌
        $brandData = [];
        foreach ($agentInfo['agent_brand'] as $brandInfo) {
            $brandData[] = array(
                'id' => trim($brandInfo['brand']["id"]),
                "logo" => getImage($brandInfo['brand']["logo"]),
                "name" => trim($brandInfo['brand']["name"]),
                "cateName" => trim($brandInfo['brand']['categorys1']["name"]),
                "investment_min" => trim(floatval($brandInfo['brand']["investment_min"])),
                "investment_max" => trim(floatval($brandInfo['brand']["investment_max"])),
            );
        }
        $data['avatar'] = getImage($agentInfo['avatar']);
        $data['realname'] = trim($agentInfo['realname']);
        $data['nickname'] = trim($agentInfo['nickname']);
        $data['com_name'] = '';
        $data['is_public_realname'] = trim($agentInfo['is_public_realname']);
        $data['signature'] = trim($agentInfo['sign']);
        $data['gender'] = trim($agentInfo['gender']);
        $data['level_name'] = trim($agentInfo['has_one_agent_level']['name']);
        $data['level_num'] = self::getAgentLevelNum($agentInfo["agent_level_id"]);
        $data['level_id'] = trim($agentInfo['agent_level_id']);
        $data['zone_name'] = Zone::getCityAndProvince($agentInfo['zone_id']);
        $data['my_agents'] = trim(count($agentInfo['agent_brand']));
        $data['brand'] = $brandData;
        $data['keywords'] = self::getAgentKeywords($agentId);
        $data['agent_id'] = trim($agentInfo['id']);
        $data['username'] = getRealTel($agentInfo['non_reversible'] , 'agent');
        $data['is_real_auth'] = empty($agentInfo['identity_card']) ? '0' : '1';
        return $data;
    }


    //获取经纪人的关键词
    public static function getAgentKeywords($agentId)
    {
        $agentInfo = self::with('hasOneZone')->where("id", $agentId)->first();
        if (!is_object($agentInfo)) {
            return false;
        }
        $agentInfo = $agentInfo->toArray();
        $data = array(
            "years" => "",
            "constellation" => "",
            "native" => "",
            "industrys" => array(),
            "evaluate" => array(),
        );
        //获取客户对经纪人的评价
        $keywordInfo = AgentKeyword::with(['keywords' => function ($query) {
            $query->where('type', 'agent');
            $query->where('status', 1);
        }])
            ->where('agent_id', $agentId)
            ->skip(0)->take(4)->get()->toArray();
        foreach ($keywordInfo as $oneKeyword) {
            $data['evaluate'][] = trim($oneKeyword['keywords']['contents']);
        }
        $cardid = trim($agentInfo["identity_card"]);
        if (!empty($cardid)) {
            $data["years"] = getTime($cardid);
            $data["constellation"] = getStarsign($cardid);
//            $data["native"]=getAddress($cardid);
            $data["native"] = $agentInfo['has_one_zone']['name'];
        }

        //获取经纪人所感兴趣的行业，最多显示3个行业，空缺则显示品牌数量最多的3个行业
        $categorysInfo = AgentCategory::with('categorys')->where('agent_id', $agentId)->get()->toArray();

        if (count($categorysInfo)) {
            foreach ($categorysInfo as $oneCategory) {
                $data["industrys"][] = ['name' => trim($oneCategory['categorys']['name'])];
            }
        } else {
            $CategorysArr = Categorys::with('brand')->get()->toArray();
            $Categorys = collect($CategorysArr)->sortByDesc(function ($item) {
                return count($item['brand']);
            })->take(3);
            foreach ($Categorys as $oneCate) {
                $data["industrys"][] = ['name' => trim($oneCate['name'])];
            }
        }
        return $data;
    }

    public static function getAgentDetail($agentId)
    {
        $agentInfo = self::where("id", $agentId)->first();
        if (is_object($agentInfo)) {
            $data = array(
                'username' => trim($agentInfo['username']),
                'nickname' => trim($agentInfo['nickname']),
                'avatar' => getImage($agentInfo['avatar']),
                'qcode' => trim($agentInfo['qrcode']),
                'realname' => trim($agentInfo['realname']),
                'signature' => empty($agentInfo['sign']) ? self::getSign() : trim($agentInfo['sign']),
                'id_card' => trim($agentInfo['identity_card']),
                'birth' => trim($agentInfo['birth']),
                'email' => trim($agentInfo['email']),
                'edu' => trim($agentInfo['diploma']),
                'profession' => trim($agentInfo['profession']),
                'earning' => trim($agentInfo['earning']),
                'zone_id' => trim($agentInfo['zone_id']),
                'is_public_realname' => trim($agentInfo['is_public_realname']),
                "auth_status" => "",
                'gender' => "",
                'zone_name' => "",
                'industry' => array(),
            );
            $gender = intval($agentInfo['gender']);
            switch ($gender) {
                case '-1':
                    $data['gender'] = "不明";
                    break;
                case '0':
                    $data['gender'] = "女";
                    break;
                case '1':
                    $data['gender'] = "男";
                    break;
            }
            $zoneId = intval($agentInfo['zone_id']);
            if ($zoneId) {
                $zoneInfo = Zone::where("id", $zoneId)->first();
                if (is_object($zoneInfo)) {
                    $data['zone_name'] = trim($zoneInfo['name']);
                }
            }
            $industryArr = $agentInfo->belongsToManyIndustryBrand()->select('id', 'name')->get()->toArray();
            $data["industry"] = $industryArr;
            return $data;
        } else {
            return false;
        }
    }

    /**
     * 二维码生成  --弃用   数据中心不处理
     */
    public function createQrCode($agent_id)
    {
        $result = Agent::where('id', $agent_id)->first();
        $account = $result->username;
        $agent_detail_url = "http://mt.wujie.com.cn/agent/index/account";

        $chl = "BEGIN:VCARD\nVERSION:3.0" . //vcard头信息
            "\nN:经济人id:$agent_id" .
            "\nTEL:经济人账号:$account" .
            "\nURL:经济人详细页:$agent_detail_url" .
            "\nEND:VCARD";

        $fileName = unique_id() . ".png";

        return img_create($chl, $fileName);
    }

    public static function getAgentLevel($agentId)
    {
        $agentInfo = self::where('id', $agentId)->first();
        if (is_object($agentInfo)) {
            $agentAchievementInfo = $agentInfo->hasManyAgentAchievement()->get();
            $myAchieveNum = intval(collect($agentAchievementInfo)->sum('my_achievement'));
            $agentLevels = AgentLevel::get()->toArray();
            if (count($agentLevels)) {
                $agentLevelCollect = collect($agentLevels);
                $sortAgentLevel = $agentLevelCollect->sortBy('min');
                $k = 0;
                foreach ($sortAgentLevel as $agentLevelInfo) {
                    if ($myAchieveNum < $agentLevelInfo['min']) {
                        $k = 1;
                        $nextLevel = $agentLevelInfo;
                        break;
                    }
                    $currLevel = $agentLevelInfo;
                }
                $summary = $currLevel['description'];
                $summaryArr = [];
                if (!empty($summary)) {
                    $summaryArr = explode('-', $summary);
                    $summaryArr = array_filter($summaryArr);
                }
                $data = [];
                if ($k) {
                    $needs = $nextLevel['min'] - $myAchieveNum;
                    $data = array(
                        'level' => trim($currLevel['name']),
                        'avatar' => getImage($agentInfo['avatar']),
                        'my_orders' => $myAchieveNum,
                        'next_level' => $nextLevel['name'],
                        'next_level_need_orders' => $needs,
                    );
                } else {
                    $data = array(
                        'level' => trim($currLevel['name']),
                        'avatar' => getImage($agentInfo['avatar']),
                        'my_orders' => $myAchieveNum,
                    );
                }
                $data['summary'] = $summaryArr;
                return $data;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    public static function getSubordinateList($agentId)
    {
        $agentInfo = self::where('id', $agentId)->where('status',1)->first();
        if (is_object($agentInfo)) {
            //我的上级id
            $superior = 0;
            $registerInvite = trim($agentInfo['register_invite']);
            if (!empty($registerInvite)) {
                $superiorInfo = self::where('username', $registerInvite)->where('status',1)->first();
                if (is_object($superiorInfo)) {
                    $superior = $superiorInfo['id'];
                }
            }

            $agentPhone = trim($agentInfo['username']);
            $downLines = self::leftJoin('zone', 'agent.zone_id', '=', 'zone.id')->leftJoin('agent_level', 'agent.agent_level_id', '=', 'agent_level.id')
                ->select('agent.id', 'agent.gender', 'agent.avatar', 'realname', 'zone.name', 'agent.created_at', 'agent.nickname', 'agent_level.name as levelName')
                ->where('register_invite', $agentPhone)->get();

            $downLineList = array();
            foreach ($downLines as $downLineInfo) {
                $nameStr = empty($downLineInfo['realname']) ? trim($downLineInfo['nickname']) : $downLineInfo['nickname'] . "(" . $downLineInfo['realname'] . ")";
                $downLineList[] = array(
                    "id" => intval($downLineInfo["id"]),
                    'gender' => trim($downLineInfo["gender"]),
                    'avatar' => getImage($downLineInfo["avatar"]),
                    'nickname' => trim($downLineInfo["nickname"]),
                    'realname' => trim($downLineInfo["realname"]),
                    'level' => trim($downLineInfo["levelName"]),
                    'com_name' => $nameStr,
                    'city' => trim($downLineInfo["name"]),
                    'created_at' => $downLineInfo["created_at"]->getTimestamp(),
                );
            }
            $count = count($downLineList);
            $data = array(
                "list" => $downLineList,
                "count" => $count,
                'superior' => trim($superior),
            );
            return $data;
        } else {
            return false;
        }
    }



    public static function getTeamSales($agentId)
    {
        $agentInfo = self::where('id', $agentId)->first();
        if (is_object($agentInfo)) {
            $currQuarter = date('Y年m月');
            $agentAllAchievement = $agentInfo->hasManyAgentAchievement()->get();
            $allAchievementCollect = collect($agentAllAchievement);
            $currQuarterData = $allAchievementCollect->filter(function($item)use($currQuarter){
                return $item['month'] == $currQuarter;
            })->first();
            if(empty($currQuarterData)){
                return false;
            }

            $data = array(
                'nickname' => trim($agentInfo['realname']),
                "quarter_orders" => trim(intval($currQuarterData['total_achievement'])),
                "my_quarter_orders" => trim(intval($currQuarterData['my_achievement'])),
                "subordinate_quarter_orders" => trim(intval($currQuarterData['team_achievement'])),
                "total_orders" => trim($allAchievementCollect->sum('total_achievement')),
                "my_orders" => trim($allAchievementCollect->sum('my_achievement')),
                "subordinate_orders" => trim($allAchievementCollect->sum('team_achievement')),
            );
            return $data;
        } else {
            return false;
        }
    }


    public static function salesDetail($agentId, $type, $page, $pageSize)
    {
        $agentInfo = self::where('id', $agentId)->where('status',1)->first();
        if (!is_object($agentInfo)) {
            return array(
                "message" => "该经纪人无效",
                "error" => 1
            );
        }
        if ($type != 'quarter' && $type != 'all') {
            return array(
                "message" => "请填入正确的请求类型",
                "error" => 1
            );
        }
        if ($type == 'quarter') {
            $currQuarter = date('Y年m月');
            $currQuarterAchievement = $agentInfo->hasManyAgentAchievement()->where("month", $currQuarter)->first();
            if (!is_object($currQuarterAchievement)) {
                return array(
                    "message" => "该季度数据不存在",
                    "error" => 1
                );
            }
            $range = array($currQuarterAchievement["id"]);
        }
        if ($type == 'all') {
            $allAchievement = $agentInfo->hasManyAgentAchievement()->get();
            $allAchievementCount = $allAchievement->count();
            if (empty($allAchievementCount)) {
                return array(
                    "message" => "历史数据获取失败",
                    "error" => 1
                );
            }
            $range = [];
            foreach ($allAchievement as $oneQuarterAchievement) {
                $range[] = $oneQuarterAchievement['id'];
            }
        }
        $start = ($page - 1) * $pageSize;

        $achievements = AgentAchievementLog::with('contract.user', 'contract.brand', 'agent')
            ->whereIn('agent_achievement_id', $range)
            ->skip($start)->take($pageSize)->get()->toArray();
        $data = [];
        foreach ($achievements as $oneAchievement) {
            $encryptPhone = substr($oneAchievement['contract']['user']['username'], 0, 3) . '****' . substr($oneAchievement['contract']['user']['username'], -4);
            $data[] = array(
                'nickname' => trim($oneAchievement['contract']['user']['nickname'] . "(" . $encryptPhone . ")"),
                'brand_name' => trim($oneAchievement['contract']['brand']['name']),
                'pagcakge_fee' => doFormatMoney(floatval($oneAchievement['contract']['amount'])),
                'created_at' => $oneAchievement['contract']['confirm_time'],
                'order_agent_id' => trim($oneAchievement['agent_id']),
                'agent_id' => trim($agentId),
                'agent_invite' => trim($agentInfo['my_invite']),
                'username' => trim($agentInfo['username']),
                'contract_id' => trim($oneAchievement['contract']['id']),
                'agent' => $oneAchievement['agent_id'] == $agentId ? $oneAchievement['agent']['realname'] : $oneAchievement['agent']['realname'] . '(下级经纪人)',
            );
        }
        return $data;
    }

    public static function getInvitationResult($agentId, $type, $page, $pageSize)
    {
        $agentInfo = self::where('id', $agentId)->where('status', 1)->first();
        if (!is_object($agentInfo)) {
            return array(
                "message" => "该经纪人不存在",
                "error" => 1
            );
        }
        $data = [];
        $nowTime = time();
        $start = ($page - 1) * $pageSize;
        //将过期的邀请函改变状态
        Invitation::where(function ($query) use ($nowTime) {
            $query->where('type', 1);
            $query->where('status', 0);
            $query->where('expiration_time', '<', $nowTime);
        })->update(['status' => '-2']);

        //获取数据
        $invitationInfo = Invitation::with('hasOneUsers', 'agent_customer', 'hasOneActivity.brands',
            'hasOneActivity.makers.zone',
            'hasManyActivitySign.belongsToMaker'
        )->where(function ($query) use ($agentId, $type) {
            $query->where('agent_id', $agentId);
            $query->where('type', 1);
            if (is_array($type)) {
                $query->whereIn('status', $type);
            } else if ($type == -1) {
                $query->whereIn('status', [-2, -1]);
            } else {
                $query->where('status', $type);
            }
        })->skip($start)->take($pageSize)->get()->toArray();

        //过滤已经下架的品牌邀约
        $invitationInfo = collect($invitationInfo)->filter(function ($item) {
            foreach ($item['has_one_activity']['brands'] as $oneBrand) {
                if ($oneBrand['agent_status'] == 0 && $item['status'] == 0) {
                    return false;
                }
            }
            return true;
        });


        foreach ($invitationInfo as $oneInvite) {
            $arr = [];
            //筛选经纪人投资人数据
            $uid = intval($oneInvite['uid']);
            $oneInvite['agent_customer'] = collect($oneInvite['agent_customer'])->filter(function ($item) use ($uid) {
                return $item['uid'] == $uid;
            })->first();
            //活动举办城市
            $cityStr = '';
            $cityArr = [];
            if ($oneInvite['status'] == 1) {
                //过滤签到数据
                $activeId = trim($oneInvite['post_id']);
                $cityArr = collect($oneInvite['has_many_activity_sign'])->filter(function ($item) use ($activeId) {
                    return $item['activity_id'] == $activeId;
                })->first();
                $cityArr && $cityStr = trim($cityArr['belongs_to_maker']['subject']);
            } else {
                $cityArr = array_pluck($oneInvite['has_one_activity']['makers'], 'zone.name');
                $cityArr = array_map(function ($item) {
                    return str_replace('市', '', $item);
                }, $cityArr);
                $cityStr = implode(',', $cityArr);
            }

            //获取状态信息
            $str = '';
            if ($oneInvite['status'] == -1) {
                $str = trim($oneInvite['remark']);
            } else if ($oneInvite['status'] == -2) {
                $str = trim($oneInvite['remark']) ?: '已过期';
            } else if ($oneInvite['status'] == 1) {
                $str = "";
            } else {
                $diff = $oneInvite['expiration_time'] - $nowTime;
                $days = intval($diff / 86400);
                $hours = intval($diff % 86400 / 3600);
                $minute = intval($diff % 86400 % 3600 / 60);
                $str = "还剩{$days}天{$hours}小时{$minute}分";
            }
            $statusArr = array(
                "status" => trim($oneInvite['status']),
                "remark" => $str
            );


            //获取经纪人投资人关系
            $source = trim($oneInvite['agent_customer']['source']);
            $relation = 0;
            switch ($source) {
                case 1:
                case 2:
                case 3:
                case 4:
                    $relation = 2;
                    break;
                case 5:
                    $relation = 1;
                    break;
                case 6:
                    $relation = 3;
                    break;
                case 7:
                    $relation = 4;
                    break;
                case 8:
                    $relation = 5;
                    break;
            }


            $arr['id'] = trim($oneInvite['id']);
            $arr['nickname'] = trim($oneInvite['has_one_users']['nickname']);
            $arr['uid'] = trim($oneInvite['has_one_users']['uid']);
            $arr['avatar'] = getImage($oneInvite['has_one_users']['avatar']);
            $arr['relation'] = trim($relation);
            $arr['activity_id'] = trim($oneInvite['has_one_activity']['id']);
            $arr['activity_title'] = trim($oneInvite['has_one_activity']['subject']);
            $arr['activity_list_img'] = getImage($oneInvite['has_one_activity']['list_img'], 'activity');
            $arr['begin_time'] = trim($oneInvite['has_one_activity']['begin_time']);
            $arr['cities'] = trim($cityStr);
            $arr['status_info'] = $statusArr;
            $arr['affirm_time'] = trim($oneInvite['updated_at']);
            $arr['updated_at'] = trim($oneInvite['created_at']);
            $arr['confirm_time'] = trim($oneInvite['updated_at']);
            $arr['confirm_day'] = trim(date("Y/m/d", $oneInvite['updated_at']));
            $arr['type'] = 2;   //区分类型
            $data[] = $arr;
        }
        return $data;
    }


    /**
     * 获取所有下线的id集   --数据中心版
     * @User yaokai
     * @param $agent_id 经纪人id
     * @return array
     */
    public static function getInviteIds($agent_id)
    {
        //所有用户
        $data = self::select('id', 'non_reversible', 'register_invite')->where('status', '1')->get()->toArray();
        //我的号码
        $non_reversible = self::where('id', $agent_id)->value('non_reversible');
        //所有下线id
        $ids = self::getTreeIds($data, $non_reversible);

        return $ids;
    }

    /**
     * 递归获取  --数据中心版
     * [getTree description]
     * @param  [type] $data 所有用户 ['id','non_reversible','register_invite']
     * @param  string $register_invite 我的号码
     * @return [type]
     */
    static function getTreeIds($data, $register_invite = '0', $is_first = true)
    {
        static $arr = [];
        if ($is_first) {
            $arr = [];
        }
        foreach ($data as $key => $val) {
            if ($val['register_invite'] == $register_invite) {
                $arr[] = $val['id'];
                self::getTreeIds($data, $val['non_reversible'], false);
            }
        }
        return $arr;
    }

    //获取经纪人客户的统计信息
    public static function getCustomerStatistics($agentId)
    {
        $agentInfo = self::where('id', $agentId)->where('status', 1)->first();
        if (!is_object($agentInfo)) {
            return array(
                "message" => '请输入有效的经纪人id',
                'error' => 1
            );
        }
        $customers = AgentCustomer::where('agent_id', $agentInfo['id'])
            ->whereIn('source', [5, 6,7])->select("uid")
            ->get();
        $customer = [];
        foreach ($customers as $oneCustomer) {
            $customer[] = intval($oneCustomer["uid"]);
        }
        $invitInfos = Invitation::where('agent_id', $agentInfo['id'])
            ->whereIn('status', [1,2])
            ->whereIn('uid', $customer)
            ->get();
        $invitShopNum = [];
        $invitActiveNum = [];
        foreach ($invitInfos as $invitInfo) {
            if ($invitInfo['type'] == 2) {
                if(!in_array($invitInfo['uid'],$invitShopNum)){
                    $invitShopNum[] = $invitInfo['uid'];
                }
            }
            if ($invitInfo['type'] == 1) {
                if(!in_array($invitInfo['uid'],$invitActiveNum)){
                    $invitActiveNum[] = $invitInfo['uid'];
                }
            }
        }
        $contractNum = Contract::where('agent_id', $agentInfo['id'])->where('status', 2)
            ->whereIn('uid', $customer)->get()->toArray();
        $contractNum = collect($contractNum)->groupBy('uid')->count();
        $agentCustomers = AgentCustomer::where('agent_id', $agentInfo['id'])->whereIn('uid', $customer)->select('level')->get();
        $agentCustomerCollect = collect($agentCustomers);
        $keyCustomer = $agentCustomerCollect->filter(function ($item) {
            return $item['level'] == 3;
        })->count();
        $mainCustomer = $agentCustomerCollect->filter(function ($item) {
            return $item['level'] == 2;
        })->count();
        $generalCustomer = $agentCustomerCollect->filter(function ($item) {
            return $item['level'] == 1;
        })->count();
        $lossCustomer = $agentCustomerCollect->filter(function ($item) {
            return $item['level'] == -1;
        })->count();
        $data = array(
            'activity_applies' => trim(count($invitActiveNum)),
            'store_inspects' => trim(count($invitShopNum)),
            'contract_signs' => trim($contractNum),
            'normal_customers' => trim($generalCustomer),
            'primary_customers' => trim($mainCustomer),
            'key_customers' => trim($keyCustomer),
            'novalue_customers' => trim($lossCustomer),
            'totals' => trim($customers->count()),
        );
        return $data;
    }

    public static function getInviteOverviewInfo($agentId)
    {
        $agentInfo = self::where('id', $agentId)->where('status', 1)->first();
        if (!is_object($agentInfo)) {
            return array(
                "message" => '请输入有效的经纪人id',
                'error' => 1
            );
        }
        $customerInfos = AgentCustomer::whereIn("source", [1, 2, 3, 4, 6, 7])
            ->where('status', "<>", -1)
            ->where('agent_id', $agentId)
            ->get();
        $customerCollect = collect($customerInfos);
        $customersCount = $customerCollect->count();
        $self_imports = $customerCollect->filter(function ($item) {
            return $item['source'] == 1;
        })->count();
        $activity_invites = $customerCollect->filter(function ($item) {
            return $item['source'] == 2;
        })->count();
        $live_invites = $customerCollect->filter(function ($item) {
            return $item['source'] == 3;
        })->count();
        $others = $customerCollect->filter(function ($item) {
            return $item['source'] == 4;
        })->count();
        $nowTime = time();
        $protect_customers = $customerCollect->filter(function ($item) use ($nowTime) {
            if ($item['protect_time'] > $nowTime) {
                return true;
            }
            return false;
        })->count();
        $data = array(
            'totals' => trim($customersCount),
            'self_imports' => trim($self_imports),
            'activity_invites' => trim($activity_invites),
            'live_invites' => trim($live_invites),
            'others' => trim($others),
            'protect_customers' => trim($protect_customers),
        );
        return $data;
    }

    /**
     * 需要活动提醒的客户
     */
    public static function getUserSainInfo($agent_id)
    {

        $agentInfo = self::where('id', $agent_id)->where('status', 1)->first();
        $nowTime = time();
        $invitInfos = Invitation::where('invitation.agent_id', $agent_id)
            ->leftJoin('user', 'user.uid', '=', 'invitation.uid')
            ->leftJoin('zone', 'zone.id', '=', 'user.zone_id')
            ->leftJoin('activity', 'invitation.post_id', '=', 'activity.id')
            ->where('invitation.type', 1)
            ->where('invitation.status', 1)
            ->where('activity.end_time', '>=', $nowTime)
            ->orderBy('activity.begin_time', 'asc')
            ->orderBy('activity.id', 'asc')
            ->select('activity.id', 'activity.subject', 'activity.begin_time',
                'user.avatar', 'zone.name', 'user.gender', 'user.uid', 'user.username','user.non_reversible',
                'user.nickname'
            )->get();

        $data = [];
        $lastId = -1;
        $k = -1;
        foreach ($invitInfos as $invitInfo) {
            $uid = trim($invitInfo['uid']);
            $activityId = trim($invitInfo['id']);
            $activeSignInfo = DB::table('activity_sign')
                ->where('uid', $uid)
                ->where('activity_id', $activityId)
                ->where('status', '0')
                ->first();
            if (!is_object($activeSignInfo)) {
                continue;
            }
            if ($invitInfo['id'] == $lastId) {
                $data[$k]['list'][] = array(
                    'uid' => trim($invitInfo['uid']),
                    'nickname' => trim($invitInfo['nickname']),
                    'avatar' => getImage($invitInfo['avatar']),
                    'city' => trim($invitInfo['name']),
                    'gender' => trim($invitInfo['gender']),
                    'phone' => trim($invitInfo['non_reversible']),
                );
            } else {
                $k++;
                $time = date('m月d日 H:i', $invitInfo['begin_time']);
                $data[$k] = array(
                    'activity_id' => trim($invitInfo['id']),
                    'activity_title' => trim($invitInfo['subject']),
                    'activity_begin_time' => $time,
                    'list' => array(
                        array(
                            'uid' => trim($invitInfo['uid']),
                            'nickname' => trim($invitInfo['nickname']),
                            'avatar' => getImage($invitInfo['avatar']),
                            'city' => trim($invitInfo['name']),
                            'gender' => trim($invitInfo['gender']),
                            'phone' => trim($invitInfo['non_reversible']),
                        )
                    )
                );
                $today = strtotime('today');
                $beginStamp = strtotime(date('Y-m-d', $invitInfo['begin_time']));
                if ($today == $beginStamp) {
                    $data[$k]['activity_begin_time'] = date('m月d日', $invitInfo['begin_time']) . ' (今天) ' . date('H:i', $invitInfo['begin_time']);
                }
                $lastId = trim($invitInfo['id']);
            }
        }
        //判断是否公开手机号
        foreach ($data as &$oneData) {
            foreach ($oneData['list'] as &$oneUser) {
                $uid = $oneUser['uid'];
                $agentCustom = AgentCustomer::where(function ($query) use ($uid, $agent_id) {
                    $query->where('uid', $uid);
                    $query->where('agent_id', $agent_id);
                })->first();
                $oneUser['is_pub_phone'] = is_object($agentCustom) ? trim($agentCustom['has_tel']) : '0';
            }
        }
        return $data;
    }

    public static function getActivityInviteInfo($agentId, $customerId, $type)
    {
        $agentInfo = self::where('id', $agentId)->where('status', 1)->first();
        if (!is_object($agentInfo)) {
            return array(
                "message" => '请输入有效的经纪人id',
                'error' => 1
            );
        }
        $userInfo = User::where('uid', $customerId)->where('status', '<>', -1)->first();
        if (!is_object($userInfo)) {
            return array(
                "message" => '请输入有效的用户id',
                'error' => 1
            );
        }
        $nowTime = time();
        Invitation::where(function ($query) use ($agentId, $customerId, $nowTime) {
            $query->where('agent_id', $agentId);
            $query->where('uid', $customerId);
            $query->where('status', 0);
            $query->where('type', 1);
            $query->where('expiration_time', '<', $nowTime);
        })->update(['status' => -2]);

        $inviteInfos = self::with('hasManyInvite.hasOneActivity.activitymaker.maker.zone',
            'hasManyInvite.hasManyActivitySign.belongsToMaker')
            ->with(['hasManyInvite' => function ($query) use ($customerId) {
                $query->where('type', 1)->where('uid', $customerId)->orderBy('created_at', 'desc');
                $query->where('status', '<>', -3);
            }])
            ->where('id', $agentId)
            ->first()->toArray();
        $data = [];
        $count = count($inviteInfos['has_many_invite']);
        $data['totals'] = $count;
        $data['nickname'] = trim($userInfo['nickname']);
        $data['avatar'] = getImage($userInfo['avatar']);
        if ($count != 0) {
            $invitesCollect = collect($inviteInfos['has_many_invite']);
            if ($type == -1) {
                $inviteInfoCollect = $invitesCollect->filter(function ($item) {
                    return $item['status'] == -1 || $item['status'] == -2;
                });
            } else if ($type == 0) {
                $inviteInfoCollect = $invitesCollect->filter(function ($item) {
                    return $item['status'] == 0;
                });
            } else if ($type == 1) {
                $inviteInfoCollect = $invitesCollect->filter(function ($item) {
                    return $item['status'] == 1 || $item['status'] == 2;
                });
            } else {
                $inviteInfoCollect = $invitesCollect;
            }
            $arr = [];
            foreach ($inviteInfoCollect as $inviteInfo) {
                $month = date('m', strtotime($inviteInfo['created_at']));
                $arr[$month]['month'] = $month;
                $citys = '';
                if ($inviteInfo['status'] == 1) {
                    $activeId = $inviteInfo['post_id'];
                    $signInfo = collect($inviteInfo['has_many_activity_sign'])->filter(function ($item) use ($activeId) {
                        return $item['activity_id'] == $activeId;
                    })->first();
                    if (!empty($signInfo)) {
                        $citys = trim($signInfo['belongs_to_maker']['subject']);
                    }
                } else {
                    $activitymakers = $inviteInfo['has_one_activity']['activitymaker'];
                    $cityArr = [];
                    foreach ($activitymakers as $oneMaker) {
                        $cityArr[] = $oneMaker['maker']['zone']['name'];
                    }

                    $citys = implode(',', $cityArr);
                }


                //邀请状态

                $str = '';
                if ($inviteInfo['status'] == -1) {
                    $str = trim($inviteInfo['remark']);
                } else if ($inviteInfo['status'] == 1) {
                    $str = "";
                } else if ($inviteInfo['status'] == 0) {
                    $diff = $inviteInfo['expiration_time'] - $nowTime;
                    $days = intval($diff / 86400);
                    $hours = intval($diff % 86400 / 3600);
                    $minute = intval($diff % 86400 % 3600 / 60);
                    $str = "还剩{$days}天{$hours}小时{$minute}分";
                }
                $statusArr = array(
                    "status" => $inviteInfo['status'],
                    "remark" => $str
                );

                $arr[$month]['list'][] = array(
                    'activity_id' => trim($inviteInfo['has_one_activity']['id']),
                    "title" => trim($inviteInfo['has_one_activity']['subject']),
                    "begin_time" => trim($inviteInfo['has_one_activity']['begin_time']),
                    "cities" => $citys,
                    "statusInfo" => $statusArr,
                    'confirm_time' => trim($inviteInfo['updated_at']),
                    'invite_id' => trim($inviteInfo['id']),
                    'list_img' => getImage($inviteInfo['has_one_activity']['list_img'], 'activity', ''),
                );
            }
            foreach ($arr as $item) {
                $data['activity_list'][] = $item;
            }
        }
        return $data;
    }

    public static function getRecordsActivity($agentId, $customerId, $brandId)
    {
        $agentInfo = self::where('id', $agentId)->where('status', 1)->first();
        if (!is_object($agentInfo)) {
            return array(
                "message" => '请输入有效的经纪人id',
                'error' => 1
            );
        }
        $userInfo = DB::table('user')->where('uid', $customerId)->where('status', '<>', -1)->first();
        if (!is_object($userInfo)) {
            return array(
                "message" => '请输入有效的用户id',
                'error' => 1
            );
        }

        $nowTime = time();
        //将过期的邀请函status置为-2
        Invitation::where(function ($query) use ($agentId, $nowTime) {
            $query->where('agent_id', $agentId);
            $query->where('expiration_time', '<', $nowTime);
            $query->where('status', 0);
            $query->where('type', 1);
        })->update(['status' => -2]);

        $inviteInfoArr = Invitation::with('hasOneActivity.brand', 'hasManyActivityMaker.maker.zone',
            'hasManyActivitySign.belongsToMaker')
            ->where(function ($query) use ($customerId, $agentId) {
                $query->where('type', 1);
                $query->where('uid', $customerId);
                $query->where('agent_id', $agentId);
                $query->orderBy('created_at', 'desc');
            })->get()->toArray();

        $inviteInfos = collect($inviteInfoArr)->filter(function ($item) use ($brandId) {
            $count = 0;
            $count = collect($item['has_one_activity']['brand'])->filter(function ($one) use ($brandId) {
                return $one['brand_id'] == $brandId;
            })->count();
            if ($count) {
                return true;
            }
            return false;
        })->toArray();
        $data = [];
        foreach ($inviteInfos as $inviteInfo) {
            $arr = [];
            $arr['created_at'] = trim($inviteInfo['created_at']);
            $arr['uid'] = trim($inviteInfo['uid']);
            $arr['activity_img'] = getImage($inviteInfo['has_one_activity']['list_img'], 'activity');
            $arr['activity_title'] = trim($inviteInfo['has_one_activity']['subject']);
            $arr['activity_id'] = trim($inviteInfo['has_one_activity']['id']);
            $arr['begin_time'] = trim($inviteInfo['has_one_activity']['begin_time']);
            $arr['confirm_time'] = trim($inviteInfo['updated_at']);
            $arr['invite_id'] = trim($inviteInfo['id']);
            $status = trim($inviteInfo['status']);

            $arr['status_info']['status'] = $status;
            if ($status == -1) {
                $arr['status_info']['remark'] = trim($inviteInfo['remark']);
            } else if ($status == 0) {
                $diff = $inviteInfo['expiration_time'] - $nowTime;
                $days = intval($diff / 86400);
                $hours = intval($diff % 86400 / 3600);
                $minute = intval($diff % 86400 % 3600 / 60);
                $str = "还剩{$days}天{$hours}小时{$minute}分";
                $arr['status_info']['remark'] = trim($str);
            } else if ($status == 1) {
                $activitySigns = collect($inviteInfo['has_many_activity_sign']);
                $activityId = $inviteInfo['post_id'];
                $activitySign = $activitySigns->filter(function ($item) use ($activityId, $customerId) {
                    return $item['uid'] == $customerId && $item['activity_id'] == $activityId;
                })->first();
                $arr['address'] = trim($activitySign['belongs_to_maker']['subject']);
            }
            if (!isset($arr['address'])) {
                $zones = [];
                foreach ($inviteInfo['has_many_activity_maker'] as $ovo) {
                    $zones[] = $ovo['maker']['zone']['name'];
                }
                $arr['address'] = implode(',', $zones);
            }
            $arr['customer_nickname'] = trim($userInfo->nickname);
            $data['activeList'][] = $arr;
        }
        $total = count($data['activeList']);
        $data['total'] = $total;
        return $data;
    }

    /*
     * 客户列表
     * shiqy
     *
     * */
    public static function getCustomerList($agentId, $orderBy, $type, $filter)
    {
        $agentInfo = self::where('id', $agentId)->where('status', '<>', '-1')->first();
        if (!is_object($agentInfo)) {
            return array(
                "message" => "请输入有效的经纪人id",
                'error' => 1
            );
        }

//        从数据库中取数据
        $agentInfos = self::
        with('hasManyCustomer.hasManyContract',
            'hasManyCustomer.user.zone',
            'hasManyCustomer.hasManyInvitation.hasManyActivitySign',
            'hasManyCustomer.hasManyInvitation.hasOneActivity',
            'hasManyCustomer.hasManyAgentCustomerLogs'
        )->
        with(['hasManyCustomer' => function ($query) {
            $query->where('status', '<>', -1);
            $query->whereNotIn('source', [8, 9]);
        }])->
        where('id', $agentId)->first()->toArray();
//            where('id',$agentId)->first();
        $agentCustomerInfos = $agentInfos['has_many_customer'];
        //对原始数据进行无效数据去除
        $newAgentCustomerInfos = [];
        foreach ($agentCustomerInfos as $agentCustomerInfo) {
            //处理合同
            $contractArr = [];
            foreach ($agentCustomerInfo['has_many_contract'] as $contract) {
                if ($contract['uid'] == $agentCustomerInfo['uid']) {
                    $contractArr[] = $contract;
                }
            }
            $agentCustomerInfo['has_many_contract'] = $contractArr;

            //处理邀请
            $invitationArr = [];
            foreach ($agentCustomerInfo['has_many_invitation'] as $invitation) {
                if ($invitation['uid'] == $agentCustomerInfo['uid']) {

                    //处理签到，如果不是门店邀请，也就不需要has_many_activity_sign属性，
                    //如果是活动邀请，就把属于邀请函邀请的活动的签到取出
                    $activitySigns = $invitation['has_many_activity_sign'];
                    $invitation['has_many_activity_sign'] = [];
                    foreach ($activitySigns as $oneSign) {
                        if ($invitation['type'] == 1 && $invitation['status'] == 1 && $invitation['post_id'] == $oneSign['activity_id']) {
                            $invitation['has_many_activity_sign'] = $oneSign;
                            break;
                        }
                    }
                    $invitationArr[] = $invitation;
                }
            }
            $agentCustomerInfo['has_many_invitation'] = $invitationArr;
            $newAgentCustomerInfos[] = $agentCustomerInfo;
        }
//        对数据进行加工
        $agentCustomerCollect = collect($newAgentCustomerInfos);
        $data = [];
        $sendCustomerNum = $agentCustomerCollect->filter(function ($item) {
            return $item['source'] == 5 || $item['source'] == 6 || $item['source'] == 7;
        })->count();
        $inviteCustomerNum = $agentCustomerCollect->filter(function ($item) {
            return $item['source'] != 5 && $item['source'] != 8;
        })->count();
        $nowTime = time();
        $activityRemindNum = $agentCustomerCollect->filter(function ($item) use ($nowTime) {
            foreach ($item['has_many_invitation'] as $oneInvitation) {
                if ($oneInvitation['type'] == 1
                    && $oneInvitation['status'] == 1
                    && $oneInvitation['has_one_activity']['end_time'] > $nowTime
                ) {
                    return true;
                }
            }
            return false;
        })->count();

        $inspectRemindNum = $agentCustomerCollect->filter(function ($item) use ($nowTime) {
            foreach ($item['has_many_invitation'] as $oneInvitation) {
                if ($oneInvitation['type'] == 2
                    && in_array($oneInvitation['status'], [1, 2])
                    && $oneInvitation['inspect_time'] > $nowTime
                ) {
                    return true;
                }
            }
            return false;
        })->count();
        $protectCustomerNum = $agentCustomerCollect->filter(function ($item) use ($nowTime) {
            return $item['protect_time'] > $nowTime;
        })->count();

        //排序
        if ($orderBy == 'intention') {
            $newSort = $agentCustomerCollect->sortByDesc(function ($item, $key) {
                return $item['user']['invest_intention'];
            });
        } else if ($orderBy == 'active') {
            $newSort = $agentCustomerCollect->sortByDesc(function ($item, $key) {
                return $item['user']['login_count'];
            });
        } else if ($orderBy == 'followed_time') {
            $newSort = $agentCustomerCollect->sortByDesc(function ($item, $key) {
                return $item['created_at'];
            });
        }
        if (isset($newSort)) {
            $agentCustomerCollect = $newSort->values();
        }
        //过滤

        if ($filter == 'ovo') {
            $agentCustomerCollect = $agentCustomerCollect->filter(function ($item) {
                foreach ($item['has_many_invitation'] as $oneInvitation) {
                    if (isset($oneInvitation['has_many_activity_sign']['status']) && $oneInvitation['has_many_activity_sign']['status'] == 1) {
                        return true;
                    }
                }
                return false;
            });
        } else if ($filter == 'inspected') {
            $agentCustomerCollect = $agentCustomerCollect->filter(function ($item) {
                foreach ($item['has_many_invitation'] as $oneInvitation) {
                    if ($oneInvitation['type'] == 2 && in_array($oneInvitation['status'], [1, 2])) {
                        return true;
                    }
                }
                return false;
            });
        } else if ($filter == 'signed_contract') {
            $agentCustomerCollect = $agentCustomerCollect->filter(function ($item) {
                foreach ($item['has_many_contract'] as $oneContract) {
                    if ($oneContract['status'] == 1 || $oneContract['status'] == 2) {
                        return true;
                    }
                }
                return false;
            });
        }

        //范围筛选
        if ($type == 'protected') {
            $agentCustomerCollect = $agentCustomerCollect->filter(function ($item) use ($nowTime) {
                if ($item['protect_time'] > $nowTime) {
                    return true;
                }
                return false;
            });
        }
        $data['send_customers'] = trim($sendCustomerNum);
        $data['invite_customers'] = trim($inviteCustomerNum);
        $data['activity_reminds'] = trim($activityRemindNum);
        $data['inspect_reminds'] = trim($inspectRemindNum);
        $data['protect_customers'] = trim($protectCustomerNum);
        foreach ($agentCustomerCollect as $oneAgentCustomer) {
            $levelStr = "";
            switch ($oneAgentCustomer['level']) {
                case -1:
                    $levelStr = '遗失客户';
                    break;
                case 1:
                    $levelStr = '普通客户';
                    break;
                case 2:
                    $levelStr = '主要客户';
                    break;
                case 3:
                    $levelStr = '关键客户';
                    break;
            }
            $lastRemark = '';
            $customerLogs = collect($oneAgentCustomer['has_many_agent_customer_logs'])
                ->sortByDesc(function ($item) {
                    return $item['created_at'];
                })->first();
            $lastRemark = trim($customerLogs['remark']);
            $oneData = array(
                'avatar' => getImage($oneAgentCustomer['user']['avatar']),
                'nickname' => trim($oneAgentCustomer['user']['nickname']),
                'gender' => trim($oneAgentCustomer['user']['gender']),
                'city' => trim($oneAgentCustomer['user']['zone']['name']),
                'level' => trim($levelStr),
                'remark' => $lastRemark,
                'uid' => trim($oneAgentCustomer['uid']),
            );
            if (in_array($oneAgentCustomer['source'], [5, 6, 7])) {
                $data['send_customer_list'][] = $oneData;
            }
            if ($oneAgentCustomer['source'] != 5 && $oneAgentCustomer['source'] != 8) {
                $data['invite_customer_list'][] = $oneData;
            }
        }
//        对结果按昵称的汉语拼音首字母正序排序
        if (!empty($data['send_customer_list'])) {
            $data['send_customer_list'] = collect($data['send_customer_list'])->sortBy(function ($item) {
                return getfirstchar($item['nickname']);
            })->toArray();
        }
        if (!empty($data['invite_customer_list'])) {
            $data['invite_customer_list'] = collect($data['invite_customer_list'])->sortBy(function ($item) {
                return getfirstchar($item['nickname']);
            })->toArray();
        }
        return $data;
    }

    /**
     * 客户——关键词搜索   --数据中心版
     * @User shiqy
     * @param $agentId
     * @param $type
     * @param $content
     * @return array
     */
    public static function getSearchInfo($agentId, $type, $content)
    {
        $agentInfo = self::where('id', $agentId)->where('status', '<>', '-1')->first();
        if (!is_object($agentInfo)) {
            return array(
                "message" => "请输入有效的经纪人id",
                'error' => 1
            );
        }
        $agentCustomerInfo = self::with('hasManyCustomer.user.zone',
            'hasManyCustomer.hasManyAgentCustomerLogs.hasOneBrand')
            ->where('id', $agentId)->first()->toArray();
        $data = [];
        $agentCustomerCollect = collect($agentCustomerInfo['has_many_customer']);
        if ($type == 'customer') {
            $newAgentCustomers = $agentCustomerCollect->filter(function ($item) use ($content) {
                $nickname = trim($item['user']['nickname']);
                $username = trim($item['user']['non_reversible']);
                if ($username == encryptTel($content)|| strpos($nickname, $content) !== false) {
                    return true;
                }
                return false;
            });
            $data['results'] = trim($newAgentCustomers->count());
            foreach ($newAgentCustomers as $agentCustomer) {
                $data['customer_list'][] = array(
                    'uid' => trim($agentCustomer['user']['uid']),
                    'avatar' => getImage($agentCustomer['user']['avatar']),
                    'nickname' => trim($agentCustomer['user']['nickname']),
                    'city' => trim($agentCustomer['user']['zone']['name']),
                    'gender' => trim($agentCustomer['user']['gender']),
                );
            }
        }
        if ($type == 'brand') {
            $newAgentCustomerInfo = [];
            foreach ($agentCustomerCollect as $agentCustomer) {
                $brandIdArr = [];
                foreach ($agentCustomer['has_many_agent_customer_logs'] as $oneLog) {
                    $brandName = trim($oneLog['has_one_brand']['name']);
                    $brandId = trim($oneLog['has_one_brand']['id']);
                    if (!in_array($brandId, $brandIdArr)) {
                        if (strpos($brandName, $content) !== false) {
                            $newAgentCustom = $agentCustomer;
                            $newAgentCustom['brandId'] = $oneLog['brand_id'];
                            $newAgentCustom['brandName'] = $brandName;
                            $newAgentCustomerInfo[] = $newAgentCustom;
                        }
                    }
                    $brandIdArr[] = $brandId;
                }
            }
            $data['results'] = count($newAgentCustomerInfo);
            $data['customer_list'] = [];
            $sortAgentCustomer = collect($newAgentCustomerInfo)->groupBy('brandId');
            foreach ($sortAgentCustomer as $key => $oneBrandAgentCustomer) {
                $arr = [];
                $arr['brand_name'] = trim($sortAgentCustomer[$key][0]['brandName']);
                foreach ($oneBrandAgentCustomer as $agentCustomer) {
                    $arr['list'][] = array(
                        'uid' => trim($agentCustomer['user']['uid']),
                        'avatar' => getImage($agentCustomer['user']['avatar']),
                        'nickname' => trim($agentCustomer['user']['nickname']),
                        'city' => trim($agentCustomer['user']['zone']['name']),
                        'gender' => trim($agentCustomer['user']['gender']),
                    );
                }
                $data['customer_list'][] = $arr;
            }
        }
        return $data;
    }

    public static function getRecordsAll($agentId, $customerId, $brandId)
    {
        $agentInfo = self::where('id', $agentId)->where('status', '<>', '-1')->first();
        if (!is_object($agentInfo)) {
            return array(
                "message" => "请输入有效的经纪人id",
                'error' => 1
            );
        }
        $customerInfo = User::where('uid', $customerId)->where('status', '<>', -1)->first();
        if (!is_object($customerInfo)) {
            return array(
                "message" => "请输入有效的客户id",
                'error' => 1
            );
        }
        $brandInfo = Brand::where('id', $brandId)->where('status', 'enable')->first();
        if (!is_object($brandInfo)) {
            return array(
                "message" => "请输入有效的品牌id",
                'error' => 1
            );
        }

        $data = [];


        $data['brand'] = array(
            'title' => trim($brandInfo['name']),
            'slogan' => trim($brandInfo['slogan']),
        );

        //跟进
        $followInfos = AgentCustomer::with('hasManyAgentCustomerLogs.hasOneBrand')
            ->with(['hasManyAgentCustomerLogs' => function ($query) use ($brandId) {
                $query->where('brand_id', $brandId);
            }])
            ->where('agent_id', $agentId)
            ->where('uid', $customerId)
            ->first();
        //跟进经纪人和投资人是否公开手机号，决定时候返回手机号
        $data['customer'] = array(
            'nickname' => trim($customerInfo['nickname']),
            'avatar' => getImage($customerInfo['avatar']),
            'gender' => trim($customerInfo['gender']),
        );
        if ($followInfos['has_tel']) {
            $data['customer']['username'] = trim(getRealTel($customerInfo['non_reversible'] , 'wjsq'));
        }

        $followInfoCollect = collect($followInfos);
        $logs = $followInfoCollect['has_many_agent_customer_logs'];
        $logsCollect = collect($logs);

        $haveSign = $logsCollect->filter(function ($item) {
            return $item['action'] == 13;
        })->count();
        $data['follow']['status'] = '0';
        if (!empty($haveSign)) {
            $data['follow']['status'] = '1';
        }

        $receiveTime = trim(strtotime($followInfos['created_at']));
        $data['follow']['start_time'] = $receiveTime;
        $nowTime = time();
        $diffDay = trim(ceil(($nowTime - $receiveTime) / 86400));
        $data['follow']['days'] = $diffDay;

        //获取任务单中数据
        $successTaskArr = [];
        $getPhoneTask = $logsCollect->filter(function ($item) {
            return $item['action'] == 2;
        })->first();
        if (!empty($getPhoneTask)) {
            $successTaskArr[] = array(
                'time' => trim($getPhoneTask['created_at']),
                'type' => 1,
            );
        }

        $haveInvi = $logsCollect->filter(function ($item) {
            return $item['action'] == 5;
        })->first();
        if (!empty($haveInvi)) {
            $successTaskArr[] = array(
                'time' => trim($haveInvi['created_at']),
                'type' => 2,
            );
        }

        $haveInspect = $logsCollect->filter(function ($item) {
            return $item['action'] == 8;
        })->first();
        if (!empty($haveInspect)) {
            $successTaskArr[] = array(
                'time' => trim($haveInspect['created_at']),
                'type' => 3,
            );
        }

        $haveContract = $logsCollect->filter(function ($item) {
            return $item['action'] == 11;
        })->first();
        if (!empty($haveContract)) {
            $successTaskArr[] = array(
                'time' => trim($haveContract['created_at']),
                'type' => 4,
            );
        }
        $data['follow']['task'] = $successTaskArr;


        //备注模块
        $remarkList = $logsCollect->filter(function ($item) use ($brandId) {
            return $item['action'] == 12;
        })->sortByDesc('created_at');
        $data['remaks'] = $remarkList->count();
        $customerLevel = $followInfoCollect['level'];
        $data['remark_list'] = [];
        foreach ($remarkList as $oneRemark) {
            $data['remark_list'][] = array(
                'content' => trim($oneRemark['remark']),
                'level' => trim($customerLevel),
                'brand' => trim($oneRemark['has_one_brand']['name']),
                'time' => trim($oneRemark['created_at']),
            );
        }
        $data['agent_realname'] = trim($agentInfo['realname']);
        $data['agent_avatar'] = getImage($agentInfo['avatar']);
        $data['relation_time'] = trim($followInfoCollect['created_at']);
        return $data;
    }

    public static function getAgentsInfo($uid)
    {
        $userInfo = User::with('agent_customer_log.agent',
            'agent_customer_log.agent_customer',
            'agent_customer_log.hasOneBrandAll', 'contract')
            ->with(['agent_customer_log.agent_customer' => function ($query) use ($uid) {
                $query->where('uid', $uid);
            }])
            ->with(['agent_customer_log' => function ($query) {
                $query->where('brand_id', '<>', 0);
            }])
            ->where(function ($query) use ($uid) {
                $query->where('uid', $uid);
                $query->where('status', '<>', -1);
            })->first();
        if (!is_object($userInfo)) {
            return array(
                'message' => '请输入有效的客户id',
                'error' => 1
            );
        }
        $data = [];
        $userInfo = $userInfo->toArray();
        //跟进品牌数
        $brandBox = [];
        $brands = collect($userInfo['agent_customer_log'])->groupBy(function ($item) {
            return $item['brand_id'];
        })->count();
        $data['follow_brands'] = trim($brands);
        //成单品牌数
        $successContract = collect($userInfo['contract'])->filter(function ($item) {
            return $item['status'] == 2;
        })->count();
        $data['success_brands'] = trim($successContract);

        //邀请人数
        $inviters = 0;
        $inviter = trim($userInfo['register_invite']);
        if (!empty($inviter)) {
            $inviters = 1;
        }
        $data['inviters'] = trim($inviters);

        //跟单经纪人
        $agentBox = [];
        $newLogArr = [];
        foreach ($userInfo['agent_customer_log'] as $oneLog) {
            if (in_array(intval($oneLog['agent_id']), $agentBox)) {
                continue;
            }
            $agentBox[] = intval($oneLog['agent_id']);
            $newLogArr[] = $oneLog;
        }
        $data['service_agents'] = trim(count($newLogArr));
        $agentGroupCollect = collect($newLogArr)->groupBy(function ($item) {
            return getfirstchar($item['agent']['nickname']);
        });
        $data['agent_list'] = [];
        foreach ($agentGroupCollect as $key => $oneGroup) {
            $arr = [];
            $arr['letter'] = $key;
            foreach ($oneGroup as $oneAgents) {
                $city = Zone::getCityAndProvince($oneAgents['agent']['zone_id']);
                $agentId = trim($oneAgents['agent']['id']);
                $arr['list'][] = array(
                    'id' => $agentId,
                    'avatar' => getImage($oneAgents['agent']['avatar']),
                    'nickname' => trim($oneAgents['agent']['nickname']),
                    'realname' => trim($oneAgents['agent']['realname']),
                    'username' => trim($oneAgents['agent']['username']),
                    'gender' => trim($oneAgents['agent']['gender']),
                    'is_public_realname' => trim($oneAgents['agent']['is_public_realname']),
                    'city' => $city,
                    'isPub' => trim($oneAgents['agent_customer']['has_tel']),
                    'brandName' => trim($oneAgents['has_one_brand_all']['name']),
                );
            }
            $data['agent_list'][] = $arr;
        }
        return $data;
    }

    public static function getSuccessBrandList($uid)
    {
        $userInfo = User::where('uid', $uid)->where('status', '<>', -1)->first();
        if (!is_object($userInfo)) {
            return array(
                'message' => '请输入有效的客户id',
                'error' => 1
            );
        }
        $data = [];
        $contractList = Contract::with('brand.categorys1', 'agent.hasOneZone', 'agent_score')
            ->where('uid', $uid)
            ->where('status', 2)
            ->get()->toArray();

//        Contract::leftJoin('brand','contract.brand_id','=','brand.id')
//            ->leftJoin('categorys1','categorys1.id','=','brand.categorys1_id')
//            ->leftJoin('agent','agent.id','=','contract.agent_id')
//            ->leftJoin('zone','agent.zone_id','=','zone.id')
//            ->leftJoin('agent_score','agent_score.agent_id','=','agent.id')
//            ->where('uid',$uid)
//            ->whereIn('status',[1,2])
//            ->get()->toArray();


        foreach ($contractList as $oneContract) {
            $arr = [];
            $arr['hasEvaluate'] = 0;
            if (!empty($oneContract['agent_score'])) {
                $arr['hasEvaluate'] = 1;
            }
            $arr['brand'] = array(
                'id' => trim($oneContract['brand']['id']),
                'name' => trim($oneContract['brand']['name']),
                'logo' => getImage($oneContract['brand']['logo']),
                'investment_min' => trim(floatval($oneContract['brand']['investment_min'])),
                'investment_max' => trim(floatval($oneContract['brand']['investment_max'])),
                'category_name' => trim($oneContract['brand']['categorys1']['name']),
            );
            $arr['agent'] = array(
                'id' => trim($oneContract['agent']['id']),
                'city' => trim($oneContract['agent']['has_one_zone']['name']),
                'gender' => trim($oneContract['agent']['gender']),
                'nickname' => trim($oneContract['agent']['nickname']),
                'realname' => trim($oneContract['agent']['realname']),
                'is_public_realname' => trim($oneContract['agent']['is_public_realname']),
                'username' => trim($oneContract['agent']['username']),
                'avatar' => getImage($oneContract['agent']['avatar']),
            );
            $arr['created_at'] = trim($oneContract['created_at']);
            $arr['contract_id'] = trim($oneContract['id']);
            $data[] = $arr;
        }
        return $data;
    }

    public static function getFollowedBrandList($uid)
    {
        $userInfo = User::where('uid', $uid)->where('status', '<>', -1)->first();
        if (!is_object($userInfo)) {
            return array(
                'message' => '请输入有效的客户id',
                'error' => 1
            );
        }
        $data = [];

        $agentCustomerArr = AgentCustomer::with('hasManyAgentCustomerLogs.hasOneBrandAll.categorys1',
            'hasManyAgentCustomerLogs.agent.hasOneZone')
            ->with(['hasManyAgentCustomerLogs' => function ($query) {
                $query->where('brand_id', '<>', 0);
            }])
            ->where('uid', $uid)->get()->toArray();

        if (count($agentCustomerArr)) {
            $customerBrandCollect = collect();
            foreach ($agentCustomerArr as $oneAgentCustomer) {
                if (!empty($oneAgentCustomer['has_many_agent_customer_logs'])) {
                    $customerBrandCollect = $customerBrandCollect->merge($oneAgentCustomer['has_many_agent_customer_logs']);
                }
            }
            $groupCustomerCollect = $customerBrandCollect->groupBy('brand_id');
            $data['brands'] = $groupCustomerCollect->count();
            $data['brand'] = [];
            foreach ($groupCustomerCollect as $oneBrandInfo) {
                $arr = [];
                $theEarlyCollect = collect($oneBrandInfo)->sortBy('created_at')->first();
                $startTime = $theEarlyCollect['created_at'];

                //获取该品牌下经纪人列表
                $agentList = [];
                //经纪人id容器，去重
                $agentBox = [];
                foreach ($oneBrandInfo as $oneLog) {
                    $agentId = intval($oneLog['agent_id']);
                    if (in_array($agentId, $agentBox)) {
                        continue;
                    } else {
                        $agentBox[] = $agentId;
                    }
                    $agentList[] = array(
                        'id' => trim($oneLog['agent']['id']),
                        'avatar' => getImage($oneLog['agent']['avatar']),
                        'nickname' => trim($oneLog['agent']['nickname']),
                        'realname' => trim($oneLog['agent']['realname']),
                        'is_public_realname' => trim($oneLog['agent']['is_public_realname']),
                        'gender' => trim($oneLog['agent']['gender']),
                        'city' => trim($oneLog['agent']['has_one_zone']['name']),
                    );
                }


                $arr = array(
                    'brand_id' => trim($oneBrandInfo[0]['has_one_brand_all']['id']),
                    'logo' => trim($oneBrandInfo[0]['has_one_brand_all']['logo']),
                    'title' => trim($oneBrandInfo[0]['has_one_brand_all']['name']),
                    'category_name' => trim($oneBrandInfo[0]['has_one_brand_all']['categorys1']['name']),
                    'investment_min' => trim(floatval($oneBrandInfo[0]['has_one_brand_all']['investment_min'])),
                    'investment_max' => trim(floatval($oneBrandInfo[0]['has_one_brand_all']['investment_max'])),
                    'followed_agents' => trim(count($agentList)),
                    'created_at' => trim($startTime),
                    'agent_list' => $agentList
                );
                $data['brand'][] = $arr;
            }
        }
        return $data;
    }


    /**
     * 获取当前季度信息
     */
    public function getQuarter($timestamp)
    {
        //定义每季度的开始和结束日期
        $first_qurater = mktime(0, 0, 0, 1, 1, date('Y'));
        $second_qurater = mktime(0, 0, 0, 4, 1, date('Y'));
        $third_qurater = mktime(0, 0, 0, 7, 1, date('Y'));
        $fourth_qurater = mktime(0, 0, 0, 10, 1, date('Y'));
        $fiveth_qurater = mktime(0, 0, 0, 1, 1, date('Y') + 1);

        if ($timestamp >= $first_qurater && $timestamp < $second_qurater) {
            $date = date('Y') . '年1月-3月';
            $first = $first_qurater;
            $second = $second_qurater;
        } elseif ($timestamp >= $second_qurater && $timestamp < $third_qurater) {
            $date = date('Y') . '年4月-6月';
            $first = $second_qurater;
            $second = $third_qurater;
        } elseif ($timestamp >= $third_qurater && $timestamp < $fourth_qurater) {
            $date = date('Y') . '年7月-9月';
            $first = $third_qurater;
            $second = $fourth_qurater;
        } elseif ($timestamp >= $fourth_qurater && $timestamp < $fiveth_qurater) {
            $date = date('Y') . '年10月-12月';
            $first = $fourth_qurater;
            $second = $fiveth_qurater;
        } else {
            throw new \RuntimeException('日期不在今年的范围内');
        }

        return [$date, $first, $second];
    }


    /**
     * 获取当前季度信息 格式不一样,有可能包含去年的最后一季度，但是不包含今年的第四季度
     */
    public function getQuarterWithBrackets($timestamp)
    {
        //定义每季度的开始和结束日期
        $zero_qurater = mktime(0, 0, 0, 1, 1, date('Y') - 1);
        $first_qurater = mktime(0, 0, 0, 1, 1, date('Y'));
        $second_qurater = mktime(0, 0, 0, 4, 1, date('Y'));
        $third_qurater = mktime(0, 0, 0, 7, 1, date('Y'));
        $fourth_qurater = mktime(0, 0, 0, 10, 1, date('Y'));


        if ($timestamp >= $zero_qurater && $timestamp < $first_qurater) {
            $date = date('Y') - 1 . '年第四季 (10月-12月)';
        } elseif ($timestamp >= $first_qurater && $timestamp < $second_qurater) {
            $date = date('Y') . '年第一季 (1月-3月)';
        } elseif ($timestamp >= $second_qurater && $timestamp < $third_qurater) {
            $date = date('Y') . '年第二季 (4月-6月)';
        } elseif ($timestamp >= $third_qurater && $timestamp < $fourth_qurater) {
            $date = date('Y') . '年第三季 (7月-9月)';
        } else {
            throw new \RuntimeException('日期不在今年的范围内');
        }

        return $date;
    }


    /**
     * 获取当前季度信息 格式不一样,有可能包含去年的最后一季度，但是不包含今年的第四季度
     */
    public function getQuarterWithLetter($timestamp)
    {
        //定义每季度的开始和结束日期
        $zero_qurater = mktime(0, 0, 0, 1, 1, date('Y') - 1);
        $first_qurater = mktime(0, 0, 0, 1, 1, date('Y'));
        $second_qurater = mktime(0, 0, 0, 4, 1, date('Y'));
        $third_qurater = mktime(0, 0, 0, 7, 1, date('Y'));
        $fourth_qurater = mktime(0, 0, 0, 10, 1, date('Y'));
        $fiveth_qurater = mktime(0, 0, 0, 1, 1, date('Y') + 1);


        if ($timestamp >= $zero_qurater && $timestamp < $first_qurater) {
            $date = '去年Q4 (10月-12月)';
        } elseif ($timestamp >= $first_qurater && $timestamp < $second_qurater) {
            $date = 'Q1 (1月-3月)';
        } elseif ($timestamp >= $second_qurater && $timestamp < $third_qurater) {
            $date = 'Q2 (4月-6月)';
        } elseif ($timestamp >= $third_qurater && $timestamp < $fourth_qurater) {
            $date = 'Q3 (7月-9月)';
        } else {
            throw new \RuntimeException('日期不在今年的范围内');
        }

        return $date;
    }


    public static function getFollowedAgents($uid)
    {
        $userInfo = User::where('uid', $uid)->where('status', '<>', -1)->first();
        if (!is_object($userInfo)) {
            return array(
                'message' => '请输入有效的客户id',
                'error' => 1
            );
        }
        $data = [];
        $agentArr = AgentCustomer::with('belongsToAgent.hasOneZone')
            ->where('uid', $uid)->get()->toArray();
        $newAgentArr = [];
        $data['totals'] = count($agentArr);
        foreach ($agentArr as $oneAgent) {
            $firstChar = trim(getfirstchar($oneAgent['belongs_to_agent']['nickname']));
            $oneAgent['firstChar'] = $firstChar;
            $newAgentArr[] = $oneAgent;
        }
        $agentCollect = collect($newAgentArr);
        $groupAgentCollect = $agentCollect->groupBy('firstChar');
        foreach ($groupAgentCollect as $key => $oneGroupAgent) {
            $arr = [];
            $arr['letter'] = $key;
            foreach ($oneGroupAgent as $oneAgent) {
                $arr['list'][] = array(
                    'avatar' => getImage($oneAgent['belongs_to_agent']['avatar']),
                    'nickname' => trim($oneAgent['belongs_to_agent']['nickname']),
                    'gender' => trim($oneAgent['belongs_to_agent']['gender']),
                    'city' => trim($oneAgent['belongs_to_agent']['has_one_zone']['name']),
                );
            }
            $data['agent_list'][] = $arr;
        }
        return $data;
    }


    /******************* 客户视角 ************/

    /*
     *活动邀请函列表
     * */
    public static function getActivityInvitesList($uid, $type)
    {
        $userInfo = User::where('uid', $uid)->where('status', '<>', -1)->first();
        if (!is_object($userInfo)) {
            return array(
                'message' => '请输入有效的客户id',
                'error' => 1
            );
        }
        $nowTime = time();
        $rel = Invitation::where(function ($query) use ($nowTime) {
            $query->where('type', 1)->where('status', 0)
                ->where('expiration_time', '<', $nowTime);
        })->update(['status' => -2]);
        $data = [];
        $inviteInfoArr = Invitation::with('hasOneActivity.makers.zone', 'belongsToAgent',
            'hasManyActivitySign.belongsToMaker', 'hasOneActivity.brands')
            ->where(function ($query) use ($uid, $type) {
                $query->where('uid', $uid)->where('type', 1);
                if ($type == -1) {
                    $query->whereIn('status', [-2, -1]);
                } else if ($type == 0 || $type == 1) {
                    $query->where('status', $type);
                }
            })
            ->orderBy('created_at', 'desc')->get()->toArray();

        //过滤已经下架的品牌邀约
        $inviteInfoArr = collect($inviteInfoArr)->filter(function ($item) {
            foreach ($item['has_one_activity']['brands'] as $oneBrand) {
                if ($oneBrand['agent_status'] == 0 && $item['status'] == 0) {
                    return false;
                }
            }
            return true;
        })->toArray();

        foreach ($inviteInfoArr as $oneInvite) {
            $arr = [];
            if ($oneInvite['status'] == -2 || $oneInvite['status'] == -1) {
                $cityStr = '';
                $cityArr = [];
                foreach ($oneInvite['has_one_activity']['makers'] as $oneMaker) {
                    $cityArr[] = trim(str_replace('市', '', $oneMaker['zone']['name']));
                }
                $cityStr = implode(',', $cityArr);
                $remark = trim($oneInvite['remark']);
                if ($oneInvite['status'] == -2 && empty($remark)) {
                    $remark = '已过期';
                }
                $arr = array(
                    'title' => trim($oneInvite['has_one_activity']['subject']),
                    'activity_id' => trim($oneInvite['has_one_activity']['id']),
                    'img' => getImage($oneInvite['has_one_activity']['list_img'], 'activity'),
                    'begin_time' => trim($oneInvite['has_one_activity']['begin_time']),
                    'agent_id' => trim($oneInvite['belongs_to_agent']['id']),
                    'host_cities' => trim($cityStr),
                    'status_info' => array(
                        'status' => trim($oneInvite['status']),
                        'remark' => $remark,
                    ),
                    'confirm_time' => trim($oneInvite['updated_at']),
                    'create_time' => trim($oneInvite['created_at']),
                    'invite_id' => trim($oneInvite['id']),
                );
            } else if ($oneInvite['status'] == 0) {
                $cityStr = '';
                $cityArr = [];
                foreach ($oneInvite['has_one_activity']['makers'] as $oneMaker) {
                    $cityArr[] = trim(str_replace('市', '', $oneMaker['zone']['name']));
                }
                $cityStr = implode(',', $cityArr);
                $activeStartTime = trim($oneInvite['expiration_time']);
                $remark = '';
                $diff = $activeStartTime - $nowTime;
                $days = intval($diff / 86400);
                $hours = intval($diff % 86400 / 3600);
                $minute = intval($diff % 86400 % 3600 / 60);
                $str = "还剩{$days}天{$hours}小时{$minute}分";
                $remark = trim($str);
                $arr = array(
                    'title' => trim($oneInvite['has_one_activity']['subject']),
                    'activity_id' => trim($oneInvite['has_one_activity']['id']),
                    'img' => getImage($oneInvite['has_one_activity']['list_img'], 'activity', ''),
                    'begin_time' => trim($oneInvite['has_one_activity']['begin_time']),
                    'agent_id' => trim($oneInvite['belongs_to_agent']['id']),
                    'host_cities' => trim($cityStr),
                    'status_info' => array(
                        'status' => trim($oneInvite['status']),
                        'remark' => $remark,
                    ),
                    'confirm_time' => "",
                    'create_time' => trim($oneInvite['created_at']),
                    'invite_id' => trim($oneInvite['id']),
                );
            } else {
                $signCollect = collect($oneInvite['has_many_activity_sign']);
                $activeId = intval($oneInvite['post_id']);
                $marker = $signCollect->filter(function ($item) use ($activeId) {
                    return $item['activity_id'] == $activeId;
                })->first();
                $markerName = trim($marker['belongs_to_maker']['subject']);
                $arr = array(
                    'title' => trim($oneInvite['has_one_activity']['subject']),
                    'activity_id' => trim($oneInvite['has_one_activity']['id']),
                    'img' => getImage($oneInvite['has_one_activity']['list_img'], 'activity'),
                    'begin_time' => trim($oneInvite['has_one_activity']['begin_time']),
                    'agent_id' => trim($oneInvite['belongs_to_agent']['id']),
                    'host_cities' => trim($markerName),
                    'status_info' => array(
                        'status' => trim($oneInvite['status']),
                        'remark' => "",
                    ),
                    'confirm_time' => trim($oneInvite['updated_at']),
                    'create_time' => trim($oneInvite['created_at']),
                    'invite_id' => trim($oneInvite['id']),
                );
            }
            $arr['invitor'] = self::unifiHandleName($oneInvite['belongs_to_agent']);
            $data[] = $arr;
        }
        return $data;
    }

    /**
     * 用户的考察邀请函列表
     *
     * @param $param
     * @return array|string
     *
     * @internal param $phoneArr
     * @internal param $type
     *
     * author zhaoyf
     */
    public function getInspectList($param)
    {
        //判断是否传递了某个status状态类型，和对传递的类型进行格式的过滤处理 4的状态020902加上的
        if (!empty($param['status']) || isset($param['status'])) {
            if (!is_numeric($param['status']) || !in_array($param['status'], [0, -1, 1, 2, 3, 4])) {
                return 'status 状态类型：只能是整形数值且只能为：0, -1, 1, 2, 3, 4';
            } else {
                $status = [intval($param['status'])];
                if ($status[0] == 1) {
                    $status = [1, 2, 3, 4];
                }
            }
        } else {
            $status = [-1, 0, 1, 2, 3, 4];
        }

        //获取集合数据
        $gather_result = Invitation::with('hasOneStore.hasOneBrand',
            'hasOneStore.hasOneZone', 'hasOneOrderItems.orders'
        )
         ->where('uid', $param['uid'])
         ->where('type', self::INSPECT_TYPE)
         ->whereIn('status', $status)
         ->get();

        //获取不同类型的数据
        foreach ($gather_result as $key => $res) {
            $brand_result = Brand::where('id', $res['hasOneStore']['hasOneBrand']['id'])->first()->agent_status;
                if (($res['status'] == 1 || $res['status'] == 2 || $res['status'] == 4 || $res['is_audit'] == 1) && $brand_result == self::CONSENT_TYPE) {
                    if (!empty($res['hasOneOrderItems']->toArray())) {
                        foreach ($res['hasOneOrderItems'] as $keys => $ress) {
                            if ($ress['orders']['status'] == 'pay') {
                                $confirms_result['confirm_time'][] = [
                                    'invite_id'     => $res['id'],
                                    'store_name'    => array_get($res['hasOneStore'], 'name'),
                                    'brand_title'   => array_get($res['hasOneStore']['hasOneBrand'], 'name'),
                                    'inspect_time'  => $res['inspect_time'],
                                    'created_at'    => $res['created_at']->getTimeStamp(),
                                    'status'        => $res['status'],
                                    'agent'         => self::where('id', $res['agent_id'])->first()->is_public_realname ? self::where('id', $res['agent_id'])->first()->realname : self::where('id', $res['agent_id'])->first()->nickname,
                                    'currency'      => number_format($res['default_money']),
                                    'head_address'  => array_get($res['hasOneStore']['hasOneZone'], 'name'),
                                    'inspect_address' => array_get($res['hasOneStore'], 'address'),
                                    'pay_way'         => !empty(array_get($ress['orders'], 'pay_way')) ? Items::pay_way($ress['orders']['pay_way']) : '',
                                    'pay_at'          => array_get($ress['orders'], 'pay_at'),
                                    'order_no'        => array_get($ress['orders'], 'order_no'),
                                    'confirm_time'    => $res['updated_at']->getTimeStamp(),
                                ];
                            }
                        }
                    }
                }
                if ($res['status'] == 0) {
                    $confirms_result['undetermined_result'][] = [
                        'invite_id'     => $res['id'],
                        'store_name'    => array_get($res['hasOneStore'], 'name'),
                        'brand_title'   => array_get($res['hasOneStore']['hasOneBrand'], 'name'),
                        'inspect_time'  => $res['inspect_time'],
                        'created_at'    => $res['created_at']->getTimeStamp(),
                        'status'        => $res['status'],
                        'agent'         => self::where('id', $res['agent_id'])->first()->is_public_realname ? self::where('id', $res['agent_id'])->first()->realname : self::where('id', $res['agent_id'])->first()->nickname,
                        'currency'      => number_format($res['default_money']),
                        'head_address'  => array_get($res['hasOneStore']['hasOneZone'], 'name'),
                        'inspect_address' => array_get($res['hasOneStore'], 'address'),
                        'status_summary'  => countDown($res['expiration_time']),
                    ];
                }
                if ($res['status'] == -1) {
                    $confirms_result['reject_result'][] = [
                        'invite_id'     => $res['id'],
                        'store_name'    => array_get($res['hasOneStore'], 'name'),
                        'brand_title'   => array_get($res['hasOneStore']['hasOneBrand'], 'name'),
                        'inspect_time'  => $res['inspect_time'],
                        'created_at'    => $res['created_at']->getTimeStamp(),
                        'status'        => $res['status'],
                        'agent'         => self::where('id', $res['agent_id'])->first()->is_public_realname ? self::where('id', $res['agent_id'])->first()->realname : self::where('id', $res['agent_id'])->first()->nickname,
                        'currency'      => number_format($res['default_money']),
                        'remark'        => $res['remark'],
                        'head_address'  => array_get($res['hasOneStore']['hasOneZone'], 'name'),
                        'inspect_address' => array_get($res['hasOneStore'], 'address'),
                        'confirm_time'    => $res['updated_at']->getTimeStamp(),
                    ];
                }
            }

        //返回结果
        return !empty($confirms_result) ? $confirms_result : [];
    }

    public static function getCanInvite($phoneArr, $type)
    {
        $data = [];
        foreach ($phoneArr as $onePhone) {
            if (!checkMobile($onePhone)) {
                $data[] = array(
                    "phone" => trim($onePhone),
                    'can_invite' => '0',
                    'reason' => '该号码格式错误',
                );
                continue;
            }
            if ($type == 1) {
                $userInfo = User::where('username', $onePhone)->first();
                if (is_object($userInfo) && !empty($userInfo['register_invite'])) {
                    $data[] = array(
                        "phone" => trim($onePhone),
                        'can_invite' => '0',
                        'reason' => '该用户邀请人已存在',
                    );
                    continue;
                }
                $data[] = array(
                    "phone" => trim($onePhone),
                    'can_invite' => '1',
                );
            } else {
                $agentInfo = self::where('username', $onePhone)->first();
                if (is_object($agentInfo)) {
                    $data[] = array(
                        "phone" => trim($onePhone),
                        'can_invite' => '0',
                        'reason' => '该用户已经是经纪人',
                    );
                    continue;
                }
                $data[] = array(
                    "phone" => trim($onePhone),
                    'can_invite' => '1',
                );
            }
        }
        return $data;
    }

    /**
     * 经纪人跟单提醒
     *
     * @param $param
     *
     * @return array|Collection
     */
    public function documentaryHints($param)
    {
        self::$params = $param; //传递参数给静态方法，是为了hasManyInvite()方法需要参数值
        $results = Invitation::with(['hasOneStore.hasOneBrand',
            'hasOneUsers', 'hasOneStore.hasOneZone',
            'hasOneOrderItems' => function ($query) {
                $query->where('type', 'inspect_invite');
            }, 'hasOneOrderItems.orders'
        ])
         ->where('type', self::INSPECT_TYPE)
         ->where('agent_id', $param['agent_id'])
         ->get();

        foreach ($results as $key => $result) {
            if ($result->status == self::CONSENT_TYPE || $result->status == 2 || $result->status == 4 || $result->is_audit == 1) {
                if (!empty($result['hasOneOrderItems'])) {
                    foreach ($result['hasOneOrderItems'] as $keys => $vls) {
                        if ($vls['orders']['status'] == 'pay') {
                            $confirm_result[] = [
                                'inspect_id'       => $result['id'],
                                'status'           => $result['status'],
                                'create_time'      => $result['created_at']->getTImestamp(),
                                'nickname'         => array_get($result['hasOneUsers'], 'nickname'),
                                'avatar'           => !empty(array_get($result['hasOneUsers'], 'avatar')) ? getImage(array_get($result['hasOneUsers'], 'avatar')) : getImage('', 'avatar', ''),
                                'brand_name'       => !empty(array_get($result['hasOneStore']['hasOneBrand'], 'name')) ? array_get($result['hasOneStore']['hasOneBrand'], 'name') : '',
                                'head_address'     => !empty(array_get($result['hasOneStore'], 'name')) ? array_get($result['hasOneStore'], 'name') : '',
                                'inspect_address'  => !empty(array_get($result['hasOneStore'], 'address')) ? array_get($result['hasOneStore'], 'address') : '',
                                'inspect_time'     => $result['inspect_time'],
                                'currency'         => number_format($result['default_money']),
                                'pay_way'          => !empty(Items::pay_way(array_get($vls['orders'], 'pay_way'))) ? Items::pay_way(array_get($vls['orders'], 'pay_way')) : '',
                                'buyer_id'         => !empty(Orders::$_PAYWAY[array_get($vls['orders'], 'buyer_id')]) ? Orders::$_PAYWAY[array_get($vls['orders'], 'buyer_id')] : '',
                                'status_summary'   => '已接受邀请',
                                'confirm_time'     => $result['updated_at']->getTimestamp(),
                                'confirm_day'      => date("Y/m/d", $result['updated_at']->getTimestamp()),
                                'type'             => 1,
                            ];
                        }
                    }
                }
            }

            if ($result->status == self::REJECT_TYPE) {
                $confirm_result[] = [
                    'inspect_id'      => $result['id'],
                    'create_time'     => $result['created_at']->getTimestamp(),
                    'status'          => $result['status'],
                    'nickname'        => array_get($result['hasOneUsers'], 'nickname'),
                    'avatar'          => !empty(array_get($result['hasOneUsers'], 'avatar')) ? getImage(array_get($result['hasOneUsers'], 'avatar')) : getImage('', 'avatar', ''),
                    'brand_name'      => !empty(array_get($result['hasOneStore']['hasOneBrand'], 'name')) ? array_get($result['hasOneStore']['hasOneBrand'], 'name') : '',
                    'head_address'    => !empty(array_get($result['hasOneStore'], 'name')) ? array_get($result['hasOneStore'], 'name') : '',
                    'inspect_address' => !empty(array_get($result['hasOneStore'], 'address')) ? array_get($result['hasOneStore'], 'address') : '',
                    'inspect_time'    => $result['inspect_time'],
                    'currency'        => number_format($result['default_money']),
                    'pay_way'         => !empty(Items::pay_way(array_get($result['hasOneOrderItems']['orders'], 'pay_way'))) ? Items::pay_way(array_get($result['hasOneOrderItems']['orders'], 'pay_way')) : '',
                    'status_summary'  => '已拒绝',
                    'reson'           => $result['remark'],
                    'confirm_time'    => $result['updated_at']->getTimestamp(),
                    'confirm_day'     => date("Y/m/d", $result['updated_at']->getTimestamp()),
                    'type'            => 1,    //区分类型
                ];
            }
        }

        //activity: 活动邀请状态列表，
        //contract: 合同状态列表
        $activity = Agent::instance()->getInvitationResult($param['agent_id'], [1, -1], $param['page'], $param['page_size']);
        $contract = Contract::contractNotice($param['agent_id']);

        //返回组合后的结果
        $confirm_gather_result = [
            'activity' => !empty($activity)       ? $activity       : [],
            'inspect'  => !empty($confirm_result) ? $confirm_result : [],
            'contract' => !empty($contract)       ? $contract       : []
        ];

        //将多个数组数据合并到一个数组里
        $new_array_data = array();
        foreach ($confirm_gather_result as $key => $vls) {
            foreach ($vls as $keys => $vs) {
                $new_array_data[] = $vs;
            }
        }

        //返回排序后的数据
        $confirm_data = collect($new_array_data)->sortByDesc('confirm_time');

        //对天数相同的数据进行归类处理
        $arrays = array();
        if ($confirm_data) {
            foreach ($confirm_data as $key => $vls) {
                $arrays[$vls['confirm_day']]['confirm_day'] = $vls['confirm_day'];
                $arrays[$vls['confirm_day']]['result'][]    = $confirm_data[$key];
            }
            rsort($arrays);
        } else {
            return [];
        }

        return $arrays ?: [];
    }

    /**
     * 获取季度选项
     */
    public function getQuarterChoice($timestamp, $created_at)
    {
        //定义每季度的开始和结束日期
        $last_last_fourth_qurater = mktime(0, 0, 0, 1, 1, date('Y') - 2);
        $last_first_qurater = mktime(0, 0, 0, 1, 1, date('Y') - 1);
        $last_second_qurater = mktime(0, 0, 0, 4, 1, date('Y') - 1);
        $last_third_qurater = mktime(0, 0, 0, 7, 1, date('Y') - 1);
        $last_fourth_qurater = mktime(0, 0, 0, 1, 1, date('Y') - 1);
        $first_qurater = mktime(0, 0, 0, 1, 1, date('Y'));
        $second_qurater = mktime(0, 0, 0, 4, 1, date('Y'));
        $third_qurater = mktime(0, 0, 0, 7, 1, date('Y'));
        $fourth_qurater = mktime(0, 0, 0, 10, 1, date('Y'));
        $fiveth_qurater = mktime(0, 0, 0, 1, 1, date('Y') + 1);


        if ($created_at < $last_first_qurater) {
            $num = 1;
        } elseif ($last_first_qurater <= $created_at && $created_at < $last_second_qurater) {
            $num = 2;
        } elseif ($last_second_qurater <= $created_at && $created_at < $last_third_qurater) {
            $num = 3;
        } elseif ($last_third_qurater <= $created_at && $created_at < $last_fourth_qurater) {
            $num = 4;
        } elseif ($last_fourth_qurater <= $created_at && $created_at < $first_qurater) {
            $num = 5;
        } elseif ($first_qurater <= $created_at && $created_at < $second_qurater) {
            $num = 6;
        } elseif ($second_qurater <= $created_at && $created_at < $third_qurater) {
            $num = 7;
        } elseif ($third_qurater <= $created_at && $created_at < $fourth_qurater) {
            $num = 8;
        } elseif ($fourth_qurater <= $created_at && $created_at < $fiveth_qurater) {
            $num = 9;
        }


        if ($timestamp >= $first_qurater && $timestamp < $second_qurater) {
            $data = [
                date('Y') - 2 . '年10月-12月',
                date('Y') - 1 . '年1月-3月',
                date('Y') - 1 . '年4月-6月',
                date('Y') - 1 . '年7月-9月',
                date('Y') - 1 . '年10月-12月',
                date('Y') . '年1月-3月',
            ];

            $data = array_slice(array_reverse($data), 0, 6 - ($num - 1));
        } elseif ($timestamp >= $second_qurater && $timestamp < $third_qurater) {
            $data = [
                date('Y') - 1 . '年1月-3月',
                date('Y') - 1 . '年4月-6月',
                date('Y') - 1 . '年7月-9月',
                date('Y') - 1 . '年10月-12月',
                date('Y') . '年1月-3月',
                date('Y') . '年4月-6月',
            ];
            $data = array_slice(array_reverse($data), 0, 6 - ($num - 2));

        } elseif ($timestamp >= $third_qurater && $timestamp < $fourth_qurater) {
            $data = [
                date('Y') - 1 . '年4月-6月',
                date('Y') - 1 . '年7月-9月',
                date('Y') - 1 . '年10月-12月',
                date('Y') . '年1月-3月',
                date('Y') . '年4月-6月',
                date('Y') . '年7月-9月',
            ];
            $data = array_slice(array_reverse($data), 0, 6 - ($num - 3));

        } elseif ($timestamp >= $fourth_qurater && $timestamp < $fiveth_qurater) {
            $data = [
                date('Y') - 1 . '年7月-9月',
                date('Y') - 1 . '年10月-12月',
                date('Y') . '年1月-3月',
                date('Y') . '年4月-6月',
                date('Y') . '年7月-9月',
                date('Y') . '年10月-12月',
            ];
            $data = array_slice(array_reverse($data), 0, 6 - ($num - 4));

        } else {
            throw new \RuntimeException('日期不在今年的范围内');
        }

        return $data;
    }

    /*
     * 活动报名信息
     * */
    public static function getAgentEnroll($agentId, $uid)
    {
        $data = [];
        $agentInfo = self::with('hasOneAgentLevel', 'hasManyCustomer')
            ->with(['hasManyCustomer' => function ($query) use ($uid) {
                $query->where('uid', $uid);
            }])
            ->where(function ($query) use ($agentId) {
                $query->where('id', $agentId);
            })
            ->first()->toArray();
        $source = intval($agentInfo['has_many_customer'][0]['source']);
        $relative = 1;
        switch ($source) {
            case 1 :
            case 2 :
            case 3 :
            case 4 :
                $relative = 2;
                break;
            case 5 :
                $relative = 1;
                break;
            case 6 :
                $relative = 3;
                break;
            case 7 :
                $relative = 4;
                break;
            case 8 :
                $relative = 5;
                break;
        }

        $data = array(
            'nickname' => trim($agentInfo['nickname']),
            'realname' => trim($agentInfo['realname']),
            'is_public_realname' => trim($agentInfo['is_public_realname']),
            'avatar' => getImage($agentInfo['avatar']),
            'level_title' => trim($agentInfo['has_one_agent_level']['name']),
            'level' => trim($agentInfo['agent_level_id']),
            'relative' => $relative,
        );
        return $data;
    }

    /*
     * shiqy
     * 邀请口号
     * */
    public static function getInviteSlogan($agentId, $type)
    {
        $agentInfo = self::where('id', $agentId)->where('status', 1)->first();
        if (!is_object($agentInfo)) {
            return array(
                'error' => 1,
                'message' => '邀请人无效'
            );
        }
        if ($type == 'agent') {
            $configInfo = Config::where('code', 'agent_invite_slogan')
                ->first();
        } else {
            $configInfo = Config::where('code', 'customer_invite_slogan')
                ->first();
            $isHave = RedPacket::showWhere()->where('type',1)->first();
        }
        $data = array(
            'avatar' => getImage($agentInfo['avatar']),
            'realname' => trim($agentInfo['realname']),
            'phone' => idCardEncrypt($agentInfo['username'] , 3 ,4,4),
            'nickname' => trim($agentInfo['nickname']),
            'is_public_realname' => trim($agentInfo['is_public_realname']),
            'slogan' => trim($configInfo['value']),
            'is_have_redpacket' => is_object($isHave) ? 1 : 0,
        );
        return $data;
    }



    /**
     * 被邀请的情况下注册成经纪人  数据中心版
     * @User yaokai
     * @param $agentId
     * @param $username
     * @param $code
     * @param $type
     * @param string $nickname
     * @param string $password
     * @param string $nationCode
     * @return array|string|static
     */
    public static function getAgentRegister($agentId, $username, $code, $type, $nickname = '', $password = '', $nationCode = '86')
    {
        //md5加盐后的号码
        $non_reversible = encryptTel($username);
        //伪号码
        $en_username = pseudoTel($username);

        $agentInfo = self::where('id', $agentId)->first();
        $downAgent = self::where('non_reversible', $non_reversible)->first();
        if (is_object($downAgent)) {
            return array(
                'error' => 1,
                'message' => '该号码已经注册过了'
            );
        }
        if (empty($code)) {
            return "该号码可以注册";
        }
        $indentResult = Identify::checkIdentify($non_reversible, $type, $code, $time = 900, 'agent');
        if ($indentResult != 'success') {
            return array(
                'error' => 1,
                'message' => $indentResult
            );
        }

        //数据中心处理
        $url = config('system.data_center.hosts') . config('system.data_center.encrypt');
        $data = [
            'nation_code' => $nationCode,
            'tel' => $username,
            'platform' => 'agent',//来源无界商圈注册
            'en_tel' => $non_reversible,//通过加盐后得到手机号码
        ];

        //请求数据中心接口
        $result = json_decode(getHttpDataCenter($url, '', $data));

        //如果异常则停止
        if (!$result) {
            return ['status' => FALSE, 'message' => '服务器异常！'];
        } elseif ($result->status == false) {
            return ['status' => false, 'message' => $result->message];
        }

        $rel = self::create([
            'nation_code' => trim($nationCode),
            'username' => $en_username,
            'non_reversible' => $non_reversible,
            "password" => empty($password) ? \Hash::make($username) : \Hash::make($password),
            "nickname" => empty($nickname) ? 'a' . time() : trim($nickname),
            'register_invite' => trim($agentInfo['non_reversible']),
            'my_invite' => Agent::createInviteNum(Agent::class, 'my_invite'),
            'agent_level_id' => 1,
            'is_alter_nickname' => empty($nickname)? 0 : 1 ,
        ]);

        //给积分
        Agentv010200::add($agentId, AgentScoreLog::$TYPES_SCORE[13], 13, '发展团队', $rel->id, 1);

        if (!is_object($rel)) {
            return array(
                'error' => 1,
                'message' => '注册失败'
            );
        }
        //给此经纪人创建一条附加记录,同时邀请经纪人抽奖次数加1
        AgentAdd::create(['agent_id'=>$rel['id']]);
        AgentAdd::where('agent_id',$agentId)->increment('draw_num');
        unset($rel->password);
        return $rel;
    }


    /**
     * 被邀请的情况下注册成投资人  数据中心版
     * @User yaokai
     * @param $agentId
     * @param $username
     * @param $code
     * @param $type
     * @param string $nickname
     * @param $appName
     * @param string $nationCode
     * @return array
     */
    public static function getCustomerRegister($agentId, $username, $code, $type, $nickname = '', $appName, $nationCode = '86')
    {
        //md5加盐后的号码
        $non_reversible = encryptTel($username);
        //伪号码
        $en_username = pseudoTel($username);

        $agentInfo = self::where('id', $agentId)->first();
        $downAgent = User::where('non_reversible', $non_reversible)->first();
        if (is_object($downAgent) && (!empty($downAgent['register_invite']) || $downAgent['status'] == -1)) {
            return array(
                'error' => 1,
                'message' => '该号码不能被邀请'
            );
        }

        //下面发送短信使用需要 zhaoyf 2017-12-15 14:35
        $datas = &$downAgent;

        $indentResult = Identify::checkIdentify($non_reversible, $type, $code, $time = 900, $appName);
        if ($indentResult != 'success') {
            return array(
                'error' => 1,
                'message' => $indentResult
            );
        }

        //判断是否是已经注册但没有邀请人的投资人
        $isSelfRegister = 0;

        DB::transaction(function () use ($username, $agentInfo, $agentId, &$downAgent, $nickname,$nationCode , &$isSelfRegister, &$datas,$non_reversible,$en_username) {
            $nowTime = time();
            //判断该投资人是否存在
            if (is_object($downAgent)) {
                User::where('non_reversible', $non_reversible)->update(['register_invite' => trim($agentInfo['non_reversible'])]);
                $uid = intval($downAgent['uid']);
                $rel = $downAgent;
                $isSelfRegister = 1;
            } else {

                //数据中心处理
                $url = config('system.data_center.hosts') . config('system.data_center.encrypt');
                $data = [
                    'nation_code' => $nationCode,
                    'tel' => $username,
                    'platform' => 'agent',//来源无界商圈注册
                    'en_tel' => $non_reversible,//通过加盐后得到手机号码
                ];

                //请求数据中心接口
                $result = json_decode(getHttpDataCenter($url, '', $data));

                //如果异常则停止
                if (!$result) {
                    return ['status' => FALSE, 'message' => '服务器异常！'];
                } elseif ($result->status == false) {
                    return ['status' => false, 'message' => $result->message];
                }


                $rel = User::create(
                    [
                        'nation_code' => trim($nationCode),
                        'username' => $en_username,
                        'non_reversible' => $non_reversible,
                        'nickname' => empty($nickname) ? getRandomString(5) : trim($nickname),
                        'register_invite' => trim($agentInfo['non_reversible']),
                        'created_at' => $nowTime,
                        'my_invite' => User::generateUniqueInviteCode(),
                    ]
                );
                $uid = $rel->uid;
            }
            //
            $downAgent = $rel;
            //判断该投资人与该经纪人是否有派单关系
            $agentCustomer = AgentCustomer::where(function ($query) use ($uid, $agentId) {
                $query->where('agent_id', $agentId);
                $query->where('uid', $uid);
            })->first();

            if (is_object($agentCustomer)) {
                AgentCustomer::where('id', $agentCustomer['id'])->update(
                    [
                        'source' => 7,
                        'has_tel' => 1,
                        'protect_time' => strtotime("+30 day"),
                    ]
                );
                $agentCustomerId = intval($agentCustomer['id']);
            } else {
                $agentCustomerInfo = AgentCustomer::create(
                    [
                        'agent_id' => $agentId,
                        'uid' => $uid,
                        'protect_time' => strtotime("+30 day"),
                        'source' => 1,
                        'brand_id' => 0,
                        'has_tel' => 1,
                        'created_at' => $nowTime,
                    ]
                );
                $agentCustomerId = $agentCustomerInfo->id;

                ################## start zhaoyf ###################

                //经纪人邀请投资人成功后，发送短信提示 zhaoyf 2017-12-13 13:35

                //对结果进行处理
                if ($datas) {
                    SendTemplateSMS(
                        'invite_customer_note_inform',          //短信模板名称
                        $agentInfo['non_reversible'],                 //接受短信人的手机号
                        'invite_customer_note_inform', [        //短信模板类型和需要传递的值
                        'user_name' => $datas->realname ?: $datas->nickname,
                        'user_tel'  => $datas->username,
                    ],$datas->nation_code);
                }
                ################### end ##################
            }

            //如果该经纪人以前对该投资人公开手机号，则不用生成获取手机号的记录。
            $data = [
                [
                    'agent_customer_id' => $agentCustomerId,
                    'action' => 2,
                    'post_id' => 0,
                    'remark' => '',
                    'brand_id' => 0,
                    'agent_id' => $agentId,
                    'uid' => $uid,
                    'created_at' => $nowTime,
                    'is_delete' => 0,
                ],
            ];


            $log = AgentCustomerLog::create(
                [
                    'agent_customer_id' => $agentCustomerId,
                    'action' => 14,
                    'post_id' => 0,
                    'remark' => '',
                    'brand_id' => 0,
                    'agent_id' => $agentId,
                    'uid' => $uid,
                    'created_at' => $nowTime,
                    'is_delete' => 0,
                ]
            );


            if (!is_object($agentCustomer) || $agentCustomer['has_tel'] == 0) {
                AgentCustomerLog::insert($data);
            }

            /*
             * 新年活动期间，邀请一个投资人，经纪人增加一次抽奖机会
             * 先判断该活动时候结束
             * */
            $newYearRedPacks = RedPacket::showWhere()->whereIn('type',[5,7])->count();
            if(!empty($newYearRedPacks)){
                AgentAdd::where('agent_id',$agentId)->increment('draw_num');
            }


            //给积分
            Agentv010200::add($agentId, AgentScoreLog::$TYPES_SCORE[12], 12, '邀请投资人', $log->id, 1);

//            给经纪人发透传 使其更新通讯录
            $res = send_transmission(json_encode(['type' => 'bind', 'style' => 'json',
                'value' => ['username' => getRealTel($agentInfo->non_reversible, 'agent'), 'id' => $rel->uid, 'realname' => $rel->realname,
                    'nickname' => $rel->nickname]]), $agentInfo, null, 1);

//            //获取投资人的token
            $user_token = GainToken($rel['uid'], $rel['nickname'],'');
            User::where('uid', $rel['uid'])->update(['token' => $user_token]);
        });
        unset($downAgent->password);
        $downAgent->is_self_register = $isSelfRegister;
        return $downAgent;
    }


    /*
    *shiqy
    * 编辑客户跟单日志
    *
    * */
    public static function getEditRemark($id, $levelId, $remark)
    {
        $logInfo = AgentCustomerLog::where('id', $id)->first();
        if (!is_object($logInfo)) {
            return array(
                'error' => 1,
                'message' => '请输入有效的日志id'
            );
        }
        AgentCustomerLog::where('id', $id)->update(['remark' => $remark]);
        AgentCustomer::where('id', $logInfo['agent_customer_id'])->update(['level' => $levelId]);
        return '更新成功';
    }

    /*
    *shiqy
    * 删除客户跟单日志
    *
    * */
    public static function getDeleteRemark($id)
    {
        $logInfo = AgentCustomerLog::where('id', $id)->first();
        if (!is_object($logInfo)) {
            return array(
                'error' => 1,
                'message' => '请输入有效的日志id'
            );
        }
        $rel = AgentCustomerLog::where('id', $id)->update(['is_delete' => 1]);
        return '删除成功';
    }


    /**
     * 我的全部经纪人  --数据中心版  没有影响 暂不处理
     * @User shiqy
     * @param $uid
     * @return array
     */
    public static function getMyAllAgents($uid)
    {
        $userInfo = User::with('agent_customer_log.agent',
            'agent_customer_log.agent_customer',
            'agent_customer_log.hasOneBrandAll')
            ->with(['agent_customer_log.agent_customer' => function ($query) use ($uid) {
                $query->where('uid', $uid);
            }])
            ->where(function ($query) use ($uid) {
                $query->where('uid', $uid);
                $query->where('status', '<>', -1);
            })->first();
        if (!is_object($userInfo)) {
            return ['message' => '请输入一个有效用户', 'error' => 1];
        }
        $userInfo = $userInfo->toArray();
        $data = [];
        //全部经纪人
        $agentBox = [];
        $newLogArr = [];
        foreach ($userInfo['agent_customer_log'] as $oneLog) {
            if (in_array(intval($oneLog['agent_id']), $agentBox)) {
                continue;
            }
            $agentBox[] = intval($oneLog['agent_id']);
            $newLogArr[] = $oneLog;
        }
        $data['service_agents'] = trim(count($newLogArr));
        $agentGroupCollect = collect($newLogArr)->groupBy(function ($item) {
            return getfirstchar($item['agent']['nickname']);
        });
        $data['agent_list'] = [];
        foreach ($agentGroupCollect as $key => $oneGroup) {
            $arr = [];
            $arr['letter'] = $key;
            foreach ($oneGroup as $oneAgents) {
                $city = Zone::getCityAndProvince($oneAgents['agent']['zone_id']);
                $agentId = trim($oneAgents['agent']['id']);
                $arr['list'][] = array(
                    'id' => $agentId,
                    'avatar' => getImage($oneAgents['agent']['avatar']),
                    'nickname' => trim($oneAgents['agent']['nickname']),
                    'realname' => trim($oneAgents['agent']['realname']),
                    'is_public_realname' => trim($oneAgents['agent']['is_public_realname']),
                    'username' => trim($oneAgents['agent']['username']),
                    'gender' => trim($oneAgents['agent']['gender']),
                    'city' => $city,
                    'isPub' => trim($oneAgents['agent_customer']['has_tel']),
                    'brandName' => trim($oneAgents['has_one_brand_all']['name']),
                );
            }
            $data['agent_list'][] = $arr;
        }
        return $data;
    }

    //保存实名认证信息
    public static function saveAuthInfo($input)
    {
        $keys = [
            'agent_id',
            'identity_card_front',
//            'identity_card_reverse',
            'gender',
            'identity_card',
            'realname',
            'birth',
        ];
        $input = array_only($input, $keys);
        foreach ($input as &$item) {
            $item = trim($item);
        }
        if (empty($input['agent_id'])) {
            return array(
                'error' => 1,
                'message' => '经纪人id不能为空',
            );
        }
        $agentId = intval($input['agent_id']);

        unset($input['agent_id']);
        if (!($input['identity_card_front'])) {
            return array(
                'error' => 1,
                'message' => '身份证正面不能为空',
            );
        }
        $isRight = idCardExpVerify($input['identity_card']);
        if (!$isRight) {
            return array(
                'error' => 1,
                'message' => '身份证格式不正确',
            );
        }
        $agentCard = Agent::where('identity_card', $input['identity_card'])->first();
        if (is_object($agentCard)) {
            return array(
                'error' => 1,
                'message' => '该身份证已注册过，请更换身份证',
            );
        }
        $input['is_verified'] = 1;
        Agent::where("id", $agentId)->update($input);



        //是否已完善资料
        $complete = Agentv010200::isComplete($agentId);

        if($complete){
            //给积分
            Agentv010200::add($agentId, AgentScoreLog::$TYPES_SCORE[18], 18, '实名认证', 0, 1,1, false,false, -1);
            //给积分
            Agentv010200::add($agentId, AgentScoreLog::$TYPES_SCORE[17], 17, '完善个人资料', 0, 1, 1, false,false,7);
        }else{
            //给积分
            Agentv010200::add($agentId, AgentScoreLog::$TYPES_SCORE[18], 18, '实名认证', 0, 1,1);
        }

        return true;
    }

    /**
     * author zhaoyf
     *
     * 客户详情 -- 意向记录
     *
     * @param $param    投资人ID
     * @return array|string
     */
    public function customerIntentionRecords($param)
    {
        $gain_result = array();

        ############  判断经纪人和投资人是否是邀请关系 ##############

        $judge_result = AgentCustomer::where('uid', $param['customer_id'])
            ->where('agent_id', $param['agent_id'])
            ->where('level', '<>', self::LOST_CUSTOMER_TYPE)
            ->where('status', '<>', self::LOST_CUSTOMER_TYPE)
            ->whereIn('source', [1, 2, 3, 4, 6, 7])
            ->first();

        //不是邀请关系时，直接返回空
        if (is_null($judge_result)) return null;


        ################# 获取投资人当前咨询的品牌 #################

        $logInfos = AgentCustomerLog::with('hasOneBrandAll.categorys1')
            ->whereHas('hasOneBrandAll', function ($query) {
                $query->where('agent_status', self::CONSENT_TYPE);
            })
            ->where('uid', $param['customer_id'])
            ->where('agent_id', '<>', $param['agent_id'])
            ->where('brand_id', '<>', 0)
            ->where('action', 1)
            ->get()->toArray();

        $logsGroupCollect = collect($logInfos)->groupBy(function ($item) {
            return date('Y-m-d', $item['created_at']);
        });
        foreach ($logsGroupCollect as $key => $oneGroup) {
            $brandsGroupCollect = collect($oneGroup)->groupBy(function ($item) {
                return $item['brand_id'];
            });
            foreach ($brandsGroupCollect as $oneBrandGroup) {
                $gain_result[] = [
                    'pai_brand_id' => trim($oneBrandGroup[0]['has_one_brand_all']['id']),
                    'pai_brand_name' => trim($oneBrandGroup[0]['has_one_brand_all']['name']),
                    'pai_brand_logo' => getImage($oneBrandGroup[0]['has_one_brand_all']['logo']),
                    'pai_brand_cate' => trim($oneBrandGroup[0]['has_one_brand_all']['categorys1']['name']),
                    'type' => 'pai_brand',
                    'created_at' => $key,
                    'brand_status' => $this->brandStatusJudge($oneBrandGroup[0]['has_one_brand_all']['id']),
                ];
            }
        }

//        $agent_customer_result = AgentCustomer::with(['brand' => function($query) {
//            $query->where('agent_status', self::CONSENT_TYPE);
//        }, 'brand.categorys1' => function($query) {
//                $query->where('status', 'enable');
//        }])
//         ->where('uid',    $param['customer_id'])
//         ->where('level',  '<>', self::LOST_CUSTOMER_TYPE)
//         ->where('source', '<>', self::RECOMMENT_TYPE)
//         ->get();
//
////        对结果进行处理
//        if ($agent_customer_result) {
//            foreach ($agent_customer_result as $key => $vls) {
//
//                if (is_null($vls->brand)) continue;
//
//                $gain_result[] = [
//                    'pai_brand_id'    => $vls->brand->id,
//                    'pai_brand_name'  => $vls->brand->name,
//                    'pai_brand_logo'  => getImage($vls->brand->logo),
//                    'pai_brand_cate'  => $vls->brand->categorys1->name,
//                    'type'            => 'pai_brand',
//                    'created_at'      => $this->_timeHandle($vls->created_at),
//                    'brand_status'    => $this->brandStatusJudge($vls->brand->id)
//                ];
//            }
//        }


        ##################  获取投资人收藏的品牌  ###################

        $favorite_result = Favorite::with('hasOneBrands.categorys1')
            ->where('uid', $param['customer_id'])
            ->where('model', 'brand')
            ->where('status', self::CONSENT_TYPE)
            ->get();

        //对结果进行处理
        if ($favorite_result) {
            foreach ($favorite_result as $key => $vls) {
                $gain_result[] = [
                    'favorite_brand_id' => $vls->hasOneBrands->id,
                    'favorite_brand_name' => $vls->hasOneBrands->name,
                    'favorite_brand_img' => getImage($vls->hasOneBrands->logo),
                    'favorite_brand_cate' => $vls->hasOneBrands->categorys1->name,
                    'type' => 'favorite_brand',
                    'created_at' => $this->_timeHandle($vls->created_at),
                    'brand_status' => $this->brandStatusJudge($vls->hasOneBrands->id),
                ];
            }
        }


        #################  获取投资人报名的活动  ###################

        $sign_result = Sign::with(['hasOneActity' => function ($query) {
            $query->where('status', self::CONSENT_TYPE)
                ->where('begin_time', '>', time());
        }])
            ->where('uid', $param['customer_id'])
            ->get();

        //对结果进行处理
        if ($sign_result) {
            foreach ($sign_result as $key => $vls) {

                if (is_null($vls->hasOneActity)) continue;

                $gain_result[] = [
                    'activity_id' => $vls->hasOneActity->id,
                    'activity_name' => $vls->hasOneActity->subject,
                    'activity_img' => getImage($vls->hasOneActity->list_img),
                    'activity_time' => $vls->hasOneActity->begin_time,
                    'type' => 'activity',
                    'created_at' => $this->_timeHandle($vls->created_at)
                ];
            }
        }


        ##################  获取投资人订阅的直播  ####################

        $subscription_result = Subscription::with(['Live' => function ($query) {
            $query->where('status', 0);
        }])
            ->where('uid', $param['customer_id'])
            ->where('status', self::CONSENT_TYPE)
            ->get();

        //对结果进行处理
        if ($subscription_result) {
            foreach ($subscription_result as $key => $vls) {
                $gain_result[] = [
                    'live_activity_id' => $vls->Live->activity_id,
                    'live_id' => $vls->Live->id,
                    'make_id' => $vls->Live->maker_id,
                    'live_title' => $vls->Live->subject,
                    'live_img' => getImage($vls->Live->list_img),
                    'live_time' => $vls->Live->begin_time,
                    'type' => 'subscription',
                    'created_at' => $this->_timeHandle($vls->created_at)
                ];
            }
        }


        ###################  获取投资人接受的考察邀请函  ######################

//        $invitation_result = Invitation::with('hasOneStore.hasOneBrand.categorys1')
//            ->where('uid', $param['customer_id'])
//            ->where('type',   self::INSPECT_TYPE)
//            ->where('status', self::CONSENT_TYPE)
//            ->where('expiration_time', '>', time())
//            ->get();

        $invitation_result = Invitation::with('hasOneStore.hasOneBrand.categorys1')
            ->where('uid', $param['customer_id'])
            ->where('type', self::INSPECT_TYPE)
            ->whereIn('status', [1, 2])
            ->get();

        //对结果进行处理
        if ($invitation_result) {
            foreach ($invitation_result as $key => $vls) {
                $gain_result[] = [
                    'inspect_invite_id' => $vls->id,
                    'brand_id' => $vls->hasOneStore->hasOneBrand->id,
                    'brand_name' => $vls->hasOneStore->hasOneBrand->name,
                    'brand_logo' => getImage($vls->hasOneStore->hasOneBrand->logo),
                    'brand_cate' => $vls->hasOneStore->hasOneBrand->categorys1->name,
                    'inspect_time' => $vls->inspect_time,
                    'type' => 'inspect_invite',
                    'created_at' => $this->_timeHandle($vls->created_at),
                    'brand_status' => $this->brandStatusJudge($vls->hasOneStore->hasOneBrand->id),
                ];
            }
        }


        ####################  获取投资人成功加盟的品牌  ##################

        $contract_result = Contract::with('brand.categorys1')
            ->where('uid', $param['customer_id'])
            ->where('status', self::INSPECT_TYPE)
            ->get();

        //对结果进行处理
        if ($contract_result) {
            foreach ($contract_result as $key => $vls) {
                $gain_result[] = [
                    'brand_id' => $vls->brand->id,
                    'brand_name' => $vls->brand->name,
                    'brand_logo' => getImage($vls->brand->logo),
                    'brand_cate' => $vls->brand->categorys1->name,
                    'invite_person' => Agent::where('id', $param['agent_id'])->where('status', self::CONSENT_TYPE)->first()->realname . '获得1000元奖励',
                    'type' => 'contract',
                    'created_at' => $this->_timeHandle($vls->created_at),
                    'brand_status' => $this->brandStatusJudge($vls->brand->id),
                ];
            }
        }

        //综合处理，进行排序组合
        if (!is_null($gain_result)) {
            $_result = array();
            $count = array();
            foreach ($gain_result as $key => $vls) {
                $_result[$vls['created_at']]['created_at'] = $vls['created_at'];
                $_result[$vls['created_at']]['data_list'][$key] = $gain_result[$key];
            }
            sort($_result);

            //返回最后数据结果
            $result_out = collect($_result)->sortByDesc('created_at');
            return [
                'data_list' => $result_out,
                'count' => count($result_out),
            ];
        } else {
            return null;
        }
    }

    /**
     * author zhaoyf
     *
     * 时间格式处理
     *
     * @param $time
     *
     * @return false|string
     */
    private function _timeHandle($time)
    {
        return date("Y-m-d", strtotime($time));
    }

    /**
     * 获取品牌的状态启用和禁用
     *
     * @param $brand_id
     *
     * @return int
     */
    public function brandStatusJudge($brand_id)
    {
        $brand_result = Brand::where('id', $brand_id)->first();

        //对状态进行判断
        if ($brand_result->agent_status == 1) {
            return '正常';
        } else {
            return '该品牌已经下架';
        }
    }





    /**
     * 获取某一经纪人的所有子孙树
     *
     * @param $arr
     * @param $username
     * @return array
     * @author tangjb
     */
    public static function sonTree($arr, $username)
    {
        static $Tree = array(); //只会初始化一次
        foreach($arr as $k=>$v) {
            if($v['register_invite'] == $username) {
                $Tree[] = $v;
                self::sonTree($arr,$v['username']);
            }
        }

        return $Tree;
    }


    /**
     * 添加登录日志
     *
     * @author tangjb
     */
    public static function createLog($userInfo)
    {
        $client = getClient();
        if('iPhone'==$client){
            $client =1;
        }elseif('android'==$client){
            $client =2;
        }else{
            $client =0;
        }

        //添加登录日志
        $login_data = [
            'uid' => $userInfo->id,
            'ip' => getIP(),
            'platform' => $client,
            'meid' => \Request::header('imei', ''),
        ];
        $res = AgentLoginLog::create($login_data);

        return $res;
    }



    /**判断一个经纪人是否对该品牌视频或资讯完成测试
     * @param $agentId  经纪人id
     * @param $contentType  判断经纪人完成视频或资讯
     * @param $contentId
     * @return bool
     */
    public static function isAgentCompleteBrandContent($agentId , $contentType,$contentId){
        $type = 2;
        if($contentType == 'video'){
            $type = 1;
        }
        $isComplete = BrandAgentCompleteQuiz::where('agent_id',$agentId)
            ->where('content_type',$type)->where('post_id',$contentId)->first();
        if($isComplete){
            return true;
        }
        if($contentType == 'video'){
            $brandVideoInfo = BrandVideo::where('id',$contentId)->first();
            $brandId = $brandVideoInfo['brand_id'];
        }
        else{
            $newInfo = Newss::where('id',$contentId)->first();
            $brandId = $newInfo['relation_id'];
        }
        $agentBrandInfo = AgentBrand::where('agent_id',$agentId)->where('brand_id',$brandId)->where('status',4)->first();
        if($agentBrandInfo){
            return true;
        }
        return false;
    }

    //获取经纪人的真实手机号
    public static function getRealPhone($non_reversible , $source){
        $url = config('system.data_center.hosts') . config('system.data_center.decrypt');
        $datas = ['en_tel'=> $non_reversible , 'platform'=>$source];
        //请求数据中心接口
        $result = json_decode(getHttpDataCenter($url, '', $datas));
        if( empty($result) || !$result->status){
            return "";
        }
        return trim($result->message);
    }


    /**
     * 获取某经纪人的所有上属
     */
    public function getSuperiors($agent_id)
    {
        $agent = self::where('id', $agent_id)->select('register_invite')->first();


        $superiors = [];

        while($agent->register_invite){
            $agent = self::where('non_reversible', $agent->register_invite)->select('id', 'username','non_reversible', 'register_invite')->first();
            $superiors[] = $agent->toArray();
        }


        return $superiors;
    }



    /**
     *  获取某经纪人的直接下属，不包含第二层下属
     * @User    todo  调用的时候传手机号md5值
     * @param $username
     * @return mixed
     */
    public function getDirectlySubordinate($username)
    {
        $subordinates = self::where('register_invite', $username)->where('status',1)->get();

        return $subordinates;
    }

    /**
     * 功能描述：获取某个经纪人的所有好友信息
     *
     * 参数说明：
     * @param $agentId
     *
     * 返回值：
     * @return array
     *
     * 实例：
     * 结果：
     *
     * 作者： shiqy
     * 创作时间：@date 2018/1/30 0030 下午 3:08
     */
    public static function agentAllFriends($agentId){
        $agentFriends = AgentFriendsRelation::where('execute_agent_id',$agentId)->orWhere('relation_agent_id',$agentId)
            ->select('execute_agent_id','relation_agent_id')->get()->toArray();
        $agentFriendIds = [];
        $agentFriendIds = array_pluck($agentFriends , 'execute_agent_id');
        $agentFriendIds = array_merge($agentFriendIds , array_pluck($agentFriends , 'relation_agent_id'));
        $agentFriendIds = array_unique($agentFriendIds);
        $data = [];
        if(!empty($agentFriendIds)){
            $data = Agent::whereIn('id' , $agentFriendIds)->where('status',1)->get()->toArray();
        }
        return $data;
    }





}
