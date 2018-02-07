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
class Intent extends Model
{

    protected $dateFormat = 'U';

    protected $table = 'brand_intent';

    //黑名单
    protected $guarded = [];

}