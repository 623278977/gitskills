<?php

/*
 * 兑吧记录表
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DuiBa extends Model {

    protected $table = 'duiba';
    protected $guarded = ['id'];

    protected function getDateFormat() {
        return date(time());
    }

}
