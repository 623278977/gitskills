<?php

namespace App\Models\CityPartner;

use Illuminate\Database\Eloquent\Model;

class Income extends Model
{
    protected $table = 'city_partner_income';
    protected $guarded = [];
    protected function getDateFormat()
    {
        return 'U';
    }
}
