<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018-1-9
 * Time: 11:41
 */

namespace App\Services\Version\Agent\Customer;
use App\Models\Agent\AgentBrand;
use App\Models\Agent\AgentCustomer;
use App\Models\Agent\CommissionLevel;
use App\Models\Brand\BrandContactor;
use App\Models\Brand\BrandContract;
use App\Models\Brand\Entity as Brand;
use App\Models\Agent\Agent;
use App\Models\Contract\Contract;
use App\Models\User\Entity as User;
use App\Models\Zone;

class _v010300 extends _v010100
{
    /*
     *创建品牌加盟函s1
     */
    public function postContractStep1($data)
    {
        $agent_id=$data['agent_id'];
        if(!$agent_id){
            return ['message'=>'缺少经纪人id', 'status'=>false];
        }
        $brand_list=AgentBrand::with('brand',function($query){
            $query->where('status','enable')->select('id','name','slogan','logo','agency_way');
        })
            ->with('brand.categorys1')
            ->where('agent_id',$agent_id)
            ->where('status',4)
            ->get();
        $count=$brand_list->count();
        if(!$count){
            return ['message'=>'目前还没有代理品牌', 'status'=>false];
        }
        $arr=[];
        foreach ($brand_list as $val){
            $arr[]=[
                'brand_id'=>$val['brand_id'],
                'name'=>$val['brand']['name'],
                'slogan'=>$val['brand']['slogan'],
                'category'=>$val['brand']['categorys1']['name'],
                'agency_way'=>array_get(Brand::$_AGENCY_WAY,$val['brand']['agency_way']),
                'logo'=>getImage($val['brand']['logo'],'','thumb')
            ];
        }
        $return=['count'=>$count,'data'=>$arr];
        return ['message'=>$return,'status'=>true];
    }

    /*
     *创建品牌加盟函s2
     */
    public function postContractStep2($data)
    {
        $agent_id=$data['agent_id'];
        $brand_id=$data['brand_id'];
        $brand_info=$this->getBrandInfo($brand_id, $agent_id);
        if(is_string($brand_info)){
            return ['message'=>$brand_info, 'status'=>true];
        }
        //合同模版
        $contracts=BrandContract::with(['brandContractCost'=>function($query){
            $query->where('is_delete','0');
        }])
            ->where('brand_id',$brand_id)
            ->where('is_delete','0')
            ->select('id', 'name', 'league_type', 'amount', 'sum_commission','address')
            ->get();

        $arr=[];
        foreach ($contracts as $val){
            $cost=[];
            $total_cost=0;
            if($val['brandContractCost']){
                foreach ($val['brandContractCost'] as $v){
                    $cost[]=[
                        'cost_type'=>$v['cost_type'],
                        'cost'=>number_format($v['cost_limit']),
                    ];
                    $total_cost +=$v['cost_limit'];
                }
            }
            //最高提成
            $commission=CommissionLevel::where('brand_id',$data['brand_id'])->orderBy('scale','desc')->first();

            $arr[]=[
                'id'=>$val['id'],
                'name'=>$val['name'],
                'league_type'=>$val['league_type'],
                'total_cost'=>number_format($total_cost),
                'max_commission'=>($commission['scale']*100).'%',
                'cost_details'=>$cost,
                'address'=>$val['address']
            ];
        }
        //商务代表电话
        $contactor=BrandContactor::where('brand_id',$data['brand_id'])->select('non_reversible')->first();

        $tel=getRealTel($contactor['non_reversible'],'agent');

        return ['message'=>['brand_info'=>$brand_info,'contract_info'=>$arr,'tel'=>$tel], 'status'=>true];

    }

    /*
     *创建品牌加盟函s3
     */
    public function postContractStep3($data)
    {
        $brand_info=$this->getBrandInfo($data['brand_id'],$data['agent_id']);
        $contract_info=$this->contractInfo($data['contract_id'], $data['brand_id']);
        $customers=AgentCustomer::where('agent_id',$data['agent_id'])->where('status','<>','-1')->count();

        $result=[
            'brand_info'=>$brand_info,
            'contract_info'=>$contract_info,
            'customers'=>$customers,
        ];
        $user_info=$data['uid']?$this->uerInfo($data['uid']):'';
        //去掉手机号
        if($user_info){
            unset($user_info['tel']);
            $result['user_info']=$user_info;
        }
        return ['message'=>$result, 'status'=>true];
    }

    /*
     * 客户列表
     */
    public function postCustomerList($data)
    {
        $brand_id=$data['brand_id'];
        $customers=AgentCustomer::with(['user'=>function($query)use($brand_id){
            $query->with(['agent_customer_log'=>function($query)use($brand_id){
                $query->where('brand_id',$brand_id);
            }]);
        }])
        ->where('agent_id',$data['agent_id'])
        ->where('status','<>','-1')
        ->get();
        $brand=Brand::where('id',$data['brand_id'])->select('name')->first();
        if(!$customers){
            return ['message'=>'目前还没有投资人，去邀请可获得积分','status'=>false];
        }
        $customers=$customers->toArray();
        $intention_user=[];
        $normal_user=[];
            foreach ($customers as $v){
                if($v['user']['agent_customer_log']){
                    $intention_user[]=[
                        'uid'=>$v['user']['uid'],
                        'nickname'=>$v['user']['nickname'],
                        'avatar'=>getImage($v['user']['avatar'],'avatar',''),
                        'gender'=>User::getGender($v['user']['gender']),
                        'zone'=>Zone::getZone($v['user']['zone_id']),
                        'selected'=>($data['uid']&&$data['uid']==$v['user']['uid'])?true:false
                    ];
                }else{
                    $normal_user[]=[
                        'uid'=>$v['user']['uid'],
                        'nickname'=>$v['user']['nickname'],
                        'avatar'=>getImage($v['user']['avatar'],'avatar',''),
                        'gender'=>$v['user']['gender'],
                        'zone'=>Zone::getZone($v['user']['zone_id']),
                        'selected'=>($data['uid']&&$data['uid']==$v['user']['uid'])?true:false
                    ];
                }
            }

        $result=[
            'brand_name'=>$brand['name'],
            'intention_customer'=>$intention_user,
            'normal_customer'=>$normal_user
        ];
        return ['message'=>$result, 'status'=>true];
    }

