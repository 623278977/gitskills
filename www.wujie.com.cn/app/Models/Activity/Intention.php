<?php

namespace App\Models\Activity;

use Illuminate\Database\Eloquent\Model;

class Intention extends Model
{
    protected $guarded = [''];
    protected $table = 'game_intention';
    protected $dateFormat = 'U';
    public function user()
    {
        return $this->hasOne('App\Models\User\Entity','uid','uid');
    }
}
