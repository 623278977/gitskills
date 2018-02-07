<?php namespace App\Models\Agent\Message;

use App\Models\Agent\AgentCustomer;
use App\Models\Agent\AgentCustomerLog;
use DB;
use App\Models\Agent\Agent;
use \App\Models\Agent\Message\AgentCustomerPush;
use Illuminate\Database\Eloquent\Model;
use App\Models\User\Entity as User;
use App\Models\Zone\Entity as Zone;

class AgentCustomerMessage extends Model
{
    protected $table = 'agent_customer_message';

    const IS_READ_TYPE = 1;     //消息已读取标记
    const REFUSE_TYPE  = -1;    //拒绝标记
    const ON_READ_TYPE = 0;     //没有读取标记

    public static $instance = null;
    public static function instance()
    {
        if (is_null(self::$instance)) {
            self::$instance = new self;
        }

        return self::$instance;
    }


    /**
     * 消息 -- 推荐投资人列表 zhaoyf
     *
     * @param $param
     *
     * @return array|string
     */
    public function recommendCustomers($param)
    {
        //对传递参数值进行处理
        if (empty($param['agent_id']) || !isset($param['agent_id']) || !is_numeric($param['agent_id'])) {
            return ['message' => '缺少经纪人ID，且只能是整数', 'status' => false];
        }

        $agent_result = self::where('agent_id', $param['agent_id'])
            ->where('is_read', self::IS_READ_TYPE)
            ->where('send_time', '<', time())
            ->orderBy('created_at', 'desc')
            ->get();

        //对结果进行处理
        if ($agent_result) {
            $confirm_result = array();
            foreach ($agent_result as $key => $vls) {
                $user_result = User::where('uid', $vls->customer_id)->first();
                $confirm_result[$key] = [
                    'user_id'    => $user_result->uid,
                    'gender'     => $user_result->gender,
                    'agent_id'   => $vls->agent_id,
                    'user_name'  => $user_result->realname ?: $user_result->nickname,
                    'user_zone'  => Zone::pidNames([$user_result->zone->id]),
                    'user_img'   => getImage($user_result->avatar, 'avatar', ''),
                    'fond_brand' => $vls->fond_brand,
                    'activity'   => $vls->activity,
                    'active'     => $vls->active,
                    'format'     => date("m/d", strtotime($vls->created_at)),
                    'created_at' => strtotime($vls->created_at),
                    'status'     => $vls->status
                ];
            }

            //对数据进行日期归类处理
            $out_put_result = array();
            foreach ($confirm_result as $key => $vls) {
                $out_put_result[$vls['format']]['format']       = $vls['format'];
                $out_put_result[$vls['format']]['data_list'][$key] = $confirm_result[$key];
            } sort($out_put_result);
               return collect($out_put_result)->sortByDesc('format');
        } else {
           return null;
        }

    }

    /**
     * 改变推荐投资人按钮状态
     * @param $param
     *
     * @return bool
     */
    public function changeCustomerButtonStatus($param)
    {
        $update_results = self::where('agent_id', $param['agent_id'])
            ->where('customer_id', $param['customer_id']);

        //判断经纪人是接受了投资人还是拒绝了
        if ($param['status'] == self::IS_READ_TYPE) {
            $update_result = $update_results->update([
                'status'  => self::IS_READ_TYPE,
                'is_read' => self::IS_READ_TYPE,
            ]);

            //对更新后的结果进行处理
            if ($update_result) {
                self::where('agent_id', '<>', $param['agent_id'])
                    ->where([
                        'customer_id' => $param['customer_id'],
                        'status'      => 0,
                    ])->update(['status' => 2]);

                //更新推送数据表的状态：接单状态，接单经纪人，读取状态
                AgentCustomerPush::where('id', $update_results->first()->post_id)
                    ->update([
                        'accept_status' => self::IS_READ_TYPE,
                        'accept_agent'  => Agent::where('id', $param['agent_id'])->first()->nickname,
                        'is_read'       => self::IS_READ_TYPE,
                        'updated_at'    => time(),
                    ]);

                //往经纪人客户表里添加数据
                $this->addAgentCustomerDatas($update_results->first());

                //根据处理结果返回结果
                return true;
            }
        } elseif ($param['status'] == self::REFUSE_TYPE) {
            $update_result = $update_results->update([
                'status'  => self::REFUSE_TYPE,
                'is_read' => self::IS_READ_TYPE,
            ]);

            //返回结果
            return true;
        }
    }


    /**
     * 往经纪人客户表里添加数据
     */
    public function addAgentCustomerDatas($param)
    {
        ############## 往经纪人客户表里添加数据 #################
        $add_data = [
            'agent_id'      => $param->agent_id,
            'uid'           => $param->customer_id,
            'level'         => 1,
            'protect_time'  => 0,
            'status'        => 0,
            'has_tel'       => 0,
            'created_at'    => time(),
            'updated_at'    => time(),
            'source'        => 5,
            'brand_id'      => $param->brand_id
        ];

        $add_result_id = AgentCustomer::insertGetId($add_data);


        ###################  往日记表里添加数据 ########################
        if ($add_result_id) {

            //日记表里需要添加的数据
            $log_add_data = [
                'agent_customer_id' => $add_result_id,
                'action'            => 1,
                'post_id'           => 0,
                'brand_id'          => $param->brand_id,
                'agent_id'          => $param->agent_id,
                'uid'               => $param->customer_id,
                'created_at'        => time(),
            ];

            AgentCustomerLog::insert($log_add_data);
        }
    }
}