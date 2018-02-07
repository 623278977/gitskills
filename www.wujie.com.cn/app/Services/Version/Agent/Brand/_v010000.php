<?php

namespace App\Services\Version\Agent\Brand;

use App\Models\Agent\AgentAchievementLog;
use App\Models\Agent\Contract;
use App\Services\Version\VersionSelect;
use App\Models\Agent\AgentBrand;
use App\Models\Agent\Agent;
use App\Models\Agent\AgentBrandLog;
use DB, Input;
use App\Models\Brand\Entity as BrandModel;
use App\Models\Brand\Entity\V020800 as BrandAgent;
use App\Models\Video\Entity\AgentVideo as VideoAgent;
use App\Models\News\Entity\AgentNews as NewsAgent;
use App\Models\Agent\AgentBrand as AgentBtandModel;
use App\Models\Agent\AgentCustomer;

class _v010000 extends VersionSelect
{
    /**
     * 作用:获取品牌详情
     * 参数:$id 品牌id  $uid  用户id
     *
     * 返回值:
     */
    public function postList($input = [])
    {
        $page_size = Input::input('page_size', 10);

        $builder = BrandModel::where('agent_status', '1')->where('status', 'enable');//经纪人端只显示agent_status为1的品牌

        $builder = AgentBrand::selectList($builder);

        //条件搜索
        $builder = BrandModel::brandList($builder, $input);

        //根据结果构建分页结果
        $result  = paginator($builder);

        //格式化数据
        $data = BrandAgent::format($result, $input);

        return ['message' => $data, 'status' => true];
    }


    /**
     * 品牌详情
     */
    public function postDetail($input = [])
    {
        //获取数据
        $result = BrandModel::single($input['id'], 1);
//        dd($result->toArray());
        //格式化数据
        $data = BrandAgent::detailFormat($result);

        //代理状态
        $data['process_status'] = AgentBtandModel::isAgent($input['agent_id'], $input['id']) ?: '0';
        //实名状态
        $data['identity_card'] = Agent::where('id',$input['agent_id'])->value('identity_card');

        //申请代理增加返回相关信息
        if ($data['process_status'] == '1' || $data['process_status'] == '2' || $data['process_status'] == '3') {
            //课程视频
            $agent_video = VideoAgent::detailFormat($input['id'], $input['agent_id']);
            $data['agent_videos'] = $agent_video;
            //课程资讯
            $agent_news = NewsAgent::detailFormat($input['id'], $input['agent_id']);
            $data['agent_news'] = $agent_news;

            //获得代理权，增加返回相关代理信息
        } elseif ($data['process_status'] == '4') {
            //课程视频
            $agent_video = VideoAgent::detailFormat($input['id'], $input['agent_id']);
            $data['agent_videos'] = $agent_video;
            //课程资讯
            $agent_news = NewsAgent::detailFormat($input['id'], $input['agent_id']);
            $data['agent_news'] = $agent_news;
            //客户信息
            $info = AgentCustomer::CustomerInfo($input['agent_id'], $input['id']);
            $data['following_customers'] = $info['following_customers'] ?: [];//跟进客户
            $data['success_customers'] = $info['success_customers'] ?: [];//成功客户
            //商务代表
//            $data['contactor']['name'] = isset($result->contactor->agent->realname) ? $result->contactor->agent->realname : '';
            $data['contactor']['name'] = $result->contactor ? $result->contactor->out_surname .' '.$result->contactor->out_position: '';
            $data['contactor']['tel'] = $result->contactor ? ($result->contactor->non_reversible ? getRealTel($result->contactor->non_reversible , 'agent') : '') : '';
            //事件信息
            $data['events'] = AgentBtandModel::eventAgent($input['agent_id'], $input['id'], $data['process_status']);
            //跟进用户统计
            $res = AgentCustomer::agentBrandCount($input['agent_id'], $input['id']);
            //我的成单量
            $data['my_own_orders'] = $res['my_own_orders'];
            //下级经纪人的成单量
            $data['my_subordinate_orders'] = $res['my_subordinate_orders'];
            //品牌累计跟单数
            $data['total_customers'] = $res['total_customers'];
            //品牌当前跟单数
            $data['now_customers'] = $res['now_customers'];
        }

        return ['message' => $data, 'status' => true];
    }


    /**
     * 品牌申请页面详情
     */
    public function postApplyDetail($input = [])
    {
        //获取数据
        $result = BrandModel::single($input['brand_id'], 1);
//        dd($data->toArray());
        //格式化数据
        $data = BrandAgent::applyDetailFormat($result);
        return ['message' => $data, 'status' => true];
    }


    /**
     * 申请代理品牌
     */
    public function postApply($input = [])
    {
        //判断是否已经申请
        $is_agent = AgentBrand::isAgent($input['agent_id'], $input['brand_id']);

        if ($is_agent) {
            return ['message' => '已申请,请勿重复申请！', 'status' => false];
        } else {
            DB::transaction(function () use ($input) {
                //创建申请记录
                $agent = AgentBrand::create([
                    'agent_id' => $input['agent_id'],
                    'brand_id' => $input['brand_id'],
                    'status' => '1',//申请中
                ]);
                //写入申请日志
                $log = AgentBrandLog::create([
                    'agent_brand_id' => $agent->id,
                    'action' => '1',//申请
                    'created_at' => time(),
                ]);
            });
            return ['message' => '申请成功', 'status' => true];
        }
        return ['message' => '异常，请稍后重试！', 'status' => false];
    }


