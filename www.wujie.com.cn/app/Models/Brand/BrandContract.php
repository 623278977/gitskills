<?php
/**活动模型
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/5/5
 * Time: 11:10
 */
namespace App\Models\Brand;

use \DB;
use Monolog\Handler\CouchDBHandlerTest;
use Illuminate\Database\Eloquent\Model;

class BrandContract extends Model
{

    public $timestamps = true;

    protected $table = 'brand_contract';

    protected $dateFormat = 'U';

    //黑名单
    protected $guarded = [];

//2代表品牌加盟 4代表渠道加盟',
    public static  $league =
    [
        2=>'品牌加盟',
        4=>'渠道加盟',
    ];

    /**
     * 关联：品牌合同费用
     */
    public function brandContractCost()
    {
        return $this->hasMany(BrandContractCost::class,'brand_contract_id','id');
    }


    public function getleague()
    {
        $res = array_get(self::$league, $this->league_type_id, '品牌加盟');
        return $res;
    }

}