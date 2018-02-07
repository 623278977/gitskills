<?php

namespace App\Services\Version\News;

use App\Models\Orders\Items;
use App\Models\Orders\Entity as Orders;
use App\Models\User\Praise;
use App\Models\Comment\Entity as Comment;
use App\Models\News\Entity as News;
use App\Models\User\Entity as User;
class _v020700 extends _v020600
{

    /*
     * 资讯列表
     */
    public function postlist($data)
    {
        //获取3天前的时间戳
        $threeTime = time() - 3600 * 24 * 3;
        //获取资讯点赞数
        foreach ($data as $k => $v) {
            foreach ($v as $key => $value) {
                $news_id = $value->id;
                $summary = $value->summary;
                if (empty($summary)) {
                    $data[$k][$key]['summary'] = mb_substr($value->detail, 0, 50) . '...';
                }else{
                    $data[$k][$key]['summary'] = $summary;
                }
                //点赞
                $data[$k][$key]['count_zan'] = Praise::ZanCount($news_id,'news');
                //评论
                $data[$k][$key]['count_comment'] = Comment::ConmmentCount($news_id,'News');
                //是否最新
                if (strtotime($value->created_at) > $threeTime) {
                    $data[$k][$key]['is_newest'] = 1;//最新
                } else {
                    $data[$k][$key]['is_newest'] = 0;//非最新
                }
                //剔除多余字段
                unset($value->detail, $value->brand, $value->keywords);
            }
        }
        return ['message' => $data['result'] ?: [], 'status' => true];
    }


    /*
     * 资讯详情
     */
    public function postDetail($param = [])
    {
        $result = parent::postDetail($param);
        $news_id = $result['data']->id;//资讯ID
        //用户所有的订单ID
        $orders_ids = Orders::select('id')
            ->where('uid', $param['uid'])
            ->where('status', 'pay')
            ->get()->toArray();
        $orders_ids = array_pluck($orders_ids, 'id');//用户所有的订单ID
        //购买详情
        $is_purchase = Items::whereIn('order_id', $orders_ids)
            ->where('type', 'news')
            ->where('status', 'pay')
            ->where('product_id', $news_id)
            ->value('id');
        if ($is_purchase) {
            $result['data']['is_purchase'] = 1;//已购买
        } else {
            $result['data']['is_purchase'] = 0;//未购买
        }
        //点赞
        $result['data']['count_zan'] = Praise::ZanCount($news_id);
        //评论
        $result['data']['count_comment'] = Comment::ConmmentCount($news_id,'News');

        return $result;
    }

    /**
     * 获取资讯购买信息
     */
    public function postBuyInfo($param)
    {
        $news = News::where('id', $param['id'])
            ->select('id', 'detail', 'summary', 'logo', 'title','score_price')->first();
        //价格信息
        $news->logo? $news->list_img = getImage($news->logo, 'news', '') :$news->list_img='';


        $news->description= ($news->summary ?$news->summary:extractText($news->detail));
        //用户积分
        $user = User::where('uid', $param['uid'])->select('score', 'username', 'nickname','realname')->first();
        !$user->realname && $user->realname = $user->nickname;
        unset($news->detail, $news->summary, $news->logo, $news->image,$user->nickname);
        //已售
        $sold_num =  Items::where('type', 'news')->where('product_id', $param['id'])->where('status', 'pay')->sum('num');
        $news->sold_num = $sold_num;
        return ['data'=>['user'=>$user, 'news'=>$news], 'status'=>true];
    }

}
