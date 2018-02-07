<?php

namespace App\Models\RedPacket;

use App\Models\Agent\Brand;
use App\Models\Agent\ContractPayLog;
use App\Models\Agent\Invitation;
use App\Models\Contract\Contract;
use App\Models\Orders\Items;
use Illuminate\Database\Eloquent\Model;
use App\Models\User\Entity as User;

class RedPacketPerson extends Model
{
    protected  $table =  'red_packet_person';
    protected $dateFormat = 'U';
    protected $guarded = [];

//'红包类型 1：品牌全场红包 2：某品牌专属红包 3:邀请红包  4:奖励红包(车马费) 5:福字红包
// 6:新年开门大吉不定额现金红包 7:春节活动不定金额现金红包 8：新年活动经纪人答题红包',

    public static  $USER_CAN_USE =
        [1, 8];

    //关联红包表
    public function red_packet(){
        return $this->belongsTo(RedPacket::class , 'red_packet_id' , 'id');
    }

    //关联用户表
    public function user(){
        return $this->belongsTo(User::class , 'receiver_id' , 'uid');
    }

    /**
     * 关联：支付日记
     */
    public function hasOneContractPayLogs()
    {
        return $this->hasOne(ContractPayLog::class,'post_id', 'id');
    }

    //获取使用信息
    public function getUsedInfo()
    {
        if(1!==$this->status){
            return false;
        }
        $used_log=RedPacketPersonUseLog::where('red_packet_person_id',$this->id)->first();
        if(!$used_log){
            return '未查询到红包的使用信息';
        }
        $data=[];
        if(1==$used_log->used_way){
            $contract_pay_log=ContractPayLog::where('contract_id',$used_log->relation_id)->where('status',1)->get();
            if(!$contract_pay_log){
                return '获取合同支付信息失败';
            }
            $contract_info=Contract::with('brand')->where('id',$used_log->relation_id)->first();
            $data['used_way']='contract';
            $data['contract_name']=$contract_info->name;
            $data['contract_no']=$contract_info->contract_no;
            $data['brand_name']=$contract_info['brand']['name'];
            $data['amount']=$contract_info->amount;
            $data['common_red_packet']=0;
            $data['brand_red_packet']=0;
            $data['reward_red_packet']=0;
            $data['invention_deduction']=0;
            $data['total_deduction']=0;
            $data['real_pay']=0;
            foreach ($contract_pay_log as $v){
                switch ($v->type){
                    case 1:
                        $data['invention_deduction']+=$v->num;
                        $data['total_deduction']+=$v->num;
                        break;
                    case 2:
                        $data['real_pay']+=$v->num;
                        break;
                    case 3:
                        $data['common_red_packet']+=$v->num;
                        $data['total_deduction']+=$v->num;
                        break;
                    case 4:
                        $data['brand_red_packet']+=$v->num;
                        $data['total_deduction']+=$v->num;
                        break;
                    case 5:
                        $data['total_deduction']+=$v->num;
                        break;
                    default:
                        break;
                }
            }
        }
        if(2==$used_log->used_way){
            $invention=Invitation::with('hasOneStore.hasOneBrand')->where('id',$used_log->relation_id)->first();
            $data['used_way']='invention';
            $data['order_type']='考察定金';
            $data['brand_name']=$invention['hasOneStore']['hasOneBrand']['name'];
            $data['pay_at']=date('Y-m-d H:i',$invention['pay_time']);

        }
        return $data;
    }

    /*
     * 红包基本信息
     */
    public function getBase()
    {
        $packet_info=RedPacket::where('id',$this->red_packet_id)->first();
        $data['name']=$packet_info->name;
        $data['description']=$packet_info->description;
        $data['amount']=$this->amount;
        $data['expire_at']=$this->expire_at==-1?'不限期限':'有效期至 '.date('Y年m月d日',$this->expire_at);
        $data['use_scenes']=$packet_info->use_sence;
        $data['type']=$packet_info->type;
        if(2==$packet_info->type||4==$packet_info->type){
            $brand=Brand::where('id',$packet_info->post_id)->first();
            $data['brand_name']=$brand->name;
            $data['brand_logo']=getImage($brand->logo);
            $data['brand_id']=$brand->id;
        }
        if($packet_info->min_consume){
            $data['min_consume']=$packet_info->min_consume;
        }
        return $data;

    }


