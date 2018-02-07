<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Zone extends Model
{
    //

    protected $table = 'zone';

    static $_municipalities = array(
        '北京市',
        '上海市',
        '重庆市',
        '天津市'
    );

    public $timestamps = false;

    /*
 * 获取全部地区对象
 */
    static function selectAllZones($cache = 1, $param = [])
    {
        if (!Cache::has('all_zone') || $cache) {
            $builder = Zone::whereRaw("status=1 and name not in ('钓鱼岛地区')")->orderBy('sort', 'desc');
            foreach ($param as $key => $search) {
                if (empty($search)) {
                    continue;
                } else {
                    $builder->where('name', 'like', '%' . $search . '%');
                }
            }
            $zones = $builder->get();
            Cache::put('all_zone', $zones, 1440);
        }
        return Cache::get('all_zone');
    }

    /**
     * @param $zone_id
     * 获取父级城市名称
     */
    static function getZone($zone_id)
    {
        if (empty($zone_id)) return '';
        $zone_name = Cache::has('zone_name_' . $zone_id) ? \Cache::get('zone_name_' . $zone_id) : false;
        if ($zone_name === false) {
            $zone = self::getRow(array('id' => $zone_id));
            if (!isset($zone->id)) {
                $zone_name = '';
            } else {

                if (!$zone->upid) {
                    $zone_name = $zone->name;
                } else {
                    $parent_zone = self::getRow(array('id' => $zone->upid));
                    if (!isset($parent_zone->id)) {
                        $zone_name = '';
                    } else {
                        if (in_array($parent_zone->name, self::$_municipalities)) {
                            $zone_name = $parent_zone->name;
                        } else {
                            $zone_name = $zone->name;
                        }
                    }
                }
            }
            \Cache::put('zone_name_' . $zone_id, $zone_name, 86400);
        }
        return Cache::get('zone_name_' . $zone_id);

    }

    static function getRow($where)
    {
        return self::where($where)->where('status', 1)->first();
    }

    /**
     * @param $zone_id
     * 子id数组  用于筛选的时候
     */
    static function getZoneIds($zone_id)
    {
        if (empty($zone_id)) return array();
        $zone = self::getRow(array('id' => $zone_id));
        if (!isset($zone->id)) return array();
        if ($zone->upid) {
            //二级
            return array($zone_id);
        } else {
            //一级
            $zone_ids = Zone::where('upid', $zone->id)->lists('id')->toArray();
            $zone_ids[] = $zone_id;
            return $zone_ids;
        }
    }

    /**
     * @param $type
     * 江浙沪:1  珠三角:2 长三角:3 港澳台:4
     */
    static function cityAliasZoneid($type)
    {
        $zone_ids = array();
        switch ($type) {
            case '1' or '3'://江苏省 浙江省 上海市
                $jiangshu = Zone::getRow(array('name' => '江苏省'));
                $zhejiang = Zone::getRow(array('name' => '浙江省'));
                $shanghai = Zone::getRow(array('name' => '上海市'));
                $zones = Zone::where('upid', $jiangshu->id)->orWhere('upid', $zhejiang->id)->orWhere('upid', $shanghai->id)->get()->toArray();
                $zone_ids = array_column($zones, 'id');
                $zone_ids[] = $jiangshu->id;
                $zone_ids[] = $zhejiang->id;
                $zone_ids[] = $shanghai->id;
                break;
            case '2'://广州、深圳 、佛山 、东莞、中山、珠海、江门、肇庆、惠州
                $city_name_array = array("广州市", "深圳市", "佛山市", "东莞市", "中山市", "珠海市", "江门市", "肇庆市", "惠州");
                foreach ($city_name_array as $k => $v) {
                    $city_zone = Zone::getRow('name', $v);
                    $zone_ids[] = $city_zone->id;
                }
                break;
            case 4:
                $xg = Zone::getRow(array('name' => '香港'));
                $tw = Zone::getRow(array('name' => '台湾省'));
                $om = Zone::getRow(array('name' => '澳门'));
                $zones = Zone::where('upid', $xg->id)->orWhere('upid', $tw->id)->orWhere('upid', $om->id)->get()->toArray();
                $zone_ids = array_column($zones, 'id');
                $zone_ids[] = $xg->id;
                $zone_ids[] = $tw->id;
                $zone_ids[] = $om->id;
                break;
        }
        return $zone_ids;
    }

    /**
     * 根据地区名返回zone_id
     * @param $zoneName
     * @return mixed
     */
    static function getZoneId($zoneName)
    {
        $all_zones = self::selectAllZones();
        foreach ($all_zones as $item) {
            if ($item->name == $zoneName)
                return $item->id;
        }
    }


    /**
     * 获取兄弟地区
     */
    static function brothers($id, $cache = 0)
    {
        $zones = \Cache::has('zone_brothers' . $id) ? \Cache::get('zone_brothers' . $id) : false;

        if ($zones === false || $cache) {
            $my = self::where('id', $id)->first();
            if (!is_object($my)) {
                $data = [];
            } else {
                $data = self::where('upid', $my->upid)->get();
            }
            $zones = $data;
            \Cache::add('zone_brothers' . $id, $data, 86400);
        }
        return $zones;
    }


    public static function getselfandparent($zone_id)
    {
        $self = self::find($zone_id);

        if (!$self) {
            return '';
        }

        if ($self->upid) {
            $pzone = self::find($self->upid);
            $zone_name = abandonProvince($pzone->name) . ' ' . abandonProvince($self->name);
        } else {
            $zone_name = abandonProvince($self->name);
        }

        return $zone_name;
    }

}
