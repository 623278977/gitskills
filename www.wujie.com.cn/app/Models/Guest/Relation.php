<?php
/**活动模型
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/5/5
 * Time: 11:10
 */
namespace App\Models\Guest;

use Illuminate\Database\Eloquent\Model;


class Relation extends Model
{

    protected $dateFormat = 'U';

    protected $table = 'guest_relation';

    //黑名单
    protected $guarded = [];


    public function guest()
    {
        return $this->hasOne('App\Models\Guest\Entity', 'id', 'guest_id');
    }


    public static  function getGuests($relation_id, $relation_type)
    {
        $relation = Relation::with(['guest'=>function($query){ $query->select('id','name', 'image', 'brief');}])
            ->where(['relation_id'=>$relation_id, 'relation_type'=>$relation_type])->get();
        $guests = [];
        foreach($relation as $k=>$v){
            $v->guest->image = getImage($v->guest->image, 'activity', '', 0);
            $guests[] = $v->guest;
        }

        return $guests;
    }

}