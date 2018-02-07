<?php namespace App\Models\Agent\NewAgentAreas;

use App\Models\Agent\Agent;
use App\Models\Brand\Entity as Brand;
use App\Models\Video\Entity as Video;
use Illuminate\Database\Eloquent\Model;
use App\Models\Comment\Entity as Comment;
use App\Models\Brand\Entity\V020800 as Brandss;
use Illuminate\Support\Facades\DB;

class NewAgentDetail extends Model
{
    protected $table = 'new_agent_detail';

    const ENABLE_TYPE = 1;  //启用标记

    public static $instance = null;

    public static function instance()
    {
        if (is_null(self::$instance)) {
            self::$instance = new self;
        }

        return self::$instance;
    }

    /**
     * 关联：品牌
     */
    public function hasManyBrand()
    {
        return $this->hasMany(Brand::class, 'id', 'brand_id')->where('agent_status', self::ENABLE_TYPE);
    }

    /**
     * 关联：视频（video）
     */
    public function hasManyVideo()
    {
        return $this->hasMany(Video::class, 'id', 'video_id');
    }

    /**
     * author zhaoyf
     *
     * 获取新手详情页详细数据
     *
     * @param = [
     *  'id'        => 外键ID     int,
     *  'agent_id'  => 用户ID     int,
     *  'pre_id'    => 位置ID     int,
     *  'page'      => 分页初始页, 默认 1  int,
     *  'page_size' => 分页个数 默认 10    int,
     *  'section'   => 评论列表分来显示标记：1：分开；0不分开   int
     * ]
     *
     * return array
     *
     */
    public function gainNewAgentDetailDatas($param)
    {
        $confirm_result = array();

        $gain_results = self::where('id', $param['id']);
        $gain_result  = $gain_results->first();
        $gain_results->increment('browse_num');     //浏览量

        //获取经纪人的基本信息
        $agent_result= $this->gainAgentInfos(Agent::class, $param['agent_id'], [
            'id', 'realname', 'nickname', 'avatar'
        ]);

        //对结果进行判断
        if (is_object($gain_result)) {
            $confirm_result = [
                'title'         => $gain_result->title,
                'summary'       => $gain_result->summary,
                'banner_img'    => getImage($gain_result->banner, 'news', ''),
                'shar_img'      => "",
                'content'       => $gain_result->detail,
                'browse_num'    => $gain_result->browse_num,
                'is_zan'        => $this->_isZans($param) ?  1 : 0,
                'zan_num'       => $this->_newAgentDetailZans($gain_result),
                'brand_data'    => $this->gainBrands($gain_result),
                'video_data'    => $this->gainVideos($gain_result),
            ];

            //对获取的经纪人结果进行判断
            if (is_object($agent_result)) {
                $confirm_result['agent_id']    = $agent_result->id;
                $confirm_result['agent_name']  = $agent_result->realname ?  $agent_result->realname : $agent_result->nickname ;
                $confirm_result['avatar']      = getImage($agent_result->avatar, 'avatar', '');
            }
        }

        return $confirm_result;
    }

    /**
     * 获取品牌信息
     *
     * @param param object
     *
     * return array
     */
    public function gainBrands($param)
    {
        $confirm_data = array();

       if (isset($param->brand_id) && !empty($param->brand_id)) {
           $brand_data = Brand::with('categorys1')
               ->whereIn('id', json_decode($param->brand_id, true))
               ->get();

           //组织数据
           foreach ($brand_data as $key => $vls) {
               $confirm_data[] = [
                   'brand_id'         => $vls->id,
                   'brand_name'       => $vls->name,
                   'brand_slogan'     => $vls->slogan ?: "",
                   'brand_logo'       => getImage($vls->logo, 'news', ''),
                   'brand_keyword'    => explode(' ', $vls->keywords),
                   'money_limit'      => number_format($vls->investment_min) .'-'. number_format($vls->investment_max),
                   'brand_cate_id'    => $vls->categorys1->id,
                   'brand_cate'       => $vls->categorys1->name,
                   'brand_commission' => Brandss::instances()->getMaxCommission($vls->id),
                ];
           }
       }

       return $confirm_data;
    }

