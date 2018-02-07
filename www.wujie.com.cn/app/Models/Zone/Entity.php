<?php namespace App\Models\Zone;

use Illuminate\Database\Eloquent\Model;
use App\Models\Zone as Zones;
use \Cache;
use DB;

class Entity extends Model
{
    protected $table ='zone';

    public static  $stance = null;
    public static function instance()
    {
        if (is_null(self::$stance)) {
            self::$stance = new self;
        }
        return self::$stance;
    }

    public function pzone()
    {
        return $this->hasOne('App\Models\Zone\Entity','id','upid');
    }

    /**
     * 获取地区信息并缓存
     * @param int $cache
     * @return array|bool
     */
    static function cache($cache = 0){
        $zones =  \Cache::has('zones') ? \Cache::get('zones') : false;

        if ($zones === false || $cache)
        {
            $data = array();
            $zones = self::where('status',1)->get()->toArray();
            foreach ($zones as $zone) {
                $data[$zone['id']] = $zone;
            }
            $zones = $data;
            \Cache::add('zones', $data,86400);
        }
        return $zones;
    }

    /**
     * 获取兄弟地区
     */
    static function brothers($id,$cache = 0)
    {
        $zones =  \Cache::has('zone_brothers'.$id) ? \Cache::get('zone_brothers'.$id) : false;

        if ($zones === false || $cache)
        {
            $my = self::where('id',$id)->first();
            if(!is_object($my)){
                $data = [];
            }else{
                $data = self::where('upid', $my->upid)->get();
            }
            $zones = $data;
            \Cache::add('zone_brothers'.$id, $data,86400);
        }
        return $zones;
    }

    /**获取父级地区名*/
    public static function pidName($id)
    {
        $upid = self::where('id',$id)->value('upid');
        if($upid != 0){
            $name = self::where('id',$upid)->value('name');
        }else{
            $name = '';
        }
        return $name;
    }

    /**获取父级地区和子地区
     *
     * @param $id
     * @return string
     */
    public static function pidNames($id)
    {
        //如果ID不存在返回空
        if (is_null($id) || empty($id)) {
            return "";
        }

        $results = "";
        $upid_and_name = self::whereIn('id', $id)
            ->select('upid', 'name')
            ->get()->toArray();

        $result  = array();
        foreach ($upid_and_name as $key => $vls) {
            if ($vls['upid'] != 0) {
                $result[$key][] = self::where('id', $vls['upid'])->value('name');
            }
            $result[$key][] = $vls['name'];

            foreach ($result as $k => $vs) {
               $results = implode(' ', $vs);
            }
        }

        return $results;
    }

    /**
     * 活动举办地
     * 0 代表是用orm查询 activity,
     * 1 代表用DB查询 activity
     *
     * param $activity
     *
     * return array
     */
    public function getZone($activity, $type = 1)
    {
        $activity = (object)$activity;

        if (!isset($activity->id)) return array();

        $data = array();
        if ($type == 0) {
            $makers = [];

            if (count($activity->makers)) {
                foreach ($activity->makers as $k => $v) {
                    if (isset($v->zone->name)) {
                        $data[] = Zones::getZone($v->zone->id);
                        $makers[$v->zone->id]=$v->id;
                    }
                }
            }
        } else {
            $makers = DB::table('activity_maker')
                ->leftJoin('maker', 'maker.id', '=', 'activity_maker.maker_id')
                ->leftJoin('zone', 'zone.id', '=', 'maker.zone_id')
                ->where('activity_maker.status', 1)
                ->where('activity_maker.activity_id', $activity->id)
                ->lists('zone.id', 'activity_maker.maker_id');

            foreach (array_values($makers) as $k => $v) {
                $data[] = Zones::getZone($v);
            }
        }
        $citys = [];
        foreach ($data as $bond => $value) {
            if ('市' == mb_substr($value, -1, 1)) {
                $citys[] = mb_substr($value, 0, -1);
            }
            if ('区' == mb_substr($value, -1, 1)) {
                $zone = DB::table('maker')
                    ->leftJoin('zone', 'maker.zone_id', '=', 'zone.id')
                    ->where('maker.id', (array_keys($makers)[$bond]))->first();
                if (!$zone) continue;
                if ($zone->level == 2 && in_array($zone->upid, [1, 2, 9, 22])) {
                    $uzone   = DB::table('zone')->where('id', $zone->upid)->first();
                    $citys[] = mb_substr($uzone->name, 0, -1);
                } else {
                    $citys[] = $value;
                }
            }
        }

        return array_values(array_unique($citys));
    }


    /**
     * 获取本级和上级城市
     *
     * @param $zone_id
     * @return mixed
     * @author tangjb
     */
    public static function getCityAndProvince($zone_id)
    {
        if(!$zone_id){
            return "";
        }

        $res = Cache::get(__METHOD__.$zone_id);

        if($res){
            return $res;
        }

        $zone = self::where('id', $zone_id)->first();
        if(!$zone){
            return '';
        }

        $str = abandonProvince($zone->name);

        if($zone->upid){
            $p_zone = self::where('id', $zone->upid)->first();
            $str = abandonProvince($p_zone->name).' '. abandonProvince($str);
        }

        Cache::put(__METHOD__.$zone_id, $str, 60*24*30);

        return $str;
    }


}
