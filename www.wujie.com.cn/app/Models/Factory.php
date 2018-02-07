<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Factory extends Model
{
    /**
     * 存储模型对象
     * 键是该模型的命名空间路径
     * 值是该模型的一个实例
     */
    public static $modelStore = [];
}
