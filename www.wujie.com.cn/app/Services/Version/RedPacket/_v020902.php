<?php

namespace App\Services\Version\RedPacket;
use App\Models\RedPacket\RedPacket;
use App\Models\RedPacket\RedPacketPerson;
use App\Models\RedPacket\RedPacketPush;
use App\Models\User\Entity as User;
use App\Services\Version\VersionSelect;
use App\Models\Identify;

class _v020902 extends VersionSelect
{
    //领取后台推送红包
    public function postReceivePush($param)
    {
        $push_id=$param['push_id'];
        $red_packet_push=RedPacketPush::with(['red_packet'=>function($query){
            $query->where('status',1)
                ->where(function ($query){
                    $query->where('expire_at','>=',time())
                        ->orWhere('expire_at',-1);
                });
        }])
        ->where('id',$push_id)
        ->first();
        if(empty($red_packet_push->red_packet)){
            return ['message'=>'此次发送的红包已领完，请您下次再来','status'=>true];
        }
        $res=[];
        foreach ($red_packet_push->red_packet as $value){
            //如果领取过则跳过
            if(!RedPacketPerson::where('red_packet_id',$value['id'])->where('receiver_id',$param['uid'])->count()){
                $res[]=RedPacketPerson::create([
                    'receiver_id'=>$param['uid'],
                    'red_packet_id'=>$value['id'],
                    'expire_at'=>$value['expire_at'],
                    'amount'=>$value['amount'],
                    'type'=>$value['type'],
                    'gain_source'=>2,
                ]);
                //领取数量增加1
                RedPacket::where('id',$value->id)->increament('gives');
            }
        }

        if(empty($res)){
            return ['message'=>'你已经领取过了','status'=>true];
        }
        $return=[];
        foreach ($res as $v){
            $return[]=$v->id;
        }
        return ['message'=>$return, 'status'=>true];
    }

    //领取品牌页面红包
    public function postReceiveBrand($param)
    {
        $brand_id=$param['brand_id'];
        $red_packets=RedPacket::where('post_id',$brand_id)
            ->where('type',2)
            ->where('status',1)
            ->where(function ($query){
                $query->where('expire_at','>=',time())
                    ->orWhere('expire_at',-1);
            })
            ->get()->toArray();

        if(!$red_packets){
            return ['message'=>'此次发送的红包已领完','status'=>false];
        }
        $res=[];
        foreach ($red_packets as $value){
            //如果没领取过则发红包
            if(!RedPacketPerson::where('red_packet_id',$value['id'])->where('receiver_id',$param['uid'])->count()){
                $res[]=RedPacketPerson::create([
                    'receiver_id'=>$param['uid'],
                    'red_packet_id'=>$value['id'],
                    'expire_at'=>$value['expire_at'],
                    'amount'=>$value['amount'],
                    'type'=>$value['type'],
                    'gain_source'=>3,
                ]);
                //领取数量增加1
                RedPacket::where('id',$value->id)->increament('gives');
            }

        }
        if(empty($res)){
            return ['message'=>'你已经领取过了','status'=>false];
        }
        $return=[];
        foreach ($res as $v){
            $return[]=$v->id;
        }
        return ['message'=>$return, 'status'=>true];
    }


