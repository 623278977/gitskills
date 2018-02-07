<?php namespace App\Services\Version\Agent\News;

use App\Models\Comment\Entity as Comment;
use App\Services\Version\VersionSelect;
use App\Services\Version\News\_v020600;
use App\Models\Orders\Entity as Orders;
use App\Http\utils\randomViewUtil;
use App\Models\Orders\Items;
use App\Models\User\Praise;
use App\Models\News\Entity;
use App\Services\News;

class _v010000 extends VersionSelect
{

    /**
     * 获取资讯列表
     *
     * @param $param
     * @param News $news
     * @return array|string
     * @internal param Request $request
     * @internal param News|null $news
     * @internal param null $version
     */
    public function postList($param)
    {
        $page       = $param['request']->get('page',      1);
        $page_size  = $param['request']->get('page_size', 10);
        $hotwords   = $param['request']->get('hotwords',  '');
        $results    = $param['news']->lists($page, $page_size, $hotwords, true);

        foreach ($results as $item) {
            if ($item->keywords) {
                $item->keywords = strpos($item->keywords, ' ') !== FALSE ? explode(' ', $item->keywords) : [$item->keywords];
            } else {
                $item->keywords = [];
            }
            $item->dataCount = $results->dataCount ?: 0;
            $item->detail    = preg_replace('/\s+/i', '', str_replace('&nbsp;', '', strip_tags($item->detail)));
        }

        $is_return = $param['request']->input('is_return') ? : 0;

        //重新组织数据
        $anew = array();
        foreach ($results as $key => $vas) {
            $anew[$key]['id']        = $vas->id;
            $anew[$key]['type']      = $vas->type;
            $anew[$key]['title']     = $vas->title;
            $anew[$key]['summary']   = empty($vas->summary) ? mb_substr($vas->detail, 0, 150) : $vas->summary;
            $anew[$key]['zans']      = Praise::ZanCount($vas->id, 'news');
            $anew[$key]['comments']  = Comment::ConmmentCount($vas->id, 'News');
            $anew[$key]['views']     = $vas->view;
            $anew[$key]['list_img']  = !empty($vas->logo) ?  getImage($vas->logo, '', 'large', 1) : '';
            $anew[$key]['is_new']    = strtotime($vas['created_at_format'] . "+3 day") > time() ? 1 : 0;
            $anew[$key]['author']    = $vas->author;
            $anew[$key]['dataCount'] = $vas->dataCount;
        }
        return  $is_return == 1 ? ['message' => $results, 'status' => true] : ['message' => $anew, 'status' => true];
    }

    /**
     * 获取资讯详情 zhaoyf
     *
     * @param $param
     * @return array|string
     * @internal param DetailRequest $request
     * @internal param News|null $news
     * @internal param null $version
     */
    public function postDetail($param)
    {
        $results  = $param['request']->input();

        //根据执行咨询ID获取一个咨询对象
        $news_result = Entity::where('id', $results['id']);

        //对要获取的咨询进行处理 且 对禁用的咨询进行过滤
        $gain_result = $news_result->first();
        if (is_object($gain_result)) {
            if ($gain_result->status == 'hidden') {
                return ['message' => '该资讯已经下架', 'status' => false];
            }
        } else {
            return ['message' => '该资讯不存在', 'status' => false];
        }

        //资讯浏览量自增1
        $news_result->increment('view');

        //伪浏览量
        $sham_view = $news_result->value('sham_view') ? : 1;
        $increment = randomViewUtil::getRandViewCount($sham_view);  //增量
        $news_result->increment('sham_view', $increment);

        $V206    = new _v020600();
        $result  = $V206->postDetail($param['request']);
        $news_id = $result['data']->id;  //资讯ID

        //用户所有的订单ID
        $orders_ids = Orders::select('id')
            ->where('uid', $results['uid'])
            ->where('status', 'pay')
            ->get()->toArray();
        $orders_ids = array_pluck($orders_ids, 'id'); //用户所有的订单ID

        //购买详情
        $is_purchase = Items::whereIn('order_id', $orders_ids)
            ->where('type', 'news')
            ->where('status', 'pay')
            ->where('product_id', $news_id)
            ->value('id');

        if ($is_purchase) {
            $result['data']['is_purchase'] = 1; //已购买
        } else {
            $result['data']['is_purchase'] = 0; //未购买
        }

        //点赞（count_zan）；评论（count_comment）
        $result['data']['count_zan']     = Praise::ZanCount($news_id);
        $result['data']['count_comment'] = Comment::ConmmentCount($news_id,'News');
        $result['data']['is_zan']        = Praise::where('uid', $results['uid'])
                                            ->where('relation', 'news')
                                            ->where('relation_id', $results['id'])
                                            ->count() ?  1 : 0;

        //重新组织数据
        $anew_details = array();
        foreach ($result as $key => $vas) {
            $anew_details[$key]['id']               = $vas['id'];
            $anew_details[$key]['name']             = $vas['title'];
            $anew_details[$key]['banner']           = !empty($vas['banner']) ?  getImage($vas['banner']) : '';
            $anew_details[$key]['logo']             = !empty($vas['logo'])   ?  getImage($vas['logo'])   : '';
            $anew_details[$key]['created_at']       = $vas['created_at_format'];
            $anew_details[$key]['detail']           = $vas['detail'];
            $anew_details[$key]['brand']            = $vas['brand'];
            $anew_details[$key]['comments']         = $vas['count_comment'];
            $anew_details[$key]['zans']             = $vas['count_zan'];
            $anew_details[$key]['is_zan']           = $vas['is_zan'];
            $anew_details[$key]['author']           = $vas['author'];
            $anew_details[$key]['type']             = $vas['type'];
            $anew_details[$key]['summary']          = $vas['summary'];
            $anew_details[$key]['brand_id']         = $vas['relation_id'];
            $anew_details[$key]['share_summary']    = $vas['share_summary']?$vas['share_summary']:$vas['summary'];
        }


        return ['message' => $anew_details['data'], 'status' => true];
    }
}