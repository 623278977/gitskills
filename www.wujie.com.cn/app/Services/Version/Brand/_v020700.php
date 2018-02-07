<?php

namespace App\Services\Version\Brand;

use App\Models\Activity\Sign;
use App\Models\Agent\AgentCustomer;
use App\Models\Agent\TraitBaseInfo\RongCloud;
use App\Models\Brand\Consult;
use App\Models\Distribution\Action;
use App\Models\Distribution\Entity as Distribution;
use App\Models\Activity\Brand as ActivityBrand;
use App\Models\Activity\Entity as Activity;
use App\Models\Brand\Tel\V020700 as TelV020700;
use App\Models\Zone\Entity as Zone;
use App\Services\Brand;
use App\Models\Brand\Entity\V020700 as BrandV020700;
use App\Models\Activity\Ticket as ActivityTicket;
use App\Models\Brand\Quiz;
use App\Services\Brand as BrandService;
use App\Models\User\Entity as User;
use App\Exceptions\ExecuteException;
use App\Models\Crm\Customer;


class _v020700 extends _v020502
{
    /**
     * 品牌列表
     */
    public function postLists($param = [])
    {
        $data = parent::postLists($param);
        //品牌描述
        foreach ($data as $k => &$v) {
            if (empty($v->brand_summary)) {
                $v->brand_summary = mb_substr(preg_replace('/\s+/i', '', str_replace('&nbsp;', '', strip_tags($v->details))), 0, 50);
            }
            //删除无用字段的返回
            unset($v->detail, $v->details, $v->summary, $v->introduce);
        }

        return $data;
    }

    /**
     * 品牌x详情
     */
    public function postDetail($data)
    {

        $data = parent::postDetail($data);
        //二维码处理

        //品牌详情二维码
        if (empty($data['brand']['qrcode'])) {
            //活动二维码
            $value = url('webapp/brand/detail/_v020700?id=' . $data['brand']['id'] . '&is_share=1');
            $file_name = unique_id() . '.png';
            $qrcode = img_create($value, $file_name);
            //将活动二维码保存
            BrandV020700::where('id', $data['brand']['id'])->update(['qrcode' => $qrcode]);
            $data['brand']['qrcode'] = getImage($qrcode, '', '');
        }else{
            $data['brand']['qrcode'] = getImage($data['brand']['qrcode'],'','');
        }

        //品牌问答数
        $data['brand']['count_quiz'] = Quiz::countQuiz($data['brand']['id']);

        //品牌电话意向数
        $data['brand']['count_tel'] = Consult::countTel($data['brand']['id']);

        $distribution_id = $data['brand']->distribution_id;
        $zone_id = $data['brand']->zone_id;
        $data['brand']->pzone_name = Zone::pidName($zone_id);//父级地区名
        //返回分享规则
        $data['distribution'] = Action\V020700::getDescribe($distribution_id, 'brand', $data['brand']->id);
        $data['is_distribution'] = Distribution::IsDeadline($data['brand']->distribution_id, $data['brand']->distribution_deadline);//分销是否失效
        //品牌相关活动id
        $activity_ids = ActivityBrand::select('activity_id')
            ->where('brand_id', $data['brand']->id)
            ->get()
            ->toArray();
        $activity_ids = array_flatten($activity_ids);
        //品牌相关活动的二维码
        foreach ($activity_ids as $k => $v) {
            $qrcode = Activity::where('id', $v)->value('qrcode');
            //如果没找到则创建
            if (!$qrcode) {
                //活动二维码
                $value = url('webapp/activity/detail/_v020700?id=' . $v . '&is_share=1');
                $file_name = unique_id() . '.png';
                $qrcode = img_create($value, $file_name);
                //将活动二维码保存
                Activity::where('id', $v)->update(['qrcode' => $qrcode]);
            }
        }

        //品牌相关活动信息
        $activity = Activity::with('makers', 'makers.zone')
            ->select('id', 'subject', 'begin_time', 'end_time', 'list_img', 'detail_img', 'qrcode')
            ->whereIn('id', $activity_ids)
            ->where('end_time', '>=', time())
            ->where('status', 1)
            ->orderBy('begin_time', 'desc')
            ->get()->toArray();

        foreach ($activity as $k => &$v) {
            foreach ($v['makers'] as $key => $value) {
                $zone = array_get($value, 'zone.name');
                $zone = str_replace('市', '', $zone);
                $v['zone_name'][$key] = $zone;
            }
            //门票
            $ticket = ActivityTicket::select('surplus', 'num')
                ->where('activity_id', $v['id'])
                ->where('type', 1)
                ->get();

            //完整二维码地址
            $v['qrcode'] = getImage($v['qrcode'], '', '');
            //默认席位
            $v['surplus'] = 0;//剩余门票席位
            $v['num'] = 0;//总门票数

            foreach ($ticket as $value) {
                $v['surplus'] += $value->surplus;//剩余门票席位
                $v['num'] += $value->num;//总门票数
            }
            //已报名数
//            $v['sign_num'] = Sign::where('activity_id',$v['id'])->count();
            $v['sign_num'] = $v['num'] - $v['surplus'];

            //活动状态
            if ($v['end_time'] >= time()) {
                $v['activity_status'] = 1;//活动报名中
            } else {
                $v['activity_status'] = 0;//活动结束
            }
            unset($v['makers']);
        }
        $data['activity'] = $activity;

        return $data;
    }