    /**
     * 获取视频信息
     *
     * @param object
     *
     * return array
     */
    public function gainVideos($param)
    {
        $confirm_data = array();

        if (isset($param->video_id) && !empty($param->video_id)) {

            $video_data = Video::whereIn('id', json_decode($param->video_id, true))->get();

            //组织数据
            foreach ($video_data as $key => $vls) {
                $confirm_data[] = [
                    'video_id'      => $vls->id,
                    'video_name'    => $vls->subject,
                    'video_url'     => $vls->video_url,
                    'video_image'   => getImage($vls->image, 'video', ''),
                    'bg_image'      => getImage($vls->bg_image, 'news', ''),
                    'video_time'    => $vls->duration,
                    'created'       => $vls->created_at->getTimestamp(),
                ];
            }
        }

        return $confirm_data;
    }

    /**
     * author zhaoyf
     *
     * 获取新手详情对应的评论数据
     *
     * @param = [
     *  'id'        => 外键ID     int,
     *  'agent_id'  => 用户ID     int,
     *  'pre_id'    => 位置ID     int,
     *  'page'      => 分页初始页, 默认 1  int,
     *  'page_size' => 分页个数 默认 10    int,
     *  'section'   => 评论列表分来显示标记：1：分开；0不分开   int
     * ]
     *
     * return array
     *
     */
    public function gainNewAgentDetailCommentDatas($param)
    {
        $comment_result = $comments = Comment::agentComments(
            $param['id'],
            'new_agent_detail',
            $param['agent_id'],
            isset($param['pre_id'])      ?  $param['pre_id']    : 0,
            isset($param['page'])         ?  $param['page']      : 1,
            isset($param['page_size']) ?  $param['page_size'] : 10,
            1,
            isset($param['section'])    ?  $param['section']    : 1
        );

        return $comment_result;
    }

    /**
     * 详情点赞获取
     */
    private function _newAgentDetailZans($param)
    {
       return DB::table('new_agent_detail_zan')
           ->where('new_agent_detail_id', $param->id)
           ->count();
    }

    /**
     * 判断当前经纪人是否已经点赞
     */
    private function _isZans($param)
    {
        return DB::table('new_agent_detail_zan')
            ->where([
                'agent_id'      => $param['agent_id'],
                'new_agent_detail_id' => $param['id']
            ])->count();
    }

    /**
     * 详情点赞
     */
    public function newAgentZans($param)
    {
       $_zan = DB::table('new_agent_detail_zan')->where([
           'agent_id'      => $param['agent_id'],
           'new_agent_detail_id' => $param['id']
       ]);
       $zan_result = $_zan->first();

       //对结果进行处理
       if ($zan_result) {
           if( $zan_result->status == self::ENABLE_TYPE) {
               return "confirm_zan";
           } else {
               $_zan->update(['status' => self::ENABLE_TYPE]);
           }
       } else {
           $zan_data = [
               'new_agent_detail_id'  => $param['id'],
               'agent_id'             => $param['agent_id'],
               'status'               => self::ENABLE_TYPE,
               'created_at'           => time(),
               'updated_at'           => time()
           ];
           return $_zan->insert($zan_data);
       }
    }

    /**
     * 新手专区详情分享次数记录
     *
     * @param param 详情页ID
     *
     * return bool
     *
     */
    public function newAgentShards($param)
    {
        self::where('id', $param['id'])->increment('shar_num');

        return true;
    }

    /**
     * author zhaoyf
     *
     * 根据经纪人ID获取经纪人基本信息
     *
     * @param class  需要查询的对象类
     * @param id     查询的用户ID
     * @param param  查询的返回值
     * @param field  查询一条还是多条数据（默认一条）
     *
     * return result object
     */
    public function gainAgentInfos($class, $id, $param, $field = "first")
    {
        $confirm_data = array();

        $agent_result = $class::where([
             'id'     => $id,
             'status' => self::ENABLE_TYPE
         ])
          ->select($param)
          ->$field();

       //对获取结果进行处理
       if (is_object($agent_result)) {
            return $agent_result;
       }

       return null;
    }

}