<?php

namespace App\Models\City;

use Illuminate\Database\Eloquent\Model;

class Partner extends Model
{
    protected $table = 'city_partner';
    public function business()
    {
        return $this->hasMany('App\Models\Business\Entity', 'partner_uid', 'uid');
    }
    public function zone()
    {
        return $this->hasOne('App\Models\Zone\Entity','id','zone_id');
    }
    public function pPartner()
    {
        return $this->hasOne('App\Models\City\Partner','uid','p_uid');
    }
    public function partnerBankAccount()
    {
        return $this->hasOne('App\Models\Partner\BankAccount','uid','uid');
    }
    public function partnerIncome()
    {
        return $this->hasMany('App\Models\City\PartnerIncome','partner_uid','uid');
    }
}
