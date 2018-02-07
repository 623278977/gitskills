<?php namespace App\Models\Agent;

use App\Models\Brand\BrandContractCost;
use App\Models\Brand\Entity as Brand;
use Illuminate\Database\Eloquent\Model;

class BrandContract extends  Model
{
    protected $table = 'brand_contract';

    public static  $stance = null;
    public static function instance()
    {
        if (is_null(self::$stance)) {
            self::$stance = new self;
        }

        return self::$stance;
    }

    //2代表品牌加盟 4代表渠道加盟',
    public static  $league =
        [
            2=>'品牌加盟',
            4=>'渠道加盟',
        ];


    /**
     * 关联：品牌
     */
    public function hasOneBrand()
    {
        return $this->hasOne(Brand::class, 'id', 'brand_id');
    }


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