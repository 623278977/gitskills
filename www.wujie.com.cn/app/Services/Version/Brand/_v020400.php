<?php

namespace App\Services\Version\Brand;

use App\Services\Version\VersionSelect;
use App\Models\Brand\Entity as BrandModel;
use App\Models\Brand\Images;
use App\Models\Brand\Quiz;
use App\Models\Brand\Consult;
use App\Models\Brand\Enter;
use App\Models\Brand\Intent;
use App\Models\Brand\Goods;
use App\Models\Brand\BrandGoods;
use App\Models\Message;
use App\Models\News\Entity as News;
use App\Models\User\Favorite;
use App\Models\Industry;
use DB;

class _v020400 extends VersionSelect
{
    /**
     * 作用:获取品牌详情
     * 参数:$id 品牌id  $uid  用户id
     *
     * 返回值:
     */
    public function postDetail($data)
    {
        //品牌
        $brand = BrandModel::single($data['id']);

        //轮播图
        $banners = Images::where(['brand_id' => $data['id'], 'type' => 'banner'])->select('id', 'src')->get();
        $banners = Images::process($banners);

        //详情图
        $detail_images = Images::where(['brand_id' => $data['id'], 'type' => 'detail'])->select('id', 'src')->get();
        $detail_images = Images::process($detail_images);
        $brand->detail_images = $detail_images;

        //相关问题 最多显示5个问题
        $questions = Quiz::where(['brand_id' => $data['id'], 'status' => 'show'])->where('admin_id', '>', 0)
            ->select('quiz', 'answer')->orderBy('created_at', 'desc')->limit(5)->get();

        //相关新闻  最多显示3条，多了给列表
        $news = News::where(['type' => 'brand', 'relation_id' => $data['id'], 'status' => 'show'])
            ->select('logo', 'title', 'detail', 'id')->orderBy('sort', 'asc')->limit(4)->get();
        $news = News::process($news);

        //用户和品牌的关系
        $is_favorite = Favorite::isFavorite('brand', $data['id'], $data['uid']);
        $relation = ['is_favorite' => $is_favorite];
        if($brand instanceof BrandModel){
            $brands = $this->recommend($brand, $data['type']);
        }else{
            $brands = [];
        }

        $goods =$this->goods($data['id']);
        if(count($goods)){
            return compact('brand', 'banners', 'questions', 'news', 'brands', 'relation', 'goods');
        }else{
            return compact('brand', 'banners', 'questions', 'news', 'brands', 'relation');
        }
    }


    /**
     * 作用:获取品下物品
     * 参数:$brand_id
     *
     * 返回值:
     */
    private function goods($brand_id)
    {
        $goods = BrandGoods::where('brand_id', $brand_id)->where('status', 'allow')->get();

        return $goods;
    }




    /**
     * 作用:获取品牌的相关品牌
     * 参数:$id 品牌id  $uid  用户id
     *
     * 返回值:
     */
    public function recommend(BrandModel $brand, $type = 'app')
    {
        //排序规则 取5条
        //1、行业分类，同行业分类（大类）的随机提供显示。
        //2、如果行业分类不满足，则根据点击量提供显示
        if ($type == 'app') {
            $same_cate_brands = BrandModel::singles()->where(['categorys1_id' => $brand->categorys1_id, 'status' => 'enable'])
                ->where('id', '<>', $brand->id)->addSelect('id','logo', 'name', 'investment_min', 'investment_max', 'keywords')
                ->orderBy(\DB::raw('RAND()'))->get();
            $same_cate_brands = BrandModel::process($same_cate_brands)->toArray();
            $other_brands = array();
            if (count($same_cate_brands) < 6) {
                $other_brands = BrandModel::singles()->where(['status' => 'enable'])->whereNotIn('id', array_column($same_cate_brands, 'id'))
                    ->where('id', '<>', $brand->id)->addSelect('id','logo', 'name', 'investment_min', 'investment_max', 'keywords')
                    ->orderBy('click_num', 'desc')->orderBy('is_recommend', 'desc')
                    ->limit(6 - count($same_cate_brands))->get();
                $other_brands = BrandModel::process($other_brands)->toArray();
            }
            $brands = array_merge($same_cate_brands, $other_brands);
        } else {
            $brands = BrandModel::singles()->where(['status' => 'enable'])
                ->where('id', '<>', $brand->id)->addSelect('id','logo', 'name', 'investment_min', 'investment_max', 'keywords')
                ->orderBy('is_recommend', 'desc')->orderBy('click_num', 'desc')->orderBy(\DB::raw('RAND()'))
                ->limit(6)->get();
            $brands = BrandModel::process($brands)->toArray();
        }

        return $brands;
    }

