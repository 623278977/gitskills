<?php
/**活动模型
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/5/5
 * Time: 11:10
 */
namespace App\Models\Brand;


use App\Models\Zone;
use Illuminate\Database\Eloquent\Model;
use \DB;

class Images extends Model
{

    protected $dateFormat = 'U';

    protected $table = 'brand_images';

    //黑名单
    protected $guarded = [];


    /**
     * 作用:批量的获得品牌图片
     * 参数:$id 品牌id
     *
     * 返回值:
     */
    public static function images()
    {

    }
    public function brand_images_info(){
        return $this->belongsTo(BrandImagesInfo::class , 'post_id' , 'id');
    }
    /**
     * 作用:批量的加工
     * 参数:$id 品牌id
     *
     * 返回值:
     */
    public static function process($collect)
    {
        foreach($collect as $k=> &$v){
            $v->src = getImage($v->src,'activity','',0);
        }

        return $collect;
    }

}