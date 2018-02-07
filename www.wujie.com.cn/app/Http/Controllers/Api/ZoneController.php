<?php
/****广告banner控制器********/

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\CommonController;
use App\Models\Maker\Entity;
use App\Models\Zone;
use Illuminate\Http\Request;

class ZoneController extends CommonController
{
    /**
     * 获取全部地区
     *
     * @return Response
     */
    public function postAjaxallzones(Request $request, $version=null)
    {
        $rows = array();
        $zones = Zone::selectAllZones();
        $i = 0;
        foreach ($zones as $k => $v) {
            if (!$v->upid) {
                $rows[$i]['id'] = $v->id;
                $rows[$i]['hot'] = $v->hot ? 1 : 0;
                $rows[$i]['recommend'] = $v->recommend ? 1 : 0;
                $rows[$i]['name'] = $v->name;
                $rows[$i]['pinyin'] = $v->pinyin;

                $upzones = Zone::where('status', '=', '1')->where('upid', '=', $v->id)->get();

                foreach ($upzones as $a => $b) {
                    $rows[$i]['cityList'][$a]['id'] = $b->id;
                    $rows[$i]['cityList'][$a]['pinyin'] = $b->pinyin;
                    $rows[$i]['cityList'][$a]['name'] = $b->name;
                    $rows[$i]['cityList'][$a]['hot'] = $v->hot ? 1 : 0;
                    $rows[$i]['cityList'][$a]['recommend'] = $v->recommend ? 1 : 0;
                }

                //如果是直辖市，就直接放入
                if (in_array($v->name, ['北京市', '上海市', '天津市', '重庆市']) && in_array($version, ['_v010000', '_v010001'])) {
                    $extra = ['id'=>$v->id, 'pinyin'=>$v->pinyin, 'name'=>$v->name, 'hot'=>$v->hot ? 1 : 0, 'recommend'=>$v->recommend ? 1 : 0];
                    array_unshift($rows[$i]['cityList'], $extra);
                }

                $i++;
            }
        }
        $type = $request->input('type', '');
        if ($type == 'register') {
            $rows = $this->registerZones($request, $rows);
        } else if ($type == 'brand') {
            $rows = $this->brandZones($rows);
        }
        return AjaxCallbackMessage($rows, true, '');
    }

    /**
     * 获取有创客空间的地区合集
     */
    public function postAjaxmakerszones(Request $request)
    {
        $rows = array();
        $makers = Entity::whereRaw("status=1")->get();
        $store_zone_ids = array();
        $zhixiashi = array('北京市', '天津市', '上海市', '重庆市');
        foreach ($makers as $k => $v) {
            $store_zone_ids[$k]['id'] = $v->zone_id;
            $uZone = Zone::where('id', '=', $v->zone_id)->first();
            $store_zone_ids[$k]['upid'] = isset($uZone->upid) ? $uZone->upid : 0;
        }
        $zones = Zone::whereRaw("status=1 and name not in ('钓鱼岛地区','南沙群岛地区','全国')")->get();
        foreach ($zones as $k => $v) {
            if (!$v->upid) {//第一级
                foreach ($store_zone_ids as $m => $n) {
                    if ($v->id == $n['upid']) {
                        $rows[$k]['id'] = $v->id;
                        $rows[$k]['name'] = $v->name;
                        $upzones = Zone::where('status', '=', 1)->where('upid', '=', $v->id)->get();
                        foreach ($upzones as $a => $b) {
                            if ($b->id == $n['id']) {
                                //判断是否是直辖市  直辖市只显示第一级
                                $upZone = Zone::where('id', $b->upid)->first();
                                if (isset($upZone->id) && in_array($upZone->name, $zhixiashi)) {
                                    //直辖市
                                    $rows[$k]['cityList'][$a]['id'] = $upZone->id;
                                    $rows[$k]['cityList'][$a]['name'] = $upZone->name;
                                    $rows[$k]['cityList'][$a]['pinyin'] = $upZone->pinyin;
                                } else {
                                    $rows[$k]['cityList'][$a]['id'] = $b->id;
                                    $rows[$k]['cityList'][$a]['name'] = $b->name;
                                    $rows[$k]['cityList'][$a]['pinyin'] = $b->pinyin;
                                }

                            }

                        }
                    }
                }

            }
            if (Zone::where('id', '=', $v->id)->first()->name == "全国") {
                $rows[$k]['id'] = $v->id;
                $rows[$k]['name'] = $v->name;
                $upzones = Zone::where('status', '=', 1)->where('upid', '=', $v->id)->get();
                foreach ($upzones as $a => $b) {
                    $rows[$k]['cityList'][$a]['id'] = $b->id;
                    $rows[$k]['cityList'][$a]['name'] = $b->name;
                    $rows[$k]['cityList'][$a]['pinyin'] = $b->pinyin;

                }
            }
        }
        $i = 0;
        $data = array();
        foreach ($rows as $k => $v) {
            $data[$i]['id'] = $v['id'];
            $data[$i]['name'] = $v['name'];
            $j = 0;
            $ids = array();
            if (isset($v['cityList']) && count($v['cityList'])) {
                foreach ($v['cityList'] as $m => $n) {
                    if (!in_array($n['id'], $ids)) {
                        $data[$i]['cityList'][$j]['id'] = $n['id'];
                        $data[$i]['cityList'][$j]['name'] = $n['name'];
                        $rows[$i]['cityList'][$j]['pinyin'] = $n['pinyin'];
                        $ids[] = $n['id'];
                    }
                    $j++;
                }
            }
            $i++;
        }
        return AjaxCallbackMessage($data, true, '');
    }

