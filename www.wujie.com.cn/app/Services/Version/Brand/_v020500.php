<?php

namespace App\Services\Version\Brand;

use App\Models\User\Fund;
use App\Models\User\Entity as User;
use App\Models\Video;
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
use App\Services\Brand as BrandService;

class _v020500 extends _v020400
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
        $brand = BrandModel::single($data['id'],1);
        if($brand){
            $brand->detail=$brand->getOriginal('details');
        }
        //轮播图
        $banners = Images::where(['brand_id' => $data['id'], 'type' => 'banner'])->select('id', 'src')->get();
        $banners = Images::process($banners);

        //详情图
        $detail_images = Images::where(['brand_id' => $data['id'], 'type' => 'detail'])->orderBy('sort','desc')->select('id', 'src','introduce')->get();
        $detail_images = Images::process($detail_images);
        $brand->detail_images = $detail_images;
        //品牌相关的资质图片
        $qualifyImages = BrandModel::qualifyImages($data['id']);
        $brand->qualifyImages = $qualifyImages;

        //是否领取过创业基金
        $brand->fetched_fund = $this->fetchedFund($data['uid'], $data['id']);

        //品牌收藏数
        $favorite_count = Favorite::where(['status' => 1, 'model' => 'brand', 'post_id' => $data['id']])->count();
        $brand->favorite_count = $favorite_count;
        //分享标识码
        $brand->share_mark = makeShareMark($data['id'], 'brand', $data['uid']);
//        $brand->code = md5(uniqid().rand(1111,9999));
        //相关问题 最多显示5个问题
        $questions = Quiz::where(['brand_id' => $data['id'], 'status' => 'show'])->where('admin_id', '>', 0)
            ->select('quiz', 'answer')->orderBy('created_at', 'desc')->limit(1)->get();

        //相关新闻  最多显示3条，多了给列表
        $news = News::where(['type' => 'brand', 'relation_id' => $data['id'], 'status' => 'show'])
            ->select('logo', 'title', 'detail', 'id')->orderBy('sort', 'asc')->limit(4)->get();
        $news = News::process($news);

        //用户和品牌的关系
        $is_favorite = Favorite::isFavorite('brand', $data['id'], $data['uid']);
        $relation = ['is_favorite' => $is_favorite];
