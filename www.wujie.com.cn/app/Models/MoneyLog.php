<?php

namespace App\Models;

use App\Models\User\Withdraw;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use App\Models\User\Entity;
use Exception;
use App\Models\User\Entity as User;
class MoneyLog extends Model
{
    //

    protected $table = 'money_log';
    protected $guarded = ['id'];

    protected function getDateFormat()
    {
        return date(time());
    }


    public static  function extractMoney($uid, $rebate, $account_type, $account, $realname, $bank_name, $contract_id)
    {
        $account_type=='ali' && $account_type='alipay';
        $user = User::find($uid);

        if($rebate<=0){
            throw new \Exception('异常，或者之前你已申请过提现！');
        }

        if($user->money<$rebate){
            throw new \Exception('余额不足！');
        }

        $return = Withdraw::create([
            'uid' => $uid,
            'money' => $rebate,
            'source' => 'money',
            'source_num' => $rebate,
            'account_type' => $account_type,
            'account' => $account,
            'name' => $realname,
            'bank_name' => $bank_name,
            'relation_type' => 1,
            'relation_id' => $contract_id,
        ]);


        //提交申请成功,减少相应的无界币
        User::where('uid', $uid)->decrement('money', $rebate);
        //现金记录
        self::create([
            'uid' => $uid,
            'operation' => '-1',
            'num' => $rebate,
            'relation_type' => '2',
            'relation_id' => $return->id,
            'action' => '2',
        ]);


        return $return;
    }


}
