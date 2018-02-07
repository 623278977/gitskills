<?php

namespace App\Models\City;

use Illuminate\Database\Eloquent\Model;

class PartnerAchievement extends Model
{
    protected $table = 'city_partner_achievement';
    public function getDateFormat()
    {
        return 'U';
    }
    public function cityPartner()
    {
        return $this->hasOne('App\Models\City\Partner','uid','partner_uid');
    }
}
