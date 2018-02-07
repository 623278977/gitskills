<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use DB, Closure;
use App\Models\Brand\Entity as Brand;
class Fund extends Model
{

    protected $table = 'user_fund';
    protected $guarded = ['id'];
    public $timestamps = false;


    /*
     * 我的创业基金
     */
    static function baseQuery($page_size = 10, Closure $callback = null ,Closure $formatCallback = null)
    {
        $builder = self::query()->where('user_fund.created_at', '>=', time() - (180 * 24 * 3600))
            ->where('user_fund.status', 'unused');

        if ($callback) {
            if($formatCallback){
                return $formatCallback($callback($builder));
            }
            return $callback($builder);
        }

        return $builder->paginate($page_size);
    }


    public function brand()
    {
        return $this->hasOne(Brand::class, 'id', 'brand_id');
    }


}
