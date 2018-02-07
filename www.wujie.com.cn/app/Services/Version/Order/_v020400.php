<?php

namespace App\Services\Version\Order;

use App\Http\Requests\Order\OrderSignRequest;
use App\Services\Version\VersionSelect;
use App\Models\Activity\Entity as Activity;
use App\Models\Activity\Sign;
use App\Models\ScoreLog;
use App\Models\User\Ticket;
use Validator;
use App\Models\Orders\Entity as Orders;
use App\Models\Order\Entity;

class _v020400 extends VersionSelect
{

    //允许购买的商品类型
    private $good_types = [
        'brand',
        'score',
        'brand_goods',
        'video_reward',
        'live_reward',
        'inspect_invite',
        'contract'
    ];




    //static $enable = FALSE;  //版本是否启用
    public function postOrderAndSign($data)
    {
        $types = array_unique(array_pluck($data['items'], 'type'));

        if (array_diff($types, $this->good_types)) {
            return ['message' => '商品类型不在允许的范围内', 'status' => false];
        }


        $rate = config('system.score_rate');

        //下单
        $orders = Orders::place(
            $data['uid'],
            $data['amount'],
            $data['items'],
            $data['pay_way'],
            $data['score_num'],
            ($data['score_num'] / $rate),
            ($data['amount'] - ($data['score_num'] / $rate)),
            'npay',
            $data['mobile'],
            $data['realname'],
            $data['zone_id'],
            $data['address']
        );
        //签名
        $str = Orders::sign($orders->order_no, $data['pay_way']);

        if ($str == -1) {
            return ['message'=>['str'=>'订单号和支付方式是必传参数','order_no'=>$orders->order_no],'status'=>false];

        } elseif ($str == -2) {
            return ['message'=>['str'=>'支付方式只能为ali或微信','order_no'=>$orders->order_no],'status'=>false];
        } elseif ($str == -3) {
            return ['message'=>['str'=>'不存在该订单','order_no'=>$orders->order_no],'status'=>false];
        } elseif ($str == -4) {
            return ['message'=>['str'=>'获取prepay_id失败','order_no'=>$orders->order_no],'status'=>false];
        }

        return ['message'=>['str'=>$str,'order_no'=>$orders->order_no],'status'=>true];

    }



    public function postOrderAndSignValidate($data)
    {
        if($data['items'][0]['type']=='brand_goods'){
            $messages = [
                'required' => ':attribute为必传参数',
            ];

            $attributes = [
                'realname' => '姓名',
                'mobile' => '手机号',
            ];

            $validator = Validator::make($data, [
                'realname' => 'required',
                'mobile' => 'required',
            ], $messages, $attributes);
            $messages = $validator->errors();

            if ($validator->fails()) {
                $message = '';
                foreach($messages->all() as $key=>$val){
                    $message.=$val;
                }
                $result['is_break'] = 1;
                $result['message'] = $message;
            }   else{
                $result['is_break'] = 0;
            }
            return $result;
        }


    }



}