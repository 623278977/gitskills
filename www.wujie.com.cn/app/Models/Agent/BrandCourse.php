<?php

namespace App\Models\Agent;

use Illuminate\Database\Eloquent\Model;
use App\Models\Brand\Entity as Brand;
use App\Models\Agent\BrandChapter;
use App\Models\Brand\BrandVideo;
use App\Models\News\Entity as News;
use App\Services\Version\Agent\Brand\_v010004 as Brand_v010004;

class BrandCourse extends Model
{
    protected  $table =  'brand_course';
    protected $dateFormat = 'U';
    protected $guarded = [];

    //关联品牌
    public function brand(){
        return $this->belongsTo(Brand::class , 'brand_id','id');
    }

    //关联章节
    public function brand_chapter(){
        return $this->belongsTo(BrandChapter::class , 'chapter_id','id');
    }

    //关联品牌视频
    public function brand_video(){
        return $this->belongsTo(BrandVideo::class,'post_id','id');
    }
    //关联品牌资讯
    public function news(){
        return $this->belongsTo(News::class,'post_id','id');
    }

    /*
     * 获取下一需要学习的课程
     * */

    public static function getNextCourse($agentId , $brandId , $currContentId , $currContentType){
        $currContentTypeStr = 'article';
        if($currContentType == 1){
            $currContentTypeStr = 'video';
        }
        $brandModel = new Brand_v010004();
        $arr = ['agent_id'=>$agentId , 'brand_id'=>$brandId];
        $chapterInfo = $brandModel->postChapterList($arr);
        if($chapterInfo['status'] == false){
            return $chapterInfo;
        }
        $data = [] ;
        $data['is_complete'] = '0';
        $chapterList = $chapterInfo['message'];
        //修改该经纪人的申请状态
        $completeness = $chapterList['completeness'];
        //获取该经纪人申请品牌的信息
        $agentBrandInfo = AgentBrand::agentStatus()->where('agent_id',$agentId)
            ->where('brand_id',$brandId)->first();
        //如果状态已经是已完成或已审核的，则不改变状态
        if( !in_array($agentBrandInfo['status'] , [3,4])){
            if($completeness > 0 && $completeness < 1){
                //正在培训中
                AgentBrand::agentStatus()->where('agent_id',$agentId)->where('brand_id',$brandId)->update(['status'=>2]);
            }
            else if($completeness == 1){
                //待审核
                AgentBrand::agentStatus()->where('agent_id',$agentId)->where('brand_id',$brandId)->update(['status'=>3]);
            }
        }

        if($chapterList['is_complete'] || empty($chapterList['chapter'])){
            $data = ['is_complete'=>'1'];
            return ['message'=> $data ,'status'=>true];
        }
        $chapterList = $chapterList['chapter'];

        //合并所有的章节内容
        $allContents = [];
        foreach ($chapterList as $oneChapter){
            $allContents = collect($allContents)->merge($oneChapter['content']);
        }
        $allContents = $allContents->toArray();

        $currCollect = collect($allContents)->filter(function($item)use($currContentId , $currContentTypeStr){
            return $item['type'] == $currContentTypeStr && $item['id'] == $currContentId;
        })->toArray();
        foreach ($currCollect as $key => $value){
            $currKey = $key;
        }
        $afterCollect = collect($allContents)->slice($currKey);
        $afterCollect->shift();
        $beforeArr = collect($allContents);
        $beforeArr->splice($currKey);
        $beforeArr = $beforeArr->toArray();
        $nextContent = $afterCollect->merge($beforeArr)->filter(function ($item){
            return $item['is_complete'] == 0;
        })->first();
        $data['content_type'] = $nextContent['type'];
        $data['content_id'] = $nextContent['id'];
        $data['content_num'] = $nextContent['cotent_num'];
        return ['message'=> $data ,'status'=>true];

    }

}
