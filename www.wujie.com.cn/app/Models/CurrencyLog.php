<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use App\Models\User\Entity;
use Exception;

class CurrencyLog extends Model
{
    //

    protected $table = 'currency_log';
    protected $guarded = ['id'];

    protected function getDateFormat()
    {
        return date(time());
    }




}