//        $brands = $this->recommend($brand, $data['type']);
        //品牌相关的视频
        $video_ids = Video::where('brand_id', $data['id'])->where('status','1')->lists('id')->toArray();

        $videos = [];
        foreach ($video_ids as $k => $v) {
            $videos[] = Video::singleVideo($v);
        }

        $goods = $this->goods($data['id']);

        //该用户对该目标点击缓存加1
        if($data['uid']){
            $origin_cache = \Cache::get('brand' . $data['id'] . 'view' . $data['uid'], 0);
            \Cache::forever('brand' . $data['id'] . 'view' . $data['uid'], $origin_cache+1);
        }
        if (count($goods)) {
            return compact('brand', 'banners', 'questions', 'news', 'brands', 'relation', 'goods', 'videos');
        } else {
            return compact('brand', 'banners', 'questions', 'news', 'brands', 'relation', 'videos');
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
                ->where('id', '<>', $brand->id)->addSelect('id', 'logo', 'name', 'investment_min', 'investment_max', 'keywords', 'created_at', 'details', 'slogan')
                ->orderBy(\DB::raw('RAND()'))->get();
            $same_cate_brands = BrandModel::process($same_cate_brands)->toArray();
            $other_brands = array();
            if (count($same_cate_brands) < 6) {
                $other_brands = BrandModel::singles()->where(['status' => 'enable'])->whereNotIn('id', array_column($same_cate_brands, 'id'))
                    ->where('id', '<>', $brand->id)->addSelect('id', 'logo', 'name', 'investment_min', 'investment_max', 'keywords', 'created_at', 'details')
                    ->orderBy('click_num', 'desc')->orderBy('is_recommend', 'desc')
                    ->limit(6 - count($same_cate_brands))->get();
                $other_brands = BrandModel::process($other_brands)->toArray();
            }
            $brands = array_merge($same_cate_brands, $other_brands);
        } else {
            $brands = BrandModel::singles()->where(['status' => 'enable'])
                ->where('id', '<>', $brand->id)->addSelect('id', 'logo', 'name', 'investment_min', 'investment_max', 'keywords', 'created_at', 'details')
                ->orderBy('is_recommend', 'desc')->orderBy('click_num', 'desc')->orderBy(\DB::raw('RAND()'))
                ->limit(6)->get();
            $brands = BrandModel::process($brands)->toArray();
        }

        return $brands;
    }

    /*
    * 领取创业基金
    */
    public function postFetchFund($param)
    {
        $exist = $this->fetchedFund($param['uid'], $param['brand_id']);
        if($exist){
            return ['data' => '你已经领取过了', 'status' => false];
        }
        $create = Fund::create(
            [
                'uid'      => $param['uid'],
                'brand_id' => $param['brand_id'],
                'fund'     => $param['fund'],
            ]
        );
        $create->code = 'APP'.date('Y').date('M').str_pad($create->id, 9, 0, STR_PAD_LEFT);
        $create->save();

        if ($create) {
            return ['data' => '创建成功', 'status' => true];
        } else {
            return ['data' => '创建失败', 'status' => false];
        }
    }



    /*
    * 品牌相关问题
    */
    public function postQuestion($param)
    {
        $questions = Quiz::where(['brand_id' => $param['brand_id'], 'status' => 'show'])
            ->where('admin_id', '>', 0)
            ->select('quiz', 'answer')
            ->orderBy('created_at', 'desc')
            ->skip(($param['page']-1)*$param['page_size'])->take($param['page_size'])
            ->get();


        return ['data'=>$questions, 'status'=>true];
    }


    /*
    * 是否领取过基金
    */
    public function fetchedFund($uid, $brand_id)
    {
        $exist = Fund::where(['uid'=>$uid, 'brand_id'=>$brand_id])
            ->where('created_at', '>', time()-(3600*24*180))
            ->first();

        if(is_object($exist) && $uid){
            return true;
        }else{
            return false;
        }
    }


    /**
     * 对品牌留言  -- 数据中心版
     * @User
     * @param $param
     * @return array
     */
    public function postMessage($param)
    {
        if(isset($param['share_mark']) && !empty($param['share_mark'])){
            $share_remark = \Crypt::decrypt($param['share_mark']);
            $md5 = substr($share_remark, 0,32);
            if($md5!=md5($_SERVER['HTTP_HOST'])){
                return ['message'=>'分享码有误', 'status'=>true];
            }
            $share_remark = explode('&', substr($share_remark, 44));
            $param['source_uid'] = $share_remark[2];
        }else{
            $param['source_uid'] = 0;
        }

        if(!$param['uid']){
            $user = User::findOrRegister($param['mobile'], $param['realname']);
            //如果异常返回异常信息
            if (!$user['status']){
                return ['message' => '哎呀! 留存失败,请稍后重试哦！', 'status' => false];
            }

            $param['uid'] = $user['user']->uid;
        }
        //沉淀
        $mobile = trim($param['mobile']);
        $enTel = encryptTel($mobile);
        depositTel($mobile , $enTel , 'wjsq' , getNationCode($mobile));

        $brand = new BrandService();

        $result = $brand->message($param['id'], $param['uid'], $param['mobile'],
            $param['realname'], $param['zone_id'], $param['address'], $param['consult'],
            $param['source_uid'], array_get($param,'intent_type', 'intent'));

        if($result){
            return ['data'=>'操作成功', 'status'=>true];
        }else{
            return ['data'=>'操作失败', 'status'=>false];
        }
    }



}