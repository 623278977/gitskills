<?php

/*
 * 用户编辑
 */

namespace App\Services\Version\User;

use App\Models\CurrencyLog;
use App\Models\ScoreLog;
use App\Models\User\Lottery;
use App\Models\Video;
use App\Models\Activity\Ticket;
use App\Models\Live\Entity as Live;
use App\Models\User\Entity as User;
use App\Models\User\Doubt as UserDoubt;
use App\Models\Doubt\Entity as Doubt;
use App\Models\User\Free as Free;
class _v020600 extends _v020502
{
    /*
     * 抽奖
     */
    public function postLottery($param)
    {
        if (!isset($param['uid'])) {
            return ['message' => '用户uid是必传参数', 'status' => false];
        }
        //先看看有没有免费机会
        $frees = Free::
        where(function($query){
                $query->where('deadline', '>', time())->orWhere('deadline', 0);
            })
            ->where('uid', $param['uid'])->where('use', '<', \DB::raw('num'))
            ->orderBy('deadline', 'asc')->get();



        //新注册用户首次登录24小时内可抽奖3次
        $times = Lottery::where('uid', $param['uid'])->count();
        $user = User::where('uid', $param['uid'])->first();

        if($user->login_count == 1 && (time()-$user->last_login <24*3600)){
            $first_login = true;
        }else{
            $first_login = false;
        }

//        //如果是首次登陆
//        if ($first_login && $times >= 3) {
//            return ['message' => '新注册用户首次登录24小时内只能抽奖3次', 'status' => false];
//        }

        //新注册用户首次登录24小时内可抽奖3次
        if (count($frees)) {
            Free::where('id', $frees[0]->id)->increment('use');
            $spend = 1;
            $unit = 'free';
        } else {//没有就使用积分
            $user = User::where('uid', $param['uid'])->where('score', '>=', 500)->first();
            if (!is_object($user)) {
                return ['message' => '您的积分不足500，无法参与抽奖！', 'status' => false];
            } else {
                User::where('uid', $param['uid'])->decrement('score', 500);
                //积分日志
                $use_score_log = ScoreLog::create(
                    [
                        'uid'   => $param['uid'],
                        'trigger_uid'   => $param['uid'],
                        'num'   => 500,
                        'remark'   => '抽奖消耗500积分',
                        'type'   => 'lottery',
                        'relation_type'   => 'lottery',
                        'operation'   => -1,
                    ]
                );
                $spend = 500;
                $unit = 'score';
            }
        }

        //中奖概率 iPhone 7一台（抽不中）、iPad一台（抽不中）、100元手机话费充值（3%）、
        //1G流量充值（5%）、1000积分（5%）、99无界币（2%）、5无界币（7%）、精美礼品（3%）、
        //再抽一次（15%）、感谢参与（60%）



        $prizes = [
            '1G流量充值卡',
            '100元手机话费充值',
            'iPad',
            'iPhone7',
            '感谢参与',
            '再抽一次',
            '精美礼品',
            '5无界币',
            '99无界币',
            '1000积分',
        ];

        $chance = [2, 1, 0, 0, 75, 13, 3, 2, 1, 3];
//        $chance = [100, 0, 0, 0, 0, 0, 0, 0, 0, 0];



        //此次获得的奖品
        $prize = $this->getPrize($chance);
        //前三次抽奖必有一次中奖 （1000积分 5无界币 精美礼品）
        $obtain_prize = Lottery::where('uid', $param['uid'])
            ->whereIn('type', ['prize', 'free'])->count();

//        if ($times == 2 && $obtain_prize == 0) {
//            while (in_array($prize['index'], [0,1,2,3,4,5,9])) {
//                $prize = $this->getPrize($chance);
//            }
//        }

        //写入数据表
        $lottery = Lottery::create(
            [
                'uid'   => $param['uid'],
                'name'  => $prizes[$prize['index']],
                'type'  => $prize['type'],
                'spend' => $spend,
                'unit'  => $unit,
//                'delivery'  => $delivery,
//                'delivery_at'  => $delivery_at,
            ]
        );

        $delivery = 'wait';
        $delivery_at = 0;
        $remark = '客服会与您取得联系，请保持手机畅通';

        //如果是再抽一次就存入机会表
        if($prize['index'] == 5){
            Free::create(
                [
                    'uid'   => $param['uid'],
                    'num'  => 1,
                    'use'  => 0,
                ]
            );
            $delivery = 'send';
            $delivery_at = time();
            $remark='';
        }

        //如果是1000积分就加积分
        if($prize['index'] == 9){
            User::where('uid', $param['uid'])->increment('score', 1000);
            //积分日志
            ScoreLog::create(
                [
                    'uid'   => $param['uid'],
                    'num'   => 1000,
                    'remark'   => '抽奖获得1000积分',
                    'type'   => 'lottery',
                    'relation_type'   => 'lottery',
                    'relation_id'   => $lottery->id,
                ]
            );
            $delivery = 'send';
            $delivery_at = time();
            $remark='';
        }

        //如果是5个无界币就加5个无界币
        if($prize['index'] == 7){
            $delivery = 'send';
            $delivery_at = time();
            $remark='';
            User::where('uid', $param['uid'])->increment('currency', 5);
            //无界币日志
            CurrencyLog::create(
                [
                    'uid'   => $param['uid'],
                    'trigger_uid'   => $param['uid'],
                    'num'   => 5,
                    'remark'   => '抽奖获得5无界币',
                    'relation_type'   => 'lottery',
                    'relation_id'   => $lottery->id,
                    'action'   => 'lottery',
                ]
            );
        }

        //如果是99个无界币就加99个无界币
        if($prize['index'] == 8){
            $delivery = 'send';
            $delivery_at = time();
            $remark='';
            User::where('uid', $param['uid'])->increment('currency', 99);
            //无界币日志
            CurrencyLog::create(
                [
                    'uid'   => $param['uid'],
                    'trigger_uid'   => $param['uid'],
                    'num'   => 99,
                    'remark'   => '抽奖获得99无界币',
                    'relation_type'   => 'lottery',
                    'relation_id'   => $lottery->id,
                    'action'   => 'lottery',
                ]
            );
        }

        //如果是谢谢参与
        if($prize['index'] == 4){
            $delivery = 'send';
            $delivery_at = time();
            $remark='未中奖';
        }

        $lottery->delivery = $delivery;
        $lottery->delivery_at = $delivery_at;
        $lottery->remark = $remark;
        $lottery->save();
        if(isset($use_score_log)){
            $use_score_log->relation_id = $lottery->id;
            $use_score_log->save();
        }


        if ($prize['index'] == 4) {
            return ['message' => ['name'=>$prizes[$prize['index']], 'index'=>$prize['index']], 'status' => true];
        } else {
            return ['message' => ['name'=>$prizes[$prize['index']], 'index'=>$prize['index']], 'status' => true];
        }
    }



