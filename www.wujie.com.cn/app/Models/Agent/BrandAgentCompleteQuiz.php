<?php

namespace App\Models\Agent;

use App\Models\Agent\Academy\BrandAgentQuiz;
use Illuminate\Database\Eloquent\Model;
use App\Models\Brand\Entity as Brand;
use App\Models\Agent\BrandChapter;
use App\Models\Brand\BrandVideo;
use App\Models\News\Entity as News;

class BrandAgentCompleteQuiz extends Model
{
    protected  $table =  'brand_agent_complete_quiz';
    protected $dateFormat = 'U';
    protected $guarded = [];

    //关联品牌
    public function brand(){
        return $this->belongsTo(Brand::class,'brand_id','id');
    }

    //关联章节
    public function brand_chapter(){
        return $this->belongsTo(BrandChapter::class ,'chapter_id','id');
    }

    //关联品牌视频
    public function brand_video(){
        return $this->belongsTo(BrandVideo::class ,'post_id','id');
    }

    //关联品牌资讯
    public function news(){
        return $this->belongsTo(News::class , 'post_id','id');
    }

    public static $instance = null;

    public static function instance()
    {
        if (is_null(self::$instance)) {
            self::$instance = new self;
        }

        return self::$instance;
    }

    /**
     * 添加一条数据
     *
     * @param param 参数结合
     */
    public function addCompleteDatas($param)
    {
        $result = BrandAgentQuiz::with('hasOneVideo.hasOneChapter', 'hasOneNews.hasOneChapter')
            ->where('id', $param['issue_id'])
            ->first();

        //对结果进行处理
        if ($result) {

            //对关联的章节结果进行处理
             if ($result->hasOneVideo && $result->hasOneVideo->hasOneChapter) {
                 $chapter_id = $result->hasOneVideo->hasOneChapter->id;
             } elseif ($result->hasOneNews && $result->hasOneNews->hasOneChapter) {
                 $chapter_id = $result->hasOneNews->hasOneChapter->id;
             }else {
                 $chapter_id = 0;
             }

            //过滤相同经纪人对应相同节点重复数据的插入
            $validate_result = self::where([
                'agent_id'      => $param['agent_id'],
                'content_type'  => $result->content_type,
                'post_id'       => $result->post_id,
            ])->first();

            //对结果进行处理
            if (is_object($validate_result)) return true;

            //组织需要添加的数据
            $insert_data = [
                'agent_id'      => $param['agent_id'],
                'content_type'  => $result->content_type,
                'post_id'       => $result->post_id,
                'brand_id'      => $result->brand_id,
                'chapter_id'    => $chapter_id,
            ];

            $res = self::create($insert_data);

//           return self::insert($insert_data);
            return $res;
        } else {
            return false;
        }

    }

    /**
     * 功能描述：获取某个经纪人已完成的所有的视频和资讯的id集合
     *
     * 参数说明：
     * @param $agentId  经纪人id
     *
     * 返回值：
     * @return array    [
     *                      'video'=> [id集合]
     *                      'news'=> [id集合]
     *                  ]
     *
     * 实例：
     * 结果：
     *
     * 作者： shiqy
     * 创作时间：@date 2018/1/23 0023 下午 1:04
     */
    public static function allCompleteContent($agentId){
        $data = [];
        //完成的视频，资讯有多少
        $completeContents = self::with(['brand_video'=>function($query){
            $query->select('is_delete','id');
        }])
            ->with(['news'=>function($query){
                $query->select('status','id');
            }])
            ->where('agent_id',$agentId)->get()->toArray();
        $data['video'] = [];
        $data['news'] = [];
        foreach ($completeContents as $oneContent){
            if($oneContent['content_type'] == 1){
                if(!empty($oneContent['brand_video']['is_delete'])){
                    continue;
                }
                $data['video'][] = $oneContent['post_id'];
            }
            else{
                if($oneContent['news']['status'] == 'hidden'){
                    continue;
                }
                $data['news'][] = $oneContent['post_id'];
            }
        }
        return $data;
    }
}
