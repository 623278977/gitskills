<?php

namespace App\Models\Agent;

use Illuminate\Database\Eloquent\Model;
use App\Models\Agent\Agent;
use App\Models\Zone\Entity as Zone;
use App\Models\Brand\Entity as Brand;
use DB, Input;

class AgentBrand extends Model
{
    protected $table = 'agent_brand';
    protected $fillable = ['agent_id', 'brand_id', 'status', 'created_at', 'updated_at'];
    protected $dateFormat = 'U';


    public function agent_brand_log()
    {
        return $this->hasMany('App\Models\Agent\AgentBrandLog', 'agent_brand_id', 'id');
    }

    public function belongsToAgent(){
        return $this->belongsTo(Agent::class ,'agent_id','id');
    }

    //关联经纪人
    public function agent(){
        return $this->belongsTo(Agent::class ,'agent_id','id');
    }

    //关联 品牌
    public function brand(){
        return $this->belongsTo(Brand::class,'brand_id','id');
    }


    //列表
    public static function selectList($builder)
    {
        $ret = $builder->select('id',
            'slogan',
            'logo',
            'name as title',
            'investment_min',
            'investment_max',
            'keywords',
            'is_recommend',
            'slogan',
            'agency_way',
            DB::raw('(select concat_ws(",",id,name) from lab_zone WHERE lab_zone.id = lab_brand.zone_id) as zone_name'),
            DB::raw('(select if(GROUP_CONCAT(activity_id), GROUP_CONCAT(activity_id), 0) from lab_activity_brand
            WHERE lab_activity_brand.brand_id = lab_brand.id) as activity_id'),
            DB::raw('(select name from lab_categorys as c where c.id = lab_brand.categorys1_id ) AS category_name'),
            DB::raw('(select IF(cl.push_money_type=1, max(cl.commission), max(cl.scale*b.amount)) from lab_commission_level cl LEFT JOIN
            (SELECT brand_id,max(amount) amount from lab_brand_contract where is_delete=0 and type=1 GROUP BY brand_id) b on b.brand_id = cl.brand_id where cl.brand_id = lab_brand.id ) AS max_amount'),
            DB::raw('(select scale from lab_commission_level as cl where cl.brand_id = lab_brand.id order by scale desc limit 0,1) AS max_percent'),
            DB::raw('(select count(1) from lab_contract lc where lc.brand_id = lab_brand.id) AS contract_num')
        );

        return $ret;
    }

    /**
     * 获取经纪人是否代理品牌
     * @param $agent_id 经纪人id
     * @param $brand_id 品牌id
     * return 0:未申请，-2拒绝 -1失去代理权 1：已申请，2:已参与培训，3:通过测试，4:获得代理权
     */
    static public function isAgent($agent_id, $brand_id)
    {
        $is_agent = self::where('agent_id', $agent_id)
            ->where('brand_id', $brand_id)
            ->where('status','!=','-2')//拒绝
            ->value('status');
        return $is_agent;
    }

    //经纪人代理品牌，只排除被拒绝的状态
    public static function agentStatus(){
        return self::where('status','<>',-2);
    }