    /**
     * 作用:获取品牌相关的新闻
     * 参数:$id 品牌id
     *
     * 返回值:
     */
    public function news($id, $page, $page_size)
    {
        //相关新闻  最多显示3条，多了给列表
        $news = News::where(['type' => 'brand', 'relation_id' => $id, 'status' => 'show'])
            ->select('logo', 'title', 'detail', 'id')->orderBy('sort', 'asc')->skip(($page - 1) * $page_size)->take($page_size)->get();
        $news = News::process($news);

        return $news;
    }

    /**
     * 作用:收藏或取消收藏某个品牌
     * 参数:$id 品牌id $uid 用户id  $type do收藏  undo 取消收藏
     *
     * 返回值:
     */
    public function collect($id, $uid, $type)
    {
        if ($type == 'do') {
            $result = Favorite::favorite('brand', $id, $uid);
        } else {
            $result = Favorite::unFavorite('brand', $id, $uid);
        }

        return $result;
    }

    /**
     * 作用:对品牌提问
     * 参数:$id 品牌id
     *
     * 返回值:
     */
    public function ask($id, $uid, $content)
    {
        //先写写入提问表
        $quiz = Quiz::create(
            [
                'brand_id' => $id,
                'uid'      => $uid,
                'quiz'     => $content
            ]
        );

        //再写入洽询表
        $result = Consult::create(
            [
                'type'        => 'quiz',
                'relation_id' => $quiz->id,
                'brand_id'    => $id,
                'uid'         => $uid
            ]
        );

        return $result;
    }

    /**
     * 作用:对品牌留言
     * 参数:$id 品牌id
     *
     * 返回值:
     */
    public function message($id, $uid, $mobile, $realname, $zone_id, $address, $consult)
    {
        $message = Intent::create(
            [
                'uid'      => $uid,
                'brand_id' => $id,
                'mobile'   => $mobile,
                'realname' => $realname,
                'zone_id'  => $zone_id,
                'address'  => $address,
                'consult'  => $consult
            ]
        );

        //再写入洽询表
        $result = Consult::create(
            [
                'type'        => 'intent',
                'relation_id' => $message->id,
                'brand_id'    => $id,
                'uid'         => $uid,
                'zone_id'     => $zone_id
            ]
        );

        return $result;
    }

    /**
     * 作用:品牌入驻
     * 参数:$id 品牌id
     *
     * 返回值:
     */
    public function enter($uid, $mobile, $realname, $brand_name, $categorys1_id, $introduce)
    {
        $result = Enter::create(
            [
                'uid'           => $uid,
                'mobile'        => $mobile,
                'realname'      => $realname,
                'brand_name'    => $brand_name,
                'categorys1_id' => $categorys1_id,
                'introduce'     => $introduce,
            ]
        );
        if($result){
            //发送短信  美国的就不发
            if(strlen($mobile)==11){
                @SendTemplateSMS('brand_enter',$mobile,'brand_enter',['brand_name'=>$brand_name]);
            }

            createMessage(
                $uid,
                $title = '品牌入驻申请提交成功',
                $content = '你已经成功提交申请，<a style="color:#1e8cd4" href="' . "wjsq://brandlist" . '">' . '点击查看' . '</a>更多品牌',
                $ext = '',
                $end = '',
                $type = 1
            );

        }


        return $result;
    }


