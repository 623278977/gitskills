<?php
/**活动模型
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/5/5
 * Time: 11:10
 */

namespace App\Models\Brand\Entity;

use App\Models\Agent\AgentAchievement;
use App\Models\Agent\BrandContract;
use App\Models\Agent\CommissionLevel;
use App\Models\Brand\BrandContactor;
use App\Models\Brand\BrandVideo;
use App\Models\Config;
use App\Models\Contract\Contract;
use App\Models\User\Favorite;
use \DB, Closure, Input;
use App\Models\Brand\Entity\V020700;
use App\Services\Version\Brand\_v020400;
use App\Models\Video;
use App\Models\Brand\Images;
use App\Models\Activity\Brand as ActivityBrand;
use App\Models\Activity\Entity as Activity;
use App\Models\VideoType;
use App\Models\Agent\AgentBrand as AgentBrands;
use App\Models\Agent\Agent;
use App\Models\User\Entity as User;
use App\Models\Agent\AgentCustomer;
use App\Models\SendOrderQueue\V020800 as SendOrderQueue;
use App\Models\SendInvestor\V020800 as SendInvestor;
use App\Models\Video\Entity\AgentVideo as VideoAgent;
use App\Models\News\Entity\AgentNews as NewsAgent;
use App\Models\Agent\AgentBrand;
use App\Models\Brand\Entity as Brand;

class V020800 extends V020700
{

    public static $instance = null;
    public static function instances()
    {
        if (is_null(self::$instance)) {
            self::$instance = new self;
        }
        return self::$instance;
    }

    /**
     * 列表格式化数据
     * @param $result 要格式化的数据
     * @param $input 带入的参数
     * @param $events
     */
    static public function format($result, $input = [], $events = false)
    {
        $service = new _v020400();
        $data = $result['data'];
        foreach ($result['data'] as $k => $item) {
            $data[$k]['investment_min'] = formatMoney($item['investment_min']);
            $data[$k]['investment_max'] = formatMoney($item['investment_max']);
            $data[$k]['max_percent'] = floatval($item['max_percent']) * 100 .'%';
            $data[$k]['logo'] = getImage($item['logo'], '', '');
            $data[$k]['investment_arrange'] = formatMoney($item['investment_min']) . ' ~ ' . formatMoney($item['investment_max']) . '万';
            $data[$k]['zone_name'] = $service->formatZoneName($item['zone_name']);
            //$item->industry_ids = $this->getBrandIndustry($item->industry_ids);
            if ($item['keywords']) {
                $data[$k]['keywords'] = strpos($item['keywords'], ' ') !== false ? explode(' ', $item['keywords']) : [$item['keywords']];
            } else {
                $data[$k]['keywords'] = [];
            }
            //品牌支持代理方式
            $brand['agency_way'] = Brand::_agentWay($item['agency_way']);
            if($brand['agency_way']['channel'] == 1){
                array_unshift($data[$k]['keywords'] , '渠道加盟');
            }
            if($brand['agency_way']['area'] == 1){
                array_unshift($data[$k]['keywords'] , '品牌加盟');
            }

            //是否代理
            $is_agent = AgentBrands::isAgent($input['agent_id'], $item['id']);
            if (empty($is_agent)) {
                $data[$k]['is_agent'] = '0';
            } else {
                $data[$k]['is_agent'] = $is_agent;
            }
            //佣金额
            $data[$k]['commission'] = number_format($item['max_amount'] ?: '0');

            if ($events) {
                //事件信息
                $data[$k]['events'] = AgentBrands::eventAgent($input['agent_id'], $item['id']);
                //跟进用户统计
                $res = AgentCustomer::agentBrandCount($input['agent_id'], $item['id']);
                //我的成单量
                $data[$k]['my_own_orders'] = $res['my_own_orders'];
                //下级经纪人的成单量
                $data[$k]['my_subordinate_orders'] = $res['my_subordinate_orders'];
                //品牌累计跟单数
                $data[$k]['total_customers'] = $res['total_customers'];
                //品牌当前跟单数
                $data[$k]['now_customers'] = $res['now_customers'];
            } else {
                //获取相关阅读数
                $course = self::readCount($item['id'], $input['agent_id']);
                $data[$k]['unread_num'] = $course['unread_num'];//未阅读数
            }
            $data[$k]['dataCount'] = $result['total'];
        }
        return $data;
    }

