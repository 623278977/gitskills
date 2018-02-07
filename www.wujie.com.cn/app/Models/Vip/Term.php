<?php

namespace App\Models\Vip;

use Illuminate\Database\Eloquent\Model;
use \Cache;
use \DB;
use \Auth;
use App\Models\Live\Entity as Live;
use App\Models\Video;

class Term extends Model
{
    protected $table = 'vip_term';
    protected $guarded = ['price'];
    protected $dateFormat = 'U';


    static function getRow($where){
        return self::where($where)->where('status','enable')->first();
    }

}