    /**
     * 作用:获取一个月内对品牌的洽讯的数据  (提问、洽谈、洽谈成功)
     * 参数:
     *
     * 返回值:
     */
    public function consult($id, $size)
    {
        $consults = Consult::singles()->where('type', '<>', 'prepay')
            ->orderBy('created_at', 'desc')->addSelect('id', 'created_at', 'type', 'status');
        if($size){
            $consults->limit($size);
        }

        if($id){
            $consults->where('id', '>', $id);
        }else{
            $consults->where('created_at', '>', (time()-3600*24*30));
        }

        $consults = $consults->get();
        $consults = Consult::process($consults)->toArray();

        return $consults;
    }






    /**
     * 作用:获取一个品牌下当天可购买的商品
     * 参数:$live_id 直播id
     *
     * 返回值:int
     */
    public function getGoods($brand_id)
    {
        $goods = Goods::where('brand_id', $brand_id)
            ->where('status', 'allow')
//            ->where('num','>' ,0)
            ->whereHas('live', function ($query) {
                $query->where('begin_time', '<', time())
                    ->where('end_time', '>', time());
            })
            ->get();

        return $goods;
    }

    /*
     * 品牌列表
     */
    public function postLists($param = [])
    {
        $data = BrandModel::baseLists($param['type'],$param,function($builder){

            $builder->select(
                'id',
                'uid',
                'logo',
                'name',
                'investment_min' ,
                'investment_max',
                'keywords',
                'introduce',
                'issuer',
                'is_recommend',
                'summary',
                'details',
                'slogan',
                'brand_summary',
                DB::raw('(select concat_ws(",",id,name) from lab_zone WHERE lab_zone.id = lab_brand.zone_id) as zone_name'),
                DB::raw('(select if(GROUP_CONCAT(activity_id), GROUP_CONCAT(activity_id), 0) from lab_activity_brand WHERE lab_activity_brand.brand_id = lab_brand.id) as activity_id'),
                DB::raw('(select name from lab_categorys as c where c.id = lab_brand.categorys1_id ) AS category_name')
            );
            return $builder;
        },function($data){

            foreach($data as $item){
                $item->investment_min = formatMoney($item->investment_min);
                $item->investment_max = formatMoney($item->investment_max);
                $item->logo = getImage($item->logo);
                $item->investment_arrange = $item->investment_min . ' ~ ' .$item->investment_max .'万';
                $item->zone_name = $this->formatZoneName($item->zone_name);
                $item->remark = $this->getBrandRemark($item->activity_id);
                //$item->industry_ids = $this->getBrandIndustry($item->industry_ids);
                if($item->keywords){
                    $item->keywords = strpos($item->keywords,' ')!==FALSE ? explode(' ',$item->keywords) : [$item->keywords];
                }else{
                    $item->keywords = [];
                }
                $item->summary = strip_tags($item->summary);
                $item->details = $item->detail = trim(preg_replace('#<\w+(\s+.*?)?>|</\w+>|&nbsp;#','',$item->details));

            }
            return $data;
        },$param['page_size']);

        foreach($data as &$item){

            if(is_object($item)){
                $item->isRoadShow = BrandModel::isRoadShow($item);
                $item->canJoin = BrandModel::canJoin($item);
                $item->dataCount = $data->dataCount;
            }

            if(is_array($item)){
                $obj = (object)$item;
                $item['isRoadShow'] = BrandModel::isRoadShow($obj);
                $item['canJoin'] = BrandModel::canJoin($obj);
                $item['dataCount'] = $data->dataCount;
            }

        }

        return $data;
    }

    /*
     *格式化行业
     */
    public function getBrandIndustry($industry_ids){
        $return = [];
        if(strpos($industry_ids,',')){
            foreach($ids = explode(',',$industry_ids) as $id){
                $return[] = Industry::find($id)->name;
            }
        }else{
            $return = Industry::find($industry_ids)->name;
        }
        return $return;
    }

    /*
     * 格式化地区名称
     */
    public function formatZoneName($zone_name){
        //todo
        $return = explode(',',$zone_name)[1];
        if(strpos($return,'市')){
            $return = str_replace('市','',$return);
        }
        return $return;
    }

    /*
     * 格式化标识
     */
    public function getBrandRemark($activity_id){
        if(!$activity_id){
            return '';
        }
        //获取标识语 todo
        return '奥特曼';
    }



}