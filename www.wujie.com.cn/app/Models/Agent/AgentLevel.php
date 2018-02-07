<?php namespace App\Models\Agent;

use Illuminate\Database\Eloquent\Model;

class AgentLevel extends Model
{
    protected  $table =  'agent_level';
//    protected static $lowestLevelId;
    //根据经纪人id获取级别名称
    public static function getLevelName($levelId = null){
        if(empty($levelId)){
            $levelInfo = self::orderBy('min','asc')->first();
        }
        else{
            $levelInfo = self::where('id',$levelId)->first();
        }
        if(!is_object($levelInfo)){
            return '';
        }
        return trim($levelInfo['name']);
    }

//    public static function getLowestLevelId(){
//        if(empty(self::$lowestLevelId)){
//            $levelInfo = self::orderBy('min','asc')->first();
//            self::$lowestLevelId = $levelInfo['id'];
//        }
//        return self::$lowestLevelId;
//    }
}