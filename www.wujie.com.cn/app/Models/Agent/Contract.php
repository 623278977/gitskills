<?php namespace App\Models\Agent;

use Illuminate\Database\Eloquent\Model;
use App\Models\User\Entity as User;
use App\Models\Brand\Entity as Brand;
use App\Models\User\Fund;

class Contract extends Model
{
    protected  $table = 'contract';
    protected $guarded = [];

    /**
     * 关联：客户
     */
    public function user()
    {
        return $this->hasOne(User::class, 'uid', 'uid');
    }


    /**
     * 关联：品牌
     */
    public function brand()
    {
        return $this->hasOne(Brand::class, 'id', 'brand_id');
    }


    /**
     * 关联：创业基金
     */
    public function fund()
    {
        return $this->hasOne(Fund::class, 'id', 'fund_id');
    }


    /**
     * 关联：创业基金
     */
    public function invitation()
    {
        return $this->hasOne(Invitation::class, 'id', 'invitation_id');
    }

    //关联品牌合同模板
    public function brand_contract()
    {
        return $this->hasOne(BrandContract::class, 'id', 'brand_contract_id');
    }


}