    /*
     *创建品牌加盟函s4
     */
    public function postContractStep4($data)
    {
        $contract=new \App\Services\Version\Agent\Contract\_v010000();
        $res=$contract->postSend($data);
        if($res['status']===false){
            return $res;
        }
        $brand_info=$this->getBrandInfo($data['brand_id'],$data['agent_id']);
        $contract_info=$this->contractInfo($data['brand_contract_id'],$data['brand_id']);
        $user_info=$this->uerInfo($data['uid']);
        unset($user_info['tel']);
        $contract_info['id']=$res['message'];
        $result=[
            'brand_info'=>$brand_info,
            'contract_info'=>$contract_info,
            'customer_info'=>$user_info
        ];
        return['message'=>$result,'status'=>true];
    }
    /*
     * 创建品牌加盟函s4等待确认的
     */
    public function postWaitConfirm($data)
    {
        $contracts=Contract::where('agent_id',$data['agent_id'])
            ->whereIn('status',['0','6'])
            ->get();
        if(!$contracts){
            return ['message'=>'暂时还没有等待确认的合同','status'=>false];
        }
        $contracts=$contracts->toArray();
        $arr=[];
        foreach ($contracts as $v){
            $contract_info=$this->contractInfo($v['brand_contract_id'],$v['brand_id']);
            $contract_info['id']=$v['id'];
            $arr[]=[
                'brand_info'=>$this->getBrandInfo($v['brand_id'],$v['agent_id']),
                'contract_info'=>$contract_info,
                'customer_info'=>$this->uerInfo($v['uid']),
                'wait_time'=>AgentCustomer::waitConfirmTime($v['updated_at']),
                'created_at'=>date('Y-m-d',$v['created_at'])
            ];
        }
        return ['message'=>$arr, 'status'=>true];
    }
    
    /*
     * 获取品牌信息
     */
    protected function getBrandInfo($brand, $agent_id)
    {
        $brand_info=Brand::with('categorys1')->where('id',$brand)
            ->where('status','enable')
            ->first();
        if(!$brand_info){
            return '该品牌暂不可用';
        }
        $brand_count=AgentBrand::where('agent_id',$agent_id)
            ->where('status','4')
            ->count();
        $contract_count=BrandContract::where('brand_id',$brand)
            ->where('is_delete','0')
            ->count();
        return [
            'id'=>$brand_info['id'],
            'name'=>$brand_info['name'],
            'slogan'=>$brand_info['slogan'],
            'category'=>$brand_info['categorys1']['name'],
            'logo'=>getImage($brand_info['logo'],'','thumb'),
            'agency_way'=>array_get(Brand::$_AGENCY_WAY,$brand_info['agency_way']),
            'brand_count'=>$brand_count,
            'contract_count'=>$contract_count
        ];
    }

    /*
     * 获取合同模版信息
     */
    protected function contractInfo($contract_id,$brand_id)
    {
        $contract=BrandContract::with(['brandContractCost'=>function($query){
            $query->where('is_delete','0');
        }])->where('id',$contract_id)
            ->first();
        $commission=CommissionLevel::where('brand_id',$brand_id)->orderBy('scale','desc')->first();
        $total_cost=0;
        $cost_detail=[];
        foreach ($contract['brandContractCost'] as $v){
            $cost_detail[]=[
                'cost_type'=>$v['cost_type'],
                'cost'=>number_format($v['cost_limit']),
            ];
            $total_cost +=$v['cost_limit'];
        }
        $return=[
            'id'=>$contract['id'],
            'address'=>$contract['address'],
            'name'=>$contract['name'],
            'league_type'=>$contract['league_type'],
            'total_cost'=>number_format($total_cost),
            'cost_details'=>$cost_detail,
            'max_commission'=>($commission['scale']*100).'%'

        ];

        return $return;
    }

    protected function uerInfo($uid)
    {
        $customer=User::where('uid',$uid)->select('uid','nickname','avatar','gender','zone_id','non_reversible')->first();
        $tel=getRealTel($customer['non_reversible'],'agent');
        $user_info=[
            'uid'=>$customer['uid'],
            'nickname'=>$customer['realname']?$customer['realname']:$customer['nickname'],
            'avatar'=>getImage($customer['avatar'],'avatar',''),
            'gender'=>$customer['gender'],
            'zone'=>Zone::getZone($customer['zone_id']),
            'tel'=>$tel
        ];
        return $user_info;
    }
}