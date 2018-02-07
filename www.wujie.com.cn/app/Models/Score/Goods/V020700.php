<?php
/**活动模型
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/5/5
 * Time: 11:10
 */
namespace App\Models\Score\Goods;

use App\Models\Zone;
use Illuminate\Database\Eloquent\Model;
use \DB;
use App\Models\Live\Entity as Live;
use App\Models\Brand\Entity as Brands;
class V020700 extends Model
{

    protected $dateFormat = 'U';

    protected $table = 'score_goods';

    //黑名单
    protected $guarded = [];



    public function getDates()
    {
        return array();
    }


    public static function getGoods()
    {
        $goods =  self::where('status', 1)->orderBy('sort', 'desc')
            ->select('id','num', 'price')->get();

        return $goods;
    }



}