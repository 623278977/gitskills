<?php

/*
 * 用户签到推送相关
 */

namespace App\Console\Commands\Agent;

use App\Models\Agent\AgentAchievement;
use App\Models\Agent\AgentCurrencyLog;
use App\Models\Config;
use Illuminate\Console\Command;
use App\Models\Agent\Agent;
use App\Models\SendOrderQueue\V020800 as SendOrderQueue;
use App\Models\User\Entity as User;
use App\Models\Zone\Entity as Zone;
use App\Models\Brand\Entity\V020800 as Brand;
class SendOrder extends Command {

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sendorder';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '派单脚本';


    public function handle()
    {
//        $agent = Agent::where('username', 18268881502)->first();
//
//
//        //发送透传加通知
//        send_transmission(json_encode([
//            'type'  => 'get_score',
//            'style' => 'json',
//            'value' => [
//                'num' => 5,
//            ],
//        ]), $agent, null, true);
//
//        dd(3);

        $duration =  Config::where('code', 'duration')->value('value');
        //查询到了时间的
        SendOrderQueue::where('status', 0)->where('send_time', '<', time()+2)->chunk(1000, function ($queues) use ($duration)  {
            //派单
            foreach($queues as $k=>$v){
                //客户信息
                $customer = User::where('uid', $v->uid)->first();
                if(!$customer){
                    //删除该链接
                    $v->status = 1;
                    $v->save();
                    continue;
                }

                if($customer->realname){
                    $realname = $customer->realname;
                }else{
                    $realname = $customer->nickname;
                }

                $gender = $customer->gender;
                $last_login = date('m/d H:i', $customer->last_login);
                $avatar = getImage($customer->avatar, 'avatar');
                $tags = User::getInstance()->getTags($customer);
                $city = Zone::getCityAndProvince($customer->zone_id);
                $uid = $v->uid;
                //品牌信息
                $brand_id = $v->brand_id;
                $brand = Brand::with('categorys1')->where('id', $v->brand_id)->first();
                if(!$brand){
                    //删除该链接
                    $v->status = 1;
                    $v->save();
                    continue;
                }
                $title = $brand->name;
                $logo = getImage($brand->logo, 'activity', '', 0);
                $category_name = $brand->categorys1->name;
                $max_commission =Brand::instances()->getMaxCommission($v->brand_id);
                $agent = Agent::where('id', $v->agent_id)->first();
                $queue_id = $v->id;
//                $data = compact('uid', 'avatar', 'realname', 'gender', 'last_login','city',
//                    'tags', 'brand_id', 'title', 'category_name', 'max_commission', 'logo', 'queue_id', 'duration');



                //_v010003数据处理  todo 17.11.9
                $res = SendOrderQueue::orderFormat($uid,$brand_id);
                $realname = $res['realname'];
                $zone_name = $res['zone_name'];
                $brand_name = $res['brand_name'];
                $brand_agency_way = $res['brand_agency_way']; //todo 增加品牌的加盟方式 zhaoyf 2018-1-23
                $slogan = $res['slogan'];
                $commission = $res['commission'];
                $difficulty = $res['difficulty'];
                $id = $v->send_investor_id;

                $data = compact('uid', 'avatar', 'realname', 'gender', 'last_login','city',
                    'tags', 'brand_id', 'title', 'category_name', 'max_commission', 'logo', 'queue_id', 'duration',
                    'zone_name','brand_name','slogan','commission','difficulty','id', 'brand_agency_way'
                );

                //把状态改成已派单
                $v->status = 1;

                if($agent){
                    $grab_duration = [date("Y-m-d H:i:s"), date("Y-m-d H:i:s", time()+$duration)];
                    if ($agent->version >= '_v010003'){
                        //发送透传加通知
                        $res = send_trans_and_notice(json_encode(['type'=>'send_order', 'style'=>'json', 'value'=>$data]),
                            $agent, $grab_duration, 1);
                    }else{
                        //发送透传
                        $res = send_transmission(json_encode(['type'=>'send_order', 'style'=>'json', 'value'=>$data]),
                            $agent, $grab_duration, 1);
                    }


                    if('ok'==$res['result']){ //推送成功 把任务id写入
                        $v->task_id = $res['taskId'];
                    }else{
                        $v->remark  = $res['result'];//推送失败，把失败原因写入
                    }

                    $v->real_send_time = time();
                }

                $v->save();
            }
        });

    }

}
