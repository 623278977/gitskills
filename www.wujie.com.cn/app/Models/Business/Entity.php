<?php

namespace App\Models\Business;

use Illuminate\Database\Eloquent\Model;

class Entity extends Model
{
    protected  $table='business';
    public function cityPartner()
    {
        return $this->hasOne('App\Models\City\Partner', 'uid','partner_uid');
    }
    public function businessFactor()
    {
        return $this->hasOne('App\Models\Business\Factor','business_id','id');
    }
    public function businessPayment()
    {
        return $this->hasOne('App\Models\Business\Payment','business_id','id');
    }
    public function businessAudit()
    {
        return $this->hasOne('App\Models\Business\Audit','business_id','id');
    }
}
