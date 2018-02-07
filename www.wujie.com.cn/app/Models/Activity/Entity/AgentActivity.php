<?php
/**活动模型
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/5/5
 * Time: 11:10
 */

namespace App\Models\Activity\Entity;

use App\Models\Activity\Brand;
use App\Models\Activity\Sign;
use \DB, Closure, Input;
use App\Models\Activity\Entity;
use App\Services\Version\Activity\_v020700 as ActivityV020700;
use App\Models\Agent\Activity\Sign as AgentActivitySign;
class AgentActivity extends Entity
{

    /**
     * 投资人活动列表格式化数据
     */
    static public function agentActivityList($input)
    {
        $page = Input::input('page', 1);
        $page_size = Input::input('page_size', 10);
        $hotwords = Input::input('hotwords', '');

        //判断数据是否足够
        //进行中的活动和将来的活动数量
        $count = self::getActivityCount('1');
//        dd($count);
        //
        if ($count < $page_size * $page && $page_size * $page - $count < $page_size) {//进行中活动缺少条数大于分页则取部分结束活动
            //没开始的活动和进行中的活动
            $activity_ing = self::getActivityListOfMaker('', '1', $page_size, '', '', $page,'',$hotwords,'',true);
            $end_activity = self::getActivityListOfMaker('', '2', $page_size, '', '', $page, '', $hotwords, $count,true);
        } elseif ($count < $page_size * $page && $page_size * $page - $count > $page_size) {//进行中活动缺少条数大于分页则全取结束活动
            $end_activity = self::getActivityListOfMaker('', '2', $page_size, '', '', $page, '', $hotwords, $count,true);
        } elseif ($count >= $page_size * $page) {//进行中活动条数大于分页则全取进行中活动
            $activity_ing = self::getActivityListOfMaker('', '1', $page_size, '', '', $page,'',$hotwords,'',true);
        } else {
            //没开始的活动和进行中的活动
            $end_activity = self::getActivityListOfMaker('', '2', $page_size, '', '', $page,'',$hotwords,'',true);
        }

        if ($activity_ing) {
            //进行中活动数据整理
            foreach ($activity_ing as $k => $v) {
                $data[$k]['id'] = $v->id;
                $data[$k]['list_img'] = $v->list_img;
                $data[$k]['title'] = $v->subject;
                $data[$k]['dataCount'] = $v->dataCount;//条数
                $data[$k]['begin_time'] = date('Y年m月d日 H:i',$v->begin_time_origin);
                $data[$k]['cities'] = implode('、', Entity::getAllCitiesOfActivity($v->id));
                $data[$k]['signs'] = Sign::signCount($v->id);
                //分享图标
                $data[$k]['share_image'] = getImage($v->share_image ?: 'images/share_image.png', 'news', '');
                if ($v->begin_time_origin < time()) {
                    $data[$k]['type'] = 'processing';//进行中
                } else {
                    $data[$k]['type'] = 'future';//将来的
                }
                $data[$k]['related_brand'] = '';
            }
        }


        if ($end_activity) {
            //结束的活动数据整理
            foreach ($end_activity as $k => $v) {
                $ret[$k]['id'] = $v->id;
                $ret[$k]['list_img'] = $v->list_img;
                $ret[$k]['title'] = $v->subject;
                $ret[$k]['dataCount'] = $v->dataCount;
                $ret[$k]['begin_time'] = date('Y年m月d日 H:i',$v->begin_time_origin);
                $ret[$k]['cities'] = implode('、', Entity::getAllCitiesOfActivity($v->id));
                $ret[$k]['signs'] = Sign::signCount($v->id);
                $ret[$k]['type'] = 'past';//进行中
                $ret[$k]['related_brand'] = Brand::related_brand($v->id);
                //分享图标
                $ret[$k]['share_image'] = getImage($v->share_image ?: 'images/share_image.png', 'news', '');
            }
        }


        //整合数据
        if ($data && $ret) {
            $result = array_merge($data, $ret);
            foreach ($result as $k=>$v){
                $result[$k]['dataCount'] = $data['0']['dataCount']+$ret['0']['dataCount'];//总数相加
            }
        } elseif ($data && !$ret) {
            $result = $data;
        }else{
            $result = $ret;
        }
        return $result ? $result : [];
    }

    /**
     * 投资人活动详情格式化数据
     */
    static public function agentActivityDetail($input, $tag = false)
    {
        $activity = new ActivityV020700();

        $data = $activity->postDetail($input, $tag)['message'];

        $ret['id'] = $data['id'];
        $ret['banner_img'] = $data['banners'];
        $ret['title'] = $data['subject'];
        $ret['begin_time'] = $data['begin_time'];
        $ret['end_time'] = $data['end_time'];
        $ret['cities'] = $data['activity_location'];
        $ret['signs'] = $data['sign_count'];
        $ret['detail'] = $data['description'];
        $ret['share_image'] = $data['share_image'];
        $ret['ticket_id'] = $data['ticket_id'];
        $ret['share_summary'] = $data['share_summary'];
        //是否已报名
        $sign = AgentActivitySign::where('agent_id', $input['agent_id'])->where('activity_id', $input['id'])->first();
        $sign ?$ret['is_sign']=1: $ret['is_sign']=0;

        foreach ($data['brand'] as $k => $v) {
            $brands[$k]['id'] = $v->id;
            $brands[$k]['title'] = $v->name;
            $brands[$k]['logo'] = $v->logo;
            $brands[$k]['category_name'] = $v->category_name;
            $brands[$k]['brand_summary'] = $v->brand_summary;
            $brands[$k]['keywords'] = $v->keywords;
            $brands[$k]['investment_min'] = $v->investment_min;
            $brands[$k]['investment_max'] = $v->investment_max;
        }

        if ($brands) {
            $ret['brand'] = $brands;
        } else {
            $ret['brand'] = [];
        }

        return $ret;
    }


    /*
    * 作用:仅获取投资人OVO活动绑定品牌的活动数量
    * @param type 活动类型 0所有的活动 1进行中和将来的 2结束的活动
    * @User: yaokai
    * 返回值: $count
    */
    public static function getActivityCount($type = '0')
    {
        // todo 测试提出：如果活动关联的品牌处于禁用的话，不要显示
        // todo changePerson zhaoyf 2018-1-04
        $ids = Brand::gainEnableBrandRelevanceToActivityIds();

        //对结果进行处理
        if (is_null($ids)) return '该活动没有对应绑定的品牌';

        switch ($type) {
            case '0'://所有
                $count = Entity::whereIn('id',$ids)->where('status', '1')->count();
                break;
            case '1'://进行中和将来
                $count = Entity::whereIn('id',$ids)->where('status', '1')->where('end_time', '>', time())->count();
                break;
            case '2'://结束的活动
                $count = Entity::whereIn('id',$ids)->where('status', '1')->where('end_time', '<', time())->count();
                break;
            default:
                $count = 0;
                break;
        }
        return $count;
    }


}