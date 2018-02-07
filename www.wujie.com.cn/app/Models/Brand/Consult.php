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
use phpDocumentor\Reflection\DocBlock\Type\Collection;

class Consult extends Model
{

    protected $dateFormat = 'U';

    protected $table = 'brand_consult';

    //黑名单
    protected $guarded = [];


    public function user()
    {
        return $this->hasOne('App\Models\User\Entity', 'uid', 'uid');
    }


    public function brand()
    {
        return $this->hasOne('App\Models\Brand\Entity', 'id', 'brand_id');
    }



    /**
     * 作用:批量的获得洽讯
     * 参数:$id 品牌id
     *
     * 返回值:
     */
    public static function singles()
    {
        $query = self::with(['user'=>function($query){ $query->select('uid','nickname');}])
            ->with(['brand'=>function($query){ $query->select('id','name');}])
            ->select('uid', 'brand_id');

        return $query;
    }



    /**
     * 作用:批量的加工
     * 参数:$collect 结果集
     *
     * 返回值:
     */
    public static function process($collect)
    {
        foreach($collect as $k=> $v){
            if($v->user->nickname){
                $v->nickname = $v->user->nickname;
            }else{
                $v->nickname = '匿名用户';
            }
            $v->brand_name = $v->brand->name;
            $v->created_at_format = date('Y-m-d H:i:s', $v->created_at->timestamp);
            if($v->type == 'intent' && $v->status=='finish'){
                $v->type = 'intent_success';
            }
            unset($v->user, $v->brand);
        }

        return $collect;
    }

    /**
     * 跟据品牌id获取意向电话数量
     */
    public static function countTel($id)
    {
        $count_tel = self::where('brand_id', $id)
            ->count();
        return $count_tel;
    }


}