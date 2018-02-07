<?php
/****测试控制器********/

namespace App\Http\Controllers\Api;

use App\Models\Orders\Entity as Orders;
use App\Jobs\SendRemindSMS;
use App\Models\Agent\AgentAchievement;
use App\Models\Agent\AgentAchievementLog;
use App\Models\Agent\ContractPayLog;
use App\Models\Agent\Entity\_v010200;
use App\Models\Agent\Invitation;
use App\Models\Agent\Score\AgentScoreLog;
use App\Models\Contract\Contract;
use App\Models\RedPacket\RedPacketPerson;
use App\Models\User\Praise;
use Illuminate\Http\Request;
use Queue;
use DB;
use App\Models\Agent\BrandAgentCompleteQuiz;
use App\Models\User\Entity as User;
use Illuminate\Support\Str;
use Carbon\Carbon;
use App\Models\Agent\Entity\_v010200 as Agentv010200;
use App\Models\Agent\Agent;
use App\Models\User\Withdraw;
class TestController extends CommonController
{


    public function anyTest(Request $request)
    {

        $Withdraw = Withdraw::find(55);
        $Withdraw->relation_type=1;
        $Withdraw->relation_id=99;
        $Withdraw->account_type='alipay';
        $Withdraw->save();
        dd(4);

        $orders_new = Orders::getRows(['status'=>'npay']);

        foreach($orders_new as $k=>$v){
            if(($v->created_at->timestamp+1800) < time()){
                if($v->hasOneOrdersItems->type!='contract'){
                    Orders::updateOrderByField(['status'=>'expire'], ['id'=>$v->id]);
                }

                //返回积分
                ScoreLog::add($v->uid, $v->score_num, 'nopay_order_return', '未支付订单积分返回', 1, false, 'orders', $v->id);
                //返还商品数量
                $items = Items::getByNo($v->order_no);
                foreach($items as $key=>$val){
                    Goods::where('id', $val->product_id)->increment('num', 1);
                }
            }
        }

        return response($res);
    }


    private function sonTree( $arr,$non_reversible)
    {
        static $Tree = array(); //只会初始化一次
        foreach($arr as $k=>$v) {
            if($v['register_invite'] == $non_reversible) {
                $Tree[] = $v;
                $this->sonTree($arr,$v['non_reversible']);
            }
        }

        return $Tree;
    }



}