<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use App\Models\Brand\Entity as Brand;
class Categorys extends Model
{

    protected $table = 'categorys';

    protected $dateFormat = 'U';
    //黑名单
    protected $guarded = [];

    //关联品牌
    public function brand(){
        return $this->hasMany(Brand::class,'categorys1_id','id');
    }

}
