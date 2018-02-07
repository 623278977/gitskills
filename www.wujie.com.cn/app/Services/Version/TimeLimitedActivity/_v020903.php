<?php
namespace App\Services\Version\TimeLimitedActivity;

use App\Models\RedPacket\RedPacket;
use App\Models\RedPacket\RedPacketPerson;
use App\Models\ScoreLog;
use App\Models\User\Lottery;
use App\Services\Version\VersionSelect;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use App\Models\User\Entity as User;
use Illuminate\Support\Facades\DB;

class _v020903 extends VersionSelect
{
    public function postNewYearLottery($param)
    {
//        if(time()<strtotime('2018-02-13 0:00:00')){
//            return ['message'=>'活动还未开始', 'status'=>false];
//        }
//        if(time()>strtotime('2018-02-23 23:59:59')){
//            return ['message'=>'活动已经结束，感谢您的关注', 'status'=>false];
//        }
        $uid=$param['uid'];
        if(empty($uid)){
            return ['message'=>'缺少uid','status'=>false];
        }
        //抽奖次数
        Cache::forget('lottery_num'.$uid);
        if(!Cache::has('lottery_num'.$uid)){
            Cache::add('lottery_num'.$uid, 100, Carbon::today()->endOfDay());
        }
        $num=Cache::get('lottery_num'.$uid);
        return ['message'=>$num, 'status'=>true];
    }

    public function postNewYearLotteryResult($param)
    {
//        if(time()<strtotime('2018-02-13 0:00:00')){
//            return ['message'=>'活动还未开始', 'status'=>false];
//        }
//        if(time()>strtotime('2018-02-23 23:59:59')){
//            return ['message'=>'活动已经结束，感谢您的关注', 'status'=>false];
//        }
        $uid=$param['uid'];

        if(empty($uid)){
            return ['message'=>'缺少uid','status'=>false];
        }
        if(!Cache::has('lottery_num'.$uid)){
            Cache::add('lottery_num'.$uid, 3, Carbon::today()->endOfDay());
        }
        if($num=Cache::get('lottery_num'.$uid)<1){
            return ['message'=>'抽奖次数已用完','status'=>false];
        }
        if($score=User::where('uid',$uid)->value('score')<100){
            return ['message'=>'积分不足，邀请注册可获得积分','status'=>false];
        }
        $result=$this->lotteryResult();
              DB::beginTransaction();
        try{
//            User::where('uid',$uid)->decrement('score',100);
            ScoreLog::add($uid, 100, 'new_year_lottery', '春节活动抽奖使用', -1);
            Cache::decrement('lottery_num'.$uid,1);
            switch ($result){
                case 1:
                    Lottery::create([

                            'uid'   => $param['uid'],
                            'name'  => array_get(Lottery::$_NEW_YEAR_REWARD,$result),
                            'type'  => 'null',
                            'spend' => 100,
                            'unit'  => 'score',
                            'delivery'=>'send',
                            'delivery_at'=>time(),
                            'activity' => 1
                    ]);
                    break;
                case 2:
                    User::where('uid',$uid)->increment('score',100);
                    ScoreLog::add($uid, 100, 'new_year_lottery', '春节活动抽奖中奖');
                    Lottery::create([
                        'uid'   => $param['uid'],
                        'name'  => array_get(Lottery::$_NEW_YEAR_REWARD,$result),
                        'type'  => 'null',
                        'spend' => 100,
                        'unit'  => 'score',
                        'delivery'=>'send',
                        'delivery_at'=>time(),
                        'activity' => 1,
                        'remark' => '春节活动抽奖'
                    ]);
                    break;
                case 3:
                    $red_packet=RedPacket::where('redeem_code','new-year-lottery')->first();
                    RedPacketPerson::create([
                        'red_packet_id'=>$red_packet->id,
                        'receiver_id'=>$uid,
                        'expire_at'=>$red_packet->expire_at,
                        'type'=>$red_packet->type,
                        'amount'=>$red_packet->amount,
                        'gain_source'=>5
                    ]);
                    Lottery::create([
                        'uid'   => $param['uid'],
                        'name'  => array_get(Lottery::$_NEW_YEAR_REWARD,$result),
                        'type'  => 'null',
                        'spend' => 100,
                        'unit'  => 'score',
                        'delivery'=>'send',
                        'delivery_at'=>time(),
                        'activity' => 1
                    ]);
                    break;
                case 4:
                    $lottery=Lottery::create([
                        'uid'   => $param['uid'],
                        'name'  => array_get(Lottery::$_NEW_YEAR_REWARD,$result),
                        'type'  => 'null',
                        'spend' => 100,
                        'unit'  => 'score',
                        'delivery'=>'wait',
                        'activity' => 1
                    ]);
                    $data['lottery_id']=$lottery->id;
                    break;
                case 5:
                    $lottery=Lottery::create([
                        'uid'   => $param['uid'],
                        'name'  => array_get(Lottery::$_NEW_YEAR_REWARD,$result),
                        'type'  => 'null',
                        'spend' => 100,
                        'unit'  => 'score',
                        'delivery'=>'wait',
                        'activity' => 1
                    ]);
                    $data['lottery_id']=$lottery->id;
                    break;
                default:
                    break;

            }
        DB::commit();
        }catch (\Exception $e){
            \DB::rollBack();
            throw(new \Exception('操作失败' . $e->getMessage()));
        }
        $data['num']=Cache::get('lottery_num'.$uid);
        $data['result']=$result;
        return ['message'=>$data,'status'=>true];
    }
    //实物奖品领奖信息
    public function postUserInfo($param)
    {
        $lottery_id=$param['lottery_id'];
        $username=trim($param['username']);
        $address=trim($param['address']);
        if(!$lottery_id){
            return ['message'=>'缺少获奖信息','status'=>false];
        }
        if(empty($param['username'])){
            return['message'=>'缺少手机号','status'=>false];
        }
        if(empty($param['address'])){
            return ['message'=>'缺少地址', 'status'=>false];
        }
        $non_reversible=encryptTel($username);
        depositTel($username,$non_reversible,'wjsq');
        $result=Lottery::where('id',$param['lottery_id'])->update([
            'address'=>$address,
            'non_reversible'=>$non_reversible
        ]);
        if(!$result){
            return ['message'=>'操作失败', 'status'=>false];
        }
        return ['message'=>'操作成功', 'status' => true];
    }

    //抽奖结果
    protected function lotteryResult(){
        $rand=mt_rand(1,10000);
        if($rand<=7000){
            return 1;
        }elseif(7000<$rand&&$rand<=8500){
            return 2;
        }elseif(8500<$rand&&$rand<=9500){
            return 3;
        }elseif(9500<$rand&&$rand<=9999){
            return 4;
        }else{
            return 5;
        }
    }
}