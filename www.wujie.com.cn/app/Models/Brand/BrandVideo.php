<?php
/**
 * Created by PhpStorm.
 * User: yaokai
 * Date: 2017/9/22 0022
 * Time: 17:40
 */
namespace App\Models\Brand;

use \DB;
use Monolog\Handler\CouchDBHandlerTest;
use Illuminate\Database\Eloquent\Model;
use App\Models\Brand\Entity as Brand;
use App\Models\Agent\BrandCourse;

class BrandVideo extends Model
{

    public $timestamps = true;

    protected $table = 'brand_video';

    protected $dateFormat = 'U';

    //黑名单
    protected $guarded = [];

    //关联品牌
    public function Brand()
    {
        return $this->belongsTo(Brand::class,'brand_id','id');
    }

    //关联品牌学习表
    public function brand_course(){
        return $this->hasOne(BrandCourse::class,'post_id','id')->where('type',1);
    }

    /**
     * 关联：章节
     */
    public function hasOneChapter()
    {
        return $this->hasOne(\App\Models\Agent\Academy\BrandChapter::class, 'id', 'chapter_id');
    }

    /**
     * 单纯获取投资人品牌课程视频id
     * $brand_id    品牌id
     * return array 视频id
     */
    public static function videoIds($brand_id)
    {
        //对应分类下的课程品牌视频
        $video_ids  = self::where('is_delete','0')
            ->where('brand_id',$brand_id)
            ->lists('id')->toArray();

        return $video_ids;
    }


}