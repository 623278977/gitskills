<?php

/*
 * 专题数据
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Special extends Model {

    protected $table = 'special';
    protected $guarded = ['id'];

    protected function getDateFormat() {
        return date(time());
    }

}