    /**
     * 获取课程相关阅读数
     * @User yaokai
     * @param $brand_id 品牌id
     * @param $agent_id 经纪人id
     * @return array
     */
    public static function readCount($brand_id, $agent_id)
    {
        //课程视频数
        $video_ids = BrandVideo::videoIds($brand_id);
        //课程资讯数
        $news_ids = NewsAgent::newsIds($brand_id, false);
        //课程总数
        $num = count($video_ids) + count($news_ids);
        //当前经纪人已阅读数
        $read_num = AgentBrand::ReadCount($agent_id, $brand_id);
        //未阅读数
        $unread_num = $num - $read_num;

        return compact('video_ids', 'news_ids', 'num', 'read_num', 'unread_num');
    }


    /**
     * 详情格式化数据
     */
    static public function detailFormat($data)
    {
        $brand['id'] = $data->id;
        $brand['title'] = $data->name;//名称
        $brand['logo'] = getImage($data->logo, '', '');//名称
        $brand['views'] = $data->click_num;//浏览数
        $brand['favours'] = Favorite::where(['status' => 1, 'model' => 'brand', 'post_id' => $data->id])->count();//收藏数
        $brand['share_num'] = $data->share_num;//转发数
        $brand['slogan'] = $data->slogan;//标语
        $brand['investment_min'] = $data->investment_min;//最低投资额
        $brand['investment_max'] = $data->investment_max;//最高投资额
        $brand['investment_arrange'] = $data->investment_arrange;
        $brand['products'] = $data->products;//主营产品
        $brand['shops_num'] = $data->shops_num;//店铺数量
        $brand['category_name'] = $data->category_name;//分类名称
        $brand['keywords'] = $data->keywords;//关键字（数组）
        $brand['company'] = $data->company;//公司名
        $brand['address'] = $data->address;//公司地址
//        $brand['detail'] = $data->detail;//图文详情
        $brand['detail'] = $data->getOriginal('details');//图文详情
        $brand['league'] = $data->league;//加盟简介
        $brand['prerequisite'] = $data->prerequisite;//加盟条件
        $brand['advantage'] = $data->advantage;//加盟优势
        $brand['share_summary'] = $data->share_summary;
        //佣金额
        $brand['commission'] = number_format($data->max_amount ?: 0);//佣金额
        $brand['max_percent'] = floatval($data->max_percent ?: 0);//佣金比例
        //品牌支持代理方式
        $brand['agency_way'] = $data->agentWay();
        if($brand['agency_way']['channel'] == 1){
            array_unshift($brand['keywords'] , '渠道加盟');
        }
        if($brand['agency_way']['area'] == 1){
            array_unshift($brand['keywords'] , '品牌加盟');
        }

        //轮播图
        $banners = Images::where(['brand_id' => $data->id, 'type' => 'banner'])->select('id', 'src')->get();
        $banners = Images::process($banners);
        $brand['banners'] = $banners;
        $brand['share_content'] = $data->share_summary;
        $images = Images::where(['brand_id' => $data->id, 'type' => 'detail'])->select('id', 'src')->get();//品牌图片(数组)

        foreach ($images as $k => $v) {
            $brand['detail_img'][$k]['id'] = $v['id'];
            $brand['detail_img'][$k]['src'] = getImage($v['src'], '', '');
        }

        //品牌相关的视频
        $video_ids = Video::where('brand_id', $data['id'])->where('agent_status','1')->lists('id')->toArray();
        foreach ($video_ids as $k => $v) {
            $videos[] = Video::singleVideo($v);
        }
        foreach ($videos as $k => $v) {
            $video[$k]['id'] = $v->id;
            $video[$k]['title'] = $v->subject;
            $video[$k]['image'] = $v->image;
            $video[$k]['created_at'] = $v->created_at;
            $video[$k]['summary'] = $v->description;//描述
        }

        //整理
        $res['brand'] = $brand;
        $res['videos'] = $video;
        //品牌相关活动
        $activity = self::BrandActivity($data->id);
        $res['activity'] = $activity;

        return $res;
    }

