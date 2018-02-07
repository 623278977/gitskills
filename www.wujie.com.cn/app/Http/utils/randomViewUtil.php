<?php
namespace App\Http\utils;

/**
 * Created by PhpStorm.
 * User: Yali
 * Date: 2017/7/3
 * Time: 18:56
 */

class randomViewUtil {
    /**
     * 获取虚拟随机访问量
     * @base--原有假的访问量
     * @active--上月的用户活跃度
     * @return--基数与随机增量的和，
     */
    public static function getRandViewCount($base) {
        $a = [];
        if (count($a) != 24) {
//            print "参数错误，取2017.5数据！";
            $a = array(22, 14, 9, 6, 4, 6, 12, 18, 26, 30, 32, 33, 33, 31, 33, 34, 34, 33, 29, 29, 33, 41, 41, 32);
        }
        if ($base < 1) {
            $base = 1;
        }
        $j = $a[date('G')] / 10;
        $r = rand(0, log($base, 2));
        $vnum = round(sqrt($j * $r))?:1;
        return $vnum;
    }
}