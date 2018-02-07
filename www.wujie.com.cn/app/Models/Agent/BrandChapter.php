<?php

namespace App\Models\Agent;

use Illuminate\Database\Eloquent\Model;
use App\Models\Brand\BrandVideo;
use App\Models\News\Entity as News;
use App\Models\Brand\Entity as Brand;
use App\Models\Agent\BrandCourse;

class BrandChapter extends Model
{

    protected  $table =  'brand_chapter';
    protected $dateFormat = 'U';
    protected $guarded = [];

//    //关联品牌视频表
//    public function brand_video(){
//        return $this->hasMany(BrandVideo::class,'chapter_id','id');
//    }
//
//    //关联资讯表
//    public function news(){
//        return $this->hasMany(News::class,'chapter_id','id')->where('type','agent');
//    }

    //关联品牌表
    public function brand(){
        return $this->belongsTo(Brand::class ,'brand_id','id');
    }

    //关联品牌课程表
    public function brand_course(){
        return $this->hasMany(BrandCourse::class , 'chapter_id' , 'id');
    }

    /*
     * 计算完成度
     * shiqy
     * */
    public static function getChapterCompleteness($brandId,$agentId){
        //判断是否代理，如果已经代理，则默认完成度为1
        $isAgentBrand = AgentBrand::where('brand_id',$brandId)
            ->where('agent_id',$agentId)->where('status',4)->first();
        if (is_object($isAgentBrand)) {
            return '1';
        }
        //完成的所有的视频，资讯id
        $allCompleteInfo = BrandAgentCompleteQuiz::allCompleteContent($agentId);
        //该品牌下的所有小节
        $builder = BrandCourse::whereHas('brand',function ($query)use($brandId){
                $query->where('agent_status',1);
                $query->where('id',$brandId);
            })->whereHas('brand_chapter',function($query){
                $query->where('status',1);
            })->where('status',1);

        //该品牌下所有的小节数
        $allContents = $builder->count();
        //该品牌下所有已完成的小节数
        $completeNum = $builder->where(function ($query)use($allCompleteInfo){
            $query->where('type' , 1)->whereIn('post_id',$allCompleteInfo['video'])
                ->orWhere(function ($query)use($allCompleteInfo){
                    $query->where('type' , 2)->whereIn('post_id',$allCompleteInfo['news']);
                });
        })->count();
        return round($completeNum / $allContents , 2).'';
    }
}