    /**
     * 获取经纪人代理品牌各种事件点
     * @param $agent_id 经纪人id
     * @param $brand_id 品牌id
     * @param $action 动作  默认4 获取代理
     * return
     */
    static public function eventAgent($agent_id, $brand_id, $action = 4)
    {
        //获得品牌代理时间
        $created_at = self::with(['agent_brand_log' => function ($query) use ($action) {
            $query->where('action', $action)->select('agent_brand_id', 'created_at as agent_time');
        }])
            ->where('agent_id', $agent_id)
            ->where('brand_id', $brand_id)
            ->first()->toArray();
        $agent_created_at = array_get($created_at, 'agent_brand_log.0.agent_time');

        //获得第一笔派单时间
        $customer_time1 = AgentCustomerLog::with(
            ['user'=>function($query){
                $query->select('uid','username','nickname','realname','zone_id');
            },'user.zone' => function ($query) {
                $query->select('id', 'name');
            }])
            ->where('brand_id', $brand_id)
            ->where('agent_id', $agent_id)
            ->where('action', '0')
            ->first();


        //获得第一笔成单时间
        $customer_time2 = AgentCustomerLog::where('brand_id', $brand_id)
            ->where('agent_id', $agent_id)
            ->where('action', '13')
            ->value('created_at');

        //数据整理
        $ret = [];
        if ($agent_created_at) {
            $a['type'] = '1';
            $a['time'] = $agent_created_at;
            $a['summary'] = '获得品牌代理权';
            array_push($ret, $a);
        }
        if ($customer_time1) {
            $b['type'] = '2';
            $b['time'] = $customer_time1->created_at;
            $b['name'] = $customer_time1->user?($customer_time1->user->realname?$customer_time1->user->realname:$customer_time1->user->nickname):'';
            //地区处理
            $zone2_name = str_replace('市', '', $customer_time1->user->zone->name);
            $zone1_name = str_replace('省', '', Zone::pidName($customer_time1->user->zone_id));
            //没有地区返回为空
            if($zone2_name){
                $b['zone_name'] = $zone1_name . ' ' . $zone2_name;
            }else{
                $b['zone_name'] = false;
            }
            $b['summary'] = '获得第一笔派单';
            array_push($ret, $b);
        }
        if ($customer_time2) {
            $c['type'] = '3';
            $c['time'] = $customer_time2;
            $c['summary'] = '获得第一笔佣金提成';
            array_push($ret, $c);

        }
        return $ret;
    }

    /**
     * 获取经纪人代理品牌课程阅读状态
     * @param $agent_id 经纪人id
     * @param $brand_id 品牌id
     * @param $post_id 关联id
     * @param $action 动作  2
     * return created_at
     */
    static public function isRead($agent_id, $brand_id, $post_id, $type)
    {
        $id = self::where('agent_id', $agent_id)
            ->where('brand_id', $brand_id)
            ->where('status','!=','-2')//排除拒绝的记录
            ->value('id');
        $result = AgentBrandLog::where('action', 2)
            ->where('type', $type)
            ->where('post_id', $post_id)
            ->where('agent_brand_id', $id)
            ->value('id');
        if ($result) {
            return 1;//已读
        }
        return 0;//未读
    }

    /**
     * 获取经纪人代理品牌课程阅读数量
     * @param $agent_id 经纪人id
     * @param $brand_id 品牌id
     * return $count 记录条数
     */
    static public function ReadCount($agent_id, $brand_id)
    {
        $id = self::where('agent_id', $agent_id)
            ->where('status','!=','-2')//排除拒绝的记录
            ->where('brand_id', $brand_id)
            ->value('id');
        $count = AgentBrandLog::where('action', 2)
            ->where('agent_brand_id', $id)
            ->count();

        return $count;
    }

    /**
     * 获取经纪人代理品牌记录的id
     * @param $agent_id 经纪人id
     * @param $brand_id 品牌id
     * return $id
     */
    public static function getABId($agent_id, $brand_id)
    {
        $id = self::where('agent_id', $agent_id)
            ->where('status','!=','-2')//排除拒绝的记录
            ->where('brand_id', $brand_id)
            ->value('id');
        return $id;
    }

    /**
     * 获取经纪人代理所有的品牌的id
     * @param $agent_id 经纪人id
     * @param $type 已获得代理品牌agent_brand 申请代理中apply_brands
     * return $ids array
     */
    public static function getAgentBrandId($agent_id, $type = 'agent_brand')
    {
        //已获得的代理品牌id
        if ($type == 'agent_brand') {
            $ids = self::where('agent_id', $agent_id)
                ->where('status', '4')
                ->lists('brand_id')->toArray();
            //申请代理中的品牌id
        } else {
            $ids = self::where('agent_id', $agent_id)
                ->whereIn('status', ['1', '2', '3'])
                ->lists('brand_id')->toArray();
        }
        return $ids;
    }

    /**
     * 获取品牌有多少经纪人代理
     * @param $brand_id
     * @return $count
     */
    public static function getBrandAgentCount($brand_id)
    {
        $count = self::where('brand_id', $brand_id)
            ->where('status','4')//已完成
            ->count();

        return $count;
    }


    /*
     * 获取该品牌下当前在线经纪人总数
     * */
    public static function getAgentBrandOnLineCount($brandId){
        $count = self::with(['belongsToAgent' => function($query) {
            $query->where('is_online',1);
        }])
        ->where('brand_id',$brandId)
        ->where('status',4)
        ->count();
        return $count;
    }

}