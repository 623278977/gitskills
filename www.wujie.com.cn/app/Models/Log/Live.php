<?php

/*
 * 直播日志
 */

namespace App\Models\Log;

class Live extends \Illuminate\Database\Eloquent\Model {

    protected $table = 'log_live';
    protected $dateFormat = 'U';
    protected $guarded = ['id'];

    //添加日志
    public static function add($platform, $vid, $uid) {
        if (!in_array($platform, ['ios', 'android', 'weixin', 'pc', 'wap', 'other'])) {
            return false;
        }
        return self::create([
                    'platform' => $platform, // enum('ios','android','weixin','pc','wap','other') not null default 'other' comment '当前终端操作平台',
                    'uid' => $uid, // int(11) unsigned NOT NULL,
                    'ip' => getIP(), // varchar(32) NOT NULL COMMENT '观看的ip',
                    'vid' => $vid, // mediumint(8) NOT NULL COMMENT '视频id',
        ]);
    }

}
