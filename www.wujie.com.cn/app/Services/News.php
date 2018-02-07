<?php

namespace App\Services;

use App\Models\Categorys as CategorysModel;
use App\Models\Comment\Entity as Comment;
use App\Models\Distribution\Action;
use App\Models\News\Entity as NewsModel;
use App\Models\User\Praise;
use App\Models\Distribution\Entity as Distribution;

class News
{

    /**
     * 作用:资讯列表
     * 参数:$page 分页  $page_size  规格
     * is_agent_news 区分是否经纪人端的热门咨询，默认为 false：不是；
     *
     * 返回值:
     */
    public function lists($page, $page_size ,$hotwords = '', $is_agent_news = false)
    {
        //默认：添加时间。
        //用户自定义排序 > 推荐 > 热门 > 其他
        $builder = NewsModel::where('status', 'show')
            ->select(
                'id',
                'is_hot',
                'is_recommend',
                'relation_id',
                'type',
                'detail',
                'logo',
                'title' ,
                'keywords' ,
                'created_at' ,
                'author',
                'view',
                'comments',
                'sham_zan as count_zan',
                'sham_view as view',
                'summary'
            );

        //Todo 为 true 时查询经纪人端的咨询，为 false 时走c端
        if ($is_agent_news) {
            $builder->where('type',  'agent_normal');
        } else {
            $builder->whereIn('type', ['none','brand']);
        }

        //其他处理一样
        $builder->orderBy('sort',     'desc')
            ->orderBy('is_recommend', 'desc')
            ->orderBy('is_hot',       'desc')
            ->orderBy('created_at',   'desc');

        //模糊查询
        if ($hotwords) {
            $builder->where(function($query) use($hotwords) {
                $query->where('title',      'like', '%' . $hotwords . '%')
                      ->orWhere('keywords', 'like', '%' . $hotwords . '%')
                      ->orWhere('detail',   'like', '%' . $hotwords . '%');
            });
        }

        $news = $builder->skip(($page - 1) * $page_size)->take($page_size)->get();
        $dataCount       = $builder->count();
        $news->dataCount = $dataCount;

        $news = NewsModel::process($news, 1);

        return $news;
    }


    /**
     * 作用:资讯详情
     * 参数:$id 资讯id
     *
     * 返回值:
     */
    public function detail($id)
    {
        $news =  NewsModel::where('status', 'show')
            ->select(
                'id',
                'created_at',
                'relation_id',
                'type',
                'detail',
                'banner',
                'title',
                'author',
                'distribution_id',
                'distribution_deadline',
                'logo',
                'score_price',
                'comments',
                'view',
                'sham_view as view',//返回假浏览数
                'sham_view',//返回假浏览数
                'rebate',
                'is_hot',
                'summary',
                'share_summary'
            )
            ->where('id', $id)->first();

        $reward = Action::where('distribution_id', $news->distribution_id)->where('action', 'share')->first();

        if(is_object($reward)){
            $news->share_reward_unit = $reward->give;
            $news->share_reward_num = $reward->trigger;
        }else{
            $news->share_reward_unit = 'none';
            $news->share_reward_num = 0;
        }
        $news = NewsModel::singleProcess($news, 1);

        return $news;
    }

    /*
     * 获取用于首页展示的数据
     */
    public function getPublicData($obj, $uid=0){

        //获取3天前的时间戳
        $threeTime = time() - 3600 * 24 * 3;
        $data = $this->detail($obj->id);
        $return = [];
        if ($data) {
            $return['id'] = $data->id;
            $return['relation_id'] = $data->relation_id;
            $return['type'] = $data->type;
            $return['detail'] = trim(preg_replace('#<\w+(\s+.*?)?>|</\w+>|&nbsp;#', '', $data->detail));
            $return['banner'] = getImage($data->banner, 'news', '');
            $return['logo'] = getImage($data->logo, 'news', '');
            $return['title'] = $data->title;
            $return['author'] = $data->author;
            $return['count_comment'] = Comment::ConmmentCount($data->id,'News');;
            $return['count_zan'] = Praise::ZanCount($data->id,'news');
            $return['view'] = $data->sham_view;//假阅读数
            $return['rebate'] = Distribution::Integer($data->rebate);//佣金池
            $return['created_at_format'] = $data->created_at_format;
            $return['is_hot'] = $data->is_hot;
            //是否最新
            if (strtotime($data->created_at) > $threeTime) {
                $return['is_newest'] = 1;//最新
            } else {
                $return['is_newest'] = 0;//非最新
            }
            //资讯描述
            $summary = $data->summary;
            if (empty($summary)) {
                $return['summary'] = mb_substr(preg_replace('/\s+/i','',str_replace('&nbsp;','',strip_tags($return['detail']))),0,50);
            }else{
                $return['summary'] = $summary;
            }
            //带有分享码的地址
            if (!$uid) {
                $return['url'] = createUrl('news/detail', array('id' => $obj->id));
                $return['share_reward_num'] = Action::getDistributionByAction('news', $obj->id, 'share')->trigger;
                $return['share_reward_unit'] = Action::getDistributionByAction('news', $obj->id, 'share')->give;
            } else {
                $return['url'] = createUrl('news/detail', array('id' => $obj->id, 'share_mark' => makeShareMark($obj->id, 'news', $uid))
                );
            }
            $return['is_distribution'] = Distribution::IsDeadline($data->distribution_id,$data->distribution_deadline);//分销是否失效
            $return['share_score'] = Distribution::shareScore($data->distribution_id);//分销分享得的积分


        }

        return $return;
    }

}