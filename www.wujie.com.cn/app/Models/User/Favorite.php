<?php
/**用户关注行业模型
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/5/5
 * Time: 11:10
 */
namespace App\Models\User;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use App\Models\Brand\Entity as Brand;

class Favorite extends Model
{
    protected $dateFormat='U';

    protected $table = 'user_favorite';

    protected $fillable = array('uid', 'model', 'post_id', 'status');

    /**
     * author zhaoyf
     *
     * 关联一个品牌ID
     */
    public function hasOneBrands()
    {
        return $this->hasOne(Brand::class, 'id', 'post_id');
    }

    protected function getDateFormat()
    {
        return date(time());
    }

    static function getRow($where)
    {
        return self::where($where)->first();
    }
    static function getRows($where,$page=0,$pageSize=10)
    {
        return self::where($where)->skip($page*$pageSize)->take($pageSize)->get();
    }

    static function getCount($where)
    {
        return self::where($where)->count();
    }

    /**
     * 收藏一个目标
     */
    static function favorite($model, $post_id, $uid)
    {
        $exist = self::where('uid', $uid)
            ->where('model', $model)
            ->where('post_id', $post_id)
            ->first();
        if (is_object($exist) && $exist->status==1) {
            return false;
        }

        if (is_object($exist) && $exist->status==0) {
            $result = self::where('uid', $uid)
                ->where('model', $model)
                ->where('post_id', $post_id)
                ->update(
                    [
                        'uid'     => $uid,
                        'post_id' => $post_id,
                        'model'   => $model,
                        'status'  => 1
                    ]
                );


            return $result;
        }

        $result = self::create(
            [
                'uid'     => $uid,
                'post_id' => $post_id,
                'model'   => $model,
                'status'  => 1
            ]
        );

        return $result;
    }


    /**
     * 取消收藏一个目标
     */
    static function unFavorite($model, $post_id, $uid)
    {
        $exist = self::where('uid', $uid)
            ->where('model', $model)
            ->where('post_id', $post_id)
            ->first();
        if (!is_object($exist)) {
            return false;
        }

        $result = self::where('uid', $uid)
            ->where('model', $model)
            ->where('post_id', $post_id)
        ->update(
            [
                'uid'     => $uid,
                'post_id' => $post_id,
                'model'   => $model,
                'status'  => 0
            ]
        );

        return $result;
    }



    /**
     * 取消收藏一个目标
     */
    static function isFavorite($model, $post_id, $uid)
    {
        $exist = self::where('uid', $uid)
            ->where('model', $model)
            ->where('post_id', $post_id)
            ->where('status', 1)
            ->first();

        if(is_object($exist)){
            return 1;
        }else{
            return 0;
        }
    }
}