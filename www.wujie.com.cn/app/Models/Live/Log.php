<?php
/**活动模型
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/5/5
 * Time: 11:10
 */
namespace App\Models\Live;

use App\Models\Activity\Entity as Activity;
use App\Models\Activity\Ticket;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use App\Models\Activity\Maker as ActivityMaker;
use \DB;
use App\Models\Vip\Entity as Vip;
use App\Models\Brand\Entity as Brand;
use App\Models\Brand\Goods;

class Log extends Model
{

    protected $dateFormat = 'U';

    protected $table = 'log_live';

    //黑名单
    protected $guarded = [];







}