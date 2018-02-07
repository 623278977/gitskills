<?php
/**活动模型
 * Created by PhpStorm.
 * User: yaokai
 * Date: 2017/9/11
 * Time: 14:40
 */
namespace App\Models\Brand;

use App\Models\Agent\Agent;
use Illuminate\Database\Eloquent\Model;
use \DB;

class Contactor extends Model
{

    protected $dateFormat = 'U';

    protected $table = 'brand_contactor';

    //黑名单
    protected $guarded = [];


    public function agent()
    {
        return $this->hasOne(Agent::class, 'id', 'agent_id');
    }


}