<?php
/**活动模型
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/5/5
 * Time: 11:10
 */
namespace App\Models\Distribution;

use App\Models\Activity\Entity as Activity;
use App\Models\Activity\Ticket;
use App\Models\Guest\Relation;
use App\Models\Video;
use App\Services\DistributionService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use App\Models\Activity\Maker as ActivityMaker;
use \DB;
use App\Models\Vip\Entity as Vip;
use App\Models\User\Entity as User;
use App\Models\Brand\Entity as Brand;
use App\Models\Brand\Goods;
use App\Models\Live\Log;

class Entity extends Model
{
    protected $dateFormat = 'U';

    protected $table = 'distribution';

    //黑名单
    protected $guarded = [];

    /**
     * 判断是否整数
     * 如果是整数则返回整数部分
     * 如果不是则返回原数据
     */
    public static function Integer($num)
    {
        $ceil = ceil($num);
        $floor = floor($num);
        if ($ceil === $floor){
            return $ceil;
        }else{
            return $num;
        }
    }

    /**
     * 判断分销是否失效
     * $param $distribution_id  分销id
     * $param $distribution_deadline 分销过期时间
     *
     * return 0已失效 1未失效
     */
    public static function IsDeadline($distribution_id,$distribution_deadline)
    {
        if ($distribution_id != 0 && $distribution_deadline > time()){
            return 1;//未失效
        }
        return 0;//已失效
    }

    /**
     * 根据分销id获取分享出去的积分
     * @User yaokai
     * @param $distribution_id
     * @return int
     */
    public static function shareScore($distribution_id){
        $share_score = Action::where('distribution_id',$distribution_id)
            ->where('action','share')
            ->where('status','enable')
            ->value('trigger');

        //如果不存在返回为0
        if (!$share_score){
            return 0;
        }else{
            return $share_score;
        }

    }

}