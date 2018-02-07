<?php

/*
 * App版本管理
 */

namespace App\Models\App;

use App\Models\Admin\Entity as Admin;
use Illuminate\Database\Eloquent\Model;

class Versions extends Model {

    protected $dateFormat = 'U';
    public static $_PLATFORMS = [
        'android' => '安卓',
        'ios' => 'IOS'
    ];
    public static $_RELEASES = [
        'yes' => '发布',
        'no' => '不发布'
    ];
    protected $table = 'app_versions';
    protected $guarded = ['id'];

    //获取状态名
    public function platformName() {
        return array_get(static::$_PLATFORMS, $this->platform);
    }

    //获取状态名
    public function releaseName() {
        return array_get(static::$_RELEASES, $this->is_release);
    }

    //关联管理员
    public function admin() {
        return $this->hasOne(Admin::class, 'id', 'admin_id');
    }

}
