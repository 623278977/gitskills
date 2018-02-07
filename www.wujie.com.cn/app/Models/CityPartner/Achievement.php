<?php

namespace App\Models\CityPartner;

use Illuminate\Database\Eloquent\Model;

class Achievement extends Model
{
    protected $table = 'city_partner_achievement';
    protected $guarded = [];
    protected function getDateFormat()
    {
        return 'U';
    }
}
