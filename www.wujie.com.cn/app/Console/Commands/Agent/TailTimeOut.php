<?php
/**
 * Created by PhpStorm.
 * User: yaokai
 * Date: 2017/10/13 0013
 * Time: 09:39
 */

namespace App\Console\Commands\Agent;

use App\Models\Agent\Agent;
use App\Models\Contract\Contract;
use Illuminate\Console\Command;
use DB;
use App\Models\Zone\Entity as Zone;
use \Mail;

class TailTimeOut extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tail_time_out';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '尾款超时未支付提醒';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        //找出超时未支付尾款的合同
        $contracts = Contract::tailIsTimeout();

        foreach ($contracts as $k => $v) {
            //地区处理
            $zone2_name = str_replace('市', '', $v->user->zone->name);
            $zone1_name = str_replace('省', '', Zone::pidName($v->user->zone_id));
            //没有地区返回为空
            if ($zone2_name) {
                $zone_name = '(' . $zone1_name . ' ' . $zone2_name . ')';
            } else {
                $zone_name = '';
            }

            $agent = Agent::where('id',$v->agent_id)->get();

            //名字处理
            $name = $v->user ? ($v->user->realname?:$v->user->nickname):'';
            //品牌名处理
            $brand_name = $v->brand?$v->brand->name:'';
            //尾款金额处理
            $tail_pay = number_format($v->amount - $v->pre_pay);

            $a = SendTemplateNotifi('agent_title', [], 'tail_time_out', [
                'name' => $name,
                'brand_name' => $brand_name,
                'zone_name' => $zone_name,
                'tail_pay' => $tail_pay,
            ], json_encode([
                'type' => 'tail_time_out',
                'style' => 'url',
                'value' => [
                    'contract_id' => $v->id,
                    'customer_id' => $v->uid
                ]
            ]), $agent, null, 1);

        }
    }
}

