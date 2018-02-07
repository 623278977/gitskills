<?php

namespace App\Models\Activity;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use App\Models\Activity\Maker as ActivityMaker;
use DB;

class ApplyActiviy extends Model
{
    protected $dateFormat = 'U';
    protected $table = 'apply_activity';
    protected $fillable = [
        'uid',
        'subject',
        'description',
        'begin_time',
        'end_time',
        'type',
        'apply_name',
        'apply_phone'
    ];
    protected $guarded = [];
}
