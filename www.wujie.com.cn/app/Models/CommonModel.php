<?php
/**
 * Created by PhpStorm.
 * User: tangjb
 * Date: 2017/9/4 0004
 * Time: 下午 9:38
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class CommonModel extends Model
{
    protected $guarded = ['id'];

    protected $dateFormat = 'U';



    public static  $instance = null;
    public static function getInstance()
    {
        if (is_null(self::$instance)) {
            self::$instance = new self;
        }

        return self::$instance;
    }



}
