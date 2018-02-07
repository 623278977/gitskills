<?php

namespace App\Models\Business;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $table = 'business_payment';

    public  function admin()
    {
        return  $this->hasOne('App\Models\Admin\Entity','id','admin_id');
    }
}
