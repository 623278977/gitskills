<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/5/5
 * Time: 11:10
 */
namespace App\Models\Activity;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class OrganizerFollow extends Model
{
    public $timestamps = false;
    protected $table = 'activity_organizer_follow';
    protected $fillable = [
        'organizer_id',
        'uid',
        'status',
        'created_at',
        'updated_at',
    ];
}