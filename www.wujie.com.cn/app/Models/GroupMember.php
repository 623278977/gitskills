<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GroupMember extends Model
{
    protected $table = 'group_member';
    public $timestamps = true;
    protected $fillable = [
        'groupid',
        'uid',
        'created_at',
        'updated_at',
    ];
}
