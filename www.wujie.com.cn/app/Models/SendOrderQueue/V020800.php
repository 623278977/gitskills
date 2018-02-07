<?php
/**派单队列表模型
 * Created by PhpStorm.
 * User: tangjb
 * Date: 2017/9/4
 * Time: 11:10
 */

namespace App\Models\SendOrderQueue;


use App\Models\Agent\Agent;
use App\Models\Agent\Invitation;
use App\Models\CommonModel;
use App\Models\User\Entity as User;
use App\Models\Brand\Entity as Brand;
use App\Models\Brand\Entity\V020800 as BrandV020800;
use App\Models\Activity\Entity\V020700 as ActivityV020700;
use App\Models\User\UserFondCate;
use \DB, Closure, Input;


class V020800 extends  CommonModel
{

    protected $table = 'send_order_queue';

    //黑名单
    protected $guarded = [];

    public $timestamps = true;

    protected $dateFormat = 'U';


    //关联经纪人
    public function agent()
    {
        return $this->hasOne(Agent::class,'id','agent_id');
    }

    //关联投资人
    public function user()
    {
        return $this->hasOne(User::class,'uid','uid');
    }

    //关联品牌
    public function brand()
    {
        return $this->hasOne(Brand::class,'id','brand_id');
    }


    /**
     * 咨询列表相关
     * @User yaokai
     * @param $agent_id 经纪人id
     * @param $orderby 排序
     * @param $categorys1_id 分类
     * @param string $page_size 分页
     * @return mixed
     */
    public static function getOrders($agent_id,$orderby,$categorys1_id,$page_size = '10')
    {

        $builder = self::with([
            'user' => function ($query) {
                $query->select('uid', 'nickname', 'realname','gender','zone_id','birth','avatar');
            },
            'brand' => function ($query){
                $query->select('id','name','slogan','categorys1_id', 'agency_way');
            },
            'user.zone' => function ($query){
                $query->select('id','name');
            },

        ]);

        //分类处理
        if(isset($categorys1_id) && !empty($categorys1_id)){
            $builder->whereIn('brand_id',function ($query) use ($categorys1_id){
                $query->from('brand')
                    ->where('categorys1_id',$categorys1_id)
                    ->lists('id');
            });
        }

        // todo 0103需求要求：由原来显示佣金的最高额度等，改成显示最高佣金的百分比（例如：20%）； 这里把原来if (c1.push_money_type=1,max(c1.commission), max(cl.scale*c1.commission)) 改成 直接获取最高佣金百分比例，强制显示佣金比例
        // todo zhaoyf 2018-1-23
        $builder = $builder->select('*',DB::raw('(select max(cl.scale) from lab_commission_level cl LEFT JOIN 
            (SELECT brand_id,max(amount) amount from lab_brand_contract where is_delete=0 and type=1 GROUP BY brand_id) b on b.brand_id = cl.brand_id where cl.brand_id = lab_send_order_queue.brand_id ) AS max_amount'))
                        ->where('agent_id', $agent_id)
                        ->whereIn('status', ['1','-1'])//已派单
                        ->orderBy('status','desc');

        //派单时间排序处理
        if(isset($orderby) && !empty($orderby)){
            if($orderby == 'send_time_desc'){
                $builder = $builder->orderBy('send_time','desc');
            }elseif($orderby == 'send_time_asc'){
                $builder = $builder->orderBy('send_time','asc');
            }//佣金排序
            elseif ($orderby == 'commission_asc'){//提成金额低到高
                $builder = $builder->orderBy('max_amount','asc');
            }elseif ($orderby == 'commission_desc'){//提成金额高到低
                $builder = $builder->orderBy('max_amount','desc');
            }
        }else{
            $builder->orderBy('id','desc');

        }

        $orders = $builder->paginate($page_size)->toArray();

        //数据处理
        foreach ($orders['data'] as $k => $v) {
            $data[$k]['id']         = $v['send_investor_id']; //派单id
            $data[$k]['uid']        = $v['uid'];//用户id
            $data[$k]['status']     = $v['status'];//用户id
            $data[$k]['avatar']     = getImage($v['user']['avatar'], 'avatar', '');//用户头像
            $data[$k]['send_time']  = self::formatTime($v['send_time']);//派单时间
            $data[$k]['gender']     = $v['user']['gender'];//用户性别
            $data[$k]['realname']   = $v['user']['realname'] ?: $v['user']['nickname'];//优先展示真实姓名
            $data[$k]['birth']      = User::getInstance()->getAgeTag(substr($v['user']['birth'], 0, 4));
            $data[$k]['zone_name']  = $v['user']['zone']['name'];//地区
            $data[$k]['brand_id']   = $v['brand_id'];//品牌id
            $data[$k]['brand_name'] = $v['brand']['name'];//品牌名称
            $data[$k]['brand_agency_way'] = Brand::$_AGENCY_WAY[$v['brand']['agency_way']];// todo 新增：品牌的加盟方式 zhaoyf 2018-1-23 下午
            $data[$k]['slogan']     = $v['brand']['slogan'];//品牌slogan
            $data[$k]['categorys1_id'] = $v['brand']['categorys1_id'];//品牌一级分类id
            //$data[$k]['commission'] = number_format($v['max_amount']);   //品牌名称
            $data[$k]['commission'] = $v['max_amount'] * 100 .'%' ?: '0%'; //品牌最高佣金百分比 zhaoyf 2018-1-23
            $data[$k]['be_industry']= self::userFondCate($v['uid']);//感兴趣行业
            $data[$k]['difficulty'] = '简单';//难度系数 Todo 暂不处理
        }

        return $data?:[];
    }

    /**
     * 咨询列表详情
     * @User yaokai
     * @param $id 任务id
     * @return string
     */
    public static function getOrder($agent_id,$send_investor_id)
    {
        $order = self::with([
            'user' => function ($query) {
                $query->select('uid', 'nickname', 'realname','gender','zone_id','birth','avatar');
            },
            'brand' => function ($query){
                $query->select('id','name','slogan','categorys1_id', 'agency_way');
            },
            'user.zone' => function ($query){
                $query->select('id','name');
            },

        ])
        ->where('agent_id', $agent_id)
        ->where('send_investor_id', $send_investor_id)
//        ->where('status', '1')//已派单
        ->first();

//        dd($order);
        if ($order){
            $data['id']         = $order['id']; //任务id
            $data['send_investor_id']= $order['send_investor_id']; //派单id
            $data['uid']        = $order['uid'];//用户id
            $data['avatar']     = getImage($order['user']['avatar'], 'avatar', '');//用户头像
            $data['send_time']  = self::formatTime($order['send_time']);//派单时间
            $data['gender']     = $order['user']['gender'];//用户性别
            $data['realname']   = $order['user']['realname'] ?: $order['user']['nickname'];//优先展示真实姓名
            $data['birth']      = User::getInstance()->getAgeTag(substr($order['user']['birth'], 0, 4));//年代
            $data['zone_name']  = $order['user']['zone']['name'];//地区
            $data['brand_id']   = $order['brand_id'];//品牌id
            $data['brand_name'] = $order['brand']['name'];//品牌名称
            $data['brand_agency_way'] = Brand::$_AGENCY_WAY[$order['brand']['agency_way']]; // todo 新增：品牌的加盟方式 zhaoyf 2018-1-23 下午
            $data['slogan']     = $order['brand']['slogan'];//品牌slogan
            $data['categorys1_id'] = $order['brand']['categorys1_id'];//品牌一级分类id
            //$data['commission'] = number_format(BrandV020800::instances()->getMaxCommission($order['brand_id']));//品牌佣金
            $data['commission'] = BrandV020800::instances()->getMaxCommission($order['brand_id'], true);//品牌佣金显示百分比例
            $data['be_industry']= self::userFondCate($order['uid']);//感兴趣行业
            $data['difficulty'] = '简单';//难度系数 Todo 暂不处理

            $data['signs'] = ActivityV020700::userActivitys($order['uid']);//参加活动数

            $data['invitations'] = Invitation::userInvitations($order['uid']);//接受考察邀请数
        }

        return $data?:'';


    }


    /**
     * 根据uid获取用户感兴趣的行业
     * @User yaokai
     * @param $uid
     * @return string
     */
    public static function userFondCate($uid)
    {
        $categorys = UserFondCate::with(['categorys' => function ($query){
            $query->select('id','name');
        }])
            ->where('uid',$uid)
            ->get()
            ->toArray();

       $implode = collect($categorys)->implode('categorys.name', ' · ');

        return $implode;
    }


    /**
     * 展示时间处理
     * @User yaokai
     * @param $time
     * @return false|string
     */
    public static function formatTime($time)
    {
        $time = $time?: time();

        //今日凌晨时间戳
        $todaytime = strtotime(date('Y-m-d'));

        //昨日凌晨时间戳
        $yesterday = strtotime("-1 day", strtotime(date('Y-m-d')));

        if ($time > $todaytime) {
            return '今天';
        } elseif ($time > $yesterday && $time < $todaytime) {
            return '昨天';
        } else {
            return date('m月d日',$time);
        }

    }


    public static function orderFormat($uid, $brand_id)
    {
        $user = User::with([
            'zone' => function ($query) {
                $query->select('id', 'name', 'upid');
            },
            'zone.pzone' => function ($query) {
                $query->select('id', 'name');
            }])
            ->select('uid', 'zone_id', 'nickname', 'realname')
            ->where('uid', $uid)
            ->first();

        // todo change：新的需求返回加盟方式，这里查询时，多返回了加盟方式的返回
        // todo zhaoyf 2018-1-23 下午
        $brand = Brand::select('id', 'name', 'slogan', 'agency_way')
            ->where('id', $brand_id)
            ->first();

        //地区处理
        $zone2_name = str_replace('市', '', $user->zone?$user->zone->name:'');
        $zone1_name = str_replace('省', '', $user->zone?($user->zone->pzone?$user->zone->pzone->name:''):'');
        if ($zone2_name && $zone1_name){
            $zone_name = $zone1_name . ' ' . $zone2_name;
        }elseif ($zone2_name){
            $zone_name = $zone2_name;
        }else{
            $zone_name = '';
        }


        //返回数据处理
        $uid = $uid;//
        $brand_id = $brand_id;//
        $realname = $user->realname ?: $user->nickname;//优先展示真实姓名

        $brand_name       = $brand->name;
        $slogan           = $brand->slogan;
//        $brand_agency_way = Brand::$_AGENCY_WAY[$brand->agency_way]; //增加返回品牌加盟方式 zhaoyf 2018-1-23 下午
        $brand_agency_way = array_get(Brand::$_AGENCY_WAY, $brand->agency_way, '');
        $commission = BrandV020800::instances()->getMaxCommission($brand_id, true); //佣金显示方式改成显示最高百分比例 zhaoyf 2018-1-23 下午
        $difficulty = '简单'; //难度系数 Todo 暂不处理

        //todo 返回品牌的加盟方式 zhaoyf 2018-1-23 下午
        return compact('uid','brand_id','realname','zone_name','brand_name','slogan','commission','difficulty', 'brand_agency_way');
    }
    

}