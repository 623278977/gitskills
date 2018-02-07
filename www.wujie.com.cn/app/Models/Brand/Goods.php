<?php
/**活动模型
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/5/5
 * Time: 11:10
 */
namespace App\Models\Brand;

use App\Models\User\Entity as User;
use App\Models\User\Share;
use App\Models\User\Ticket as UserTicket;
use App\Models\Zone;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use App\Models\Activity\Maker as ActivityMaker;
use App\Models\Vip\Entity as Vip;
use \DB;
use Monolog\Handler\CouchDBHandlerTest;
use App\Models\ScoreLog;
use App\Models\Activity\Sign as ActivitySign;
use App\Models\Activity\Ticket as ActivityTicket;
use App\Models\Message;
use App\Models\Live\Entity as Live;
use App\Models\Live\Subscribe;
class Goods extends Model
{

    public $timestamps = false;

    protected $table = 'live_brand_goods';

    //黑名单
    protected $guarded = [];

    public function live()
    {
        return $this->hasOne('App\Models\Live\Entity', 'id', 'live_id');
    }

    public function brand()
    {
        return $this->hasOne('App\Models\Brand\Entity', 'id', 'brand_id');
    }

    /*
    * 作用：减少物品数量
    * 参数：$num 减少的数量 $goods_id 物品id
    * 返回值：bool
    */
    public static function reduceNum($num, $goods_id)
    {
        $goods = self::where('id', $goods_id)->first();
        if($goods->num<$num){
            return false;
        }

        self::where('id', $goods_id)->decrement('num', $num);
        return true;
    }

}