    /*
    * 获取城市,不包括江浙沪等区域性地方.
    */
    public function registerZones(Request $request, $zones)
    {
        $return = [];
        foreach ($zones as $zone) {
            if (in_array($zone->name, ['北京市', '上海市', '天津市', '重庆市', '海外', '澳门', '香港', '台湾省'])) {
                unset($zone->cityList);
                $return[] = $zone;
            }
            foreach ($zone->cityList as $city) {
                $return[] = $city;
            }
        }
        $collection = collect($return);
        $return = $collection->sortBy('pinyin')->values()->all();
        $name = $request->input('name', '');
        if ($name) {
            $return = [];
            foreach ($collection as $item) {
                if (strpos($item->name, $name) !== FALSE) {
                    $return[] = $item;
                } else if (strpos($item->pinyin, $name) !== FALSE) {
                    $return[] = $item;
                }
            }
        }
        return $return;
    }

    /*
     * 品牌选择
     */
    public function brandZones($zones)
    {
        $return = [];
        $all = Zone\Entity::where(['name' => '全国'])->first();//dd($all);
        $overseas = Zone\Entity::where(['name' => '海外'])->first();//dd($all);
        foreach ($zones as &$zone) {
            if (in_array($zone['name'], ['其他', '江浙沪', '京津冀'])) {
                continue;
            }
            if (in_array($zone['name'], ['北京市', '上海市', '天津市', '重庆市', '海外', '澳门', '香港', '台湾省', '其他', '南沙群岛地区', '全国', '江浙沪', '京津冀'])) {
                unset($zone['cityList']);
                $data = $zone;
                $zone['cityList'][] = $data;
                $return[] = $zone;
            } else {
                $data = $zone;
                unset($data['cityList']);
                array_unshift($zone['cityList'], $data);
                $return[] = $zone;
            }
            if ($zone['name'] == '全国') {
                array_unshift($return, $zone);
            }
        }
        array_pop($return);
        //$zone = [
        //  "id"=> $all->id,
        //  "hot"=>$all->hot,
        //  "recommend"=>$all->recommend,
        //  "name"=>$all->name,
        //  "pinyin"=>$all->pinyin,
        //  "cityList"=> [["id"=> $all->id, "hot"=> $all->hot, "recommend"=> $all->recommend, "name"=> $all->name, "pinyin"=> $all->pinyin]]
        //];
        //array_unshift($return,$zone);
        //$overseas_zone = [
        //		"id"=> $overseas->id,
        //		"hot"=>$overseas->hot,
        //		"recommend"=>$overseas->recommend,
        //		"name"=>$overseas->name,
        //		"pinyin"=>$overseas->pinyin,
        //		"cityList"=> [["id"=> $overseas->id, "hot"=> $overseas->hot, "recommend"=> $overseas->recommend, "name"=> $overseas->name, "pinyin"=> $overseas->pinyin]]
        //];
        //array_push($return,$overseas_zone);
        //array_unique($return);
        return $return;
    }
}