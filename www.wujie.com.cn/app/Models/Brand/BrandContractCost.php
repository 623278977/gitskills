<?php namespace App\Models\Brand;

use Illuminate\Database\Eloquent\Model;

class BrandContractCost extends Model
{
    public $timestamps = true;

    protected $table = 'brand_contract_cost';

    protected $dateFormat = 'U';

    //黑名单
    protected $guarded = [];
}
