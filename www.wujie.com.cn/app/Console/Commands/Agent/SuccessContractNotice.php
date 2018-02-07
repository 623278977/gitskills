<?php namespace App\Console\Commands\Agent;

use App\Models\Contract\Contract;
use Illuminate\Console\Command;
use App\Models\Zone\Entity as Zone;
use App\Models\Agent\AgentCustomer;

class SuccessContractNotice extends Command
{
    protected $signature = 'Agent:SuccessContractNotice';

    const ACCEPT_TYPE           = 1;     //接受
    const CONFIRM_CONTRACT_TYPE = 2;     //成功加盟品牌类型
    const BAD_TYPE              = -1;    //拒绝、失去类型
    //const INVITE_TYPE           = [1, 2, 3, 4, 6, 7];    //邀请类型

    /**
     * 某个经纪人的邀请投资人成功加盟品牌时，进行的消息提示
     */
    public function handle()
    {
        //执行发送数据信息
        $this->gainCustomerLeagueBrandDatasToSendNotice();
    }

    /**
     * 获取投资人成功加盟品牌后的数据信息，并发送消息
     */
    protected function gainCustomerLeagueBrandDatasToSendNotice()
    {
        $query_result = Contract::with('brand', 'user')
            ->where('status', self::CONFIRM_CONTRACT_TYPE)
            ->first();

        //对结果进行处理
        if ($query_result && !is_null($query_result->user)) {
            $gain_result = $this->gainCustomerInviteAgent($query_result->user->uid);

            //对返回的结果进行处理
            if ($gain_result) {
                $this->sendInfos($gain_result, $query_result);
            }
        }
    }

    /**
     * 获取投资人的邀请经纪人信息
     */
    protected function gainCustomerInviteAgent($uid)
    {
        $query_result = AgentCustomer::where('uid', $uid)
            ->where('level',  '<>', self::BAD_TYPE)
            ->where('status', '<>', self::BAD_TYPE)
            ->whereIn('source', [1, 2, 3, 4, 6, 7])
            ->first();

        //对结果进行处理
        if (is_object($query_result)) {
            return $query_result;
        } else {
            return false;
        }
    }

    /**
     * 发送消息
     *
     * @param agent：消息的接受经纪人
     * @param param：发送消息需要的数据信息
     */
    protected function sendInfos($agent, $param)
    {
        //对接受到的品牌进行判断处理
        if (!is_null($param->user) && !is_null($param->brand) &&
            $param->brand->agent_status == self::ACCEPT_TYPE) {

            //数组需要发送的数据信息
            $_info = trans('tui.success_contract_info_notice', [
                'customer_name' => $param->user->realname ?: $param->user->nickname,
                'zone_name'     => Zone::pidNames([$param->user->zone_id]),
                'brand_name'    => $param->brand->name,
            ]);

            //发送消息
            send_notification('无界商圈', $_info, json_encode([
                'type' => 'success_contract_notice',
                'style' => 'json',
                'value' => $param->user->uid
            ]), $agent, true);
        }
    }
}