    /**
     * 下单购买合同 混合支付，最大限度的使用红包
     *
     * @param $uid
     * @param $brand_id
     * @param $order_id
     * @param $contract_id
     * @author tangjb
     */
    public static  function usePacket($uid, $brand_id, $order_id, $contract_id, $amount)
    {
        //pay_log表把红包使用掉
        //找出uid所有的红包
//        1：品牌全场红包 2：某品牌专属红包 3:邀请红包  4:奖励红包(车马费) 5:福字红包
//        6:新年开门大吉不定额现金红包 7:春节活动不定金额现金红包 8：新年活动经纪人答题红包',
        $now = time();
        $packets = RedPacketPerson::where('receiver_id', $uid)->where('status', 0)
            ->where(function($query) use ($brand_id){
                $query->whereIn('type', RedPacketPerson::$USER_CAN_USE)->orWhere(function($builder)use ($brand_id){
                    $builder->where('type', 2)->whereIn('red_packet_id', function($query)use ($brand_id){
                        $query->from('red_packet')->where('type', 2)->where('post_id', $brand_id)->lists('id');
                    });
                })->orWhere(function($builder)use ($brand_id){
                    $builder->where('type', 4)->whereIn('red_packet_id', function($query)use ($brand_id){
                        $query->from('red_packet')->where('type', 4)->where('post_id', $brand_id)->lists('id');
                    });
                });
            })
            ->where(function($query){
                $query->where('expire_at', '>=', time())->orWhere('expire_at', -1);
            })
            ->whereIn('red_packet_id', function ($query)use($amount) {
                $query->from('red_packet')->where('status', 1)->where('min_consume', '<=', $amount)->lists('id');
            })
            ->get();



//        红包优惠
        $data = $use_log = [];
        foreach ($packets as $k => $v) {
            $data[$k]['contract_id'] = $contract_id;
            $data[$k]['type'] = self::transformType($v->type);
            $data[$k]['post_id'] = $v->id;
            $data[$k]['num'] = $v->amount;
            $data[$k]['status'] = 1;
            $data[$k]['pay_at'] = $now;
            $data[$k]['order_id'] = $order_id;
            $data[$k]['order_no'] = self::produceOrderNo();
            $data[$k]['created_at'] = $now;
            $data[$k]['updated_at'] = $now;
            $v->status = 1;
            $v->used_at = $now;
            $v->save();


            $use_log[$k]['red_packet_person_id'] = $v->id;
            $use_log[$k]['used_way'] = 1;
            $use_log[$k]['relation_id'] = $contract_id;
            $use_log[$k]['created_at'] = $now;
            $use_log[$k]['updated_at'] = $now;
        }

        //          考察订金
        $invite = Invitation::where('uid', $uid)->where('type', 2)->whereIn('post_id', function($query) use($brand_id){
            $query->from('brand_store')->where('brand_id', $brand_id)->lists('id');
        })->where('status', 1)->first();

        if($invite){
            $invite_deduce = ['contract_id'=>$contract_id, 'type'=>1, 'post_id'=>$invite->id, 'num'=>$invite->default_money,
                                'status'=>1, 'pay_at'=>$now, 'order_id'=>$order_id, 'order_no'=>self::produceOrderNo(), 'created_at'=>$now, 'updated_at'=>$now
            ];
            $data[] = $invite_deduce;
            $invite->status =4;
            $invite->contract_id =$contract_id;
            $invite->save();
        }


        //    意向加盟金
        $intent_brand = Items::where('type', 'brand')->where('status', 'pay')->whereIn('order_id', function($query) use($uid){
            $query->from('orders')->where('uid', $uid)->lists('id');
        })->whereIn('product_id', function($query) use($brand_id){
            $query->from('live_brand_goods')->where('brand_id', $brand_id)->lists('id');
        })
            ->first();

        if($intent_brand){
            $intent_deduce = ['contract_id'=>$contract_id, 'type'=>9, 'post_id'=>$intent_brand->id, 'num'=>$intent_brand->price,
                'status'=>1, 'pay_at'=>$now, 'order_id'=>$order_id, 'order_no'=>self::produceOrderNo(), 'created_at'=>$now, 'updated_at'=>$now
            ];
            $data[] = $intent_deduce;
            $intent_brand->status = 'used';
            $intent_brand->save();
        }


        //        `type` tinyint(4) NOT NULL COMMENT '支付类型：1：考察订金抵扣；2：pos机支付；3：通用红包 4：品牌红包
//        5:奖励红包(车马费) 6：初创红包（邀请红包) 7:新年活动经纪人答题红包  8: 线下到帐 9 :品牌加盟预付金抵扣',


        //        初创红包
        $initial = RedPacketPerson::where('receiver_id', $uid)->where('red_packet_id', function ($query) {
            $query->from('red_packet')->where('type', 3)->value('id');
        })->where('status', 0)
            ->where(function ($query) {
                $query->where('expire_at', '>=', time())->orWhere('expire_at', -1);
            })
            ->whereIn('red_packet_id', function ($query)use($amount) {
                $query->from('red_packet')->where('status', 1)->where('min_consume', '<=', $amount)->lists('id');
            })
            ->first();


        if($initial){
            $initial_deduce = ['contract_id'=>$contract_id, 'type'=>6, 'post_id'=>$initial->id, 'num'=>$initial->amount,
                'status'=>1, 'pay_at'=>$now, 'order_id'=>$order_id, 'order_no'=>self::produceOrderNo(),
                'created_at'=>$now, 'updated_at'=>$now
            ];

            $initial_log = ['red_packet_person_id'=>$initial->id, 'used_way'=>1, 'relation_id'=>$contract_id,
                'created_at'=>$now, 'updated_at'=>$now
            ];
            $data[] = $initial_deduce;
            $use_log[] = $initial_log;
            $initial->status = 1;
            $initial->used_at = $now;
            $initial->save();
        }


        $res = ContractPayLog::insert($data);
        RedPacketPersonUseLog::insert($use_log);

        return $res;
    }