    /**
     * 申请详情格式化数据
     */
    static public function ApplyDetailFormat($data)
    {
        $brand['id'] = $data->id;
        $brand['title'] = $data->name;//名称
        $brand['logo'] = getImage($data->logo, '', '');//logo
        $brand['slogan'] = $data->slogan;//标语
        $brand['investment_min'] = $data->investment_min;//最低投资额
        $brand['investment_max'] = $data->investment_max;//最高投资额
        $brand['investment_arrange'] = $data->investment_arrange;
        $brand['products'] = $data->products;//主营产品
        $brand['shops_num'] = $data->shops_num;//店铺数量
        $brand['category_name'] = $data->category_name;//分类名称
        $brand['keywords'] = $data->keywords;//关键字（数组）
        $brand['agent_num'] = AgentBrands::getBrandAgentCount($data->id);//该品牌的代理人数
        $brand['commission_des'] = $data->commission_des;//提成说明
        $brand['condition'] = $data->condition;//代理条件


        return $brand;
    }

    /**
     * 无界妈妈获取品牌相关活动
     * @param 品牌id
     * return 品牌相关活动信息
     */
    public static function BrandActivity($brand_id)
    {
        //品牌相关活动id
        $activity_ids = ActivityBrand::select('activity_id')
            ->where('brand_id', $brand_id)
            ->get()
            ->toArray();
        $activity_ids = array_flatten($activity_ids);
        //品牌相关活动信息
        $activity = Activity::with('makers', 'makers.zone')
            ->select('id', 'subject as title', 'begin_time', 'end_time', 'list_img')
            ->whereIn('id', $activity_ids)
//            ->where('end_time', '>=', time())
            ->where('status', 1)
            ->orderBy('begin_time', 'desc')
            ->get()->toArray();

        //活动信息处理
        foreach ($activity as $k => &$v) {
            $v['cities'] = [];
            foreach ($v['makers'] as $key => $value) {
                $zone = array_get($value, 'zone.name');
                $zone = str_replace('市', '', $zone);
                $v['cities'][$key] = $zone;
            }
            //完整图片地址
            $v['list_img'] = getImage($v['list_img'], 'activity', '');
            //活动状态
            if ($v['end_time'] >= time()) {
                $v['can_apply'] = 1;//活动报名中
            } else {
                $v['can_apply'] = 0;//活动结束
            }
            unset($v['makers']);
        }
        return $activity;
    }


