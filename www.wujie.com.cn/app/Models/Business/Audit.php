<?php

namespace App\Models\Business;

use Illuminate\Database\Eloquent\Model;

class Audit extends Model
{
    protected $table = 'business_audit';
    protected $dateFormat = 'U';
    public function admin()
    {
        return $this->hasOne('App\Models\Admin\Entity','id','admin_id');
    }
}
