<?php namespace App\Models\Agent\Academy;

use Illuminate\Database\Eloquent\Model;
use App\Models\Brand\BrandVideo;
use App\Models\News\Entity as News;

class BrandAgentQuiz extends Model
{
    protected $table = 'brand_agent_quiz';

    const ENABLE_TYPE   = 1;    //使用状态

    public static $instance = null;

    public static function instance()
    {
        if (is_null(self::$instance)) {
            self::$instance = new self;
        }

        return self::$instance;
    }

    /**
     * 关联视频
     */
    public function hasOneVideo()
    {
        return $this->hasOne(BrandVideo::class, 'id', 'post_id');
    }

    /**
     * 关联咨询
     */
    public function hasOneNews()
    {
        return $this->hasOne(News::class,'id', 'post_id');
    }

    /**
     * 关联：品牌
     */
    public function hasOneBrand()
    {
        return $this->hasOne(Brand::class, 'id', 'brand_id');
    }

    /**
     * 关联：测试项
     */
    public function hasManyBrandQuizOptions()
    {
        return $this->hasMany(BrandQuizOptions::class, 'quiz_id', 'id');
    }

    /**
     * author zhaoyf
     *
     * 获取答题数据
     */
    public function agentStudyTopicLists($param)
    {
        //根据关联测试和测试项表，获取到对应的答题数据
        $result = self::with(['hasManyBrandQuizOptions' => function($query) {
            $query->where('content', '<>', '');
        }])
         ->where([
             'brand_id'      => $param['brand_id'],
             'post_id'       => $param['post_id'],
             'content_type'  => $param['study_type'],
             'status'        => self::ENABLE_TYPE,
         ])
         ->orderByRaw("RAND()")
         ->first();

        //对结果进行处理
        if ($result && !is_null($result->hasManyBrandQuizOptions)) {
            return $result;
        } else {
            return null;
        }
    }

    /**
     * author zhaoyf
     *
     * 获取选择题目的答案
     *
     * @param tags 区分是获取数据还是更新数据，为true时，是获取数据
     *
     */
    public function gainTopicAnswer($param)
    {
        $query_result = self::where([
            'id'     => $param['issue_id'],
            'status' => self::ENABLE_TYPE,
        ]);

        $query_result->increment('nums');
        $confirm_result = $query_result->first();

        //对结果进行处理
        if (is_object($confirm_result)) {
            return $confirm_result;
        } else {
            return null;
        }
    }
}