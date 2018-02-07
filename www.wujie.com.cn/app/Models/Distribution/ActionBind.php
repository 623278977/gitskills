<?php

/*
 * 分销动作绑定
 */

namespace App\Models\Distribution;
use App\Models\Distribution\Action\V020700 as Actionv020700;
use App\Models\Activity\Entity as Activity;
use App\Models\News\Entity as News;
use App\Models\Brand\Entity as Brand;
use App\Models\Live\Entity as Live;
use App\Models\Video;
class ActionBind extends \Illuminate\Database\Eloquent\Model
{

    protected $table = 'distribution_action_bind';
    protected $guarded = ['id'];
    public $timestamps = false;

    public function action()
    {
        return $this->hasOne('App\Models\Distribution\Action', 'id', 'distribution_action_id');
    }


    /**
     * 按照分销id获取下面的动作，并排序好再返回
     * @param $distribution_id
     *
     * @return mixed
     */
    public static function getRules( $relation_type, $relation_id)
    {
        if($relation_type=='activity'){
           $entity = Activity::find($relation_id, ['distribution_id']);
        }elseif($relation_type=='news'){
            $entity = News::find($relation_id, ['distribution_id']);
        }elseif($relation_type=='brand'){
            $entity = Brand::find($relation_id, ['distribution_id']);
        }elseif($relation_type=='video'){
            $entity = Video::find($relation_id, ['distribution_id']);
        }elseif($relation_type=='live'){
            $entity = Live::find($relation_id, ['distribution_id']);
        }


        $action_bind = self::where('relation_type', $relation_type)->where('relation_id', $relation_id)->where('status', 'enable')
            ->get();

        $actions =  Actionv020700::whereIn('id', array_pluck($action_bind, 'distribution_action_id'))->where('distribution_id', $entity->distribution_id)
            ->where('status', 'enable')->select('action', 'give', 'trigger', 'describe')->get()->toArray();

        $arr = ['share', 'relay', 'view', 'watch', 'enroll', 'sign', 'intent'];

        $func = function ($before, $after) use ($arr)
        {
            return (array_search($before['action'], $arr) < array_search($after['action'], $arr)) ? -1 : 1;
        };
        //排序
        usort($actions, $func);

        foreach($actions as $k=>&$v){
            $v['describe'] = trim($v['describe']);
        }

        return $actions;
    }


}
