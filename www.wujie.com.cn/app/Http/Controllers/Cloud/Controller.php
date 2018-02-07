<?php

/*
 * 基本处理，包含身份验证
 */

namespace App\Http\Controllers\Cloud;

class Controller extends \App\Http\Controllers\Controller {
    //初始化处理
    public function __construct() {
        $this->beforeFilter(function() {
            
        });
    }

}
