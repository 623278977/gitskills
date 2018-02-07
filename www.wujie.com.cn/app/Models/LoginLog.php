<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use App\Models\User\Entity;
use Exception;

class LoginLog extends Model
{
    //

    protected $table = 'login_log';
    protected $guarded = ['id'];

    protected function getDateFormat()
    {
        return date(time());
    }




}
