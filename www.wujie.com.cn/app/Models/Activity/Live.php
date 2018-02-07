<?php
/**活动模型
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/5/5
 * Time: 11:10
 */
namespace App\Models\Activity;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Live extends Model
{

    protected $dateFormat = 'U';

    protected $table = 'live';

    //黑名单
    protected $guarded = [];

    static function getBase($live){
        if(!isset($live->id)) return array();
        $data=array();
        $data['id']=$live->id;
        $data['subject']=$live->subject;
        $data['url']=createUrl('live/detail',array(
            'id'=>$live->id,
            'pagetag'=>config('app.live_detail')
        ));;
        return $data;
    }
}