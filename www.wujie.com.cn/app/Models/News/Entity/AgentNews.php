<?php
/**活动模型
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/5/5
 * Time: 11:10
 */

namespace App\Models\News\Entity;

use App\Models\Comment\Entity as Comment;
use App\Models\News\Entity as News;
use App\Models\Agent\AgentBrand;
use App\Services\News as Newss;
use App\Models\User\Praise;
use \DB, Closure, Input;

class AgentNews extends News
{
    public  static $instance = null;
    public  static function instance()
    {
        if (is_null(self::$instance)) {
            self::$instance = new self;
        }
        return self::$instance;
    }

    /**
     * 格式化获取投资人课程资讯数据
     */
    static public function detailFormat($brand_id,$agent_id)
    {
        //对应分类下的课程品牌资讯
        $news_ids = self::newsIds($brand_id);
        foreach ($news_ids as $k => $v) {
            $news[] = News::singleProcess($v);
        }
        foreach ($news as $k => $v) {
            $agent_news[$k]['id'] = $v->id;
            $agent_news[$k]['title'] = $v->title;
            $agent_news[$k]['image'] = $v->logo;
            $agent_news[$k]['created_at'] = strtotime($v->created_at);
            $agent_news[$k]['summary'] = $v->summary;//描述
            $agent_news[$k]['is_read'] = AgentBrand::isRead($agent_id,$brand_id, $v->id, 'news');
        }
        return $agent_news;
    }

    /**
     * 单纯获取投资人品牌课程资讯id
     * $brand_id    品牌id
     * $data  返回格式 默认true返回所有信息 false仅返回ids集
     * return array 资讯id
     */
    public static function newsIds($brand_id,$data = true)
    {
        //对应分类下的课程品牌资讯
        $builder = News::where('relation_id', $brand_id)
            ->where('status', 'show')
            ->where('type','agent');
        if($data){
            $news_ids = $builder->get();
        }else{
            $news_ids = $builder->lists('id')->toArray();
        }
        return $news_ids;
    }

    /**
     * 经纪人--资讯
     *
     * @param $page
     * @param $page_size
     * @return array
     */
    public function newsList($page, $page_size)
    {
        $obj_news = new Newss();
        $news     = DB::table('news')
            ->select('id', 'created_at', 'index_sort', 'sort','is_recommend', 'type')
            ->where('status', 'show')
            ->where('type', ['agent_index'])
            ->orderBy('is_recommend', 'desc')
            ->orderBy('index_sort', 'desc')
            ->orderBy('sort', 'desc')
            ->orderBy('id','desc')
            ->offset(($page - 1) * $page_size)
            ->limit($page_size)
            ->get();

        $news_result = array_map(function($newss) use($obj_news) {
            $data = $obj_news->detail($newss->id);

            //资讯组合数据
            $return['id']                =  $data->id;
            $return['title']             =  $data->title;
            $return['list_img']          =  !empty($data->logo) ?  getImage($data->logo, '', 'large', 1) : "";
            $return['author']            =  $data->author;
            $return['zans']              =  Praise::ZanCount($data->id, 'news');
            $return['comments']          =  Comment::ConmmentCount($data->id, 'News');
            $return['relation_id']       =  $data->relation_id;
            $return['views']             =  $data->sham_view;   //假阅读数
            $return['detail']            =  mb_substr(trim(preg_replace('#<\w+(\s+.*?)?>|</\w+>|&nbsp;#', '', $data->detail)), 0, 55) . "...";
            $return['created_at_format'] =  $data->created_at_format;
            $return['index_sort']        =  $data->index_sort;

            //资讯描述
            $summary = $data->summary;
            if (empty($summary)) {
                $return['summary'] = mb_substr(preg_replace('/\s+/i', '', str_replace('&nbsp;', '', strip_tags($return['detail']))), 0, 50);
            } else {
                $return['summary'] = $summary;
            }

            return $return;
        }, $news);

        return $news_result;
    }

}