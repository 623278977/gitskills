<?php
/**活动模型
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/5/5
 * Time: 11:10
 */
namespace App\Models\News;


use Illuminate\Database\Eloquent\Model;
use \DB;
use App\Models\Brand\Entity as Brand;
use App\Models\Agent\BrandCourse;
use App\Models\Agent\BrandChapter;
class Entity extends Model
{

    protected $dateFormat = 'U';

    protected $table = 'news';

    //黑名单
    protected $guarded = [];

    /**
     * 作用:批量的加工
     * 参数:$collect 结果集
     *
     * 返回值:
     */
    public static function process($collect, $with_brand=0)
    {
        foreach($collect as $k=> $v){
            self::singleProcess($v, $with_brand, 0);
        }

        return $collect;
    }

    //关联品牌学习表
    public function brand_course(){
        return $this->hasOne(BrandCourse::class,'post_id','id')->where('type',2);
    }

    /**
     * 关联：章节
     */
    public function hasOneChapter()
    {
        return $this->hasOne(\App\Models\Agent\Academy\BrandChapter::class, 'id', 'chapter_id');
    }

    /**
     * 作用:单条的加工
     * 参数:$new orm 结果
     *
     * 返回值:
     */
    public static function singleProcess($new, $with_brand=0, $with_tag=1)
    {
        if($new->logo){
            $new->logo = getImage($new->logo,'activity','large',1);
        }
        if($new->banner){
            $new->banner = getImage($new->banner,'activity','',0);
        }

        if($new->created_at){
            $new->created_at_format= date('Y-m-d H:i', $new->created_at->timestamp);
        }

        if ($with_brand && isset($new->type) && $new->type=='brand') {
            $brand = Brand::with(['categorys1' => function($query) {
                $query->select('id','name');
            }])
                ->select( 'categorys1_id', 'name')
                ->where('status', 'enable')
                ->where('id', $new->relation_id)
                ->first();

            //$brand->category_name = $brand->categorys1->name;
            unset($brand->categorys1, $brand->categorys1_id);
            $new->brand = $brand;
        }

        if(!$with_tag){
            $new->detail = trim(str_replace('&nbsp;','',strip_tags($new->detail)));
        }

        return $new;
    }

    /*
     * 获取指定品牌资讯的序号，如 2.2
     * */
    public static function getNewsNum($newId){
        $newInfo = self::where('status','show')
            ->where('type','agent')->where('id',$newId)
            ->where('chapter_id','<>',0)
            ->first();
        if(!is_object($newInfo)){
            return ['message'=>'资讯id无效', 'status'=>false];
        }
//        获取章节的序号
        $chapterId = intval($newInfo['chapter_id']);
        $chapterNum = BrandChapter::getBrandChapterNum($chapterId);
        if(isset($chapterNum['status'])){
            return $chapterNum;
        }
        $courseInfo = BrandCourse::where('chapter_id',$chapterId)->where('status',1)
            ->orderBy('sort','desc')->orderBy('created_at','desc')->get()->toArray();
        $newNum = 1;
        $flag = 0;
        foreach ($courseInfo as $oneCourse){
            if($oneCourse['type'] == 2 && $oneCourse['post_id'] == $newId){
                $flag = 1;
                break;
            }
            $newNum++;
        }
        if(!$flag){
            return ['message'=>'资讯序号获取失败', 'status'=>false];
        }
        return "{$chapterNum}.{$newNum}";
    }


}