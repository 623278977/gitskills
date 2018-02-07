<?php
/**活动模型
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/5/5
 * Time: 11:10
 */
namespace App\Models\Brand\Entity;

use App\Models\User\Favorite;
use App\Models\User\Industry;
use \DB , Closure ,Input;
use App\Models\Brand\Entity ;
use App\Models\Distribution\Action\V020700 as ActionV020700;
use App\Models\Video\Entity\V020700 as VideoV020700;
use App\Models\Distribution\ActionBind;

class V020700 extends Entity
{
    public static function getPublicData($obj, $uid=0, $withdistribution = 0)
    {
        $data = self::single($obj->id);
        $return = [];

        if($data){
            $return['id'] = $data->id;
            $return['name'] = $data->name;
            $return['logo'] = $data->logo;
            $return['slogan'] = $data->slogan;
            $return['category_name'] = $data->category_name;
            $return['summary'] = mb_substr($data->brand_summary, 0, 50, 'UTF-8');
            //有就取，没就取details
            $data->detail = trim(preg_replace('#<\w+(\s+.*?)?>|</\w+>|&nbsp;#', '', $data->detail));
            $return['summary'] =$data->brand_summary? mb_substr($data->brand_summary, 0, 50, 'UTF-8'):mb_substr($data->detail, 0, 50, 'UTF-8');
            $return['investment_min'] = $data->investment_min;
            $return['investment_max'] = $data->investment_max;
            $return['share_num'] = $data->share_num;
            $return['view'] = $data->click_num;
            $return['rebate'] =abandonZero($data->rebate);
            if(!$uid){
                $return['url'] = createUrl('brand/detail', array('id' => $obj->id));
            }else{
                $return['url'] = createUrl('brand/detail', array('id' => $obj->id, 'share_mark' => makeShareMark($obj->id, 'brand', $uid)));
            }

            if($withdistribution && $data->distribution_id>0){
                //获取分享规则
                $rules = ActionBind::getRules('brand', $data->id);
                $return['rules'] = $rules;
            }
        }
        return $return;
    }



    /**
     * 获取分销详情的品牌简要信息
     */
    public static function getDistributionSimple($id)
    {
        $brand = self::where('id', $id)->with(['categorys1'=>function($query){$query->select('id','name');}])

            ->select('id','name', 'logo','categorys1_id','keywords','slogan',
                'brand_summary','details','investment_min','investment_max', 'share_num', 'click_num', 'distribution_id', 'tags')->first();
/*        $brand->description = cut_str(trim(preg_replace('#<\w+(\s+.*?)?>|</\w+>|&nbsp;#', '', $brand->brand_summary)),30); */
        $brand->description = $brand->brand_summary?$brand->brand_summary:extractText($brand->details);
        $brand->investment_max = abandonZero($brand->investment_max);
        $brand->investment_min = abandonZero($brand->investment_min);
        $brand->category_name = $brand->categorys1->name;
        $brand->view = $brand->click_num;
        $brand->logo =  $brand->list_img = getImage($brand->logo, 'video', '');
        unset($brand->categorys1, $brand->brand_summary, $brand->click_num, $brand->details);
        $brand->type='brand';
        $brand->tags =$brand->keywords? explode(' ', $brand->keywords) :[];
        return $brand;
    }