    /*
    * 还剩下多少机会
    */
    public function postSurplus($param)
    {
        if (!isset($param['uid'])) {
            return ['message' => '用户uid是必传参数', 'status' => false];
        }
        $user = User::where('uid', $param['uid'])->first();
        $num = Free::where('uid', $param['uid'])
                    ->where(function($query){
                        $query->where('deadline', '>', time())->orWhere('deadline', 0);
                    })
            ->sum('num');
        $use = Free::where('uid', $param['uid'])
            ->where(function($query){
                $query->where('deadline', '>', time())->orWhere('deadline', 0);
            })
            ->sum('use');
        $surplus = $num-$use+floor($user->score/500);


        return ['message' => [ 'surplus'=>$surplus, 'score'=>$user->score], 'status' => true];
    }



    /*
    * 抽奖记录
    */
    public function postRecords($param)
    {
        $records = Lottery::where('uid', $param['uid'])
//            ->skip(($param['page']-1)*$param['page_size'])->take($param['page_size'])
                ->orderBy('created_at', 'desc')
            ->get(['name', 'remark', 'created_at', 'spend', 'unit']);

        $res = $records->map(function($item, $key){
            if($item->unit=='free'){
                $item->spend=0;
            }
            $item->created_at_format = date('m/d H:i', $item->created_at->timestamp);

            if(strstr($item->name, '1G流量充值卡') || strstr($item->name, '100元手机话费充值')|| strstr($item->name, '精美礼品')){
                $item->is_entity = 1;
            }else{
                $item->is_entity = 0;
            }
            return $item;
        });


        return ['message' => ['res'=>$res, 'page'=>$param['page']], 'status' => true];
    }