    /**
     * 品牌跟进中的客户
     * @param array $input
     * @return array|string
     */
    public function postBrandCustomer($input = [])
    {
        //客户信息
        $info = AgentCustomer::ContractInfo($input['agent_id'], $input['contract_id']);
        $data['following_customers'] = $info['following_customers'] ?: [];
        $data['brand_name'] = $info['brand_name'] ?: [];

        return ['message' => $data, 'status' => true];
    }


    /**
     * 申请代理品牌进度跟进
     */
    public function postApplyStatus($input = [])
    {
        $type = $input['type'] ?: 'none';
        $post_id = $input['post_id'] ?: '0';
        if (empty($input['brand_id'])) {
            return ['message' => '缺少品牌id', 'status' => false];
        } else {
            $status = BrandModel::where('id', $input['brand_id'])->value('agent_status');
            if (!$status) {
                return ['message' => '异常，该品牌不存在，或者已经下架', 'status' => false];
            }
        }
        if (empty($input['agent_id'])) {
            return ['message' => '缺少经纪人id', 'status' => false];
        } else {
            $agent = Agent::where('id', $input['agent_id'])->value('status');
            if (!$agent || $agent == '-1') {
                return ['message' => '经纪人不存在！', 'status' => false];
            }
        }
        //判断是否代理
        $is_agent = AgentBrand::isAgent($input['agent_id'], $input['brand_id']);
        if (!$is_agent || $is_agent == '-1') {
            return ['message' => '经纪人未代理该品牌！', 'status' => false];
        }

        //判断是否学习完成
        $course = BrandAgent::readCount($input['brand_id'], $input['agent_id']);
        $video_ids = $course['video_ids'];//课程视频数
        $news_ids = $course['news_ids'];//课程资讯数
        $num = $course['num'];//课程总数
        $read_num = $course['read_num'];//当前经纪人已阅读数
        //关联判断
        if (in_array($type, ['video', 'news'])) {
            if ($post_id == '0') {
                return ['message' => '课程关联id异常！', 'status' => false];
            } else {
                switch ($type) {
                    case 'video':
                        if (!in_array($post_id, $video_ids)) {
                            return ['message' => '课程关联id异常!', 'status' => false];
                        }
                        break;
                    case'news':
                        if (!in_array($post_id, $news_ids)) {
                            return ['message' => '课程关联id异常!', 'status' => false];
                        }
                        break;
                    default:
                        break;
                }
            }
        }

        DB::transaction(function () use ($input, $num) {
            //已有记录不需要添加
            $is_read = AgentBrand::isRead($input['agent_id'], $input['brand_id'], $input['post_id'], $input['type']);
            if (!$is_read) {
                //经纪人代理品牌id
                $agent_brand_id = AgentBrand::getABId($input['agent_id'], $input['brand_id']);
                //写入申请日志
                $log = AgentBrandLog::create([
                    'agent_brand_id' => $agent_brand_id,
                    'action' => '2',//学习中
                    'created_at' => time(),
                    'post_id' => $input['post_id'],
                    'type' => $input['type'],
                ]);
                //写完日志判断是否已全读//当前经纪人已阅读数
                $read_num = AgentBrand::ReadCount($input['agent_id'], $input['brand_id']);

                //当前代理状态
                $status = AgentBrand::isAgent($input['agent_id'],$input['brand_id']);

                //如果已代理不需要修改状态
                if ($status != '4'){
                    //全读则修改状态
                    if ($num == $read_num) {
                        //修改申请记录状态
                        $agent = AgentBrand::where('agent_id', $input['agent_id'])
                            ->where('brand_id', $input['brand_id'])
                            ->where('status','!=','-2')//不等于拒绝
                            ->update(['status' => '3']);
                        //经纪人代理品牌id
                        $agent_id = AgentBrand::getABId($input['agent_id'], $input['brand_id']);

                        //写入申请日志
                        $log = AgentBrandLog::create([
                            'agent_brand_id' => $agent_id,
                            'action' => '3',//等待审核
                            'created_at' => time(),
                        ]);
                    }else{//修改申请记录状态
                        $agent = AgentBrand::where('agent_id', $input['agent_id'])
                            ->where('brand_id', $input['brand_id'])
                            ->where('status','!=','-2')//不等于拒绝
                            ->update(['status' => '2']);//学习中
                    }
                }
            }
            return ['message' => '操作成功', 'status' => true];
        });
//        return ['message' => '操作异常，请重试', 'status' => false];
        return ['message' => '操作成功', 'status' => true];
    }


    /**
     * 品牌提成详情  --数据中心版
     *
     * @param Request $request
     * @param null $version
     * @return string
     * @author tangjb
     */
    public function postCommission($data)
    {
        $contract = Contract::where('id', $data['contract_id'])->first();

        $log = AgentAchievementLog::with('contract.brand', 'contract.user')
            ->where('contract_id', $data['contract_id'])
//            ->where('agent_id', $contract->agent_id)
            ->first();


        $title = $log->contract->brand->name;
        $realname = $log->contract->user->realname ? $log->contract->user->realname : $log->contract->user->nickname;
        $username = $log->contract->user->username;
        $commission = $log->commission;
        $success_time = $log->contract->confirm_time;


        $data = compact('title', 'realname', 'username', 'commission', 'success_time');


        return ['message' => $data, 'status' => true];
    }

}