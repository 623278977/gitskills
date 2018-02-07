<?php namespace App\Models\Brand;

use App\Models\Brand\Entity as Brand;
use App\Models\Zone\Entity as Zone;
use Illuminate\Database\Eloquent\Model;

class BrandStore extends Model
{
    protected $table = 'brand_store';

    /**
     * 关联：品牌
     */
    public function hasOneBrand()
    {
        return $this->hasOne(Brand::class, 'id', 'brand_id');
    }

    /**
     * 关联：地区
     */
    public function hasOneZone()
    {
        return $this->hasOne(Zone::class, 'id', 'zone_id');
    }
}