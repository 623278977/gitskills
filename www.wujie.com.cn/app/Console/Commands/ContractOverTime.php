<?php

/*
 * 用户签到推送相关
 */

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputArgument;
use App\Models\Contract\Contract;

class ContractOverTime extends Command {

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'contract_over_time';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '合同还有两天就要过期，给c端投资人发送过期提醒';


    /*合同还有两天就要过期，给c端投资人发送过期提醒*/
    public function handle() {
        $nowTime = time();
        $contractInfo = Contract::with('agent.zone','brand','user')
            ->where(function($query)use($nowTime){
                $query->where('status',0);
                $query->where('created_at','<',$nowTime - 8*86400);
                $query->where('created_at','>',$nowTime - 10*86400);
            })->get();
        foreach ($contractInfo as $oneContract){
            $agentInfo = $oneContract->getRelations()['agent'];
            $city = Zone::getCityAndProvince($agentInfo['zone_id']);
            $brandInfo = $oneContract->getRelations()['brand'];
            $userInfo = $oneContract->getRelations()['user'];
            SendTemplateNotifi('wjsq_title',[],'contract_over_time',[
                'nickname'=> trim($agentInfo['nickname']),
                'city'=> trim($city),
                'brandName'=> trim($brandInfo['name']),
            ],json_encode([
                'type'=>'contract_over_time',
                'style' => 'url',
                'value' => $oneContract['id'],
            ]),$userInfo,null);
//            send_notification('无界商圈', "经纪人：{$agentInfo['nickname']}（{$city}）向你发出的 [ {$brandInfo['name']} ] 付款协议即将过期。请尽快确认加盟合同！",
//                json_encode(['type'=>'contract_over_time', 'style'=>'json',
//                    'value'=>"/webapp/client/pactdetails/_v020800?contract_id={$oneContract['id']}"]),
//                $userInfo,null);
        }
    }
}