    /**
     * 获取与视频绑定过的品牌，按视频更新时间倒叙排列
     */
    public static function getRecBrand()
    {
        $videos = VideoV020700::where('brand_id', '>', 0)->orderBy('updated_at', 'desc')->groupBy('brand_id')->get(['brand_id']);

        $brand_ids =  array_pluck($videos->toArray(), 'brand_id');


        $brands = self::with(['video'=>function($query){
            $query->orderBy('created_at', 'desc')
                ->select('brand_id','id', 'image', 'created_at','subject');}])
            ->with(['categorys1'=>function($query){ $query->select('id','name');}])
            ->has('video')->whereIn('id', array_pluck($videos->toArray(), 'brand_id'))
            ->where('is_recommend', 'yes')
            ->where('status', 'enable')
            ->select('id', 'categorys1_id', 'name',  'brand_summary', 'keywords', 'investment_min', 'investment_max', 'details', 'logo', 'slogan')
            ->get();


        $brands = $brands->sortBy(
            function ($item, $key) {
                if(!count($item->video)){
                        return 1;
//                    return ($item->video[0]->created_at);
                }else{
                    return 0-($item->video[0]->created_at->timestamp);
                }
            })->take(3);



        //如果不够
        if(count($brands)<3){
            $diff = array_diff($brand_ids, array_pluck($brands, 'id'));
            $brands_add = self::with(['video'=>function($query){$query->orderBy('created_at', 'desc')
                ->select('brand_id','id', 'image', 'created_at','subject');}])
                ->with(['categorys1'=>function($query){ $query->select('id','name');}])
                ->has('video')->whereIn('id', $diff)
                ->where('status', 'enable')
                ->select('id', 'categorys1_id', 'name',  'brand_summary', 'keywords',
                    'investment_min', 'investment_max', 'details', 'logo', 'slogan')
                ->get();
            $brands_add = $brands_add->sortBy(
                function ($item, $key) {
                    if(!count($item->video)){
                        return 1;
                    }else{
                        return 0-($item->video[0]->created_at->timestamp);
                    }
                })->take(3-count($brands));

            $brands_total = $brands->merge($brands_add);
        }else{
            $brands_total = $brands;
        }

        $data = $brands_total->toArray();
        foreach($data as $k=>&$v){
            $v['video_count'] = count($v['video']);
            $v['investment_min'] = abandonZero($v['investment_min']);
            $v['investment_max'] = abandonZero($v['investment_max']);
            $v['logo'] = getImage($v['logo'], 'brand', '');
            $v['category_name'] = $v['categorys1']['name'];
            $v['description'] =$v['brand_summary'] ? $v['brand_summary']:cut_str(trim(strip_tags($v['details'])), 30);
            $v['tags'] = $v['keywords']? explode(' ', $v['keywords']): [];
            foreach($v['video'] as $key=>&$val){
                $val['image'] = getImage($val['image'], 'video', '');
                $val['created_at'] = date('m月d日 H:i', $val['created_at']);
            }
            unset($v['details'], $v['brand_summary'], $v['categorys1']);
        }

        return $data;
    }



    public static function getShowBrand($recBrands, $pageSize=12, $page=1)
    {
        $recBrandids = array_pluck($recBrands, 'id');

        $videos = VideoV020700::where('brand_id', '>', 0)
            ->whereNotIn('brand_id', $recBrandids)
            ->orderBy('updated_at', 'desc')->groupBy('brand_id')->get(['brand_id']);

        $brand_ids =  array_pluck($videos->toArray(), 'brand_id');

        $brands = self::with('video')
            ->whereIn('id', $brand_ids)
            ->select('id', 'name',   'logo')
            ->skip(($page-1)*$pageSize)
            ->orderBy('sort', 'desc')
            ->take($pageSize)
            ->get();
        $data = $brands->toArray();

        foreach($data as $k=>&$v){
            $v['video_count'] = count($v['video']);
            $v['logo'] = getImage($v['logo'], 'brand', '');
            unset($v['video']);
        }

        return $data;
    }


    public static function getVideosByBrand($brand_id)
    {
        $videos = VideoV020700::where('brand_id', $brand_id)->select('id', 'image', 'created_at','subject', 'description')->get();

        $videos->transform(function ($item, $key) {
            $item->image = getImage($item->image, 'brand', '');
            $item->created_at = date('m/d H:i', $item->created_at);
            $item->description = cut_str(trim(strip_tags($item->description)), 30);
            return $item;
        });

        return $videos;
    }

    
    /**
     * 判断用户是否收藏了品牌
     * @param $uid 用户id
     * @param $id 品牌id
     * return  1 已收藏 ，0 未收藏
     */
    public static function getCollect($uid=0,$id)
    {
        $collect = Favorite::where('uid',$uid)
                ->where('post_id',$id)
                ->where('status','1')
                ->value('id');
        if ($collect){
            return 1;//已收藏
        }else{
            return 0;//未收藏
        }
    }
}