    /**
     * 设置待处理的队列数据
     *
     * @param $brand_id 品牌id
     * @param $uid      客户id
     * @author tangjb  todo 这个代码需要优化，目前2000个经纪人需要17s执行，1000个经纪人需要9s执行
     */
    public function setSendQueue($brand_id, $uid)
    {
        $brand= Brand::find($brand_id);
        $contactor_agent_id = BrandContactor::where('brand_id', $brand_id)->value('agent_id');

        if($brand->agent_status){//如果平台已代理
            $is_agent = 1;
            //获取代理了所有经纪人
            $agents = Agent::with('hasOneZone')
                ->whereIn('id', function($query) use ($brand_id){
                    $query->from('agent_brand')->where('status', 4)->where('brand_id', $brand_id)->lists('agent_id');
                })
//                ->where('id','<>', $contactor_agent_id)//不包含商务
                ->get();
        }else{//如果平台未代理  直接发给客服
            $is_agent = 0;
            $agents = Agent::with('hasOneZone')
                ->whereIn('id', function($query){
                    $query->from('agent_service')->where('status', 1)->lists('agent_id');
                })
                ->select('id', 'avatar', 'zone_id')->get();
        }




        $user = User::where('uid', $uid)->first();

        foreach ($agents as $k => $v) {
            $sort = 0;

            //就近原则
            if ($v->zone_id == $user->zone_id) {
                $sort += 100;
            }

            //闲忙原则
            $count = AgentCustomer::where('agent_id', $v->id)->where('uid', $uid)->where('status', 0)
                ->where('created_at', '>', strtotime(date('Y-m-d')))->count();

            $sort += -$count;

            //成交原则 成单率
            $success = Contract::where('status', 1)->where('brand_id', $brand_id)->where('agent_id', $v->id)->count();
            $all = Contract::where('brand_id', $brand_id)->where('agent_id', $v->id)->count();

            $rate = $all == 0 ? 0 : $success / $all;

            $sort += $rate;
            $v->sort = $sort;

            //如果是该品牌的商务或者是内部经纪人就把sort值改成-100,
            if($contactor_agent_id==$v->id || $v->account_type==3){
                $v->sort = -100;
            }
        }
        $agents = $agents->sortByDesc('sort');
        $sorts = array_values(array_unique(array_pluck($agents, 'sort')));


        //写入派单表
        $sendInvestor = SendInvestor::create(
            [
                'uid' => $uid,
                'brand_id' => $brand_id
            ]
        );

        $first = [];
        $data = [];
        $agent_ids = [];
        $time = time();
        //取出最大排序值
        $max_sort = max($sorts);


        foreach ($agents as $k => $v) {
            if ($v->sort == $max_sort || !$is_agent) {
                $first[] = [
                    'agent_id' => $v->id,
                    'uid' => $uid,
                    'brand_id' => $brand_id,
                    'send_investor_id' => $sendInvestor->id,
                    'status' => '1',//已直接发送
                    'send_time' => $time,
                    'created_at' => $time,
                    'updated_at' => $time,
                    'real_send_time' => $time,
//                    'task_id' => '第一批群发，没有task_id',
                ];
                $agent_ids[] = $v->id;

            } else {
                $data[] = [
                    'agent_id' => $v->id,
                    'uid' => $uid,
                    'brand_id' => $brand_id,
                    'send_investor_id' => $sendInvestor->id,
                    'status' => '0',//等待脚本发送
                    'send_time' => (array_search($v->sort, $sorts)) * 30 + $time,
                    'created_at' => $time,
                    'updated_at' => $time,
                    'real_send_time' => '',
                    'task_id' => '',
                ];
            }
        }

        $value = SendOrderQueue::orderFormat($uid,$brand_id);

        //拼入派单id
        $value['id'] = $sendInvestor->id;


        //需要优先发送的用户
        $agent = Agent::whereIn('id',$agent_ids)->get();

        $duration =  Config::where('code', 'duration')->value('value');
        $grab_duration = [date("Y-m-d H:i:s"), date("Y-m-d H:i:s", time()+$duration)];

        //发送透传
        $res = send_trans_and_notice(json_encode(['type'=>'send_order', 'style'=>'json', 'value'=>$value]),
            $agent, $grab_duration, 1);


        $first = array_map(function($k) use($res){
            $k['task_id'] = json_encode($res);
            return $k;
        }, $first);


        //组合数据插入表
        $datas = array_merge($first,$data);

        //写入队列表
        SendOrderQueue::insert($datas);

        return  $sendInvestor->id;
    }


    /**
     * 获取品牌的最大佣金
     *
     * @param $brand_id
     * @param tag表示是否只返回最高佣金的百分比 默认：false不是；
     *
     * @author tangjb
     */
    public function getMaxCommission($brand_id, $tag = false)
    {
        $amount = BrandContract::where('brand_id', $brand_id)
            ->where('is_delete', 0)
            ->where('type', 1)
            ->max('amount');

        $level = CommissionLevel::where('brand_id', $brand_id)->orderBy('id', 'desc')->first();

        // todo 如果标记为真，直接返回佣金的百分比 0103版本处理 zhaoyf 2018-1-23 下午
        if ($tag) {
            if ($level) {
                return $level->scale * 100 .'%';
            } else {
                return '0%';
            }
        }

        if(!$level){
            return 0;
        }

        if ($level->push_money_type == 1) {
            return $level->commission;
        } else {
            return $amount * $level->scale;
        }
    }


    /**
     * 跟据类型 和相关id判断品牌是否下架
     * @User yaokai
     * @param $type contract合同 activity活动
     * @param $post_id 相关id
     * @return $status  0 已下架 1未下架
     */
    public static function brandAgentStatus($type,$post_id)
    {
        switch ($type){
            case 'contract' ://合同
                $brand_id = Contract::where('id',$post_id)->value('brand_id');
                break;
//            case 'activity'://活动
//                $brand_id = ActivityBrand::where('activity_id',$post_id)->value('brand_id');
//                break;
//            case 'activity'://活动
//                $brand_id = ActivityBrand::where('activity_id',$post_id)->value('brand_id');
//                break;

            default:
                $brand_id = '';
                break;
        }

        //品牌状态
        $status = self::where('id',$brand_id)->value('agent_status');

        return $status;
    }

}