    /**
     * 转换类型
     *
     * @author tangjb
     */
    public static function transformType($type)
    {
//        `type` tinyint(2) unsigned NOT NULL COMMENT '红包类型 1：品牌全场红包 2：某品牌专属红包 3:邀请红包
//         4:奖励红包(车马费) 5:福字红包 6:新年开门大吉不定额现金红包 7:春节活动不定金额现金红包 8：新年活动经纪人答题红包',

//        `type` tinyint(4) NOT NULL COMMENT '支付类型：1：考察订金抵扣；2：pos机支付；3：通用红包 4：品牌红包
//        5:奖励红包(车马费) 6：初创红包（邀请红包) 7:新年活动经纪人答题红包  8: 线下到帐 9 :品牌加盟预付金抵扣',

        $arr = [
          1=>3,
          2=>4,
          3=>6,
          4=>5,
          8=>7,
        ];

        return $arr[$type];
    }


    public static  function produceOrderNo()
    {
        $order_no = chr(rand(97, 122)) . chr(rand(97, 122)) . chr(rand(97, 122)). chr(rand(97, 122)) . time() . chr(rand(97, 122)) . chr(rand(97, 122)) . chr(rand(97, 122)). chr(rand(97, 122));

        return $order_no;
    }


    public static function getAllDiscount($uid, $brand_id, $amount)
    {
        //初创红包
        $initial = RedPacketPerson::where('receiver_id', $uid)->where('red_packet_id', function ($query) {
            $query->from('red_packet')->where('type', 3)->value('id');
        })
            ->whereIn('red_packet_id', function ($query)use($amount) {
                $query->from('red_packet')->where('status', 1)->where('min_consume', '<=', $amount)->lists('id');
            })
            ->where('status', 0)
            ->first();


        if($initial){
            $initial = $initial->amount;
        }else{
            $initial = 0;
        }

        //红包优惠
//        $packets = RedPacketPerson::where('receiver_id', $uid)->where('status', 0)
//            ->whereIn('type', [1, 2, 4, 5, 6, 7, 8])->get();


        $packets = RedPacketPerson::where('receiver_id', $uid)->where('status', 0)
            ->where(function($query) use ($brand_id){
                $query->whereIn('type', RedPacketPerson::$USER_CAN_USE)->orWhere(function($builder)use ($brand_id){
                    $builder->where('type', 2)->whereIn('red_packet_id', function($query)use ($brand_id){
                        $query->from('red_packet')->where('type', 2)->where('post_id', $brand_id)->lists('id');
                    });
                })->orWhere(function($builder)use ($brand_id){
                    $builder->where('type', 4)->whereIn('red_packet_id', function($query)use ($brand_id){
                        $query->from('red_packet')->where('type', 4)->where('post_id', $brand_id)->lists('id');
                    });
                });
            })
            ->where(function($query){
                $query->where('expire_at', '>=', time())->orWhere('expire_at', -1);
            })
            ->whereIn('red_packet_id', function ($query)use($amount) {
                $query->from('red_packet')->where('status', 1)->where('min_consume', '<=', $amount)->lists('id');
            })
            ->get();


        //1：品牌全场红包 2：某品牌专属红包 3:邀请红包  4:奖励红包(车马费) 5:福字红包
        // 6:新年开门大吉不定额现金红包 7:春节活动不定金额现金红包 8：新年活动经纪人答题红包',
        $packet_sum = 0;
        foreach ($packets as $k => $v) {
            if (in_array($v->type, [2, 4])) {
                $packet = RedPacket::where('id', $v->red_packet_id)->first();
                if ($packet->post_id != $brand_id) continue;
            }
            $packet_sum += $v->amount;
        }



        //考察订金
        $invite = Invitation::where('uid', $uid)->where('type', 2)->whereIn('post_id', function($query) use($brand_id){
            $query->from('brand_store')->where('brand_id', $brand_id)->lists('id');
        })->where('status', 1)
            ->first();

        if($invite){
            $invite = $invite->default_money;
        }else{
            $invite = 0;
        }


        //意向加盟金
        $intent_brand = Items::where('type', 'brand')->where('status', 'pay')->whereIn('order_id', function($query) use($uid){
            $query->from('orders')->where('uid', $uid)->lists('id');
        })->whereIn('product_id', function($query) use($brand_id){
            $query->from('live_brand_goods')->where('brand_id', $brand_id)->lists('id');
        })->first();

        if($intent_brand){
            $intent_brand = $intent_brand->price;
        }else{
            $intent_brand = 0;
        }


        $total = $initial+$packet_sum+$invite+$intent_brand;
        $initial = numFormatWithComma($initial);
        $packet_sum = numFormatWithComma($packet_sum);
        $invite = numFormatWithComma($invite);
        $intent_brand = numFormatWithComma($intent_brand);
        $total = numFormatWithComma($total);


        return compact('initial', 'packet_sum', 'invite', 'intent_brand', 'total');
    }

}