    public function getPrize($chance)
    {
        $res = '';
        $sum = array_sum($chance);

        foreach ($chance as $key => $val) {
            $randNum = mt_rand(1, $sum);
            if ($randNum <= $val) {
                $res = $key;
                break;
            } else {
                $sum -= $val;
            }
        }

        //这种写法速度反而要慢点，因为要构造数组
//        $randNum = mt_rand(1, 100);
//
//        if(in_array($randNum, range(1,5))){
//            $res=0;
//        }elseif(in_array($randNum, range(6,8))){
//            $res=1;
//        }elseif(in_array($randNum, range(9,68))){
//            $res=4;
//        }elseif(in_array($randNum, range(69,83))){
//            $res=5;
//        }elseif(in_array($randNum, range(84,86))){
//            $res=6;
//        }elseif(in_array($randNum, range(87,93))){
//            $res=7;
//        }elseif(in_array($randNum, range(94,95))){
//            $res=8;
//        }elseif(in_array($randNum, range(96,100))){
//            $res=9;
//        }

        if ($res == 5) {
            $type = 'free';
        } elseif ($res == 4) {
            $type = 'none';
        } else {
            $type = 'prize';
        }

        return ['index' => $res, 'type' => $type];
    }

    /*
    * 机器人服务
    */
    public function postDoubt($param)
    {
        if(!isset($param['uid'])||!isset($param['source'])||!isset($param['source_id'])||!isset($param['type'])){
            return ['message' => '参数错误', 'status' => false];
        }

        $lists = [];
        if ($param['type'] === 'first') {
            $lists = Doubt::orderBy('sort', 'desc')
                ->orderBy('is_hot', 'asc')
                ->orderBy('created_at', 'desc')
//                ->where('id', $param['content'])
                ->where('status', 'open')
                ->limit(6)->get(['id','title']);
            if($param['source']==='brand_detail'){
                $message = 'hi~ 有什么能帮助到你？有任何疑问都可以拨打400-011-0061客服中心咨询。或者留下你的电话小5会在第一时间跟你联系~';
            }else{
                $message = '欢迎来到无界商圈，我们为你提供OVO品牌招商一站式服务。很高兴见到你，我是机器人小5。也许你会问：';
            }
            $data = $lists;
            return ['message' => ['data'=>$data, 'message'=>$message, 'type'=>1], 'status' => true];
        }


        if ($param['type'] === 'precise') {
            $lists = Doubt::orderBy('sort', 'desc')
                ->orderBy('is_hot', 'asc')
                ->orderBy('created_at', 'desc')
                ->where('id', $param['content'])
                ->where('status', 'open')->get();
        }

        if ($param['type'] === 'keyword') {
            $lists = Doubt::
                where(function($query) use($param){
                    $query
                        ->where('title', 'like', '%' . $param['content'] . '%')
                        ->orWhere('keyword', 'like', '%' . $param['content'] . '%' )
                    ;
                })
                ->orderBy('sort', 'desc')
                ->orderBy('is_hot', 'asc')
                ->orderBy('created_at', 'desc')
                ->where('status', 'open')
                ->get();
            $message = '你是不是要问:';

            //收集用户的疑问
            $userdobut = UserDoubt::create([
                'uid'=>$param['uid'],
                'source'=>$param['source'],
                'source_id'=>$param['source_id'],
                'keyword'=>$param['content']
            ]);
        }

        if (count($lists) === 0) {
            $data = [
                'content' => '想获得品牌的加盟方案，通过发现商机页面全网搜索，包括行业、名称，就能轻松获得品牌的详情，加盟，优惠，如何对接等信息。同样您可以拨打我们客服热线：400-011-0061 我们竭诚为您服务~',
                'tel'=>'400-011-0061',
            ];
            $data['type'] = 0;
            if(isset($userdobut)){
                $userdobut->answer = 'default';
                $userdobut->save();
            }
            return ['message' => $data, 'status' => true];
        } elseif (count($lists) === 1) {
            $data = [
                'content' => $lists[0]->content,
                'tel'=>$lists[0]->tel,
            ];
            $data['type'] = 0;

            if(isset($userdobut)){
                $userdobut->answer = 'valid';
                $userdobut->save();
            }
            return ['message' => $data, 'status' => true];
        } else {
            $data = Doubt::
            where(function($query) use($param){
                $query->where('title', 'like', '%' . $param['content'] . '%')
                    ->orWhere('keyword', 'like', '%' . $param['content'] . '%' )
                ;
            })
                ->orderBy('sort', 'desc')
                ->orderBy('is_hot', 'asc')
                ->orderBy('created_at', 'desc')
                ->where('status', 'open')->get(['id','title']);

            if(isset($userdobut)){
                $userdobut->answer = 'valid';
                $userdobut->save();
            }
            return ['message' => ['data'=>$data, 'message'=>$message, 'type'=>1], 'status' => true];
        }

    }

}
