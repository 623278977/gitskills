<?php
/**活动模型
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/5/5
 * Time: 11:10
 */
namespace App\Models\Doubt;

use App\Http\Controllers\Api\BrandController;
use App\Models\Distribution\Action;
use App\Models\User\Favorite;
use App\Models\User\Industry;
use App\Models\Zone;
use App\Services\Version\Brand\_v020400;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use \DB , Closure ,Input;
class Entity extends Model
{

    protected $dateFormat = 'U';

    protected $table = 'doubt';

    //黑名单
    protected $guarded = [];





}