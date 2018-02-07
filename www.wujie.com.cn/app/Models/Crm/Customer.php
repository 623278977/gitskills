<?php
/**活动模型
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/5/5
 * Time: 11:10
 */
namespace App\Models\Crm;

use App\Models\Activity\Entity as Activity;
use App\Models\Activity\Ticket;
use App\Models\CurrencyLog;
use App\Models\Guest\Relation;
use App\Models\ScoreLog;
use App\Models\Video;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use App\Models\Activity\Maker as ActivityMaker;
use \DB;
use App\Models\Vip\Entity as Vip;
use App\Models\User\Entity as User;
use App\Models\Brand\Entity as Brand;
use App\Models\Brand\Goods;
//use App\Models\Live\Log;
use App\Models\Distribution\Log as DistributionLog;
use App\Models\Share\Log as ShareLog;
use App\Models\Distribution\ActionBind;
class Customer extends Model
{
    protected $dateFormat = 'U';

    protected $table = 'customer';

    //黑名单
    protected $guarded = [];

    protected $connection = 'crm';


    public static function findOrCreate($tel, Array $data)
    {
        $customer = self::where('tel', $tel)->first();
        if(is_object($customer)){
            return ['user'=>$customer,'is_exist'=>1];
        }else{
            $data ['tel'] = $tel;
            $customer = self::create($data);
            return ['user'=>$customer,'is_exist'=>0];
        }
    }




}