    /*
    * 品牌墙
    */
    public function postWall($data)
    {
        //推荐品牌
        $recBrands = BrandV020700::getRecBrand();
        //品牌路演秀
        $showBrands = BrandV020700::getShowBrand($recBrands, $data['page_size'], $data['page']);

        if ($data['page'] > 1) {
            return ['message' => ['showBrands' => $showBrands], 'status' => true];
        }

        return ['message' => ['recBrands' => $recBrands, 'showBrands' => $showBrands], 'status' => true];
    }

    /*
    * 品牌招商详情
    */
    public function postShowDetail($data)
    {
        //品牌信息
        $infos = BrandV020700::getDistributionSimple($data['id']);
        //品牌下视频信息
        $videos = BrandV020700::getVideosByBrand($data['id']);

        return ['message' => ['infos' => $infos, 'videos' => $videos], 'status' => true];
    }

    /**
     * 品牌电话咨询留存   --数据中心版
     * @User tangjb
     * @param $data
     * @return array
     */
    public function postTel($data)
    {
        if (empty($data['brand_id'])) {
            return ['message' => '品牌id是必传参数', 'status' => false];
        }

        if (empty($data['tel']) || !checkMobile($data['tel'])) {
            return ['message' => '未传手机号，或者手机号不合法', 'status' => false];
        }

        $arr['brand_id'] = $data['brand_id'];
        $arr['tel'] = pseudoTel($data['tel']);
        $arr['non_reversible'] = encryptTel($data['tel']);


        \DB::beginTransaction();
        try {
            
            $object = TelV020700::create($arr);
//            $customer = Customer::findOrCreate($data['tel'], ['grade_id' => 4, 'source_id' => 7]);

            $user = User::findOrRegister($data['tel'], '');
            //如果异常返回异常信息
            if (!$user['status']){
                return ['message' => '哎呀! 留存失败,请稍后重试哦！', 'status' => false];
            }
            $uid = $user['user']->uid;

            //再写入洽询表
            $result = Consult::create(
                [
                    'type'        => 'tel',
                    'relation_id' => $object->id,
                    'brand_id'    => $data['brand_id'],
                    'uid'         => $uid,
                ]
            );
            \DB::commit();
        } catch (\Exception $e) {
            \DB::rollBack();
            throw(new ExecuteException('电话咨询留存失败，出现异常' . $e->getMessage()));
        }

        //如果是新注册的用户 并且是线上环境 发送邮件
        if (\App::environment() == 'production') {
            \Mail::raw(
                "无界商圈app一个客户可能有品牌加盟意向，手机号为" . $data['tel'],
                function ($message) use ($data) {
                    $message->to('zhangy@tyrbl.com')->cc('tangjb@tyrbl.com')
                        ->subject("无界商圈app一个客户可能有品牌加盟意向，手机号为" . $data['tel']);
                }
            );
        }

        if (is_object($object)) {
            return ['message' => '留存成功', 'status' => true];
        } else {
            return ['message' => '留存失败', 'status' => false];
        }
    }

    public function postCollect($data)
    {
        $brand = new Brand();
        $result = $brand->collect($data['id'], $data['uid'], $data['type']);

        if ($result) {
            return ['message' => true, 'status' => true];
        } else {
            return ['message' => false, 'status' => false];
        }
    }

    /*
    * 品牌相关问题
    */
    public function postQuestion($param)
    {
        $questions = Quiz::where(['brand_id' => $param['brand_id'], 'status' => 'show'])
            ->with('user')
            ->where('admin_id', '>', 0)
            ->select('quiz', 'answer', 'created_at', 'uid')
            ->orderBy('created_at', 'desc')
            ->skip(($param['page'] - 1) * $param['page_size'])->take($param['page_size'])
            ->get();

        $questions->transform(
            function ($item, $key) {
                is_object($item->user) ? $avatar = $item->user->avatar : $avatar = '';
                $item->avatar = getImage($avatar, 'avatar', '');
                $item->created_at_formart = date('Y-m-d H:i:s', $item->created_at->timestamp);
                unset($item->user);

                return $item;
            }
        );

        $brand = BrandV020700::find($param['brand_id'], ['name']);

        $data['questions'] = $questions;
        $data['brand_name'] = $brand->name;

        return ['data' => $data, 'status' => true];
    }

