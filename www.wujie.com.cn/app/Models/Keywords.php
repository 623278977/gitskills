<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Closure;

class Keywords extends Model
{
    protected $table = "keywords";
    static function getDataByType($type, $page_size = 10, Closure $callback = null)
    {
        $builder = self::where('type', $type);

        if ($callback) {
            $builder = $callback($builder);
        }

        return $builder->paginate($page_size);

    }
}