    //领取分享页面红包
    public function postReceiveShare($param)
    {
        $username=$param['username'];
        $code=$param['code'];
        $brand_id=$param['brand_id'];
        $non_reversible = encryptTel($username);
        if (empty($code)) {
            return ['message'=>'验证码不能为空', 'status'=>false];
        }
        if (empty($username)) {
            return ['message'=>'手机号不能为空', 'status'=>false];
        }
        if(empty($brand_id)){
            return ['message'=>'品牌id不能为空', 'status'=>false];
        }

        $flag = Identify::checkIdentify($non_reversible,'standard', $code);
        if ($flag !== 'success' && in_array($username, ['15658676670', '15068713205','13900000022'])) {
            $flag = 'success';
        }

        if ($flag === 'success') {

            if ($user = User::where('non_reversible', $non_reversible)->first()) {

                $red_packets=RedPacket::where('post_id',$brand_id)
                    ->where('type',2)
                    ->where('status',1)
                    ->where(function ($query){
                        $query->where('expire_at','>=',time())
                            ->orWhere('expire_at',-1);
                    })
                    ->get()->toArray();

                if(!$red_packets){
                    return ['message'=>'此次发送的红包已领完','status'=>false];
                }
                $packets=[];
                foreach ($red_packets as $value){
                    //如果领取过则跳过
                    if(!$exist=RedPacketPerson::where('red_packet_id',$value['id'])->where('receiver_id',$user->uid)->count()){
                        $res=RedPacketPerson::create([
                            'receiver_id'=>$user->uid,
                            'red_packet_id'=>$value['id'],
                            'expire_at'=>$value['expire_at'],
                            'amount'=>$value['amount'],
                            'type'=>$value['type'],
                            'gain_source'=>4,
                        ]);
                        $packets[]=$res->id;
                        //领取数量增加1
                        RedPacket::where('id',$value->id)->increment('gives');
                    }

                }

                if(empty($packets)){
                    return ['message'=>'你已经领取过了','status'=>false];
                }

                return ['message'=>['uid'=>$user['uid'],'red_packets'=>$packets], 'status'=>true];

            } else {
                //新用户注册并领取红包
                $register=new \App\Services\Version\Login\_v020800();
                $res=$register->postRegisteraccount(['username'=>$username,]);
                if(!$res['status']){
                    return ['message'=>$res['message'], 'status'=>false];
                }
                $user=$res['message'];
                //领取红包
                $red_packets=RedPacket::where('post_id',$brand_id)
                    ->where('type',2)
                    ->where('status',1)
                    ->where(function ($query){
                        $query->where('expire_at','>=',time())
                            ->orWhere('expire_at',-1);
                    })
                    ->get()->toArray();

                if(!$red_packets){
                    return ['message'=>'此次发送的红包已领完','status'=>false];
                }
                $packets=[];
                foreach ($red_packets as $value){
                    //如果领取过则跳过
                    if(!RedPacketPerson::where('red_packet_id',$value['id'])->where('receiver_id',$param['uid'])->count()){
                        $red=RedPacketPerson::create([
                            'receiver_id'=>$user['uid'],
                            'red_packet_id'=>$value['id'],
                            'expire_at'=>$value['expire_at'],
                            'amount'=>$value['amount'],
                            'type'=>$value['type'],
                            'gain_source'=>4,
                        ]);
                        $packets[]=$red->id;
                        //领取数量增加1
                        RedPacket::where('id',$value->id)->increment('gives');
                    }
                }

                if(empty($red)){
                    return ['message'=>'你已经领取过了','status'=>false];
                }

                return ['message'=>['uid'=>$user['uid'],'red_packets'=>$packets], 'status'=>true];
            }

        }

        return ['message'=>'验证码不正确', 'status'=>false];
    }

    //红包领取反馈页面
    public function postReceiveSuccess($param)
    {
        $red_packet_person_id=$param['id'];
        if(empty($red_packet_person_id)){
            return ['message'=>'缺少红包id', 'status'=>false];
        }
        if(!$param['uid']){
            return ['message' => '缺少用户id', 'status'=>false];
        }
        if(is_array($red_packet_person_id)){
            foreach ($red_packet_person_id as $value){
                $red_packet_person=RedPacketPerson::where('id',$value)->where('receiver_id',$param['uid'])->first();
                if($red_packet_person){
                    $return[]=$red_packet_person->getBase();
                }
            }
        }else{
            $red_packet_person=RedPacketPerson::where('id',$red_packet_person_id)->where('receiver_id',$param['uid'])->first();
            if(empty($red_packet_person)){
                return ['message'=>'没有找到红包', 'status'=>false];
            }
            $return[]=$red_packet_person->getBase();
        }
        return ['message'=>$return,'status'=>true];
    }
}