    /**
     * 对品牌留言  -- 数据中心版
     * @User tangjb
     * @param $param
     * @return array
     */
    public function postMessage($param)
    {
        if (isset($param['share_mark']) && !empty($param['share_mark'])) {
            $share_remark = \Crypt::decrypt($param['share_mark']);
            $md5 = substr($share_remark, 0, 32);
            if ($md5 != md5($_SERVER['HTTP_HOST'])) {
                return ['message' => '分享码有误', 'status' => true];
            }
            $share_remark = explode('&', substr($share_remark, 44));
            $param['source_uid'] = $share_remark[2];
        } else {
            $param['source_uid'] = 0;
        }



        \DB::beginTransaction();
        try {

            if (!$param['uid']) {
                $user = User::findOrRegister($param['mobile'], $param['realname']);

                //如果异常返回异常信息
                if (!$user['status']){
                    return ['message' => '哎呀! 留存失败,请稍后重试哦！', 'status' => false];
                }

                $param['uid'] = $user['user']->uid;
            }

//            $customer = Customer::findOrCreate($param['mobile'], ['grade_id' => 4, 'source_id' => 7, 'name' => $param['realname']]);

            //沉淀
            $mobile = trim($param['mobile']);
            $enTel = encryptTel($mobile);
            depositTel($mobile , $enTel , 'wjsq' , getNationCode($mobile));

            $brand = new BrandService();
            $result = $brand->message(
                $param['id'],
                $param['uid'],
                $param['mobile'],
                $param['realname'],
                $param['zone_id'],
                $param['address'],
                $param['consult'],
                $param['source_uid'],
                array_get($param, 'intent_type', 'intent')
//                $param['reply_time_limit']
            );
            \DB::commit();
        } catch (\Exception $e) {
            \DB::rollBack();
            throw(new ExecuteException('对品牌留言失败，出现异常' . $e->getMessage()));
        }

        //如果是新注册的用户 并且是线上环境 发送邮件
        if (\App::environment() == 'production') {
            \Mail::raw(
                "无界商圈app一个客户可能有品牌加盟意向，手机号为" . $param['mobile'].'，姓名是'.$param['realname'].',留言内容是'.$param['consult'],
                function ($message) use ( $param) {
                    $message->to('zhangy@tyrbl.com')->cc('tangjb@tyrbl.com')
                        ->subject("无界商圈app一个客户可能有品牌加盟意向，手机号为" . $param['mobile'].'，姓名是'.$param['realname']);
                }
            );
        }

        if ($result) {
            return ['data' => '操作成功', 'status' => true];
        } else {
            return ['data' => '操作失败', 'status' => false];
        }
    }

    /**
     * 对品牌提问   --数据中心版
     * @User tangjb
     * @param $param
     * @return array
     */
    public function postAsk($param)
    {
        if (isset($param['share_mark']) && !empty($param['share_mark'])) {
            $share_remark = \Crypt::decrypt($param['share_mark']);
            $md5 = substr($share_remark, 0, 32);
            if ($md5 != md5($_SERVER['HTTP_HOST'])) {
                return ['data' => '分享码有误', 'status' => false];
            }
            $share_remark = explode('&', substr($share_remark, 44));
            $param['source_uid'] = $share_remark[2];
        } else {
            $param['source_uid'] = 0;
        }

        if (!$param['uid'] && empty($param['realname'])) {
            return ['data' => '姓名是必填选项', 'status' => false];
        }

        empty($param['realname']) && $param['realname'] ='';

        empty($param['reply_time_limit']) && $param['reply_time_limit'] = 'all';

        $limits = ['all'=>'没有要求', 'working'=>'上班时间', 'off_working'=>'下班时间'];

        \DB::beginTransaction();
        try {
            if (!$param['uid']) {
                $user = User::findOrRegister($param['mobile'], $param['realname']);

                //如果异常返回异常信息
                if (!$user['status']){
                    return ['message' => '哎呀! 留存失败,请稍后重试哦！', 'status' => false];
                }

                $param['uid'] = $user['user']->uid;
            }

//            $customer = Customer::findOrCreate($param['mobile'], ['grade_id' => 4, 'source_id' => 7, 'name'=>$param['realname']]);

            $brand = new BrandService();
            $result = $brand->ask($param['id'], $param['uid'], $param['content'], $param['reply_time_limit']);
            \DB::commit();
        } catch (\Exception $e) {
            \DB::rollBack();
            throw(new ExecuteException('对品牌提问失败，出现异常' . $e->getMessage()));
        }


        //如果是新注册的用户 并且是线上环境 发送邮件
        if (\App::environment() == 'production') {
            \Mail::raw(
                "无界商圈app一个客户可能有品牌加盟意向，手机号为" . $param['mobile'].'，
                    姓名是'.$param['realname'].',回访时间要求是'.$limits[$param['reply_time_limit']].',提问内容是'.$param['content'],
                function ($message) use ($param) {
                    $message->to('zhangy@tyrbl.com')->cc('tangjb@tyrbl.com')
                        ->subject("无界商圈app一个客户可能有品牌加盟意向，手机号为" . $param['mobile'].'，姓名是'.$param['realname']);
                }
            );
        }


        if ($result) {
            return ['data' => '操作成功', 'status' => true];
        } else {
            return ['data' => '操作失败', 'status' => false];
        }
    }

}