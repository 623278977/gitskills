<?php

namespace App\Services;

use App\Models\Agent\AgentBrand;
use App\Models\Agent\TraitBaseInfo\RongCloud;
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
use Illuminate\Support\Collection;
use App\Models\Agent\AgentCustomer;

class Brand
{
    use RongCloud;

    /**
     * 作用:获取品牌详情
     * 参数:$id 品牌id  $uid  用户id
     *
     * 返回值:
     */
    public function detail($id, $uid, $type)
    {
        //品牌
        $brand = BrandModel::single($id);

        //轮播图
        $banners = Images::where(['brand_id' => $id, 'type' => 'banner'])->select('id', 'src')->get();
        $banners = Images::process($banners);

        //详情图
        $detail_images = Images::where(['brand_id' => $id, 'type' => 'detail'])->select('id', 'src')->get();
        $detail_images = Images::process($detail_images);
        $brand->detail_images = $detail_images;

        //相关问题 最多显示5个问题
        $questions = Quiz::where(['brand_id' => $id, 'status' => 'show'])->where('admin_id', '>', 0)
            ->select('quiz', 'answer')->orderBy('created_at', 'desc')->limit(5)->get();

        //相关新闻  最多显示3条，多了给列表
        $news = News::where(['type' => 'brand', 'relation_id' => $id, 'status' => 'show'])
            ->select('logo', 'title', 'detail', 'id')->orderBy('sort', 'asc')->limit(4)->get();
        $news = News::process($news);

        //用户和品牌的关系
        $is_favorite = Favorite::isFavorite('brand', $id, $uid);
        $relation = ['is_favorite' => $is_favorite];
        $brands = $this->recommend($brand, $type);

        $todayHasGoods = $this->getGoods($id);
        $todayHasGoods = count($todayHasGoods) ? 1 : 0;

        //todo 用户查看品牌详情后，发送融云消息 zhaoyf
        $query_results = AgentCustomer::where('uid', $uid)
            ->whereIn('source', [1, 2, 3, 4, 6, 7])
            ->where('level',  '<>', -1)
            ->where('status', '<>', -1)
            ->select('uid', 'agent_id')
            ->first();

        if ($query_results) {

            //查询该品牌是否已经被当前经纪人代理过了
            $agent_brand_result = AgentBrand::where([
                'agent_id' => $query_results->agent_id,
                'brand_id' => $id,
                'status' => 4,
            ])->first();

            if ($agent_brand_result) {
                $type = 'confirm_brand_notice';
            } else {
                $type = 'no_brand_notice';
            }

            //发送融云消息
            Brand::gatherInfoSends([$uid, 'agent'.$query_results->agent_id, [
                    'brand_name' => BrandModel::where(['id' => $id, 'agent_status' => 1])->first()->name,
                    'start_a'    => "<a href='" . env('APP_HOST') . "/webapp/agent/brand/detail/_v010002?id={$id}&agent_id={$query_results->agent_id}'>",
                    'end_a'      => "</a>" ]
            ], ['browse_brand_notice', $type], ['text', 'custom'], ['true', 'my'], 'user');
        }

        return compact('brand', 'banners', 'questions', 'news', 'brands', 'relation', 'todayHasGoods');
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
                ->where('id', '<>', $brand->id)->addSelect('id', 'logo', 'name', 'investment_min', 'investment_max', 'keywords')
                ->orderBy(\DB::raw('RAND()'))->get();
            $same_cate_brands = BrandModel::process($same_cate_brands)->toArray();
            $other_brands = array();
            if (count($same_cate_brands) < 6) {
                $other_brands = BrandModel::singles()->where(['status' => 'enable'])->whereNotIn('id', array_column($same_cate_brands, 'id'))
                    ->where('id', '<>', $brand->id)->addSelect('id', 'logo', 'name', 'investment_min', 'investment_max', 'keywords')
                    ->orderBy('click_num', 'desc')->orderBy('is_recommend', 'desc')
                    ->limit(6 - count($same_cate_brands))->get();
                $other_brands = BrandModel::process($other_brands)->toArray();
            }
            $brands = array_merge($same_cate_brands, $other_brands);
        } else {
            $brands = BrandModel::singles()->where(['status' => 'enable'])
                ->where('id', '<>', $brand->id)->addSelect('id', 'logo', 'name', 'investment_min', 'investment_max', 'keywords')
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

            //todo 用户收藏品牌成功后，发送融云消息 zhaoyf
            $query_results = AgentCustomer::where('uid', $uid)
                ->whereIn('source', [1, 2, 3, 4, 6, 7])
                ->where('level',  '<>', -1)
                ->where('status', '<>', -1)
                ->select('uid', 'agent_id')
                ->first();

            //发送融云消息
            if ($query_results) {
//                Brand::gatherInfoSends([
//                    $uid,
//                    'agent' . $query_results->agent_id,
//                    ['brand_name' => BrandModel::where(['id' => $id, 'agent_status' => 1])->first()->name]
//                ], 'fond_brand_notice', 'text', 'true', 'user');
                $_datas = trans('tui.fond_brand_notice', ['brand_name' => BrandModel::where(['id' => $id, 'status' => 'enable'])->first()->name]);
                $send_notice_result = SendCloudMessage($uid, 'agent'.$query_results->agent_id, $_datas, 'RC:TxtMsg', '', true, 'one_user');
            }
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
    public function ask($id, $uid, $content, $reply_time_limit='all')
    {
        //先写写入提问表
        $quiz = Quiz::create(
            [
                'brand_id'         => $id,
                'uid'              => $uid,
                'quiz'             => $content,
                'reply_time_limit' => $reply_time_limit,
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
    public function message($id, $uid, $mobile, $realname, $zone_id, $address, $consult, $source_uid = 0, $type = 'intent', $reply_time_limit = 'all')
    {
        if (!in_array($type, ['intent', 'discount', 'floor_price', 'data'], true)) {
            return false;
        }

        depositTel($mobile, encryptTel($mobile));
        $message = Intent::create(
            [
                'uid'              => $uid,
                'type'             => $type,
                'brand_id'         => $id,
                'mobile'           => pseudoTel($mobile),
                'non_reversible'   => encryptTel($mobile),
                'realname'         => $realname,
                'zone_id'          => $zone_id,
                'address'          => $address,
                'consult'          => $consult,
                'source_uid'       => $source_uid,
                'reply_time_limit'       => $reply_time_limit,
            ]
        );

        //再写入洽询表
        $result = Consult::create(
            [
                'type'        => $type,
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
                'uid' => $uid,
                'mobile' => pseudoTel($mobile),
                'realname' => $realname,
                'brand_name' => $brand_name,
                'categorys1_id' => $categorys1_id,
                'introduce' => $introduce,
                'non_reversible' => encryptTel($mobile),
            ]
        );
        $desposit = depositTel($mobile, encryptTel($mobile));

        if ($result) {
            //发送短信

            //美国的手机号就不发短信
            if (strlen($mobile) == 11) {
                @SendTemplateSMS('brand_enter', $mobile, 'brand_enter', ['brand_name' => $brand_name], '86', 'wjsq', false);

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
        if ($size) {
            $consults->limit($size);
        }

        if ($id) {
            $consults->where('id', '>', $id);
        } else {
            $consults->where('created_at', '>', (time() - 3600 * 24 * 30));
        }

        $consults = $consults->get();
        $consults = Consult::process($consults)->toArray();

        return $consults;
    }

    /**
     * 作用:根据id获取
     * 参数:
     *
     * 返回值:
     */
    public function goods($id, $type = 'live')
    {
        if ($type == 'live') {
            $good = Goods::where('id', $id)->select('id', 'brand_id', 'price', 'num', 'code', 'status', 'title')->first();
            $brand = BrandModel::singles()->where(['status' => 'enable'])->where('id', $good->brand_id)
                ->addSelect('logo', 'name', 'investment_min', 'investment_max', 'keywords', 'details', 'brand_summary')->first();
            if (!is_object($brand)) {
                return -1;
            }
            $goods = BrandModel::process($brand)->toArray();
            $goods['brand_id'] = $good->brand_id;
            $goods['id'] = $good->id;
            $goods['price'] = $good->price;
            $goods['num'] = $good->num;
            $goods['code'] = $good->code;
            $goods['status'] = $good->status;
            $goods['goods_title'] = $good->title;
            $goods['investment_arrange'] = $brand->investment_min . '万-' . $brand->investment_max . '万';
        } else {
            $good = BrandGoods::where(['id' => $id, 'status' => 'allow'])->first();
            $brand = BrandModel::singles()->where(['status' => 'enable'])->where('id', $good->brand_id)
                ->addSelect('logo', 'name', 'investment_min', 'investment_max', 'keywords', 'id', 'details', 'brand_summary')->first();
            if (!is_object($brand)) {
                return -1;
            }
            $goods = BrandModel::process($brand)->toArray();

            $goods['brand_id'] = $good->brand_id;
            $goods['id'] = $good->id;
            $goods['goods_title'] = $good->title;
            $goods['price'] = $good->price;
            $goods['league'] = $good->league;
            $goods['investment_arrange'] = $brand->investment_min . '万-' . $brand->investment_max . '万';
        }

        return $goods;
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
            ->whereHas(
                'live',
                function ($query) {
                    $query->where('begin_time', '<', time())
                        ->where('end_time', '>', time());
                }
            )
            ->get();

        return $goods;
    }

    /**
     * 作用:根据品牌id列表获取品牌列表
     * 参数:$brand_ids
     *
     * 返回值:
     */
    public function brandList(Array $brand_ids, $order = 0)
    {
        $ids = '\'';
        foreach ($brand_ids as $k => $v) {
            if ($k == 0) {
                $ids .= $v;
            } else {
                $ids .= ',' . $v;
            }
        }
        $ids .= '\'';
        $brands = BrandModel::singles()->where('status', 'enable')->orderBy(\DB::RAW("FIND_IN_SET(id,$ids)"), 'asc')
            ->whereIn('id', $brand_ids)
            ->addSelect('id', 'logo', 'name', 'investment_min', 'investment_max', 'keywords', 'summary', 'brand_summary',
                'introduce', 'is_recommend', 'slogan', 'details')->get();
//        if ($order == 1) {
//            $orderbrands = [];
//            foreach ($brand_ids as $key => $val) {
//                foreach ($brands as $k => $v) {
//                    if ($v->id == $val) {
//                        $orderbrands[] = $v;
//                    }
//                }
//            }
//            $brands = Collection::make($orderbrands);
//        }
        $brands = BrandModel::process($brands)->toArray();

        return $brands;
    }

}