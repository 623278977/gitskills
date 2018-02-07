<?php namespace App\Models\Agent;

use Illuminate\Database\Eloquent\Model;
use App\Models\Brand\Entity as Brand;
use App\Models\Agent\Agent;
use App\Models\User\Entity as User;
use App\Models\Brand\Entity\V020800 as Brands;
use App\Models\Zone\Entity as Zone;
use Illuminate\Support\Facades\DB;

class AgentCustomerLog extends Model
{

    protected  $table       = 'agent_customer_log';
    protected $guarded      = [];

    protected $dateFormat   = 'U';

    public $timestamps      = false;

    /**
     * 关联：品牌
     */
    public function hasOneBrand()
    {
        return $this->hasOne(Brand::class, 'id', 'brand_id')
            ->select('id', 'logo', 'name');
    }
    /*
     * 关联品牌，获取全部的字段
     * */
    public function hasOneBrandAll()
    {
        return $this->hasOne(Brand::class, 'id', 'brand_id');
    }


    //关键经纪人
    public function agent(){
        return $this->belongsTo(Agent::class,'agent_id','id');
    }

    //关联用户
    public function user() {
        return $this->hasOne(User::class, 'uid', 'uid');
    }

    //关联经纪人客户表
    public function agent_customer() {
        return $this->hasOne(AgentCustomer::class, 'id', 'agent_customer_id');
    }

    //仅action=9,10,11,13时关联
    public function contracts(){
        return $this->hasOne('App\Models\Contract\Contract','id','post_id');
    }

    /**
     * 关联：经纪人客户表
     */
    public function belongsToAgentCustomer()
    {
        return $this->belongsTo(AgentCustomer::class, 'uid', 'uid');
    }

    /**
     * author zhaoyf
     *
     * 根据指定经纪人ID获取派单成功数据，然后发送通知信息
     *
     * @param   param    经纪人ID array
     *
     * @return data_list|array
     */
    public static function sendInform($param)
    {
        $confirm_result = array();

        //根据指定经纪人ID获取派单数据信息
        $_result = self::with('hasOneBrand', 'user')
            ->where('agent_id', $param['agent_id'])
            ->where('is_delete', 0)
            ->where('action', 0)
            ->groupBy(DB::raw('agent_id, brand_id, uid'))
            ->get();

        //对结果进行处理，然后返回结果
        if ($_result) {
            foreach ($_result as $key => $value) {

                $user_result = User::where('uid', $value->uid)->where('status', 1)->first();
                if (isset($user_result->zone_id) && !empty($user_result->zone_id)) {
                    $user_data = $user_result->realname ?: $user_result->nickname . '（' . abandonProvince(Zone::pidNames([$user_result->zone_id])) . '）';
                } else {
                    $user_data = $user_result->realname ?: $user_result->nickname;
                }

                //组合需要发送的通知数据
                $brand_commission = Brands::instances()->getMaxCommission($value->hasOneBrand->id);
                $confirm_result[$key] = [
                    'time'  => $value->created_at,
                    'type'  => 'customer_info',
                    'cont'  => [
                        'title'         => "【抢单成功】 与投资人：{$user_data}达成跟单关系！",
                        'id'            => $value->id,
                        'brand_id'      => $value->hasOneBrand->id,
                        'user_id'       => $value->user->uid,
                        'brand_name'    => $value->hasOneBrand->name,
                        'user_name'     => $user_data,
                        'take_time'     => $value->created_at,
                        'describe'      => trans('notification.join_order_describe', ['moneys' => $brand_commission]),
                    ]
                ];
            }
        }

        return $confirm_result;
    }

}